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

// Fatal error catcher to avoid HTML responses on fatal errors
register_shutdown_function(function () {
    $err = error_get_last();
    if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
        while (ob_get_level() > 0) { ob_end_clean(); }
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Fatal server error: ' . $err['message'] . ' in ' . $err['file'] . ' on line ' . $err['line']]);
    }
});

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
$db_path = __DIR__ . '/db_connect.php';
if (file_exists($db_path)) {
    require_once $db_path;
} else {
    respond('error', 'Database connection file is missing.');
}

// Note: Mail configuration should be defined as constants
// MAIL_SMTP_USER, MAIL_SMTP_PASS, MAIL_SMTP_HOST, etc.
// You can define these in a config file and include it here

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
    $mode = isset($input['mode']) ? strtolower(trim($input['mode'])) : '';
    $isAmendment = ($mode === 'amendment');
    $test_mode = !empty($input['test_mode']);
    if ($trip_id <= 0) {
        respond('error', 'Invalid or missing trip_id.');
    }

	// Fetch Trip Details (Tour Code, Customer, and Package)
    $hasGuestDetails = false; $cT = $conn->query("SHOW COLUMNS FROM trips LIKE 'guest_details'"); if ($cT && $cT->num_rows>0) $hasGuestDetails = true;
	$tripDetailsStmt = $conn->prepare("SELECT tour_code, customer_name, trip_package_id".($hasGuestDetails?", guest_details":"")." FROM trips WHERE id = ?");
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
    $guestDetails = isset($tripData['guest_details']) ? trim($tripData['guest_details']) : '';
    $tripPackageId = isset($tripData['trip_package_id']) ? (int)$tripData['trip_package_id'] : 0;
	$tripDetailsStmt->close();

// Ensure email log table exists
    $conn->query("CREATE TABLE IF NOT EXISTS hotel_email_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        trip_id INT NOT NULL,
        hotel_id INT NOT NULL,
        email_type VARCHAR(20) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_trip_hotel (trip_id, hotel_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // --- VALIDATION BLOCK ---
	// Fetch ALL days, then validate only days where the package requires a hotel (if defined).
	$allDaysStmt = $conn->prepare("SELECT id, day_date, hotel_id, room_type_data FROM itinerary_days WHERE trip_id = ? ORDER BY day_date ASC");
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

    // Build set of days that require a hotel per package
    $requiredHotelDays = [];
    if ($tripPackageId > 0) {
        $reqStmt = $conn->prepare("SELECT day_number, hotel_id FROM package_day_requirements WHERE trip_package_id = ? ORDER BY day_number");
        if ($reqStmt) {
            $reqStmt->bind_param('i', $tripPackageId);
            $reqStmt->execute();
            $reqRes = $reqStmt->get_result();
            while ($rr = $reqRes->fetch_assoc()) {
                $dn = (int)$rr['day_number'];
                if (!empty($rr['hotel_id'])) { $requiredHotelDays[$dn] = true; }
            }
            $reqStmt->close();
        }
    }
    $validateAll = empty($requiredHotelDays);

	while ($r = $allDaysResult->fetch_assoc()) {
		$dayIndexById[(int)$r['id']] = $idx;
        $requiresHotel = $validateAll ? true : isset($requiredHotelDays[$idx]);
        if (!$requiresHotel) { $idx++; continue; }
		if (empty($r['hotel_id']) || $r['hotel_id'] == 0) {
			$validationMessages[] = [
				'type'       => 'error',
				'day_id'     => (int)$r['id'],
				'day_number' => $idx,
				'text'       => 'Day ' . $idx . ' (' . $r['day_date'] . '): Missing hotel assignment.'
			];
		} else {
			// Check if room quantities are specified
			$hasRooms = false;
			if (!empty($r['room_type_data'])) {
				try {
					$parsed = json_decode($r['room_type_data'], true);
					if ($parsed && is_array($parsed)) {
						foreach ($parsed as $qty) {
							if ($qty > 0) { $hasRooms = true; break; }
						}
					}
				} catch (Exception $e) { /* ignore */ }
			}
			
			if (!$hasRooms) {
				$validationMessages[] = [
					'type'       => 'error',
					'day_id'     => (int)$r['id'],
					'day_number' => $idx,
					'text'       => 'Day ' . $idx . ' (' . $r['day_date'] . '): Hotel assigned, but missing room quantities.'
				];
			}
		}
		$idx++;
	}
	$allDaysStmt->close();

	if (!empty($validationMessages)) {
		respond('error', 'Cannot send emails. Required hotel days must have hotel and room quantities.', ['messages' => $validationMessages]);
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
           id.room_type_data,
		       id.services_provided,
		       h.name AS hotel_name,
		       h.email AS hotel_email
        FROM itinerary_days id
        LEFT JOIN hotels h ON id.hotel_id = h.id
        WHERE id.trip_id = ?
		  AND id.hotel_id IS NOT NULL
	" . (($hasHotelInformed && !$isAmendment) ? "\n\t\t  AND (id.hotel_informed IS NULL OR id.hotel_informed = 0)\n" : "\n") . "
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
		// Parse room quantities
		$roomQuantities = [];
		if (!empty($row['room_type_data'])) {
			try {
				$parsed = json_decode($row['room_type_data'], true);
				if ($parsed && is_array($parsed)) {
					foreach ($parsed as $type => $qty) {
						if ($qty > 0) {
							$roomQuantities[] = $qty . ' ' . ucfirst($type);
						}
					}
				}
			} catch (Exception $e) {
				// Ignore parsing errors
			}
		}
		$roomSummary = !empty($roomQuantities) ? implode(', ', $roomQuantities) : 'Not specified';
		
		$byHotel[$hid]['bookings'][] = [
			'date' => $row['day_date'],
			'room_type' => $roomSummary,
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
		// Determine amendment ordinal for this hotel (only for amendment mode)
		$amendOrdinal = '';
		$amendNum = 0;
		if ($isAmendment) {
			try {
				$cntStmt = $conn->prepare("SELECT COUNT(*) AS c FROM hotel_email_logs WHERE trip_id = ? AND hotel_id = ? AND email_type = 'amendment'");
				if ($cntStmt) {
					$cntStmt->bind_param('ii', $trip_id, $hid);
					$cntStmt->execute();
					$res = $cntStmt->get_result();
					$prev = ($res && $res->num_rows>0) ? intval($res->fetch_assoc()['c'] ?? 0) : 0;
					$amendNum = $prev + 1;
					$amendOrdinal = $amendNum . (in_array($amendNum % 100, [11,12,13]) ? 'th' : (['','st','nd','rd'][min($amendNum % 10,3)] ?? 'th'));
					$cntStmt->close();
				}
			} catch (\Throwable $e) { /* ignore */ }
		}
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

			// Aggregate and normalize services across the block (B/L/D unique, ordered)
			$tokens = [];
			foreach ($block['daily_services'] as $service) {
				$s = strtoupper(trim((string)$service));
				if ($s === '') { continue; }
				$parts = preg_split('/[\s,;]+/', $s);
				foreach ($parts as $p) {
					$p = trim($p);
					if ($p === '') { continue; }
					if (in_array($p, ['B','L','D'], true)) { $tokens[] = $p; }
				}
			}
			$tokens = array_values(array_unique($tokens));
			// Order B, L, D
			$order = ['B'=>0,'L'=>1,'D'=>2];
			usort($tokens, function($a,$b) use ($order){ return ($order[$a]??9) <=> ($order[$b]??9); });
			$servicesDisplay = !empty($tokens) ? implode(', ', $tokens) : 'N/A';
			$servicesCellContent = htmlspecialchars($servicesDisplay);
			
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

// Build changes since last email for amendment mode
        $changesListHtml = '';
        if ($isAmendment) {
            try {
                // Find latest email log (any type) for this hotel
                $lastLogAt = null;
                $logStmt = $conn->prepare("SELECT MAX(created_at) AS last_sent FROM hotel_email_logs WHERE trip_id = ? AND hotel_id = ?");
                if ($logStmt) {
                    $logStmt->bind_param('ii', $trip_id, $hid);
                    $logStmt->execute();
                    $logRes = $logStmt->get_result();
                    $lastLogAt = ($logRes && $logRes->num_rows>0) ? ($logRes->fetch_assoc()['last_sent'] ?? null) : null;
                    $logStmt->close();
                }
                if ($lastLogAt) {
                    $chg = $conn->prepare("SELECT room_type, old_value, new_value, created_at FROM pax_amendments WHERE trip_id = ? AND created_at > ? ORDER BY created_at ASC");
                    if ($chg) {
                        $chg->bind_param('is', $trip_id, $lastLogAt);
                        $chg->execute();
                        $r = $chg->get_result();
                        $latest = [];
                        while ($row = $r->fetch_assoc()) {
                            $rt = strtolower($row['room_type']);
                            $latest[$rt] = ['old'=>$row['old_value'], 'new'=>$row['new_value']];
                        }
                        $chg->close();
                        if (!empty($latest)) {
                            $items = [];
                            foreach (['double','twin','single','triple'] as $rt) {
                                if (isset($latest[$rt])) {
                                    $cap = ucfirst($rt);
                                    $items[] = "<li><strong>$cap</strong>: " . intval($latest[$rt]['old']) . " → <span style='color:#065f46;font-weight:700;'>" . intval($latest[$rt]['new']) . "</span></li>";
                                }
                            }
                            if (!empty($items)) {
                                $changesListHtml = "<ul style='margin:6px 0 0 16px; padding:0;'>" . implode('', $items) . "</ul>";
                            }
                        }
                    }
                }
            } catch (\Throwable $e) { /* ignore diff errors */ }
        }

$preface = $isAmendment
            ? "<p style='color:#b45309;'><strong>Amendment Notice:</strong> This is the <strong>" . ($amendOrdinal ?: 'amendment') . "</strong> to the booking. There has been a change in booking details (e.g., number of rooms/services). Updated details are below.</p>" . $changesListHtml
            : "";

        $emailBodyHtml = "
			<p>Dear {$hotelName},</p>
			" . $preface . "
			<p>We would like to " . ($isAmendment ? "amend our booking for" : "request a booking for") . " our guest(s), <strong>" . htmlspecialchars($customerName) . "</strong>, under the tour code <strong>" . htmlspecialchars($tourCode) . "</strong>.</p>" .
            ($guestDetails!=='' ? ("<p><strong>Guest Details:</strong> " . nl2br(htmlspecialchars($guestDetails)) . "</p>") : "") .
			"<p>Please find the " . ($isAmendment ? "updated" : "booking") . " details below:</p>
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
			<p>Kindly confirm " . ($isAmendment ? "the amendment" : "this booking") . " at your earliest convenience.</p>
			<p>Thank you.</p>
		";
		$altBodyText = "Dear {$hotelName},\n\nWe would like to request a booking for our guest(s), " . htmlspecialchars($customerName) . ", under the tour code " . htmlspecialchars($tourCode) . ".\n\nPlease find the booking details below:\n\n" . implode("\n", $altTextLines) . "\n\nKindly confirm this booking at your earliest convenience.\n\nThank you.";
		// --- END: MODIFICATION ---

		$rangeText = $formatRanges($group['day_numbers']);
		$sentOk = false;
		$sendError = '';

		if (defined('MAIL_SMTP_USER') && MAIL_SMTP_USER && defined('MAIL_SMTP_PASS') && MAIL_SMTP_PASS) {
			$hasLib = (file_exists(__DIR__ . '/../libs/PHPMailer/src/PHPMailer.php'));
			if ($hasLib) {
				try {
					require_once __DIR__ . '/../libs/PHPMailer/src/PHPMailer.php';
					require_once __DIR__ . '/../libs/PHPMailer/src/SMTP.php';
					require_once __DIR__ . '/../libs/PHPMailer/src/Exception.php';
					$mail = new \PHPMailer\PHPMailer\PHPMailer(true);
		                  $mail->isSMTP();
					$mail->Host = defined('MAIL_SMTP_HOST') ? MAIL_SMTP_HOST : 'smtp.gmail.com';
					$mail->Port = defined('MAIL_SMTP_PORT') ? MAIL_SMTP_PORT : 587;
					$mail->SMTPAuth = true;
					$mail->Username = MAIL_SMTP_USER;
					$mail->Password = MAIL_SMTP_PASS;
					$mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
					$mail->CharSet = 'UTF-8';
					$mail->SMTPDebug = 2; // Enable verbose debug output
					$mail->Debugoutput = function($str, $level) {
						error_log("PHPMailer Debug [{$level}]: {$str}");
					};
					$mail->setFrom(defined('MAIL_FROM_EMAIL') && MAIL_FROM_EMAIL ? MAIL_FROM_EMAIL : MAIL_SMTP_USER, defined('MAIL_FROM_NAME') ? MAIL_FROM_NAME : 'Trip Coordinator');
					$mail->addAddress($hotelEmail, $hotelName);
		                  $mail->isHTML(true);
					// --- MODIFIED: Email Subject ---
$mail->Subject = ($isAmendment ? ('Amendment ' . ($amendNum>0?('#'.$amendNum.' '):'') . 'Booking Update') : 'Hotel Booking Request') . ' for ' . $tourCode . ' - ' . $customerName;
					$mail->Body = $emailBodyHtml;
					$mail->AltBody = $altBodyText;
					$mail->send();
					$sentOk = true;
				} catch (\Throwable $ex) {
					$sendError = $ex->getMessage();
					error_log("SMTP Error for hotel {$hotelEmail}: {$sendError}");
				}
			} else {
				$sendError = 'PHPMailer library not found.';
			}
		} else {
			$sendError = 'SMTP credentials not configured.';
		}

		if ($test_mode) {
			$messages[] = [
				'type' => 'info',
				'text' => '[TEST MODE] ' . $rangeText . ' ----> ' . $hotelName . ' (no email sent)'
			];
			$sentOk = true;
} elseif ($sentOk) {
$messages[] = [
				'type' => 'success',
				'text' => $rangeText . ' ----> ' . $hotelName . ' ' . ($isAmendment ? ('amendment ' . ($amendNum>0?('#'.$amendNum.' '):'') . 'email sent') : 'booking request sent')
			];
            // Log the email send
            try {
                $etype = $isAmendment ? 'amendment' : 'initial';
                $hidToLog = isset($group['hotel_id']) ? (int)$group['hotel_id'] : (int)$hid;
                if ($hidToLog > 0) {
                    $logStmt = $conn->prepare("INSERT INTO hotel_email_logs (trip_id, hotel_id, email_type) VALUES (?, ?, ?)");
                    if ($logStmt) { $logStmt->bind_param('iis', $trip_id, $hidToLog, $etype); $logStmt->execute(); $logStmt->close(); }
                }
            } catch (\Throwable $ex) { /* ignore */ }
			if (!$isAmendment) { $toMarkInformed = array_merge($toMarkInformed, $group['day_ids']); }
		} else {
			$messages[] = [
				'type' => 'error',
				'text' => $rangeText . ' ----> ' . $hotelName . ' email failed (' . $sendError . ')'
			];
		}
	}

if (!$test_mode && $hasHotelInformed && !$isAmendment && !empty($toMarkInformed)) {
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

respond('success', $isAmendment ? 'Hotel amendment processing completed.' : 'Hotel notification processing completed.', ['messages' => $messages]);
} catch (Throwable $e) {
    respond('error', 'Server error: ' . $e->getMessage());
}