<?php
// Try to include session check with error handling
if (file_exists('../../utils/check_session.php')) {
    require_once '../../utils/check_session.php';
} else {
    // Fallback session check
    session_start();
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => 'Authentication required',
            'redirect' => 'login.html'
        ]);
        exit;
    }
}

header('Content-Type: application/json');
include '../../src/services/db_connect.php';

$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

// Handle CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

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
    case 'checkGuideAvailability':
        checkGuideAvailability($conn);
        break;
    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action specified.']);
        break;
}

// ============= TRIP MANAGEMENT =============
function getTrips($conn) {
    $sql = "SELECT t.id, t.customer_name, t.tour_code, t.start_date, t.end_date, t.status, t.trip_package_id, p.name as package_name
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
    $result = $conn->query("SELECT id, name, description, No_of_Days, total_price FROM trip_packages ORDER BY name");

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
function addTrip($conn) {
    $customer_name = isset($_POST['customer_name']) ? trim($_POST['customer_name']) : '';
    $tour_code = isset($_POST['tour_code']) ? trim($_POST['tour_code']) : '';
    $trip_package_id = isset($_POST['trip_package_id']) ? intval($_POST['trip_package_id']) : 0;
    $start_date = isset($_POST['start_date']) ? trim($_POST['start_date']) : '';
    $end_date = isset($_POST['end_date']) ? trim($_POST['end_date']) : '';
    $status = isset($_POST['status']) ? trim($_POST['status']) : 'Pending';

    if (empty($customer_name) || $trip_package_id === 0 || empty($start_date) || empty($end_date)) {
        echo json_encode(['status' => 'error', 'message' => 'Please fill all required fields.']);
        return;
    }

    if (strtotime($end_date) < strtotime($start_date)) {
        echo json_encode(['status' => 'error', 'message' => 'End date cannot be before the start date.']);
        return;
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
        // --- 2. INSERT trip with correct bind_param ---
        $stmt = $conn->prepare("INSERT INTO trips (customer_name, tour_code, trip_package_id, start_date, end_date, status, total_price) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }
        $stmt->bind_param("ssisssd", $customer_name, $tour_code, $trip_package_id, $start_date, $end_date, $status, $total_price);
        if (!$stmt->execute()) {
            throw new Exception('Execute failed: ' . $stmt->error);
        }
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
            $current_date = new DateTime($start_date);
            $end = new DateTime($end_date);
            $end->modify('+1 day'); 
            
            $stmt_itinerary = $conn->prepare("INSERT INTO itinerary_days (trip_id, day_date, guide_id, vehicle_id, hotel_id, notes, services_provided, day_type) VALUES (?, ?, NULL, NULL, NULL, '', '', 'normal')");
            if (!$stmt_itinerary) {
                throw new Exception('Prepare itinerary failed: ' . $conn->error);
            }
            
            while ($current_date < $end) {
                $date_str = $current_date->format('Y-m-d');
                $stmt_itinerary->bind_param("is", $trip_id, $date_str);
                if (!$stmt_itinerary->execute()) {
                    throw new Exception('Itinerary insert failed: ' . $stmt_itinerary->error);
                }
                $current_date->modify('+1 day');
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

    if (empty($id) || empty($customer_name) || $trip_package_id === 0 || empty($start_date) || empty($end_date)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        return;
    }

    $stmt = $conn->prepare("UPDATE trips SET customer_name = ?, tour_code = ?, trip_package_id = ?, start_date = ?, end_date = ?, status = ? WHERE id = ?");
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Database prepare failed: ' . $conn->error]);
        return;
    }

    $stmt->bind_param("ssisssi", $customer_name, $tour_code, $trip_package_id, $start_date, $end_date, $status, $id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Trip updated successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update trip: ' . $stmt->error]);
    }
    $stmt->close();
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
    $data['vehicles'] = $conn->query("SELECT id, vehicle_name, capacity FROM vehicles ORDER BY vehicle_name")->fetch_all(MYSQLI_ASSOC);
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
                $fields['guide_informed'] = intval($day['guide_informed']) ? 1 : 0;
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
    $result = $conn->query("SELECT id, vehicle_name, capacity, availability FROM vehicles ORDER BY vehicle_name");
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

    if (empty($vehicle_name)) {
        echo json_encode(['status' => 'error', 'message' => 'Vehicle name is required.']);
        return;
    }

    $stmt = $conn->prepare("INSERT INTO vehicles (vehicle_name, capacity, availability) VALUES (?, ?, ?)");
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Database prepare failed: ' . $conn->error]);
        return;
    }

    $stmt->bind_param("sis", $vehicle_name, $capacity, $availability);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Vehicle added successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add vehicle: ' . $stmt->error]);
    }
    $stmt->close();
}

function updateVehicle($conn) {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $vehicle_name = isset($_POST['vehicle_name']) ? trim($_POST['vehicle_name']) : '';
    $capacity = isset($_POST['capacity']) ? intval($_POST['capacity']) : 1;
    $availability = isset($_POST['availability']) ? trim($_POST['availability']) : 'Available';

    if (empty($id) || empty($vehicle_name)) {
        echo json_encode(['status' => 'error', 'message' => 'Vehicle ID and name are required.']);
        return;
    }

    $stmt = $conn->prepare("UPDATE vehicles SET vehicle_name = ?, capacity = ?, availability = ? WHERE id = ?");
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Database prepare failed: ' . $conn->error]);
        return;
    }

    $stmt->bind_param("sisi", $vehicle_name, $capacity, $availability, $id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Vehicle updated successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update vehicle: ' . $stmt->error]);
    }
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
    $code = trim($data['code'] ?? '');
    $days = intval($data['No_of_Days'] ?? 0);
    $day_requirements = $data['day_requirements'] ?? [];
    
    if (empty($name) || $days < 1) {
        echo json_encode(['status' => 'error', 'message' => 'Package name and days are required']);
        return;
    }
    
    $conn->begin_transaction();
    try {
        // Insert package
        $stmt = $conn->prepare("INSERT INTO trip_packages (name, code, No_of_Days) VALUES (?, ?, ?)");
        if (!$stmt) {
            throw new Exception('Database prepare failed: ' . $conn->error);
        }
        
        $stmt->bind_param("ssi", $name, $code, $days);
        if (!$stmt->execute()) {
            throw new Exception('Failed to insert package: ' . $stmt->error);
        }
        
        $package_id = $conn->insert_id;
        $stmt->close();
        
        // Insert day requirements
        if (!empty($day_requirements)) {
            $req_stmt = $conn->prepare(
                "INSERT INTO package_day_requirements (trip_package_id, day_number, hotel_id, guide_required, vehicle_required, vehicle_type) 
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            
            if (!$req_stmt) {
                throw new Exception('Failed to prepare requirements statement: ' . $conn->error);
            }
            
            foreach ($day_requirements as $day => $req) {
                $hotel_id = !empty($req['hotel_id']) ? intval($req['hotel_id']) : null;
                $guide_required = $req['guide_required'] ? 1 : 0;
                $vehicle_required = $req['vehicle_required'] ? 1 : 0;
                $vehicle_type = !empty($req['vehicle_type']) ? $req['vehicle_type'] : null;
                
                $req_stmt->bind_param("iiiiss", $package_id, $day, $hotel_id, $guide_required, $vehicle_required, $vehicle_type);
                if (!$req_stmt->execute()) {
                    throw new Exception('Failed to insert day requirement: ' . $req_stmt->error);
                }
            }
            
            $req_stmt->close();
        }
        
        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => 'Package created successfully']);
        
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
    $code = trim($data['code'] ?? '');
    $days = intval($data['No_of_Days'] ?? 0);
    $day_requirements = $data['day_requirements'] ?? [];
    
    if (!$id || empty($name) || $days < 1) {
        echo json_encode(['status' => 'error', 'message' => 'Package ID, name and days are required']);
        return;
    }
    
    $conn->begin_transaction();
    try {
        // Update package
        $stmt = $conn->prepare("UPDATE trip_packages SET name = ?, code = ?, No_of_Days = ? WHERE id = ?");
        if (!$stmt) {
            throw new Exception('Database prepare failed: ' . $conn->error);
        }
        
        $stmt->bind_param("ssii", $name, $code, $days, $id);
        if (!$stmt->execute()) {
            throw new Exception('Failed to update package: ' . $stmt->error);
        }
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
            $req_stmt = $conn->prepare(
                "INSERT INTO package_day_requirements (trip_package_id, day_number, hotel_id, guide_required, vehicle_required, vehicle_type) 
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            
            if (!$req_stmt) {
                throw new Exception('Failed to prepare requirements statement: ' . $conn->error);
            }
            
            foreach ($day_requirements as $day => $req) {
                $hotel_id = !empty($req['hotel_id']) ? intval($req['hotel_id']) : null;
                $guide_required = $req['guide_required'] ? 1 : 0;
                $vehicle_required = $req['vehicle_required'] ? 1 : 0;
                $vehicle_type = !empty($req['vehicle_type']) ? $req['vehicle_type'] : null;
                
                $req_stmt->bind_param("iiiiss", $id, $day, $hotel_id, $guide_required, $vehicle_required, $vehicle_type);
                if (!$req_stmt->execute()) {
                    throw new Exception('Failed to insert day requirement: ' . $req_stmt->error);
                }
            }
            
            $req_stmt->close();
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
    $sql = "SELECT day_number, hotel_id, guide_required, vehicle_required, vehicle_type 
            FROM package_day_requirements 
            WHERE trip_package_id = ? 
            ORDER BY day_number";
    
    $stmt = $conn->prepare($sql);
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

// ============= HOTEL RECORDS =============
function getHotelRecords($conn) {
    $sql = "
        SELECT 
            t.id as trip_id,
            t.customer_name as guest_name,
            t.tour_code,
            t.status,
            id.day_date as check_in_date,
            id.day_date as check_out_date,
            h.name as hotel_name,
            id.room_type_data as room_details
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
                'room_details' => $row['room_details']
            ];
        }
        
        $grouped_bookings[$key]['dates'][] = $row['check_in_date'];
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
            $records[] = [
                'trip_id' => $booking['trip_id'],
                'guest_name' => $booking['guest_name'],
                'tour_code' => $booking['tour_code'],
                'hotel_name' => $booking['hotel_name'],
                'check_in_date' => $group[0],
                'check_out_date' => end($group),
                'status' => $booking['status'],
                'room_details' => $booking['room_details']
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
        $sql = "SELECT DISTINCT 
                   g.id as guide_id,
                   g.name as guide_name,
                   g.email as guide_email,
                   g.language as guide_language,
                   g.availability_status as guide_status,
                   id.day_date as assignment_date,
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
        
        if (isset($stmt)) {
            $stmt->close();
        }
        
        echo json_encode(['status' => 'success', 'data' => $records]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    
    $result = $conn->query($sql);
    if (!$result) {
        echo json_encode(['status' => 'error', 'message' => 'Database query failed: ' . $conn->error]);
        return;
    }

    $records = [];
    while ($row = $result->fetch_assoc()) {
        $records[] = $row;
    }
    
    echo json_encode(['status' => 'success', 'data' => $records]);
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

$conn->close();
?>
