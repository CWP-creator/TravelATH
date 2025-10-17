<?php
// Start output buffering immediately to catch any unwanted output
ob_start();

// Set headers before any output
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
	exit(0);
}

// Suppress all HTML errors and ensure clean JSON output
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(0); // Completely suppress errors that might output HTML

// Set error handler to catch any PHP errors and convert to JSON
set_error_handler(function($severity, $message, $file, $line) {
    // Clean output buffer
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error', 
        'message' => "PHP Error: $message in $file on line $line"
    ]);
    exit;
});

// Check if db_connect.php exists before requiring it
$db_path = __DIR__ . '/../../db_connect.php';
if (file_exists($db_path)) {
    require_once $db_path;
} elseif (file_exists(__DIR__ . '/db_connect.php')) {
    require_once __DIR__ . '/db_connect.php';
} else {
    respond('error', 'Database connection file is missing. Please create db_connect.php in the root directory');
}

// Include mail configuration if available
$mail_config_path = __DIR__ . '/../../mail_config.php';
if (file_exists($mail_config_path)) {
    require_once $mail_config_path;
} else {
    @include_once __DIR__ . '/mail_config.php';
}

function respond($status, $message, $extra = []) {
	// Clean any previous output to avoid invalid JSON
	while (ob_get_level() > 0) {
    ob_end_clean();
	}
	header('Content-Type: application/json');
	echo json_encode(array_merge(['status' => $status, 'message' => $message], $extra));
	exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
	$trip_id = isset($input['trip_id']) ? intval($input['trip_id']) : 0;
	if ($trip_id <= 0) {
		respond('error', 'Invalid or missing trip_id.');
	}

	// --- ADDED: Fetch Trip Details (Tour Code and Customer Name) ---
	$tripDetailsStmt = $conn->prepare("SELECT tour_code, customer_name FROM trips WHERE id = ?");
	if (!$tripDetailsStmt) {
		respond('error', 'Database prepare failed for trip details: ' . $conn->error);
	}
	$tripDetailsStmt->bind_param('i', $trip_id);
	$tripDetailsStmt->execute();
	$tripDetailsResult = $tripDetailsStmt->get_result();
	if ($tripDetailsResult->num_rows === 0) {
		respond('error', 'Trip not found.');
	}
	$tripData = $tripDetailsResult->fetch_assoc();
	$tourCode = $tripData['tour_code'] ?: 'N/A';
	$customerName = $tripData['customer_name'] ?: 'Our Valued Guest';
	$tripDetailsStmt->close();
	// --- END: Fetch Trip Details ---

	// --- VALIDATION BLOCK ---
	// First, fetch ALL days to validate that every day has a hotel and room type.
	$allDaysStmt = $conn->prepare("SELECT id, day_date, hotel_id, room_type_id FROM itinerary_days WHERE trip_id = ? ORDER BY day_date ASC");
	if (!$allDaysStmt) {
		respond('error', 'Database prepare failed: ' . $conn->error);
	}
	$allDaysStmt->bind_param('i', $trip_id);
	$allDaysStmt->execute();
	$allDaysResult = $allDaysStmt->get_result();

	if ($allDaysResult->num_rows === 0) {
		respond('error', 'No itinerary days found for this trip.');
	}

	$dayIndexById = [];
	$validationMessages = [];
	$idx = 1;
	while ($r = $allDaysResult->fetch_assoc()) {
		$dayIndexById[(int)$r['id']] = $idx;
		if (empty($r['hotel_id']) || $r['hotel_id'] == 0) {
			$validationMessages[] = [
				'type'       => 'error',
				'day_id'     => (int)$r['id'],
				'day_number' => $idx,
				'text'       => 'Day ' . $idx . ' (' . $r['day_date'] . '): Missing hotel assignment.'
			];
		} elseif (empty($r['room_type_id']) || $r['room_type_id'] == 0) {
			$validationMessages[] = [
				'type'       => 'error',
				'day_id'     => (int)$r['id'],
				'day_number' => $idx,
				'text'       => 'Day ' . $idx . ' (' . $r['day_date'] . '): Hotel assigned, but missing room type.'
			];
		}
		$idx++;
	}
	$allDaysStmt->close();

	if (!empty($validationMessages)) {
		respond('error', 'Cannot send emails. All days must have a hotel and room type assigned.', ['messages' => $validationMessages]);
	}
	// --- END: VALIDATION BLOCK ---


	// Detect if hotel_informed column exists to avoid SQL errors on older schemas
	$hasHotelInformed = false;
	$colCheck = $conn->query("SHOW COLUMNS FROM itinerary_days LIKE 'hotel_informed'");
	if ($colCheck && $colCheck->num_rows > 0) {
		$hasHotelInformed = true;
	}

	$sql = "
		SELECT id.id AS itinerary_day_id,
           id.day_date,
           id.hotel_id,
           id.room_type_id,
		       id.services_provided,
		       h.name AS hotel_name,
		       h.email AS hotel_email,
		       rt.name AS room_type_name
        FROM itinerary_days id
        LEFT JOIN hotels h ON id.hotel_id = h.id
        LEFT JOIN room_types rt ON id.room_type_id = rt.id
        WHERE id.trip_id = ?
		  AND id.hotel_id IS NOT NULL
	" . ($hasHotelInformed ? "\n\t\t  AND (id.hotel_informed IS NULL OR id.hotel_informed = 0)\n" : "\n") . "
        ORDER BY id.day_date ASC
	";

	$stmt = $conn->prepare($sql);
    if (!$stmt) {
		respond('error', 'Database prepare failed: ' . $conn->error);
	}
	$stmt->bind_param('i', $trip_id);
	$stmt->execute();
    $result = $stmt->get_result();
	$rows = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

	if (empty($rows)) {
		respond('success', 'No uninformed hotel assignments found for this trip. All hotels may have been previously notified.', ['messages' => []]);
	}

	$messages = [];
	$toMarkInformed = [];

	// Group all selected days by hotel
	$byHotel = [];
	foreach ($rows as $row) {
		$hid = (int)$row['hotel_id'];
		if (!isset($byHotel[$hid])) {
			$byHotel[$hid] = [
				'hotel_id' => $hid,
				'hotel_name' => $row['hotel_name'],
				'hotel_email' => $row['hotel_email'],
        'day_ids' => [],
				'day_numbers' => [],
				'bookings' => []
			];
		}
		$dayId = (int)$row['itinerary_day_id'];
		$byHotel[$hid]['day_ids'][] = $dayId;
		$byHotel[$hid]['day_numbers'][] = isset($dayIndexById[$dayId]) ? $dayIndexById[$dayId] : null;
		$byHotel[$hid]['bookings'][] = [
			'date' => $row['day_date'],
			'room_type' => $row['room_type_name'] ?: 'Not specified',
			'services' => isset($row['services_provided']) ? $row['services_provided'] : ''
		];
	}

	// Helper to build compact ranges from day numbers
	$formatRanges = function(array $nums) {
		$nums = array_values(array_filter($nums, function($v){ return $v !== null; }));
		sort($nums);
		if (empty($nums)) return '';
    $ranges = [];
		$start = $nums[0];
    $end = $start;
		for ($i = 1; $i < count($nums); $i++) {
			if ($nums[$i] === $end + 1) {
				$end = $nums[$i];
      } else {
				$ranges[] = ($start === $end) ? ("day $start") : ("day $start to day $end");
				$start = $nums[$i];
        $end = $start;
      }
    }
		$ranges[] = ($start === $end) ? ("day $start") : ("day $start to day $end");
    return implode(', ', $ranges);
	};

	foreach ($byHotel as $hid => $group) {
		$hotelName = $group['hotel_name'] ?: ('Hotel ' . $hid);
		$hotelEmail = $group['hotel_email'];
		if (empty($hotelEmail)) {
			$messages[] = [
				'type' => 'error',
				'text' => "No email set for $hotelName; skipping."
			];
			continue;
		}
		
		$bookings = $group['bookings'];
		$bookingBlocks = [];
		if (!empty($bookings)) {
			$currentBlock = [
				'check_in' => $bookings[0]['date'],
				'last_night' => $bookings[0]['date'],
				'room_type' => $bookings[0]['room_type'],
				'daily_services' => [$bookings[0]['date'] => $bookings[0]['services']]
			];

			for ($i = 1; $i < count($bookings); $i++) {
				$prevBooking = $bookings[$i - 1];
				$currentBooking = $bookings[$i];
				$isConsecutive = (strtotime($currentBooking['date']) === strtotime($prevBooking['date'] . ' +1 day'));
				$isSameRoomType = ($currentBooking['room_type'] === $currentBlock['room_type']);

				if ($isConsecutive && $isSameRoomType) {
					$currentBlock['last_night'] = $currentBooking['date'];
					$currentBlock['daily_services'][$currentBooking['date']] = $currentBooking['services'];
				} else {
					$bookingBlocks[] = $currentBlock;
					$currentBlock = [
						'check_in' => $currentBooking['date'],
						'last_night' => $currentBooking['date'],
						'room_type' => $currentBooking['room_type'],
						'daily_services' => [$currentBooking['date'] => $currentBooking['services']]
					];
				}
			}
			$bookingBlocks[] = $currentBlock;
		}

		// --- MODIFIED: HTML TABLE FORMATTING & EMAIL BODY ---
		$tableRowsHtml = '';
		$altTextLines = []; // For the plain text version of the email

		foreach ($bookingBlocks as $block) {
			$checkInDate = htmlspecialchars($block['check_in']);
			$checkOutDate = htmlspecialchars(date('Y-m-d', strtotime($block['last_night'] . ' +1 day')));
			$roomType = htmlspecialchars($block['room_type']);

			// Aggregate all unique, non-empty services for the block
			$servicesList = [];
			foreach ($block['daily_services'] as $service) {
				$serviceText = trim((string)$service);
				if ($serviceText !== '') {
					$servicesList[] = htmlspecialchars($serviceText);
				}
			}
			$uniqueServices = array_unique($servicesList);
			$servicesCellContent = !empty($uniqueServices) ? implode('<br>', $uniqueServices) : 'N/A';
			
			// Build the HTML table row
			$tableRowsHtml .= "
				<tr>
					<td style='border: 1px solid #ddd; padding: 8px;'>{$checkInDate}</td>
					<td style='border: 1px solid #ddd; padding: 8px;'>{$checkOutDate}</td>
					<td style='border: 1px solid #ddd; padding: 8px;'>{$roomType}</td>
					<td style='border: 1px solid #ddd; padding: 8px;'>{$servicesCellContent}</td>
				</tr>
			";
			
			// For plain text email
			$altTextServices = !empty($uniqueServices) ? implode(', ', $uniqueServices) : 'N/A';
			$altTextLines[] = "Check-In: $checkInDate, Check-Out: $checkOutDate, Room: $roomType, Services: $altTextServices";
		}

		$emailBodyHtml = "
			<p>Dear {$hotelName},</p>
			<p>We would like to request a booking for our guest(s), <strong>" . htmlspecialchars($customerName) . "</strong>, under the tour code <strong>" . htmlspecialchars($tourCode) . "</strong>.</p>
			<p>Please find the booking details below:</p>
			<table style='width: 100%; border-collapse: collapse; font-family: sans-serif; font-size: 14px; text-align: left;'>
				<thead style='background-color: #f2f2f2;'>
					<tr>
						<th style='border: 1px solid #ddd; padding: 8px;'>Check In</th>
						<th style='border: 1px solid #ddd; padding: 8px;'>Check Out</th>
						<th style='border: 1px solid #ddd; padding: 8px;'>Room Type</th>
						<th style='border: 1px solid #ddd; padding: 8px;'>Services Provided</th>
					</tr>
				</thead>
				<tbody>
					{$tableRowsHtml}
				</tbody>
			</table>
			<p>Kindly confirm this booking at your earliest convenience.</p>
			<p>Thank you.</p>
		";
		$altBodyText = "Dear {$hotelName},\n\nWe would like to request a booking for our guest(s), " . htmlspecialchars($customerName) . ", under the tour code " . htmlspecialchars($tourCode) . ".\n\nPlease find the booking details below:\n\n" . implode("\n", $altTextLines) . "\n\nKindly confirm this booking at your earliest convenience.\n\nThank you.";
		// --- END: MODIFICATION ---

		$rangeText = $formatRanges($group['day_numbers']);
		$sentOk = false;
		$sendError = '';

		if (defined('MAIL_SMTP_USER') && MAIL_SMTP_USER && defined('MAIL_SMTP_PASS') && MAIL_SMTP_PASS) {
			$hasLib = (file_exists(__DIR__ . '/../../PHPMailer/src/PHPMailer.php'));
			if ($hasLib) {
				try {
					require_once __DIR__ . '/../../PHPMailer/src/PHPMailer.php';
					require_once __DIR__ . '/../../PHPMailer/src/SMTP.php';
					require_once __DIR__ . '/../../PHPMailer/src/Exception.php';
					$mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                    $mail->isSMTP();
					$mail->Host = defined('MAIL_SMTP_HOST') ? MAIL_SMTP_HOST : 'smtp.gmail.com';
					$mail->Port = defined('MAIL_SMTP_PORT') ? MAIL_SMTP_PORT : 587;
					$mail->SMTPAuth = true;
					$mail->Username = MAIL_SMTP_USER;
					$mail->Password = MAIL_SMTP_PASS;
					$mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
					$mail->CharSet = 'UTF-8';
					$mail->setFrom(defined('MAIL_FROM_EMAIL') && MAIL_FROM_EMAIL ? MAIL_FROM_EMAIL : MAIL_SMTP_USER, defined('MAIL_FROM_NAME') ? MAIL_FROM_NAME : 'Trip Coordinator');
					$mail->addAddress($hotelEmail, $hotelName);
                    $mail->isHTML(true);
					// --- MODIFIED: Email Subject ---
					$mail->Subject = 'Hotel Booking Request for ' . $tourCode . ' - ' . $customerName;
					$mail->Body = $emailBodyHtml;
					$mail->AltBody = $altBodyText;
					$mail->send();
					$sentOk = true;
				} catch (\Throwable $ex) {
					$sendError = $ex->getMessage();
				}
			} else {
				$sendError = 'PHPMailer library not found.';
			}
		} else {
			$sendError = 'SMTP credentials not configured.';
		}

		if ($sentOk) {
			$messages[] = [
				'type' => 'success',
				'text' => $rangeText . ' ----> ' . $hotelName . ' status email sent'
			];
			$toMarkInformed = array_merge($toMarkInformed, $group['day_ids']);
		} else {
			$messages[] = [
				'type' => 'error',
				'text' => $rangeText . ' ----> ' . $hotelName . ' email failed (' . $sendError . ')'
			];
		}
	}

	if ($hasHotelInformed && !empty($toMarkInformed)) {
		$placeholders = implode(',', array_fill(0, count($toMarkInformed), '?'));
		$types = str_repeat('i', count($toMarkInformed));
		$upd = $conn->prepare("UPDATE itinerary_days SET hotel_informed = 1 WHERE id IN ($placeholders)");
		if ($upd) {
			$upd->bind_param($types, ...$toMarkInformed);
			$upd->execute();
			$upd->close();
		}
	}

	if (!$hasHotelInformed) {
		$messages[] = [
			'type' => 'info',
			'text' => "Schema note: 'hotel_informed' column not found; status not updated."
		];
	}

	respond('success', 'Hotel notification processing completed.', ['messages' => $messages]);
} catch (Throwable $e) {
    respond('error', 'Server error: ' . $e->getMessage());
}