<?php
// Start output buffering immediately to catch any unwanted output
ob_start();

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit(0); }

ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(0);

// Fatal error catcher to avoid HTML responses on fatal errors
register_shutdown_function(function () {
    $err = error_get_last();
    if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
        while (ob_get_level() > 0) { ob_end_clean(); }
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Fatal server error: ' . $err['message'] . ' in ' . $err['file'] . ' on line ' . $err['line']]);
    }
});

set_error_handler(function($severity, $message, $file, $line) {
    while (ob_get_level() > 0) { ob_end_clean(); }
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => "PHP Error: $message in $file on line $line"]);
    exit;
});

require_once __DIR__ . '/db_connect.php';

function respond($status, $message, $extra = []) {
    while (ob_get_level() > 0) { ob_end_clean(); }
    header('Content-Type: application/json');
    echo json_encode(array_merge(['status' => $status, 'message' => $message], $extra));
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $trip_id = isset($input['trip_id']) ? intval($input['trip_id']) : 0;
    $test_mode = !empty($input['test_mode']);
    $override_email = isset($input['override_email']) ? filter_var($input['override_email'], FILTER_VALIDATE_EMAIL) : null;
    $use_override = !empty($override_email);
    if ($trip_id <= 0) respond('error', 'Invalid or missing trip_id.');

    // Trip details
    $tripStmt = $conn->prepare("SELECT tour_code, customer_name, start_date, end_date FROM trips WHERE id = ?");
    if (!$tripStmt) respond('error', 'DB prepare failed: ' . $conn->error);
    $tripStmt->bind_param('i', $trip_id);
    $tripStmt->execute();
    $tripRes = $tripStmt->get_result();
    if ($tripRes->num_rows === 0) respond('error', 'Trip not found.');
    $trip = $tripRes->fetch_assoc();
    $tripStmt->close();

    // Build day index
    $idxStmt = $conn->prepare("SELECT id FROM itinerary_days WHERE trip_id = ? ORDER BY day_date ASC");
    $idxStmt->bind_param('i', $trip_id); $idxStmt->execute(); $idxRes = $idxStmt->get_result();
    $dayIndexById = []; $n=1; while($r=$idxRes->fetch_assoc()){ $dayIndexById[(int)$r['id']]=$n++; }
    $idxStmt->close();

    // Detect vehicle_informed column
    $hasVehicleInformed = false;
    $colCheck = $conn->query("SHOW COLUMNS FROM itinerary_days LIKE 'vehicle_informed'");
    if ($colCheck && $colCheck->num_rows > 0) { $hasVehicleInformed = true; }

    // Fetch uninformed vehicle assignments
    $sql = "SELECT id.id AS itinerary_day_id, id.day_date, id.vehicle_id, v.vehicle_name, v.capacity, v.email AS vehicle_email
            FROM itinerary_days id
            LEFT JOIN vehicles v ON id.vehicle_id = v.id
            WHERE id.trip_id = ? AND id.vehicle_id IS NOT NULL" .
            ($hasVehicleInformed ? " AND (id.vehicle_informed IS NULL OR id.vehicle_informed = 0)" : "").
            " ORDER BY id.day_date ASC";
    $stmt = $conn->prepare($sql);
    if (!$stmt) respond('error', 'DB prepare failed: ' . $conn->error);
    $stmt->bind_param('i', $trip_id); $stmt->execute(); $res = $stmt->get_result();
    $rows = $res->fetch_all(MYSQLI_ASSOC); $stmt->close();

    if (empty($rows)) respond('success', 'No uninformed vehicle assignments found for this trip.', ['messages'=>[]]);

    $byVehicle = [];
    foreach ($rows as $row) {
        $vid = (int)$row['vehicle_id'];
        if (!isset($byVehicle[$vid])) {
            $byVehicle[$vid] = [
                'vehicle_id'=>$vid,
                'vehicle_name'=>$row['vehicle_name'],
                'vehicle_email'=>$row['vehicle_email'],
                'capacity'=>$row['capacity'],
                'day_ids'=>[],
                'day_numbers'=>[],
                'assignments'=>[]
            ];
        }
        $dayId = (int)$row['itinerary_day_id'];
        $byVehicle[$vid]['day_ids'][] = $dayId;
        $byVehicle[$vid]['day_numbers'][] = isset($dayIndexById[$dayId]) ? $dayIndexById[$dayId] : null;
        $byVehicle[$vid]['assignments'][] = [ 'date'=>$row['day_date'] ];
    }

    $formatRanges = function(array $nums){ $nums=array_values(array_filter($nums, fn($v)=>$v!==null)); sort($nums); if(empty($nums))return''; $ranges=[]; $s=$nums[0]; $e=$s; for($i=1;$i<count($nums);$i++){ if($nums[$i]===$e+1){$e=$nums[$i];} else { $ranges[]=($s===$e?"day $s":"day $s to day $e"); $s=$nums[$i]; $e=$s; } } $ranges[]=($s===$e?"day $s":"day $s to day $e"); return implode(', ',$ranges); };

    $messages = []; $toMarkInformed = [];

    foreach ($byVehicle as $vid => $group) {
        $vehName = $group['vehicle_name'] ?: ('Vehicle ' . $vid);
        $vehEmail = $group['vehicle_email'];
        if (empty($vehEmail)) { $messages[] = ['type'=>'error','text'=>"No email set for $vehName; skipping."]; continue; }

        // Build email body
        $rowsHtml = '';
        $altLines = [];
        foreach ($group['assignments'] as $a) {
            $d = htmlspecialchars($a['date']);
            $rowsHtml .= "<tr><td style='border:1px solid #ddd;padding:8px;'>$d</td></tr>";
            $altLines[] = "Date: $d";
        }

        $html = "<p>Dear Driver/Operator of <strong>".htmlspecialchars($vehName)."</strong>,</p>".
                "<p>You are assigned for guest <strong>".htmlspecialchars($trip['customer_name'])."</strong> (Tour: <strong>".htmlspecialchars($trip['tour_code'])."</strong>).</p>".
                "<p><strong>Trip Duration:</strong> ".htmlspecialchars($trip['start_date'])." to ".htmlspecialchars($trip['end_date'])."</p>".
                "<table style='width:100%;border-collapse:collapse;font-family:sans-serif;font-size:14px;'><thead style='background:#f2f2f2'><tr><th style='border:1px solid #ddd;padding:8px;'>Date</th></tr></thead><tbody>$rowsHtml</tbody></table>".
                "<p>Please confirm your availability.</p>";
        $alt = "You are assigned for guest ".$trip['customer_name']." (Tour: ".$trip['tour_code'].").\n" . implode("\n",$altLines);

        $sentOk=false; $err='';
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
                    $mail->SMTPAuth = true; $mail->Username = MAIL_SMTP_USER; $mail->Password = MAIL_SMTP_PASS;
                    $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS; $mail->CharSet='UTF-8';
                    $mail->setFrom(defined('MAIL_FROM_EMAIL') && MAIL_FROM_EMAIL ? MAIL_FROM_EMAIL : MAIL_SMTP_USER, defined('MAIL_FROM_NAME') ? MAIL_FROM_NAME : 'Trip Coordinator');
                    if ($use_override) {
                        $mail->addAddress($override_email, 'Override Recipient'); $mail->isHTML(true);
                        $mail->Subject = '[OVERRIDE] Vehicle Assignment - ' . $trip['tour_code'] . ' - ' . $trip['customer_name'];
                        $mail->Body = "<p><strong>Intended recipient:</strong> " . htmlspecialchars($vehEmail ?: 'N/A') . " (" . htmlspecialchars($vehName ?: 'N/A') . ")</p>" . $html;
                        $mail->AltBody = "Intended recipient: " . ($vehEmail ?: 'N/A') . " (" . ($vehName ?: 'N/A') . ")\n\n" . $alt;
                    } else {
                        $mail->addAddress($vehEmail, $vehName); $mail->isHTML(true);
                        $mail->Subject = 'Vehicle Assignment - ' . $trip['tour_code'] . ' - ' . $trip['customer_name'];
                        $mail->Body = $html; $mail->AltBody = $alt;
                    }
                    $mail->send(); $sentOk=true;
                } catch (\Throwable $ex) { $err=$ex->getMessage(); }
            } else { $err='PHPMailer library not found.'; }
        } else { $err='SMTP credentials not configured.'; }

        $rangeText = $formatRanges($group['day_numbers']);
        if ($test_mode) {
            $messages[] = ['type'=>'info','text'=> '[TEST MODE] ' . $rangeText . ' ----> ' . $vehName . ' (no email sent)'];
            $sentOk = true;
        } elseif ($sentOk) {
            $messages[] = ['type'=>'success','text'=> ($use_override ? '[OVERRIDE] ' : '') . $rangeText . ' ----> ' . $vehName . ' duty notification sent'];
            $toMarkInformed = array_merge($toMarkInformed, $group['day_ids']);
        } else {
            $messages[] = ['type'=>'error','text'=> $rangeText . ' ----> ' . $vehName . ' email failed (' . $err . ')'];
        }
    }

    if (!$test_mode && !$use_override && $hasVehicleInformed && !empty($toMarkInformed)) {
        $placeholders = implode(',', array_fill(0, count($toMarkInformed), '?'));
        $types = str_repeat('i', count($toMarkInformed));
        $upd = $conn->prepare("UPDATE itinerary_days SET vehicle_informed = 1 WHERE id IN ($placeholders)");
        if ($upd) { $upd->bind_param($types, ...$toMarkInformed); $upd->execute(); $upd->close(); }
    }

    if (!$hasVehicleInformed) {
        $messages[] = ['type'=>'info','text'=>"Schema note: 'vehicle_informed' column not found; status not updated."];
    }

    respond('success','Vehicle notification processing completed.', ['messages'=>$messages]);

} catch (Throwable $e) { respond('error','Server error: ' . $e->getMessage()); }
