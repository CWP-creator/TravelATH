<?php
header('Content-Type: application/json');
include 'db_connect.php';

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

    case 'getRoomTypes':
    getRoomTypes($conn);
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

    // --- 1. FETCH total_price from trip_packages table ---
    $stmt_price = $conn->prepare("SELECT total_price FROM trip_packages WHERE id = ?");
    if (!$stmt_price) {
        echo json_encode(['status' => 'error', 'message' => 'Database prepare failed: ' . $conn->error]);
        return;
    }

    $stmt_price->bind_param("i", $trip_package_id);
    $stmt_price->execute();
    $result_price = $stmt_price->get_result();

    // Check if package exists and get price
    if ($result_price->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid trip package selected.']);
        $stmt_price->close();
        return;
    }

    $row = $result_price->fetch_assoc();
    $total_price = $row['total_price'] ? floatval($row['total_price']) : 0;
    $stmt_price->close();

    $conn->begin_transaction();
    try {
        // --- 2. INSERT trip with correct bind_param ---
        $stmt = $conn->prepare("INSERT INTO trips (customer_name, tour_code, trip_package_id, start_date, end_date, status, total_price) VALUES (?, ?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }

        // ssisssd corresponds to (string, string, integer, string, string, string, double)
        $stmt->bind_param("ssisssd", $customer_name, $tour_code, $trip_package_id, $start_date, $end_date, $status, $total_price);

        if (!$stmt->execute()) {
            throw new Exception('Execute failed: ' . $stmt->error);
        }

        $trip_id = $conn->insert_id;
        $stmt->close();

        // --- Create itinerary entries for each day ---
        $current_date = new DateTime($start_date);
        $end = new DateTime($end_date);
        $end->modify('+1 day'); 
        
        // --- CORRECTED LINE ---
        $stmt_itinerary = $conn->prepare("INSERT INTO itinerary_days (trip_id, day_date, guide_id, vehicle_id, hotel_id, notes) VALUES (?, ?, NULL, NULL, NULL, '')");

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
        $stmt = $conn->prepare("UPDATE itinerary_days SET guide_id = ?, vehicle_id = ?, hotel_id = ?, notes = ?, services_provided = ? WHERE id = ?");
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }

        foreach($itinerary_days as $day) {
            $guide_id = !empty($day['guide_id']) ? intval($day['guide_id']) : null;
            $vehicle_id = !empty($day['vehicle_id']) ? intval($day['vehicle_id']) : null;
            $hotel_id = !empty($day['hotel_id']) ? intval($day['hotel_id']) : null;
            $notes = isset($day['notes']) ? trim($day['notes']) : '';
            $services_provided = isset($day['services_provided']) ? trim($day['services_provided']) : '';
            $id = intval($day['id']);

            $stmt->bind_param("iiissi", $guide_id, $vehicle_id, $hotel_id, $notes, $services_provided, $id);
            if (!$stmt->execute()) {
                throw new Exception('Execute failed: ' . $stmt->error);
            }
        }
        $stmt->close();
        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => 'Itinerary updated successfully!']);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Failed to update itinerary: ' . $e->getMessage()]);
    }
}

// ============= HOTEL MANAGEMENT =============
function getHotels($conn) {
    $result = $conn->query("SELECT id, name, room_types, availability FROM hotels ORDER BY name");
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
    $room_types = isset($_POST['room_types']) ? trim($_POST['room_types']) : '';
    $availability = isset($_POST['availability']) ? trim($_POST['availability']) : 'Available';

    if (empty($name)) {
        echo json_encode(['status' => 'error', 'message' => 'Hotel name is required.']);
        return;
    }

    $stmt = $conn->prepare("INSERT INTO hotels (name, room_types, availability) VALUES (?, ?, ?)");
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Database prepare failed: ' . $conn->error]);
        return;
    }

    $stmt->bind_param("sss", $name, $room_types, $availability);

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
    $room_types = isset($_POST['room_types']) ? trim($_POST['room_types']) : '';
    $availability = isset($_POST['availability']) ? trim($_POST['availability']) : 'Available';

    if (empty($id) || empty($name)) {
        echo json_encode(['status' => 'error', 'message' => 'Hotel ID and name are required.']);
        return;
    }

    $stmt = $conn->prepare("UPDATE hotels SET name = ?, room_types = ?, availability = ? WHERE id = ?");
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Database prepare failed: ' . $conn->error]);
        return;
    }

    $stmt->bind_param("sssi", $name, $room_types, $availability, $id);

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
    $result = $conn->query("SELECT id, name, language, availability_status FROM guides ORDER BY name");
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
    $availability_status = isset($_POST['availability_status']) ? trim($_POST['availability_status']) : 'Available';

    if (empty($name)) {
        echo json_encode(['status' => 'error', 'message' => 'Guide name is required.']);
        return;
    }

    $stmt = $conn->prepare("INSERT INTO guides (name, language, availability_status) VALUES (?, ?, ?)");
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Database prepare failed: ' . $conn->error]);
        return;
    }

    $stmt->bind_param("sss", $name, $language, $availability_status);

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
    $availability_status = isset($_POST['availability_status']) ? trim($_POST['availability_status']) : 'Available';

    if (empty($id) || empty($name)) {
        echo json_encode(['status' => 'error', 'message' => 'Guide ID and name are required.']);
        return;
    }

    $stmt = $conn->prepare("UPDATE guides SET name = ?, language = ?, availability_status = ? WHERE id = ?");
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Database prepare failed: ' . $conn->error]);
        return;
    }

    $stmt->bind_param("sssi", $name, $language, $availability_status, $id);

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

$conn->close();
?>