<?php require_once '../utils/check_session.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Agency Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4A90E2;
            --background-color: #f0f2f5;
            --sidebar-bg: #ffffff;
            --header-bg: #3678c5;
            --card-background: #ffffff;
            --text-color: #333;
            --text-light: #888;
            --border-color: #e0e0e0;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);

            --card1-bg: linear-gradient(to right, #6a82fb, #8996f6);
            --card2-bg: linear-gradient(to right, #f76b8a, #f98e9f);
            --card3-bg: linear-gradient(to right, #4facfe, #00f2fe);
            --card4-bg: linear-gradient(to right, #43e97b, #38f9d7);

            --success-color: #4CAF50;
            --error-color: #F44336;
            --warning-color: #f5a623;

            --row-available-bg: #f0fff0;
            --row-not-available-bg: #fff5f5;
            --row-on-trip-bg: #f5f5ff;
        }

        body {
            font-family: 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
            margin: 0;
            background-color: var(--background-color);
            color: var(--text-color);
            display: flex;
        }

        .sidebar {
            width: 240px;
            background: var(--sidebar-bg);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            display: flex;
            flex-direction: column;
            border-right: 1px solid var(--border-color);
            transition: width 0.3s;
        }

        .sidebar-header {
            padding: 20px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary-color);
            border-bottom: 1px solid var(--border-color);
        }
        
        .sidebar-nav {
            list-style: none;
            padding: 20px 0;
            margin: 0;
            flex-grow: 1;
        }

        .sidebar-nav li a {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 20px;
            text-decoration: none;
            color: var(--text-light);
            font-weight: 500;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .sidebar-nav li a:hover, .sidebar-nav li.active a {
            background-color: #e8f0fe;
            color: var(--primary-color);
        }

        .sidebar-nav li a .fa-fw {
            width: 20px;
        }
        
        .page-wrapper {
            margin-left: 240px;
            width: calc(100% - 240px);
            display: flex;
            flex-direction: column;
            transition: all 0.3s;
        }

        .top-header {
            background-color: var(--header-bg);
            color: white;
            padding: 0 30px;
            height: 72px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header-title {
            font-size: 1.4rem;
            font-weight: 500;
        }
        
        .header-stats {
            display: flex;
            gap: 20px;
        }
        
        .stat-item {
            text-align: right;
        }

        .stat-item .value {
            font-size: 1.2rem;
            font-weight: bold;
        }

        .stat-item .label {
            font-size: 0.8rem;
            opacity: 0.8;
        }

        .main-content {
            flex-grow: 1;
            padding: 30px;
            overflow-y: auto;
        }

        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: var(--shadow);
        }

        .stat-card h3 {
            margin: 0 0 5px 0;
            font-size: 1rem;
        }
        .stat-card .value {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .stat-card .detail {
            font-size: 0.85rem;
            opacity: 0.9;
        }
        
        .card-1 { background: var(--card1-bg); }
        .card-2 { background: var(--card2-bg); }
        .card-3 { background: var(--card3-bg); }
        .card-4 { background: var(--card4-bg); }

        .content-section {
            display: none;
        }
        
        .content-section.active {
            display: block;
        }

        .trips-container {
            background-color: var(--card-background);
            padding: 20px;
            border-radius: 8px;
            box-shadow: var(--shadow);
        }
        
        .trips-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .trips-header h2 {
            margin: 0;
            font-size: 1.2rem;
        }

        .btn-add {
            background-color: var(--primary-color);
            padding: 10px 15px;
            border-radius: 5px;
            color: white;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            border: none;
        }
        .btn-add:hover { background-color: #357ABD; }

        .table-container { overflow-x: auto; }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }
        th {
            background-color: #f8f9fa;
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text-light);
        }

        tr {
            transition: background-color 0.3s ease;
        }

        tr:last-child td { border-bottom: none; }
        
        .status {
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 0.8em;
            text-align: center;
            font-weight: 500;
        }
        .status-Active { background-color: #e0f8e9; color: #4CAF50; }
        .status-Completed { background-color: #e9ecef; color: #6c757d; }
        .status-Pending { background-color: #fff3e0; color: #ff9800; }

        .row-status-Available { background-color: var(--row-available-bg); }
        .row-status-Not-Available { background-color: var(--row-not-available-bg); }
        .row-status-On-Trip { background-color: var(--row-on-trip-bg); }

        .actions a {
            color: var(--text-light);
            margin: 0 8px;
            font-size: 1rem;
            text-decoration: none;
            cursor: pointer;
        }

        .actions .fa-route { color: var(--primary-color); }
        .actions .fa-pencil { color: var(--warning-color); }
        .actions .fa-trash { color: var(--error-color); }
        
        .modal {
            display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); animation: fadeIn 0.3s;
        }
        .modal-content {
            background-color: #fefefe; margin: 5% auto; padding: 25px; border: 1px solid #888; width: 90%; max-width: 500px; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); animation: slideIn 0.3s;
        }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideIn { from { transform: translateY(-50px); } to { transform: translateY(0); } }
        .close-btn {
            color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;
        }
        .modal-content h2 {
            margin-top: 0; color: var(--primary-color);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block; margin-bottom: 5px; font-weight: bold;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 5px; box-sizing: border-box;
        }
        .form-buttons {
            display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;
        }
        button, .btn {
            padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; color: white; font-weight: bold; transition: background-color 0.3s ease;
        }
        .btn-save { background-color: var(--success-color); }
        .btn-cancel { background-color: #aaa; }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .services-block {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            background: #f9f9f9;
        }

        .services-block label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            margin: 8px 0;
            font-weight: normal;
        }

        .services-block input[type="checkbox"] {
            width: auto;
            cursor: pointer;
        }

        .services-display {
            display: inline-block;
            background: #e3f2fd;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.85em;
            font-weight: 500;
            color: #1976d2;
        }

        #hotel_assignments_container {
            max-height: 250px;
            overflow-y: auto;
            border-top: 1px solid var(--border-color);
            margin-top: 15px;
            padding-top: 10px;
        }

        .toast {
            visibility: hidden; min-width: 250px; margin-left: -125px; background-color: #333; color: #fff; text-align: center; border-radius: 5px; padding: 16px; position: fixed; z-index: 1001; left: 50%; bottom: 30px; font-size: 17px; opacity: 0; transition: opacity 0.3s, visibility 0.3s, bottom 0.3s;
        }
        .toast.show {
            visibility: visible; opacity: 1;
        }
        .toast.success { background-color: var(--success-color); }
        .toast.error { background-color: var(--error-color); }

        @media screen and (max-width: 992px) {
            .sidebar {
                width: 70px;
            }
            .sidebar-header {
                padding: 20px 0;
                font-size: 1.2rem;
            }
            .sidebar-header .full-text { display: none; }
            .sidebar-nav li a {
                justify-content: center;
            }
            .sidebar-nav li a .link-text { display: none; }

            .page-wrapper {
                width: calc(100% - 70px);
                margin-left: 70px;
            }
        }
        @media screen and (max-width: 768px) {
            body { flex-direction: column; }
            .sidebar {
                width: 100%;
                height: auto;
                position: static;
                flex-direction: row;
                justify-content: space-around;
                border-right: none;
                border-bottom: 1px solid var(--border-color);
            }
            .sidebar-header { display: none; }
            .sidebar-nav { display: flex; flex-grow: 1; justify-content: center; }
            .sidebar-nav li a { padding: 15px; }

            .page-wrapper {
                width: 100%;
                margin-left: 0;
            }
            .top-header { height: auto; flex-direction: column; padding: 15px; gap: 15px; }
            .header-stats { gap: 15px; }
            .main-content { padding: 15px; }
            .stats-cards { gap: 15px; }
        }

        @media screen and (max-width: 576px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <aside class="sidebar">
        <div class="sidebar-header"><span class="full-text">ATH</span></div>

        <ul class="sidebar-nav">
            <li class="active"><a data-section="dashboard"><i class="fas fa-tachometer-alt fa-fw"></i> <span class="link-text">Dashboard</span></a></li>
            <li><a data-section="trips"><i class="fas fa-file-alt fa-fw"></i> <span class="link-text">Trip Files</span></a></li>
            <li><a data-section="packages"><i class="fas fa-box-open fa-fw"></i> <span class="link-text">Packages</span></a></li>
            <li><a data-section="hotels"><i class="fas fa-hotel fa-fw"></i> <span class="link-text">Hotels</span></a></li>
            <li><a data-section="vehicles"><i class="fas fa-car fa-fw"></i> <span class="link-text">Vehicles</span></a></li>
            <li><a data-section="guides"><i class="fas fa-user-friends fa-fw"></i> <span class="link-text">Guides</span></a></li>
        </ul>
        <ul class="sidebar-nav">
    <li style="margin-top: auto; border-top: 1px solid var(--border-color); padding-top: 10px;">
        <a href="#" onclick="logout(); return false;">
            <i class="fas fa-sign-out-alt fa-fw"></i> 
            <span class="link-text">Logout</span>
        </a>
    </li>
</ul>
    </aside>

    <div class="page-wrapper">
        <header class="top-header">
            <div class="header-title">Travel Agency Management System</div>
            <div class="header-stats">
                <div class="stat-item">
                    <div class="value" id="totalTripsStat">0</div>
                    <div class="label">Total Trips</div>
                </div>
                <div class="stat-item">
                    <div class="value" id="activeTripsStat">0</div>
                    <div class="label">Active</div>
                </div>
                <div class="stat-item">
                    <div class="value">$0.00</div>
                    <div class="label">Revenue</div>
                </div>
            </div>
        </header>

        <main class="main-content">
            <section id="dashboardSection" class="content-section active">
                <div class="stats-cards">
                    <div class="stat-card card-1">
                        <h3>This Month's Bookings</h3>
                        <div class="value" id="monthlyBookings">0</div>
                        <div class="detail"><i class="fas fa-arrow-up"></i> 0% from last month</div>
                    </div>
                    <div class="stat-card card-2">
                        <h3>Total Customers</h3>
                        <div class="value" id="totalCustomers">0</div>
                        <div class="detail"><i class="fas fa-arrow-up"></i> 0% growth</div>
                    </div>
                    <div class="stat-card card-3">
                        <h3>Available Vehicles</h3>
                        <div class="value" id="vehicleCount">0</div>
                        <div class="detail">Fleet overview</div>
                    </div>
                    <div class="stat-card card-4">
                        <h3>Active Guides</h3>
                        <div class="value" id="guideCount">0</div>
                        <div class="detail">Tour guides</div>
                    </div>
                </div>

                <div class="trips-container">
                    <div class="trips-header">
                        <h2>Recent Trip Files</h2>
                        <button id="addTripBtn" class="btn-add"><i class="fas fa-plus"></i> New File</button>
                    </div>
                    <div class="table-container">
                        <table id="tripsTable">
                            <thead>
                                <tr>
                                    <th>FILE ID</th>
                                    <th>CUSTOMER</th>
                                    <th>TOUR CODE</th>
                                    <th>PACKAGE</th>
                                    <th>START DATE</th>
                                    <th>END DATE</th>
                                    <th>STATUS</th>
                                    <th>ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </section>

            <section id="tripsSection" class="content-section">
                <div class="trips-container">
                    <div class="trips-header">
                        <h2>All Trips</h2>
                        <button id="addTripBtn2" class="btn-add"><i class="fas fa-plus"></i> New Trip</button>
                    </div>
                    <div class="table-container">
                        <table id="allTripsTable">
                            <thead>
                                <tr>
                                    <th>File ID</th>
                                    <th>CUSTOMER</th>
                                    <th>TOUR CODE</th>
                                    <th>PACKAGE</th>
                                    <th>START DATE</th>
                                    <th>END DATE</th>
                                    <th>STATUS</th>
                                    <th>ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </section>

            <section id="packagesSection" class="content-section">
                <div class="trips-container">
                    <div class="trips-header">
                        <h2>Trip Packages Management</h2>
                        <button id="addPackageBtn" class="btn-add"><i class="fas fa-plus"></i> Add Package</button>
                    </div>
                    <div class="table-container">
                        <table id="packagesTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>PACKAGE NAME</th>
                                    <th>CODE</th>
                                    <th>DAYS</th>
                                    <th>ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </section>

            <section id="hotelsSection" class="content-section">
                <div class="trips-container">
                    <div class="trips-header">
                        <h2>Hotels Management</h2>
                        <button id="addHotelBtn" class="btn-add"><i class="fas fa-plus"></i> Add Hotel</button>
                    </div>
                    <div class="table-container">
                        <table id="hotelsTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>HOTEL NAME</th>
                                    <th>ROOM TYPES</th>
                                    <th>SERVICES PROVIDED</th>
                                    <th>AVAILABILITY</th>
                                    <th>ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </section>

            <section id="vehiclesSection" class="content-section">
                <div class="trips-container">
                    <div class="trips-header">
                        <h2>Vehicles Management</h2>
                        <button id="addVehicleBtn" class="btn-add"><i class="fas fa-plus"></i> Add Vehicle</button>
                    </div>
                    <div class="table-container">
                        <table id="vehiclesTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>VEHICLE NAME</th>
                                    <th>CAPACITY</th>
                                    <th>AVAILABILITY</th>
                                    <th>ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </section>

            <section id="guidesSection" class="content-section">
                <div class="trips-container">
                    <div class="trips-header">
                        <h2>Tour Guides Management</h2>
                        <button id="addGuideBtn" class="btn-add"><i class="fas fa-plus"></i> Add Guide</button>
                    </div>
                    <div class="table-container">
                        <table id="guidesTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>GUIDE NAME</th>
                                    <th>LANGUAGE</th>
                                    <th>AVAILABILITY</th>
                                    <th>ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <div id="tripModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" data-modal="tripModal">&times;</span>
            <h2 id="modalTitle">Add Trip</h2>
            <form id="tripForm">
                <input type="hidden" id="tripIdHidden" name="id">

                <div id="fileIdGroup" class="form-group" style="display: none;">
                    <label for="tripIdDisplay">File ID</label>
                    <input type="text" id="tripIdDisplay" readonly style="font-weight: bold; background-color: #f8f9fa;">
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="customer_name">Customer Name</label>
                        <input type="text" id="customer_name" name="customer_name" required>
                    </div>
                    <div class="form-group">
                        <label for="tour_code">Tour Code</label>
                        <input type="text" id="tour_code" name="tour_code">
                    </div>
                </div>

                <div class="form-group">
                    <label for="trip_package_id">Trip Package</label>
                    <select id="trip_package_id" name="trip_package_id" required>
                        <option value="">Select Package</option>
                    </select>
                </div>

                <div id="package_description_container" class="form-group" style="display: none;">
                    <label>Package Details</label>
                    <div id="package_description"></div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="start_date">Start Date</label>
                        <input type="date" id="start_date" name="start_date" required>
                    </div>
                    <div class="form-group">
                        <label for="end_date">End Date</label>
                        <input type="date" id="end_date" name="end_date" required>
                        <small id="end_date_suggestion" style="display: block; margin-top: 5px; color: var(--primary-color); font-weight: 500;"></small>
                    </div>
                </div>
                

                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="Pending">Pending</option>
                        <option value="Active">Active</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>
                <div class="form-buttons">
                    <button type="button" class="btn-cancel btn" data-modal="tripModal">Cancel</button>
                    <button type="submit" class="btn-save btn">Save Trip</button>
                </div>
            </form>
        </div>
    </div>

    <div id="packageModal" class="modal">
        <div class="modal-content" style="max-width: 600px;">
            <span class="close-btn" data-modal="packageModal">&times;</span>
            <h2 id="packageModalTitle">Add Package</h2>
            <form id="packageForm">
                <input type="hidden" id="packageId" name="id">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="package_name">Package Name</label>
                        <input type="text" id="package_name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="package_code">Package Code</label>
                        <input type="text" id="package_code" name="code" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="package_days">Number of Days</label>
                    <input type="number" id="package_days" name="No_of_Days" min="1" required>
                </div>
                
                <div id="hotel_assignments_container" class="form-group">
                    </div>

                <div class="form-buttons">
                    <button type="button" class="btn-cancel btn" data-modal="packageModal">Cancel</button>
                    <button type="submit" class="btn-save btn">Save Package</button>
                </div>
            </form>
        </div>
    </div>

    <div id="hotelModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" data-modal="hotelModal">&times;</span>
            <h2 id="hotelModalTitle">Add Hotel</h2>
            <form id="hotelForm">
                <input type="hidden" id="hotelId" name="id">
                
                <div class="form-group">
                    <label for="hotel_name">Hotel Name</label>
                    <input type="text" id="hotel_name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="hotel_room_types">Room Types</label>
                    <input type="text" id="hotel_room_types" name="room_types" placeholder="e.g., Suite, Double, Single">
                </div>

                <div class="form-group">
                    <label>Services Provided</label>
                    <div class="services-block">
                        <label>
                            <input type="checkbox" name="service_breakfast" value="B"> 
                            <span>Breakfast (B)</span>
                        </label>
                        <label>
                            <input type="checkbox" name="service_lunch" value="L"> 
                            <span>Lunch (L)</span>
                        </label>
                        <label>
                            <input type="checkbox" name="service_dinner" value="D"> 
                            <span>Dinner (D)</span>
                        </label>
                        <small style="color: #666; display: block; margin-top: 8px;">Select meals provided by this hotel</small>
                        <input type="hidden" id="hotel_services_provided" name="services_provided">
                    </div>
                </div>

                <div class="form-group">
                    <label for="hotel_availability">Availability Status</label>
                    <select id="hotel_availability" name="availability" required>
                        <option value="Available">Available</option>
                        <option value="Not Available">Not Available</option>
                    </select>
                </div>
                
                <div class="form-buttons">
                    <button type="button" class="btn-cancel btn" data-modal="hotelModal">Cancel</button>
                    <button type="submit" class="btn-save btn">Save Hotel</button>
                </div>
            </form>
        </div>
    </div>

    <div id="vehicleModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" data-modal="vehicleModal">&times;</span>
            <h2 id="vehicleModalTitle">Add Vehicle</h2>
            <form id="vehicleForm">
                <input type="hidden" id="vehicleId" name="id">
                <div class="form-group">
                    <label for="vehicle_name">Vehicle Name</label>
                    <input type="text" id="vehicle_name" name="vehicle_name" required>
                </div>
                <div class="form-group">
                    <label for="vehicle_capacity">Capacity</label>
                    <input type="number" id="vehicle_capacity" name="capacity" min="1" required>
                </div>
                <div class="form-group">
                    <label for="vehicle_availability">Availability Status</label>
                    <select id="vehicle_availability" name="availability" required>
                        <option value="Available">Available</option>
                        <option value="Not Available">Not Available</option>
                        <option value="On Trip">On Trip</option>
                    </select>
                </div>
                <div class="form-buttons">
                    <button type="button" class="btn-cancel btn" data-modal="vehicleModal">Cancel</button>
                    <button type="submit" class="btn-save btn">Save Vehicle</button>
                </div>
            </form>
        </div>
    </div>

    <div id="guideModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" data-modal="guideModal">&times;</span>
            <h2 id="guideModalTitle">Add Guide</h2>
            <form id="guideForm">
                <input type="hidden" id="guideId" name="id">
                <div class="form-group">
                    <label for="guide_name">Guide Name</label>
                    <input type="text" id="guide_name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="guide_language">Language</label>
                    <input type="text" id="guide_language" name="language">
                </div>
                <div class="form-group">
                    <label for="guide_availability">Availability Status</label>
                    <select id="guide_availability" name="availability_status" required>
                        <option value="Available">Available</option>
                        <option value="Not Available">Not Available</option>
                        <option value="On Trip">On Trip</option>
                    </select>
                </div>
                <div class="form-buttons">
                    <button type="button" class="btn-cancel btn" data-modal="guideModal">Cancel</button>
                    <button type="submit" class="btn-save btn">Save Guide</button>
                </div>
            </form>
        </div>
    </div>
    
    <div id="toast" class="toast"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const API_URL = 'api/api.php';

            let tripsData = [];
            let hotelsData = [];
            let vehiclesData = [];
            let guidesData = [];
            let packagesData = [];

    async function logout() {
    if (confirm('Are you sure you want to logout?')) {
        try {
            const response = await fetch('../utils/auth.php?action=logout');
            const result = await response.json();
            if (result.status === 'success') {
                window.location.href = '../login.html';
            }
        } catch (error) {
            console.error('Logout error:', error);
            window.location.href = '../login.html';
        }
    }
}

            // Navigation
            document.querySelectorAll('.sidebar-nav a').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const section = this.dataset.section;
                    if (!section) return;
                    
                    document.querySelectorAll('.sidebar-nav li').forEach(li => li.classList.remove('active'));
                    this.parentElement.classList.add('active');
                    
                    document.querySelectorAll('.content-section').forEach(sec => sec.classList.remove('active'));
                    document.getElementById(section + 'Section').classList.add('active');
                    
                    switch(section) {
                        case 'trips':
                            renderTrips(tripsData, document.querySelector('#allTripsTable tbody'));
                            break;
                        case 'packages':
                            fetchPackages();
                            break;
                        case 'hotels':
                            fetchHotels();
                            break;
                        case 'vehicles':
                            fetchVehicles();
                            break;
                        case 'guides':
                            fetchGuides();
                            break;
                    }
                });
            });
            
            const updateStats = (trips) => {
                const totalTrips = trips.length;
                const activeTrips = trips.filter(t => t.status === 'Active').length;
                
                document.getElementById('totalTripsStat').textContent = totalTrips;
                document.getElementById('activeTripsStat').textContent = activeTrips;
                document.getElementById('monthlyBookings').textContent = totalTrips; 
                
                const uniqueCustomers = [...new Set(trips.map(item => item.customer_name))].length;
                document.getElementById('totalCustomers').textContent = uniqueCustomers;
            };

            const fetchTrips = async () => {
                try {
                    const response = await fetch(`${API_URL}?action=getTrips`);
                    const result = await response.json();
                    if (result.status === 'success') {
                        tripsData = result.data;
                        renderTrips(tripsData, document.querySelector('#tripsTable tbody'));
                        renderTrips(tripsData, document.querySelector('#allTripsTable tbody'));
                        updateStats(tripsData);
                    } else {
                        showToast(result.message, 'error');
                    }
                } catch (error) {
                    showToast('Error fetching trips.', 'error');
                }
            };
            
            const fetchPackages = async () => {
                try {
                    const response = await fetch(`${API_URL}?action=getTripPackages`);
                    const result = await response.json();
                    if (result.status === 'success') {
                        packagesData = result.data;
                        renderPackages(packagesData);
                        const select = document.getElementById('trip_package_id');
                        select.innerHTML = '<option value="">Select Package</option>';
                        result.data.forEach(pkg => {
                            const days = pkg.No_of_Days ? ` (${pkg.No_of_Days} Days)` : '';
                            select.innerHTML += `<option value="${pkg.id}" data-description="${pkg.description || ''}" data-days="${pkg.No_of_Days || ''}">${pkg.name}${days}</option>`;
                        });
                    }
                } catch (error) {
                    showToast('Error fetching packages.', 'error');
                }
            };

            const fetchHotels = async () => {
                try {
                    const response = await fetch(`${API_URL}?action=getHotels`);
                    const result = await response.json();
                    if (result.status === 'success') {
                        hotelsData = result.data;
                        renderHotels(result.data);
                    }
                } catch (error) {
                    showToast('Error fetching hotels.', 'error');
                }
            };

            const fetchVehicles = async () => {
                try {
                    const response = await fetch(`${API_URL}?action=getVehicles`);
                    const result = await response.json();
                    if (result.status === 'success') {
                        vehiclesData = result.data;
                        renderVehicles(result.data);
                        document.getElementById('vehicleCount').textContent = result.data.length;
                    }
                } catch (error) {
                    showToast('Error fetching vehicles.', 'error');
                }
            };

            const fetchGuides = async () => {
                try {
                    const response = await fetch(`${API_URL}?action=getGuides`);
                    const result = await response.json();
                    if (result.status === 'success') {
                        guidesData = result.data;
                        renderGuides(result.data);
                        document.getElementById('guideCount').textContent = result.data.length;
                    }
                } catch (error) {
                    showToast('Error fetching guides.', 'error');
                }
            };

            // --- UI Rendering ---
            const renderTrips = (trips, tbody) => {
                tbody.innerHTML = '';
                if (!trips || trips.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;">No trips found.</td></tr>';
                    return;
                }
                trips.forEach(trip => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>#${String(trip.id).padStart(3, '0')}</td>
                        <td>${trip.customer_name}</td>
                        <td>${trip.tour_code || 'N/A'}</td>
                        <td>${trip.package_name}</td>
                        <td>${trip.start_date}</td>
                        <td>${trip.end_date}</td>
                        <td><span class="status status-${trip.status}">${trip.status}</span></td>
                        <td class="actions">
                            <a href="Itinerary.php?trip_id=${trip.id}" title="View Itinerary"><i class="fas fa-route"></i></a>
                            <a href="#" class="btn-edit-trip" data-id="${trip.id}"><i class="fas fa-pencil"></i></a>
                            <a href="#" class="btn-delete-trip" data-id="${trip.id}"><i class="fas fa-trash"></i></a>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            };

            const renderPackages = (packages) => {
                const tbody = document.querySelector('#packagesTable tbody');
                tbody.innerHTML = '';
                if (!packages || packages.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">No packages found.</td></tr>';
                    return;
                }
                packages.forEach(pkg => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${pkg.id}</td>
                        <td>${pkg.name}</td>
                        <td>${pkg.code || 'N/A'}</td>
                        <td>${pkg.No_of_Days || 'N/A'}</td>
                        <td class="actions">
                            <a href="#" class="btn-edit-package" data-id="${pkg.id}"><i class="fas fa-pencil"></i></a>
                            <a href="#" class="btn-delete-package" data-id="${pkg.id}"><i class="fas fa-trash"></i></a>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            };

            const renderHotels = (hotels) => {
                const tbody = document.querySelector('#hotelsTable tbody');
                tbody.innerHTML = '';
                if (!hotels || hotels.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">No hotels found.</td></tr>';
                    return;
                }
                hotels.forEach(hotel => {
                    const row = document.createElement('tr');
                    if (hotel.availability) {
                        const statusClass = `row-status-${hotel.availability.replace(/\s+/g, '-')}`;
                        row.classList.add(statusClass);
                    }
                    row.innerHTML = `
                        <td>${hotel.id}</td>
                        <td>${hotel.name}</td>
                        <td>${hotel.room_types || 'N/A'}</td>
                        <td>
                            <span class="services-display">
                                ${hotel.services_provided || 'N/A'}
                            </span>
                        </td>
                        <td>${hotel.availability || 'N/A'}</td>
                        <td class="actions">
                            <a href="#" class="btn-edit-hotel" data-id="${hotel.id}"><i class="fas fa-pencil"></i></a>
                            <a href="#" class="btn-delete-hotel" data-id="${hotel.id}"><i class="fas fa-trash"></i></a>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            };

            const renderVehicles = (vehicles) => {
                const tbody = document.querySelector('#vehiclesTable tbody');
                tbody.innerHTML = '';
                if (!vehicles || vehicles.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">No vehicles found.</td></tr>';
                    return;
                }
                vehicles.forEach(vehicle => {
                    const row = document.createElement('tr');
                    if (vehicle.availability) {
                        const statusClass = `row-status-${vehicle.availability.replace(/\s+/g, '-')}`;
                        row.classList.add(statusClass);
                    }
                    row.innerHTML = `
                        <td>${vehicle.id}</td>
                        <td>${vehicle.vehicle_name}</td>
                        <td>${vehicle.capacity || 'N/A'}</td>
                        <td>${vehicle.availability || 'N/A'}</td>
                        <td class="actions">
                            <a href="#" class="btn-edit-vehicle" data-id="${vehicle.id}"><i class="fas fa-pencil"></i></a>
                            <a href="#" class="btn-delete-vehicle" data-id="${vehicle.id}"><i class="fas fa-trash"></i></a>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            };
            
            const renderGuides = (guides) => {
                const tbody = document.querySelector('#guidesTable tbody');
                tbody.innerHTML = '';
                if (!guides || guides.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">No guides found.</td></tr>';
                    return;
                }
                guides.forEach(guide => {
                    const row = document.createElement('tr');
                    if (guide.availability_status) {
                        const statusClass = `row-status-${guide.availability_status.replace(/\s+/g, '-')}`;
                        row.classList.add(statusClass);
                    }
                    row.innerHTML = `
                        <td>${guide.id}</td>
                        <td>${guide.name}</td>
                        <td>${guide.language || 'N/A'}</td>
                        <td>${guide.availability_status || 'N/A'}</td>
                        <td class="actions">
                            <a href="#" class="btn-edit-guide" data-id="${guide.id}"><i class="fas fa-pencil"></i></a>
                            <a href="#" class="btn-delete-guide" data-id="${guide.id}"><i class="fas fa-trash"></i></a>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            };
            
            const calculateEndDate = () => {
                const packageSelect = document.getElementById('trip_package_id');
                const startDateInput = document.getElementById('start_date');
                const endDateInput = document.getElementById('end_date');
                const suggestionEl = document.getElementById('end_date_suggestion');

                const selectedOption = packageSelect.options[packageSelect.selectedIndex];
                const days = selectedOption.dataset.days;
                const startDateValue = startDateInput.value;

                suggestionEl.textContent = '';

                if (days && startDateValue) {
                    const duration = parseInt(days, 10);
                    if (isNaN(duration)) return;
                    const startDate = new Date(startDateValue);
                    
                    const endDate = new Date(startDate.getTime());
                    endDate.setDate(endDate.getDate() + duration - 1);
                    
                    const year = endDate.getFullYear();
                    const month = String(endDate.getMonth() + 1).padStart(2, '0');
                    const day = String(endDate.getDate()).padStart(2, '0');
                    
                    const formattedEndDate = `${year}-${month}-${day}`;
                    
                    endDateInput.value = formattedEndDate;
                    suggestionEl.textContent = `End date auto-calculated for ${duration} days.`;
                }
            };

            document.getElementById('trip_package_id').addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const description = selectedOption.dataset.description;
                const descriptionContainer = document.getElementById('package_description_container');
                const descriptionBox = document.getElementById('package_description');

                if (description && description.trim() !== '') {
                    descriptionBox.textContent = description;
                    descriptionContainer.style.display = 'block';
                } else {
                    descriptionContainer.style.display = 'none';
                }
                
                calculateEndDate();
            });

            document.getElementById('start_date').addEventListener('change', calculateEndDate);
            
            // --- Modal Handling ---
            const openModal = (modalId) => {
                document.getElementById(modalId).style.display = 'block';
            };

            const closeModal = (modalId) => {
                document.getElementById(modalId).style.display = 'none';
            };

            document.querySelectorAll('.close-btn, .btn-cancel').forEach(btn => {
                btn.addEventListener('click', function() {
                    closeModal(this.dataset.modal);
                });
            });

            window.addEventListener('click', (event) => {
                if (event.target.classList.contains('modal')) {
                    closeModal(event.target.id);
                }
            });

            document.getElementById('addTripBtn').addEventListener('click', () => {
                document.getElementById('tripForm').reset();
                document.getElementById('tripIdHidden').value = '';
                document.getElementById('tripIdDisplay').value = '';
                document.getElementById('fileIdGroup').style.display = 'none';
                document.getElementById('modalTitle').textContent = 'Add Trip';
                
                document.getElementById('package_description_container').style.display = 'none';
                document.getElementById('end_date_suggestion').textContent = '';

                openModal('tripModal');
            });
            document.getElementById('addTripBtn2').addEventListener('click', () => document.getElementById('addTripBtn').click());

            document.getElementById('addPackageBtn').addEventListener('click', () => {
                document.getElementById('packageForm').reset();
                document.getElementById('packageId').value = '';
                document.getElementById('packageModalTitle').textContent = 'Add Package';
                document.getElementById('hotel_assignments_container').innerHTML = '';
                openModal('packageModal');
            });

            document.getElementById('addHotelBtn').addEventListener('click', () => {
                document.getElementById('hotelForm').reset();
                document.getElementById('hotelId').value = '';
                document.getElementById('hotelModalTitle').textContent = 'Add Hotel';
                document.getElementById('hotel_services_provided').value = '';
                openModal('hotelModal');
            });

            document.getElementById('addVehicleBtn').addEventListener('click', () => {
                document.getElementById('vehicleForm').reset();
                document.getElementById('vehicleId').value = '';
                document.getElementById('vehicleModalTitle').textContent = 'Add Vehicle';
                openModal('vehicleModal');
            });

            document.getElementById('addGuideBtn').addEventListener('click', () => {
                document.getElementById('guideForm').reset();
                document.getElementById('guideId').value = '';
                document.getElementById('guideModalTitle').textContent = 'Add Guide';
                openModal('guideModal');
            });

            // --- Package Modal Logic ---
            const generateHotelSelectors = (dayCount) => {
                const container = document.getElementById('hotel_assignments_container');
                container.innerHTML = '<h4>Hotel Assignments by Day</h4>';
                
                let hotelOptions = '<option value="">-- No Hotel --</option>';
                hotelsData.forEach(hotel => {
                    hotelOptions += `<option value="${hotel.id}">${hotel.name}</option>`;
                });

                for (let i = 1; i <= dayCount; i++) {
                    const dayDiv = document.createElement('div');
                    dayDiv.className = 'form-group';
                    dayDiv.innerHTML = `
                        <label for="hotel_day_${i}">Day ${i}</label>
                        <select id="hotel_day_${i}" name="hotel_assignment_${i}" data-day="${i}">
                            ${hotelOptions}
                        </select>
                    `;
                    container.appendChild(dayDiv);
                }
            };
            
            document.getElementById('package_days').addEventListener('change', function() {
                const days = parseInt(this.value, 10);
                if (days > 0 && days < 100) { // Safety limit
                    generateHotelSelectors(days);
                } else {
                    document.getElementById('hotel_assignments_container').innerHTML = '';
                }
            });

            // --- Handle Services Checkboxes ---
            document.getElementById('hotelForm').addEventListener('change', function(e) {
                if (e.target.name.startsWith('service_')) {
                    const services = [];
                    if (document.querySelector('input[name="service_breakfast"]').checked) services.push('B');
                    if (document.querySelector('input[name="service_lunch"]').checked) services.push('L');
                    if (document.querySelector('input[name="service_dinner"]').checked) services.push('D');
                    
                    document.getElementById('hotel_services_provided').value = services.join(', ');
                }
            });

            // --- Form Submissions ---
            const handleFormSubmit = async (form, action, callback) => {
                const formData = new FormData(form);
                try {
                    const response = await fetch(`${API_URL}?action=${action}`, {
                        method: 'POST',
                        body: formData
                    });
                    const result = await response.json();
                    if (result.status === 'success') {
                        showToast(result.message, 'success');
                        callback();
                    } else {
                        showToast(result.message, 'error');
                    }
                } catch (error) {
                    showToast('An error occurred while saving: ' + error.message, 'error');
                }
            };

            document.getElementById('tripForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const tripId = document.getElementById('tripIdHidden').value;
                const action = tripId ? 'updateTrip' : 'addTrip';
                handleFormSubmit(this, action, () => {
                    closeModal('tripModal');
                    fetchTrips();
                    fetchPackages(); // Refresh packages in trip form
                });
            });

            document.getElementById('packageForm').addEventListener('submit', async function(e) {
                e.preventDefault();
                const packageId = document.getElementById('packageId').value;
                const action = packageId ? 'updateTripPackage' : 'addTripPackage';
                
                const data = {
                    id: packageId || undefined,
                    name: document.getElementById('package_name').value,
                    code: document.getElementById('package_code').value,
                    No_of_Days: document.getElementById('package_days').value,
                    hotel_assignments: {}
                };
                
                document.querySelectorAll('#hotel_assignments_container select').forEach(select => {
                    const day = select.dataset.day;
                    const hotelId = select.value;
                    if (hotelId) {
                        data.hotel_assignments[day] = hotelId;
                    }
                });

                try {
                    const response = await fetch(`${API_URL}?action=${action}`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(data)
                    });
                    const result = await response.json();
                    if (result.status === 'success') {
                        showToast(result.message, 'success');
                        closeModal('packageModal');
                        fetchPackages();
                    } else {
                        showToast(result.message, 'error');
                    }
                } catch (error) {
                    showToast('An error occurred: ' + error.message, 'error');
                }
            });

            document.getElementById('hotelForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const hotelId = document.getElementById('hotelId').value;
                const action = hotelId ? 'updateHotel' : 'addHotel';
                handleFormSubmit(this, action, () => {
                    closeModal('hotelModal');
                    fetchHotels();
                });
            });

            document.getElementById('vehicleForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const vehicleId = document.getElementById('vehicleId').value;
                const action = vehicleId ? 'updateVehicle' : 'addVehicle';
                handleFormSubmit(this, action, () => {
                    closeModal('vehicleModal');
                    fetchVehicles();
                });
            });

            document.getElementById('guideForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const guideId = document.getElementById('guideId').value;
                const action = guideId ? 'updateGuide' : 'addGuide';
                handleFormSubmit(this, action, () => {
                    closeModal('guideModal');
                    fetchGuides();
                });
            });

            // --- Event Delegation for Edit/Delete ---
            document.addEventListener('click', async function(e) {
                const target = e.target.closest('a[data-id]');
                if (!target) return;

                const id = target.dataset.id;
                
                // Edit Actions
                if (target.classList.contains('btn-edit-trip')) {
                    e.preventDefault();
                    const trip = tripsData.find(t => t.id == id);
                    if (trip) {
                        document.getElementById('modalTitle').textContent = 'Edit Trip';
                        document.getElementById('tripIdHidden').value = trip.id;
                        document.getElementById('tripIdDisplay').value = '#' + String(trip.id).padStart(3, '0');
                        document.getElementById('fileIdGroup').style.display = 'block';
                        document.getElementById('customer_name').value = trip.customer_name;
                        document.getElementById('tour_code').value = trip.tour_code || '';
                        document.getElementById('trip_package_id').value = trip.trip_package_id;
                        document.getElementById('start_date').value = trip.start_date;
                        document.getElementById('end_date').value = trip.end_date;
                        document.getElementById('status').value = trip.status;
                        document.getElementById('trip_package_id').dispatchEvent(new Event('change'));
                        openModal('tripModal');
                    }
                }

                if (target.classList.contains('btn-edit-package')) {
                    e.preventDefault();
                    const pkg = packagesData.find(p => p.id == id);
                    if (pkg) {
                        document.getElementById('packageModalTitle').textContent = 'Edit Package';
                        document.getElementById('packageId').value = pkg.id;
                        document.getElementById('package_name').value = pkg.name;
                        document.getElementById('package_code').value = pkg.code || '';
                        document.getElementById('package_days').value = pkg.No_of_Days;
                        
                        generateHotelSelectors(pkg.No_of_Days);
                        
                        try {
                            const response = await fetch(`${API_URL}?action=getPackageHotels&trip_package_id=${pkg.id}`);
                            const result = await response.json();
                            if (result.status === 'success' && result.data) {
                                result.data.forEach(assignment => {
                                    const selector = document.getElementById(`hotel_day_${assignment.day_number}`);
                                    if (selector) {
                                        selector.value = assignment.hotel_id;
                                    }
                                });
                            }
                        } catch (error) {
                            showToast('Could not load hotel assignments.', 'error');
                        }

                        openModal('packageModal');
                    }
                }
                
                if (target.classList.contains('btn-edit-hotel')) {
                    e.preventDefault();
                    const hotel = hotelsData.find(h => h.id == id);
                    if (hotel) {
                        document.getElementById('hotelModalTitle').textContent = 'Edit Hotel';
                        document.getElementById('hotelId').value = hotel.id;
                        document.getElementById('hotel_name').value = hotel.name;
                        document.getElementById('hotel_room_types').value = hotel.room_types || '';
                        document.getElementById('hotel_availability').value = hotel.availability || 'Available';
                        
                        document.querySelector('input[name="service_breakfast"]').checked = hotel.services_provided && hotel.services_provided.includes('B');
                        document.querySelector('input[name="service_lunch"]').checked = hotel.services_provided && hotel.services_provided.includes('L');
                        document.querySelector('input[name="service_dinner"]').checked = hotel.services_provided && hotel.services_provided.includes('D');
                        document.getElementById('hotel_services_provided').value = hotel.services_provided || '';
                        
                        openModal('hotelModal');
                    }
                }
                
                if (target.classList.contains('btn-edit-vehicle')) {
                    e.preventDefault();
                    const vehicle = vehiclesData.find(v => v.id == id);
                    if (vehicle) {
                        document.getElementById('vehicleModalTitle').textContent = 'Edit Vehicle';
                        document.getElementById('vehicleId').value = vehicle.id;
                        document.getElementById('vehicle_name').value = vehicle.vehicle_name;
                        document.getElementById('vehicle_capacity').value = vehicle.capacity || '';
                        document.getElementById('vehicle_availability').value = vehicle.availability || 'Available';
                        openModal('vehicleModal');
                    }
                }
                
                if (target.classList.contains('btn-edit-guide')) {
                    e.preventDefault();
                    const guide = guidesData.find(g => g.id == id);
                    if (guide) {
                        document.getElementById('guideModalTitle').textContent = 'Edit Guide';
                        document.getElementById('guideId').value = guide.id;
                        document.getElementById('guide_name').value = guide.name;
                        document.getElementById('guide_language').value = guide.language || '';
                        document.getElementById('guide_availability').value = guide.availability_status || 'Available';
                        openModal('guideModal');
                    }
                }

                // Delete Actions
                const handleDelete = async (action, callback) => {
                    try {
                        const response = await fetch(`${API_URL}?action=${action}`, {
                            method: 'DELETE',
                            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                            body: `id=${id}`
                        });
                        const result = await response.json();
                        if(result.status === 'success'){
                            showToast(result.message, 'success');
                            callback();
                        } else {
                            showToast(result.message, 'error');
                        }
                    } catch (error) {
                        showToast(`Error deleting item.`, 'error');
                    }
                };
                
                if (target.classList.contains('btn-delete-trip')) {
                    e.preventDefault();
                    if (confirm('Are you sure you want to delete this trip?')) handleDelete('deleteTrip', fetchTrips);
                }
                if (target.classList.contains('btn-delete-package')) {
                    e.preventDefault();
                    if (confirm('Are you sure you want to delete this package?')) handleDelete('deleteTripPackage', fetchPackages);
                }
                if (target.classList.contains('btn-delete-hotel')) {
                    e.preventDefault();
                    if (confirm('Are you sure you want to delete this hotel?')) handleDelete('deleteHotel', fetchHotels);
                }
                if (target.classList.contains('btn-delete-vehicle')) {
                    e.preventDefault();
                    if (confirm('Are you sure you want to delete this vehicle?')) handleDelete('deleteVehicle', fetchVehicles);
                }
                if (target.classList.contains('btn-delete-guide')) {
                    e.preventDefault();
                    if (confirm('Are you sure you want to delete this guide?')) handleDelete('deleteGuide', fetchGuides);
                }
            });

            // --- Utility ---
            function showToast(message, type = 'success') {
                const toast = document.getElementById('toast');
                toast.textContent = message;
                toast.className = `toast show ${type}`;
                setTimeout(() => {
                    toast.className = toast.className.replace('show', '');
                }, 3000);
            }

            // Initial Data Load
            fetchTrips();
            fetchPackages();
            fetchVehicles();
            fetchGuides();
            fetchHotels(); 
        });
    </script>
</body>
</html>