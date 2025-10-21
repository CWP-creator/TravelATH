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
    $test_mode = !empty($input['test_mode']);
    if ($trip_id <= 0) {
        respond('error', 'Invalid or missing trip_id.');
    }

	// --- Fetch Trip Details (Tour Code and Customer Name) ---
	$tripDetailsStmt = $conn->prepare("SELECT tour_code, customer_name, start_date, end_date FROM trips WHERE id = ?");
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
	$startDate = $tripData['start_date'];
	$endDate = $tripData['end_date'];
	$tripDetailsStmt->close();

	// --- Build day index for reference ---
	$allDaysStmt = $conn->prepare("SELECT id FROM itinerary_days WHERE trip_id = ? ORDER BY day_date ASC");
	if (!$allDaysStmt) {
		respond('error', 'Database prepare failed: ' . $conn->error);
	}
	$allDaysStmt->bind_param('i', $trip_id);
	$allDaysStmt->execute();
	$allDaysResult = $allDaysStmt->get_result();

	$dayIndexById = [];
	$idx = 1;
	
	while ($r = $allDaysResult->fetch_assoc()) {
		$dayIndexById[(int)$r['id']] = $idx++;
	}
	$allDaysStmt->close();

	// Detect if guide_informed column exists to avoid SQL errors on older schemas
	$hasGuideInformed = false;
	$colCheck = $conn->query("SHOW COLUMNS FROM itinerary_days LIKE 'guide_informed'");
	if ($colCheck && $colCheck->num_rows > 0) {
		$hasGuideInformed = true;
	}

	$sql = "
		SELECT id.id AS itinerary_day_id,
           id.day_date,
           id.guide_id,
		   id.notes,
		   id.services_provided,
		   g.name AS guide_name,
		   g.email AS guide_email,
		   g.language AS guide_language
        FROM itinerary_days id
        LEFT JOIN guides g ON id.guide_id = g.id
        WHERE id.trip_id = ?
		  AND id.guide_id IS NOT NULL
	" . ($hasGuideInformed ? "\n\t\t  AND (id.guide_informed IS NULL OR id.guide_informed = 0)\n" : "\n") . "
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
		respond('success', 'No uninformed guide assignments found for this trip. All guides may have been previously notified.', ['messages' => []]);
	}

	$messages = [];
	$toMarkInformed = [];

	// Group all selected days by guide
	$byGuide = [];
	foreach ($rows as $row) {
		$gid = (int)$row['guide_id'];
		if (!isset($byGuide[$gid])) {
			$byGuide[$gid] = [
				'guide_id' => $gid,
				'guide_name' => $row['guide_name'],
				'guide_email' => $row['guide_email'],
				'guide_language' => $row['guide_language'],
        		'day_ids' => [],
				'day_numbers' => [],
				'assignments' => []
			];
		}
		$dayId = (int)$row['itinerary_day_id'];
		$byGuide[$gid]['day_ids'][] = $dayId;
		$byGuide[$gid]['day_numbers'][] = isset($dayIndexById[$dayId]) ? $dayIndexById[$dayId] : null;
		
		$byGuide[$gid]['assignments'][] = [
			'date' => $row['day_date'],
			'notes' => $row['notes'] ?: '',
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

	foreach ($byGuide as $gid => $group) {
		$guideName = $group['guide_name'] ?: ('Guide ' . $gid);
		$guideEmail = $group['guide_email'];
		if (empty($guideEmail)) {
			$messages[] = [
				'type' => 'error',
				'text' => "No email set for $guideName; skipping."
			];
			continue;
		}
		
		$assignments = $group['assignments'];
		$tableRowsHtml = '';
		$altTextLines = []; // For the plain text version of the email
		
		foreach ($assignments as $assignment) {
			$assignmentDate = htmlspecialchars($assignment['date']);
			$notes = htmlspecialchars($assignment['notes']) ?: 'General tour duties';
			
			// Build the HTML table row
			$tableRowsHtml .= "
				<tr>
					<td style='border: 1px solid #ddd; padding: 8px;'>{$assignmentDate}</td>
					<td style='border: 1px solid #ddd; padding: 8px;'>{$notes}</td>
				</tr>
			";
			
			// For plain text email
			$altTextLines[] = "Date: $assignmentDate, Duties: $notes";
		}

		$emailBodyHtml = "
			<p>Dear " . htmlspecialchars($guideName) . ",</p>
			<p>You have been assigned as a guide for our guest(s), <strong>" . htmlspecialchars($customerName) . "</strong>, under the tour code <strong>" . htmlspecialchars($tourCode) . "</strong>.</p>
<p><strong>Trip Duration:</strong> " . htmlspecialchars($startDate) . " to " . htmlspecialchars($endDate) . "</p>
			<p>Please find your duty schedule below:</p>
			<table style='width: 100%; border-collapse: collapse; font-family: sans-serif; font-size: 14px; text-align: left;'>
				<thead style='background-color: #f2f2f2;'>
					<tr>
						<th style='border: 1px solid #ddd; padding: 8px;'>Date</th>
						<th style='border: 1px solid #ddd; padding: 8px;'>Duties & Activities</th>
					</tr>
				</thead>
				<tbody>
					{$tableRowsHtml}
				</tbody>
			</table>
			<p>Please confirm your availability and prepare accordingly for the assigned duties.</p>
			<p>Thank you for your service.</p>
			<br>
			<p>Best regards,<br>Trip Coordination Team</p>
		";
$altBodyText = "Dear " . htmlspecialchars($guideName) . ",\n\nYou have been assigned as a guide for our guest(s), " . htmlspecialchars($customerName) . ", under the tour code " . htmlspecialchars($tourCode) . ".\n\nTrip Duration: " . htmlspecialchars($startDate) . " to " . htmlspecialchars($endDate) . "\n\nPlease find your duty schedule below:\n\n" . implode("\n", $altTextLines) . "\n\nPlease confirm your availability and prepare accordingly for the assigned duties.\n\nThank you for your service.\n\nBest regards,\nTrip Coordination Team";

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
					$mail->setFrom(defined('MAIL_FROM_EMAIL') && MAIL_FROM_EMAIL ? MAIL_FROM_EMAIL : MAIL_SMTP_USER, defined('MAIL_FROM_NAME') ? MAIL_FROM_NAME : 'Trip Coordinator');
					$mail->addAddress($guideEmail, $guideName);
                    $mail->isHTML(true);
					$mail->Subject = 'Guide Assignment - ' . $tourCode . ' - ' . $customerName;
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

		if ($test_mode) {
			$messages[] = [
				'type' => 'info',
				'text' => '[TEST MODE] ' . $rangeText . ' ----> ' . $guideName . ' (no email sent)'
			];
			$sentOk = true; // consider as success for flow, but do not mark informed
		} elseif ($sentOk) {
			$messages[] = [
				'type' => 'success',
				'text' => $rangeText . ' ----> ' . $guideName . ' duty notification sent'
			];
			$toMarkInformed = array_merge($toMarkInformed, $group['day_ids']);
		} else {
			$messages[] = [
				'type' => 'error',
				'text' => $rangeText . ' ----> ' . $guideName . ' email failed (' . $sendError . ')'
			];
		}
	}

	if (!$test_mode && $hasGuideInformed && !empty($toMarkInformed)) {
		$placeholders = implode(',', array_fill(0, count($toMarkInformed), '?'));
		$types = str_repeat('i', count($toMarkInformed));
		$upd = $conn->prepare("UPDATE itinerary_days SET guide_informed = 1 WHERE id IN ($placeholders)");
		if ($upd) {
			$upd->bind_param($types, ...$toMarkInformed);
			$upd->execute();
			$upd->close();
		}
	}

	if (!$hasGuideInformed) {
		$messages[] = [
			'type' => 'info',
			'text' => "Schema note: 'guide_informed' column not found; status not updated."
		];
	}

	respond('success', 'Guide notification processing completed.', ['messages' => $messages]);
} catch (Throwable $e) {
    respond('error', 'Server error: ' . $e->getMessage());
}