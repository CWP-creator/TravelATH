<?php
header('Content-Type: application/json');
include 'db_connect.php'; // Ensure you have your db connection file

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
    
    // Hotel Management
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
    
    // Vehicle Management
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
    
    // Guide Management
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
    
    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action specified.']);
        break;
}

// Function to fetch all trips
function getTrips($conn) {
    $sql = "SELECT t.id, t.customer_name, t.start_date, t.end_date, t.status, t.trip_package_id, p.name as package_name 
            FROM trips t 
            JOIN trip_packages p ON t.trip_package_id = p.id
            ORDER BY t.start_date DESC";
    $result = $conn->query($sql);
    $trips = [];
    while ($row = $result->fetch_assoc()) {
        $trips[] = $row;
    }
    echo json_encode(['status' => 'success', 'data' => $trips]);
}

// Function to fetch all trip packages
function getTripPackages($conn) {
    $result = $conn->query("SELECT id, name, description FROM trip_packages ORDER BY name");
    $packages = [];
    while ($row = $result->fetch_assoc()) {
        $packages[] = $row;
    }
    echo json_encode(['status' => 'success', 'data' => $packages]);
}

// Function to add a new trip and create its blank itinerary
function addTrip($conn) {
    $customer_name = $_POST['customer_name'];
    $trip_package_id = $_POST['trip_package_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $status = $_POST['status'];

    if (empty($customer_name) || empty($trip_package_id) || empty($start_date) || empty($end_date)) {
        echo json_encode(['status' => 'error', 'message' => 'Please fill all required fields.']);
        return;
    }
    
    if (strtotime($end_date) < strtotime($start_date)) {
        echo json_encode(['status' => 'error', 'message' => 'End date cannot be before the start date.']);
        return;
    }

    $conn->begin_transaction();
    try {
        // 1. Insert the main trip
        $stmt = $conn->prepare("INSERT INTO trips (customer_name, trip_package_id, start_date, end_date, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sisss", $customer_name, $trip_package_id, $start_date, $end_date, $status);
        $stmt->execute();
        $trip_id = $conn->insert_id;
        $stmt->close();

        // 2. Create blank itinerary days for the trip duration
        $current_date = new DateTime($start_date);
        $end = new DateTime($end_date);
        $stmt_itinerary = $conn->prepare("INSERT INTO itinerary_days (trip_id, day_date) VALUES (?, ?)");

        while ($current_date <= $end) {
            $date_str = $current_date->format('Y-m-d');
            $stmt_itinerary->bind_param("is", $trip_id, $date_str);
            $stmt_itinerary->execute();
            $current_date->modify('+1 day');
        }
        $stmt_itinerary->close();

        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => 'Trip and itinerary created successfully.']);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Failed to add trip: ' . $e->getMessage()]);
    }
}

// Function to update a trip's core details
function updateTrip($conn) {
    $id = $_POST['id'];
    $customer_name = $_POST['customer_name'];
    $trip_package_id = $_POST['trip_package_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $status = $_POST['status'];

    if (empty($id) || empty($customer_name) || empty($trip_package_id) || empty($start_date) || empty($end_date)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        return;
    }

    $stmt = $conn->prepare("UPDATE trips SET customer_name = ?, trip_package_id = ?, start_date = ?, end_date = ?, status = ? WHERE id = ?");
    $stmt->bind_param("sisssi", $customer_name, $trip_package_id, $start_date, $end_date, $status, $id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Trip updated successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update trip.']);
    }
    $stmt->close();
}

// Function to delete a trip (cascades to itinerary_days)
function deleteTrip($conn) {
    parse_str(file_get_contents("php://input"), $_DELETE);
    $id = $_DELETE['id'];

    if (empty($id)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid Trip ID.']);
        return;
    }

    $stmt = $conn->prepare("DELETE FROM trips WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Trip deleted successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete trip.']);
    }
    $stmt->close();
}

// Function to fetch itinerary details and all resources (guides, vehicles, hotels)
function getItinerary($conn) {
    $trip_id = isset($_GET['trip_id']) ? intval($_GET['trip_id']) : 0;
    if ($trip_id === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid Trip ID provided.']);
        return;
    }

    $data = [];

    // Fetch Trip Details
    $stmt_trip = $conn->prepare("SELECT t.*, p.name AS package_name FROM trips t JOIN trip_packages p ON t.trip_package_id = p.id WHERE t.id = ?");
    $stmt_trip->bind_param("i", $trip_id);
    $stmt_trip->execute();
    $data['trip'] = $stmt_trip->get_result()->fetch_assoc();
    $stmt_trip->close();

    // Fetch Itinerary Days
    $stmt_days = $conn->prepare("SELECT * FROM itinerary_days WHERE trip_id = ? ORDER BY day_date ASC");
    $stmt_days->bind_param("i", $trip_id);
    $stmt_days->execute();
    $result_days = $stmt_days->get_result();
    $data['itinerary_days'] = [];
    while($row = $result_days->fetch_assoc()) {
        $data['itinerary_days'][] = $row;
    }
    $stmt_days->close();

    // Fetch all resources
    $data['guides'] = $conn->query("SELECT id, name FROM guides ORDER BY name")->fetch_all(MYSQLI_ASSOC);
    $data['vehicles'] = $conn->query("SELECT id, name FROM vehicles ORDER BY name")->fetch_all(MYSQLI_ASSOC);
    $data['hotels'] = $conn->query("SELECT id, name, location FROM hotels ORDER BY name")->fetch_all(MYSQLI_ASSOC);

    echo json_encode(['status' => 'success', 'data' => $data]);
}

// Function to update an entire itinerary in one transaction
function updateItinerary($conn) {
    $input = json_decode(file_get_contents('php://input'), true);
    $itinerary_days = $input['itinerary_days'];

    if (empty($itinerary_days) || !is_array($itinerary_days)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid data provided.']);
        return;
    }

    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("UPDATE itinerary_days SET guide_id = ?, vehicle_id = ?, hotel_id = ?, notes = ? WHERE id = ?");
        foreach($itinerary_days as $day) {
            $guide_id = !empty($day['guide_id']) ? $day['guide_id'] : null;
            $vehicle_id = !empty($day['vehicle_id']) ? $day['vehicle_id'] : null;
            $hotel_id = !empty($day['hotel_id']) ? $day['hotel_id'] : null;
            $notes = $day['notes'];
            $id = $day['id'];
            $stmt->bind_param("iiisi", $guide_id, $vehicle_id, $hotel_id, $notes, $id);
            $stmt->execute();
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
    $result = $conn->query("SELECT id, name, location FROM hotels ORDER BY name");
    $hotels = [];
    while ($row = $result->fetch_assoc()) {
        $hotels[] = $row;
    }
    echo json_encode(['status' => 'success', 'data' => $hotels]);
}

function addHotel($conn) {
    $name = $_POST['name'];
    $location = $_POST['location'];

    if (empty($name)) {
        echo json_encode(['status' => 'error', 'message' => 'Hotel name is required.']);
        return;
    }

    $stmt = $conn->prepare("INSERT INTO hotels (name, location) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $location);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Hotel added successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add hotel.']);
    }
    $stmt->close();
}

function updateHotel($conn) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $location = $_POST['location'];

    if (empty($id) || empty($name)) {
        echo json_encode(['status' => 'error', 'message' => 'Hotel ID and name are required.']);
        return;
    }

    $stmt = $conn->prepare("UPDATE hotels SET name = ?, location = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $location, $id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Hotel updated successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update hotel.']);
    }
    $stmt->close();
}

function deleteHotel($conn) {
    parse_str(file_get_contents("php://input"), $_DELETE);
    $id = $_DELETE['id'];

    if (empty($id)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid Hotel ID.']);
        return;
    }

    $stmt = $conn->prepare("DELETE FROM hotels WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Hotel deleted successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete hotel.']);
    }
    $stmt->close();
}

// ============= VEHICLE MANAGEMENT =============

function getVehicles($conn) {
    $result = $conn->query("SELECT id, name FROM vehicles ORDER BY name");
    $vehicles = [];
    while ($row = $result->fetch_assoc()) {
        $vehicles[] = $row;
    }
    echo json_encode(['status' => 'success', 'data' => $vehicles]);
}

function addVehicle($conn) {
    $name = $_POST['name'];

    if (empty($name)) {
        echo json_encode(['status' => 'error', 'message' => 'Vehicle name is required.']);
        return;
    }

    $stmt = $conn->prepare("INSERT INTO vehicles (name) VALUES (?)");
    $stmt->bind_param("s", $name);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Vehicle added successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add vehicle.']);
    }
    $stmt->close();
}

function updateVehicle($conn) {
    $id = $_POST['id'];
    $name = $_POST['name'];

    if (empty($id) || empty($name)) {
        echo json_encode(['status' => 'error', 'message' => 'Vehicle ID and name are required.']);
        return;
    }

    $stmt = $conn->prepare("UPDATE vehicles SET name = ? WHERE id = ?");
    $stmt->bind_param("si", $name, $id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Vehicle updated successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update vehicle.']);
    }
    $stmt->close();
}

function deleteVehicle($conn) {
    parse_str(file_get_contents("php://input"), $_DELETE);
    $id = $_DELETE['id'];

    if (empty($id)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid Vehicle ID.']);
        return;
    }

    $stmt = $conn->prepare("DELETE FROM vehicles WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Vehicle deleted successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete vehicle.']);
    }
    $stmt->close();
}

// ============= GUIDE MANAGEMENT =============

function getGuides($conn) {
    $result = $conn->query("SELECT id, name FROM guides ORDER BY name");
    $guides = [];
    while ($row = $result->fetch_assoc()) {
        $guides[] = $row;
    }
    echo json_encode(['status' => 'success', 'data' => $guides]);
}

function addGuide($conn) {
    $name = $_POST['name'];

    if (empty($name)) {
        echo json_encode(['status' => 'error', 'message' => 'Guide name is required.']);
        return;
    }

    $stmt = $conn->prepare("INSERT INTO guides (name) VALUES (?)");
    $stmt->bind_param("s", $name);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Guide added successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add guide.']);
    }
    $stmt->close();
}

function updateGuide($conn) {
    $id = $_POST['id'];
    $name = $_POST['name'];

    if (empty($id) || empty($name)) {
        echo json_encode(['status' => 'error', 'message' => 'Guide ID and name are required.']);
        return;
    }

    $stmt = $conn->prepare("UPDATE guides SET name = ? WHERE id = ?");
    $stmt->bind_param("si", $name, $id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Guide updated successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update guide.']);
    }
    $stmt->close();
}

function deleteGuide($conn) {
    parse_str(file_get_contents("php://input"), $_DELETE);
    $id = $_DELETE['id'];

    if (empty($id)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid Guide ID.']);
        return;
    }

    $stmt = $conn->prepare("DELETE FROM guides WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Guide deleted successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete guide.']);
    }
    $stmt->close();
}

$conn->close();
?>