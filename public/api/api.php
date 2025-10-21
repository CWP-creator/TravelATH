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
    case 'checkGuideConflict':
        checkGuideConflict($conn);
        break;
    case 'updateTripPax':
        updateTripPax($conn);
        break;
    case 'getNextTourCode':
        getNextTourCode($conn);
        break;
    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action specified.']);
        break;
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
    $customer_name = isset($_POST['customer_name']) ? trim($_POST['customer_name']) : '';
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

    // Derive trip start/end from arrival/departure if not provided
    if (empty($start_date)) { $start_date = $arrival_date; }
    if (empty($end_date)) { $end_date = $departure_date; }

    if (empty($customer_name) || $trip_package_id === 0 || empty($start_date) || empty($end_date)) {
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
            if ($col === 'trip_package_id') { $types .= 'i'; $values[] = (int)$fields[$col]; }
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

        // --- 3. Create Itinerary Entries ---
        
        // --- SPECIAL LOGIC FOR ANNAPURNA TREK ---
        if ($package_name === 'Annapurna Base Camp Trek') {
            
$annapurna_itinerary = [
    1 => ['notes' => "Arrival in Kathmandu. Various arrival times. Depending on flight, pick-up and transfer to the hotel.", 'hotel' => "Sweethome Bhaktapur", 'meals' => ""],
    2 => ['notes' => "Sightseeing in Bhaktapur and the surrounding area.", 'hotel' => "Sweethome Bhaktapur", 'meals' => "B"],
    3 => ['notes' => "Flight to Pokhara. In the afternoon: drive to Ulleri – trekking to Ghorepani (2,750 m).", 'hotel' => "Trekking Lodges", 'meals' => "B, L, D"],
    4 => ['notes' => "Sunrise at Poon Hill (3,150 m), trekking to Tadapani (2,760 m).", 'hotel' => "Trekking Lodges", 'meals' => "B, L, D"],
    5 => ['notes' => "Trekking to Chhomrong (2,170 m).", 'hotel' => "Trekking Lodges", 'meals' => "B, L, D"],
    6 => ['notes' => "Trekking via Bamboo (2,370 m) to Dobhan (2,560 m).", 'hotel' => "Trekking Lodges", 'meals' => "B, L, D"],
    7 => ['notes' => "Trekking to Deurali (3,210 m) and on to MBC (Machapuchare Base Camp, 3,700 m).", 'hotel' => "Trekking Lodges", 'meals' => "B, L, D"],
    8 => ['notes' => "Trekking to ABC (Annapurna Base Camp, 4,130 m) and return to MBC.", 'hotel' => "Trekking Lodges", 'meals' => "B, L, D"],
    9 => ['notes' => "Return trek to Bamboo.", 'hotel' => "Trekking Lodges", 'meals' => "B, L, D"],
    10 => ['notes' => "Trekking via Chhomrong to Jhinu Danda; if possible, drive back to Pokhara.", 'hotel' => "Pokhara Lake View Resort", 'meals' => "B, L"],
    11 => ['notes' => "Pokhara at leisure – relax day.", 'hotel' => "Pokhara Lake View Resort", 'meals' => "B"],
    12 => ['notes' => "Flight to Kathmandu. Afternoon free.", 'hotel' => "Shambala Hotel", 'meals' => "B"],
    13 => ['notes' => "Sightseeing in Kathmandu.", 'hotel' => "Shambala Hotel", 'meals' => "B, D"],
    14 => ['notes' => "Departure for home.", 'hotel' => null, 'meals' => "B"],
];

            // --- Robust hotel lookup ---
            $hotel_names_from_plan = array_unique(array_filter(array_column($annapurna_itinerary, 'hotel')));
            $hotel_ids = [];
            if (!empty($hotel_names_from_plan)) {
                $all_hotels_result = $conn->query("SELECT id, name FROM hotels");
                $db_hotels = [];
                while ($row = $all_hotels_result->fetch_assoc()) {
                    $db_hotels[strtolower(trim($row['name']))] = $row['id'];
                }

                foreach ($hotel_names_from_plan as $plan_hotel_name) {
                    $lookup_key = strtolower(trim($plan_hotel_name));
                    if (isset($db_hotels[$lookup_key])) {
                        $hotel_ids[$plan_hotel_name] = $db_hotels[$lookup_key];
                    }
                }
            }

            $stmt_itinerary = $conn->prepare("INSERT INTO itinerary_days (trip_id, day_date, hotel_id, notes, services_provided, guide_id, vehicle_id, day_type) VALUES (?, ?, ?, ?, ?, NULL, NULL, 'normal')");
            if (!$stmt_itinerary) {
                throw new Exception('Prepare itinerary failed: ' . $conn->error);
            }

            $current_date = new DateTime($start_date);
            $day_counter = 1;
            $trip_duration = (new DateTime($end_date))->diff(new DateTime($start_date))->days + 1;

            while ($day_counter <= $trip_duration) {
                $date_str = $current_date->format('Y-m-d');
                $day_details = $annapurna_itinerary[$day_counter] ?? ['notes' => 'Day off or unspecified activities.', 'hotel' => null, 'meals' => ''];
                
                $hotel_id = null;
                if ($day_details['hotel'] && isset($hotel_ids[$day_details['hotel']])) {
                    $hotel_id = $hotel_ids[$day_details['hotel']];
                }
                
                $notes = $day_details['notes'];
                $services_provided = $day_details['meals'];
                
                $stmt_itinerary->bind_param("isiss", $trip_id, $date_str, $hotel_id, $notes, $services_provided);
                if (!$stmt_itinerary->execute()) {
                    throw new Exception('Annapurna itinerary insert failed: ' . $stmt_itinerary->error);
                }
                
                $current_date->modify('+1 day');
                $day_counter++;
            }
            $stmt_itinerary->close();

        } else {
            // --- DEFAULT LOGIC for any other trip package ---
            // Try to pull package day requirements for notes/services/hotel.
            $pkgReq = [];
            $hasSvc=false; $hasNotes=false;
            $c1=$conn->query("SHOW COLUMNS FROM package_day_requirements LIKE 'day_services'"); if($c1 && $c1->num_rows>0)$hasSvc=true;
            $c2=$conn->query("SHOW COLUMNS FROM package_day_requirements LIKE 'day_notes'"); if($c2 && $c2->num_rows>0)$hasNotes=true;
            $sel = "SELECT day_number, hotel_id" . ($hasSvc? ", day_services" : "") . ($hasNotes? ", day_notes" : "") . " FROM package_day_requirements WHERE trip_package_id = ?";
            if ($s=$conn->prepare($sel)){
                $s->bind_param('i', $trip_package_id); $s->execute(); $rs=$s->get_result();
                while($row=$rs->fetch_assoc()){ $pkgReq[(int)$row['day_number']]=$row; }
                $s->close();
            }

            $current_date = new DateTime($start_date);
            $end = new DateTime($end_date);
            $end->modify('+1 day'); 
            
            $stmt_itinerary = $conn->prepare("INSERT INTO itinerary_days (trip_id, day_date, guide_id, vehicle_id, hotel_id, notes, services_provided, day_type) VALUES (?, ?, NULL, NULL, ?, ?, ?, 'normal')");
            if (!$stmt_itinerary) {
                throw new Exception('Prepare itinerary failed: ' . $conn->error);
            }
            
            $dayNum = 1;
            while ($current_date < $end) {
                $date_str = $current_date->format('Y-m-d');
                $r = isset($pkgReq[$dayNum]) ? $pkgReq[$dayNum] : null;
                $hid = $r && isset($r['hotel_id']) ? ( (int)$r['hotel_id'] ?: null ) : null;
                $svc = $r && isset($r['day_services']) ? $r['day_services'] : '';
                $nts = $r && isset($r['day_notes']) ? $r['day_notes'] : '';
                $stmt_itinerary->bind_param("isiss", $trip_id, $date_str, $hid, $nts, $svc);
                if (!$stmt_itinerary->execute()) { throw new Exception('Itinerary insert failed: ' . $stmt_itinerary->error); }
                $current_date->modify('+1 day');
                $dayNum++;
            }
            $stmt_itinerary->close();
        }

        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => 'Trip created successfully.']);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Failed to add trip: ' . $e->getMessage()]);
    }
}


function updateTrip($conn) {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $customer_name = isset($_POST['customer_name']) ? trim($_POST['customer_name']) : '';
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

    // Derive trip start/end from arrival/departure if not provided
    if (empty($start_date)) { $start_date = $arrival_date; }
    if (empty($end_date)) { $end_date = $departure_date; }

    if (empty($id) || empty($customer_name) || $trip_package_id === 0 || empty($start_date) || empty($end_date)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        return;
    }

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
        if ($key === 'trip_package_id') { $types .= 'i'; $values[] = (int)$val; }
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
    parse_str(file_get_contents("php://input"), $_DELETE);
    $id = isset($_DELETE['id']) ? intval($_DELETE['id']) : 0;

    if (empty($id)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid Trip ID.']);
        return;
    }

    $stmt = $conn->prepare("DELETE FROM trips WHERE id = ?");
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Database prepare failed: ' . $conn->error]);
        return;
    }

    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Trip deleted successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete trip: ' . $stmt->error]);
    }
    $stmt->close();
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

        foreach($itinerary_days as $day) {
            $id = intval($day['id']);

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
        WHERE id.hotel_id IS NOT NULL AND id.hotel_id != '' AND id.hotel_id != '0'
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
        // Detect guide_informed column
        $hasGuideInformed = false;
        $colCheck = $conn->query("SHOW COLUMNS FROM itinerary_days LIKE 'guide_informed'");
        if ($colCheck && $colCheck->num_rows > 0) { $hasGuideInformed = true; }

        $selectInformed = $hasGuideInformed ? 'id.guide_informed as guide_informed,' : '0 as guide_informed,';

        $sql = "SELECT DISTINCT 
                   g.id as guide_id,
                   g.name as guide_name,
                   g.email as guide_email,
                   g.language as guide_language,
                   g.availability_status as guide_status,
                   id.day_date as assignment_date,
                   $selectInformed
                   t.id as trip_id,
                   t.customer_name as guest_name,
                   t.tour_code,
                   t.status
                FROM guides g
                INNER JOIN itinerary_days id ON g.id = id.guide_id
                INNER JOIN trips t ON id.trip_id = t.id
                WHERE id.guide_id IS NOT NULL AND id.guide_id != '' AND id.guide_id != '0'";
        
        $params = [];
        $types = "";
        
        if ($statusFilter && $statusFilter !== 'all') {
            $sql .= " AND g.availability_status = ?";
            $params[] = $statusFilter;
            $types .= "s";
        }
        
        if ($monthFilter && $monthFilter !== 'all') {
            $sql .= " AND MONTH(id.day_date) = ? AND YEAR(id.day_date) = ?";
            $params[] = $monthFilter;
            $params[] = $year;
            $types .= "ii";
        }
        
        $sql .= " ORDER BY g.name, id.day_date";
        
        if (!empty($params)) {
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $conn->query($sql);
        }
        
        if (!$result) {
            throw new Exception('Database query failed: ' . $conn->error);
        }
        
        $records = [];
        while ($row = $result->fetch_assoc()) {
            $records[] = $row;
        }
        
        if (isset($stmt)) { $stmt->close(); }
        
        echo json_encode(['status' => 'success', 'data' => $records]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
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
        // Detect vehicle_informed column
        $hasVehicleInformed = false;
        $colCheck = $conn->query("SHOW COLUMNS FROM itinerary_days LIKE 'vehicle_informed'");
        if ($colCheck && $colCheck->num_rows > 0) { $hasVehicleInformed = true; }
        $selectInformed = $hasVehicleInformed ? 'id.vehicle_informed as vehicle_informed,' : '0 as vehicle_informed,';
        // Optional number_plate
        $hasPlate = false; $pc = $conn->query("SHOW COLUMNS FROM vehicles LIKE 'number_plate'"); if ($pc && $pc->num_rows > 0) { $hasPlate = true; }
        $selectPlate = $hasPlate ? 'v.number_plate as number_plate,' : "'' as number_plate,";

        $sql = "SELECT 
                   v.id as vehicle_id,
                   v.vehicle_name,
                   $selectPlate
                   v.email as vehicle_email,
                   id.day_date as assignment_date,
                   $selectInformed
                   t.id as trip_id,
                   t.customer_name as guest_name,
                   t.tour_code,
                   t.status
                FROM vehicles v
                INNER JOIN itinerary_days id ON v.id = id.vehicle_id
                INNER JOIN trips t ON id.trip_id = t.id
                WHERE id.vehicle_id IS NOT NULL AND id.vehicle_id != '' AND id.vehicle_id != '0'
                ORDER BY v.vehicle_name, id.day_date";

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

$conn->close();
?>
