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
        
        /* Collapsible Sidebar Styles */
        .nav-section {
            margin: 10px 0;
        }
        
        .section-toggle {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px 20px;
            text-decoration: none;
            color: var(--text-light);
            font-weight: 600;
            cursor: pointer;
            border-bottom: 1px solid var(--border-color);
            background: #f8f9fa;
            transition: all 0.3s ease;
        }
        
        .section-toggle:hover {
            background-color: #e8f0fe;
            color: var(--primary-color);
        }
        
        .toggle-arrow {
            margin-left: auto;
            transition: transform 0.3s ease;
            font-size: 0.8rem;
        }
        
        .section-toggle.expanded .toggle-arrow {
            transform: rotate(180deg);
        }
        
        .nav-submenu {
            list-style: none;
            padding: 0;
            margin: 0;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            background: #fdfdfd;
        }
        
        .nav-submenu.expanded {
            max-height: 500px;
        }
        
        .nav-submenu li a {
            padding: 12px 20px 12px 50px;
            font-weight: 500;
            border-left: 3px solid transparent;
        }
        
        .nav-submenu li a:hover {
            background-color: #e8f0fe;
            color: var(--primary-color);
            border-left-color: var(--primary-color);
        }
        
        .nav-submenu li.active a {
            background-color: #e8f0fe;
            color: var(--primary-color);
            border-left-color: var(--primary-color);
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
        /* Wider, compact Trip modal */
        #tripModal .modal-content { 
            max-width: 700px; 
            padding: 25px; 
            max-height: 80vh; 
            overflow: auto; 
            position: relative;
        }
        #tripModal .modal-content form { display: flex; flex-direction: column; }
        /* Ensure all steps render within modal width */
        #tripModal #tripStep1, #tripModal #tripStep2, #tripModal #tripStep3 { width: 100%; }
        #tripModal .section-card { width: 100%; box-sizing: border-box; }
        #tripModal #departureDetailsCard h4 { display:flex; align-items:center; gap:8px; }
        #tripModal #departureGroupsContainer { grid-template-columns: repeat(2, minmax(320px, 1fr)); }
        /* #tripModal .modal-content form > *:last-child.form-buttons { margin-top: auto; } -- REMOVED */
        #tripModal .form-grid { grid-template-columns: 1fr 1fr; gap: 16px; }
        #tripModal .section-card { padding: 15px; margin-bottom: 15px; }
        #tripModal #tripStep2 .section-card:last-child{ margin-bottom: 15px; }
        #tripModal .form-group { margin-bottom: 15px; }
        #tripModal .form-group label { margin-bottom: 5px; }
        #tripModal .form-group input, #tripModal .form-group select { padding: 10px; }
        #tripModal details { background: #ffffff; border: 1px dashed var(--border-color); border-radius: 8px; padding: 8px 10px; }
        #tripModal details summary { cursor: pointer; font-weight: 600; color: var(--text-light); }
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
        /* Counters for couples/singles */
        .counter-row{display:flex;align-items:center;justify-content:space-between;gap:12px;margin:6px 0;padding:8px 10px;border:1px solid var(--border-color);background:#fff;border-radius:6px;}
        .counter-label{font-weight:600;color:var(--text-color);}
        .counter{display:inline-flex;align-items:center;gap:8px;}
        .counter-value{min-width:28px;text-align:center;font-weight:700;}
        .counter-btn{background:#1e40af;color:#fff;border:none;border-radius:4px;width:30px;height:28px;cursor:pointer;}
        .counter-btn:disabled{opacity:.5;cursor:not-allowed;}

        /* Arrival drawer */
        .arrival-drawer{ position: fixed; right: 30px; top: 110px; width: 560px; max-width: 92vw; height: 72vh; background:#fff; border:1px solid var(--border-color); border-radius:10px; box-shadow: var(--shadow); transform: translateX(110%); transition: transform .3s ease; z-index: 1100; display:flex; flex-direction:column; }
        .arrival-drawer.open{ transform: translateX(0); }
        .arrival-drawer .header{ padding:10px 12px; display:flex; align-items:center; justify-content:space-between; background:#f7f7f9; border-bottom:1px solid var(--border-color); font-weight:700; }
        .arrival-drawer .body{ padding:10px 12px; overflow:auto; }

        .form-buttons {
            /* position: sticky; bottom: 0; z-index: 2; -- REMOVED */
            background: #fff; 
            padding-top: 15px; 
            margin-top: 20px;
            display: flex; 
            justify-content: flex-end; 
            gap: 10px; 
            border-top: 1px solid var(--border-color);
        }
        /* Inline controls inside the trip form */
        .form-inline-controls {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin: 10px 0 16px;
        }
        .icon-btn {
            background: #fff;
            color: var(--primary-color);
            border: 1px solid var(--border-color);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .icon-btn:hover { background: #f3f4f6; }
        .save-btn {
            background: var(--success-color);
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 10px 16px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        button, .btn {
            padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; color: white; font-weight: bold; transition: background-color 0.3s ease;
        }
        .btn-save { background-color: var(--success-color); }
        .btn-cancel { background-color: #aaa; }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .section-card {
            border: 1px solid var(--border-color);
            background: #f7f9ff;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 12px;
            box-shadow: var(--shadow);
        }
        .section-card h4 {
            margin: 0 0 10px 0;
            font-size: 1rem;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .section-card h4 i { opacity: 0.85; }

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

        #day_requirements_container {
            max-height: 400px;
            overflow-y: auto;
            border-top: 1px solid var(--border-color);
            margin-top: 15px;
            padding-top: 10px;
        }
        
        .day-requirement-card {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            margin-bottom: 15px;
            overflow: hidden;
        }
        
        .day-header {
            background: var(--primary-color);
            color: white;
            padding: 10px 15px;
            font-weight: 600;
        }
        
        .day-content {
            padding: 15px;
            background: #fdfdfd;
        }
        
        .requirement-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
            margin-bottom: 10px;
        }
        
        .requirement-section {
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            padding: 10px;
            background: white;
        }
        
        .requirement-section h5 {
            margin: 0 0 8px 0;
            font-size: 0.9rem;
            color: var(--text-light);
            font-weight: 600;
        }
        
        .requirement-checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
        }
        
        .requirement-checkbox input[type="checkbox"] {
            width: auto;
        }
        
        .vehicle-type-select {
            width: 100%;
            padding: 5px;
            border: 1px solid var(--border-color);
            border-radius: 3px;
            font-size: 0.9rem;
            margin-top: 5px;
        }
        
        @media screen and (max-width: 768px) {
            .requirement-grid {
                grid-template-columns: 1fr;
            }
        }

        .toast {
            visibility: hidden; min-width: 250px; margin-left: -125px; background-color: #333; color: #fff; text-align: center; border-radius: 5px; padding: 16px; position: fixed; z-index: 1001; left: 50%; bottom: 30px; font-size: 17px; opacity: 0; transition: opacity 0.3s, visibility 0.3s, bottom 0.3s;
        }
        .toast.show {
            visibility: visible; opacity: 1;
        }
        .toast.success { background-color: var(--success-color); }
        .toast.error { background-color: var(--error-color); }
        .toast.info { background-color: #3b82f6; }
        .toast.warning { background-color: var(--warning-color); }

        /* Action notification */
        .action-toast {
            position: fixed; right: 20px; bottom: 20px; background: #111827; color: #fff; padding: 14px 16px; border-radius: 8px; box-shadow: 0 6px 18px rgba(0,0,0,0.2); display: none; align-items: center; gap: 12px; z-index: 1002;
        }
        .action-toast.show { display: flex; }
        .action-toast .msg { margin-right: 8px; }
        .action-toast .btn {
            border: none; border-radius: 6px; padding: 8px 10px; font-weight: 600; cursor: pointer;
        }
        .action-toast .btn-primary { background: var(--primary-color); color: #fff; }
        .action-toast .btn-secondary { background: #374151; color: #e5e7eb; }

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
            
            /* Hide collapsible functionality on small screens */
            .section-toggle .link-text { display: none; }
            .toggle-arrow { display: none; }
            .nav-submenu {
                max-height: none;
                position: static;
            }
            .nav-submenu li a {
                padding: 15px;
                justify-content: center;
            }
            .nav-submenu li a .link-text { display: none; }

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

        /* Hotel Records Styles */
        .hotel-records-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .hotel-group {
            background: var(--card-background);
            border-radius: 8px;
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .hotel-header {
            background: var(--primary-color);
            color: white;
            padding: 15px 20px;
            font-size: 1.1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .hotel-bookings {
            padding: 0;
        }

        .booking-item {
            padding: 15px 20px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background-color 0.3s ease;
        }

        .booking-item:last-child {
            border-bottom: none;
        }

        .booking-item:hover {
            background-color: #f8f9fa;
        }

        .booking-main {
            flex: 1;
        }

        .booking-title {
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 4px;
        }

        .booking-details {
            color: var(--text-light);
            font-size: 0.9rem;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .booking-meta {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .booking-duration {
            background: var(--background-color);
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--text-light);
        }

        .booking-actions {
            display: flex;
            gap: 10px;
        }

        .booking-actions a {
            color: var(--primary-color);
            font-size: 1.1rem;
            text-decoration: none;
            padding: 5px;
            border-radius: 3px;
            transition: background-color 0.3s ease;
        }

        .booking-actions a:hover {
            background-color: var(--background-color);
        }

        .no-records {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-light);
            font-style: italic;
        }

        @media screen and (max-width: 768px) {
            .booking-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .booking-meta {
                justify-content: space-between;
                width: 100%;
            }

            .booking-details {
                gap: 10px;
            }
        }
    </style>
</head>

<body>

    <aside class="sidebar">
        <div class="sidebar-header"><span class="full-text">ATH</span></div>

        <ul class="sidebar-nav">
            <li class="active"><a data-section="dashboard"><i class="fas fa-tachometer-alt fa-fw"></i> <span class="link-text">Dashboard</span></a></li>
            
            <!-- Entry Section -->
            <li class="nav-section">
                <a class="section-toggle" data-toggle="entry">
                    <i class="fas fa-plus-circle fa-fw"></i> 
                    <span class="link-text">Manage</span>
                    <i class="fas fa-chevron-down toggle-arrow"></i>
                </a>
                <ul class="nav-submenu" id="entrySubmenu">
                    <li><a data-section="trips"><i class="fas fa-file-alt fa-fw"></i> <span class="link-text">Trip Files</span></a></li>
                    <li><a data-section="packages"><i class="fas fa-box-open fa-fw"></i> <span class="link-text">Packages</span></a></li>
                    <li><a data-section="hotels"><i class="fas fa-hotel fa-fw"></i> <span class="link-text">Hotels</span></a></li>
                    <li><a data-section="vehicles"><i class="fas fa-car fa-fw"></i> <span class="link-text">Vehicles</span></a></li>
                    <li><a data-section="guides"><i class="fas fa-user-friends fa-fw"></i> <span class="link-text">Guides</span></a></li>
                </ul>
            </li>
            
            <!-- Reports Section -->
            <li class="nav-section">
                <a class="section-toggle" data-toggle="reports">
                    <i class="fas fa-chart-bar fa-fw"></i> 
                    <span class="link-text">Insights</span>
                    <i class="fas fa-chevron-down toggle-arrow"></i>
                </a>
                <ul class="nav-submenu" id="reportsSubmenu">
                    <li><a data-section="insights"><i class="fas fa-plane fa-fw"></i> <span class="link-text">Arrival & Departure</span></a></li>
                    <li><a data-section="hotelrecords"><i class="fas fa-calendar-check fa-fw"></i> <span class="link-text">Hotel Records</span></a></li>
                    <li><a data-section="guiderecords"><i class="fas fa-user-check fa-fw"></i> <span class="link-text">Guide Records</span></a></li>
                    <li><a data-section="vehiclerecords"><i class="fas fa-truck fa-fw"></i> <span class="link-text">Vehicle Records</span></a></li>
                    <li><a data-section="dayroster"><i class="fas fa-calendar-day fa-fw"></i> <span class="link-text">Duty Roster</span></a></li>
                </ul>
            </li>
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
                        <div style="display:flex; gap:8px; align-items:center;">
                          <button id="addTripBtn" class="btn-add"><i class="fas fa-plus"></i> New File</button>
                        </div>
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
                                    <th>EMAIL</th>
                                    <th>AVAILABILITY</th>
                                    <th>ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </section>

            <section id="insightsSection" class="content-section">
                <div class="trips-container">
                    <div class="trips-header" style="align-items:center; gap:10px;">
                        <h2>Arrival & Departure Insights</h2>
                        <div style="display:flex; gap:10px; align-items:center;">
                            <input type="month" id="insightsMonth" class="btn-add" style="padding: 10px; border-radius: 5px; border: 1px solid var(--border-color); background: white; color: var(--text-color);">
                        </div>
                    </div>
                    <div id="insightsContainer">
                        <div id="arrivalInsightsContainer" class="hotel-records-container" style="margin-bottom:16px;"></div>
                        <div id="departureInsightsContainer" class="hotel-records-container"></div>
                    </div>
                </div>
            </section>

            <section id="vehiclerecordsSection" class="content-section">
                <div class="trips-container">
                    <div class="trips-header">
                        <h2>Vehicle Assignment Records</h2>
                    </div>
                    <div id="vehicleRecordsContainer" class="hotel-records-container"></div>
                </div>
            </section>

            <section id="dayrosterSection" class="content-section">
                <div class="trips-container">
                    <div class="trips-header" style="align-items:center; gap:10px;">
                        <h2>Duty Roster</h2>
                        <div style="display:flex; gap:10px; align-items:center;">
                            <input type="month" id="dayRosterMonth" class="btn-add" style="padding: 10px; border-radius: 5px; border: 1px solid var(--border-color); background: white; color: var(--text-color);">
                        </div>
                    </div>
                    <div id="dayRosterContainer" class="hotel-records-container"></div>
                </div>
            </section>

            <section id="hotelrecordsSection" class="content-section">
                <div class="trips-container">
                    <div class="trips-header">
                        <h2>Hotel Booking Records</h2>
                        <div style="display: flex; gap: 10px;">
                            <select id="filterStatus" class="btn-add" style="padding: 10px; border-radius: 5px; border: 1px solid var(--border-color); background: white; color: var(--text-color);">
                                <option value="">All Status</option>
                                <option value="Active">Active</option>
                                <option value="Completed">Completed</option>
                                <option value="Pending">Pending</option>
                            </select>
                            <input type="month" id="filterMonth" class="btn-add" style="padding: 10px; border-radius: 5px; border: 1px solid var(--border-color); background: white; color: var(--text-color);">
                        </div>
                    </div>
                    <div id="hotelRecordsContainer" class="hotel-records-container">
                        <!-- Hotel records will be rendered here -->
                    </div>
                </div>
            </section>

            <section id="guiderecordsSection" class="content-section">
                <div class="trips-container">
                    <div class="trips-header">
                        <h2>Guide Assignment Records</h2>
                        <div style="display: flex; gap: 10px;">
                            <select id="guideFilterStatus" class="btn-add" style="padding: 10px; border-radius: 5px; border: 1px solid var(--border-color); background: white; color: var(--text-color);">
                                <option value="">All Status</option>
                                <option value="Active">Active</option>
                                <option value="Completed">Completed</option>
                                <option value="Pending">Pending</option>
                            </select>
                            <input type="month" id="guideFilterMonth" class="btn-add" style="padding: 10px; border-radius: 5px; border: 1px solid var(--border-color); background: white; color: var(--text-color);">
                        </div>
                    </div>
                    <div id="guideRecordsContainer" class="hotel-records-container">
                        <!-- Guide records will be rendered here -->
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

                <div id="tripStep1">
                <div class="section-card">
                    <h4><i class="fas fa-suitcase-rolling"></i> Tour Details</h4>
                    <div class="form-group">
                        <label for="trip_package_id">Tour Name (Package)</label>
                        <select id="trip_package_id" name="trip_package_id" required>
                            <option value="">Select Package</option>
                        </select>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="tour_code">Tour File No</label>
                            <input type="text" id="tour_code" name="tour_code" placeholder="e.g. T-0001">
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status" required>
                                <option value="Pending">Pending</option>
                                <option value="Active">Active</option>
                                <option value="Completed">Completed</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="start_date">Start Date</label>
                            <input type="date" id="start_date" name="start_date" required>
                        </div>
                        <div class="form-group">
                            <label for="end_date">End Date</label>
                            <input type="date" id="end_date" name="end_date" required>
                        </div>
                    </div>

                    <details>
                        <summary>Optional details</summary>
                        <div class="form-grid" style="margin-top:8px;">
                            <div class="form-group">
                                <label for="passport_no">Passport No</label>
                                <input type="text" id="passport_no" name="passport_no">
                            </div>
                            <div class="form-group">
                                <label for="address">Address</label>
                                <input type="text" id="address" name="address">
                            </div>
                        </div>
                        <div id="package_description_container" class="form-group" style="display: none; margin-top:8px;">
                            <label>Package Details</label>
                            <div id="package_description"></div>
                        </div>
                    </details>
                </div>

                <div class="section-card" id="guestDetailsCard">
                    <h4><i class="fas fa-user"></i> Guest Details</h4>
                <div class="form-group">
                        <label for="company">Company</label>
                        <select id="company" name="company">
                            <option value="">Select Company</option>
                            <option value="Individual">Individual</option>
                            <option value="ASI">ASI</option>
                            <option value="Booking">Booking</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="booking_status">Booking Status</label>
                        <select id="booking_status" name="booking_status">
                            <option value="Booking">Booking</option>
                            <option value="Pre-Booking">Pre-Booking</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <div class="counter-row">
                            <span class="counter-label">No. of Couples</span>
                            <div class="counter">
                                <button type="button" class="counter-btn" data-target="couples_count" data-delta="-1">-</button>
                                <span class="counter-value" data-for="couples_count">0</span>
                                <button type="button" class="counter-btn" data-target="couples_count" data-delta="1">+</button>
                            </div>
                        </div>
                        <input type="number" id="couples_count" name="couples_count" min="0" value="0" style="display:none;">
                    </div>
                    <div class="form-group">
                        <div class="counter-row">
                            <span class="counter-label">No. of Singles</span>
                            <div class="counter">
                                <button type="button" class="counter-btn" data-target="singles_count" data-delta="-1">-</button>
                                <span class="counter-value" data-for="singles_count">0</span>
                                <button type="button" class="counter-btn" data-target="singles_count" data-delta="1">+</button>
                            </div>
                        </div>
                        <input type="number" id="singles_count" name="singles_count" min="0" value="0" style="display:none;">
                    </div>

                    <div class="form-group" style="margin-top:10px;">
                        <label for="country">Country</label>
                        <select id="country" name="country"></select>
                    </div>

                    <div id="guestNamesContainer" class="form-group" style="margin-top:10px;">
                        <label>Guest Names</label>
                        <div id="guestNamesInner"></div>
                    </div>
                    <div class="form-group" style="margin-top:10px;">
                        <label for="guest_details">Guest Details (optional)</label>
                        <textarea id="guest_details" name="guest_details" rows="2" placeholder="Notes about guests, preferences, special info..."></textarea>
                    </div>
                </div>
                <div id="controlsStep1" class="form-inline-controls">
                    <button type="button" id="btnStepBack1" class="icon-btn" title="Back" aria-label="Back">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    <button type="submit" id="btnStepSave1" class="save-btn" title="Save Trip">
                        <i class="fas fa-save"></i>
                        <span>Save</span>
                    </button>
                    <button type="button" id="btnStepNext1" class="icon-btn" title="Next" aria-label="Next">
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
                </div> <!-- end step 1 -->

                <div id="tripStep2">
                <div class="section-card" id="travelDetailsCard">
                    <h4><i class="fas fa-plane-arrival"></i> Travel Details</h4>

                    <div class="form-group">
                        <label>Arrival Mode</label>
                        <select id="arrival_mode">
                            <option value="single">Single Arrival</option>
                            <option value="multi">Multiple Arrivals</option>
                        </select>
                    </div>

                    <div id="singleArrivalSection" class="requirement-section" style="border:1px dashed var(--border-color); border-radius:8px; padding:10px; background:#fff;">
                        <h5 style="margin:0 0 6px 0; color: var(--text-light);">Single Arrival</h5>
                        <div class="form-grid" style="grid-template-columns:1fr 1fr 1fr; gap:12px; align-items:end;">
                            <div class="form-group"><label for="arrival_date">Date <small style="color:#6b7280;">(day)</small></label><div style="display:flex; gap:8px; align-items:center;"><input type="date" id="arrival_date" name="arrival_date"><span id="arrivalDayBadge" style="min-width:32px; text-align:center; padding:4px 6px; border:1px solid var(--border-color); border-radius:6px; background:#f9fafb; color:#111827; font-weight:700;">--</span></div></div>
                            <div class="form-group"><label for="arrival_time">Time (optional)</label><input type="time" id="arrival_time" name="arrival_time"></div>
                            <div class="form-group"><label for="arrival_flight">Flight (optional)</label><input type="text" id="arrival_flight" name="arrival_flight" placeholder="e.g. XY123"></div>
                        </div>
                    </div>

                    <div id="multiArrivalSection" style="display:none;">
                        <div id="namesPoolContainer" class="requirement-section" style="border:1px dashed var(--border-color); border-radius:8px; padding:10px; background:#fff; width:100%;">
                            <h5 style="margin:0 0 8px 0; color: var(--text-light);">Unassigned Guests</h5>
                            <div id="namesPool" style="display:flex; gap:8px; flex-wrap:wrap; justify-content:flex-end;"></div>
                        </div>
                    </div>

                        <div style="display:flex; justify-content:flex-end; margin:10px 0;">
                            <button type="button" id="btnAddArrivalGroup" class="btn-add"><i class="fas fa-plus"></i> Arrival Group</button>
                        </div>
                        <div id="arrivalGroupsContainer" style="display:grid; grid-template-columns: repeat(2, minmax(320px, 1fr)); gap:10px;"></div>
                    </div>

                <div id="controlsStep2" class="form-inline-controls">
                    <button type="button" id="btnStepBack2" class="icon-btn" title="Back" aria-label="Back">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    <button type="submit" id="btnStepSave2" class="save-btn" title="Save Trip">
                        <i class="fas fa-save"></i>
                        <span>Save</span>
                    </button>
                    <button type="button" id="btnStepNext2" class="icon-btn" title="Next" aria-label="Next">
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>

                </div>
                                <div id="tripStep3" style="display:none;">
                    <div class="section-card" id="departureDetailsCard">
                        <h4><i class="fas fa-plane-departure"></i> Departure Details</h4>
                        <div class="dep-controls" style="display:flex; align-items:center; justify-content:space-between; gap:10px; margin:10px 0;">
                            <label for="sameAsArrivalDepCheckbox" style="display:flex; gap:8px; align-items:center; margin:0; font-weight:600; color: var(--text-color);">
                                <input type="checkbox" id="sameAsArrivalDepCheckbox">
                                <span>Use same groups as Arrival</span>
                            </label>
                            <button type="button" id="btnAddDepartureGroup" class="btn-add"><i class="fas fa-plus"></i> Departure Group</button>
                        </div>
                        <div class="form-group" id="depSingleControls" style="display:none;">
                            <div class="form-grid" style="grid-template-columns:1fr 1fr 1fr; gap:12px; align-items:end;">
                                <div class="form-group"><label for="departure_date">Date <small style="color:#6b7280;">(day)</small></label><div style="display:flex; gap:8px; align-items:center;"><input type="date" id="departure_date" name="departure_date"><span id="departureDayBadge" style="min-width:32px; text-align:center; padding:4px 6px; border:1px solid var(--border-color); border-radius:6px; background:#f9fafb; color:#111827; font-weight:700;">--</span></div></div>
                                <div class="form-group"><label for="departure_time">Time (optional)</label><input type="time" id="departure_time" name="departure_time"></div>
                                <div class="form-group"><label for="departure_flight">Flight (optional)</label><input type="text" id="departure_flight" name="departure_flight" placeholder="e.g. XY456"></div>
                            </div>
                        </div>
                        <div id="depNamesPoolContainer" class="requirement-section" style="display:none; border:1px dashed var(--border-color); border-radius:8px; padding:10px; background:#fff; width:100%;">
                            <h5 style="margin:0 0 8px 0; color: var(--text-light);">Unassigned Guests</h5>
                            <div id="depNamesPool" style="display:flex; gap:8px; flex-wrap:wrap; justify-content:flex-end;"></div>
                        </div>
                        <div id="departureGroupsContainer" style="display:grid; grid-template-columns: repeat(2, minmax(320px, 1fr)); gap:10px;"></div>
                    </div>
                </div>
                </div> <!-- end step 2 -->

 <!-- end step 3 -->

                <div id="controlsStep3" class="form-inline-controls" style="display:none;">
                    <button type="button" id="btnStepBack3" class="icon-btn" title="Back" aria-label="Back">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    <button type="submit" id="btnStepSave3" class="save-btn" title="Save Trip">
                        <i class="fas fa-save"></i>
                        <span>Save</span>
                    </button>
                </div>


            </form>
        </div>
    </div>

    <div id="packageModal" class="modal">
        <div class="modal-content" style="max-width: 800px;">
            <span class="close-btn" data-modal="packageModal">&times;</span>
            <h2 id="packageModalTitle">Add Package</h2>
            <div id="packageDuplicateBanner" style="display:none; background:#fff3cd; color:#856404; border:1px solid #ffeeba; padding:10px; border-radius:6px; margin:8px 0;">
                Note: This package code was duplicated. Please review and update the Package Code before saving.
            </div>
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
                
                <div id="day_requirements_container" class="form-group">
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
                    <label for="hotel_email">Email</label>
                    <input type="email" id="hotel_email" name="email" placeholder="hotel@example.com">
                </div>
                
                <div class="form-group">
                    <label>Room Types (optional)</label>
                    <div class="services-block" style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
                        <label style="display:flex; gap:6px; align-items:center;"><input type="checkbox" id="room_type_double"> <span>Double</span></label>
                        <label style="display:flex; gap:6px; align-items:center;"><input type="checkbox" id="room_type_twin"> <span>Twin</span></label>
                        <label style="display:flex; gap:6px; align-items:center;"><input type="checkbox" id="room_type_single"> <span>Single</span></label>
                        <label style="display:flex; gap:6px; align-items:center;"><input type="checkbox" id="room_type_triple"> <span>Triple</span></label>
                        <input type="hidden" id="hotel_room_types" name="room_types">
                    </div>
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
                    <label for="vehicle_email">Email</label>
                    <input type="email" id="vehicle_email" name="email" placeholder="driver@example.com">
                </div>
                <div class="form-group">
                    <label for="vehicle_number_plate">Number Plate</label>
                    <input type="text" id="vehicle_number_plate" name="number_plate" placeholder="e.g., BA-2-CHA-1234">
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
                    <label for="guide_email">Email</label>
                    <input type="email" id="guide_email" name="email" placeholder="guide@example.com">
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

    <!-- Missing Assignment Modal -->
    <style>
      .mini-modal { position: fixed; inset: 0; background: rgba(0,0,0,0.35); display: none; align-items:center; justify-content:center; z-index: 1200; }
      .mini-modal .content { width: 430px; background:#fff; border-radius:10px; box-shadow: var(--shadow-md); overflow:hidden; }
      .mini-modal .header { padding:10px 14px; background:#f3f4f6; border-bottom:1px solid #e5e7eb; font-weight:700; display:flex; justify-content:space-between; align-items:center; }
      .mini-modal .body { padding:14px; display:flex; flex-direction:column; gap:10px; }
      .mini-modal .footer { padding:10px 14px; border-top:1px solid #e5e7eb; display:flex; gap:8px; justify-content:flex-end; }
      .mini-modal .btn { padding:8px 12px; border:none; border-radius:6px; cursor:pointer; font-weight:600; }
      .mini-modal .btn-primary { background: var(--primary-color); color: #fff; }
      .mini-modal .btn-secondary { background: #e5e7eb; color:#111827; }
      .mini-modal .close { cursor:pointer; color:#6b7280; }
    </style>
    <div id="missingAssignModal" class="mini-modal" aria-hidden="true">
      <div class="content">
        <div class="header">
          <span id="missingAssignTitle">Assign Missing</span>
          <span id="missingAssignClose" class="close"><i class="fas fa-times"></i></span>
        </div>
        <div class="body">
          <div id="missingAssignInfo" style="font-size:0.9rem; color: var(--text-color);"></div>
          <div id="missingAssignSelectGroup" class="form-group">
            <label id="missingAssignLabel">Select</label>
            <div class="custom-select"><select id="missingAssignSelect"></select></div>
            <small id="missingAssignHint" style="color:#6b7280"></small>
          </div>
          <div id="missingRoomsContainer" class="form-group" style="display:none;">
            <label>Room Quantities</label>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
              <div>
                <label style="font-size:0.8rem; color:#6b7280;">Double</label>
                <input type="number" id="room_double" min="0" value="0" style="width:100%; padding:8px;">
              </div>
              <div>
                <label style="font-size:0.8rem; color:#6b7280;">Twin</label>
                <input type="number" id="room_twin" min="0" value="0" style="width:100%; padding:8px;">
              </div>
              <div>
                <label style="font-size:0.8rem; color:#6b7280;">Single</label>
                <input type="number" id="room_single" min="0" value="0" style="width:100%; padding:8px;">
              </div>
              <div>
                <label style="font-size:0.8rem; color:#6b7280;">Triple</label>
                <input type="number" id="room_triple" min="0" value="0" style="width:100%; padding:8px;">
              </div>
            </div>
            <small style="color:#6b7280">Enter at least one non-zero value.</small>
          </div>
          <div id="missingServicesContainer" class="form-group" style="display:none;">
            <label>Services Provided</label>
            <div style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
              <label style="display:flex; gap:6px; align-items:center;">
                <input type="checkbox" id="svc_b" value="B"> <span>B (Breakfast)</span>
              </label>
              <label style="display:flex; gap:6px; align-items:center;">
                <input type="checkbox" id="svc_l" value="L"> <span>L (Lunch)</span>
              </label>
              <label style="display:flex; gap:6px; align-items:center;">
                <input type="checkbox" id="svc_d" value="D"> <span>D (Dinner)</span>
              </label>
            </div>
            <small style="color:#6b7280">Select meals included for this day.</small>
          </div
        </div>
        <div class="footer">
          <button id="missingAssignSkip" class="btn btn-secondary">Skip</button>
          <button id="missingAssignSave" class="btn btn-primary">Assign & Next</button>
        </div>
      </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const API_URL = 'api/api.php';

            let tripsData = [];
            let hotelsData = [];
            let vehiclesData = [];
            let guidesData = [];
            let packagesData = [];
            let hotelRecordsData = [];
            let guideRecordsData = [];

    async function logout() {
        if (confirm('Are you sure you want to logout?')) {
            try {
                // Show loading state
                showToast('Logging out...', 'success');
                
                const response = await fetch('../utils/auth.php?action=logout', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    credentials: 'same-origin'
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                
                if (result.status === 'success') {
                    showToast('Logged out successfully', 'success');
                    // Small delay to show the message
                    setTimeout(() => {
                        window.location.href = 'login.html';
                    }, 1000);
                } else {
                    showToast(result.message || 'Logout failed', 'error');
                    // Redirect anyway after a delay
                    setTimeout(() => {
                        window.location.href = 'login.html';
                    }, 2000);
                }
            } catch (error) {
                console.error('Logout error:', error);
                showToast('Logout failed, but redirecting...', 'error');
                // Always redirect to login page even if logout fails
                setTimeout(() => {
                    window.location.href = 'login.html';
                }, 2000);
            }
        }
    }

            // Collapsible Sidebar Toggle
            document.querySelectorAll('.section-toggle').forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.dataset.toggle + 'Submenu';
                    const submenu = document.getElementById(targetId);
                    
                    // Toggle expanded class
                    this.classList.toggle('expanded');
                    submenu.classList.toggle('expanded');
                });
            });
            
            // Navigation
            document.querySelectorAll('.sidebar-nav a[data-section]').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const section = this.dataset.section;
                    if (!section) return;
                    
                    // Remove active class from all nav items
                    document.querySelectorAll('.sidebar-nav li').forEach(li => li.classList.remove('active'));
                    
                    // Handle dashboard (direct child)
                    if (section === 'dashboard') {
                        this.parentElement.classList.add('active');
                    } else {
                        // For submenu items, add active to the submenu li
                        this.parentElement.classList.add('active');
                        
                        // Ensure the parent section is expanded
                        const submenu = this.closest('.nav-submenu');
                        if (submenu) {
                            const sectionToggle = submenu.previousElementSibling;
                            sectionToggle.classList.add('expanded');
                            submenu.classList.add('expanded');
                        }
                    }
                    
                    document.querySelectorAll('.content-section').forEach(sec => sec.classList.remove('active'));
                    document.getElementById(section + 'Section').classList.add('active');
                    
                    switch(section) {
                        case 'trips':
                            renderTrips(tripsData, document.querySelector('#allTripsTable tbody'));
                            break;
                        case 'insights':
                            fetchInsights();
                            break;
                        case 'packages':
                            fetchPackages();
                            break;
                        case 'hotels':
                            fetchHotels();
                            break;
                        case 'hotelrecords':
                            fetchHotelRecords();
                            break;
                        case 'guiderecords':
                            fetchGuideRecords();
                            break;
                        case 'vehiclerecords':
                            fetchVehicleRecords();
                            break;
                        case 'dayroster':
                            fetchDayRoster();
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
            
            // Month inputs default to current
            const nowMonth = new Date().toISOString().slice(0,7);
            const insM = document.getElementById('insightsMonth'); if (insM) { insM.value = nowMonth; insM.addEventListener('change', fetchInsights); }

            // Initialize sidebar - expand Entry section by default
            const entryToggle = document.querySelector('[data-toggle="entry"]');
            const entrySubmenu = document.getElementById('entrySubmenu');
            if (entryToggle && entrySubmenu) {
                entryToggle.classList.add('expanded');
                entrySubmenu.classList.add('expanded');
            }
            
            const updateStats = (trips) => {
                const totalTrips = trips.length;
                const activeTrips = trips.filter(t => t.status === 'Active').length;
                
                document.getElementById('totalTripsStat').textContent = totalTrips;
                document.getElementById('activeTripsStat').textContent = activeTrips;
                document.getElementById('monthlyBookings').textContent = totalTrips; 
                
                const uniqueCustomers = [...new Set(trips.map(item => item.customer_name))].length;
                document.getElementById('totalCustomers').textContent = uniqueCustomers;
            };

            async function fetchTrips() {
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
            
            async function fetchPackages() {
                try {
                    const response = await fetch(`${API_URL}?action=getTripPackages`);
                    const result = await response.json();
                    if (result.status === 'success') {
                        packagesData = [...result.data].sort((a,b)=> String(a.name||'').localeCompare(String(b.name||'')));
                        renderPackages(packagesData);
                        const select = document.getElementById('trip_package_id');
                        select.innerHTML = '<option value="">Select Package</option>';
                        packagesData.forEach(pkg => {
                            const days = pkg.No_of_Days ? ` (${pkg.No_of_Days} Days)` : '';
                            select.innerHTML += `<option value="${pkg.id}" data-description="${pkg.description || ''}" data-days="${pkg.No_of_Days || ''}">${pkg.name}${days}</option>`;
                        });
                    }
                } catch (error) {
                    showToast('Error fetching packages.', 'error');
                }
            };

            async function fetchHotels() {
                try {
                    const response = await fetch(`${API_URL}?action=getHotels`);
                    const result = await response.json();
                    if (result.status === 'success') {
                        hotelsData = [...result.data].sort((a,b)=> String(a.name||'').localeCompare(String(b.name||'')));
                        renderHotels(hotelsData);
                        // If a new hotel was just added for a package day, append/select it
                        if (window.__pendingHotelSelectId && window.__pendingHotelName) {
                          const target = document.getElementById(window.__pendingHotelSelectId);
                          const nameLower = window.__pendingHotelName.trim().toLowerCase();
                          const found = hotelsData.find(h=> String(h.name||'').trim().toLowerCase()===nameLower);
                          if (target && found) {
                            // ensure option exists
                            if (!Array.from(target.options).some(o=> String(o.value)===String(found.id))) {
                              const opt = document.createElement('option'); opt.value = found.id; opt.textContent = found.name; target.appendChild(opt);
                            }
                            target.value = String(found.id);
                          }
                          window.__pendingHotelSelectId = null; window.__pendingHotelName = null;
                        }
                    }
                } catch (error) {
                    showToast('Error fetching hotels.', 'error');
                }
            };

            async function fetchVehicles() {
                try {
                    const response = await fetch(`${API_URL}?action=getVehicles`);
                    const result = await response.json();
                    if (result.status === 'success') {
                        vehiclesData = [...result.data].sort((a,b)=> String(a.vehicle_name||'').localeCompare(String(b.vehicle_name||'')));
                        renderVehicles(vehiclesData);
                        document.getElementById('vehicleCount').textContent = vehiclesData.length;
                    }
                } catch (error) {
                    showToast('Error fetching vehicles.', 'error');
                }
            };

            async function fetchGuides() {
                try {
                    const response = await fetch(`${API_URL}?action=getGuides`);
                    const result = await response.json();
                    if (result.status === 'success') {
                        guidesData = [...result.data].sort((a,b)=> String(a.name||'').localeCompare(String(b.name||'')));
                        renderGuides(guidesData);
                        document.getElementById('guideCount').textContent = guidesData.length;
                    }
                } catch (error) {
                    showToast('Error fetching guides.', 'error');
                }
            };

            async function fetchHotelRecords() {
                try {
                    const response = await fetch(`${API_URL}?action=getHotelRecords`);
                    const result = await response.json();
                    if (result.status === 'success') {
                        hotelRecordsData = result.data;
                        renderHotelRecords(result.data);
                    } else {
                        showToast(result.message || 'Error fetching hotel records.', 'error');
                    }
                } catch (error) {
                    showToast('Error fetching hotel records.', 'error');
                }
            };
            
            async function fetchInsights() {
                try {
                    const m = document.getElementById('insightsMonth');
                    const month = m && m.value ? m.value : new Date().toISOString().slice(0,7);
                    const [arrRes, depRes] = await Promise.all([
                        fetch(`${API_URL}?action=getArrivalInsights&month=${encodeURIComponent(month)}`),
                        fetch(`${API_URL}?action=getDepartureInsights&month=${encodeURIComponent(month)}`)
                    ]);
                    const arrJs = await arrRes.json();
                    const depJs = await depRes.json();
                    if (arrJs.status==='success') renderArrivalInsights(arrJs.data); else showToast(arrJs.message||'Arrival insights error','error');
                    if (depJs.status==='success') renderDepartureInsights(depJs.data); else showToast(depJs.message||'Departure insights error','error');
                } catch(e){ showToast('Error fetching insights','error'); }
            };

            async function fetchVehicleRecords() {
                try {
                    const response = await fetch(`${API_URL}?action=getVehicleRecords`);
                    const result = await response.json();
                    if (result.status === 'success') {
                        renderVehicleRecords(result.data);
                    } else {
                        showToast(result.message || 'Error fetching vehicle records.', 'error');
                    }
                } catch (error) {
                    showToast('Error fetching vehicle records.', 'error');
                }
            };

            async function fetchGuideRecords() {
                try {
                    const response = await fetch(`${API_URL}?action=getGuideRecords`);
                    const result = await response.json();
                    if (result.status === 'success') {
                        guideRecordsData = result.data;
                        renderGuideRecords(result.data);
                    } else {
                        showToast(result.message || 'Error fetching guide records.', 'error');
                    }
                } catch (error) {
                    showToast('Error fetching guide records.', 'error');
                }
            };

            // --- UI Rendering ---
            function renderArrivalInsights(rows){
                const c = document.getElementById('arrivalInsightsContainer'); if (!c) return; c.innerHTML='';
                if (!rows || rows.length===0){ c.innerHTML = '<div class="no-records">No arrivals found for this period.</div>'; return; }
                // Group by date
                const byDate = rows.reduce((acc,r)=>{ const d=r.arrival_date; (acc[d]=acc[d]||[]).push(r); return acc; },{});
                Object.keys(byDate).sort().forEach(date=>{
                    const group = document.createElement('div'); group.className='hotel-group';
                    const head = document.createElement('div'); head.className='hotel-header'; head.style.background='#e6f4ea'; head.style.color='#166534'; head.innerHTML = `<i class="fas fa-plane-arrival"></i> ${date} <span style="margin-left:auto;">${byDate[date].length} arrival(s)</span>`; group.appendChild(head);
                    const body = document.createElement('div'); body.className='hotel-bookings';
                    byDate[date].forEach((r,idx)=>{
                        const item = document.createElement('div'); item.className='booking-item';
                        const plate = r.number_plate ? ` (${r.number_plate})` : '';
                        item.innerHTML = `
                            <div class="booking-main">
                                <div class="booking-title">[A${idx+1}] ${r.customer_name} — ${r.tour_code||''}</div>
                                <div class="booking-details">
                                    <span><i class="far fa-clock"></i> ${r.arrival_time||'—'}</span>
                                    <span><i class="fas fa-plane"></i> ${r.flight_no||'—'}</span>
                                    <span><i class="fas fa-users"></i> Pax: ${r.pax_count||'—'}</span>
                                    <span><i class="fas fa-map-marker-alt"></i> Pickup: ${r.pickup_location||'—'}</span>
                                    <span><i class="fas fa-hotel"></i> Drop: ${r.drop_hotel_name||'—'}</span>
                                    <span><i class="fas fa-car"></i> ${r.vehicle_name? (r.vehicle_name+plate) : '—'}</span>
                                    <span><i class="fas fa-user-tie"></i> ${r.guide_name||'—'}</span>
                                </div>
                            </div>
                            <div class="booking-actions">
                                <a href="Itinerary.php?trip_id=${r.trip_id}&focus_date=${r.arrival_date}" title="Open Itinerary on ${r.arrival_date}"><i class="fas fa-route"></i></a>
                            </div>`;
                        body.appendChild(item);
                    });
                    group.appendChild(body); c.appendChild(group);
                });
            }

            function renderDepartureInsights(rows){
                const c = document.getElementById('departureInsightsContainer'); if (!c) return; c.innerHTML='';
                if (!rows || rows.length===0){ c.innerHTML = '<div class="no-records">No departures found for this period.</div>'; return; }
                // Group by date
                const byDate = rows.reduce((acc,r)=>{ const d=r.departure_date; (acc[d]=acc[d]||[]).push(r); return acc; },{});
                Object.keys(byDate).sort().forEach(date=>{
                    const group = document.createElement('div'); group.className='hotel-group';
                    const head = document.createElement('div'); head.className='hotel-header'; head.style.background='#eef2ff'; head.style.color='#1e40af'; head.innerHTML = `<i class="fas fa-plane-departure"></i> ${date} <span style="margin-left:auto;">${byDate[date].length} departure(s)</span>`; group.appendChild(head);
                    const body = document.createElement('div'); body.className='hotel-bookings';
                    byDate[date].forEach((r,idx)=>{
                        const item = document.createElement('div'); item.className='booking-item';
                        const plate = r.number_plate ? ` (${r.number_plate})` : '';
                        item.innerHTML = `
                            <div class="booking-main">
                                <div class="booking-title">[D${idx+1}] ${r.customer_name} — ${r.tour_code||''}</div>
                                <div class="booking-details">
                                    <span><i class="far fa-clock"></i> ${r.departure_time||'—'}</span>
                                    <span><i class="fas fa-plane"></i> ${r.flight_no||'—'}</span>
                                    <span><i class="fas fa-users"></i> Pax: ${r.pax_count||'—'}</span>
                                    <span><i class="fas fa-hotel"></i> From: ${r.pickup_hotel_name||'—'}</span>
                                    <span><i class="fas fa-car"></i> ${r.vehicle_name? (r.vehicle_name+plate) : '—'}</span>
                                    <span><i class="fas fa-user-tie"></i> ${r.guide_name||'—'}</span>
                                </div>
                            </div>
                            <div class="booking-actions">
                                <a href="Itinerary.php?trip_id=${r.trip_id}&focus_date=${r.departure_date}" title="Open Itinerary on ${r.departure_date}"><i class="fas fa-route"></i></a>
                            </div>`;
                        body.appendChild(item);
                    });
                    group.appendChild(body); c.appendChild(group);
                });
            }

            // --- UI Rendering ---
            function renderTrips(trips, tbody) {
                tbody.innerHTML = '';
                if (!trips || trips.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;">No trips found.</td></tr>';
                    return;
                }
                // Sort trips in ascending order by ID
                const sortedTrips = [...trips].sort((a, b) => a.id - b.id);
                sortedTrips.forEach(trip => {
                    const row = document.createElement('tr');
                    row.setAttribute('data-id', trip.id);
                    row.classList.add('trip-row');
                    row.innerHTML = `
                        <td>#${String(trip.id).padStart(3, '0')}</td>
                        <td>${trip.customer_name}</td>
                        <td>${trip.tour_code || 'N/A'}</td>
                        <td>${trip.package_name}</td>
                        <td>${trip.start_date}</td>
                        <td>${trip.end_date}</td>
                        <td><span class=\"status status-${trip.status}\">${trip.status}</span></td>
                        <td class=\"actions\">
                            <a href=\"#\" class=\"btn-edit-trip\" data-id=\"${trip.id}\" title=\"Edit Trip\"><i class=\"fas fa-pencil\"></i></a>
                            <a href=\"#\" class=\"btn-duplicate-trip\" data-id=\"${trip.id}\" title=\"Duplicate Trip\"><i class=\"fas fa-clone\"></i></a>
                            <a href=\"#\" class=\"btn-delete-trip\" data-id=\"${trip.id}\"><i class=\"fas fa-trash\"></i></a>
                        </td>
                    `;
                    row.addEventListener('dblclick', () => { window.location.href = `Itinerary.php?trip_id=${trip.id}`; });
                    tbody.appendChild(row);
                });
            };

                function renderPackages(packages) {
                const tbody = document.querySelector('#packagesTable tbody');
                tbody.innerHTML = '';
                if (!packages || packages.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">No packages found.</td></tr>';
                    return;
                }
                const sortedPackages = [...packages].sort((a,b)=> String(a.name||'').localeCompare(String(b.name||'')));
                sortedPackages.forEach(pkg => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${pkg.id}</td>
                        <td>${pkg.name}</td>
                        <td>${pkg.code || 'N/A'}</td>
                        <td>${pkg.No_of_Days || 'N/A'}</td>
                        <td class="actions">
                            <a href="#" class="btn-duplicate-package" data-id="${pkg.id}" title="Duplicate Package"><i class="fas fa-clone"></i></a>
                            <a href="#" class="btn-create-trip-from-package" data-id="${pkg.id}" title="Create Trip from Package"><i class="fas fa-route"></i></a>
                            <a href="#" class="btn-delete-package" data-id="${pkg.id}" title="Delete Package"><i class="fas fa-trash"></i></a>
                        </td>
                    `;
                    row.classList.add('package-row');
                    row.setAttribute('data-id', pkg.id);
                    tbody.appendChild(row);
                });
            };

            function renderHotels(hotels) {
                const tbody = document.querySelector('#hotelsTable tbody');
                tbody.innerHTML = '';
                if (!hotels || hotels.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">No hotels found.</td></tr>';
                    return;
                }
                const sortedHotels = [...hotels].sort((a,b)=> String(a.name||'').localeCompare(String(b.name||'')));
                sortedHotels.forEach(hotel => {
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

            function renderVehicles(vehicles) {
                const tbody = document.querySelector('#vehiclesTable tbody');
                tbody.innerHTML = '';
                if (!vehicles || vehicles.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">No vehicles found.</td></tr>';
                    return;
                }
                const sortedVehicles = [...vehicles].sort((a,b)=> String(a.vehicle_name||'').localeCompare(String(b.vehicle_name||'')));
                sortedVehicles.forEach(vehicle => {
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
            
            function renderGuides(guides) {
                const tbody = document.querySelector('#guidesTable tbody');
                tbody.innerHTML = '';
                if (!guides || guides.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">No guides found.</td></tr>';
                    return;
                }
                const sortedGuides = [...guides].sort((a,b)=> String(a.name||'').localeCompare(String(b.name||'')));
                sortedGuides.forEach(guide => {
                    const row = document.createElement('tr');
                    if (guide.availability_status) {
                        const statusClass = `row-status-${guide.availability_status.replace(/\s+/g, '-')}`;
                        row.classList.add(statusClass);
                    }
                    row.innerHTML = `
                        <td>${guide.id}</td>
                        <td>${guide.name}</td>
                        <td>${guide.language || 'N/A'}</td>
                        <td>${guide.email || 'N/A'}</td>
                        <td>${guide.availability_status || 'N/A'}</td>
                        <td class="actions">
                            <a href="#" class="btn-edit-guide" data-id="${guide.id}"><i class="fas fa-pencil"></i></a>
                            <a href="#" class="btn-delete-guide" data-id="${guide.id}"><i class="fas fa-trash"></i></a>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            };
            
            function renderHotelRecords(records) {
                const container = document.querySelector('#hotelRecordsContainer');
                container.innerHTML = '';
                
                if (!records || records.length === 0) {
                    container.innerHTML = '<div class="no-records">No hotel booking records found.</div>';
                    return;
                }
                
                // Group records by hotel name
                const groupedByHotel = records.reduce((groups, record) => {
                    const hotelName = record.hotel_name;
                    if (!groups[hotelName]) {
                        groups[hotelName] = [];
                    }
                    groups[hotelName].push(record);
                    return groups;
                }, {});
                
                // Render each hotel group
                Object.keys(groupedByHotel).sort().forEach(hotelName => {
                    const hotelGroup = document.createElement('div');
                    hotelGroup.className = 'hotel-group';
                    
                    const hotelHeader = document.createElement('div');
                    hotelHeader.className = 'hotel-header';
                    hotelHeader.innerHTML = `
                        <i class="fas fa-hotel"></i>
                        ${hotelName}
                        <span style="margin-left: auto; font-size: 0.9rem; opacity: 0.9;">${groupedByHotel[hotelName].length} booking(s)</span>
                    `;
                    
                    const hotelBookings = document.createElement('div');
                    hotelBookings.className = 'hotel-bookings';
                    
                    groupedByHotel[hotelName].forEach(record => {
                        // Calculate duration
                        const checkIn = new Date(record.check_in_date);
                        const checkOut = new Date(record.check_out_date);
                        const duration = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24)) + 1;
                        
                        // Format room details
                        let roomDetails = 'No rooms specified';
                        if (record.room_details) {
                            try {
                                const roomData = JSON.parse(record.room_details);
                                const roomSummary = [];
                                if (roomData.double > 0) roomSummary.push(`${roomData.double} Double`);
                                if (roomData.twin > 0) roomSummary.push(`${roomData.twin} Twin`);
                                if (roomData.single > 0) roomSummary.push(`${roomData.single} Single`);
                                if (roomData.triple > 0) roomSummary.push(`${roomData.triple} Triple`);
                                roomDetails = roomSummary.length > 0 ? roomSummary.join(', ') : 'No rooms specified';
                            } catch (e) {
                                roomDetails = record.room_details || 'No rooms specified';
                            }
                        }
                        
                        // Get tour code
                        const tourCode = record.tour_code || 'No Code';
                        
                        const bookingItem = document.createElement('div');
                        bookingItem.className = 'booking-item';
                        const informed = Number(record.hotel_informed) === 1;
                        const informedPill = `<span class="status" style="background: ${informed ? '#dcfce7' : '#fee2e2'}; color: ${informed ? '#166534' : '#991b1b'};">${informed ? 'Informed' : 'Uninformed'}</span>`;
                        bookingItem.innerHTML = `
                            <div class="booking-main">
                                <div class="booking-title">
                                    ${record.guest_name} | Tour: ${tourCode}
                                </div>
                                <div class="booking-details">
                                    <span><i class="fas fa-calendar-alt"></i> ${record.check_in_date} to ${record.check_out_date}</span>
                                    <span><i class="fas fa-bed"></i> ${roomDetails}</span>
                                </div>
                            </div>
                            <div class="booking-meta">
                                <span class="booking-duration">${duration} day${duration !== 1 ? 's' : ''}</span>
                                ${informedPill}
                                <span class="status status-${record.status}">${record.status}</span>
                                <div class="booking-actions">
                                    <a href=\"Itinerary.php?trip_id=${record.trip_id}&focus_date=${record.check_in_date}\" title=\"Open Itinerary on ${record.check_in_date}\">
                                        <i class="fas fa-route"></i>
                                    </a>
                                </div>
                            </div>
                        `;
                        
                        hotelBookings.appendChild(bookingItem);
                    });
                    
                    hotelGroup.appendChild(hotelHeader);
                    hotelGroup.appendChild(hotelBookings);
                    container.appendChild(hotelGroup);
                });
            };
            
            function renderVehicleRecords(records) {
                const container = document.querySelector('#vehicleRecordsContainer');
                container.innerHTML = '';
                if (!records || records.length === 0) {
                    container.innerHTML = '<div class="no-records">No vehicle assignment records found.</div>';
                    return;
                }
                // Group by vehicle name
                const grouped = records.reduce((g, r) => { const name = r.vehicle_name + (r.number_plate ? ` (${r.number_plate})` : ''); (g[name] = g[name] || []).push(r); return g; }, {});
                Object.keys(grouped).sort().forEach(name => {
                    const groupEl = document.createElement('div'); groupEl.className = 'hotel-group';
                    const header = document.createElement('div'); header.className = 'hotel-header';
                    header.innerHTML = `<i class="fas fa-truck"></i> ${name} <span style="margin-left:auto; font-size:0.9rem; opacity:0.9;">${grouped[name].length} assignment(s)</span>`;
                    const list = document.createElement('div'); list.className = 'hotel-bookings';
                    grouped[name].forEach(rec => {
                        const item = document.createElement('div'); item.className = 'booking-item';
                        const informed = Number(rec.vehicle_informed) === 1;
                        const informedPill = `<span class="status" style="background:${informed ? '#dcfce7' : '#fee2e2'}; color:${informed ? '#166534' : '#991b1b'};">${informed ? 'Informed' : 'Uninformed'}</span>`;
                        item.innerHTML = `
                            <div class="booking-main">
                                <div class="booking-title">${rec.guest_name} | Tour: ${rec.tour_code || 'No Code'}</div>
                                <div class="booking-details">
                                    <span><i class="fas fa-calendar-alt"></i> ${rec.assignment_date}</span>
                                    ${rec.vehicle_email ? `<span><i class=\"fas fa-envelope\"></i> ${rec.vehicle_email}</span>` : ''}
                                </div>
                            </div>
                            <div class="booking-meta">
                                <span class="booking-duration">1 day</span>
                                ${informedPill}
                                <span class="status status-${rec.status}">${rec.status}</span>
                                <div class="booking-actions">
                                    <a href=\"Itinerary.php?trip_id=${rec.trip_id}&focus_date=${rec.assignment_date}\" title=\"Open Itinerary on ${rec.assignment_date}\">
                                </div>
                            </div>`;
                        list.appendChild(item);
                    });
                    groupEl.appendChild(header); groupEl.appendChild(list); container.appendChild(groupEl);
                });
            }

            const renderGuideRecords = (records) => {
                const container = document.querySelector('#guideRecordsContainer');
                container.innerHTML = '';
                
                if (!records || records.length === 0) {
                    container.innerHTML = '<div class="no-records">No guide assignment records found.</div>';
                    return;
                }
                
                // Group records by guide name
                const groupedByGuide = records.reduce((groups, record) => {
                    const guideName = record.guide_name;
                    if (!groups[guideName]) {
                        groups[guideName] = [];
                    }
                    groups[guideName].push(record);
                    return groups;
                }, {});
                
                // Render each guide group
                Object.keys(groupedByGuide).sort().forEach(guideName => {
                    const guideGroup = document.createElement('div');
                    guideGroup.className = 'hotel-group';
                    
                    const guideHeader = document.createElement('div');
                    guideHeader.className = 'hotel-header';
                    guideHeader.innerHTML = `
                        <i class="fas fa-user-tie"></i>
                        ${guideName}
                        <span style="margin-left: auto; font-size: 0.9rem; opacity: 0.9;">${groupedByGuide[guideName].length} assignment(s)</span>
                    `;
                    
                    const guideAssignments = document.createElement('div');
                    guideAssignments.className = 'hotel-bookings';
                    
                    groupedByGuide[guideName].forEach(record => {
                        // Get tour code and guide language info
                        const tourCode = record.tour_code || 'No Code';
                        const languageInfo = record.guide_language ? ` (${record.guide_language})` : '';
                        
                        const assignmentItem = document.createElement('div');
                        assignmentItem.className = 'booking-item';
                        const informed = Number(record.guide_informed) === 1;
                        const informedPill = `<span class="status" style="background: ${informed ? '#dcfce7' : '#fee2e2'}; color: ${informed ? '#166534' : '#991b1b'};">${informed ? 'Informed' : 'Uninformed'}</span>`;
                        assignmentItem.innerHTML = `
                            <div class="booking-main">
                                <div class="booking-title">
                                    ${record.guest_name} | Tour: ${tourCode}${languageInfo}
                                </div>
                                <div class="booking-details">
                                    <span><i class="fas fa-calendar-alt"></i> ${record.assignment_date}</span>
                                    ${record.guide_email ? `<span><i class=\"fas fa-envelope\"></i> ${record.guide_email}</span>` : ''}
                                </div>
                            </div>
                            <div class="booking-meta">
                                <span class="booking-duration">1 day</span>
                                ${informedPill}
                                <span class="status status-${record.status}">${record.status}</span>
                                <div class="booking-actions">
                                    <a href=\"Itinerary.php?trip_id=${record.trip_id}&focus_date=${record.assignment_date}\" title=\"Open Itinerary on ${record.assignment_date}\">
                                        <i class="fas fa-route"></i>
                                    </a>
                                </div>
                            </div>
                        `;
                        
                        guideAssignments.appendChild(assignmentItem);
                    });
                    
                    guideGroup.appendChild(guideHeader);
                    guideGroup.appendChild(guideAssignments);
                    container.appendChild(guideGroup);
                });
            };
            

            document.getElementById('trip_package_id').addEventListener('change', async function() {
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
                calculateDepartureDate();
                // Auto-generate Tour File No from package code (only for new trip)
                const isEditing = !!document.getElementById('tripIdHidden').value;
                const pkgId = this.value;
                if (pkgId){
                  if (!isEditing){
                    try{
                      const r = await fetch(`${API_URL}?action=getNextTourCode&trip_package_id=${pkgId}&_=${Date.now()}`);
                      const j = await r.json();
                      if (j.status==='success' && j.data && j.data.tour_code){
                        document.getElementById('tour_code').value = j.data.tour_code;
                        document.getElementById('modalTitle').textContent = 'New Trip • ' + j.data.tour_code;
                      }
                    }catch(e){ /* ignore */ }
                  }
                  try{
                    const rq = await fetch(`${API_URL}?action=getPackageRequirements&trip_package_id=${pkgId}&_=${Date.now()}`);
                    const jr = await rq.json();
                    if (jr.status==='success'){
                      const d1 = (jr.data||[]).find(r=> String(r.day_number)==='1' && r.hotel_id);
                      firstDayHotelId = d1 && d1.hotel_id ? String(d1.hotel_id) : null;
                      renderArrivalGroups();
                      calculateEndFromStart();
                    }
                  }catch(e){ firstDayHotelId = null; }
                }
            });

            const calculateDepartureDate = () => {
                const packageSelect = document.getElementById('trip_package_id');
                const arrivalInput = document.getElementById('arrival_date');
                const departureInput = document.getElementById('departure_date');
                if (!arrivalInput || !departureInput || !packageSelect) return;

                const selectedOption = packageSelect.options[packageSelect.selectedIndex];
                const rawDays = parseInt(selectedOption?.dataset?.days ?? '0', 10);
                const arrivalVal = arrivalInput.value;

                if (!arrivalVal) { departureInput.value = ''; const ed = document.getElementById('end_date'); if (ed) ed.value = ''; return; }

                const start = new Date(arrivalVal + 'T00:00:00');
                const end = new Date(start.getTime());
                const durationDays = (!isNaN(rawDays) && rawDays > 0) ? rawDays : 2; // default to 2-day trip
                end.setDate(end.getDate() + durationDays - 1);

                const year = end.getFullYear();
                const month = String(end.getMonth() + 1).padStart(2, '0');
                const day = String(end.getDate()).padStart(2, '0');
                const endStr = `${year}-${month}-${day}`;
                departureInput.value = endStr;
                const ed = document.getElementById('end_date'); if (ed) ed.value = endStr;
                const sd = document.getElementById('start_date'); if (sd && arrivalInput.value) sd.value = arrivalInput.value;
            };
            const calculateEndFromStart = () => {
                const sd = document.getElementById('start_date')?.value||'';
                const pkgSel = document.getElementById('trip_package_id');
                const days = parseInt(pkgSel?.options[pkgSel.selectedIndex]?.dataset?.days||'0',10) || 0;
                if (!sd || days<=0) return;
                const end = new Date(sd + 'T00:00:00'); end.setDate(end.getDate()+days-1);
                const y=end.getFullYear(), m=String(end.getMonth()+1).padStart(2,'0'), d=String(end.getDate()).padStart(2,'0');
                const endStr = `${y}-${m}-${d}`;
                const ed = document.getElementById('end_date'); if (ed) ed.value = endStr;
                const dep = document.getElementById('departure_date'); if (dep) dep.value = endStr;
            };

            function updateDayBadge(inputId, badgeId){ const el = document.getElementById(inputId); const b = document.getElementById(badgeId); if (!el || !b) return; const v = el.value; if (!v){ b.textContent='--'; return; } const d = new Date(v+'T00:00:00'); b.textContent = String(d.getDate()).padStart(2,'0'); }
            const arrivalDateEl = document.getElementById('arrival_date');
            if (arrivalDateEl) {
                arrivalDateEl.addEventListener('change', ()=>{ calculateDepartureDate(); updateDayBadge('arrival_date','arrivalDayBadge'); });
                updateDayBadge('arrival_date','arrivalDayBadge');
            }
            const startDateEl = document.getElementById('start_date');
            if (startDateEl) {
                startDateEl.addEventListener('change', ()=>{
                    const a = document.getElementById('arrival_date'); if (a) { a.value = startDateEl.value; calculateDepartureDate(); }
                    // Keep all arrival groups at start_date + 1
                    const newDate = dayAfterStart();
                    tripArrivalsState = (tripArrivalsState||[]).map(g=> ({...g, arrival_date: newDate}));
                    renderArrivalGroups();
                    updateNamesPool();
                    calculateEndFromStart();
                });
            }
            const endDateEl = document.getElementById('end_date');
            if (endDateEl) {
                endDateEl.addEventListener('change', ()=>{
                    const d = document.getElementById('departure_date'); if (d) d.value = endDateEl.value;
                });
            }
            
            // --- Guest counts and names ---
            let guestState = { couples_count: 0, singles_count: 0, couples: [], singles: [] };
            function applyPrebookingTemplate(){
                const bs = document.getElementById('booking_status');
                if (!bs || bs.value !== 'Pre-Booking') return;
                const ccEl = document.getElementById('couples_count');
                const scEl = document.getElementById('singles_count');
                if (ccEl) ccEl.value = 5; if (scEl) scEl.value = 4;
                updateCounterDisplays();
                renderGuestNameInputs();
                // Fill default names: Couples A-E (A1,A2 ... E1,E2), Singles S1..S4
                const inner = document.getElementById('guestNamesInner'); if (!inner) return;
                const letters = ['A','B','C','D','E'];
                const coupleRows = inner.querySelectorAll('[data-role="couple-row"]');
                coupleRows.forEach((row, idx)=>{
                    const L = letters[idx] || String.fromCharCode(65+idx);
                    const a = row.querySelector('input[data-k="name1"]'); const b = row.querySelector('input[data-k="name2"]');
                    if (a) a.value = `${L}1`; if (b) b.value = `${L}2`;
                });
                const singleRows = inner.querySelectorAll('[data-role="single-row"]');
                singleRows.forEach((row, idx)=>{ const a = row.querySelector('input[data-k="name1"]'); if (a) a.value = `S${idx+1}`; });
                updateNamesPool();
                updateTotalPax();
            }
            function updateTotalPax(){
                const cc = parseInt(document.getElementById('couples_count')?.value||0, 10) || 0;
                const sc = parseInt(document.getElementById('singles_count')?.value||0, 10) || 0;
                const total = (cc*2) + sc;
                const tp = document.getElementById('total_pax'); if (tp) tp.value = String(total);
            }
            function updateCounterDisplays(){
                const cc = document.getElementById('couples_count');
                const sc = document.getElementById('singles_count');
                const ccSpan = document.querySelector('[data-for="couples_count"]'); if (ccSpan) ccSpan.textContent = String(cc ? cc.value : 0);
                const scSpan = document.querySelector('[data-for="singles_count"]'); if (scSpan) scSpan.textContent = String(sc ? sc.value : 0);
            }
            function renderGuestNameInputs(){
                const inner = document.getElementById('guestNamesInner'); if (!inner) return;
                const cc = parseInt(document.getElementById('couples_count')?.value||0, 10) || 0;
                const sc = parseInt(document.getElementById('singles_count')?.value||0, 10) || 0;
                // preserve existing values
                const curCouples = [];
                inner.querySelectorAll('[data-role="couple-row"]').forEach(row=>{
                    const a = row.querySelector('input[data-k="name1"]').value;
                    const b = row.querySelector('input[data-k="name2"]').value;
                    curCouples.push([a,b]);
                });
                const curSingles = [];
                inner.querySelectorAll('[data-role="single-row"]').forEach(row=>{
                    const a = row.querySelector('input[data-k="name1"]').value; curSingles.push(a);
                });
                guestState.couples = curCouples; guestState.singles = curSingles;
                inner.innerHTML = '';
                // Couples section
                const cWrap = document.createElement('div');
                cWrap.innerHTML = '<strong>Couples</strong>';
                for (let i=0;i<cc;i++){
                    const row = document.createElement('div'); row.setAttribute('data-role','couple-row'); row.style.cssText='display:grid; grid-template-columns:1fr 1fr; gap:8px; margin:6px 0;';
                    const v1 = guestState.couples[i]?.[0] || '';
                    const v2 = guestState.couples[i]?.[1] || '';
                    row.innerHTML = `<input type="text" data-k="name1" placeholder="Name A (${i+1})" value="${v1}"><input type="text" data-k="name2" placeholder="Name B (${i+1})" value="${v2}">`;
                    cWrap.appendChild(row);
                }
                inner.appendChild(cWrap);
                // Singles section
                const sWrap = document.createElement('div');
                sWrap.style.marginTop = '10px';
                sWrap.innerHTML = '<strong>Singles</strong>';
                for (let j=0;j<sc;j++){
                    const row = document.createElement('div'); row.setAttribute('data-role','single-row'); row.style.cssText='margin:6px 0;';
                    const v = guestState.singles[j] || '';
                    row.innerHTML = `<input type="text" data-k="name1" placeholder="Name ${j+1}" value="${v}">`;
                    sWrap.appendChild(row);
                }
                inner.appendChild(sWrap);
                // Update pool as names change
                inner.querySelectorAll('input[data-k]').forEach(inp=>{
                    inp.addEventListener('input', ()=>{ updateNamesPool(); updateDepNamesPool(); });
                });
                updateNamesPool(); updateDepNamesPool();
            }
            function collectGuestData(){
                const inner = document.getElementById('guestNamesInner'); if (!inner) return { couples: [], singles: [] };
                const couples = []; const singles = [];
                inner.querySelectorAll('[data-role="couple-row"]').forEach(row=>{
                    const a = row.querySelector('input[data-k="name1"]').value.trim();
                    const b = row.querySelector('input[data-k="name2"]').value.trim();
                    couples.push([a,b]);
                });
                inner.querySelectorAll('[data-role="single-row"]').forEach(row=>{
                    const a = row.querySelector('input[data-k="name1"]').value.trim(); singles.push(a);
                });
                return { couples, singles };
            }
            async function fetchGuestsForTrip(tripId){
                try{
                    const r = await fetch(`${API_URL}?action=getTripGuests&trip_id=${tripId}&_=${Date.now()}`);
                    const j = await r.json();
                    if (j.status==='success' && j.data){
                        const cc = j.data.couples?.length||0; const sc = j.data.singles?.length||0;
                        const ccEl = document.getElementById('couples_count'); const scEl = document.getElementById('singles_count');
                        if (ccEl) ccEl.value = cc; if (scEl) scEl.value = sc; updateCounterDisplays(); updateTotalPax();
                        renderGuestNameInputs();
                        // Fill values
                        const inner = document.getElementById('guestNamesInner'); if (inner){
                            inner.querySelectorAll('[data-role="couple-row"]').forEach((row, idx)=>{
                                const a = j.data.couples[idx]?.[0]||''; const b=j.data.couples[idx]?.[1]||'';
                                row.querySelector('input[data-k="name1"]').value = a; row.querySelector('input[data-k="name2"]').value = b;
                            });
                            inner.querySelectorAll('[data-role="single-row"]').forEach((row, idx)=>{
                                const a = j.data.singles[idx]||''; row.querySelector('input[data-k="name1"]').value = a;
                            });
                        }
                    }
                }catch(e){ /* ignore */ }
            }
            async function saveGuestsForTrip(tripId){
                const data = collectGuestData();
                try{
                    const resp = await fetch(`${API_URL}?action=saveTripGuests`, { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ trip_id: Number(tripId), couples: data.couples, singles: data.singles }) });
                    const j = await resp.json().catch(()=>null);
                    if (!j || j.status!=='success'){
                        showToast((j&&j.message)||'Failed to save guest names','error');
                    }
                }catch(e){ showToast('Failed to save guest names','error'); }
            }
            document.getElementById('couples_count')?.addEventListener('input', ()=>{ updateCounterDisplays(); renderGuestNameInputs(); updateTotalPax(); });
            document.getElementById('singles_count')?.addEventListener('input', ()=>{ updateCounterDisplays(); renderGuestNameInputs(); updateTotalPax(); });
            document.getElementById('booking_status')?.addEventListener('change', ()=>{ if (document.getElementById('booking_status').value === 'Pre-Booking') applyPrebookingTemplate(); });
            document.addEventListener('click', function(e){
                const btn = e.target.closest('.counter-btn');
                if (!btn) return;
                const id = btn.getAttribute('data-target');
                const delta = parseInt(btn.getAttribute('data-delta')||'0',10);
                const input = document.getElementById(id);
                if (!input) return;
                const current = parseInt(input.value||'0',10) || 0;
                const next = Math.max(0, current + delta);
                input.value = String(next);
                updateCounterDisplays();
                renderGuestNameInputs();
                updateTotalPax();
            });
            
            // --- Modal Handling ---
            const openModal = (modalId) => {
                document.getElementById(modalId).style.display = 'block';
            };

            const closeModal = (modalId) => {
                const el = document.getElementById(modalId);
                if (el) el.style.display = 'none';
            };
            const closeAllModals = () => {
                ['tripModal','packageModal','hotelModal','vehicleModal','guideModal'].forEach(closeModal);
                const mini = document.getElementById('missingAssignModal'); if (mini) mini.style.display = 'none';
            };

            document.querySelectorAll('.close-btn, .btn-cancel').forEach(btn => {
                btn.addEventListener('click', function() {
                    closeModal(this.dataset.modal);
                });
            });

            // Disable closing modals by clicking outside the content
            // window.addEventListener('click', (event) => {
            //     if (event.target.classList.contains('modal')) {
            //         // Do not close on outside click
            //     }
            // });
            // Close any open modal on ESC
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' || e.key === 'Esc') {
                    e.preventDefault();
                    closeAllModals();
                }
            });

            // Populate countries dropdown
            const COUNTRY_LIST = [
              'Afghanistan','Albania','Algeria','Andorra','Angola','Argentina','Armenia','Australia','Austria','Azerbaijan','Bahamas','Bahrain','Bangladesh','Barbados','Belarus','Belgium','Belize','Benin','Bhutan','Bolivia','Bosnia and Herzegovina','Botswana','Brazil','Brunei','Bulgaria','Burkina Faso','Burundi','Cambodia','Cameroon','Canada','Cape Verde','Central African Republic','Chad','Chile','China','Colombia','Comoros','Congo (DRC)','Congo (Republic)','Costa Rica','Cote d\'Ivoire','Croatia','Cuba','Cyprus','Czechia','Denmark','Djibouti','Dominica','Dominican Republic','Ecuador','Egypt','El Salvador','Equatorial Guinea','Eritrea','Estonia','Eswatini','Ethiopia','Fiji','Finland','France','Gabon','Gambia','Georgia','Germany','Ghana','Greece','Grenada','Guatemala','Guinea','Guinea-Bissau','Guyana','Haiti','Honduras','Hungary','Iceland','India','Indonesia','Iran','Iraq','Ireland','Israel','Italy','Jamaica','Japan','Jordan','Kazakhstan','Kenya','Kiribati','Kuwait','Kyrgyzstan','Laos','Latvia','Lebanon','Lesotho','Liberia','Libya','Liechtenstein','Lithuania','Luxembourg','Madagascar','Malawi','Malaysia','Maldives','Mali','Malta','Marshall Islands','Mauritania','Mauritius','Mexico','Micronesia','Moldova','Monaco','Mongolia','Montenegro','Morocco','Mozambique','Myanmar','Namibia','Nauru','Nepal','Netherlands','New Zealand','Nicaragua','Niger','Nigeria','North Korea','North Macedonia','Norway','Oman','Pakistan','Palau','Panama','Papua New Guinea','Paraguay','Peru','Philippines','Poland','Portugal','Qatar','Romania','Russia','Rwanda','Saint Kitts and Nevis','Saint Lucia','Saint Vincent and the Grenadines','Samoa','San Marino','Sao Tome and Principe','Saudi Arabia','Senegal','Serbia','Seychelles','Sierra Leone','Singapore','Slovakia','Slovenia','Solomon Islands','Somalia','South Africa','South Korea','South Sudan','Spain','Sri Lanka','Sudan','Suriname','Sweden','Switzerland','Syria','Taiwan','Tajikistan','Tanzania','Thailand','Timor-Leste','Togo','Tonga','Trinidad and Tobago','Tunisia','Turkey','Turkmenistan','Tuvalu','Uganda','Ukraine','United Arab Emirates','United Kingdom','United States','Uruguay','Uzbekistan','Vanuatu','Vatican City','Venezuela','Vietnam','Yemen','Zambia','Zimbabwe'
            ];
            const countryEl = document.getElementById('country');
            if (countryEl && countryEl.tagName.toLowerCase()==='select'){
              countryEl.innerHTML = '<option value="">Select Country</option>' + COUNTRY_LIST.map(c=>`<option value="${c}">${c}</option>`).join('');
            }

            document.getElementById('addTripBtn').addEventListener('click', () => {
                document.getElementById('tripForm').reset();
                const bs = document.getElementById('booking_status'); if (bs) bs.value = 'Booking';
                document.getElementById('tripIdHidden').value = '';
                document.getElementById('tripIdDisplay').value = '';
                document.getElementById('fileIdGroup').style.display = 'none';
                document.getElementById('modalTitle').textContent = 'Add Trip';
                
                document.getElementById('package_description_container').style.display = 'none';

                // Creation-lite: hide Guest Details and Arrival/Departure sections & controls
                const gdc = document.getElementById('guestDetailsCard'); if (gdc) gdc.style.display = 'none';
                const s2 = document.getElementById('tripStep2'); if (s2) s2.style.display = 'none';
                const s3 = document.getElementById('tripStep3'); if (s3) s3.style.display = 'none';
                const c2 = document.getElementById('controlsStep2'); if (c2) c2.style.display = 'none';
                const c3 = document.getElementById('controlsStep3'); if (c3) c3.style.display = 'none';
                const next1 = document.getElementById('btnStepNext1'); if (next1) next1.style.display = 'none';
                const back1 = document.getElementById('btnStepBack1'); if (back1) back1.style.display = 'none';

                // Reset arrivals state (not used on creation)
                tripArrivalsState = [];
                renderArrivalGroups();
                tripDeparturesState = [];
                renderDepartureGroups();
                // Reset guest state
                const ccEl = document.getElementById('couples_count'); const scEl = document.getElementById('singles_count');
                if (ccEl) ccEl.value = 0; if (scEl) scEl.value = 0; updateCounterDisplays(); updateTotalPax(); renderGuestNameInputs();
                updateCompanyMode();
                setTripStep(1);

                openModal('tripModal');
            });
            document.getElementById('addTripBtn2').addEventListener('click', () => document.getElementById('addTripBtn').click());

            document.getElementById('addPackageBtn').addEventListener('click', () => {
                document.getElementById('packageForm').reset();
                document.getElementById('packageId').value = '';
                document.getElementById('packageModalTitle').textContent = 'Add Package';
                document.getElementById('day_requirements_container').innerHTML = '';
                const dupBanner = document.getElementById('packageDuplicateBanner'); if (dupBanner) dupBanner.style.display = 'none';
                openModal('packageModal');
            });

            document.getElementById('addHotelBtn').addEventListener('click', () => {
                document.getElementById('hotelForm').reset();
                document.getElementById('hotelId').value = '';
                document.getElementById('hotelModalTitle').textContent = 'Add Hotel';
                document.getElementById('hotel_services_provided').value = '';
                document.getElementById('hotel_room_types').value = '';
                ['room_type_double','room_type_twin','room_type_single','room_type_triple'].forEach(id=>{ const el=document.getElementById(id); if(el) el.checked=false; });
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
            const generateDayRequirements = (dayCount) => {
                const container = document.getElementById('day_requirements_container');
                container.innerHTML = '<h4>Day-wise Requirements</h4>';
                
                let hotelOptions = '<option value="">-- No Hotel --</option>';
                hotelsData.forEach(hotel => {
                    hotelOptions += `<option value="${hotel.id}">${hotel.name}</option>`;
                });
                hotelOptions += `<option value="__add__">+ Add new hotel…</option>`;

                for (let i = 1; i <= dayCount; i++) {
                    const dayCard = document.createElement('div');
                    dayCard.className = 'day-requirement-card';
                    dayCard.innerHTML = `
                        <div class="day-header">
                            <i class="fas fa-calendar-day"></i> Day ${i}
                        </div>
                        <div class="day-content">
                            <div class="requirement-grid">
                                <!-- Hotel Section -->
                                <div class="requirement-section">
                                    <h5><i class="fas fa-hotel"></i> Hotel</h5>
                                    <select id="hotel_day_${i}" name="hotel_assignment_${i}" data-day="${i}" class="form-control">
                                        ${hotelOptions}
                                    </select>
                                </div>
                                
                                <!-- Guide Section -->
                                <div class="requirement-section">
                                    <h5><i class="fas fa-user-friends"></i> Guide</h5>
                                    <div class="requirement-checkbox">
                                        <input type="checkbox" id="guide_required_day_${i}" name="guide_required_${i}" value="1">
                                        <label for="guide_required_day_${i}">Guide Required</label>
                                    </div>
                                </div>
                                
                                <!-- Vehicle Section -->
                                <div class="requirement-section">
                                    <h5><i class="fas fa-car"></i> Vehicle</h5>
                                    <div class="requirement-checkbox">
                                        <input type="checkbox" id="vehicle_required_day_${i}" name="vehicle_required_${i}" value="1">
                                        <label for="vehicle_required_day_${i}">Vehicle Required</label>
                                    </div>
                                    <select id="vehicle_type_day_${i}" name="vehicle_type_${i}" class="vehicle-type-select" style="display: none;">
                                        <option value="">Select Vehicle Type</option>
                                        <option value="tour">Tour/Sightseeing</option>
                                        <option value="arrival">Arrival Transfer</option>
                                        <option value="departure">Departure Transfer</option>
                                        <option value="intercity">Intercity Travel</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>

                                <!-- Services Section -->
                                <div class="requirement-section">
                                    <h5><i class="fas fa-utensils"></i> Services</h5>
                                    <div class="requirement-checkbox">
                                        <label><input type="checkbox" id="svc_b_${i}"> Breakfast (B)</label>
                                        <label><input type="checkbox" id="svc_l_${i}"> Lunch (L)</label>
                                        <label><input type="checkbox" id="svc_d_${i}"> Dinner (D)</label>
                                    </div>
                                </div>

                                <!-- Activities/Notes -->
                                <div class="requirement-section" style="grid-column: 1 / -1;">
                                    <h5><i class="fas fa-list-ul"></i> Activities / Notes</h5>
                                    <textarea id="notes_day_${i}" rows="2" placeholder="Describe activities for this day..."></textarea>
                                </div>
                            </div>
                        </div>
                    `;
                    container.appendChild(dayCard);
                    
                    // Add listeners: vehicle checkbox + hotel add
                    const vehicleCheckbox = dayCard.querySelector(`#vehicle_required_day_${i}`);
                    const vehicleTypeSelect = dayCard.querySelector(`#vehicle_type_day_${i}`);
                    if (vehicleCheckbox) {
                      vehicleCheckbox.addEventListener('change', function() {
                          vehicleTypeSelect.style.display = this.checked ? 'block' : 'none';
                          if (!this.checked) {
                              vehicleTypeSelect.value = '';
                          }
                      });
                    }
                    const hotelSel = dayCard.querySelector(`#hotel_day_${i}`);
                    if (hotelSel) {
                      hotelSel.addEventListener('change', function(){
                        if (this.value === '__add__') {
                          // remember target select to set after creation
                          window.__pendingHotelSelectId = this.id;
                          window.__pendingHotelName = null;
                          this.value = '';
                          // open hotel modal
                          document.getElementById('hotelForm').reset();
                          document.getElementById('hotelId').value = '';
                          document.getElementById('hotelModalTitle').textContent = 'Add Hotel';
                          document.getElementById('hotel_services_provided').value = '';
                          openModal('hotelModal');
                        }
                      });
                    }
                }
            };
            
            document.getElementById('package_days').addEventListener('change', function() {
                const days = parseInt(this.value, 10);
                // Capture existing selections to preserve on re-render
                const preserve = {};
                const container = document.getElementById('day_requirements_container');
                if (container) {
                    const cards = container.querySelectorAll('.day-requirement-card');
                    cards.forEach((card, idx) => {
                        const i = idx + 1;
                        const data = {};
                        const h = card.querySelector(`#hotel_day_${i}`); if (h) data.hotel_id = h.value;
                        const g = card.querySelector(`#guide_required_day_${i}`); if (g) data.guide_required = g.checked;
                        const v = card.querySelector(`#vehicle_required_day_${i}`); if (v) data.vehicle_required = v.checked;
                        const vt = card.querySelector(`#vehicle_type_day_${i}`); if (vt) data.vehicle_type = vt.value;
                        const b = card.querySelector(`#svc_b_${i}`); const l = card.querySelector(`#svc_l_${i}`); const d = card.querySelector(`#svc_d_${i}`);
                        data.svc_b = !!(b && b.checked); data.svc_l = !!(l && l.checked); data.svc_d = !!(d && d.checked);
                        const n = card.querySelector(`#notes_day_${i}`); if (n) data.notes = n.value;
                        preserve[i] = data;
                    });
                }
                if (days > 0 && days < 100) { // Safety limit
                    generateDayRequirements(days);
                    // Reapply preserved selections for overlapping day range
                    for (let i=1; i<=days; i++){
                        const d = preserve[i]; if (!d) continue;
                        const h = document.getElementById(`hotel_day_${i}`); if (h && typeof d.hotel_id !== 'undefined') h.value = d.hotel_id;
                        const g = document.getElementById(`guide_required_day_${i}`); if (g && typeof d.guide_required !== 'undefined') g.checked = d.guide_required;
                        const v = document.getElementById(`vehicle_required_day_${i}`); if (v && typeof d.vehicle_required !== 'undefined') { v.checked = d.vehicle_required; const vt = document.getElementById(`vehicle_type_day_${i}`); if (vt) { vt.style.display = v.checked ? 'block' : 'none'; if (typeof d.vehicle_type !== 'undefined') vt.value = d.vehicle_type || ''; } }
                        const b = document.getElementById(`svc_b_${i}`); if (b) b.checked = !!d.svc_b;
                        const l = document.getElementById(`svc_l_${i}`); if (l) l.checked = !!d.svc_l;
                        const di = document.getElementById(`svc_d_${i}`); if (di) di.checked = !!d.svc_d;
                        const n = document.getElementById(`notes_day_${i}`); if (n && typeof d.notes !== 'undefined') n.value = d.notes;
                    }
                } else {
                    document.getElementById('day_requirements_container').innerHTML = '';
                }
            });

            // --- Handle Services Checkboxes ---
            document.getElementById('hotelForm').addEventListener('change', function(e) {
                if (e.target.name && e.target.name.startsWith('service_')) {
                    const services = [];
                    if (document.querySelector('input[name="service_breakfast"]').checked) services.push('B');
                    if (document.querySelector('input[name="service_lunch"]').checked) services.push('L');
                    if (document.querySelector('input[name="service_dinner"]').checked) services.push('D');
                    document.getElementById('hotel_services_provided').value = services.join(', ');
                }
                // Update room types hidden input on any room_type_* change
                if (e.target && e.target.id && e.target.id.startsWith('room_type_')) {
                    const parts = [];
                    if (document.getElementById('room_type_double')?.checked) parts.push('Double');
                    if (document.getElementById('room_type_twin')?.checked) parts.push('Twin');
                    if (document.getElementById('room_type_single')?.checked) parts.push('Single');
                    if (document.getElementById('room_type_triple')?.checked) parts.push('Triple');
                    document.getElementById('hotel_room_types').value = parts.join(', ');
                }
            });

            // --- Form Submissions ---
            const handleFormSubmit = async (form, action, callback) => {
                const formData = new FormData(form);
                try {
                    const response = await fetch(`${API_URL}?action=${action}`, {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        body: formData
                    });
                    const text = await response.text();
                    let result;
                    try { result = JSON.parse(text); } catch(e){
                        showToast(`Invalid server response (showing first 200 chars): ${text.substring(0,200)}`, 'error');
                        return;
                    }
                    if (result.status === 'success') {
                        showToast(result.message, 'success');
                        callback();
                    } else {
                        showToast(result.message || 'Request failed.', 'error');
                    }
                } catch (error) {
                    showToast('An error occurred while saving: ' + error.message, 'error');
                }
            };

            document.getElementById('tripForm').addEventListener('submit', async function(e) {
                e.preventDefault();
                const isUpdate = !!document.getElementById('tripIdHidden').value;
                const action = isUpdate ? 'updateTrip' : 'addTrip';
                const formData = new FormData(this);
                // Ensure dates exist
                try {
                    let sd = formData.get('start_date');
                    let ed = formData.get('end_date');
                    const arrInput = document.getElementById('arrival_date');
                    const pkgSel = document.getElementById('trip_package_id');
                    const pkgDays = parseInt(pkgSel?.options[pkgSel.selectedIndex]?.dataset?.days||'0',10) || 0;
                    // Earliest group date
                    const grpDates = tripArrivalsState.map(a=>a.arrival_date).filter(Boolean).sort();
                    const earliest = grpDates.length ? grpDates[0] : (arrInput?.value||'');
                    if (!sd && earliest){ formData.set('start_date', earliest); sd = earliest; }
                    if (!ed){
                        if (sd && pkgDays>0){
                            const d = new Date(sd + 'T00:00:00'); d.setDate(d.getDate() + Math.max(1,pkgDays) - 1);
                            const y=d.getFullYear(), m=String(d.getMonth()+1).padStart(2,'0'), da=String(d.getDate()).padStart(2,'0');
                            formData.set('end_date', `${y}-${m}-${da}`);
                        } else if (sd){ formData.set('end_date', sd); }
                    }
                } catch(_){}
                try {
                    const resp = await fetch(`${API_URL}?action=${action}`, { method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'}, body: formData });
                    const text = await resp.text();
                    let result; try { result = JSON.parse(text); } catch(e){ showToast('Invalid response: '+text.substring(0,120),'error'); return; }
                    if (result.status !== 'success'){ showToast(result.message||'Save failed','error'); return; }
                    // Trip id for arrivals
                    let tripId = document.getElementById('tripIdHidden').value;
                    if (!isUpdate) { tripId = (result.data && result.data.id) ? String(result.data.id) : ''; }
                    if (tripId){
                        // If multi mode, save groups; if single, build one group from single inputs
                        const modeSel = document.getElementById('arrival_mode');
                        if (modeSel && modeSel.value==='single'){
                            const names = getAllGuestNames();
                            const pax = names.length;
                            const one = [{ arrival_date: document.getElementById('arrival_date')?.value||'', arrival_time: document.getElementById('arrival_time')?.value||'', flight_no: document.getElementById('arrival_flight')?.value||'', pax_count: pax, pickup_location: names.join('\n'), drop_hotel_id:'', vehicle_id:'', guide_id:'', notes:'', vehicle_informed:0, guide_informed:0 }];
                            tripArrivalsState = one;
                        }
                        await saveArrivalsForTrip(tripId);
                        await saveDeparturesForTrip(tripId);
                        await saveGuestsForTrip(tripId);
                    }
                    showToast(result.message,'success');
                    closeModal('tripModal');
                    fetchTrips();
                    fetchPackages();
                } catch (err) {
                    showToast('Error saving trip: '+err.message,'error');
                }
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
                    day_requirements: {}
                };
                
                // Collect day-wise requirements
                const dayCount = parseInt(document.getElementById('package_days').value, 10);
                for (let i = 1; i <= dayCount; i++) {
                    const hotelSelect = document.getElementById(`hotel_day_${i}`);
                    const guideRequired = document.getElementById(`guide_required_day_${i}`);
                    const vehicleRequired = document.getElementById(`vehicle_required_day_${i}`);
                    const vehicleType = document.getElementById(`vehicle_type_day_${i}`);
                    const svcB = document.getElementById(`svc_b_${i}`);
                    const svcL = document.getElementById(`svc_l_${i}`);
                    const svcD = document.getElementById(`svc_d_${i}`);
                    const notes = document.getElementById(`notes_day_${i}`);
                    const services = [];
                    if (svcB && svcB.checked) services.push('B');
                    if (svcL && svcL.checked) services.push('L');
                    if (svcD && svcD.checked) services.push('D');
                    
                    data.day_requirements[i] = {
                        hotel_id: hotelSelect ? hotelSelect.value : null,
                        guide_required: guideRequired ? guideRequired.checked : false,
                        vehicle_required: vehicleRequired ? vehicleRequired.checked : false,
                        vehicle_type: vehicleType ? vehicleType.value : null,
                        day_services: services.join(', '),
                        day_notes: notes ? notes.value : ''
                    };
                }

                try {
                    const response = await fetch(`${API_URL}?action=${action}`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(data)
                    });
                    const result = await response.json();
                    if (result.status === 'success') {
                        showToast(result.message, 'success');
                        const newId = (!packageId && result.data && result.data.id) ? result.data.id : (packageId || null);
                        closeModal('packageModal');
                        await fetchPackages();
                        // After creating a package, ask to create a trip from it via notification
                        if (!packageId && newId) {
                            showActionToast('Package created. Create a trip from it now?', 'Create Trip', () => openTripModalForPackage(newId));
                        }
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
                // capture intended name for selection after adding
                window.__pendingHotelName = (!hotelId) ? (document.getElementById('hotel_name').value||'') : null;
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
            // Duplicate Trip (deep copy)
            async function duplicateTrip(tripId){
              try{
                // Load source
                const itRes = await fetch(`${API_URL}?action=getItinerary&trip_id=${tripId}&_=${Date.now()}`);
                const it = await itRes.json(); if (it.status!=='success') { showToast(it.message||'Failed to load trip','error'); return; }
                const srcTrip = it.data.trip; const srcDays = it.data.itinerary_days||[]; const srcArrivals = it.data.arrivals||[];
                // Guests
                let guests = { couples: [], singles: [] };
                try{ const gRes = await fetch(`${API_URL}?action=getTripGuests&trip_id=${tripId}&_=${Date.now()}`); const gJs = await gRes.json(); if (gJs.status==='success') guests = gJs.data||guests; }catch(e){}
                // New tour code
                let tour_code = srcTrip.tour_code;
                try{ const r=await fetch(`${API_URL}?action=getNextTourCode&trip_package_id=${srcTrip.trip_package_id}&_=${Date.now()}`); const j=await r.json(); if (j.status==='success') tour_code=j.data.tour_code; }catch(e){}
                // Create new trip with same fields
                const fd = new FormData();
                fd.set('customer_name', srcTrip.customer_name||'');
                fd.set('tour_code', tour_code||'');
                fd.set('trip_package_id', srcTrip.trip_package_id);
                fd.set('start_date', srcTrip.start_date); fd.set('end_date', srcTrip.end_date);
                fd.set('status', srcTrip.status||'Pending');
                ['company','country','address','passport_no','arrival_date','arrival_time','arrival_flight','departure_date','departure_time','departure_flight','total_pax','couples_count','singles_count','guest_details'].forEach(k=>{ if (typeof srcTrip[k] !== 'undefined' && srcTrip[k] !== null) fd.set(k, srcTrip[k]); });
                const addRes = await fetch(`${API_URL}?action=addTrip`, { method:'POST', body: fd });
                const addJs = await addRes.json(); if (addJs.status!=='success'){ showToast(addJs.message||'Failed to create trip','error'); return; }
                const newId = addJs.data && addJs.data.id; if (!newId){ showToast('No new trip id returned','error'); return; }
                // Copy arrivals
                try{ await fetch(`${API_URL}?action=saveTripArrivals`, { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ trip_id: Number(newId), arrivals: srcArrivals }) }); }catch(e){}
                // Copy departures
                try{ const dRes = await fetch(`${API_URL}?action=getTripDepartures&trip_id=${tripId}`); const dJs = await dRes.json(); if (dJs.status==='success'){ await fetch(`${API_URL}?action=saveTripDepartures`, { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ trip_id: Number(newId), departures: dJs.data||[] }) }); } }catch(e){}
                // Copy guests
                try{ await fetch(`${API_URL}?action=saveTripGuests`, { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ trip_id: Number(newId), couples: guests.couples||[], singles: guests.singles||[] }) }); }catch(e){}
                // Copy itinerary assignments
                try{
                  const newItR = await fetch(`${API_URL}?action=getItinerary&trip_id=${newId}&_=${Date.now()}`); const newIt = await newItR.json();
                  if (newIt.status==='success'){
                    const newDays = newIt.data.itinerary_days||[];
                    const payload = [];
                    for (let i=0;i<Math.min(srcDays.length, newDays.length);i++){
                      const s = srcDays[i]; const n = newDays[i];
                      payload.push({ id: n.id, guide_id: s.guide_id||null, vehicle_id: s.vehicle_id||null, hotel_id: s.hotel_id||null, room_type_data: s.room_type_data||null, guide_informed: s.guide_informed?1:0, vehicle_informed: s.vehicle_informed?1:0, hotel_informed: s.hotel_informed?1:0, notes: s.notes||'', services_provided: s.services_provided||'' });
                    }
                    if (payload.length){ await fetch(`${API_URL}?action=updateItinerary`, { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ itinerary_days: payload }) }); }
                  }
                }catch(e){}
                showToast(`Trip duplicated as ${tour_code}`,'success');
                fetchTrips();
              }catch(err){ showToast('Duplicate failed: '+err.message,'error'); }
            }

            // Missing assignment wizard (kept for other flows)
            let wizardState = { type:'rooms', items:[], index:0, tripId:null, days:[], changes:{} };
            const missingModal = document.getElementById('missingAssignModal');
            const missingTitle = document.getElementById('missingAssignTitle');
            const missingInfo = document.getElementById('missingAssignInfo');
            const missingLabel = document.getElementById('missingAssignLabel');
            const missingHint = document.getElementById('missingAssignHint');
            const missingSelect = document.getElementById('missingAssignSelect');
            const missingSelectGroup = document.getElementById('missingAssignSelectGroup');
            const roomsGroup = document.getElementById('missingRoomsContainer');
            const servicesGroup = document.getElementById('missingServicesContainer');
            const rDouble = document.getElementById('room_double');
            const rTwin = document.getElementById('room_twin');
            const rSingle = document.getElementById('room_single');
            const rTriple = document.getElementById('room_triple');
            const svcB = document.getElementById('svc_b');
            const svcL = document.getElementById('svc_l');
            const svcD = document.getElementById('svc_d');
            document.getElementById('missingAssignClose').addEventListener('click', () => missingModal.style.display='none');
            document.getElementById('missingAssignSkip').addEventListener('click', () => { wizardState.index++; showWizardStep(); });
            document.getElementById('missingAssignSave').addEventListener('click', () => { saveWizardSelection(); });

            const openWizard = () => missingModal.style.display = 'flex';
            const closeWizard = () => missingModal.style.display = 'none';

            const fetchItineraryFor = async (tripId) => {
              const cacheBuster = Date.now();
              const res = await fetch(`${API_URL}?action=getItinerary&trip_id=${tripId}&_=${cacheBuster}`);
              const json = await res.json();
              if (json.status !== 'success') throw new Error(json.message||'Failed to fetch itinerary');
              const trip = json.data.trip;
              const days = json.data.itinerary_days;
              let reqByDay = {};
              try {
                if (trip && trip.trip_package_id){
                  const r = await fetch(`${API_URL}?action=getPackageRequirements&trip_package_id=${trip.trip_package_id}&_=${Date.now()}`);
                  const jr = await r.json();
                  if (jr.status==='success'){
                    (jr.data||[]).forEach(req => { reqByDay[req.day_number] = req; });
                  }
                }
              } catch (e) { /* ignore */ }
              return { trip, days, reqByDay };
            };

            function parseRoomData(rd){
              try { const j = rd && rd !== 'null' ? JSON.parse(rd) : {}; return {double:+(j.double||0),twin:+(j.twin||0),single:+(j.single||0),triple:+(j.triple||0)}; } catch(e){ return {double:0,twin:0,single:0,triple:0}; }
            }

            function roomsAreMissing(day){
              const q = parseRoomData(day.room_type_data);
              return (q.double + q.twin + q.single + q.triple) === 0;
            }

            function collectMissingFromDays(days, type){
              const items = [];
              days.forEach((d, idx) => {
                const dayIndex = idx + 1;
                const req = wizardState.requirementsByDay?.[dayIndex];
                if (type==='hotel'){
                  if (!d.hotel_id || d.hotel_id===0 || String(d.hotel_id)==='0') items.push({day:d, dayIndex});
                }
                if (type==='rooms'){
                  const hasHotel = d.hotel_id && String(d.hotel_id) !== '0';
                  if (hasHotel && roomsAreMissing(d)) items.push({day:d, dayIndex});
                }
                if (type==='guide'){
                  const guideRequired = !req || String(req.guide_required)==='1' || req.guide_required===1;
                  if (guideRequired && (!d.guide_id || d.guide_id===0 || String(d.guide_id)==='0')) items.push({day:d, dayIndex});
                }
                if (type==='vehicle'){
                  const vehicleRequired = !req || String(req.vehicle_required)==='1' || req.vehicle_required===1;
                  if (vehicleRequired && (!d.vehicle_id || d.vehicle_id===0 || String(d.vehicle_id)==='0')) items.push({day:d, dayIndex});
                }
                if (type==='services'){
                  const hasHotel = d.hotel_id && String(d.hotel_id) !== '0';
                  const hasServices = (d.services_provided||'').trim().length>0;
                  if (hasHotel && !hasServices) items.push({day:d, dayIndex});
                }
              });
              return items;
            }

            function renderWizard(type, it){
              servicesGroup.style.display = 'none';
              if (type==='rooms'){
                missingTitle.textContent = 'Enter Room Quantities';
                missingLabel.textContent = 'Rooms';
                const q = parseRoomData(it.day.room_type_data);
                rDouble.value = q.double||0; rTwin.value=q.twin||0; rSingle.value=q.single||0; rTriple.value=q.triple||0;
                missingHint.textContent = 'Provide room counts for this day';
                roomsGroup.style.display = 'block';
                missingSelectGroup.style.display = 'none';
              } else if (type==='hotel') {
                let opts = '<option value=\"\">Not assigned</option>';
                hotelsData.forEach(h => { const sel=(String(h.id)===String(it.day.hotel_id))?'selected':''; opts+=`<option value=\"${h.id}\" ${sel}>${h.name}</option>`; });
                missingSelect.innerHTML = opts;
                missingTitle.textContent = 'Assign Hotel';
                missingLabel.textContent = 'Select Hotel';
                missingHint.textContent = 'Choose a hotel for this day';
                roomsGroup.style.display = 'none';
                servicesGroup.style.display = 'none';
                missingSelectGroup.style.display = 'block';
              } else if (type==='guide') {
                let opts = '<option value=\"\">Not assigned</option>';
                guidesData.forEach(g => { const sel=(String(g.id)===String(it.day.guide_id))?'selected':''; opts+=`<option value=\"${g.id}\" ${sel}>${g.name}${g.language?` (${g.language})`:''}</option>`; });
                missingSelect.innerHTML = opts;
                missingTitle.textContent = 'Assign Guide';
                missingLabel.textContent = 'Select Guide';
                missingHint.textContent = 'Choose a guide for this day';
                roomsGroup.style.display = 'none';
                missingSelectGroup.style.display = 'block';
              } else if (type==='vehicle') {
                let opts = '<option value=\"\">Not assigned</option>';
                vehiclesData.forEach(v => { const plate=v.number_plate?` (${v.number_plate})`:''; const sel=(String(v.id)===String(it.day.vehicle_id))?'selected':''; opts+=`<option value=\"${v.id}\" ${sel}>${v.vehicle_name}${plate}</option>`; });
                missingSelect.innerHTML = opts;
                missingTitle.textContent = 'Assign Vehicle';
                missingLabel.textContent = 'Select Vehicle';
                missingHint.textContent = 'Choose a vehicle for this day';
                roomsGroup.style.display = 'none';
                missingSelectGroup.style.display = 'block';
              } else if (type==='services') {
                const val = (it.day.services_provided || '').split(',').map(s=>s.trim());
                svcB.checked = val.includes('B'); svcL.checked = val.includes('L'); svcD.checked = val.includes('D');
                missingTitle.textContent = 'Select Services';
                missingLabel.textContent = 'Meals';
                missingHint.textContent = 'Tick applicable services for this day';
                roomsGroup.style.display = 'none';
                missingSelectGroup.style.display = 'none';
                servicesGroup.style.display = 'block';
              }
            }

            function showWizardStep(){
              if (wizardState.index >= wizardState.items.length){
                if (wizardState.type==='hotel'){
                  wizardState.type='rooms';
                  wizardState.items = collectMissingFromDays(wizardState.days,'rooms');
                  wizardState.index = 0;
                } else if (wizardState.type==='rooms'){
                  wizardState.type='guide';
                  wizardState.items = collectMissingFromDays(wizardState.days,'guide');
                  wizardState.index = 0;
                } else if (wizardState.type==='guide'){
                  wizardState.type='vehicle';
                  wizardState.items = collectMissingFromDays(wizardState.days,'vehicle');
                  wizardState.index = 0;
                } else if (wizardState.type==='vehicle'){
                  wizardState.type='services';
                  wizardState.items = collectMissingFromDays(wizardState.days,'services');
                  wizardState.index = 0;
                } else { closeWizard(); proceedAfterWizard(); return; }
              }
              if (wizardState.items.length===0){ closeWizard(); proceedAfterWizard(); return; }
              const it = wizardState.items[wizardState.index];
              missingInfo.textContent = `Trip #${String(wizardState.tripId).padStart(3,'0')} • Day ${it.dayIndex} (${it.day.day_date})`;
              renderWizard(wizardState.type, it);
              openWizard();
            }

            async function saveWizardSelection(){
              const it = wizardState.items[wizardState.index];
              const val = missingSelect.value;
              const d = it.day;
              if (!wizardState.changes[d.id]) wizardState.changes[d.id] = { id: d.id, guide_id: d.guide_id, vehicle_id: d.vehicle_id, hotel_id: d.hotel_id, notes: d.notes||'', services_provided: d.services_provided||'', room_type_data: d.room_type_data||null };
              if (wizardState.type==='hotel'){
                if (!val){ alert('Please select a hotel.'); return; }
                wizardState.changes[d.id].hotel_id = val || null;
                // reflect change so subsequent steps see hotel
                it.day.hotel_id = val;
              } else if (wizardState.type==='rooms'){
                const q = { double: parseInt(rDouble.value||0), twin: parseInt(rTwin.value||0), single: parseInt(rSingle.value||0), triple: parseInt(rTriple.value||0) };
                if ((q.double+q.twin+q.single+q.triple)===0){ alert('Please enter at least one room.'); return; }
                wizardState.changes[d.id].room_type_data = JSON.stringify(q);
                it.day.room_type_data = wizardState.changes[d.id].room_type_data;
              } else if (wizardState.type==='guide') {
                if (!val){ alert('Please select a guide.'); return; }
                wizardState.changes[d.id].guide_id = val || null;
                it.day.guide_id = val;
              } else if (wizardState.type==='vehicle') {
                if (!val){ alert('Please select a vehicle.'); return; }
                wizardState.changes[d.id].vehicle_id = val || null;
                it.day.vehicle_id = val;
              } else if (wizardState.type==='services') {
                const svcs = [];
                if (svcB.checked) svcs.push('B'); if (svcL.checked) svcs.push('L'); if (svcD.checked) svcs.push('D');
                if (svcs.length===0){ alert('Please select at least one service (B/L/D).'); return; }
                wizardState.changes[d.id].services_provided = svcs.join(', ');
                it.day.services_provided = wizardState.changes[d.id].services_provided;
              }
              wizardState.index++;
              showWizardStep();
            }

            async function ensureAssignmentsForTrip(tripId){
              // Make sure lists available
              if (!hotelsData.length) await fetchHotels();
              if (!vehiclesData.length) await fetchVehicles();
              if (!guidesData.length) await fetchGuides();
              wizardState.tripId = tripId; wizardState.changes = {};
              const it = await fetchItineraryFor(tripId);
              wizardState.days = it.days;
              wizardState.requirementsByDay = it.reqByDay || {};
              // Auto-assign hotels from package if day has none
              wizardState.days.forEach((d, idx) => {
                const req = wizardState.requirementsByDay[idx+1];
                if (req && req.hotel_id && (!d.hotel_id || String(d.hotel_id)==='0')){
                  if (!wizardState.changes[d.id]) wizardState.changes[d.id] = { id: d.id };
                  wizardState.changes[d.id].hotel_id = req.hotel_id;
                  d.hotel_id = req.hotel_id;
                }
              });
              wizardState.type='hotel'; wizardState.items = collectMissingFromDays(wizardState.days,'hotel'); wizardState.index=0;
              if (wizardState.items.length===0){ wizardState.type='rooms'; wizardState.items = collectMissingFromDays(wizardState.days,'rooms'); wizardState.index=0; }
              if (wizardState.items.length===0){ wizardState.type='guide'; wizardState.items = collectMissingFromDays(wizardState.days,'guide'); wizardState.index=0; }
              if (wizardState.items.length===0){ wizardState.type='vehicle'; wizardState.items = collectMissingFromDays(wizardState.days,'vehicle'); wizardState.index=0; }
              if (wizardState.items.length===0){ wizardState.type='services'; wizardState.items = collectMissingFromDays(wizardState.days,'services'); wizardState.index=0; }
              if (wizardState.items.length===0) return true;
              return new Promise(async (resolve) => {
                proceedAfterWizard = async () => {
                  closeWizard();
                  const changed = Object.values(wizardState.changes);
                  if (changed.length){
                    try {
                      const resp = await fetch(`${API_URL}?action=updateItinerary`, { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ itinerary_days: changed })});
                      const js = await resp.json(); if (js.status!=='success') showToast(js.message||'Failed to save assignments','error'); else showToast('Assignments updated','success');
                    } catch(e){ showToast('Failed to save assignments','error'); }
                  }
                  resolve(true);
                };
                showWizardStep();
              });
            }

            async function parseJsonResponse(resp, label){
              const text = await resp.text();
              try { return JSON.parse(text); } catch(e){
                addEmailStatusItem('error', `${label}: invalid response (showing first 120 chars) -> ${text.substring(0,120)}`);
                throw new Error(`${label} returned non-JSON response`);
              }
            }

            async function sendAllEmailsForTrip(tripId, triggerEl){
              try {
                // Ensure required assignments
                await ensureAssignmentsForTrip(tripId);
                if (triggerEl) { triggerEl.innerHTML = '<i class="fas fa-spinner fa-spin"></i>'; triggerEl.style.pointerEvents = 'none'; }
                openEmailStatusPanel(); clearEmailStatus(); addEmailStatusItem('queued', `Trip #${String(tripId).padStart(3,'0')}: starting bulk emails...`);

                // Guides
                addEmailStatusItem('queued','Guides: sending...');
                let resp = await fetch('../src/services/send_guide_email.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ trip_id: tripId })});
                let result = await parseJsonResponse(resp, 'Guides');
                if (result.messages && result.messages.length) { result.messages.forEach(m => addEmailStatusItem(m.type||'info', '[Guides] ' + (m.text||''))); }
                else { addEmailStatusItem(result.status||'info','[Guides] ' + (result.message||'')); }

                // Hotels
                addEmailStatusItem('queued','Hotels: sending...');
                resp = await fetch('../src/services/send_hotel_email.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ trip_id: tripId })});
                result = await parseJsonResponse(resp, 'Hotels');
                if (result.messages && result.messages.length) { result.messages.forEach(m => addEmailStatusItem(m.type||'info', '[Hotels] ' + (m.text||''))); }
                else { addEmailStatusItem(result.status||'info','[Hotels] ' + (result.message||'')); }

                // Vehicles
                addEmailStatusItem('queued','Vehicles: sending...');
                resp = await fetch('../src/services/send_vehicle_email.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ trip_id: tripId })});
                result = await parseJsonResponse(resp, 'Vehicles');
                if (result.messages && result.messages.length) { result.messages.forEach(m => addEmailStatusItem(m.type||'info', '[Vehicles] ' + (m.text||''))); }
                else { addEmailStatusItem(result.status||'info','[Vehicles] ' + (result.message||'')); }

                addEmailStatusItem('sent', 'Bulk emails completed.');
                showToast('All emails processed.','success');
                fetchTrips();
              } catch (err){
                addEmailStatusItem('error','Bulk email failed: ' + err.message);
                showToast('Bulk email failed: ' + err.message, 'error');
              } finally {
                if (triggerEl) { triggerEl.innerHTML = '<i class="fas fa-envelope"></i>'; triggerEl.style.pointerEvents = ''; }
              }
            }



            let tripArrivalsState = [];
            let tripDeparturesState = [];
            let namesPoolVisible = true;
            let firstDayHotelId = null;
            let itineraryDaysCache = [];
            let selectedArrivalIndex = null;
            let selectedDepartureIndex = null;
            function getHotelOptionsHTML(selected){ return ['<option value="">--</option>'].concat((hotelsData||[]).map(h=>`<option value="${h.id}" ${String(selected)===String(h.id)?'selected':''}>${h.name}</option>`)).join(''); }
            function getVehicleOptionsHTML(selected){ return ['<option value="">--</option>'].concat((vehiclesData||[]).map(v=>`<option value="${v.id}" ${String(selected)===String(v.id)?'selected':''}>${v.vehicle_name}${v.number_plate?` (${v.number_plate})`:''}</option>`)).join(''); }
            function getGuideOptionsHTML(selected){ return ['<option value="">--</option>'].concat((guidesData||[]).map(g=>`<option value="${g.id}" ${String(selected)===String(g.id)?'selected':''}>${g.name}${g.language?` (${g.language})`:''}</option>`)).join(''); }
            function getAllGuestUnits(){
                const units = [];
                // Couples
                document.querySelectorAll('#guestNamesInner [data-role="couple-row"]').forEach((row, idx)=>{
                    const a = (row.querySelector('input[data-k="name1"]').value||'').trim();
                    const b = (row.querySelector('input[data-k="name2"]').value||'').trim();
                    if (a || b) units.push({ type:'couple', members: [a,b].filter(Boolean), label: [a,b].filter(Boolean).join(' & ') + ' (Couple)' });
                });
                // Singles
                document.querySelectorAll('#guestNamesInner [data-role="single-row"]').forEach((row, idx)=>{
                    const s = (row.querySelector('input[data-k="name1"]').value||'').trim();
                    if (s) units.push({ type:'single', members:[s], label:s });
                });
                return units;
            }
            function getAllGuestNames(){
                return getAllGuestUnits().flatMap(u => u.members);
            }
            function unassignedUnits(){
                const assigned = new Set();
                tripArrivalsState.forEach(g=>{
                    (g.pickup_location||'').split(/\r?\n/).map(s=>s.trim()).filter(Boolean).forEach(n=>assigned.add(n));
                });
                // Hide unit if ANY of its members is already assigned
                return getAllGuestUnits().filter(u=> u.members.every(n=> !assigned.has(n)) );
            }
            function updateNamesPool(){
                const pool = document.getElementById('namesPool'); if (!pool) return;
                const units = unassignedUnits();
                pool.innerHTML = '';
                // Hide entire block if none left
                const poolWrap = document.getElementById('namesPoolContainer');
                if (poolWrap) poolWrap.style.display = units.length ? 'block' : 'none';
                units.forEach(u=>{
                    const chip = document.createElement('div');
                    chip.textContent = u.label; chip.style.cssText = 'padding:6px 10px; border:1px solid #ddd; border-radius:16px; background:#f9fafb; cursor:pointer; user-select:none;';
                    chip.addEventListener('click', ()=>{
                        if (selectedArrivalIndex===null) selectedArrivalIndex = 0;
                        if (selectedArrivalIndex===null || !tripArrivalsState[selectedArrivalIndex]) return;
                        const g = tripArrivalsState[selectedArrivalIndex];
                        const cur = Array.isArray(g.pickup_list) ? [...g.pickup_list] : (g.pickup_location||'').split(/\r?\n/).filter(Boolean);
                        u.members.forEach(n=> { if (!cur.includes(n)) cur.push(n); });
                        g.pickup_list = cur;
                        g.pickup_location = cur.join('\n');
                        g.pax_count = cur.length;
                        renderArrivalGroups(); updateNamesPool(); updateDepNamesPool();
                    });
                    pool.appendChild(chip);
                });
                document.getElementById('namesPoolContainer').style.display = units.length>0 ? 'block' : 'none';
            }
            function unassignedUnitsForDeparture(){
                const assigned = new Set();
                tripDeparturesState.forEach(g=>{
                    (g.pickup_location||'').split(/\r?\n/).map(s=>s.trim()).filter(Boolean).forEach(n=>assigned.add(n));
                });
                return getAllGuestUnits().filter(u=> u.members.every(n=> !assigned.has(n)) );
            }
            function updateDepNamesPool(){
                const pool = document.getElementById('depNamesPool'); const wrap = document.getElementById('depNamesPoolContainer');
                if (!pool || !wrap) return;
                const units = unassignedUnitsForDeparture();
                pool.innerHTML = '';
                wrap.style.display = units.length ? 'block' : 'none';
                units.forEach(u=>{
                    const chip = document.createElement('div');
                    chip.textContent = u.label; chip.style.cssText = 'padding:6px 10px; border:1px solid #ddd; border-radius:16px; background:#f9fafb; cursor:pointer; user-select:none;';
                    chip.addEventListener('click', ()=>{
                        if (selectedDepartureIndex===null) selectedDepartureIndex = 0;
                        if (selectedDepartureIndex===null || !tripDeparturesState[selectedDepartureIndex]) return;
                        const g = tripDeparturesState[selectedDepartureIndex];
                        const cur = Array.isArray(g.pickup_list) ? [...g.pickup_list] : (g.pickup_location||'').split(/\r?\n/).filter(Boolean);
                        u.members.forEach(n=> { if (!cur.includes(n)) cur.push(n); });
                        g.pickup_list = cur;
                        g.pickup_location = cur.join('\n');
                        g.pax_count = cur.length;
                        renderDepartureGroups(); updateDepNamesPool();
                    });
                    pool.appendChild(chip);
                });
            }
            function dayAfterStart(){
                const sd = document.getElementById('start_date')?.value||'';
                if (!sd) return '';
                const d = new Date(sd + 'T00:00:00'); d.setDate(d.getDate()+1); const y=d.getFullYear(), m=String(d.getMonth()+1).padStart(2,'0'), da=String(d.getDate()).padStart(2,'0'); return `${y}-${m}-${da}`;
            }
            function addArrivalGroup(){
                tripArrivalsState.push({ arrival_date: dayAfterStart(), arrival_time:'', flight_no:'', pax_count:0, pickup_location:'', pickup_list:[], drop_hotel_id:'', vehicle_id:'', guide_id:'', notes:'', vehicle_informed:0, guide_informed:0 });
                selectedArrivalIndex = tripArrivalsState.length - 1;
                renderArrivalGroups(); updateNamesPool();
            }
            function renderArrivalGroups(){
                const c = document.getElementById('arrivalGroupsContainer'); if (!c) return; c.innerHTML='';
                if (!tripArrivalsState.length){ c.innerHTML = '<div style="color:#6b7280;">No arrivals added.</div>'; updateNamesPool(); return; }
                tripArrivalsState.forEach((a, idx) => {
                    // Default date to trip start date
                    const startVal = document.getElementById('start_date')?.value||'';
                    if (!a.arrival_date && startVal) a.arrival_date = startVal;
                    // Default drop hotel to nearest to Day 1
                    if (!a.drop_hotel_id || String(a.drop_hotel_id)===''){ a.drop_hotel_id = computeDefaultDropHotelId(a.arrival_date) || firstDayHotelId || ''; }
                    const card = document.createElement('div');
                    card.className = 'requirement-section' + (selectedArrivalIndex===idx? ' selected-arrival' : '');
                    card.style.marginBottom = '10px';
                    card.style.borderWidth = (selectedArrivalIndex===idx? '2px':'1px');
                    card.style.borderColor = (selectedArrivalIndex===idx? '#2563eb':'var(--border-color)');
                    const title = `Arrival A${idx+1}`;
                    const assignedNames = Array.isArray(a.pickup_list) ? a.pickup_list : (a.pickup_location||'').split(/\r?\n/).map(s=>s.trim()).filter(Boolean);
                    a.pickup_list = assignedNames; a.pickup_location = assignedNames.join('\n');
                    card.innerHTML = `
                        <div style="display:flex; align-items:center; justify-content:space-between; gap:8px; margin-bottom:6px;" data-role="header">
                            <h5 style="margin:0; color: var(--text-light);">${title}</h5>
                            <div style="display:flex; gap:6px;">
                                <button type="button" class="btn btn-secondary" data-role="toggle-pool"><i class="fas fa-plus"></i></button>
                                <button type="button" class="btn btn-cancel" data-role="remove">Remove</button>
                            </div>
                        </div>
                        <div class="form-grid" style="grid-template-columns:1fr 1fr 1fr; gap:12px; margin-bottom:8px;">
                            <div class="form-group"><label>Date</label><input type="date" data-k="arrival_date" value="${a.arrival_date||''}"></div>
                            <div class="form-group"><label>Time</label><input type="time" data-k="arrival_time" value="${a.arrival_time||''}"></div>
                            <div class="form-group"><label>Flight</label><input type="text" data-k="flight_no" value="${a.flight_no||''}"></div>
                        </div>
                        <div class=\"form-group\"><label>Assign Guests</label>
                            <div data-role=\"drop\" style=\"min-height:64px; border:2px dashed #d1d5db; border-radius:8px; display:flex; flex-wrap:wrap; gap:8px; align-items:center; justify-content:${assignedNames.length? 'flex-start':'center'}; padding:10px; background:#fff;\">
${assignedNames.length ? assignedNames.map(n => `<span class="chip" data-name="${n}" style="padding:6px 10px; border:1px solid #ddd; border-radius:16px; background:#eef2ff; display:inline-flex; align-items:center; gap:6px;">${n}<button type="button" data-role="unassign" data-name="${n}" style="border:none; background:transparent; color:#6b7280; cursor:pointer;">×</button></span>`).join('') : '<div style="text-align:center; color:#9ca3af;">Click a name on the left to add here</div>'}
                            </div>
                        </div>
                        <div class="form-grid" style="grid-template-columns:1fr 1fr 1fr; gap:12px;">
                            <div class="form-group"><label>Drop Hotel</label><select data-k="drop_hotel_id">${getHotelOptionsHTML(a.drop_hotel_id||'')}</select></div>
                            <div class="form-group"><label>Vehicle</label><select data-k="vehicle_id">${getVehicleOptionsHTML(a.vehicle_id||'')}</select></div>
                            <div class="form-group"><label>Guide</label><select data-k="guide_id">${getGuideOptionsHTML(a.guide_id||'')}</select></div>
                        </div>
                        <div class="form-group"><label>Notes</label><textarea rows="3" data-k="notes" placeholder="Instructions, special requests...">${a.notes||''}</textarea></div>
                        <div style="display:flex; gap:8px; align-items:center;">
                            <label style="display:flex; gap:6px; align-items:center;"><input type="checkbox" data-k="vehicle_informed" ${a.vehicle_informed? 'checked':''}> Vehicle informed</label>
                            <label style="display:flex; gap:6px; align-items:center;"><input type="checkbox" data-k="guide_informed" ${a.guide_informed? 'checked':''}> Guide informed</label>
                        </div>
                    `;
                    // Normalize any malformed data-role attributes in chips due to escaped quotes
                    card.querySelectorAll('[data-role]').forEach(function(el){
                        var v = el.getAttribute('data-role');
                        if (v && v.indexOf('unassign') !== -1) el.setAttribute('data-role','unassign');
                    });
                    card.querySelectorAll('[data-k]').forEach(el=>{
                        el.addEventListener('change', ()=>{
                            const k = el.getAttribute('data-k');
                            let v = (el.type==='checkbox') ? el.checked : el.value;
                            // Vehicle overlap check on same date
                            if (k==='vehicle_id'){
                                const date = (tripArrivalsState[idx].arrival_date||'');
                                if (v){
                                    const conflictIdx = tripArrivalsState.findIndex((g,i)=> i!==idx && g.arrival_date===date && String(g.vehicle_id||'')===String(v));
                                    if (conflictIdx!==-1){
                                        if (!confirm(`This vehicle is already assigned to Arrival A${conflictIdx+1} on ${date}. Allow overlap?`)){
                                            // revert
                                            el.value = tripArrivalsState[idx].vehicle_id || '';
                                            return;
                                        }
                                    }
                                }
                            }
                            // Guide overlap check on same date across arrival groups
                            if (k==='guide_id'){
                                const date = (tripArrivalsState[idx].arrival_date||'');
                                if (v){
                                    const conflictIdx = tripArrivalsState.findIndex((g,i)=> i!==idx && g.arrival_date===date && String(g.guide_id||'')===String(v));
                                    if (conflictIdx!==-1){
                                        if (!confirm(`This guide is already assigned to Arrival A${conflictIdx+1} on ${date}. Allow overlap?`)){
                                            el.value = tripArrivalsState[idx].guide_id || '';
                                            return;
                                        }
                                    }
                                }
                            }
                            tripArrivalsState[idx][k] = v;
                            if (k==='arrival_date') syncArrivalDateFromGroups();
                        });
                    });
                    const drop = card.querySelector('[data-role="drop"]');
                    const choose = ()=>{ selectedArrivalIndex = idx; const cont = document.getElementById('arrivalGroupsContainer'); if (cont) cont.scrollTop = 0; renderArrivalGroups(); updateNamesPool(); };
                    if (drop){ drop.addEventListener('click', choose); }
                    const header = card.querySelector('[data-role="header"]');
                    if (header){ header.addEventListener('click', choose); }
                    card.addEventListener('click', (e)=>{
                        const un = e.target.closest('[data-role="unassign"]');
                        if (un){ e.preventDefault(); const name = un.getAttribute('data-name'); const cur = (Array.isArray(tripArrivalsState[idx].pickup_list)? tripArrivalsState[idx].pickup_list : (tripArrivalsState[idx].pickup_location||'').split(/\r?\n/).filter(Boolean)).filter(n=>n!==name); tripArrivalsState[idx].pickup_list = cur; tripArrivalsState[idx].pickup_location = cur.join('\n'); tripArrivalsState[idx].pax_count = cur.length; renderArrivalGroups(); updateNamesPool(); return; }
                        const rm = e.target.closest('[data-role="remove"]'); if (rm){ tripArrivalsState.splice(idx,1); selectedArrivalIndex = Math.max(0, Math.min((selectedArrivalIndex||0)- (selectedArrivalIndex>idx?1:0), tripArrivalsState.length-1)); renderArrivalGroups(); updateNamesPool(); }
                    });
                    c.appendChild(card);
                });
                updateNamesPool();
            }
            function renderDepartureGroups(){
                const c = document.getElementById('departureGroupsContainer'); if (!c) return; c.innerHTML='';
                if (!tripDeparturesState.length){ c.innerHTML = '<div style="color:#6b7280;">No departures added.</div>'; return; }
                tripDeparturesState.forEach((d, idx) => {
                    const card = document.createElement('div');
                    card.className = 'requirement-section' + (selectedDepartureIndex===idx? ' selected-arrival' : '');
                    card.style.marginBottom = '10px';
                    card.style.borderWidth = (selectedDepartureIndex===idx? '2px':'1px');
                    card.style.borderColor = (selectedDepartureIndex===idx? '#1e40af':'var(--border-color)');
                    const title = `Departure D${idx+1}`;
                    const assignedNames = Array.isArray(d.pickup_list) ? d.pickup_list : (d.pickup_location||'').split(/\r?\n/).map(s=>s.trim()).filter(Boolean);
                    d.pickup_list = assignedNames; d.pickup_location = assignedNames.join('\n');
                    card.innerHTML = `
                        <div style="display:flex; align-items:center; justify-content:space-between; gap:8px; margin-bottom:6px;" data-role="header">
                            <h5 style="margin:0; color: var(--text-light);">${title}</h5>
                            <div style="display:flex; gap:6px;">
                                <button type="button" class="btn btn-cancel" data-role="remove">Remove</button>
                            </div>
                        </div>
                        <div class="form-grid" style="grid-template-columns:1fr 1fr 1fr; gap:12px; margin-bottom:8px;">
                            <div class="form-group"><label>Date</label><input type="date" data-k="departure_date" value="${d.departure_date||''}"></div>
                            <div class="form-group"><label>Time</label><input type="time" data-k="departure_time" value="${d.departure_time||''}"></div>
                            <div class="form-group"><label>Flight</label><input type="text" data-k="flight_no" value="${d.flight_no||''}"></div>
                        </div>
                        <div class=\"form-group\"><label>Assign Guests</label>
                            <div data-role=\"drop\" style=\"min-height:64px; border:2px dashed #d1d5db; border-radius:8px; display:flex; flex-wrap:wrap; gap:8px; align-items:center; justify-content:${assignedNames.length? 'flex-start':'center'}; padding:10px; background:#fff;\">\r
                                ${assignedNames.length ? assignedNames.map(n => `<span class=\"chip\" data-name=\"${n}\" style=\"padding:6px 10px; border:1px solid #ddd; border-radius:16px; background:#eef2ff; display:inline-flex; align-items:center; gap:6px;\">${n}<button type=\"button\" data-role=\"unassign\" data-name=\"${n}\" style=\"border:none; background:transparent; color:#6b7280; cursor:pointer;\">×</button></span>`).join('') : '<div style=\"text-align:center; color:#9ca3af;\">Click a name above to add here</div>'}
                            </div>
                        </div>
                        <div class="form-grid" style="grid-template-columns:1fr 1fr 1fr; gap:12px;">
                            <div class="form-group"><label>From Hotel</label><select data-k="pickup_hotel_id">${getHotelOptionsHTML(d.pickup_hotel_id||'')}</select></div>
                            <div class="form-group"><label>Vehicle</label><select data-k="vehicle_id">${getVehicleOptionsHTML(d.vehicle_id||'')}</select></div>
                            <div class="form-group"><label>Guide</label><select data-k="guide_id">${getGuideOptionsHTML(d.guide_id||'')}</select></div>
                        </div>
                        <div class="form-group"><label>Notes</label><textarea rows="3" data-k="notes" placeholder="Instructions, special requests...">${d.notes||''}</textarea></div>
                        <div style="display:flex; gap:8px; align-items:center;">
                            <label style="display:flex; gap:6px; align-items:center;"><input type="checkbox" data-k="vehicle_informed" ${d.vehicle_informed? 'checked':''}> Vehicle informed</label>
                            <label style="display:flex; gap:6px; align-items:center;"><input type="checkbox" data-k="guide_informed" ${d.guide_informed? 'checked':''}> Guide informed</label>
                        </div>
                    `;
                    card.querySelectorAll('[data-k]').forEach(el=>{
                        el.addEventListener('change', ()=>{
                            const k = el.getAttribute('data-k');
                            const v = (el.type==='checkbox') ? el.checked : el.value;
                            // Departure vehicle overlap on same date across groups
                            if (k==='vehicle_id'){
                                const date = (tripDeparturesState[idx].departure_date||'');
                                if (v){
                                    const conflictIdx = tripDeparturesState.findIndex((g,i)=> i!==idx && g.departure_date===date && String(g.vehicle_id||'')===String(v));
                                    if (conflictIdx!==-1){
                                        if (!confirm(`This vehicle is already assigned to Departure D${conflictIdx+1} on ${date}. Allow overlap?`)){
                                            el.value = tripDeparturesState[idx].vehicle_id || '';
                                            return;
                                        }
                                    }
                                }
                            }
                            // Departure guide overlap on same date across groups
                            if (k==='guide_id'){
                                const date = (tripDeparturesState[idx].departure_date||'');
                                if (v){
                                    const conflictIdx = tripDeparturesState.findIndex((g,i)=> i!==idx && g.departure_date===date && String(g.guide_id||'')===String(v));
                                    if (conflictIdx!==-1){
                                        if (!confirm(`This guide is already assigned to Departure D${conflictIdx+1} on ${date}. Allow overlap?`)){
                                            el.value = tripDeparturesState[idx].guide_id || '';
                                            return;
                                        }
                                    }
                                }
                            }
                            tripDeparturesState[idx][k] = v;
                        });
                    });
                    const drop = card.querySelector('[data-role="drop"]');
                    const choose = ()=>{ selectedDepartureIndex = idx; const cont = document.getElementById('departureGroupsContainer'); if (cont) cont.scrollTop = 0; renderDepartureGroups(); };
                    if (drop){ drop.addEventListener('click', choose); }
                    const header = card.querySelector('[data-role="header"]'); if (header){ header.addEventListener('click', choose); }
                    card.addEventListener('click', (e)=>{
                        const un = e.target.closest('[data-role="unassign"]');
                        if (un){ e.preventDefault(); const name = un.getAttribute('data-name'); const cur = (Array.isArray(tripDeparturesState[idx].pickup_list)? tripDeparturesState[idx].pickup_list : (tripDeparturesState[idx].pickup_location||'').split(/\r?\n/).filter(Boolean)).filter(n=>n!==name); tripDeparturesState[idx].pickup_list = cur; tripDeparturesState[idx].pickup_location = cur.join('\n'); tripDeparturesState[idx].pax_count = cur.length; renderDepartureGroups(); return; }
                        const rm = e.target.closest('[data-role="remove"]'); if (rm){ tripDeparturesState.splice(idx,1); selectedDepartureIndex = Math.max(0, Math.min((selectedDepartureIndex||0)- (selectedDepartureIndex>idx?1:0), tripDeparturesState.length-1)); renderDepartureGroups(); }
                    });
                    c.appendChild(card);
                });
                updateDepNamesPool();
            }
            function cloneDeparturesFromArrivals(){
                const end = document.getElementById('end_date')?.value||'';
                const hotelId = computeDefaultPickupHotelIdForDeparture();
                tripDeparturesState = tripArrivalsState.map(a=>({
                    departure_date: end,
                    departure_time: '',
                    flight_no: '',
                    pax_count: (Array.isArray(a.pickup_list)? a.pickup_list.length : (a.pickup_location||'').split(/\r?\n/).filter(Boolean).length),
                    pickup_location: (Array.isArray(a.pickup_list)? a.pickup_list.join('\n') : (a.pickup_location||'')),
                    pickup_list: (Array.isArray(a.pickup_list)? [...a.pickup_list] : (a.pickup_location||'').split(/\r?\n/).filter(Boolean)),
                    pickup_hotel_id: hotelId,
                    vehicle_id: '',
                    guide_id: '',
                    notes: '',
                    vehicle_informed: 0,
                    guide_informed: 0
                }));
                renderDepartureGroups();
                updateDepNamesPool();
            }
            document.getElementById('sameAsArrivalDepCheckbox')?.addEventListener('change', (e)=>{
                if (e.target.checked){ cloneDeparturesFromArrivals(); }
            });
            document.getElementById('btnAddDepartureGroup')?.addEventListener('click', ()=>{
                tripDeparturesState.push({ departure_date: document.getElementById('end_date')?.value||'', departure_time:'', flight_no:'', pax_count:0, pickup_location:'', pickup_list:[], pickup_hotel_id: computeDefaultPickupHotelIdForDeparture(), vehicle_id:'', guide_id:'', notes:'', vehicle_informed:0, guide_informed:0 });
                selectedDepartureIndex = tripDeparturesState.length - 1;
                renderDepartureGroups();
                updateNamesPool(); updateDepNamesPool();
            });
            async function fetchArrivalsForTrip(tripId){
                try{ const r = await fetch(`${API_URL}?action=getTripArrivals&trip_id=${tripId}`); const j = await r.json(); if (j.status==='success'){ tripArrivalsState = j.data||[]; renderArrivalGroups(); syncArrivalDateFromGroups(); updateNamesPool();
                    // Infer arrival mode from number of groups
                    const modeSel = document.getElementById('arrival_mode'); if (modeSel){ modeSel.value = (tripArrivalsState.length>1)? 'multi' : 'single'; modeSel.dispatchEvent(new Event('change')); }
                } }catch(e){ /* ignore */ }
            }
            async function saveArrivalsForTrip(tripId){
                try{ await fetch(`${API_URL}?action=saveTripArrivals`, { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ trip_id: Number(tripId), arrivals: tripArrivalsState }) }); }catch(e){ /* ignore */ }
            }
            async function fetchDeparturesForTrip(tripId){
                try{ const r = await fetch(`${API_URL}?action=getTripDepartures&trip_id=${tripId}`); const j = await r.json(); if (j.status==='success'){ tripDeparturesState = j.data||[]; renderDepartureGroups(); } }catch(e){ /* ignore */ }
            }
            async function saveDeparturesForTrip(tripId){
                try{ await fetch(`${API_URL}?action=saveTripDepartures`, { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ trip_id: Number(tripId), departures: tripDeparturesState }) }); }catch(e){ /* ignore */ }
            }
            const btnAddArrivalGroup = document.getElementById('btnAddArrivalGroup'); if (btnAddArrivalGroup){ btnAddArrivalGroup.addEventListener('click', ()=>{ addArrivalGroup(); syncArrivalDateFromGroups(); }); }

            function syncArrivalDateFromGroups(){
                // Set the hidden arrival_date field to earliest group date (for single/multi cohesion)
                const input = document.getElementById('arrival_date'); if (!input) return;
                const dates = tripArrivalsState.map(a=>a.arrival_date).filter(Boolean).sort();
                if (dates.length){ input.value = dates[0]; calculateDepartureDate(); }
            }
            function computeDefaultDropHotelId(dateStr){
                if (!itineraryDaysCache || !itineraryDaysCache.length) return firstDayHotelId || '';
                // find exact date, else next available after date
                let exact = itineraryDaysCache.find(d=> d.day_date===dateStr && d.hotel_id);
                if (exact && exact.hotel_id) return String(exact.hotel_id);
                // next after
                const sorted = [...itineraryDaysCache].filter(d=> d.hotel_id).sort((a,b)=> a.day_date.localeCompare(b.day_date));
                for (const d of sorted){ if (d.day_date>=dateStr) return String(d.hotel_id); }
                // fallback first with hotel
                return String((sorted[0]&&sorted[0].hotel_id) || firstDayHotelId || '');
            }
            function computeDefaultPickupHotelIdForDeparture(){
                if (!itineraryDaysCache || !itineraryDaysCache.length) return firstDayHotelId || '';
                const withHotel = itineraryDaysCache.filter(d=> d && d.hotel_id);
                if (withHotel.length===0) return firstDayHotelId || '';
                const lastWithHotel = [...withHotel].sort((a,b)=> a.day_date.localeCompare(b.day_date)).pop();
                if (lastWithHotel && lastWithHotel.hotel_id) return String(lastWithHotel.hotel_id);
                if (withHotel.length>=2) return String(withHotel[withHotel.length-2].hotel_id);
                return firstDayHotelId || '';
            }
            // Arrival mode toggle
            (function(){
                const mode = document.getElementById('arrival_mode');
                const secSingle = document.getElementById('singleArrivalSection');
                const secMulti = document.getElementById('multiArrivalSection');
                if (mode){
                    mode.addEventListener('change', ()=>{
                        const v = mode.value;
                        if (v==='single'){
                            secSingle.style.display='block'; secMulti.style.display='none';
                        } else {
                            secSingle.style.display='none'; secMulti.style.display='block';
                            updateNamesPool();
                        }
                    });
                }
            })();
            function updateCompanyMode(){
                const travel = document.getElementById('travelDetailsCard');
                if (travel) travel.style.display = '';
                // Set selected group to first for assignment convenience
                if (tripArrivalsState.length && selectedArrivalIndex===null){ selectedArrivalIndex = 0; renderArrivalGroups(); updateNamesPool(); }
            }
            document.getElementById('company')?.addEventListener('change', ()=>{ updateCompanyMode(); });
            // Stepper
            let tripCurrentStep = 1;
            function setTripStep(n){
                tripCurrentStep = n;
                const s1 = document.getElementById('tripStep1');
                const s2 = document.getElementById('tripStep2');
                const s3 = document.getElementById('tripStep3');
                if (s1) s1.style.display = (n===1)?'':'none';
                if (s2) s2.style.display = (n===2)?'':'none';
                if (s3) s3.style.display = (n===3)?'':'none';
                const c1 = document.getElementById('controlsStep1');
                const c2 = document.getElementById('controlsStep2');
                const c3 = document.getElementById('controlsStep3');
                if (c1) c1.style.display = (n===1)?'flex':'none';
                if (c2) c2.style.display = (n===2)?'flex':'none';
                if (c3) c3.style.display = (n===3)?'flex':'none';
                const back1 = document.getElementById('btnStepBack1');
                const next1 = document.getElementById('btnStepNext1');
                const save1 = document.getElementById('btnStepSave1');
                const back2 = document.getElementById('btnStepBack2');
                const next2 = document.getElementById('btnStepNext2');
                const save2 = document.getElementById('btnStepSave2');
                const back3 = document.getElementById('btnStepBack3');
                const save3 = document.getElementById('btnStepSave3');
                if (back1) back1.style.display = 'none';
                if (next1) next1.style.display = '';
                if (save1) save1.style.display = '';
                if (back2) back2.style.display = '';
                if (next2) next2.style.display = '';
                if (save2) save2.style.display = '';
                if (back3) back3.style.display = '';
                if (save3) save3.style.display = '';
            }
document.getElementById('btnStepNext')?.addEventListener('click', ()=> { const next = Math.min(3, (tripCurrentStep||1)+1); setTripStep(next); updateDayBadge('arrival_date','arrivalDayBadge'); });
            document.getElementById('btnStepBack')?.addEventListener('click', ()=> setTripStep(1));
            document.getElementById('btnStepNext1')?.addEventListener('click', ()=> { setTripStep(2); updateDayBadge('arrival_date','arrivalDayBadge'); });
            document.getElementById('btnStepBack2')?.addEventListener('click', ()=> setTripStep(1));
            document.getElementById('btnStepNext2')?.addEventListener('click', ()=> { setTripStep(3); const ed = document.getElementById('end_date'); if (ed) { const dep = document.getElementById('departure_date'); if (dep) dep.value = ed.value || ''; } });
            document.getElementById('btnStepBack3')?.addEventListener('click', ()=> setTripStep(2));

            async function populateTripForm(trip){
                document.getElementById('modalTitle').textContent = 'Edit Trip';
                document.getElementById('tripIdHidden').value = trip.id;
                document.getElementById('tripIdDisplay').value = '#' + String(trip.id).padStart(3, '0');
                document.getElementById('fileIdGroup').style.display = 'block';
                document.getElementById('company').value = (trip.company || '');
                const bs = document.getElementById('booking_status'); if (bs) bs.value = 'Booking';
                const gd = document.getElementById('guest_details'); if (gd) gd.value = (trip.guest_details || '');
                const totalPaxEl = document.getElementById('total_pax');
                if (totalPaxEl) { totalPaxEl.value = (trip.total_pax != null ? trip.total_pax : ''); }
                const countryEl = document.getElementById('country');
                if (countryEl && countryEl.tagName.toLowerCase()==='select') countryEl.value = (trip.country || ''); else document.getElementById('country').value = (trip.country || '');
                document.getElementById('tour_code').value = trip.tour_code || '';
                document.getElementById('passport_no').value = (trip.passport_no || '');
                document.getElementById('address').value = (trip.address || '');
                document.getElementById('trip_package_id').value = trip.trip_package_id;
                // Dates
                document.getElementById('start_date').value = (trip.start_date || '');
                document.getElementById('end_date').value = (trip.end_date || '');
                document.getElementById('arrival_date').value = (trip.arrival_date || '');
                document.getElementById('arrival_time').value = (trip.arrival_time || '');
                document.getElementById('arrival_flight').value = (trip.arrival_flight || '');
                const depD = document.getElementById('departure_date'); if (depD) depD.value = (trip.departure_date || '');
                const depT = document.getElementById('departure_time'); if (depT) depT.value = (trip.departure_time || '');
                const depF = document.getElementById('departure_flight'); if (depF) depF.value = (trip.departure_flight || '');
                document.getElementById('status').value = trip.status;
                // Pax breakdown (optional columns)
                const ccEl = document.getElementById('couples_count'); const scEl = document.getElementById('singles_count');
                if (ccEl && typeof trip.couples_count !== 'undefined') ccEl.value = trip.couples_count ?? 0;
                if (scEl && typeof trip.singles_count !== 'undefined') scEl.value = trip.singles_count ?? 0;
                updateTotalPax(); renderGuestNameInputs();
                document.getElementById('trip_package_id').dispatchEvent(new Event('change'));
                updateCompanyMode();
                setTripStep(1);
                // Load arrivals (kept hidden for Individual)
                tripArrivalsState = [];
                renderArrivalGroups();
                fetchArrivalsForTrip(trip.id);
                tripDeparturesState = [];
                renderDepartureGroups();
                fetchDeparturesForTrip(trip.id);
                // Determine Day 1 hotel from itinerary
                try{
                    const r = await fetch(`${API_URL}?action=getItinerary&trip_id=${trip.id}&_=${Date.now()}`);
                    const j = await r.json();
                    if (j.status==='success'){
                        itineraryDaysCache = j.data.itinerary_days||[];
                        const d = itineraryDaysCache[0];
                        firstDayHotelId = d && d.hotel_id ? String(d.hotel_id) : null;
                        renderArrivalGroups();
                    }
                }catch(e){ firstDayHotelId = null; itineraryDaysCache = []; }
                // Load guests
                fetchGuestsForTrip(trip.id);
            }

            function openTripModalForPackage(pkgId){
                // Reset trip form as Add Trip
                document.getElementById('tripForm').reset();
                document.getElementById('tripIdHidden').value = '';
                document.getElementById('tripIdDisplay').value = '';
                document.getElementById('fileIdGroup').style.display = 'none';
                document.getElementById('modalTitle').textContent = 'Add Trip';
                // Select the package
                const pkgSelect = document.getElementById('trip_package_id');
                if (pkgSelect){
                    pkgSelect.value = String(pkgId);
                    pkgSelect.dispatchEvent(new Event('change'));
                }
                openModal('tripModal');
            }

            async function openPackageEditModal(pkg){
                document.getElementById('packageModalTitle').textContent = 'Edit Package';
                document.getElementById('packageId').value = pkg.id;
                document.getElementById('package_name').value = pkg.name;
                document.getElementById('package_code').value = pkg.code || '';
                document.getElementById('package_days').value = pkg.No_of_Days;
                generateDayRequirements(pkg.No_of_Days);
                try {
                    const response = await fetch(`${API_URL}?action=getPackageRequirements&trip_package_id=${pkg.id}`);
                    const result = await response.json();
                    if (result.status === 'success' && result.data) {
                        result.data.forEach(req => {
                            const dayNum = req.day_number;
                            const hotelSelector = document.getElementById(`hotel_day_${dayNum}`);
                            if (hotelSelector && req.hotel_id) { hotelSelector.value = req.hotel_id; }
                            const guideCheckbox = document.getElementById(`guide_required_day_${dayNum}`);
                            if (guideCheckbox) { guideCheckbox.checked = req.guide_required === '1' || req.guide_required === 1; }
                            const vehicleCheckbox = document.getElementById(`vehicle_required_day_${dayNum}`);
                            const vehicleTypeSelect = document.getElementById(`vehicle_type_day_${dayNum}`);
                            if (vehicleCheckbox) {
                                vehicleCheckbox.checked = req.vehicle_required === '1' || req.vehicle_required === 1;
                                if (vehicleCheckbox.checked && vehicleTypeSelect) { vehicleTypeSelect.style.display = 'block'; vehicleTypeSelect.value = req.vehicle_type || ''; }
                            }
                            const svcB = document.getElementById(`svc_b_${dayNum}`);
                            const svcL = document.getElementById(`svc_l_${dayNum}`);
                            const svcD = document.getElementById(`svc_d_${dayNum}`);
                            const svcs = (req.day_services || '').toString();
                            if (svcB) svcB.checked = svcs.includes('B');
                            if (svcL) svcL.checked = svcs.includes('L');
                            if (svcD) svcD.checked = svcs.includes('D');
                            const notesEl = document.getElementById(`notes_day_${dayNum}`);
                            if (notesEl && typeof req.day_notes !== 'undefined' && req.day_notes !== null) { notesEl.value = req.day_notes; }
                        });
                    }
                } catch (error) { showToast('Could not load package requirements.', 'error'); }
                openModal('packageModal');
            }

            function openTripById(id){
                const trip = tripsData.find(t => t.id == id);
                if (!trip){ showToast('Trip not found','error'); return; }
                populateTripForm(trip);
                // Ensure all sections visible for editing
                const gdc = document.getElementById('guestDetailsCard'); if (gdc) gdc.style.display = '';
                const s2 = document.getElementById('tripStep2'); if (s2) s2.style.display = '';
                const s3 = document.getElementById('tripStep3'); if (s3) s3.style.display = '';
                const c2 = document.getElementById('controlsStep2'); if (c2) c2.style.display = 'flex';
                const c3 = document.getElementById('controlsStep3'); if (c3) c3.style.display = 'flex';
                const next1 = document.getElementById('btnStepNext1'); if (next1) next1.style.display = '';
                openModal('tripModal');
            }

            document.addEventListener('dblclick', async function(e){
                const tr = e.target.closest('tr.trip-row');
                const pkgTr = e.target.closest('tr.package-row');
                if (tr) {
                    const id = tr.getAttribute('data-id');
                    if (id) window.location.href = `Itinerary.php?trip_id=${id}`;
                    return;
                }
                if (pkgTr) {
                    const id = pkgTr.getAttribute('data-id');
                    const pkg = packagesData.find(p => String(p.id) === String(id));
                    if (pkg) { await openPackageEditModal(pkg); }
                }
            });

            document.addEventListener('click', async function(e) {
                const target = e.target.closest('a[data-id]');
                if (!target) return;

                const id = target.dataset.id;
                
                // Edit Actions

                if (target.classList.contains('btn-edit-trip')) {
                    e.preventDefault();
                    openTripById(id);
                }

                if (target.classList.contains('btn-create-trip-from-package')) {
                    e.preventDefault();
                    const pkg = packagesData.find(p => p.id == id);
                    if (pkg) {
                        openTripModalForPackage(pkg.id);
                    }
                }

                if (target.classList.contains('btn-duplicate-trip')) {
                    e.preventDefault();
                    await duplicateTrip(id);
                }

                if (target.classList.contains('btn-duplicate-package')) {
                    e.preventDefault();
                    const pkg = packagesData.find(p => p.id == id);
                    if (!pkg) return;
                    try {
                        // Fetch requirements to clone
                        const resp = await fetch(`${API_URL}?action=getPackageRequirements&trip_package_id=${pkg.id}`);
                        const reqJson = await resp.json();
                        const reqs = (reqJson.status === 'success' && Array.isArray(reqJson.data)) ? reqJson.data : [];
                        const day_requirements = {};
                        reqs.forEach(r => {
                            const dn = r.day_number;
                            day_requirements[dn] = {
                                hotel_id: r.hotel_id || null,
                                guide_required: (r.guide_required === '1' || r.guide_required === 1),
                                vehicle_required: (r.vehicle_required === '1' || r.vehicle_required === 1),
                                vehicle_type: r.vehicle_type || null,
                                day_services: r.day_services || '',
                                day_notes: r.day_notes || ''
                            };
                        });

                        const baseCode = (pkg.code && String(pkg.code).trim() !== '') ? String(pkg.code).trim() : ('PKG' + pkg.id);
                        let attempt = 0; let maxAttempts = 20; let success = false; let lastErr = ''; let newId = null;
                        while (attempt < maxAttempts && !success) {
                            attempt++;
                            const suffix = (attempt === 1) ? '-COPY' : `-COPY${attempt}`;
                            const newCode = (baseCode + suffix).slice(0,50);
                            const payload = { name: pkg.name, code: newCode, No_of_Days: pkg.No_of_Days, description: pkg.description || '', day_requirements };
                            const createResp = await fetch(`${API_URL}?action=addTripPackage`, { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload)});
                            const createJson = await createResp.json();
                            if (createJson.status === 'success') { success = true; newId = (createJson.data && createJson.data.id) ? createJson.data.id : null; break; }
                            lastErr = createJson.message || 'Unknown error';
                            if (!/code/i.test(lastErr)) { break; }
                        }
                        if (success) {
                            showToast('Package duplicated. Code duplicated; please update.', 'warning');
                            await fetchPackages();
                            const newPkg = packagesData.find(p => String(p.id) === String(newId));
                            if (newPkg) {
                                await openPackageEditModal(newPkg);
                                const banner = document.getElementById('packageDuplicateBanner'); if (banner) banner.style.display = 'block';
                                const codeInput = document.getElementById('package_code'); if (codeInput) { codeInput.focus(); codeInput.select(); }
                            }
                        } else {
                            showToast(`Failed to duplicate package: ${lastErr}`, 'error');
                        }
                    } catch (err) { showToast('Duplication failed: '+ err.message, 'error'); }
                }

                if (target.classList.contains('btn-edit-package')) {
                    e.preventDefault();
                    const pkg = packagesData.find(p => p.id == id);
                    if (pkg) { await openPackageEditModal(pkg); }
                }
                
                if (target.classList.contains('btn-edit-hotel')) {
                    e.preventDefault();
                    const hotel = hotelsData.find(h => h.id == id);
                    if (hotel) {
                        document.getElementById('hotelModalTitle').textContent = 'Edit Hotel';
                        document.getElementById('hotelId').value = hotel.id;
                        document.getElementById('hotel_name').value = hotel.name;
                        document.getElementById('hotel_email').value = hotel.email || '';
                        // Room types (checkbox UI + hidden value)
                        const rt = (hotel.room_types||'').toLowerCase();
                        document.getElementById('room_type_double').checked = /double/.test(rt);
                        document.getElementById('room_type_twin').checked = /twin/.test(rt);
                        document.getElementById('room_type_single').checked = /single/.test(rt);
                        document.getElementById('room_type_triple').checked = /triple/.test(rt);
                        const parts = [];
                        if (document.getElementById('room_type_double').checked) parts.push('Double');
                        if (document.getElementById('room_type_twin').checked) parts.push('Twin');
                        if (document.getElementById('room_type_single').checked) parts.push('Single');
                        if (document.getElementById('room_type_triple').checked) parts.push('Triple');
                        document.getElementById('hotel_room_types').value = parts.join(', ');
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
                        document.getElementById('vehicle_email').value = vehicle.email || '';
                        const plateInput = document.getElementById('vehicle_number_plate');
                        if (plateInput) { plateInput.value = vehicle.number_plate || ''; }
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
                        document.getElementById('guide_email').value = guide.email || '';
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
            function ensureActionToastEl(){
                let el = document.getElementById('actionToast');
                if (!el){
                    el = document.createElement('div');
                    el.id = 'actionToast';
                    el.className = 'action-toast';
                    el.innerHTML = `
                        <span class="msg"></span>
                        <div style="display:flex; gap:8px;">
                            <button type="button" class="btn btn-secondary" data-role="dismiss">Dismiss</button>
                            <button type="button" class="btn btn-primary" data-role="action">Action</button>
                        </div>
                    `;
                    document.body.appendChild(el);
                }
                return el;
            }
            function showActionToast(message, actionLabel, onAction){
                const el = ensureActionToastEl();
                el.querySelector('.msg').textContent = message;
                const actionBtn = el.querySelector('button[data-role="action"]');
                actionBtn.textContent = actionLabel || 'OK';
                const dismissBtn = el.querySelector('button[data-role="dismiss"]');
                const hide = () => { el.classList.remove('show'); };
                // Remove previous listeners by cloning
                const actionBtnClone = actionBtn.cloneNode(true);
                actionBtn.parentNode.replaceChild(actionBtnClone, actionBtn);
                actionBtnClone.addEventListener('click', () => { hide(); try{ onAction && onAction(); }catch(e){} });
                const dismissClone = dismissBtn.cloneNode(true);
                dismissBtn.parentNode.replaceChild(dismissClone, dismissBtn);
                dismissClone.addEventListener('click', hide);
                el.classList.add('show');
            }
            function showToast(message, type = 'success') {
                const toast = document.getElementById('toast');
                toast.textContent = message;
                toast.className = `toast show ${type}`;
                setTimeout(() => {
                    toast.className = toast.className.replace('show', '');
                }, 3000);
            }

            // Filter functions for hotel records
            const filterHotelRecords = () => {
                const statusFilter = document.getElementById('filterStatus').value;
                const monthFilter = document.getElementById('filterMonth').value;
                
                let filteredData = [...hotelRecordsData];
                
                if (statusFilter) {
                    filteredData = filteredData.filter(record => record.status === statusFilter);
                }
                
                if (monthFilter) {
                    const [year, month] = monthFilter.split('-');
                    filteredData = filteredData.filter(record => {
                        const checkInDate = new Date(record.check_in_date);
                        return checkInDate.getFullYear() == year && (checkInDate.getMonth() + 1) == month;
                    });
                }
                
                renderHotelRecords(filteredData);
            };
            
            // Add event listeners for hotel filters
            document.getElementById('filterStatus').addEventListener('change', filterHotelRecords);
            document.getElementById('filterMonth').addEventListener('change', filterHotelRecords);
            
            // Day Roster
            const renderDayRoster = (rows) => {
                const container = document.getElementById('dayRosterContainer');
                container.innerHTML = '';
                if (!rows || rows.length === 0) {
                    container.innerHTML = '<div class="no-records">No entries found for the selected period.</div>';
                    return;
                }
                // Group by date
                const grouped = rows.reduce((acc, r) => { (acc[r.day_date] = acc[r.day_date] || []).push(r); return acc; }, {});
                Object.keys(grouped).sort().forEach(date => {
                    const group = document.createElement('div'); group.className = 'hotel-group';
                    const header = document.createElement('div'); header.className = 'hotel-header';
                    header.innerHTML = `<i class="fas fa-calendar-day"></i> ${date} <span style="margin-left:auto; opacity:0.9;">${grouped[date].length} file(s)</span>`;
                    const list = document.createElement('div'); list.className = 'hotel-bookings';
                    grouped[date].sort((a,b)=> (a.tour_code||'').localeCompare(b.tour_code||''));
                    grouped[date].forEach(r => {
                        const item = document.createElement('div'); item.className = 'booking-item';
                        const hotel = r.hotel_name ? `<span><i class=\"fas fa-hotel\"></i> ${r.hotel_name}</span>` : '';
                        const guide = r.guide_name ? `<span><i class=\"fas fa-user\"></i> ${r.guide_name}${r.guide_language?` (${r.guide_language})`:''}</span>` : '';
                        const arrText = (r.arrival_vehicle_summary||'').trim();
                        const arrCount = arrText ? arrText.split(',').length : 0;
                        const arrivals = arrText ? `<span><i class=\"fas fa-plane-arrival\"></i> ${arrText} ${arrCount>1? `<span class=\"status\" style=\"background:#e0f2fe;color:#075985\">${arrCount} arrivals</span>` : ''}</span>` : '';
                        const depText = (r.departure_vehicle_summary||'').trim();
                        const depCount = depText ? depText.split(',').length : 0;
                        const departures = depText ? `<span><i class=\"fas fa-plane-departure\"></i> ${depText} ${depCount>1? `<span class=\"status\" style=\"background:#e0e7ff;color:#1e40af\">${depCount} departures</span>` : ''}</span>` : '';
                        const vehicle = r.vehicle_name ? `<span><i class=\"fas fa-car\"></i> ${r.vehicle_name}${r.number_plate?` (${r.number_plate})`:''}</span>` : '';
                        const services = (r.services_provided||'').trim()? `<span><i class=\"fas fa-utensils\"></i> ${r.services_provided}</span>`: '';
                        const informedPills = [
                          r.hotel_informed==1? '<span class="status" style="background:#dcfce7;color:#166534">Hotel Informed</span>':'',
                          r.guide_informed==1? '<span class="status" style="background:#dcfce7;color:#166534">Guide Informed</span>':'',
                          r.vehicle_informed==1? '<span class="status" style="background:#dcfce7;color:#166534">Vehicle Informed</span>':''
                        ].filter(Boolean).join(' ');
                        item.innerHTML = `
                          <div class="booking-main">
                            <div class="booking-title">#${String(r.trip_id).padStart(3,'0')} • ${r.guest_name} ${r.tour_code? '| Tour: ' + r.tour_code : ''}</div>
                            <div class=\"booking-details\">${hotel} ${guide} ${vehicle} ${arrivals} ${departures} ${services}</div>
                          </div>
                          <div class="booking-meta">
                            ${informedPills}
                            <span class="status status-${r.status}">${r.status}</span>
                            <div class=\"booking-actions\"><a href=\"Itinerary.php?trip_id=${r.trip_id}&focus_date=${date}\" title=\"Open Itinerary on ${date}\"><i class=\"fas fa-route\"></i></a></div>
                          </div>`;
                        // Notes row
                        if ((r.notes||'').trim()){
                          const notes = document.createElement('div');
                          notes.style.cssText = 'margin:8px 0 0 0; font-size:0.9rem; color:#374151;';
                          notes.innerHTML = `<i class="fas fa-sticky-note"></i> ${r.notes}`;
                          item.querySelector('.booking-main').appendChild(notes);
                        }
                        list.appendChild(item);
                    });
                    group.appendChild(header); group.appendChild(list); container.appendChild(group);
                });
            };
            const fetchDayRoster = async () => {
                try {
                    const month = document.getElementById('dayRosterMonth').value;
                    const url = month ? `${API_URL}?action=getDayRoster&month=${month}` : `${API_URL}?action=getDayRoster`;
                    const resp = await fetch(url);
                    const json = await resp.json();
                    if (json.status === 'success') renderDayRoster(json.data);
                    else showToast(json.message || 'Failed to load day roster','error');
                } catch (e) { showToast('Failed to load day roster','error'); }
            };

            // Filter functions for guide records
            const filterGuideRecords = () => {
                const statusFilter = document.getElementById('guideFilterStatus').value;
                const monthFilter = document.getElementById('guideFilterMonth').value;
                
                let filteredData = [...guideRecordsData];
                
                if (statusFilter) {
                    filteredData = filteredData.filter(record => record.status === statusFilter);
                }
                
                if (monthFilter) {
                    const [year, month] = monthFilter.split('-');
                    filteredData = filteredData.filter(record => {
                        const assignmentDate = new Date(record.assignment_date);
                        return assignmentDate.getFullYear() == year && (assignmentDate.getMonth() + 1) == month;
                    });
                }
                
                renderGuideRecords(filteredData);
            };
            
            // Add event listeners for guide filters
            document.getElementById('guideFilterStatus').addEventListener('change', filterGuideRecords);
            document.getElementById('guideFilterMonth').addEventListener('change', filterGuideRecords);

            // Day roster filters
            const drMonth = document.getElementById('dayRosterMonth');
            if (drMonth){
              const now = new Date(); const ym = `${now.getFullYear()}-${String(now.getMonth()+1).padStart(2,'0')}`;
              drMonth.value = ym;
              drMonth.addEventListener('change', fetchDayRoster);
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