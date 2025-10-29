<?php
// Ensure clean JSON-only responses and catch fatals
ob_start();
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
set_error_handler(function($severity, $message, $file, $line){
    while (ob_get_level() > 0) { ob_end_clean(); }
    if (!headers_sent()) { header('Content-Type: application/json'); }
    echo json_encode(['status'=>'error','message'=>"PHP Error: $message in $file on line $line"]);
    exit;
});
register_shutdown_function(function(){
    $err = error_get_last();
    if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])){
        while (ob_get_level() > 0) { ob_end_clean(); }
        if (!headers_sent()) { header('Content-Type: application/json'); }
        echo json_encode(['status'=>'error','message'=>'Fatal server error: '.$err['message'].' in '.$err['file'].' on line '.$err['line']]);
    }
});

// Mark this request as API so session checker returns JSON instead of redirecting
if (!defined('IS_API')) { define('IS_API', true); }

// Try to include session check with error handling
if (file_exists('../../utils/check_session.php')) {
    require_once '../../utils/check_session.php';
} else {
    // Fallback session check
    session_start();
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Authentication required',
            'redirect' => 'login.html'
        ]);
        exit;
    }
}

include '../../src/services/db_connect.php';

// Ensure users schema has must_change_password flag
function ensureUsersSchema($conn){
    $res = $conn->query("SHOW COLUMNS FROM users LIKE 'must_change_password'");
    if (!$res || $res->num_rows===0){ $conn->query("ALTER TABLE users ADD COLUMN must_change_password TINYINT(1) DEFAULT 0"); }
}
ensureUsersSchema($conn);

$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Main switch to handle different actions
switch ($action) {
    case 'getTrips':
        getTrips($conn);
        break;
    case 'getTripPackages':
        getTripPackages($conn);
        break;
    case 'addTripPackage':
        addTripPackage($conn);
        break;
    case 'updateTripPackage':
        updateTripPackage($conn);
        break;
    case 'deleteTripPackage':
        deleteTripPackage($conn);
        break;
    case 'addTrip':
        addTrip($conn);
        break;
    case 'updateTrip':
        updateTrip($conn);
        break;
    case 'deleteTrip':
        deleteTrip($conn);
        break;
    case 'cancelTrip':
        cancelTrip($conn);
        break;
    case 'getItinerary':
        getItinerary($conn);
        break;
    case 'updateItinerary':
        updateItinerary($conn);
        break;
    case 'getHotels':
        getHotels($conn);
        break;
    case 'addHotel':
        addHotel($conn);
        break;
    case 'updateHotel':
        updateHotel($conn);
        break;
    case 'deleteHotel':
        deleteHotel($conn);
        break;
    case 'getVehicles':
        getVehicles($conn);
        break;
    case 'addVehicle':
        addVehicle($conn);
        break;
    case 'updateVehicle':
        updateVehicle($conn);
        break;
    case 'deleteVehicle':
        deleteVehicle($conn);
        break;
    case 'getGuides':
        getGuides($conn);
        break;
    case 'addGuide':
        addGuide($conn);
        break;
    case 'updateGuide':
        updateGuide($conn);
        break;
    case 'deleteGuide':
        deleteGuide($conn);
        break;
    case 'getPackageHotels':
    getPackageHotels($conn);
        break;
    case 'getPackageRequirements':
        getPackageRequirements($conn);
        break;

    case 'getRoomTypes':
    getRoomTypes($conn);
        break;
    case 'getHotelRecords':
        getHotelRecords($conn);
        break;
    case 'getGuideRecords':
        getGuideRecords($conn);
        break;
    case 'getVehicleRecords':
        getVehicleRecords($conn);
        break;
    case 'getDayRoster':
        getDayRoster($conn);
        break;
    case 'checkGuideConflict':
        checkGuideConflict($conn);
        break;
    case 'updateTripPax':
        updateTripPax($conn);
        break;
    case 'getTripArrivals':
        getTripArrivals($conn);
        break;
    case 'saveTripArrivals':
        saveTripArrivals($conn);
        break;
    case 'deleteTripArrival':
        deleteTripArrival($conn);
        break;
    case 'getTripDepartures':
        getTripDepartures($conn);
        break;
    case 'saveTripDepartures':
        saveTripDepartures($conn);
        break;
    case 'getArrivalInsights':
        getArrivalInsights($conn);
        break;
    case 'getDepartureInsights':
        getDepartureInsights($conn);
        break;
    case 'getPackageRecords':
        getPackageRecords($conn);
        break;
    case 'getTripGuests':
        getTripGuests($conn);
        break;
    case 'saveTripGuests':
        saveTripGuests($conn);
        break;
    case 'getNextTourCode':
        getNextTourCode($conn);
        break;
    case 'analyzeImportFile':
        analyzeImportFile($conn);
        break;
    case 'importPackages':
        importPackages($conn);
        break;
    case 'getPaxDetails':
        getPaxDetails($conn);
        break;
    case 'savePaxDetails':
        savePaxDetails($conn);
        break;
    case 'getPaxAmendments':
        getPaxAmendments($conn);
        break;
    case 'updateItineraryRooms':
        updateItineraryRooms($conn);
        break;
    case 'getActivities':
        getActivities($conn);
        break;
    case 'addActivity':
        addActivity($conn);
        break;
    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action specified.']);
        break;
}

// ============= PACKAGE RECORDS =============
function getPackageRecords($conn){
    $sql = "SELECT t.id, t.file_name, t.start_date, t.end_date, p.name AS package_name
            FROM trips t
            JOIN trip_packages p ON t.trip_package_id = p.id
            WHERE t.status <> 'Cancelled'
            ORDER BY p.name, t.start_date";
    $res = $conn->query($sql);
    if (!$res){ echo json_encode(['status'=>'error','message'=>'DB error: '.$conn->error]); return; }
    $rows = [];
    while ($r = $res->fetch_assoc()){ $rows[] = $r; }
    echo json_encode(['status'=>'success','data'=>$rows]);
}

// ============= TRIP MANAGEMENT =============
function getTrips($conn) {
    $sql = "SELECT t.*, p.name as package_name
            FROM trips t
            JOIN trip_packages p ON t.trip_package_id = p.id
            ORDER BY t.start_date DESC";
    $result = $conn->query($sql);

    if (!$result) {
        echo json_encode(['status' => 'error', 'message' => 'Database query failed: ' . $conn->error]);
        return;
    }

    $trips = [];
    while ($row = $result->fetch_assoc()) {
        $trips[] = $row;
    }
    echo json_encode(['status' => 'success', 'data' => $trips]);
}

function getTripPackages($conn) {
    $result = $conn->query("SELECT id, name, code, description, No_of_Days, total_price FROM trip_packages ORDER BY name");

    if (!$result) {
        echo json_encode(['status' => 'error', 'message' => 'Database query failed: ' . $conn->error]);
        return;
    }

    $packages = [];
    while ($row = $result->fetch_assoc()) {
        $packages[] = $row;
    }
    echo json_encode(['status' => 'success', 'data' => $packages]);
}
function getNextTourCode($conn){
    $pkg_id = isset($_GET['trip_package_id']) ? intval($_GET['trip_package_id']) : 0;
    if ($pkg_id<=0){ echo json_encode(['status'=>'error','message'=>'trip_package_id required']); return; }
    $pkg = $conn->prepare("SELECT code FROM trip_packages WHERE id=?"); if(!$pkg){ echo json_encode(['status'=>'error','message'=>'DB error']); return; }
    $pkg->bind_param('i',$pkg_id); $pkg->execute(); $res=$pkg->get_result(); if($res->num_rows===0){ echo json_encode(['status'=>'error','message'=>'Package not found']); return; }
    $code = trim($res->fetch_assoc()['code']??''); $pkg->close(); if($code===''){ echo json_encode(['status'=>'error','message'=>'Package code is empty']); return; }
    $prefix = $code . '-';
    $q = $conn->prepare("SELECT tour_code FROM trips WHERE tour_code LIKE CONCAT(?, '%')"); $q->bind_param('s',$prefix); $q->execute(); $rs=$q->get_result();
    $maxN = 0; while($row=$rs->fetch_assoc()){ if (preg_match('/^'.preg_quote($code,'/').'-00(\d+)$/',$row['tour_code'],$m)){ $n=intval($m[1]); if($n>$maxN)$maxN=$n; } }
    $next = $maxN+1; $tour = sprintf('%s-00%02d',$code,$next);
    echo json_encode(['status'=>'success','data'=>['tour_code'=>$tour]]);
}

function addTrip($conn) {
    // New: file_name (primary), fallback to customer_name for backward compat
    $file_name = isset($_POST['file_name']) ? trim($_POST['file_name']) : (isset($_POST['customer_name']) ? trim($_POST['customer_name']) : '');
    $customer_name = $file_name; // Keep customer_name synced with file_name for now
    $tour_code = isset($_POST['tour_code']) ? trim($_POST['tour_code']) : '';
    $trip_package_id = isset($_POST['trip_package_id']) ? intval($_POST['trip_package_id']) : 0;
    $start_date = isset($_POST['start_date']) ? trim($_POST['start_date']) : '';
    $end_date = isset($_POST['end_date']) ? trim($_POST['end_date']) : '';
    $status = isset($_POST['status']) ? trim($_POST['status']) : 'Pending';

    // Optional fields
    $company = isset($_POST['company']) ? trim($_POST['company']) : null;
    $country = isset($_POST['country']) ? trim($_POST['country']) : null;
    $address = isset($_POST['address']) ? trim($_POST['address']) : null;
    $passport_no = isset($_POST['passport_no']) ? trim($_POST['passport_no']) : null;
    $arrival_date = isset($_POST['arrival_date']) ? trim($_POST['arrival_date']) : null;
    $arrival_time = isset($_POST['arrival_time']) ? trim($_POST['arrival_time']) : null;
    $arrival_flight = isset($_POST['arrival_flight']) ? trim($_POST['arrival_flight']) : null;
    $departure_date = isset($_POST['departure_date']) ? trim($_POST['departure_date']) : null;
    $departure_time = isset($_POST['departure_time']) ? trim($_POST['departure_time']) : null;
    $departure_flight = isset($_POST['departure_flight']) ? trim($_POST['departure_flight']) : null;
    $total_pax = isset($_POST['total_pax']) && $_POST['total_pax'] !== '' ? intval($_POST['total_pax']) : null;
    $couples_count = isset($_POST['couples_count']) && $_POST['couples_count'] !== '' ? intval($_POST['couples_count']) : null;
    $singles_count = isset($_POST['singles_count']) && $_POST['singles_count'] !== '' ? intval($_POST['singles_count']) : null;

    // Derive trip start/end from arrival/departure if not provided
    if (empty($start_date)) { $start_date = $arrival_date; }
    if (empty($end_date)) { $end_date = $departure_date; }

    // Compute total_pax from couples/singles if not provided
    if ($total_pax === null && ($couples_count !== null || $singles_count !== null)) {
        $total_pax = (int)(($couples_count ?? 0) * 2 + ($singles_count ?? 0));
    }

    if ($trip_package_id === 0 || empty($start_date) || empty($end_date)) {
        echo json_encode(['status' => 'error', 'message' => 'Please fill all required fields.']);
        return;
    }

    if (strtotime($end_date) < strtotime($start_date)) {
        echo json_encode(['status' => 'error', 'message' => 'End date cannot be before the start date.']);
        return;
    }

    // Reject duplicate tour_code if provided
    if (!empty($tour_code)){
        $du = $conn->prepare("SELECT COUNT(*) c FROM trips WHERE tour_code = ?"); $du->bind_param('s',$tour_code); $du->execute(); $dr=$du->get_result(); $cnt=intval(($dr->fetch_assoc()['c'])??0); $du->close();
        if ($cnt>0){ echo json_encode(['status'=>'error','message'=>'Tour File No already exists.']); return; }
    }

    // Ensure pax columns exist (couples_count, singles_count)
    if (function_exists('ensureTripGuestsSchema')) { ensureTripGuestsSchema($conn); }

    // --- 1. FETCH package details from trip_packages table ---
    $stmt_package = $conn->prepare("SELECT name, total_price FROM trip_packages WHERE id = ?");
    if (!$stmt_package) {
        echo json_encode(['status' => 'error', 'message' => 'Database prepare failed: ' . $conn->error]);
        return;
    }
    $stmt_package->bind_param("i", $trip_package_id);
    $stmt_package->execute();
    $result_package = $stmt_package->get_result();

    if ($result_package->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid trip package selected.']);
        $stmt_package->close();
        return;
    }

    $package_row = $result_package->fetch_assoc();
    $package_name = $package_row['name'];
    $total_price = $package_row['total_price'] ? floatval($package_row['total_price']) : 0;
    $stmt_package->close();

    $conn->begin_transaction();
    try {
        // Discover available columns in trips table
        $cols = [];
        $resCols = $conn->query("SHOW COLUMNS FROM trips");
        if ($resCols) {
            while ($r = $resCols->fetch_assoc()) { $cols[strtolower($r['Field'])] = true; }
        }

        // Base fields
        $fields = [
            'file_name' => $file_name,
            'customer_name' => $customer_name,
            'tour_code' => $tour_code,
            'trip_package_id' => $trip_package_id,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'status' => $status,
            'total_price' => $total_price
        ];

        // Optional fields if columns exist
        $optionalMap = [
            'company' => $company,
            'country' => $country,
            'address' => $address,
            'passport_no' => $passport_no,
            'arrival_date' => $arrival_date,
            'arrival_time' => $arrival_time,
            'arrival_flight' => $arrival_flight,
            'departure_date' => $departure_date,
            'departure_time' => $departure_time,
            'departure_flight' => $departure_flight,
            'total_pax' => $total_pax,
            'couples_count' => $couples_count,
'singles_count' => $singles_count,
            'guest_details' => isset($_POST['guest_details']) ? trim($_POST['guest_details']) : null,
            'guest_status' => isset($_POST['guest_status']) ? trim($_POST['guest_status']) : null,
            'dob' => isset($_POST['dob']) ? trim($_POST['dob']) : null,
        ];
        foreach ($optionalMap as $col => $val) {
            if (isset($cols[$col])) { $fields[$col] = $val; }
        }

        // Build dynamic insert
        $columns = array_keys($fields);
        $placeholders = [];
        $types = '';
        $values = [];
        foreach ($columns as $col) {
            $placeholders[] = '?';
            if ($col === 'trip_package_id' || $col === 'total_pax') { $types .= 'i'; $values[] = (int)$fields[$col]; }
            elseif ($col === 'total_price') { $types .= 'd'; $values[] = (float)$fields[$col]; }
            else { $types .= 's'; $values[] = $fields[$col]; }
        }

        $sql = "INSERT INTO trips (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $stmt = $conn->prepare($sql);
        if (!$stmt) { throw new Exception('Prepare failed: ' . $conn->error); }
        $stmt->bind_param($types, ...$values);
        if (!$stmt->execute()) { throw new Exception('Execute failed: ' . $stmt->error); }
        $trip_id = $conn->insert_id;
        $stmt->close();

        // Automatically create itinerary days from package requirements
        try {
            // Get package requirements
            $req_stmt = $conn->prepare("SELECT day_number, hotel_id, guide_required, vehicle_required, vehicle_type, day_services, day_notes FROM package_day_requirements WHERE trip_package_id = ? ORDER BY day_number");
            if ($req_stmt) {
                $req_stmt->bind_param("i", $trip_package_id);
                $req_stmt->execute();
                $req_result = $req_stmt->get_result();
                
                $start_date_obj = new DateTime($start_date);
                $day_counter = 1;
                $days_created = 0;
                
                while ($req_row = $req_result->fetch_assoc()) {
                    $offset = max(0, intval($req_row['day_number']) - 1);
                    $day_date = clone $start_date_obj;
                    $day_date->modify('+' . $offset . ' days');
                    $day_date_str = $day_date->format('Y-m-d');
                    
                    // Insert itinerary day
                    $day_stmt = $conn->prepare("INSERT INTO itinerary_days (trip_id, day_date, hotel_id, guide_id, vehicle_id, notes, services_provided, day_type) VALUES (?, ?, ?, NULL, NULL, ?, ?, 'normal')");
                    if ($day_stmt) {
                        $hotel_id = $req_row['hotel_id'] ?: null;
                        $notes = $req_row['day_notes'] ?: '';
                        $services = $req_row['day_services'] ?: '';
                        
                        $day_stmt->bind_param("isiss", $trip_id, $day_date_str, $hotel_id, $notes, $services);
                        $day_stmt->execute();
                        $day_stmt->close();
                        $days_created++;
                    }
                    
                    $day_counter++;
                }
                $req_stmt->close();
                
                // If no days were created from package requirements, create basic days based on trip duration
                if ($days_created === 0) {
                    $start_date_obj = new DateTime($start_date);
                    $end_date_obj = new DateTime($end_date);
                    $trip_duration = $start_date_obj->diff($end_date_obj)->days + 1;
                    
                    for ($i = 0; $i < $trip_duration; $i++) {
                        $day_date = clone $start_date_obj;
                        $day_date->modify('+' . $i . ' days');
                        $day_date_str = $day_date->format('Y-m-d');
                        
                        $day_stmt = $conn->prepare("INSERT INTO itinerary_days (trip_id, day_date, hotel_id, guide_id, vehicle_id, notes, services_provided, day_type) VALUES (?, ?, NULL, NULL, NULL, '', '', 'normal')");
                        if ($day_stmt) {
                            $day_stmt->bind_param("is", $trip_id, $day_date_str);
                            $day_stmt->execute();
                            $day_stmt->close();
                        }
                    }
                }
            }
        } catch (Exception $e) {
            // Log error but don't fail the trip creation
            error_log("Failed to create itinerary days: " . $e->getMessage());
        }

        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => 'Trip created successfully.', 'data' => ['id' => $trip_id]]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Failed to add trip: ' . $e->getMessage()]);
    }
}


function updateTrip($conn) {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $file_name = isset($_POST['file_name']) ? trim($_POST['file_name']) : '';
    $customer_name = $file_name; // Keep synced with file_name
    $tour_code = isset($_POST['tour_code']) ? trim($_POST['tour_code']) : '';
    $trip_package_id = isset($_POST['trip_package_id']) ? intval($_POST['trip_package_id']) : 0;
    $start_date = isset($_POST['start_date']) ? trim($_POST['start_date']) : '';
    $end_date = isset($_POST['end_date']) ? trim($_POST['end_date']) : '';
    $status = isset($_POST['status']) ? trim($_POST['status']) : 'Pending';

    // Optional fields
    $company = isset($_POST['company']) ? trim($_POST['company']) : null;
    $country = isset($_POST['country']) ? trim($_POST['country']) : null;
    $address = isset($_POST['address']) ? trim($_POST['address']) : null;
    $passport_no = isset($_POST['passport_no']) ? trim($_POST['passport_no']) : null;
    $arrival_date = isset($_POST['arrival_date']) ? trim($_POST['arrival_date']) : null;
    $arrival_time = isset($_POST['arrival_time']) ? trim($_POST['arrival_time']) : null;
    $arrival_flight = isset($_POST['arrival_flight']) ? trim($_POST['arrival_flight']) : null;
    $departure_date = isset($_POST['departure_date']) ? trim($_POST['departure_date']) : null;
    $departure_time = isset($_POST['departure_time']) ? trim($_POST['departure_time']) : null;
    $departure_flight = isset($_POST['departure_flight']) ? trim($_POST['departure_flight']) : null;
    $total_pax = isset($_POST['total_pax']) && $_POST['total_pax'] !== '' ? intval($_POST['total_pax']) : null;
    $couples_count = isset($_POST['couples_count']) && $_POST['couples_count'] !== '' ? intval($_POST['couples_count']) : null;
    $singles_count = isset($_POST['singles_count']) && $_POST['singles_count'] !== '' ? intval($_POST['singles_count']) : null;

    // Derive trip start/end from arrival/departure if not provided
    if (empty($start_date)) { $start_date = $arrival_date; }
    if (empty($end_date)) { $end_date = $departure_date; }

    // Compute total_pax from couples/singles if not provided
    if ($total_pax === null && ($couples_count !== null || $singles_count !== null)) {
        $total_pax = (int)(($couples_count ?? 0) * 2 + ($singles_count ?? 0));
    }

    if (empty($id) || $trip_package_id === 0 || empty($start_date) || empty($end_date)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        return;
    }

    // Ensure pax columns exist
    if (function_exists('ensureTripGuestsSchema')) { ensureTripGuestsSchema($conn); }

    // Fetch current trip dates to compute itinerary adjustments
    $cur = $conn->prepare("SELECT start_date, end_date FROM trips WHERE id = ?");
    if (!$cur) { echo json_encode(['status'=>'error','message'=>'Database prepare failed: '.$conn->error]); return; }
    $cur->bind_param('i', $id); $cur->execute(); $res = $cur->get_result();
    if ($res->num_rows===0){ echo json_encode(['status'=>'error','message'=>'Trip not found']); return; }
    $old = $res->fetch_assoc(); $cur->close();
    $old_start = $old['start_date']; $old_end = $old['end_date'];

    // Discover available columns
    $cols = [];
    $resCols = $conn->query("SHOW COLUMNS FROM trips");
    if ($resCols) { while ($r = $resCols->fetch_assoc()) { $cols[strtolower($r['Field'])] = true; } }

    $fields = [
        'file_name' => $file_name,
        'customer_name' => $customer_name,
        'tour_code' => $tour_code,
        'trip_package_id' => $trip_package_id,
        'start_date' => $start_date,
        'end_date' => $end_date,
        'status' => $status
    ];

    $optionalMap = [
        'company' => $company,
        'country' => $country,
        'address' => $address,
        'passport_no' => $passport_no,
        'arrival_date' => $arrival_date,
        'arrival_time' => $arrival_time,
        'arrival_flight' => $arrival_flight,
        'departure_date' => $departure_date,
        'departure_time' => $departure_time,
        'departure_flight' => $departure_flight,
        'total_pax' => $total_pax,
        'couples_count' => $couples_count,
        'singles_count' => $singles_count,
'guest_details' => isset($_POST['guest_details']) ? trim($_POST['guest_details']) : null,
        'guest_status' => isset($_POST['guest_status']) ? trim($_POST['guest_status']) : null,
        'dob' => isset($_POST['dob']) ? trim($_POST['dob']) : null,
    ];
    foreach ($optionalMap as $col => $val) { if (isset($cols[$col])) { $fields[$col] = $val; } }

    // Reject duplicate tour_code if provided
    if (!empty($tour_code)){
        $du = $conn->prepare("SELECT COUNT(*) c FROM trips WHERE tour_code = ? AND id <> ?"); $du->bind_param('si',$tour_code,$id); $du->execute(); $dr=$du->get_result(); $cnt=intval(($dr->fetch_assoc()['c'])??0); $du->close();
        if ($cnt>0){ echo json_encode(['status'=>'error','message'=>'Tour File No already exists.']); return; }
    }

    // Build dynamic update
    $setParts = [];
    $types = '';
    $values = [];
    foreach ($fields as $key => $val) {
        $setParts[] = "$key = ?";
        if ($key === 'trip_package_id' || $key === 'total_pax') { $types .= 'i'; $values[] = (int)$val; }
        else { $types .= 's'; $values[] = $val; }
    }
    $types .= 'i';
    $values[] = $id;

    $conn->begin_transaction();
    try {
        $sql = "UPDATE trips SET " . implode(', ', $setParts) . " WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) { throw new Exception('Database prepare failed: ' . $conn->error); }
        $stmt->bind_param($types, ...$values);
        if (!$stmt->execute()) { throw new Exception('Trip update failed: ' . $stmt->error); }
        $stmt->close();

        // Adjust itinerary days if dates changed
        $oldStart = new DateTime($old_start); $oldEnd = new DateTime($old_end);
        $newStart = new DateTime($start_date); $newEnd = new DateTime($end_date);
        $oldDays = (int)$oldEnd->diff($oldStart)->days + 1;
        $newDays = (int)$newEnd->diff($newStart)->days + 1;

        if ($old_start !== $start_date || $old_end !== $end_date) {
            // Always rebase itinerary: set Day 1 to newStart, keep order, adjust count
            $rowsRes = $conn->query("SELECT id FROM itinerary_days WHERE trip_id = $id ORDER BY day_date ASC");
            $existing = [];
            while ($r = $rowsRes->fetch_assoc()) { $existing[] = (int)$r['id']; }
            $minCount = min(count($existing), $newDays);
            // Update dates for minCount rows
            $d = clone $newStart;
            for ($i=0; $i<$minCount; $i++){
                $dateStr = $d->format('Y-m-d');
                $eid = $existing[$i];
                $u = $conn->prepare("UPDATE itinerary_days SET day_date = ? WHERE id = ?");
                if ($u) { $u->bind_param('si', $dateStr, $eid); $u->execute(); $u->close(); }
                $d->modify('+1 day');
            }
            // If more days needed, insert blanks
            for ($i=$minCount; $i<$newDays; $i++){
                $dateStr = $d->format('Y-m-d');
                $ins = $conn->prepare("INSERT INTO itinerary_days (trip_id, day_date, guide_id, vehicle_id, hotel_id, notes, services_provided, day_type) VALUES (?, ?, NULL, NULL, NULL, '', '', 'normal')");
                if ($ins) { $ins->bind_param('is', $id, $dateStr); $ins->execute(); $ins->close(); }
                $d->modify('+1 day');
            }
            // If too many days, delete extras starting from end
            if (count($existing) > $newDays) {
                $toDelete = array_slice($existing, $newDays);
                if (!empty($toDelete)){
                    $ids = implode(',', array_map('intval', $toDelete));
                    $conn->query("DELETE FROM itinerary_days WHERE id IN ($ids)");
                }
            }
        }

        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => 'Trip updated successfully.']);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

function deleteTrip($conn) {
    // Hard delete (kept for legacy), not used by UI anymore.
    parse_str(file_get_contents("php://input"), $_DELETE);
    $id = isset($_DELETE['id']) ? intval($_DELETE['id']) : 0;
    if (empty($id)) { echo json_encode(['status' => 'error', 'message' => 'Invalid Trip ID.']); return; }
    $stmt = $conn->prepare("DELETE FROM trips WHERE id = ?"); if (!$stmt) { echo json_encode(['status' => 'error', 'message' => 'Database prepare failed: ' . $conn->error]); return; }
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) { echo json_encode(['status' => 'success', 'message' => 'Trip deleted successfully.']); }
    else { echo json_encode(['status' => 'error', 'message' => 'Failed to delete trip: ' . $stmt->error]); }
    $stmt->close();
}

function cancelTrip($conn){
    parse_str(file_get_contents("php://input"), $_DELETE);
    $id = isset($_DELETE['id']) ? intval($_DELETE['id']) : 0;
    if (empty($id)) { echo json_encode(['status'=>'error','message'=>'Invalid Trip ID.']); return; }
    $conn->begin_transaction();
    try{
        // Mark trip as Cancelled
        $u = $conn->prepare("UPDATE trips SET status='Cancelled' WHERE id = ?");
        if (!$u) { throw new Exception('Prepare failed: '.$conn->error); }
        $u->bind_param('i',$id); if(!$u->execute()){ throw new Exception('Update failed: '.$u->error); } $u->close();
        // Remove itinerary and assignments so records/reports exclude it
        // Itinerary days
        $conn->query("DELETE FROM itinerary_days WHERE trip_id = ".intval($id));
        // Arrivals/Departures tables may not exist yet; guard with ensure helpers if available
        if (function_exists('ensureTripArrivalsTable')) { ensureTripArrivalsTable($conn); }
        if (function_exists('ensureTripDeparturesTable')) { ensureTripDeparturesTable($conn); }
        $conn->query("DELETE FROM trip_arrivals WHERE trip_id = ".intval($id));
        $conn->query("DELETE FROM trip_departures WHERE trip_id = ".intval($id));
        // Guests (optional)
        if (function_exists('ensureTripGuestsSchema')) { /* keep guest schema; optionally clear names */ }
        $conn->commit();
        echo json_encode(['status'=>'success','message'=>'Trip cancelled']);
    }catch(Exception $e){
        $conn->rollback();
        echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
    }
}
function getPackageHotels($conn) {
    $trip_package_id = isset($_GET['trip_package_id']) ? intval($_GET['trip_package_id']) : 0;
    
    if ($trip_package_id === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid package ID.']);
        return;
    }

    $sql = "SELECT 
                pha.day_number,
                pha.hotel_id,
                h.id,
                h.name,
                h.room_types,
                h.availability
            FROM package_hotel_assignments pha
            JOIN hotels h ON pha.hotel_id = h.id
            WHERE pha.trip_package_id = ?
            ORDER BY pha.day_number ASC";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Database prepare failed: ' . $conn->error]);
        return;
    }

    $stmt->bind_param("i", $trip_package_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $hotels = [];
    while ($row = $result->fetch_assoc()) {
        $hotels[] = $row;
    }

    $stmt->close();
    echo json_encode(['status' => 'success', 'data' => $hotels]);
}

function getRoomTypes($conn) {
    $result = $conn->query("SELECT id, name, description, capacity FROM room_types ORDER BY name");
    
    if (!$result) {
        echo json_encode(['status' => 'error', 'message' => 'Database query failed: ' . $conn->error]);
        return;
    }

    $roomTypes = [];
    while ($row = $result->fetch_assoc()) {
        $roomTypes[] = $row;
    }
    
    echo json_encode(['status' => 'success', 'data' => $roomTypes]);
}
function getItinerary($conn) {
    $trip_id = isset($_GET['trip_id']) ? intval($_GET['trip_id']) : 0;
    if ($trip_id === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid Trip ID provided.']);
        return;
    }

    $data = [];

    $stmt_trip = $conn->prepare("SELECT t.*, p.name AS package_name FROM trips t JOIN trip_packages p ON t.trip_package_id = p.id WHERE t.id = ?");
    if (!$stmt_trip) {
        echo json_encode(['status' => 'error', 'message' => 'Database prepare failed: ' . $conn->error]);
        return;
    }

    $stmt_trip->bind_param("i", $trip_id);
    $stmt_trip->execute();
    $trip_result = $stmt_trip->get_result();

    if ($trip_result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Trip not found. Trip ID: ' . $trip_id]);
        $stmt_trip->close();
        return;
    }

    $data['trip'] = $trip_result->fetch_assoc();
    $stmt_trip->close();

    $stmt_days = $conn->prepare("SELECT * FROM itinerary_days WHERE trip_id = ? ORDER BY day_date ASC");
    if (!$stmt_days) {
        echo json_encode(['status' => 'error', 'message' => 'Database prepare failed: ' . $conn->error]);
        return;
    }

    $stmt_days->bind_param("i", $trip_id);
    $stmt_days->execute();
    $result_days = $stmt_days->get_result();
    $data['itinerary_days'] = [];
    while($row = $result_days->fetch_assoc()) {
        $data['itinerary_days'][] = $row;
    }
    $stmt_days->close();

    $data['guides'] = $conn->query("SELECT id, name, language FROM guides ORDER BY name")->fetch_all(MYSQLI_ASSOC);
    // Vehicles with optional number_plate
    $vehHasPlate = false;
    $vehColCheck = $conn->query("SHOW COLUMNS FROM vehicles LIKE 'number_plate'");
    if ($vehColCheck && $vehColCheck->num_rows > 0) { $vehHasPlate = true; }
    $vehSelect = "SELECT id, vehicle_name, capacity" . ($vehHasPlate ? ", number_plate" : "") . " FROM vehicles ORDER BY vehicle_name";
    $data['vehicles'] = $conn->query($vehSelect)->fetch_all(MYSQLI_ASSOC);
    $data['hotels'] = $conn->query("SELECT id, name FROM hotels ORDER BY name")->fetch_all(MYSQLI_ASSOC);

    // Include arrivals if table exists
    ensureTripArrivalsTable($conn);
    $arr = $conn->prepare("SELECT id, trip_id, arrival_date, arrival_time, flight_no, pax_count, pickup_location, drop_hotel_id, vehicle_id, guide_id, notes, vehicle_informed, guide_informed FROM trip_arrivals WHERE trip_id = ? ORDER BY arrival_date, arrival_time");
    if ($arr) { $arr->bind_param('i', $trip_id); $arr->execute(); $res = $arr->get_result(); $data['arrivals'] = $res->fetch_all(MYSQLI_ASSOC); $arr->close(); }
    // Include departures
    ensureTripDeparturesTable($conn);
    $dep = $conn->prepare("SELECT id, trip_id, departure_date, departure_time, flight_no, pax_count, pickup_location, pickup_hotel_id, vehicle_id, guide_id, notes, vehicle_informed, guide_informed FROM trip_departures WHERE trip_id = ? ORDER BY departure_date, departure_time");
    if ($dep) { $dep->bind_param('i', $trip_id); $dep->execute(); $dr = $dep->get_result(); $data['departures'] = $dr->fetch_all(MYSQLI_ASSOC); $dep->close(); }

    echo json_encode(['status' => 'success', 'data' => $data]);
}

function updateItinerary($conn) {
    $input = json_decode(file_get_contents('php://input'), true);
    $itinerary_days = isset($input['itinerary_days']) ? $input['itinerary_days'] : [];

    if (empty($itinerary_days) || !is_array($itinerary_days)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid data provided.']);
        return;
    }

    $conn->begin_transaction();
    try {
        // Discover available columns to avoid errors on older schemas
        $cols = [];
        $resCols = $conn->query("SHOW COLUMNS FROM itinerary_days");
        if ($resCols) {
            while ($r = $resCols->fetch_assoc()) { $cols[strtolower($r['Field'])] = true; }
        }

        // Track affected trip IDs to sync PAX after updates
        $affectedTrips = [];

        foreach($itinerary_days as $day) {
            $id = intval($day['id']);

            // Resolve trip_id for this day (so we can recalc PAX later)
            $tidStmt = $conn->prepare("SELECT trip_id FROM itinerary_days WHERE id = ?");
            if ($tidStmt) {
                $tidStmt->bind_param('i', $id);
                $tidStmt->execute();
                $tidRes = $tidStmt->get_result();
                if ($tidRes && $tidRes->num_rows > 0) {
                    $tidRow = $tidRes->fetch_assoc();
                    $affectedTrips[intval($tidRow['trip_id'])] = true;
                }
                $tidStmt->close();
            }

            // Base fields always safe
            $fields = [
                'guide_id' => (isset($day['guide_id']) && $day['guide_id'] !== '') ? intval($day['guide_id']) : null,
                'vehicle_id' => (isset($day['vehicle_id']) && $day['vehicle_id'] !== '') ? intval($day['vehicle_id']) : null,
                'hotel_id' => (isset($day['hotel_id']) && $day['hotel_id'] !== '') ? intval($day['hotel_id']) : null,
                'notes' => isset($day['notes']) ? trim($day['notes']) : '',
                'services_provided' => isset($day['services_provided']) ? trim($day['services_provided']) : ''
            ];

            // Optional fields
            if (isset($cols['room_type_id'])) {
                $fields['room_type_id'] = (isset($day['room_type_id']) && $day['room_type_id'] !== '') ? intval($day['room_type_id']) : null;
            }
            if (isset($cols['room_type_data'])) {
                $fields['room_type_data'] = isset($day['room_type_data']) ? $day['room_type_data'] : null;
            }
            if (isset($cols['day_type'])) {
                $fields['day_type'] = isset($day['day_type']) ? trim($day['day_type']) : 'normal';
            }
            if (isset($cols['guide_informed']) && array_key_exists('guide_informed', $day)) {
                // Read current guide_informed to prevent reverting once set (same as hotel logic)
                $cur = $conn->query("SELECT guide_informed FROM itinerary_days WHERE id = " . $id);
                $curVal = null;
                if ($cur && $cur->num_rows > 0) {
                    $curVal = (int)($cur->fetch_assoc()['guide_informed']);
                }
                $requested = intval($day['guide_informed']) ? 1 : 0;
                // Lock to 1 if already informed (prevents changing back to uninformed)
                $fields['guide_informed'] = ($curVal === 1) ? 1 : $requested;
            }
            if (isset($cols['vehicle_informed']) && array_key_exists('vehicle_informed', $day)) {
                $fields['vehicle_informed'] = intval($day['vehicle_informed']) ? 1 : 0;
            }
            if (isset($cols['hotel_informed'])) {
                // Read current informed to prevent reverting once set
                $cur = $conn->query("SELECT hotel_informed FROM itinerary_days WHERE id = " . $id);
                $curVal = null;
                if ($cur && $cur->num_rows > 0) {
                    $curVal = (int)($cur->fetch_assoc()['hotel_informed']);
                }
                $requested = array_key_exists('hotel_informed', $day) ? (intval($day['hotel_informed']) ? 1 : 0) : $curVal;
                // Lock to 1 if already informed
                $fields['hotel_informed'] = ($curVal === 1) ? 1 : ($requested ?? 0);
            }

            // Build SQL dynamically
            $setParts = [];
            $types = '';
            $values = [];
            foreach ($fields as $key => $val) {
                $setParts[] = "$key = ?";
                $types .= in_array($key, ['notes','services_provided','day_type','room_type_data']) ? 's' : 'i';
                $values[] = $val;
            }
            $types .= 'i';
            $values[] = $id;

            $sql = "UPDATE itinerary_days SET " . implode(', ', $setParts) . " WHERE id = ?";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception('Prepare failed: ' . $conn->error);
            }
            $stmt->bind_param($types, ...$values);
            if (!$stmt->execute()) {
                throw new Exception('Execute failed: ' . $stmt->error);
            }
            $stmt->close();
        }

        // After updating itinerary days, sync pax_details with max room counts from itinerary for affected trips
        if (!empty($affectedTrips)) {
            // Ensure pax tables exist
            if (function_exists('ensurePaxDetailsTable')) { ensurePaxDetailsTable($conn); }
            $userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;
            $userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : null;

            foreach (array_keys($affectedTrips) as $tripId) {
                // Compute max rooms across all days
                $daysStmt = $conn->prepare("SELECT room_type_data FROM itinerary_days WHERE trip_id = ? AND room_type_data IS NOT NULL");
                $roomMax = ['double'=>0,'single'=>0,'triple'=>0,'twin'=>0];
                if ($daysStmt) {
                    $daysStmt->bind_param('i', $tripId);
                    $daysStmt->execute();
                    $daysRes = $daysStmt->get_result();
                    while ($row = $daysRes->fetch_assoc()) {
                        $data = json_decode($row['room_type_data'], true);
                        if (is_array($data)) {
                            foreach (['double','single','triple','twin'] as $k) {
                                $val = intval($data[$k] ?? 0);
                                if ($val > $roomMax[$k]) { $roomMax[$k] = $val; }
                            }
                        }
                    }
                    $daysStmt->close();
                }

                // Fetch existing pax_details
                $exStmt = $conn->prepare("SELECT double_rooms, single_rooms, triple_rooms, twin_rooms FROM pax_details WHERE trip_id = ?");
                $existing = null;
                if ($exStmt) {
                    $exStmt->bind_param('i', $tripId);
                    $exStmt->execute();
                    $exRes = $exStmt->get_result();
                    $existing = $exRes && $exRes->num_rows > 0 ? $exRes->fetch_assoc() : null;
                    $exStmt->close();
                }

                // Upsert pax_details with computed max
                $upSql = "INSERT INTO pax_details (trip_id, double_rooms, single_rooms, triple_rooms, twin_rooms, updated_at)
                          VALUES (?, ?, ?, ?, ?, NOW())
                          ON DUPLICATE KEY UPDATE
                          double_rooms = VALUES(double_rooms),
                          single_rooms = VALUES(single_rooms),
                          triple_rooms = VALUES(triple_rooms),
                          twin_rooms = VALUES(twin_rooms),
                          updated_at = NOW()";
                $up = $conn->prepare($upSql);
                if ($up) {
                    $up->bind_param('iiiii', $tripId, $roomMax['double'], $roomMax['single'], $roomMax['triple'], $roomMax['twin']);
                    $up->execute();
                    $up->close();
                }

                // Log amendments if values changed
                if ($existing) {
                    $map = [
                        ['type'=>'double','field'=>'double_rooms'],
                        ['type'=>'single','field'=>'single_rooms'],
                        ['type'=>'triple','field'=>'triple_rooms'],
                        ['type'=>'twin','field'=>'twin_rooms'],
                    ];
                    foreach ($map as $m) {
                        $old = intval($existing[$m['field']] ?? 0);
                        $new = intval($roomMax[$m['type']] ?? 0);
                        if ($old !== $new) {
                            $insAm = $conn->prepare("INSERT INTO pax_amendments (trip_id, room_type, old_value, new_value, user_id, user_name, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
                            if ($insAm) {
                                $rt = $m['type'];
                                $insAm->bind_param('isiiis', $tripId, $rt, $old, $new, $userId, $userName);
                                $insAm->execute();
                                $insAm->close();
                            }
                        }
                    }
                }
            }
        }

        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => 'Itinerary updated successfully!']);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Failed to update itinerary: ' . $e->getMessage()]);
    }
}

// ============= HOTEL MANAGEMENT =============
function getHotels($conn) {
    $result = $conn->query("SELECT id, name, email, room_types, availability, services_provided FROM hotels ORDER BY name");
    if (!$result) {
        echo json_encode(['status' => 'error', 'message' => 'Database query failed: ' . $conn->error]);
        return;
    }

    $hotels = [];
    while ($row = $result->fetch_assoc()) {
        $hotels[] = $row;
    }
    echo json_encode(['status' => 'success', 'data' => $hotels]);
}

function addHotel($conn) {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : null;
    $room_types = isset($_POST['room_types']) ? trim($_POST['room_types']) : '';
    $availability = isset($_POST['availability']) ? trim($_POST['availability']) : 'Available';
    $services_provided = isset($_POST['services_provided']) ? trim($_POST['services_provided']) : '';

    if (empty($name)) {
        echo json_encode(['status' => 'error', 'message' => 'Hotel name is required.']);
        return;
    }

    $stmt = $conn->prepare("INSERT INTO hotels (name, email, room_types, availability, services_provided) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Database prepare failed: ' . $conn->error]);
        return;
    }

    $stmt->bind_param("sssss", $name, $email, $room_types, $availability, $services_provided);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Hotel added successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add hotel: ' . $stmt->error]);
    }
    $stmt->close();
}

function updateHotel($conn) {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : null;
    $room_types = isset($_POST['room_types']) ? trim($_POST['room_types']) : '';
    $availability = isset($_POST['availability']) ? trim($_POST['availability']) : 'Available';
    $services_provided = isset($_POST['services_provided']) ? trim($_POST['services_provided']) : '';

    if (empty($id) || empty($name)) {
        echo json_encode(['status' => 'error', 'message' => 'Hotel ID and name are required.']);
        return;
    }

    $stmt = $conn->prepare("UPDATE hotels SET name = ?, email = ?, room_types = ?, availability = ?, services_provided = ? WHERE id = ?");
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Database prepare failed: ' . $conn->error]);
        return;
    }

    $stmt->bind_param("sssssi", $name, $email, $room_types, $availability, $services_provided, $id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Hotel updated successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update hotel: ' . $stmt->error]);
    }
    $stmt->close();
}

function deleteHotel($conn) {
    parse_str(file_get_contents("php://input"), $_DELETE);
    $id = isset($_DELETE['id']) ? intval($_DELETE['id']) : 0;

    if (empty($id)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid Hotel ID.']);
        return;
    }

    $stmt = $conn->prepare("DELETE FROM hotels WHERE id = ?");
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Database prepare failed: ' . $conn->error]);
        return;
    }

    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Hotel deleted successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete hotel: ' . $stmt->error]);
    }
    $stmt->close();
}

// ============= VEHICLE MANAGEMENT =============
function getVehicles($conn) {
    // Detect optional columns
    $hasEmail = false; $hasPlate = false;
    $colCheck = $conn->query("SHOW COLUMNS FROM vehicles LIKE 'email'");
    if ($colCheck && $colCheck->num_rows > 0) { $hasEmail = true; }
    $colCheck2 = $conn->query("SHOW COLUMNS FROM vehicles LIKE 'number_plate'");
    if ($colCheck2 && $colCheck2->num_rows > 0) { $hasPlate = true; }

    $select = "SELECT id, vehicle_name, capacity, availability" . ($hasEmail ? ", email" : "") . ($hasPlate ? ", number_plate" : "") . " FROM vehicles ORDER BY vehicle_name";

    $result = $conn->query($select);
    if (!$result) {
        echo json_encode(['status' => 'error', 'message' => 'Database query failed: ' . $conn->error]);
        return;
    }

    $vehicles = [];
    while ($row = $result->fetch_assoc()) {
        $vehicles[] = $row;
    }
    echo json_encode(['status' => 'success', 'data' => $vehicles]);
}

function addVehicle($conn) {
    $vehicle_name = isset($_POST['vehicle_name']) ? trim($_POST['vehicle_name']) : '';
    $capacity = isset($_POST['capacity']) ? intval($_POST['capacity']) : 1;
    $availability = isset($_POST['availability']) ? trim($_POST['availability']) : 'Available';
    $email = isset($_POST['email']) ? trim($_POST['email']) : null;
    $number_plate = isset($_POST['number_plate']) ? trim($_POST['number_plate']) : null;

    if (empty($vehicle_name)) {
        echo json_encode(['status' => 'error', 'message' => 'Vehicle name is required.']);
        return;
    }

    // Detect optional columns
    $cols = [];
    $resCols = $conn->query("SHOW COLUMNS FROM vehicles");
    if ($resCols) { while ($r = $resCols->fetch_assoc()) { $cols[strtolower($r['Field'])] = true; } }

    $fields = [ 'vehicle_name'=>$vehicle_name, 'capacity'=>$capacity, 'availability'=>$availability ];
    if (isset($cols['email'])) $fields['email'] = $email;
    if (isset($cols['number_plate'])) $fields['number_plate'] = $number_plate;

    $columns = array_keys($fields);
    $placeholders = array_fill(0, count($columns), '?');
    $types = ''; $values = [];
    foreach ($columns as $col) {
        if ($col === 'capacity') { $types .= 'i'; $values[] = (int)$fields[$col]; }
        else { $types .= 's'; $values[] = $fields[$col]; }
    }

    $sql = "INSERT INTO vehicles (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
    $stmt = $conn->prepare($sql);
    if (!$stmt) { echo json_encode(['status'=>'error','message'=>'Database prepare failed: '.$conn->error]); return; }
    $stmt->bind_param($types, ...$values);

    if ($stmt->execute()) { echo json_encode(['status'=>'success','message'=>'Vehicle added successfully.']); }
    else { echo json_encode(['status'=>'error','message'=>'Failed to add vehicle: '.$stmt->error]); }
    $stmt->close();
}

function updateVehicle($conn) {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $vehicle_name = isset($_POST['vehicle_name']) ? trim($_POST['vehicle_name']) : '';
    $capacity = isset($_POST['capacity']) ? intval($_POST['capacity']) : 1;
    $availability = isset($_POST['availability']) ? trim($_POST['availability']) : 'Available';
    $email = isset($_POST['email']) ? trim($_POST['email']) : null;
    $number_plate = isset($_POST['number_plate']) ? trim($_POST['number_plate']) : null;

    if (empty($id) || empty($vehicle_name)) {
        echo json_encode(['status' => 'error', 'message' => 'Vehicle ID and name are required.']);
        return;
    }

    // Detect columns
    $cols = [];
    $resCols = $conn->query("SHOW COLUMNS FROM vehicles");
    if ($resCols) { while ($r = $resCols->fetch_assoc()) { $cols[strtolower($r['Field'])] = true; } }

    $fields = [ 'vehicle_name'=>$vehicle_name, 'capacity'=>$capacity, 'availability'=>$availability ];
    if (isset($cols['email'])) $fields['email'] = $email;
    if (isset($cols['number_plate'])) $fields['number_plate'] = $number_plate;

    $setParts = []; $types=''; $values=[];
    foreach ($fields as $k=>$v) {
        $setParts[] = "$k = ?";
        if ($k==='capacity') { $types.='i'; $values[] = (int)$v; }
        else { $types.='s'; $values[] = $v; }
    }
    $types.='i'; $values[] = $id;

    $sql = "UPDATE vehicles SET ".implode(', ',$setParts)." WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) { echo json_encode(['status'=>'error','message'=>'Database prepare failed: '.$conn->error]); return; }
    $stmt->bind_param($types, ...$values);

    if ($stmt->execute()) { echo json_encode(['status'=>'success','message'=>'Vehicle updated successfully.']); }
    else { echo json_encode(['status'=>'error','message'=>'Failed to update vehicle: '.$stmt->error]); }
    $stmt->close();
}

function deleteVehicle($conn) {
    parse_str(file_get_contents("php://input"), $_DELETE);
    $id = isset($_DELETE['id']) ? intval($_DELETE['id']) : 0;

    if (empty($id)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid Vehicle ID.']);
        return;
    }

    $stmt = $conn->prepare("DELETE FROM vehicles WHERE id = ?");
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Database prepare failed: ' . $conn->error]);
        return;
    }

    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Vehicle deleted successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete vehicle: ' . $stmt->error]);
    }
    $stmt->close();
}

// ============= GUIDE MANAGEMENT =============
function getGuides($conn) {
    $result = $conn->query("SELECT id, name, language, email, availability_status FROM guides ORDER BY name");
    if (!$result) {
        echo json_encode(['status' => 'error', 'message' => 'Database query failed: ' . $conn->error]);
        return;
    }

    $guides = [];
    while ($row = $result->fetch_assoc()) {
        $guides[] = $row;
    }
    echo json_encode(['status' => 'success', 'data' => $guides]);
}

function addGuide($conn) {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $language = isset($_POST['language']) ? trim($_POST['language']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $availability_status = isset($_POST['availability_status']) ? trim($_POST['availability_status']) : 'Available';

    if (empty($name)) {
        echo json_encode(['status' => 'error', 'message' => 'Guide name is required.']);
        return;
    }

    // Validate email if provided
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Please enter a valid email address.']);
        return;
    }

    $stmt = $conn->prepare("INSERT INTO guides (name, language, email, availability_status) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Database prepare failed: ' . $conn->error]);
        return;
    }

    $stmt->bind_param("ssss", $name, $language, $email, $availability_status);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Guide added successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add guide: ' . $stmt->error]);
    }
    $stmt->close();
}

function updateGuide($conn) {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $language = isset($_POST['language']) ? trim($_POST['language']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $availability_status = isset($_POST['availability_status']) ? trim($_POST['availability_status']) : 'Available';

    if (empty($id) || empty($name)) {
        echo json_encode(['status' => 'error', 'message' => 'Guide ID and name are required.']);
        return;
    }

    // Validate email if provided
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Please enter a valid email address.']);
        return;
    }

    $stmt = $conn->prepare("UPDATE guides SET name = ?, language = ?, email = ?, availability_status = ? WHERE id = ?");
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Database prepare failed: ' . $conn->error]);
        return;
    }

    $stmt->bind_param("ssssi", $name, $language, $email, $availability_status, $id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Guide updated successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update guide: ' . $stmt->error]);
    }
    $stmt->close();
}

function deleteGuide($conn) {
    parse_str(file_get_contents("php://input"), $_DELETE);
    $id = isset($_DELETE['id']) ? intval($_DELETE['id']) : 0;

    if (empty($id)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid Guide ID.']);
        return;
    }

    $stmt = $conn->prepare("DELETE FROM guides WHERE id = ?");
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Database prepare failed: ' . $conn->error]);
        return;
    }

    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Guide deleted successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete guide: ' . $stmt->error]);
    }
    $stmt->close();
}

// ============= TRIP PACKAGE MANAGEMENT =============
function addTripPackage($conn) {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid JSON data received']);
        return;
    }
    
    $name = trim($data['name'] ?? '');
    $code = strtoupper(trim($data['code'] ?? ''));
    $days = intval($data['No_of_Days'] ?? 0);
    $description = trim($data['description'] ?? '');
    $day_requirements = $data['day_requirements'] ?? [];
    
    if (empty($name) || $days < 1) {
        echo json_encode(['status' => 'error', 'message' => 'Package name and days are required']);
        return;
    }
    if (empty($code)) {
        echo json_encode(['status' => 'error', 'message' => 'Package code is required']);
        return;
    }
    // Prevent duplicate codes
    $du = $conn->prepare("SELECT COUNT(*) c FROM trip_packages WHERE code = ?");
    if ($du) { $du->bind_param('s',$code); $du->execute(); $dr=$du->get_result(); $cnt=intval(($dr->fetch_assoc()['c'])??0); $du->close(); if ($cnt>0){ echo json_encode(['status'=>'error','message'=>'Package code already exists']); return; } }
    
    $conn->begin_transaction();
    try {
        // Insert package (include columns that exist)
        $pkgCols = [];
        $resCols = $conn->query("SHOW COLUMNS FROM trip_packages");
        if ($resCols) { while ($r = $resCols->fetch_assoc()) { $pkgCols[strtolower($r['Field'])] = true; } }
        $hasDesc = isset($pkgCols['description']);
        $hasCode = isset($pkgCols['code']);
        if (!$hasCode) { throw new Exception("trip_packages.code column not found"); }
        if ($hasDesc) {
            $stmt = $conn->prepare("INSERT INTO trip_packages (name, code, No_of_Days, description) VALUES (?, ?, ?, ?)");
            if (!$stmt) { throw new Exception('Database prepare failed: ' . $conn->error); }
            $stmt->bind_param("ssis", $name, $code, $days, $description);
        } else {
            $stmt = $conn->prepare("INSERT INTO trip_packages (name, code, No_of_Days) VALUES (?, ?, ?)");
            if (!$stmt) { throw new Exception('Database prepare failed: ' . $conn->error); }
            $stmt->bind_param("ssi", $name, $code, $days);
        }
        if (!$stmt->execute()) { throw new Exception('Failed to insert package: ' . $stmt->error); }
        $package_id = $conn->insert_id;
        $stmt->close();
        
        // Insert day requirements
        if (!empty($day_requirements)) {
            $hasSvc = false; $hasNotes=false; $c1=$conn->query("SHOW COLUMNS FROM package_day_requirements LIKE 'day_services'"); if($c1 && $c1->num_rows>0)$hasSvc=true; $c2=$conn->query("SHOW COLUMNS FROM package_day_requirements LIKE 'day_notes'"); if($c2 && $c2->num_rows>0)$hasNotes=true;
            if ($hasSvc || $hasNotes){
                $sqlIns = "INSERT INTO package_day_requirements (trip_package_id, day_number, hotel_id, guide_required, vehicle_required, vehicle_type" . ($hasSvc?", day_services":"") . ($hasNotes?", day_notes":"") . ") VALUES (?, ?, NULLIF(?,0), ?, ?, NULLIF(?, '')" . ($hasSvc?", NULLIF(?, '')":"") . ($hasNotes?", NULLIF(?, '')":"") . ")";
                $req_stmt = $conn->prepare($sqlIns);
                if (!$req_stmt) { throw new Exception('Failed to prepare requirements statement: ' . $conn->error); }
                foreach ($day_requirements as $day => $req) {
                    $hotel_id = !empty($req['hotel_id']) ? intval($req['hotel_id']) : 0;
                    $guide_required = $req['guide_required'] ? 1 : 0;
                    $vehicle_required = $req['vehicle_required'] ? 1 : 0;
                    $vehicle_type = !empty($req['vehicle_type']) ? $req['vehicle_type'] : '';
                    $svc = isset($req['day_services']) ? $req['day_services'] : '';
                    $notes = isset($req['day_notes']) ? $req['day_notes'] : '';
                    if ($hasSvc && $hasNotes){ $req_stmt->bind_param("iiiiisss", $package_id, $day, $hotel_id, $guide_required, $vehicle_required, $vehicle_type, $svc, $notes); }
                    elseif ($hasSvc){ $req_stmt->bind_param("iiiiiss", $package_id, $day, $hotel_id, $guide_required, $vehicle_required, $vehicle_type, $svc); }
                    else { $req_stmt->bind_param("iiiiis", $package_id, $day, $hotel_id, $guide_required, $vehicle_required, $vehicle_type); }
                    if (!$req_stmt->execute()) { throw new Exception('Failed to insert day requirement: ' . $req_stmt->error); }
                }
                $req_stmt->close();
            } else {
                $req_stmt = $conn->prepare("INSERT INTO package_day_requirements (trip_package_id, day_number, hotel_id, guide_required, vehicle_required, vehicle_type) VALUES (?, ?, NULLIF(?,0), ?, ?, NULLIF(?, ''))");
                if (!$req_stmt) { throw new Exception('Failed to prepare requirements statement: ' . $conn->error); }
                foreach ($day_requirements as $day => $req) {
                    $hotel_id = !empty($req['hotel_id']) ? intval($req['hotel_id']) : 0;
                    $guide_required = $req['guide_required'] ? 1 : 0;
                    $vehicle_required = $req['vehicle_required'] ? 1 : 0;
                    $vehicle_type = !empty($req['vehicle_type']) ? $req['vehicle_type'] : '';
                    $req_stmt->bind_param("iiiiis", $package_id, $day, $hotel_id, $guide_required, $vehicle_required, $vehicle_type);
                    if (!$req_stmt->execute()) { throw new Exception('Failed to insert day requirement: ' . $req_stmt->error); }
                }
                $req_stmt->close();
            }
        }
        
        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => 'Package created successfully', 'data' => ['id' => $package_id]]);
        
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

function updateTripPackage($conn) {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid JSON data received']);
        return;
    }
    
    $id = intval($data['id'] ?? 0);
    $name = trim($data['name'] ?? '');
    $code = strtoupper(trim($data['code'] ?? ''));
    $days = intval($data['No_of_Days'] ?? 0);
    $description = trim($data['description'] ?? '');
    $day_requirements = $data['day_requirements'] ?? [];
    
    if (!$id || empty($name) || $days < 1) {
        echo json_encode(['status' => 'error', 'message' => 'Package ID, name and days are required']);
        return;
    }
    if (empty($code)) { echo json_encode(['status'=>'error','message'=>'Package code is required']); return; }
    // Prevent duplicate codes on update
    $du = $conn->prepare("SELECT COUNT(*) c FROM trip_packages WHERE code = ? AND id <> ?");
    if ($du) { $du->bind_param('si',$code,$id); $du->execute(); $dr=$du->get_result(); $cnt=intval(($dr->fetch_assoc()['c'])??0); $du->close(); if ($cnt>0){ echo json_encode(['status'=>'error','message'=>'Package code already exists']); return; } }
    
    $conn->begin_transaction();
    try {
        // Update package (respect existing columns)
        $pkgCols = [];
        $resCols = $conn->query("SHOW COLUMNS FROM trip_packages");
        if ($resCols) { while ($r = $resCols->fetch_assoc()) { $pkgCols[strtolower($r['Field'])] = true; } }
        $hasDesc = isset($pkgCols['description']);
        $hasCode = isset($pkgCols['code']);
        if ($hasCode && $hasDesc) {
            $stmt = $conn->prepare("UPDATE trip_packages SET name = ?, code = ?, No_of_Days = ?, description = ? WHERE id = ?");
            if (!$stmt) { throw new Exception('Database prepare failed: ' . $conn->error); }
            $stmt->bind_param("ssisi", $name, $code, $days, $description, $id);
        } elseif ($hasCode && !$hasDesc) {
            $stmt = $conn->prepare("UPDATE trip_packages SET name = ?, code = ?, No_of_Days = ? WHERE id = ?");
            if (!$stmt) { throw new Exception('Database prepare failed: ' . $conn->error); }
            $stmt->bind_param("ssii", $name, $code, $days, $id);
        } else {
            // No 'code' column – cannot save code value
            throw new Exception("trip_packages.code column not found");
        }
        if (!$stmt->execute()) { throw new Exception('Failed to update package: ' . $stmt->error); }
        $stmt->close();
        
        // Delete existing requirements
        $del_stmt = $conn->prepare("DELETE FROM package_day_requirements WHERE trip_package_id = ?");
        if ($del_stmt) {
            $del_stmt->bind_param("i", $id);
            $del_stmt->execute();
            $del_stmt->close();
        }
        
        // Insert new day requirements
        if (!empty($day_requirements)) {
            $hasSvc = false; $hasNotes=false; $c1=$conn->query("SHOW COLUMNS FROM package_day_requirements LIKE 'day_services'"); if($c1 && $c1->num_rows>0)$hasSvc=true; $c2=$conn->query("SHOW COLUMNS FROM package_day_requirements LIKE 'day_notes'"); if($c2 && $c2->num_rows>0)$hasNotes=true;
            if ($hasSvc || $hasNotes){
                $sqlIns = "INSERT INTO package_day_requirements (trip_package_id, day_number, hotel_id, guide_required, vehicle_required, vehicle_type" . ($hasSvc?", day_services":"") . ($hasNotes?", day_notes":"") . ") VALUES (?, ?, NULLIF(?,0), ?, ?, NULLIF(?, '')" . ($hasSvc?", NULLIF(?, '')":"") . ($hasNotes?", NULLIF(?, '')":"") . ")";
                $req_stmt = $conn->prepare($sqlIns);
                if (!$req_stmt) { throw new Exception('Failed to prepare requirements statement: ' . $conn->error); }
                foreach ($day_requirements as $day => $req) {
                    $hotel_id = !empty($req['hotel_id']) ? intval($req['hotel_id']) : 0;
                    $guide_required = $req['guide_required'] ? 1 : 0;
                    $vehicle_required = $req['vehicle_required'] ? 1 : 0;
                    $vehicle_type = !empty($req['vehicle_type']) ? $req['vehicle_type'] : '';
                    $svc = isset($req['day_services']) ? $req['day_services'] : '';
                    $notes = isset($req['day_notes']) ? $req['day_notes'] : '';
                    if ($hasSvc && $hasNotes){ $req_stmt->bind_param("iiiiisss", $id, $day, $hotel_id, $guide_required, $vehicle_required, $vehicle_type, $svc, $notes); }
                    elseif ($hasSvc){ $req_stmt->bind_param("iiiiiss", $id, $day, $hotel_id, $guide_required, $vehicle_required, $vehicle_type, $svc); }
                    else { $req_stmt->bind_param("iiiiis", $id, $day, $hotel_id, $guide_required, $vehicle_required, $vehicle_type); }
                    if (!$req_stmt->execute()) { throw new Exception('Failed to insert day requirement: ' . $req_stmt->error); }
                }
                $req_stmt->close();
            } else {
                $req_stmt = $conn->prepare("INSERT INTO package_day_requirements (trip_package_id, day_number, hotel_id, guide_required, vehicle_required, vehicle_type) VALUES (?, ?, NULLIF(?,0), ?, ?, NULLIF(?, ''))");
                if (!$req_stmt) { throw new Exception('Failed to prepare requirements statement: ' . $conn->error); }
                foreach ($day_requirements as $day => $req) {
                    $hotel_id = !empty($req['hotel_id']) ? intval($req['hotel_id']) : 0;
                    $guide_required = $req['guide_required'] ? 1 : 0;
                    $vehicle_required = $req['vehicle_required'] ? 1 : 0;
                    $vehicle_type = !empty($req['vehicle_type']) ? $req['vehicle_type'] : '';
                    $req_stmt->bind_param("iiiiis", $id, $day, $hotel_id, $guide_required, $vehicle_required, $vehicle_type);
                    if (!$req_stmt->execute()) { throw new Exception('Failed to insert day requirement: ' . $req_stmt->error); }
                }
                $req_stmt->close();
            }
        }
        
        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => 'Package updated successfully']);
        
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

function deleteTripPackage($conn) {
    parse_str(file_get_contents("php://input"), $_DELETE);
    $id = isset($_DELETE['id']) ? intval($_DELETE['id']) : 0;
    
    if (!$id) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid Package ID']);
        return;
    }
    
    // Check if package is being used by any trips
    $check_stmt = $conn->prepare("SELECT COUNT(*) as count FROM trips WHERE trip_package_id = ?");
    if ($check_stmt) {
        $check_stmt->bind_param("i", $id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row['count'] > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Cannot delete package. It is being used by ' . $row['count'] . ' trip(s).']);
            $check_stmt->close();
            return;
        }
        $check_stmt->close();
    }
    
    // Delete package (requirements will be deleted by CASCADE)
    $stmt = $conn->prepare("DELETE FROM trip_packages WHERE id = ?");
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Database prepare failed: ' . $conn->error]);
        return;
    }
    
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Package deleted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete package: ' . $stmt->error]);
    }
    $stmt->close();
}

// ============= PACKAGE REQUIREMENTS =============
function getPackageRequirements($conn) {
    $trip_package_id = isset($_GET['trip_package_id']) ? intval($_GET['trip_package_id']) : 0;
    
    if (!$trip_package_id) {
        echo json_encode(['status' => 'error', 'message' => 'Package ID is required']);
        return;
    }
    
    // First check if we have new format data in package_day_requirements
    // add services/notes if columns exist
    $hasSvc=false; $hasNotes=false; $c1=$conn->query("SHOW COLUMNS FROM package_day_requirements LIKE 'day_services'"); if($c1 && $c1->num_rows>0)$hasSvc=true; $c2=$conn->query("SHOW COLUMNS FROM package_day_requirements LIKE 'day_notes'"); if($c2 && $c2->num_rows>0)$hasNotes=true;
    $select = "SELECT day_number, hotel_id, guide_required, vehicle_required, vehicle_type" . ($hasSvc? ", day_services":"") . ($hasNotes? ", day_notes":"") . " FROM package_day_requirements WHERE trip_package_id = ? ORDER BY day_number";
    
    $stmt = $conn->prepare($select);
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Database prepare failed: ' . $conn->error]);
        return;
    }
    
    $stmt->bind_param("i", $trip_package_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $requirements = [];
    while ($row = $result->fetch_assoc()) {
        $requirements[] = $row;
    }
    
    $stmt->close();
    
    // If no new format data, try to get legacy hotel assignments
    if (empty($requirements)) {
        $legacy_sql = "SELECT day_number, hotel_id 
                      FROM trip_package_hotels 
                      WHERE trip_package_id = ? 
                      ORDER BY day_number";
        
        $legacy_stmt = $conn->prepare($legacy_sql);
        if ($legacy_stmt) {
            $legacy_stmt->bind_param("i", $trip_package_id);
            $legacy_stmt->execute();
            $legacy_result = $legacy_stmt->get_result();
            
            while ($row = $legacy_result->fetch_assoc()) {
                $requirements[] = [
                    'day_number' => $row['day_number'],
                    'hotel_id' => $row['hotel_id'],
                    'guide_required' => 0,
                    'vehicle_required' => 0,
                    'vehicle_type' => null
                ];
            }
            $legacy_stmt->close();
        }
    }
    
    echo json_encode(['status' => 'success', 'data' => $requirements]);
}

function checkGuideConflict($conn) {
    $payload = file_get_contents('php://input');
    $data = json_decode($payload, true);
    $guide_id = isset($data['guide_id']) ? intval($data['guide_id']) : 0;
    $date = isset($data['date']) ? trim($data['date']) : '';
    $exclude_trip_id = isset($data['exclude_trip_id']) ? intval($data['exclude_trip_id']) : 0;

    if ($guide_id<=0 || $date==='') { echo json_encode(['status'=>'error','message'=>'guide_id and date are required']); return; }

    $sql = "SELECT t.id AS trip_id, t.customer_name, t.tour_code, id.day_date
            FROM itinerary_days id
            JOIN trips t ON t.id = id.trip_id
            WHERE id.guide_id = ? AND id.day_date = ?" . ($exclude_trip_id>0 ? " AND id.trip_id <> ?" : "");
    $stmt = $conn->prepare($sql);
    if (!$stmt) { echo json_encode(['status'=>'error','message'=>'DB prepare failed: '.$conn->error]); return; }
    if ($exclude_trip_id>0) { $stmt->bind_param('isi', $guide_id, $date, $exclude_trip_id); }
    else { $stmt->bind_param('is', $guide_id, $date); }
    $stmt->execute(); $res = $stmt->get_result();
    $conflicts = $res->fetch_all(MYSQLI_ASSOC); $stmt->close();
    echo json_encode(['status'=>'success','data'=>$conflicts]);
}

// ============= HOTEL RECORDS =============

function updateTripPax($conn) {
    $input = json_decode(file_get_contents('php://input'), true);
    $trip_id = isset($input['trip_id']) ? intval($input['trip_id']) : 0;
    $total_pax = isset($input['total_pax']) ? intval($input['total_pax']) : null;
    if ($trip_id<=0 || $total_pax===null || $total_pax<0){ echo json_encode(['status'=>'error','message'=>'trip_id and valid total_pax required']); return; }
    // Check column exists
    $colCheck = $conn->query("SHOW COLUMNS FROM trips LIKE 'total_pax'");
    if (!$colCheck || $colCheck->num_rows===0){ echo json_encode(['status'=>'error','message'=>'total_pax column not found in trips table']); return; }
    $stmt = $conn->prepare("UPDATE trips SET total_pax = ? WHERE id = ?");
    if (!$stmt){ echo json_encode(['status'=>'error','message'=>'DB prepare failed: '.$conn->error]); return; }
    $stmt->bind_param('ii', $total_pax, $trip_id);
    if ($stmt->execute()) echo json_encode(['status'=>'success','message'=>'Total pax updated']);
    else echo json_encode(['status'=>'error','message'=>'Update failed: '.$stmt->error]);
    $stmt->close();
}

function getHotelRecords($conn) {
    // Detect hotel_informed column
    $hasHotelInformed = false;
    $colCheck = $conn->query("SHOW COLUMNS FROM itinerary_days LIKE 'hotel_informed'");
    if ($colCheck && $colCheck->num_rows > 0) { $hasHotelInformed = true; }

    $selectInformed = $hasHotelInformed ? ', id.hotel_informed as hotel_informed' : ", 0 as hotel_informed";

    $sql = "
        SELECT 
            t.id as trip_id,
            t.customer_name as guest_name,
            t.tour_code,
            t.status,
            id.day_date as check_in_date,
            id.day_date as check_out_date,
            h.name as hotel_name,
            id.room_type_data as room_details" . $selectInformed . "
        FROM trips t
        JOIN itinerary_days id ON t.id = id.trip_id
        JOIN hotels h ON id.hotel_id = h.id
        WHERE id.hotel_id IS NOT NULL AND id.hotel_id != '' AND id.hotel_id != '0' AND t.status <> 'Cancelled' AND t.status IS NOT NULL AND t.status <> ''
        ORDER BY id.day_date DESC, t.id
    ";
    
    $result = $conn->query($sql);
    if (!$result) {
        echo json_encode(['status' => 'error', 'message' => 'Database query failed: ' . $conn->error]);
        return;
    }

    $records = [];
    $grouped_bookings = [];
    
    // Group consecutive days at the same hotel for the same trip
    while ($row = $result->fetch_assoc()) {
        $key = $row['trip_id'] . '_' . $row['hotel_name'];
        
        if (!isset($grouped_bookings[$key])) {
            $grouped_bookings[$key] = [
                'trip_id' => $row['trip_id'],
                'guest_name' => $row['guest_name'],
                'tour_code' => $row['tour_code'],
                'hotel_name' => $row['hotel_name'],
                'status' => $row['status'],
                'dates' => [],
                'date_informed' => [],
                'room_details' => $row['room_details']
            ];
        }
        
        $grouped_bookings[$key]['dates'][] = $row['check_in_date'];
        $grouped_bookings[$key]['date_informed'][$row['check_in_date']] = intval($row['hotel_informed']);
    }
    
    // Convert grouped bookings to individual records with check-in and check-out dates
    foreach ($grouped_bookings as $booking) {
        sort($booking['dates']);
        $consecutive_groups = [];
        $current_group = [$booking['dates'][0]];
        
        for ($i = 1; $i < count($booking['dates']); $i++) {
            $current_date = new DateTime($booking['dates'][$i]);
            $prev_date = new DateTime($booking['dates'][$i-1]);
            $diff = $current_date->diff($prev_date)->days;
            
            if ($diff == 1) {
                $current_group[] = $booking['dates'][$i];
            } else {
                $consecutive_groups[] = $current_group;
                $current_group = [$booking['dates'][$i]];
            }
        }
        $consecutive_groups[] = $current_group;
        
        // Create records for each consecutive group
        foreach ($consecutive_groups as $group) {
            // Compute informed = all days in the block are informed
            $allInformed = true;
            foreach ($group as $d) {
                $val = isset($booking['date_informed'][$d]) ? intval($booking['date_informed'][$d]) : 0;
                if ($val !== 1) { $allInformed = false; break; }
            }

            $records[] = [
                'trip_id' => $booking['trip_id'],
                'guest_name' => $booking['guest_name'],
                'tour_code' => $booking['tour_code'],
                'hotel_name' => $booking['hotel_name'],
                'check_in_date' => $group[0],
                'check_out_date' => end($group),
                'status' => $booking['status'],
                'room_details' => $booking['room_details'],
                'hotel_informed' => $allInformed ? 1 : 0
            ];
        }
    }
    
    // Sort by check-in date descending
    usort($records, function($a, $b) {
        return strtotime($b['check_in_date']) - strtotime($a['check_in_date']);
    });
    
    echo json_encode(['status' => 'success', 'data' => $records]);
}

// ============= GUIDE RECORDS =============
function getGuideRecords($conn) {
    $statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
    $monthFilter = isset($_GET['month']) ? $_GET['month'] : '';
    $year = isset($_GET['year']) && $_GET['year'] ? $_GET['year'] : date('Y');
    try {
        // Optional informed flag
        $hasGuideInf = false; $cg = $conn->query("SHOW COLUMNS FROM itinerary_days LIKE 'guide_informed'"); if ($cg && $cg->num_rows>0) $hasGuideInf = true;
        $idGuideInf = $hasGuideInf ? 'id.guide_informed' : '0';
        // Base from itinerary_days
        $q1 = "SELECT 
                   g.id AS guide_id,
                   g.name AS guide_name,
                   g.email AS guide_email,
                   g.language AS guide_language,
                   g.availability_status AS guide_status,
                   id.day_date AS assignment_date,
                   $idGuideInf AS guide_informed,
                   t.id AS trip_id,
                   t.customer_name AS guest_name,
                   t.tour_code,
                   t.status
                FROM itinerary_days id
                JOIN trips t ON t.id = id.trip_id
                JOIN guides g ON g.id = id.guide_id
                WHERE id.guide_id IS NOT NULL AND id.guide_id <> '' AND id.guide_id <> '0' AND t.status <> 'Cancelled' AND t.status IS NOT NULL AND t.status <> ''";
        // From arrivals
        $q2 = "SELECT 
                   g.id AS guide_id,
                   g.name AS guide_name,
                   g.email AS guide_email,
                   g.language AS guide_language,
                   g.availability_status AS guide_status,
                   ta.arrival_date AS assignment_date,
                   ta.guide_informed AS guide_informed,
                   t.id AS trip_id,
                   t.customer_name AS guest_name,
                   t.tour_code,
                   t.status
                FROM trip_arrivals ta
                JOIN trips t ON t.id = ta.trip_id
                JOIN guides g ON g.id = ta.guide_id
                WHERE ta.guide_id IS NOT NULL AND ta.guide_id <> '' AND ta.guide_id <> '0' AND t.status <> 'Cancelled' AND t.status IS NOT NULL AND t.status <> ''";
        // From departures
        $q3 = "SELECT 
                   g.id AS guide_id,
                   g.name AS guide_name,
                   g.email AS guide_email,
                   g.language AS guide_language,
                   g.availability_status AS guide_status,
                   td.departure_date AS assignment_date,
                   td.guide_informed AS guide_informed,
                   t.id AS trip_id,
                   t.customer_name AS guest_name,
                   t.tour_code,
                   t.status
                FROM trip_departures td
                JOIN trips t ON t.id = td.trip_id
                JOIN guides g ON g.id = td.guide_id
                WHERE td.guide_id IS NOT NULL AND td.guide_id <> '' AND td.guide_id <> '0' AND t.status <> 'Cancelled' AND t.status IS NOT NULL AND t.status <> ''";
        // Combine
        $sql = "$q1 UNION ALL $q2 UNION ALL $q3";
        // Filters
        $whereTail = [];
        $params = [];
        $types = '';
        if ($statusFilter && $statusFilter !== 'all') { $whereTail[] = "guide_status = ?"; $params[] = $statusFilter; $types .= 's'; }
        if ($monthFilter && $monthFilter !== 'all') { $whereTail[] = "MONTH(assignment_date) = ? AND YEAR(assignment_date) = ?"; $params[] = $monthFilter; $params[] = $year; $types .= 'ii'; }
        $sql = "SELECT * FROM (".$sql.") x" . (count($whereTail)? (" WHERE ".implode(' AND ',$whereTail)) : '') . " ORDER BY guide_name, assignment_date";
        if (!empty($params)) { $stmt = $conn->prepare($sql); $stmt->bind_param($types, ...$params); $stmt->execute(); $result = $stmt->get_result(); }
        else { $result = $conn->query($sql); }
        if (!$result) throw new Exception('Database query failed: '.$conn->error);
        $records = []; while($row=$result->fetch_assoc()){ $records[]=$row; }
        if (isset($stmt)) $stmt->close();
        echo json_encode(['status'=>'success','data'=>$records]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
    }
}

function checkGuideAvailability($conn) {
    $guideId = $_POST['guide_id'] ?? $_GET['guide_id'] ?? null;
    $dayDate = $_POST['day_date'] ?? $_GET['day_date'] ?? null;
    $currentTripId = $_POST['trip_id'] ?? $_GET['trip_id'] ?? null;
    
    if (!$guideId || !$dayDate) {
        http_response_code(400);
        echo json_encode(['error' => 'Guide ID and day date are required']);
        return;
    }
    
    try {
        $sql = "SELECT 
                    id.trip_id,
                    i.itinerary_name,
                    g.name as guide_name,
                    t.customer_name,
                    t.tour_code
                FROM itinerary_days id
                INNER JOIN trips t ON id.trip_id = t.id
                INNER JOIN guides g ON id.guide_id = g.id
                WHERE id.guide_id = ? AND id.day_date = ?";
        
        $params = [$guideId, $dayDate];
        $types = "is";
        
        // Exclude current trip if provided (for editing existing trips)
        if ($currentTripId) {
            $sql .= " AND id.trip_id != ?";
            $params[] = $currentTripId;
            $types .= "i";
        }
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $conflicts = [];
        while ($row = $result->fetch_assoc()) {
            $conflicts[] = $row;
        }
        
        $response = [
            'available' => empty($conflicts),
            'conflicts' => $conflicts
        ];
        
        header('Content-Type: application/json');
        echo json_encode($response);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

function getVehicleRecords($conn) {
    try {
        // Detect optional columns
        $hasVehInf = false; $cv = $conn->query("SHOW COLUMNS FROM itinerary_days LIKE 'vehicle_informed'"); if ($cv && $cv->num_rows>0) $hasVehInf = true;
        $hasPlate = false; $pc = $conn->query("SHOW COLUMNS FROM vehicles LIKE 'number_plate'"); if ($pc && $pc->num_rows > 0) { $hasPlate = true; }
        $plateField = $hasPlate ? 'v.number_plate' : "''";
        $idVehInf = $hasVehInf ? 'id.vehicle_informed' : '0';

        // Base from itinerary days
        $q1 = "SELECT 
                   v.id AS vehicle_id,
                   v.vehicle_name,
                   $plateField AS number_plate,
                   v.email AS vehicle_email,
                   id.day_date AS assignment_date,
                   $idVehInf AS vehicle_informed,
                   t.id AS trip_id,
                   t.customer_name AS guest_name,
                   t.tour_code,
                   t.status
                FROM itinerary_days id
                JOIN trips t ON t.id = id.trip_id
                JOIN vehicles v ON v.id = id.vehicle_id
                WHERE id.vehicle_id IS NOT NULL AND id.vehicle_id <> '' AND id.vehicle_id <> '0' AND t.status <> 'Cancelled' AND t.status IS NOT NULL AND t.status <> ''";
        // From arrivals
        $q2 = "SELECT 
                   v.id AS vehicle_id,
                   v.vehicle_name,
                   $plateField AS number_plate,
                   v.email AS vehicle_email,
                   ta.arrival_date AS assignment_date,
                   ta.vehicle_informed AS vehicle_informed,
                   t.id AS trip_id,
                   t.customer_name AS guest_name,
                   t.tour_code,
                   t.status
                FROM trip_arrivals ta
                JOIN trips t ON t.id = ta.trip_id
                JOIN vehicles v ON v.id = ta.vehicle_id
                WHERE ta.vehicle_id IS NOT NULL AND ta.vehicle_id <> '' AND ta.vehicle_id <> '0' AND t.status <> 'Cancelled' AND t.status IS NOT NULL AND t.status <> ''";
        // From departures
        $q3 = "SELECT 
                   v.id AS vehicle_id,
                   v.vehicle_name,
                   $plateField AS number_plate,
                   v.email AS vehicle_email,
                   td.departure_date AS assignment_date,
                   td.vehicle_informed AS vehicle_informed,
                   t.id AS trip_id,
                   t.customer_name AS guest_name,
                   t.tour_code,
                   t.status
                FROM trip_departures td
                JOIN trips t ON t.id = td.trip_id
                JOIN vehicles v ON v.id = td.vehicle_id
                WHERE td.vehicle_id IS NOT NULL AND td.vehicle_id <> '' AND td.vehicle_id <> '0' AND t.status <> 'Cancelled' AND t.status IS NOT NULL AND t.status <> ''";
        $sql = "$q1 UNION ALL $q2 UNION ALL $q3 ORDER BY vehicle_name, assignment_date";
        $result = $conn->query($sql);
        if (!$result) { throw new Exception('Database query failed: ' . $conn->error); }
        $records = [];
        while ($row = $result->fetch_assoc()) { $records[] = $row; }
        echo json_encode(['status' => 'success', 'data' => $records]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

function getDayRoster($conn){
    try {
        // Detect informed flags and vehicle plate
        $hasHotelInf = false; $cH = $conn->query("SHOW COLUMNS FROM itinerary_days LIKE 'hotel_informed'"); if ($cH && $cH->num_rows>0) $hasHotelInf = true;
        $hasGuideInf = false; $cG = $conn->query("SHOW COLUMNS FROM itinerary_days LIKE 'guide_informed'"); if ($cG && $cG->num_rows>0) $hasGuideInf = true;
        $hasVehInf = false; $cV = $conn->query("SHOW COLUMNS FROM itinerary_days LIKE 'vehicle_informed'"); if ($cV && $cV->num_rows>0) $hasVehInf = true;
        $hasPlate = false; $pc = $conn->query("SHOW COLUMNS FROM vehicles LIKE 'number_plate'"); if ($pc && $pc->num_rows>0) $hasPlate = true;

        $month = isset($_GET['month']) ? trim($_GET['month']) : '';
        $date = isset($_GET['date']) ? trim($_GET['date']) : '';

        // Determine period start/end
        if ($date !== '') {
            $periodStart = new DateTime($date);
            $periodEnd = new DateTime($date);
        } else {
            if ($month === '' || !preg_match('/^\d{4}-\d{2}$/', $month)) {
                $month = date('Y-m');
            }
            $periodStart = new DateTime($month . '-01');
            $periodEnd = clone $periodStart; $periodEnd->modify('last day of this month');
        }
        $startStr = $periodStart->format('Y-m-d');
        $endStr = $periodEnd->format('Y-m-d');

        // Fetch trips overlapping this period
        $sqlTrips = "SELECT id, customer_name, tour_code, status, start_date, end_date FROM trips WHERE start_date <= ? AND end_date >= ? AND status <> 'Cancelled' AND status IS NOT NULL AND status <> '' ORDER BY id";
        $stTrips = $conn->prepare($sqlTrips); if (!$stTrips) throw new Exception('DB prepare failed: '.$conn->error);
        $stTrips->bind_param('ss', $endStr, $startStr); $stTrips->execute(); $rsTrips = $stTrips->get_result();
        $trips = [];
        while ($t = $rsTrips->fetch_assoc()) { $trips[] = $t; }
        $stTrips->close();

        if (empty($trips)) { echo json_encode(['status'=>'success','data'=>[]]); return; }

        // Fetch itinerary assignments within period for all trips
        $cols = [
            'id.trip_id',
            'id.day_date',
            'id.notes',
            'id.services_provided',
            'h.name as hotel_name',
            'g.name as guide_name',
            'g.language as guide_language',
            'v.vehicle_name'
        ];
        $cols[] = $hasPlate ? 'v.number_plate as number_plate' : "'' as number_plate";
        $cols[] = $hasHotelInf ? 'id.hotel_informed as hotel_informed' : '0 as hotel_informed';
        $cols[] = $hasGuideInf ? 'id.guide_informed as guide_informed' : '0 as guide_informed';
        $cols[] = $hasVehInf ? 'id.vehicle_informed as vehicle_informed' : '0 as vehicle_informed';

        $sqlId = "SELECT ".implode(', ', $cols)." 
                  FROM itinerary_days id
                  LEFT JOIN hotels h ON h.id = id.hotel_id
                  LEFT JOIN guides g ON g.id = id.guide_id
                  LEFT JOIN vehicles v ON v.id = id.vehicle_id
                  WHERE id.day_date BETWEEN ? AND ?";
        $stId = $conn->prepare($sqlId); if (!$stId) throw new Exception('DB prepare failed: '.$conn->error);
        $stId->bind_param('ss', $startStr, $endStr); $stId->execute(); $rsId = $stId->get_result();
        $ass = [];
        while ($r = $rsId->fetch_assoc()) {
            $tid = (int)$r['trip_id']; $dd = $r['day_date'];
            if (!isset($ass[$tid])) $ass[$tid] = [];
            $ass[$tid][$dd] = $r;
        }
        $stId->close();

        // Preload arrival vehicles within the period
        $arrivalsMap = [];
        $qA = $conn->prepare("SELECT ta.trip_id, ta.arrival_date, ta.arrival_time, v.vehicle_name, v.number_plate FROM trip_arrivals ta LEFT JOIN vehicles v ON v.id = ta.vehicle_id WHERE ta.arrival_date BETWEEN ? AND ?");
        if ($qA) { $qA->bind_param('ss', $startStr, $endStr); $qA->execute(); $rA=$qA->get_result(); while($row=$rA->fetch_assoc()){ $tid=(int)$row['trip_id']; $d=$row['arrival_date']; if(!isset($arrivalsMap[$tid]))$arrivalsMap[$tid]=[]; if(!isset($arrivalsMap[$tid][$d]))$arrivalsMap[$tid][$d]=[]; $label=$row['vehicle_name']; if ($hasPlate && !empty($row['number_plate'])) $label .= ' ('.$row['number_plate'].')'; if (!empty($row['arrival_time'])) $label .= ' @ '.$row['arrival_time']; $arrivalsMap[$tid][$d][]=$label; } $qA->close(); }
        // Preload departure vehicles within the period
        ensureTripDeparturesTable($conn);
        $departuresMap = [];
        $qD = $conn->prepare("SELECT td.trip_id, td.departure_date, td.departure_time, v.vehicle_name, v.number_plate FROM trip_departures td LEFT JOIN vehicles v ON v.id = td.vehicle_id WHERE td.departure_date BETWEEN ? AND ?");
        if ($qD) { $qD->bind_param('ss',$startStr,$endStr); $qD->execute(); $rD=$qD->get_result(); while($row=$rD->fetch_assoc()){ $tid=(int)$row['trip_id']; $d=$row['departure_date']; if(!isset($departuresMap[$tid]))$departuresMap[$tid]=[]; if(!isset($departuresMap[$tid][$d]))$departuresMap[$tid][$d]=[]; $label=$row['vehicle_name']; if ($hasPlate && !empty($row['number_plate'])) $label.=' ('.$row['number_plate'].')'; if (!empty($row['departure_time'])) $label.=' @ '.$row['departure_time']; $departuresMap[$tid][$d][]=$label; } $qD->close(); }

        // Build diary-style rows for each day per trip in overlap
        $rows = [];
        foreach ($trips as $t) {
            $tid = (int)$t['id'];
            $tStart = new DateTime($t['start_date']);
            $tEnd = new DateTime($t['end_date']);
            // clamp to period
            $d = $tStart > $periodStart ? clone $tStart : clone $periodStart;
            $endD = $tEnd < $periodEnd ? clone $tEnd : clone $periodEnd;
            while ($d <= $endD) {
                $ds = $d->format('Y-m-d');
                $row = [
                    'day_date' => $ds,
                    'trip_id' => $tid,
                    'guest_name' => $t['customer_name'],
                    'tour_code' => $t['tour_code'],
                    'status' => $t['status'],
                    'notes' => '',
                    'services_provided' => '',
                    'hotel_name' => null,
                    'guide_name' => null,
                    'guide_language' => null,
                    'vehicle_name' => null,
                    'number_plate' => '' ,
                    'hotel_informed' => 0,
                    'guide_informed' => 0,
                    'vehicle_informed' => 0,
                ];
                if (isset($ass[$tid]) && isset($ass[$tid][$ds])) {
                    $a = $ass[$tid][$ds];
                    $row['notes'] = $a['notes'] ?? '';
                    $row['services_provided'] = $a['services_provided'] ?? '';
                    $row['hotel_name'] = $a['hotel_name'] ?? null;
                    $row['guide_name'] = $a['guide_name'] ?? null;
                    $row['guide_language'] = $a['guide_language'] ?? null;
                    $row['vehicle_name'] = $a['vehicle_name'] ?? null;
                    $row['number_plate'] = $a['number_plate'] ?? '';
                    $row['hotel_informed'] = intval($a['hotel_informed'] ?? 0);
                    $row['guide_informed'] = intval($a['guide_informed'] ?? 0);
                    $row['vehicle_informed'] = intval($a['vehicle_informed'] ?? 0);
                }
                // Add arrival vehicle summary if any
                if (isset($arrivalsMap[$tid]) && isset($arrivalsMap[$tid][$ds]) && count($arrivalsMap[$tid][$ds])>0){
                    $row['arrival_vehicle_summary'] = implode(', ', $arrivalsMap[$tid][$ds]);
                } else {
                    $row['arrival_vehicle_summary'] = '';
                }
                // Add departure vehicle summary if any
                if (isset($departuresMap[$tid]) && isset($departuresMap[$tid][$ds]) && count($departuresMap[$tid][$ds])>0){
                    $row['departure_vehicle_summary'] = implode(', ', $departuresMap[$tid][$ds]);
                } else {
                    $row['departure_vehicle_summary'] = '';
                }
                $rows[] = $row;
                $d->modify('+1 day');
            }
        }
        // Sort by date, then tour_code
        usort($rows, function($a,$b){ $c = strcmp($a['day_date'],$b['day_date']); if ($c!==0) return $c; return strcmp((string)$a['tour_code'], (string)$b['tour_code']); });
        echo json_encode(['status'=>'success','data'=>$rows]);
    } catch (Exception $e){
        http_response_code(500);
        echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
    }
}

// --- Utility: check if table exists
function tableExists($conn, $table){
    $table = $conn->real_escape_string($table);
    $res = $conn->query("SHOW TABLES LIKE '$table'");
    return ($res && $res->num_rows > 0);
}

// --- Trip Guests schema helper
function ensureTripGuestsSchema($conn){
    // Preferred new table: guests
    $conn->query("CREATE TABLE IF NOT EXISTS guests (
        id INT AUTO_INCREMENT PRIMARY KEY,
        trip_id INT NOT NULL,
        type VARCHAR(10) NOT NULL,
        name1 VARCHAR(255) NOT NULL,
        name2 VARCHAR(255) NULL,
        passport1 VARCHAR(100) NULL,
        passport2 VARCHAR(100) NULL,
        dob1 DATE NULL,
        dob2 DATE NULL,
        country1 VARCHAR(100) NULL,
        country2 VARCHAR(100) NULL,
        remark1 VARCHAR(255) NULL,
        remark2 VARCHAR(255) NULL,
        display_order INT NOT NULL DEFAULT 0,
        INDEX(trip_id), INDEX(type)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Backward-compatible table: trip_guests
    $conn->query("CREATE TABLE IF NOT EXISTS trip_guests (
        id INT AUTO_INCREMENT PRIMARY KEY,
        trip_id INT NOT NULL,
        type VARCHAR(10) NOT NULL,
        name1 VARCHAR(255) NOT NULL,
        name2 VARCHAR(255) NULL,
        passport1 VARCHAR(100) NULL,
        passport2 VARCHAR(100) NULL,
        dob1 DATE NULL,
        dob2 DATE NULL,
        country1 VARCHAR(100) NULL,
        country2 VARCHAR(100) NULL,
        remark1 VARCHAR(255) NULL,
        remark2 VARCHAR(255) NULL,
        display_order INT NOT NULL DEFAULT 0,
        INDEX(trip_id), INDEX(type)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Ensure optional columns exist on older schemas for both tables
    foreach (['guests','trip_guests'] as $tbl){
        $cols = [];
        $resCols = $conn->query("SHOW COLUMNS FROM $tbl");
        if ($resCols) { while ($r = $resCols->fetch_assoc()) { $cols[strtolower($r['Field'])] = true; } }
        if (!isset($cols['passport1'])) { $conn->query("ALTER TABLE $tbl ADD COLUMN passport1 VARCHAR(100) NULL AFTER name2"); }
        if (!isset($cols['passport2'])) { $conn->query("ALTER TABLE $tbl ADD COLUMN passport2 VARCHAR(100) NULL AFTER passport1"); }
        if (!isset($cols['dob1'])) { $conn->query("ALTER TABLE $tbl ADD COLUMN dob1 DATE NULL AFTER passport2"); }
        if (!isset($cols['dob2'])) { $conn->query("ALTER TABLE $tbl ADD COLUMN dob2 DATE NULL AFTER dob1"); }
        if (!isset($cols['country1'])) { $conn->query("ALTER TABLE $tbl ADD COLUMN country1 VARCHAR(100) NULL AFTER dob2"); }
        if (!isset($cols['country2'])) { $conn->query("ALTER TABLE $tbl ADD COLUMN country2 VARCHAR(100) NULL AFTER country1"); }
        if (!isset($cols['remark1'])) { $conn->query("ALTER TABLE $tbl ADD COLUMN remark1 VARCHAR(255) NULL AFTER country2"); }
        if (!isset($cols['remark2'])) { $conn->query("ALTER TABLE $tbl ADD COLUMN remark2 VARCHAR(255) NULL AFTER remark1"); }
    }

    // Ensure trips table has file_name column (NEW - replacing customer_name)
    $res = $conn->query("SHOW COLUMNS FROM trips LIKE 'file_name'");
    if (!$res || $res->num_rows===0){ 
        $conn->query("ALTER TABLE trips ADD COLUMN file_name VARCHAR(255) NULL AFTER id"); 
        // Copy existing customer_name to file_name for migration
        $conn->query("UPDATE trips SET file_name = customer_name WHERE file_name IS NULL");
    }
    
    // Ensure trips table has pax-related columns (will be removed later)
    $res = $conn->query("SHOW COLUMNS FROM trips LIKE 'total_pax'");
    if (!$res || $res->num_rows===0){ $conn->query("ALTER TABLE trips ADD COLUMN total_pax INT NULL"); }
    $res = $conn->query("SHOW COLUMNS FROM trips LIKE 'couples_count'");
    if (!$res || $res->num_rows===0){ $conn->query("ALTER TABLE trips ADD COLUMN couples_count INT NULL"); }
    $res = $conn->query("SHOW COLUMNS FROM trips LIKE 'singles_count'");
    if (!$res || $res->num_rows===0){ $conn->query("ALTER TABLE trips ADD COLUMN singles_count INT NULL"); }
    $res = $conn->query("SHOW COLUMNS FROM trips LIKE 'guest_status'");
    if (!$res || $res->num_rows===0){ $conn->query("ALTER TABLE trips ADD COLUMN guest_status VARCHAR(50) NULL"); }
}

// --- Trip arrivals schema helper
function ensureTripArrivalsTable($conn){
    $conn->query("CREATE TABLE IF NOT EXISTS trip_arrivals (
        id INT AUTO_INCREMENT PRIMARY KEY,
        trip_id INT NOT NULL,
        arrival_date DATE NOT NULL,
        arrival_time TIME NULL,
        flight_no VARCHAR(100) NULL,
        pax_count INT DEFAULT 0,
        pickup_location VARCHAR(255) NULL,
        drop_hotel_id INT NULL,
        vehicle_id INT NULL,
        guide_id INT NULL,
        notes TEXT NULL,
        vehicle_informed TINYINT(1) DEFAULT 0,
        guide_informed TINYINT(1) DEFAULT 0,
        INDEX(trip_id), INDEX(arrival_date), INDEX(vehicle_id), INDEX(guide_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

function getTripArrivals($conn){
    $trip_id = isset($_GET['trip_id']) ? intval($_GET['trip_id']) : 0;
    if ($trip_id<=0){ echo json_encode(['status'=>'error','message'=>'trip_id required']); return; }
    ensureTripArrivalsTable($conn);
    $stmt = $conn->prepare("SELECT id, trip_id, arrival_date, arrival_time, flight_no, pax_count, pickup_location, drop_hotel_id, vehicle_id, guide_id, notes, vehicle_informed, guide_informed FROM trip_arrivals WHERE trip_id = ? ORDER BY arrival_date, arrival_time");
    if ($stmt){ $stmt->bind_param('i', $trip_id); $stmt->execute(); $res = $stmt->get_result(); $rows = $res->fetch_all(MYSQLI_ASSOC); $stmt->close(); echo json_encode(['status'=>'success','data'=>$rows]); return; }
    echo json_encode(['status'=>'error','message'=>'DB prepare failed: '.$conn->error]);
}

function saveTripArrivals($conn){
    $payload = file_get_contents('php://input');
    $data = json_decode($payload, true);
    $trip_id = isset($data['trip_id']) ? intval($data['trip_id']) : 0;
    $arrivals = isset($data['arrivals']) && is_array($data['arrivals']) ? $data['arrivals'] : [];
    if ($trip_id<=0){ echo json_encode(['status'=>'error','message'=>'trip_id required']); return; }
    ensureTripArrivalsTable($conn);
    // Delete existing
    $del = $conn->prepare("DELETE FROM trip_arrivals WHERE trip_id = ?");
    if ($del){ $del->bind_param('i', $trip_id); $del->execute(); $del->close(); }
    // Insert new
    $ins = $conn->prepare("INSERT INTO trip_arrivals (trip_id, arrival_date, arrival_time, flight_no, pax_count, pickup_location, drop_hotel_id, vehicle_id, guide_id, notes, vehicle_informed, guide_informed) VALUES (?, ?, NULLIF(?,''), NULLIF(?,''), ?, NULLIF(?,''), NULLIF(?,0), NULLIF(?,0), NULLIF(?,0), NULLIF(?,''), ?, ?)");
    if (!$ins){ echo json_encode(['status'=>'error','message'=>'DB prepare failed: '.$conn->error]); return; }
    foreach ($arrivals as $a){
        $arrival_date = trim($a['arrival_date'] ?? '');
        if ($arrival_date==='') { continue; }
        $arrival_time = trim($a['arrival_time'] ?? '');
        $flight_no = trim($a['flight_no'] ?? '');
        $pax_count = intval($a['pax_count'] ?? 0);
        $pickup_location = trim($a['pickup_location'] ?? '');
        $drop_hotel_id = intval($a['drop_hotel_id'] ?? 0);
        $vehicle_id = intval($a['vehicle_id'] ?? 0);
        $guide_id = intval($a['guide_id'] ?? 0);
        $notes = trim($a['notes'] ?? '');
        $vehicle_informed = intval($a['vehicle_informed'] ?? 0) ? 1 : 0;
        $guide_informed = intval($a['guide_informed'] ?? 0) ? 1 : 0;
        $ins->bind_param('isssississii', $trip_id, $arrival_date, $arrival_time, $flight_no, $pax_count, $pickup_location, $drop_hotel_id, $vehicle_id, $guide_id, $notes, $vehicle_informed, $guide_informed);
        $ins->execute();
    }
    $ins->close();
    echo json_encode(['status'=>'success','message'=>'Arrivals saved']);
}

// --- Trip departures schema helper
function ensureTripDeparturesTable($conn){
    // Base table (includes pickup_location to mirror arrivals schema)
    $conn->query("CREATE TABLE IF NOT EXISTS trip_departures (
        id INT AUTO_INCREMENT PRIMARY KEY,
        trip_id INT NOT NULL,
        departure_date DATE NOT NULL,
        departure_time TIME NULL,
        flight_no VARCHAR(100) NULL,
        pax_count INT DEFAULT 0,
        pickup_location VARCHAR(255) NULL,
        pickup_hotel_id INT NULL,
        vehicle_id INT NULL,
        guide_id INT NULL,
        notes TEXT NULL,
        vehicle_informed TINYINT(1) DEFAULT 0,
        guide_informed TINYINT(1) DEFAULT 0,
        INDEX(trip_id), INDEX(departure_date), INDEX(vehicle_id), INDEX(guide_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    // Ensure pickup_location column exists for older schemas
    $col = $conn->query("SHOW COLUMNS FROM trip_departures LIKE 'pickup_location'");
    if (!$col || $col->num_rows===0){
        $conn->query("ALTER TABLE trip_departures ADD COLUMN pickup_location VARCHAR(255) NULL AFTER pax_count");
    }
}

function getTripDepartures($conn){
    $trip_id = isset($_GET['trip_id']) ? intval($_GET['trip_id']) : 0;
    if ($trip_id<=0){ echo json_encode(['status'=>'error','message'=>'trip_id required']); return; }
    ensureTripDeparturesTable($conn);
$stmt = $conn->prepare("SELECT id, trip_id, departure_date, departure_time, flight_no, pax_count, pickup_location, pickup_hotel_id, vehicle_id, guide_id, notes, vehicle_informed, guide_informed FROM trip_departures WHERE trip_id = ? ORDER BY departure_date, departure_time");
    if ($stmt){ $stmt->bind_param('i', $trip_id); $stmt->execute(); $res = $stmt->get_result(); $rows = $res->fetch_all(MYSQLI_ASSOC); $stmt->close(); echo json_encode(['status'=>'success','data'=>$rows]); return; }
    echo json_encode(['status'=>'error','message'=>'DB prepare failed: '.$conn->error]);
}

function saveTripDepartures($conn){
    $payload = file_get_contents('php://input');
    $data = json_decode($payload, true);
    $trip_id = isset($data['trip_id']) ? intval($data['trip_id']) : 0;
    $departures = isset($data['departures']) && is_array($data['departures']) ? $data['departures'] : [];
    if ($trip_id<=0){ echo json_encode(['status'=>'error','message'=>'trip_id required']); return; }
    ensureTripDeparturesTable($conn);
    // Delete existing
    $del = $conn->prepare("DELETE FROM trip_departures WHERE trip_id = ?");
    if ($del){ $del->bind_param('i', $trip_id); $del->execute(); $del->close(); }
    // Insert new
$ins = $conn->prepare("INSERT INTO trip_departures (trip_id, departure_date, departure_time, flight_no, pax_count, pickup_location, pickup_hotel_id, vehicle_id, guide_id, notes, vehicle_informed, guide_informed) VALUES (?, ?, NULLIF(?,''), NULLIF(?,''), ?, NULLIF(?,''), NULLIF(?,0), NULLIF(?,0), NULLIF(?,0), NULLIF(?,''), ?, ?)");
    if (!$ins){ echo json_encode(['status'=>'error','message'=>'DB prepare failed: '.$conn->error]); return; }
    foreach ($departures as $d){
        $departure_date = trim($d['departure_date'] ?? '');
        if ($departure_date==='') { continue; }
        $departure_time = trim($d['departure_time'] ?? '');
        $flight_no = trim($d['flight_no'] ?? '');
        $pax_count = intval($d['pax_count'] ?? 0);
        $pickup_location = trim($d['pickup_location'] ?? '');
        $pickup_hotel_id = intval($d['pickup_hotel_id'] ?? 0);
        $vehicle_id = intval($d['vehicle_id'] ?? 0);
        $guide_id = intval($d['guide_id'] ?? 0);
        $notes = trim($d['notes'] ?? '');
        $vehicle_informed = intval($d['vehicle_informed'] ?? 0) ? 1 : 0;
        $guide_informed = intval($d['guide_informed'] ?? 0) ? 1 : 0;
        $ins->bind_param('isssissisiii', $trip_id, $departure_date, $departure_time, $flight_no, $pax_count, $pickup_location, $pickup_hotel_id, $vehicle_id, $guide_id, $notes, $vehicle_informed, $guide_informed);
        $ins->execute();
    }
    $ins->close();
    echo json_encode(['status'=>'success','message'=>'Departures saved']);
}

function getArrivalInsights($conn){
    ensureTripArrivalsTable($conn);
    $month = isset($_GET['month']) ? trim($_GET['month']) : date('Y-m'); // YYYY-MM
    if (!preg_match('/^\d{4}-\d{2}$/',$month)) { $month = date('Y-m'); }
    $start = $month.'-01';
    $end = date('Y-m-t', strtotime($start));
    // Optional vehicle number_plate column
    $hasPlate = false; $pc = $conn->query("SHOW COLUMNS FROM vehicles LIKE 'number_plate'"); if ($pc && $pc->num_rows>0) $hasPlate = true;
    $selectPlate = $hasPlate ? ', v.number_plate' : '';
    $sql = "SELECT ta.id, ta.trip_id, ta.arrival_date, ta.arrival_time, ta.flight_no, ta.pax_count, ta.pickup_location, ta.drop_hotel_id,
                   ta.vehicle_id, ta.guide_id, ta.notes, t.customer_name, t.tour_code,
                   v.vehicle_name".$selectPlate.", g.name AS guide_name, h.name AS drop_hotel_name
            FROM trip_arrivals ta
            JOIN trips t ON t.id = ta.trip_id
            LEFT JOIN vehicles v ON v.id = ta.vehicle_id
            LEFT JOIN guides g ON g.id = ta.guide_id
            LEFT JOIN hotels h ON h.id = ta.drop_hotel_id
            WHERE ta.arrival_date BETWEEN ? AND ? AND t.status <> 'Cancelled' AND t.status IS NOT NULL AND t.status <> ''
            ORDER BY ta.arrival_date, ta.arrival_time, t.tour_code";
    $stmt = $conn->prepare($sql);
    if (!$stmt){ echo json_encode(['status'=>'error','message'=>'DB prepare failed: '.$conn->error]); return; }
    $stmt->bind_param('ss', $start, $end); $stmt->execute(); $res = $stmt->get_result();
    $rows = [];
    while ($r = $res->fetch_assoc()) { $rows[] = $r; }
    $stmt->close();
    echo json_encode(['status'=>'success','data'=>$rows]);
}

function getDepartureInsights($conn){
    ensureTripDeparturesTable($conn);
    $month = isset($_GET['month']) ? trim($_GET['month']) : date('Y-m');
    if (!preg_match('/^\d{4}-\d{2}$/',$month)) { $month = date('Y-m'); }
    $start = $month.'-01';
    $end = date('Y-m-t', strtotime($start));
    // Optional vehicle number_plate column
    $hasPlate = false; $pc = $conn->query("SHOW COLUMNS FROM vehicles LIKE 'number_plate'"); if ($pc && $pc->num_rows>0) $hasPlate = true;
    $selectPlate = $hasPlate ? ', v.number_plate' : '';
    $sql = "SELECT td.id, td.trip_id, td.departure_date, td.departure_time, td.flight_no, td.pax_count,
                   td.pickup_location, td.pickup_hotel_id, td.vehicle_id, td.guide_id, td.notes,
                   t.customer_name, t.tour_code,
                   v.vehicle_name".$selectPlate.", g.name AS guide_name, h.name AS pickup_hotel_name
            FROM trip_departures td
            JOIN trips t ON t.id = td.trip_id
            LEFT JOIN vehicles v ON v.id = td.vehicle_id
            LEFT JOIN guides g ON g.id = td.guide_id
            LEFT JOIN hotels h ON h.id = td.pickup_hotel_id
            WHERE td.departure_date BETWEEN ? AND ? AND t.status <> 'Cancelled' AND t.status IS NOT NULL AND t.status <> ''
            ORDER BY td.departure_date, td.departure_time, t.tour_code";
    $stmt = $conn->prepare($sql);
    if (!$stmt){ echo json_encode(['status'=>'error','message'=>'DB prepare failed: '.$conn->error]); return; }
    $stmt->bind_param('ss', $start, $end); $stmt->execute(); $res = $stmt->get_result();
    $rows = [];
    while($r = $res->fetch_assoc()) { $rows[] = $r; }
    $stmt->close();
    echo json_encode(['status'=>'success','data'=>$rows]);
}
function deleteTripArrival($conn){
    parse_str(file_get_contents("php://input"), $_DELETE);
    $id = isset($_DELETE['id']) ? intval($_DELETE['id']) : 0;
    if ($id<=0){ echo json_encode(['status'=>'error','message'=>'Invalid id']); return; }
    ensureTripArrivalsTable($conn);
    $stmt = $conn->prepare("DELETE FROM trip_arrivals WHERE id = ?");
    if (!$stmt){ echo json_encode(['status'=>'error','message'=>'DB prepare failed: '.$conn->error]); return; }
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) echo json_encode(['status'=>'success','message'=>'Arrival deleted']);
    else echo json_encode(['status'=>'error','message'=>'Delete failed: '.$stmt->error]);
    $stmt->close();
}

function getTripGuests($conn){
    $trip_id = isset($_GET['trip_id']) ? intval($_GET['trip_id']) : 0;
    if ($trip_id<=0){ echo json_encode(['status'=>'error','message'=>'trip_id required']); return; }
    ensureTripGuestsSchema($conn);
    $tbl = tableExists($conn, 'guests') ? 'guests' : 'trip_guests';
    $stmt = $conn->prepare("SELECT type, name1, name2, passport1, passport2, dob1, dob2, country1, country2, remark1, remark2 FROM $tbl WHERE trip_id = ? ORDER BY display_order, id");
    if (!$stmt){ echo json_encode(['status'=>'error','message'=>'DB prepare failed: '.$conn->error]); return; }
    $stmt->bind_param('i', $trip_id); $stmt->execute(); $res = $stmt->get_result();
    $couples = []; $singles = [];
    $couples_details = []; $singles_details = [];
    while ($row = $res->fetch_assoc()){
        if ($row['type'] === 'couple') {
            $couples[] = [ $row['name1'], $row['name2'] ];
            $couples_details[] = [
                'name1'=>$row['name1'], 'name2'=>$row['name2'],
                'passport1'=>$row['passport1'], 'passport2'=>$row['passport2'],
                'dob1'=>$row['dob1'], 'dob2'=>$row['dob2'],
                'country'=>$row['country1'],
                'remark1'=>$row['remark1'], 'remark2'=>$row['remark2']
            ];
        } else {
            $singles[] = $row['name1'];
            $singles_details[] = [
                'name'=>$row['name1'],
                'passport'=>$row['passport1'],
                'dob'=>$row['dob1'],
                'country'=>$row['country1'],
                'remark'=>$row['remark1']
            ];
        }
    }
    $stmt->close();
    echo json_encode(['status'=>'success','data'=>['couples'=>$couples,'singles'=>$singles,'details'=>['couples'=>$couples_details,'singles'=>$singles_details]]]);
}

function saveTripGuests($conn){
    $payload = file_get_contents('php://input');
    $data = json_decode($payload, true);
    $trip_id = isset($data['trip_id']) ? intval($data['trip_id']) : 0;
    $couples = isset($data['couples']) && is_array($data['couples']) ? $data['couples'] : [];
    $singles = isset($data['singles']) && is_array($data['singles']) ? $data['singles'] : [];
    $couples_details = isset($data['couples_details']) && is_array($data['couples_details']) ? $data['couples_details'] : [];
    $singles_details = isset($data['singles_details']) && is_array($data['singles_details']) ? $data['singles_details'] : [];
    if ($trip_id<=0){ echo json_encode(['status'=>'error','message'=>'trip_id required']); return; }
    
    try {
        ensureTripGuestsSchema($conn);
        $tbl = tableExists($conn, 'guests') ? 'guests' : 'trip_guests';
        // Replace existing
        $del = $conn->prepare("DELETE FROM $tbl WHERE trip_id = ?");
        if ($del){ $del->bind_param('i', $trip_id); $del->execute(); $del->close(); }
        // Insert with extended columns
        $ins = $conn->prepare("INSERT INTO $tbl (trip_id, type, name1, name2, passport1, passport2, dob1, dob2, country1, country2, remark1, remark2, display_order) VALUES (?, ?, ?, NULLIF(?, ''), NULLIF(?, ''), NULLIF(?, ''), NULLIF(?, ''), NULLIF(?, ''), NULLIF(?, ''), NULLIF(?, ''), NULLIF(?, ''), NULLIF(?, ''), ?)");
        if (!$ins){ throw new Exception('DB prepare failed: '.$conn->error); }
    $order = 1;
    // Couples: prefer details if provided
    if (!empty($couples_details)){
        foreach ($couples_details as $cd){
            $n1 = trim($cd['name1'] ?? ''); if ($n1==='') continue;
            $n2 = trim($cd['name2'] ?? '');
            $p1 = trim($cd['passport1'] ?? ''); $p2 = trim($cd['passport2'] ?? '');
            $d1 = trim($cd['dob1'] ?? ''); $d2 = trim($cd['dob2'] ?? '');
            $country = trim($cd['country'] ?? '');
            $r1 = trim($cd['remark1'] ?? ''); $r2 = trim($cd['remark2'] ?? '');
            $type='couple';
            $ins->bind_param('isssssssssssi', $trip_id, $type, $n1, $n2, $p1, $p2, $d1, $d2, $country, $country, $r1, $r2, $order);
            $ins->execute();
            $order++;
        }
    } else {
        foreach ($couples as $c){
            $n1 = trim($c[0] ?? ''); $n2 = trim($c[1] ?? '');
            if ($n1==='') continue; $type='couple';
            $empty='';
            $ins->bind_param('isssssssssssi', $trip_id, $type, $n1, $n2, $empty, $empty, $empty, $empty, $empty, $empty, $empty, $empty, $order);
            $ins->execute();
            $order++;
        }
    }
    // Singles: prefer details if provided
    if (!empty($singles_details)){
        foreach ($singles_details as $sd){
            $n1 = trim($sd['name'] ?? ''); if ($n1==='') continue;
            $p1 = trim($sd['passport'] ?? '');
            $d1 = trim($sd['dob'] ?? '');
            $country = trim($sd['country'] ?? '');
            $r1 = trim($sd['remark'] ?? '');
            $type='single'; $empty='';
            $ins->bind_param('isssssssssssi', $trip_id, $type, $n1, $empty, $p1, $empty, $d1, $empty, $country, $empty, $r1, $empty, $order);
            $ins->execute();
            $order++;
        }
    } else {
        foreach ($singles as $s){
            $n1 = trim($s ?? ''); if ($n1==='') continue; $type='single'; $empty='';
            $ins->bind_param('isssssssssssi', $trip_id, $type, $n1, $empty, $empty, $empty, $empty, $empty, $empty, $empty, $empty, $empty, $order);
            $ins->execute();
            $order++;
        }
    }
        $ins->close();
        // Update trips counters
        $cou = !empty($couples_details) ? count(array_filter($couples_details, function($cd){ return trim($cd['name1'] ?? '') !== ''; })) : count(array_filter($couples, function($c){ return trim($c[0] ?? '') !== ''; }));
        $sin = !empty($singles_details) ? count(array_filter($singles_details, function($sd){ return trim($sd['name'] ?? '') !== ''; })) : count(array_filter($singles, function($s){ return trim($s ?? '') !== ''; }));
        $pax = (int)($cou * 2 + $sin);
        $upd = $conn->prepare("UPDATE trips SET couples_count = ?, singles_count = ?, total_pax = ? WHERE id = ?");
        if ($upd){ $upd->bind_param('iiii', $cou, $sin, $pax, $trip_id); $upd->execute(); $upd->close(); }
        echo json_encode(['status'=>'success','message'=>'Guests saved','data'=>['couples_count'=>$cou,'singles_count'=>$sin,'total_pax'=>$pax]]);
    } catch (Exception $e) {
        echo json_encode(['status'=>'error','message'=>'Save guests error: '.$e->getMessage().' at line '.$e->getLine()]);
    }
}

// ============= PACKAGE IMPORT FUNCTIONS =============

function analyzeImportFile($conn) {
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['status' => 'error', 'message' => 'No file uploaded or upload error']);
        return;
    }
    
    $file = $_FILES['file'];
    $fileName = $file['name'];
    $filePath = $file['tmp_name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    try {
        if ($fileExtension === 'csv') {
            // Handle CSV files
            $data = analyzeCsvFile($filePath);
        } else if (in_array($fileExtension, ['xlsx', 'xls'])) {
            // Handle Excel files with PhpSpreadsheet
            $data = analyzeExcelFile($filePath);
        } else {
            throw new Exception('Unsupported file format. Please upload CSV or Excel files.');
        }
        
        echo json_encode([
            'status' => 'success', 
            'data' => $data,
            'message' => 'File analyzed successfully'
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Error analyzing file: ' . $e->getMessage()
        ]);
    }
}

function analyzeCsvFile($filePath) {
    $headers = [];
    $sampleData = [];
    
    if (($handle = fopen($filePath, 'r')) !== FALSE) {
        // Read headers from first row
        $headers = fgetcsv($handle);
        
        // Read up to 5 sample rows
        $rowCount = 0;
        while (($row = fgetcsv($handle)) !== FALSE && $rowCount < 5) {
            $sampleData[] = $row;
            $rowCount++;
        }
        fclose($handle);
    }
    
    return [
        'headers' => $headers,
        'sample_data' => $sampleData,
        'total_rows' => $rowCount
    ];
}

function analyzeExcelFile($filePath) {
    // Check if PhpSpreadsheet is available, otherwise provide fallback
    if (!class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
        // Fallback: suggest CSV conversion
        throw new Exception('Excel support requires PhpSpreadsheet library. Please either:\n1. Install PhpSpreadsheet: composer require phpoffice/phpspreadsheet\n2. Or convert your Excel file to CSV format and try again');
    }
    
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
    $worksheet = $spreadsheet->getActiveSheet();
    
    // Get headers from first row
    $headers = [];
    $highestColumn = $worksheet->getHighestColumn();
    $columnRange = range('A', $highestColumn);
    
    foreach ($columnRange as $column) {
        $headers[] = $worksheet->getCell($column . '1')->getCalculatedValue();
    }
    
    // Get sample data (up to 5 rows)
    $sampleData = [];
    $highestRow = min(6, $worksheet->getHighestRow()); // Max 5 sample rows + header
    
    for ($row = 2; $row <= $highestRow; $row++) {
        $rowData = [];
        foreach ($columnRange as $column) {
            $rowData[] = $worksheet->getCell($column . $row)->getCalculatedValue();
        }
        $sampleData[] = $rowData;
    }
    
    return [
        'headers' => $headers,
        'sample_data' => $sampleData,
        'total_rows' => $worksheet->getHighestRow() - 1 // Exclude header row
    ];
}

function importPackages($conn) {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!isset($data['mappings']) || !isset($data['data'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid import data']);
        return;
    }
    
    $mappings = $data['mappings'];
    $importData = $data['data'];
    
    try {
        $conn->begin_transaction();
        
        $packagesCreated = 0;
        $daysImported = 0;
        $processedPackages = [];
        $startTime = microtime(true);
        
        foreach ($importData as $rowIndex => $row) {
            $packageData = [];
            $requirementsData = [];
            
            // Map the row data to our database fields
            foreach ($mappings as $columnIndex => $mapping) {
                $value = isset($row[$columnIndex]) ? trim($row[$columnIndex]) : '';
                $dbField = $mapping['db_field'];
                
                switch ($dbField) {
                    case 'package_name':
                        $packageData['name'] = $value;
                        break;
                    case 'package_code':
                        $packageData['code'] = $value;
                        break;
                    case 'total_days':
                        $packageData['total_days'] = intval($value);
                        break;
                    case 'day_number':
                        $requirementsData['day_number'] = intval($value);
                        break;
                    case 'hotel_name':
                        $requirementsData['hotel_name'] = $value;
                        break;
                    case 'guide_required':
                        $requirementsData['guide_required'] = (strtolower($value) === 'yes' || $value === '1');
                        break;
                    case 'vehicle_required':
                        $requirementsData['vehicle_required'] = (strtolower($value) === 'yes' || $value === '1');
                        break;
                    case 'vehicle_type':
                        $requirementsData['vehicle_type'] = $value;
                        break;
                    case 'services':
                        $requirementsData['services'] = $value;
                        break;
                    case 'activities':
                        $requirementsData['activities'] = $value;
                        break;
                }
            }
            
            // Create or get package
            $packageId = null;
            $packageKey = $packageData['name'] . '_' . $packageData['code'];
            
            if (isset($processedPackages[$packageKey])) {
                $packageId = $processedPackages[$packageKey];
            } else {
                // Create new package
                if (empty($packageData['name'])) {
                    continue; // Skip rows without package name
                }
                
                $packageCode = $packageData['code'] ?: 'PKG_' . time();
                $totalDays = $packageData['total_days'] ?: 1;
                
                $stmt = $conn->prepare("INSERT INTO trip_packages (name, code, No_of_Days, description) VALUES (?, ?, ?, ?)");
                $description = 'Imported package';
                $stmt->bind_param('ssis', $packageData['name'], $packageCode, $totalDays, $description);
                
                if ($stmt->execute()) {
                    $packageId = $conn->insert_id;
                    $processedPackages[$packageKey] = $packageId;
                    $packagesCreated++;
                } else {
                    throw new Exception('Failed to create package: ' . $stmt->error);
                }
                $stmt->close();
            }
            
            // Add day requirements if we have day data
            if ($packageId && !empty($requirementsData['day_number'])) {
                $dayNumber = $requirementsData['day_number'];
                $hotelId = null;
                
                // Try to find hotel by name
                if (!empty($requirementsData['hotel_name'])) {
                    $hotelStmt = $conn->prepare("SELECT id FROM hotels WHERE name LIKE ?");
                    $hotelName = '%' . $requirementsData['hotel_name'] . '%';
                    $hotelStmt->bind_param('s', $hotelName);
                    $hotelStmt->execute();
                    $hotelResult = $hotelStmt->get_result();
                    if ($hotelRow = $hotelResult->fetch_assoc()) {
                        $hotelId = $hotelRow['id'];
                    }
                    $hotelStmt->close();
                }
                
                // Insert or update day requirements
                $reqStmt = $conn->prepare("
                    INSERT INTO trip_package_requirements 
                    (trip_package_id, day_number, hotel_id, guide_required, vehicle_required, vehicle_type, day_services, day_notes) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE
                    hotel_id = VALUES(hotel_id),
                    guide_required = VALUES(guide_required),
                    vehicle_required = VALUES(vehicle_required),
                    vehicle_type = VALUES(vehicle_type),
                    day_services = VALUES(day_services),
                    day_notes = VALUES(day_notes)
                ");
                
                $guideRequired = $requirementsData['guide_required'] ? 1 : 0;
                $vehicleRequired = $requirementsData['vehicle_required'] ? 1 : 0;
                $vehicleType = $requirementsData['vehicle_type'] ?: null;
                $services = $requirementsData['services'] ?: '';
                $notes = $requirementsData['activities'] ?: '';
                
                $reqStmt->bind_param('iiiissss', 
                    $packageId, $dayNumber, $hotelId, $guideRequired, $vehicleRequired, $vehicleType, $services, $notes
                );
                
                if ($reqStmt->execute()) {
                    $daysImported++;
                } else {
                    throw new Exception('Failed to create day requirements: ' . $reqStmt->error);
                }
                $reqStmt->close();
            }
        }
        
        $conn->commit();
        $processingTime = round((microtime(true) - $startTime) * 1000) . 'ms';
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Packages imported successfully',
            'data' => [
                'packages_created' => $packagesCreated,
                'days_imported' => $daysImported,
                'processing_time' => $processingTime
            ]
        ]);
        
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode([
            'status' => 'error',
            'message' => 'Import failed: ' . $e->getMessage()
        ]);
    }
}

// ============= PAX DETAILS FUNCTIONS =============

function getPaxDetails($conn) {
    $trip_id = isset($_GET['trip_id']) ? intval($_GET['trip_id']) : 0;
    
    if ($trip_id === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Trip ID is required']);
        return;
    }
    
    // Check if pax_details table exists, create if not
    ensurePaxDetailsTable($conn);
    
    // Get trip details
    $tripSql = "SELECT id, file_name, tour_code FROM trips WHERE id = ?";
    $tripStmt = $conn->prepare($tripSql);
    $tripStmt->bind_param('i', $trip_id);
    $tripStmt->execute();
    $tripResult = $tripStmt->get_result();
    $trip = $tripResult->fetch_assoc();
    
    if (!$trip) {
        echo json_encode(['status' => 'error', 'message' => 'Trip not found']);
        return;
    }
    
    // Calculate room totals from itinerary_days and get hotel names
    $daysSql = "SELECT id.room_type_data, id.hotel_id, h.name as hotel_name 
                FROM itinerary_days id 
                LEFT JOIN hotels h ON id.hotel_id = h.id 
                WHERE id.trip_id = ? AND id.room_type_data IS NOT NULL";
    $daysStmt = $conn->prepare($daysSql);
    $daysStmt->bind_param('i', $trip_id);
    $daysStmt->execute();
    $daysResult = $daysStmt->get_result();
    
    $roomTotals = ['double' => 0, 'single' => 0, 'triple' => 0, 'twin' => 0];
    $maxRooms = ['double' => 0, 'single' => 0, 'triple' => 0, 'twin' => 0];
    $hotels = [];
    
    while ($dayRow = $daysResult->fetch_assoc()) {
        if ($dayRow['room_type_data']) {
            $roomData = json_decode($dayRow['room_type_data'], true);
            if ($roomData) {
                // Track the maximum of each room type across all days
                foreach (['double', 'single', 'triple', 'twin'] as $type) {
                    $count = intval($roomData[$type] ?? 0);
                    if ($count > $maxRooms[$type]) {
                        $maxRooms[$type] = $count;
                    }
                }
            }
        }
        // Collect unique hotel names
        if ($dayRow['hotel_name'] && !in_array($dayRow['hotel_name'], $hotels)) {
            $hotels[] = $dayRow['hotel_name'];
        }
    }
    
    // Check if there's saved PAX details (manual overrides)
    $sql = "SELECT * FROM pax_details WHERE trip_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $trip_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $savedPax = $result->fetch_assoc();
    
    // Use saved values if they exist, otherwise use calculated max from itinerary
    $paxDetails = [
        $trip_id => [
            'trip_id' => $trip_id,
            'file_name' => $trip['file_name'] ?: $trip['tour_code'] ?: 'File ' . $trip_id,
            'double' => $savedPax ? intval($savedPax['double_rooms']) : $maxRooms['double'],
            'single' => $savedPax ? intval($savedPax['single_rooms']) : $maxRooms['single'],
            'triple' => $savedPax ? intval($savedPax['triple_rooms']) : $maxRooms['triple'],
            'twin' => $savedPax ? intval($savedPax['twin_rooms']) : $maxRooms['twin'],
            'hotels' => $hotels, // Include hotel names
            'amendments' => [],
            'from_itinerary' => !$savedPax // Flag to indicate if values are from itinerary
        ]
    ];
    
    // Get amendments for this trip
    $amendSql = "SELECT * FROM pax_amendments WHERE trip_id = ? ORDER BY created_at ASC";
    $amendStmt = $conn->prepare($amendSql);
    $amendStmt->bind_param('i', $trip_id);
    $amendStmt->execute();
    $amendResult = $amendStmt->get_result();
    
    while ($amendRow = $amendResult->fetch_assoc()) {
        $paxDetails[$trip_id]['amendments'][] = [
            'room_type' => $amendRow['room_type'],
            'old_value' => intval($amendRow['old_value']),
            'new_value' => intval($amendRow['new_value']),
            'timestamp' => $amendRow['created_at'],
            'user_id' => isset($amendRow['user_id']) ? intval($amendRow['user_id']) : null,
            'user_name' => $amendRow['user_name'] ?? null
        ];
    }
    
    echo json_encode(['status' => 'success', 'data' => $paxDetails]);
}

function savePaxDetails($conn) {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!isset($data['trip_id']) || !isset($data['pax_data'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
        return;
    }
    
    $trip_id = intval($data['trip_id']);
    $pax_data = $data['pax_data'];
    
    // Check if pax_details table exists, create if not
    ensurePaxDetailsTable($conn);
    
    try {
        $conn->begin_transaction();
        
        // Get existing values for amendment tracking
        $existingSql = "SELECT * FROM pax_details WHERE trip_id = ?";
        $existingStmt = $conn->prepare($existingSql);
        $existingStmt->bind_param('i', $trip_id);
        $existingStmt->execute();
        $existingResult = $existingStmt->get_result();
        $existing = $existingResult->fetch_assoc();
        
        // Upsert pax_details
        $double = intval($pax_data['double'] ?? 0);
        $single = intval($pax_data['single'] ?? 0);
        $triple = intval($pax_data['triple'] ?? 0);
        $twin = intval($pax_data['twin'] ?? 0);
        
        $upsertSql = "INSERT INTO pax_details (trip_id, double_rooms, single_rooms, triple_rooms, twin_rooms, updated_at) 
                      VALUES (?, ?, ?, ?, ?, NOW()) 
                      ON DUPLICATE KEY UPDATE 
                      double_rooms = VALUES(double_rooms), 
                      single_rooms = VALUES(single_rooms), 
                      triple_rooms = VALUES(triple_rooms), 
                      twin_rooms = VALUES(twin_rooms), 
                      updated_at = NOW()";
        $upsertStmt = $conn->prepare($upsertSql);
        $upsertStmt->bind_param('iiiii', $trip_id, $double, $single, $triple, $twin);
        $upsertStmt->execute();
        
        // Track amendments if values changed
        if ($existing) {
            $roomTypes = ['double', 'single', 'triple', 'twin'];
            $dbFields = ['double_rooms', 'single_rooms', 'triple_rooms', 'twin_rooms'];
            
            foreach ($roomTypes as $idx => $roomType) {
                $dbField = $dbFields[$idx];
                $oldValue = intval($existing[$dbField]);
                $newValue = intval($pax_data[$roomType] ?? 0);
                
                if ($oldValue !== $newValue) {
                    // Capture the user making this change, if available
                    $userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;
                    $userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : null;

                    $amendSql = "INSERT INTO pax_amendments (trip_id, room_type, old_value, new_value, user_id, user_name, created_at) 
                                VALUES (?, ?, ?, ?, ?, ?, NOW())";
                    $amendStmt = $conn->prepare($amendSql);
                    $amendStmt->bind_param('isiiis', $trip_id, $roomType, $oldValue, $newValue, $userId, $userName);
                    $amendStmt->execute();
                }
            }
        }
        
        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => 'PAX details saved']);
        
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Save failed: ' . $e->getMessage()]);
    }
}

function getPaxAmendments($conn) {
    $trip_id = isset($_GET['trip_id']) ? intval($_GET['trip_id']) : 0;
    
    if ($trip_id === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Trip ID is required']);
        return;
    }
    
    ensurePaxDetailsTable($conn);
    
    $sql = "SELECT * FROM pax_amendments WHERE trip_id = ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $trip_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $amendments = [];
    while ($row = $result->fetch_assoc()) {
        $userName = $row['user_name'] ?? '';
        if (($userName === '' || $userName === null) && !empty($row['user_id'])) {
            $uStmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
            if ($uStmt) {
                $uid = intval($row['user_id']);
                $uStmt->bind_param('i', $uid);
                $uStmt->execute();
                $uRes = $uStmt->get_result();
                if ($uRes && $uRes->num_rows > 0) {
                    $uRow = $uRes->fetch_assoc();
                    $userName = $uRow['name'] ?? $userName;
                }
                $uStmt->close();
            }
        }
        $amendments[] = [
            'room_type' => $row['room_type'],
            'old_value' => intval($row['old_value']),
            'new_value' => intval($row['new_value']),
            'timestamp' => $row['created_at'],
            'user_id' => isset($row['user_id']) ? intval($row['user_id']) : null,
            'user_name' => $userName ?: null
        ];
    }
    
    echo json_encode(['status' => 'success', 'data' => $amendments]);
}

function ensurePaxDetailsTable($conn) {
    // Create pax_details table if it doesn't exist
    $createPaxDetailsTable = "
        CREATE TABLE IF NOT EXISTS pax_details (
            id INT AUTO_INCREMENT PRIMARY KEY,
            trip_id INT NOT NULL,
            double_rooms INT DEFAULT 0,
            single_rooms INT DEFAULT 0,
            triple_rooms INT DEFAULT 0,
            twin_rooms INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_trip (trip_id),
            FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ";
    $conn->query($createPaxDetailsTable);
    
    // Create pax_amendments table if it doesn't exist (includes user columns for fresh installs)
    $createPaxAmendmentsTable = "
        CREATE TABLE IF NOT EXISTS pax_amendments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            trip_id INT NOT NULL,
            room_type VARCHAR(20) NOT NULL,
            old_value INT NOT NULL,
            new_value INT NOT NULL,
            user_id INT NULL,
            user_name VARCHAR(255) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE,
            INDEX idx_trip_created (trip_id, created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ";
    $conn->query($createPaxAmendmentsTable);

    // Ensure user tracking columns exist for existing installations
    $res1 = $conn->query("SHOW COLUMNS FROM pax_amendments LIKE 'user_id'");
    if (!$res1 || $res1->num_rows === 0) {
        $conn->query("ALTER TABLE pax_amendments ADD COLUMN user_id INT NULL AFTER new_value");
    }
    $res2 = $conn->query("SHOW COLUMNS FROM pax_amendments LIKE 'user_name'");
    if (!$res2 || $res2->num_rows === 0) {
        $conn->query("ALTER TABLE pax_amendments ADD COLUMN user_name VARCHAR(255) NULL AFTER user_id");
    }
}

function updateItineraryRooms($conn) {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!isset($data['trip_id']) || !isset($data['room_data'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
        return;
    }
    
    $trip_id = intval($data['trip_id']);
    $room_data = $data['room_data'];
    
    // Build room_type_data JSON
    $room_json = json_encode([
        'double' => intval($room_data['double'] ?? 0),
        'twin' => intval($room_data['twin'] ?? 0),
        'single' => intval($room_data['single'] ?? 0),
        'triple' => intval($room_data['triple'] ?? 0)
    ]);
    
    try {
        // Update all itinerary days for this trip with new room counts
        $sql = "UPDATE itinerary_days SET room_type_data = ? WHERE trip_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $room_json, $trip_id);
        $stmt->execute();
        
        echo json_encode(['status' => 'success', 'message' => 'Itinerary rooms updated']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Update failed: ' . $e->getMessage()]);
    }
}

// ============= ACTIVITY MANAGEMENT =============
function getActivities($conn) {
    // Ensure activities table exists
    ensureActivitiesTable($conn);
    
    $sql = "SELECT * FROM package_activities ORDER BY name ASC";
    $result = $conn->query($sql);
    
    if (!$result) {
        echo json_encode(['status' => 'error', 'message' => 'Database query failed: ' . $conn->error]);
        return;
    }
    
    $activities = [];
    while ($row = $result->fetch_assoc()) {
        $activities[] = $row;
    }
    
    echo json_encode(['status' => 'success', 'data' => $activities]);
}

function addActivity($conn) {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    
    if (empty($name)) {
        echo json_encode(['status' => 'error', 'message' => 'Activity name is required']);
        return;
    }
    
    // Ensure activities table exists
    ensureActivitiesTable($conn);
    
    // Check for duplicate activity name
    $checkSql = "SELECT COUNT(*) as count FROM package_activities WHERE name = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param('s', $name);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    $row = $result->fetch_assoc();
    $checkStmt->close();
    
    if ($row && intval($row['count']) > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Activity with this name already exists']);
        return;
    }
    
    // Detect if description column exists
    $hasDesc = false;
    $colRes = $conn->query("SHOW COLUMNS FROM package_activities LIKE 'description'");
    if ($colRes && $colRes->num_rows > 0) { $hasDesc = true; }
    
    // Insert new activity (schema-aware)
    if ($hasDesc) {
        $sql = "INSERT INTO package_activities (name, description, created_at) VALUES (?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        if (!$stmt) { echo json_encode(['status' => 'error', 'message' => 'Database prepare failed: ' . $conn->error]); return; }
        $stmt->bind_param('ss', $name, $description);
    } else {
        $sql = "INSERT INTO package_activities (name, created_at) VALUES (?, NOW())";
        $stmt = $conn->prepare($sql);
        if (!$stmt) { echo json_encode(['status' => 'error', 'message' => 'Database prepare failed: ' . $conn->error]); return; }
        $stmt->bind_param('s', $name);
    }
    
    if ($stmt->execute()) {
        $activity_id = $conn->insert_id;
        echo json_encode([
            'status' => 'success', 
            'message' => 'Activity added successfully',
            'data' => [
                'id' => $activity_id,
                'name' => $name,
                'description' => $hasDesc ? $description : null
            ]
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add activity: ' . $stmt->error]);
    }
    
    $stmt->close();
}

function ensureActivitiesTable($conn) {
    // Create package_activities table if it doesn't exist
    $createActivitiesTable = "
        CREATE TABLE IF NOT EXISTS package_activities (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_name (name)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ";
    $conn->query($createActivitiesTable);

    // Seed default/common activities (idempotent)
    try {
        // Detect if 'description' column exists to tailor inserts
        $hasDesc = false;
        $colRes = $conn->query("SHOW COLUMNS FROM package_activities LIKE 'description'");
        if ($colRes && $colRes->num_rows > 0) { $hasDesc = true; }
        
        $defaults = [
            // Set 1
            'Departure From Home',
            'Arrival and transfer to Bhaktapur.',
            'Drive to Namobuddha visit monastery and evenig sightseeing at Bhaktapur.',
            'Drive from Bhaktapur to Charaudi and rafting to Kurintar and short walk to resort.',
            'Walk back to the highway and drive from Kurintar to Gorkha.',
            'Drive from Gorkha to Pokhara and further upto Dhampus Phedi and hike to Dhampus.',
            'Hike from Dhampus to Potana and to Australian camp and back to lodge.Evenign Momo Class.',
            'After breakfast hike to Gurung village and lunch with Locals and hike to Suiket phedi and drive to Pokhara.',
            'Boating over Phewa lake and hike to Peace stupa and back to hotel.',
            'Drive from Pokhara to Chitwan .',
            'Jungle activities in Chitwan National Park',
            'Drive from Chitwan to Kathmandu and sightseeing at Pashupati.',
            'Explore Ason Market and drive to Childrens home.',
            'Depature tansfer to airport',
            // Set 2
            'Arrival and Transfer to Hotel at Bhaktapur',
            'Sightseeing of Royal City Bhaktapur',
            'Drive from Bhaktapur to Changunarayan and hike to Telko and drive to Nagarkot',
            'Hike from Nagarkot to Telkot and drive to Kathmandu',
            'Drive from Kathmandu to Bandipur',
            'Drive from Bandipur to Dhampus Phedi and hike to Dhampus',
            'Trek from Dhampus to Landruk (1 640 m)',
            'Trek from Landruk to Ghandruk ( 2 050 m)',
            'Trek from Ghandruk to Tadapani (2 685 m)',
            'Trek from Tadapani to Ghorepani (2 870 m)',
            'Early morning hike to Poonhill for sunrise and back to Ghorpania to Ulleri and drive to Pokhara.',
            'Exploration day at Pokhara',
            'Drive from Pokhara to Chitwan National Park',
            'Jungle Activities at Chitwan National Park',
            'Drive back from Chitwan to Kathmandu and evening Pashupati and Boudhnath sightseeing farewell dinner and drive back to Hotel.',
            'Departure Transfer to airport.'
        ];
        // Prepare once
        if ($hasDesc) {
            $ins = $conn->prepare("INSERT IGNORE INTO package_activities (name, description, created_at) VALUES (?, '', NOW())");
            if ($ins) {
                foreach ($defaults as $raw) {
                    $name = trim(preg_replace('/\s+/', ' ', $raw));
                    if ($name === '') { continue; }
                    $ins->bind_param('s', $name);
                    $ins->execute();
                }
                $ins->close();
            }
        } else {
            $ins = $conn->prepare("INSERT IGNORE INTO package_activities (name, created_at) VALUES (?, NOW())");
            if ($ins) {
                foreach ($defaults as $raw) {
                    $name = trim(preg_replace('/\s+/', ' ', $raw));
                    if ($name === '') { continue; }
                    $ins->bind_param('s', $name);
                    $ins->execute();
                }
                $ins->close();
            }
        }
    } catch (Exception $e) {
        // Non-fatal
        error_log('Activity seeding failed: '.$e->getMessage());
    }
}
