<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Itinerary Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #6366f1;
            --primary-light: #818cf8;
            --primary-dark: #4f46e5;
            --secondary-color: #64748b;
            --secondary-light: #94a3b8;
            --secondary-dark: #475569;
            --export-color: #10b981;
            --export-dark: #059669;
            --background: #fafafa;
            --surface: #ffffff;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --text-light: #9ca3af;
            --border: #e5e7eb;
            --border-light: #f3f4f6;
            --success: #10b981;
            --error: #ef4444;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        * { 
            box-sizing: border-box; 
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica', 'Arial', sans-serif;
            background: var(--background);
            color: var(--text-primary);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            font-size: 15px; 
        }

        .container {
            max-width: 950px; 
            margin: 0 auto;
            padding: 25px 20px; 
        }

        main {
            padding-bottom: 90px;
        }

        .page-header {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border);
        }
        
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .trip-info h1 {
            font-size: 1.4rem; 
            font-weight: 700;
            margin-bottom: 5px; 
        }
        
        .trip-meta {
            font-size: 0.8rem; 
        }
        
        .btn-back {
            padding: 7px 14px; 
            font-size: 0.9rem; 
            border: 1px solid var(--border); 
            border-radius: 7px;
            text-decoration: none;
            color: var(--text-secondary);
            transition: all 0.2s;
        }
        .btn-back:hover {
            background: var(--border-light);
        }

        .tabs-and-toggle {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
            border-bottom: 2px solid var(--border-light);
        }

        .day-tabs-wrapper {
            display: flex;
            align-items: center;
            flex-grow: 1;
            margin-right: 15px;
            position: relative;
            overflow: hidden; 
        }

        .day-tabs-nav {
            overflow-x: auto;
            white-space: nowrap;
            padding: 5px 0;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            flex-grow: 1;
            margin-right: 0;
            padding-left: 25px; 
            padding-right: 25px;
        }

        .day-tabs-nav::-webkit-scrollbar {
            display: none;
        }

        .tab-scroll-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 50%;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--text-secondary);
            box-shadow: var(--shadow-sm);
            transition: all 0.2s;
            opacity: 0.9;
        }

        .tab-scroll-btn:hover {
            background: var(--border-light);
            opacity: 1;
        }

        .tab-scroll-btn.left {
            left: 0;
        }

        .tab-scroll-btn.right {
            right: 0;
        }

        .summary-toggle-btn {
            background: var(--surface);
            color: var(--secondary-color); 
            border: 1px solid var(--secondary-light); 
            padding: 8px 15px;
            border-radius: 7px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            flex-shrink: 0;
            line-height: 1;
        }
        .summary-toggle-btn:hover {
            background: var(--border-light);
        }
        .summary-toggle-btn.active {
            background: var(--secondary-dark); 
            color: white;
            border-color: var(--secondary-dark);
            box-shadow: var(--shadow-sm);
        }
        
        .tab-button {
            display: inline-block;
            padding: 11px 18px; 
            margin-right: 10px;
            background: var(--surface);
            color: var(--text-secondary);
            border: 1px solid var(--border);
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.98rem; 
            font-weight: 500;
            transition: all 0.2s;
            appearance: none; 
            -webkit-appearance: none;
        }

        .tab-button.active {
            padding: 12px 18px; 
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
            box-shadow: var(--shadow-sm);
        }

        .itinerary-grid, .summary-view-wrapper {
            padding: 35px; 
            border: 1px solid var(--border);
            border-radius: 14px;
            background: var(--surface);
            box-shadow: var(--shadow);
            min-height: 650px; 
            animation: fadeIn 0.3s ease-out;
        }
        
        .summary-view-wrapper {
            display: none;
        }

        .day-content-wrapper {
            display: none;
            animation: fadeIn 0.3s ease-out;
        }

        .day-content-wrapper.active {
            display: block;
        }
        
        .day-content-wrapper > div:first-child {
            font-size: 1.1rem; 
            font-weight: 600;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .summary-card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 12px;
        }

        .summary-card {
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 10px;
            background: var(--border-light);
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-direction: column;
            gap: 6px;
            min-width: 250px;
        }
        
        .summary-header {
            font-size: 0.9rem;
            font-weight: 700;
            padding-bottom: 4px;
            border-bottom: 1px solid var(--border);
            color: var(--secondary-dark);
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.8rem;
            padding: 2px 0;
        }
        
        .summary-item i {
            margin-right: 6px;
            color: var(--secondary-color);
            font-size: 0.9rem;
        }
        
        .summary-label {
            color: var(--text-secondary);
            font-weight: 500;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            font-size: 0.8rem;
        }

        .summary-value {
            font-weight: 700;
            text-align: right;
            color: var(--text-primary);
            max-width: 50%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: 0.8rem;
        }

        .summary-status {
            font-size: 0.7rem;
            font-weight: 600;
            padding: 2px 5px;
            border-radius: 3px;
            margin-left: 8px;
        }
        
        .status-informed {
            background: var(--success);
            color: white;
        }
        
        .status-uninformed {
            background: var(--error);
            color: white;
        }
        
        .status-unassigned {
            background: var(--text-light);
            color: var(--text-primary);
        }

        .assignments-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .hotel-group {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
        }
        
        .hotel-fields {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            align-items: start;
        }
        
        .notes-section {
            grid-column: 1 / -1;
        }
        
        .room-quantities-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
            margin-top: 8px;
        }
        
        .room-quantity-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .room-quantity-item label {
            font-size: 0.75rem;
            color: var(--text-secondary);
            min-width: 45px;
        }
        
        .room-quantity-item input {
            width: 50px !important;
            padding: 4px !important;
            font-size: 0.8rem;
            border-radius: 3px;
        }
        
        /* Responsive layout for mobile */
        @media screen and (max-width: 768px) {
            .assignments-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }
            
            .hotel-fields {
                grid-template-columns: 1fr;
                gap: 10px;
            }
            
            .room-quantities-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
        
        /* Ensure form controls have consistent height */
        .form-group-controls {
            min-height: 80px;
        }
        
        .custom-select select {
            min-height: 32px;
        }
        
        /* Searchable Select Styles */
        .searchable-select {
            position: relative;
            width: 100%;
        }
        
        .searchable-select-input {
            width: 100%;
            padding: 7px 30px 7px 9px;
            font-size: 0.85rem;
            border: 1px solid var(--border);
            border-radius: 7px;
            background: var(--border-light);
            color: var(--text-primary);
            cursor: pointer;
        }
        
        .searchable-select-input:focus {
            outline: none;
            border-color: var(--primary-color);
            background: white;
        }
        
        .searchable-select-arrow {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            color: var(--text-light);
            transition: transform 0.2s;
        }
        
        .searchable-select.open .searchable-select-arrow {
            transform: translateY(-50%) rotate(180deg);
        }
        
        .searchable-select-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid var(--border);
            border-top: none;
            border-radius: 0 0 7px 7px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .searchable-select.open .searchable-select-dropdown {
            display: block;
        }
        
        .searchable-select-option {
            padding: 8px 12px;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
            font-size: 0.85rem;
        }
        
        .searchable-select-option:last-child {
            border-bottom: none;
        }
        
        .searchable-select-option:hover {
            background: var(--background-color);
        }
        
        .searchable-select-option.selected {
            background: var(--primary-color);
            color: white;
        }
        
        .searchable-select-option.no-results {
            color: var(--text-light);
            font-style: italic;
            cursor: default;
        }
        
        .searchable-select-option.no-results:hover {
            background: transparent;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .form-group-controls {
            border: 1px solid var(--border);
            padding: 10px; 
            border-radius: 9px;
            display: flex;
            flex-direction: column;
        }
        
        .form-group label {
            font-size: 0.85rem; 
            letter-spacing: 0.05em;
            font-weight: 600;
            color: var(--text-primary);
        }

        select, textarea {
            border: 1px solid var(--border);
            color: var(--text-primary);
            background: var(--border-light);
            width: 100%;
            outline: none;
        }
        
        select:disabled {
            background: #f0f0f0;
            color: #888;
            cursor: not-allowed;
        }
        
        .custom-select {
            position: relative;
            flex-grow: 1;
        }
        
        .custom-select select {
            padding: 7px 9px; 
            font-size: 0.85rem; 
            border-radius: 7px;
            appearance: none; 
            -webkit-appearance: none;
        }
        
        .informed-switch-container {
            margin-top: 10px; 
            padding-top: 8px;
            border-top: 1px dashed var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 25px; 
        }

        .informed-status-label {
            font-size: 0.8rem; 
            font-weight: 500;
            color: var(--text-secondary);
        }

        .informed-toggle {
            width: 80px; 
            height: 25px;
        }
        
        .informed-toggle .toggle-label {
            display: block;
            line-height: 23px; 
            text-align: center;
            font-size: 0.75rem; 
            font-weight: 700;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.2s, border-color 0.2s;
        }
        
        /* Style for the disabled label when informed */
        .informed-input[disabled] + .toggle-label {
            background: #a7f3d0 !important; /* Lighter success color */
            color: var(--success) !important;
            border: 1px solid #a7f3d0 !important;
            cursor: not-allowed;
        }

        .informed-input:checked + .toggle-label {
            background: var(--success);
            color: white;
            border: 1px solid var(--success);
        }
        
        .informed-input:not(:checked) + .toggle-label {
            background: var(--error);
            color: white;
            border: 1px solid var(--error);
        }
        .informed-input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .services-block {
            border: 1px solid var(--border);
            padding: 10px;
            border-radius: 7px;
            background: var(--border-light);
            margin-top: 8px;
        }

        .services-block label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            margin: 6px 0;
            font-weight: normal;
            font-size: 0.85rem;
        }

        .services-block input[type="checkbox"] {
            width: auto;
            cursor: pointer;
            margin: 0;
        }

        .notes-section {
            grid-column: 1 / -1;
        }
        .form-group textarea {
            padding: 12px;
            min-height: 140px; 
            font-size: 0.9rem; 
            border-radius: 7px; 
            resize: vertical; 
        }
        
        .form-actions {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 18px 25px;
            border-top: 1px solid var(--border);
            background: var(--surface);
            box-shadow: 0 -4px 10px rgba(0, 0, 0, 0.05);
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .form-actions-buttons {
            display: flex;
            gap: 12px;
        }
        .save-info {
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        .btn-save {
            background: var(--primary-color);
            color: white;
            padding: 13px 28px; 
            border-radius: 11px;
            font-weight: 700;
            font-size: 0.95rem;
            transition: all 0.2s;
            box-shadow: 0 4px 8px rgba(99, 102, 241, 0.3);
            border: none; 
            cursor: pointer; 
        }
        .btn-save:hover:not(:disabled) {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        .btn-export {
            background: var(--export-color);
            color: white;
            padding: 13px 20px; 
            border-radius: 11px;
            font-weight: 700;
            font-size: 0.95rem;
            transition: all 0.2s;
            box-shadow: 0 4px 8px rgba(16, 185, 129, 0.3);
            border: none; 
            cursor: pointer;
        }
        .btn-export:hover:not(:disabled) {
            background: var(--export-dark);
            transform: translateY(-1px);
        }
        
        .btn-save:disabled, .btn-export:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* Email Status Panel */
        .email-status-panel {
            position: fixed;
            right: 20px;
            bottom: 95px;
            width: 360px;
            max-height: 60vh;
            background: var(--surface);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-md);
            border-radius: 10px;
            overflow: hidden;
            display: none;
            z-index: 1100;
        }
        .email-status-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 12px;
            background: var(--border-light);
            border-bottom: 1px solid var(--border);
            font-weight: 700;
            color: var(--text-primary);
        }
        .email-status-body {
            padding: 8px 12px;
            max-height: 50vh;
            overflow: auto;
        }
        .email-status-list { list-style: none; margin: 0; padding: 0; }
        .email-status-item {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            padding: 8px 4px;
            border-bottom: 1px dashed var(--border-light);
            font-size: 0.9rem;
        }
        .email-status-item:last-child { border-bottom: none; }
        .status-pill {
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 700;
            white-space: nowrap;
        }
        .pill-sent { background: #dcfce7; color: #166534; }
        .pill-queued { background: #e0f2fe; color: #075985; }
        .pill-error { background: #fee2e2; color: #991b1b; }
        .email-status-close { cursor: pointer; color: var(--text-secondary); }
        .email-status-minmax { cursor: pointer; color: var(--text-secondary); margin-right: 10px; }
        .email-status-body.minimized { display: none; }

        .error-message {
            padding: 20px;
            background: #fee2e2;
            border: 1px solid var(--error);
            border-radius: 8px;
            color: var(--error);
            text-align: center;
        }

        .package-hotel-badge {
            display: inline-block;
            background: #e0f2fe;
            color: #0369a1;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 600;
            margin-left: 5px;
        }

        .service-tag {
            display: inline-block;
            background: #10b981;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 0.7rem;
            font-weight: 600;
            margin: 2px;
        }
        
        .room-quantities-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            background: var(--border-light);
            padding: 12px;
            border-radius: 6px;
            border: 1px solid var(--border);
        }
        
        .room-quantity-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }
        
        .room-quantity-item label {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-primary);
            margin: 0;
            flex: 1;
        }
        
        /* Red dot indicator for missing hotel assignments */
        .tab-button {
            position: relative;
        }
        
        .tab-button .missing-hotel-indicator {
            position: absolute;
            top: 4px;
            right: 6px;
            width: 8px;
            height: 8px;
            background: var(--error);
            border-radius: 50%;
            box-shadow: 0 0 0 2px var(--surface);
            animation: pulse-red 2s infinite;
        }
        
        @keyframes pulse-red {
            0%, 100% {
                opacity: 1;
                transform: scale(1);
            }
            50% {
                opacity: 0.7;
                transform: scale(1.2);
            }
        }
        
        .tab-button.active .missing-hotel-indicator {
            box-shadow: 0 0 0 2px var(--primary-color);
        }

        @media (max-width: 768px) {
            .tabs-and-toggle {
                flex-direction: column;
                align-items: stretch;
            }
            .day-tabs-wrapper {
                margin-right: 0;
                margin-bottom: 10px;
            }
            .day-tabs-nav {
                padding-left: 0;
                padding-right: 0;
            }
            .tab-scroll-btn {
                display: none;
            }
            .assignments-grid {
                grid-template-columns: 1fr;
            }
            .hotel-group, .hotel-fields {
                grid-column: span 1;
                grid-template-columns: 1fr;
            }
            .itinerary-grid, .summary-view-wrapper {
                padding: 20px;
                min-height: 550px; 
            }
            .form-actions {
                flex-direction: column;
                gap: 10px;
                padding: 15px;
            }
            .form-actions-buttons {
                width: 100%;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 8px;
            }
            .btn-save, .btn-export {
                flex: 1;
                min-width: calc(50% - 4px);
                padding: 10px 8px;
                font-size: 0.85rem;
            }
            .btn-save {
                flex-basis: 100%;
                order: 4;
            }
        }
    </style>
</head>
<body>

    <div class="container" id="app">
        <header class="page-header">
            <div class="header-top">
                <div class="trip-info">
                    <h1 id="tripTitle">Loading Trip...</h1>
                    <div class="trip-meta" id="tripMeta"></div>
                </div>
                <a href="index.php" class="btn-back">
                    <i class="fas fa-arrow-left"></i> 
                    <span>Back</span>
                </a>
            </div>
        </header>

        <main>
            <div class="tabs-and-toggle">
                <div class="day-tabs-wrapper">
                    <button id="scrollLeftBtn" class="tab-scroll-btn left"><i class="fas fa-chevron-left"></i></button>
                    <nav class="day-tabs-nav" id="dayTabs"></nav>
                    <button id="scrollRightBtn" class="tab-scroll-btn right"><i class="fas fa-chevron-right"></i></button>
                </div>
                <button id="summaryToggleBtn" class="summary-toggle-btn" data-view-mode="details">
                    <i class="fas fa-list-check"></i> 
                    <span>View Summary</span>
                </button>
            </div>
            
            <form id="itineraryForm">
                <div class="itinerary-grid" id="itineraryGrid">
                    <div class="loading">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p>Loading itinerary...</p>
                    </div>
                </div>
                
                <div class="summary-view-wrapper" id="summaryView"></div>

                <div class="form-actions">
                    <div class="save-info">
                        <i class="fas fa-info-circle"></i>
                        <span>Changes will be saved to database</span>
                    </div>
                    <div class="form-actions-buttons">
                        <button type="button" id="emailGuidesBtn" class="btn-export">
                            <i class="fas fa-user-tie"></i>
                            <span>Email Guides</span>
                        </button>
                        <button type="button" id="emailHotelsBtn" class="btn-export">
                            <i class="fas fa-envelope"></i>
                            <span>Email Hotels</span>
                        </button>
                        <button type="button" id="emailVehiclesBtn" class="btn-export">
                            <i class="fas fa-truck"></i>
                            <span>Email Vehicles</span>
                        </button>
                        <button type="button" id="exportCsvBtn" class="btn-export">
                            <i class="fas fa-file-csv"></i>
                            <span>Export to CSV</span>
                        </button>
                        <button type="submit" class="btn-save">
                            <i class="fas fa-check"></i>
                            <span>Save Changes</span>
                        </button>
                    </div>
                </div>
            </form>
        </main>
    </div>

    <!-- Missing Assignment Modal -->
    <style>
      .mini-modal { position: fixed; inset: 0; background: rgba(0,0,0,0.35); display: none; align-items:center; justify-content:center; z-index: 1200; }
      .mini-modal .content { width: 380px; background:#fff; border-radius:10px; box-shadow: var(--shadow-md); overflow:hidden; }
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
          <div id="missingAssignInfo" style="font-size:0.9rem; color: var(--text-secondary);"></div>
          <div class="form-group">
            <label id="missingAssignLabel">Select</label>
            <div class="custom-select"><select id="missingAssignSelect"></select></div>
          </div>
        </div>
        <div class="footer">
          <button id="missingAssignSkip" class="btn btn-secondary">Skip</button>
          <button id="missingAssignSave" class="btn btn-primary">Assign & Next</button>
        </div>
      </div>
    </div>

    <!-- Email Status Panel -->
    <div id="emailStatusPanel" class="email-status-panel">
        <div class="email-status-header">
            <span><i class="fas fa-envelope-open-text"></i> Email Status</span>
            <div>
                <span id="emailStatusMinMax" class="email-status-minmax" title="Minimize/Maximize"><i class="fas fa-minus"></i></span>
                <span id="emailStatusClose" class="email-status-close"><i class="fas fa-times"></i></span>
            </div>
        </div>
        <div class="email-status-body">
            <ul id="emailStatusList" class="email-status-list"></ul>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const API_URL = 'api/api.php';
            const itineraryForm = document.getElementById('itineraryForm');
            const itineraryGrid = document.getElementById('itineraryGrid');
            const summaryView = document.getElementById('summaryView');
            const dayTabsContainer = document.getElementById('dayTabs');
            const summaryToggleBtn = document.getElementById('summaryToggleBtn');
            const scrollLeftBtn = document.getElementById('scrollLeftBtn');
            const scrollRightBtn = document.getElementById('scrollRightBtn');
            const saveBtn = document.querySelector('.btn-save');
            const exportCsvBtn = document.getElementById('exportCsvBtn');
            const emailHotelsBtn = document.getElementById('emailHotelsBtn');
            const emailGuidesBtn = document.getElementById('emailGuidesBtn');
            const emailVehiclesBtn = document.getElementById('emailVehiclesBtn');
            const emailStatusPanel = document.getElementById('emailStatusPanel');
            const emailStatusList = document.getElementById('emailStatusList');
            const emailStatusClose = document.getElementById('emailStatusClose');
            const emailStatusMinMax = document.getElementById('emailStatusMinMax');
            
            const urlParams = new URLSearchParams(window.location.search);
            const tripId = urlParams.get('trip_id');

            let allGuides = [];
            let allVehicles = [];
            let allHotels = [];
            let currentItineraryDays = [];
            let packageHotels = [];
            let packageRequirements = [];
            let roomTypes = [];

            function showToast(message, type = 'success') {
                const toast = document.getElementById('toast') || createToastElement();
                toast.textContent = message;
                toast.className = `toast show ${type}`;
                setTimeout(() => { toast.classList.remove('show'); }, 3000); 
            }

            function createToastElement() {
                const toast = document.createElement('div');
                toast.id = 'toast';
                toast.style.cssText = `
                    position: fixed; bottom: 100px; left: 50%; transform: translateX(-50%);
                    padding: 12px 24px; border-radius: 8px; color: white; font-weight: 600;
                    opacity: 0; visibility: hidden; transition: all 0.3s; z-index: 10000;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                `;
                toast.classList.add('toast');
                document.body.appendChild(toast);
                
                const style = document.createElement('style');
                style.textContent = `
                    .toast.show { opacity: 1 !important; visibility: visible !important; }
                    .toast.success { background: #10b981; }
                    .toast.error { background: #ef4444; }
                    .toast.info { background: #3b82f6; }
                `;
                document.head.appendChild(style);
                
                return toast;
            }

            // Email Status Panel helpers
            const openEmailStatusPanel = () => { emailStatusPanel.style.display = 'block'; };
            const closeEmailStatusPanel = () => { emailStatusPanel.style.display = 'none'; };
            emailStatusClose.addEventListener('click', closeEmailStatusPanel);
            emailStatusMinMax.addEventListener('click', () => {
                const body = emailStatusPanel.querySelector('.email-status-body');
                const icon = emailStatusMinMax.querySelector('i');
                if (body.classList.contains('minimized')) {
                    body.classList.remove('minimized');
                    icon.classList.remove('fa-plus');
                    icon.classList.add('fa-minus');
                } else {
                    body.classList.add('minimized');
                    icon.classList.remove('fa-minus');
                    icon.classList.add('fa-plus');
                }
            });
            const clearEmailStatus = () => { emailStatusList.innerHTML = ''; };
            const addEmailStatusItem = (status, text) => {
                const li = document.createElement('li');
                li.className = 'email-status-item';
                let pillClass = 'pill-queued';
                let pillText = 'QUEUED';
                if (status === 'success' || status === 'sent') { pillClass = 'pill-sent'; pillText = 'SENT'; }
                if (status === 'error' || status === 'failed') { pillClass = 'pill-error'; pillText = 'ERROR'; }
                if (status === 'info' || status === 'queued') { pillClass = 'pill-queued'; pillText = 'QUEUED'; }
                li.innerHTML = `<span class="status-pill ${pillClass}">${pillText}</span><span>${text}</span>`;
                emailStatusList.appendChild(li);
            };

            // Searchable Select Component
            class SearchableSelect {
                constructor(container, options, selectedValue = '') {
                    if (!container) {
                        throw new Error('SearchableSelect: container element is required');
                    }
                    if (!Array.isArray(options)) {
                        throw new Error('SearchableSelect: options must be an array');
                    }
                    
                    this.container = container;
                    this.options = options;
                    this.selectedValue = selectedValue;
                    this.selectedText = '';
                    this.isOpen = false;
                    this.filteredOptions = [...options];
                    
                    this.init();
                }
                
                init() {
                    // Find selected option text
                    const selectedOption = this.options.find(opt => opt.value === this.selectedValue);
                    this.selectedText = selectedOption ? selectedOption.text : '';
                    
                    // Create HTML structure
                    this.container.innerHTML = `
                        <div class="searchable-select">
                            <input type="text" class="searchable-select-input" 
                                   placeholder="Search hotels..." 
                                   value="${this.selectedText}" readonly>
                            <i class="fas fa-chevron-down searchable-select-arrow"></i>
                            <div class="searchable-select-dropdown"></div>
                        </div>
                    `;
                    
                    this.selectEl = this.container.querySelector('.searchable-select');
                    this.inputEl = this.container.querySelector('.searchable-select-input');
                    this.dropdownEl = this.container.querySelector('.searchable-select-dropdown');
                    
                    this.bindEvents();
                    this.renderOptions();
                }
                
                bindEvents() {
                    // Toggle dropdown
                    this.inputEl.addEventListener('click', () => {
                        this.toggle();
                    });
                    
                    // Enable typing to search
                    this.inputEl.addEventListener('input', (e) => {
                        if (!this.isOpen) this.open();
                        this.filter(e.target.value);
                    });
                    
                    // Handle keyboard navigation
                    this.inputEl.addEventListener('keydown', (e) => {
                        if (e.key === 'ArrowDown') {
                            e.preventDefault();
                            if (!this.isOpen) this.open();
                        } else if (e.key === 'Escape') {
                            this.close();
                        }
                    });
                    
                    // Close on outside click
                    document.addEventListener('click', (e) => {
                        if (!this.container.contains(e.target)) {
                            this.close();
                        }
                    });
                }
                
                renderOptions() {
                    const options = this.filteredOptions.length > 0 ? this.filteredOptions : 
                        [{ value: '', text: 'No hotels found', disabled: true }];
                    
                    this.dropdownEl.innerHTML = options.map(option => 
                        `<div class="searchable-select-option ${option.disabled ? 'no-results' : ''} ${option.value === this.selectedValue ? 'selected' : ''}" 
                              data-value="${option.value}">
                            ${option.text}
                        </div>`
                    ).join('');
                    
                    // Bind option click events
                    this.dropdownEl.querySelectorAll('.searchable-select-option:not(.no-results)').forEach(optionEl => {
                        optionEl.addEventListener('click', (e) => {
                            const value = e.target.dataset.value;
                            const option = this.options.find(opt => opt.value === value);
                            this.selectOption(option);
                        });
                    });
                }
                
                filter(searchText) {
                    this.filteredOptions = this.options.filter(option => 
                        option.text.toLowerCase().includes(searchText.toLowerCase())
                    );
                    this.renderOptions();
                }
                
                selectOption(option) {
                    this.selectedValue = option.value;
                    this.selectedText = option.text;
                    this.inputEl.value = option.text;
                    this.close();
                    
                    // Trigger change event
                    const changeEvent = new CustomEvent('change', {
                        detail: { value: option.value, text: option.text }
                    });
                    this.container.dispatchEvent(changeEvent);
                }
                
                open() {
                    this.isOpen = true;
                    this.selectEl.classList.add('open');
                    this.inputEl.removeAttribute('readonly');
                    this.inputEl.focus();
                }
                
                close() {
                    this.isOpen = false;
                    this.selectEl.classList.remove('open');
                    this.inputEl.setAttribute('readonly', true);
                    this.inputEl.value = this.selectedText;
                }
                
                toggle() {
                    if (this.isOpen) {
                        this.close();
                    } else {
                        this.open();
                    }
                }
                
                getValue() {
                    return this.selectedValue;
                }
                
                setValue(value) {
                    const option = this.options.find(opt => opt.value === value);
                    if (option) {
                        this.selectOption(option);
                    }
                }
            }

            const fetchItinerary = async () => {
                try {
                    console.log('Starting fetchItinerary...');
                    if (!tripId) {
                        itineraryGrid.innerHTML = '<div class="error-message">No trip ID provided. Please go back and select a trip.</div>';
                        return;
                    }

                    const cacheBuster = new Date().getTime();
                    const response = await fetch(`${API_URL}?action=getItinerary&trip_id=${tripId}&_=${cacheBuster}`);
                    const result = await response.json();
                    
                    if (result.status !== 'success') {
                        itineraryGrid.innerHTML = `<div class="error-message">${result.message}</div>`;
                        return;
                    }

                    console.log('API response received:', result);
                    const { trip, itinerary_days, guides, vehicles, hotels } = result.data;
                    
                    allGuides = guides;
                    allVehicles = vehicles;
                    allHotels = hotels;
                    currentItineraryDays = itinerary_days;
                    console.log('Data loaded - hotels:', allHotels.length, 'guides:', allGuides.length, 'vehicles:', allVehicles.length);
                    
                    if (trip.trip_package_id) {
                        try {
                            // Fetch package requirements (new format with guide/vehicle info)
                            const reqResponse = await fetch(`${API_URL}?action=getPackageRequirements&trip_package_id=${trip.trip_package_id}`);
                            const reqResult = await reqResponse.json();
                            if (reqResult.status === 'success') {
                                packageRequirements = reqResult.data;
                                // Also populate legacy packageHotels for backward compatibility
                                packageHotels = reqResult.data.map(req => ({
                                    day_number: req.day_number,
                                    hotel_id: req.hotel_id
                                })).filter(ph => ph.hotel_id);
                            }

                            const rtResponse = await fetch(`${API_URL}?action=getRoomTypes`);
                            const rtResult = await rtResponse.json();
                            if (rtResult.status === 'success') {
                                roomTypes = rtResult.data;
                            }
                        } catch (e) {
                            console.log('Package requirements/room types not available, using fallback');
                        }
                    }
                    
                    document.getElementById('tripTitle').textContent = `${trip.customer_name}'s ${trip.package_name}`;
                    document.getElementById('tripMeta').innerHTML = `
                        <div class="trip-meta-item">
                            <i class="far fa-calendar"></i>
                            <span>From ${trip.start_date} to ${itinerary_days.length > 0 ? itinerary_days[itinerary_days.length - 1].day_date : trip.end_date}</span>
                        </div>
                        <div class="trip-meta-item">
                            <i class="fas fa-info-circle"></i>
                            <span>${itinerary_days.length} Days</span>
                        </div>
                    `;

                    console.log('About to render itinerary...');
                    try {
                        renderItinerary(itinerary_days);
                        console.log('Itinerary rendered successfully');
                    } catch (error) {
                        console.error('Error rendering itinerary:', error);
                        itineraryGrid.innerHTML = '<div class="error-message">Error rendering itinerary: ' + error.message + '</div>';
                        return;
                    }
                    
                    try {
                        renderTabsAndSwitch(itinerary_days);
                        renderSummaryCards(itinerary_days);
                    } catch (error) {
                        console.error('Error rendering tabs/summary:', error);
                    }
                    
                    summaryToggleBtn.addEventListener('click', () => {
                        const nextMode = summaryToggleBtn.dataset.viewMode === 'details' ? 'summary' : 'details';
                        toggleView(nextMode);
                    });
                    
                    exportCsvBtn.addEventListener('click', exportToCSV);
                    emailHotelsBtn.addEventListener('click', sendHotelEmail);
                    emailGuidesBtn.addEventListener('click', sendGuideEmail);
                    emailVehiclesBtn.addEventListener('click', sendVehicleEmail);
                    
                    scrollLeftBtn.addEventListener('click', () => scrollTabs('left'));
                    scrollRightBtn.addEventListener('click', () => scrollTabs('right'));
                    
                    toggleView('details');
                    // Do not auto-start missing assignment wizard on itinerary view
                    
                } catch (error) {
                    console.error('Fetch itinerary error:', error);
                    showToast('Error loading itinerary: ' + error.message, 'error');
                    itineraryGrid.innerHTML = `<div class="error-message">Error loading itinerary: ${error.message}<br>Please check the console for details and try again.</div>`;
                }
            };

            const renderItinerary = (itinerary_days) => {
                itineraryGrid.innerHTML = '';
                let dayCounter = 1;
                itinerary_days.forEach(day => {
                    const dayContentWrapper = document.createElement('div');
                    dayContentWrapper.className = 'day-content-wrapper';
                    dayContentWrapper.dataset.dayId = day.id;
                    dayContentWrapper.dataset.dayNumber = dayCounter;
                    
                    const dayDate = new Date(day.day_date + 'T00:00:00');
                    const dateString = dayDate.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric' });

                    const guideOptions = createSelectOptions(allGuides, day.guide_id, 'guide');
                    const vehicleOptions = createSelectOptions(allVehicles, day.vehicle_id, 'vehicle');
                    
                    let hotelOptions = createSelectOptions(allHotels, day.hotel_id, 'hotel');
                    
                    // Parse room quantities from existing data
                    let roomQuantities = {
                        double: 0,
                        twin: 0,
                        single: 0,
                        triple: 0
                    };
                    
                    // If there's existing room_type_data, parse it
                    if (day.room_type_data && day.room_type_data !== 'null' && day.room_type_data.trim() !== '') {
                        try {
                            const parsed = JSON.parse(day.room_type_data);
                            if (parsed && typeof parsed === 'object') {
                                roomQuantities = { ...roomQuantities, ...parsed };
                            }
                        } catch (e) {
                            // If parsing fails, keep defaults
                            console.log('Failed to parse room_type_data:', day.room_type_data);
                        }
                    }

                    let hotelBadge = '';
                    let showServices = false;
                    
                    let servicesProvided = day.services_provided || '';
                    let hasBreakfast = servicesProvided.includes('B');
                    let hasLunch = servicesProvided.includes('L');
                    let hasDinner = servicesProvided.includes('D');
                    
                    if (packageHotels.length > 0) {
                        const packageHotel = packageHotels.find(ph => ph.day_number === dayCounter);
                        if (packageHotel) {
                            // Auto-select the package hotel but keep it editable
                            if (!day.hotel_id) {
                                // Only auto-assign if no hotel is currently assigned
                                const hotelObj = allHotels.find(h => h.id == packageHotel.hotel_id);
                                if (hotelObj) {
                                    day.hotel_id = packageHotel.hotel_id;
                                    hotelOptions = createSelectOptions(allHotels, packageHotel.hotel_id, 'hotel');
                                }
                            }
                            hotelBadge = '';
                            showServices = true;
                            
                            if (!servicesProvided && packageHotel.services_provided) {
                                servicesProvided = packageHotel.services_provided;
                                hasBreakfast = servicesProvided.includes('B');
                                hasLunch = servicesProvided.includes('L');
                                hasDinner = servicesProvided.includes('D');
                            }
                            
                             if (packageHotel.room_types && roomTypes.length > 0) {
                                const hotelRoomTypeNames = packageHotel.room_types.split(',').map(rt => rt.trim().toLowerCase());
                                let tempRoomTypeOptions = '<option value="">Select Room Type</option>';
                                
                                roomTypes.forEach(rt => {
                                    if (hotelRoomTypeNames.includes(rt.name.toLowerCase())) {
                                        const isSelected = rt.id == day.room_type_id ? 'selected' : '';
                                        tempRoomTypeOptions += `<option value="${rt.id}" ${isSelected}>${rt.name}</option>`;
                                    }
                                });
                                roomTypeOptions = tempRoomTypeOptions;
                            }
                        }
                    }
                    
                    if (day.hotel_id) {
                         showServices = true;
                    }


                    // Check if guide/vehicle are required for this day
                    const packageReq = packageRequirements.find(req => req.day_number === dayCounter);
                    const showGuide = !packageReq || packageReq.guide_required == 1;
                    const showVehicle = !packageReq || packageReq.vehicle_required == 1;
                    const vehicleTypeLabel = packageReq && packageReq.vehicle_type ? 
                        ` (${packageReq.vehicle_type.charAt(0).toUpperCase() + packageReq.vehicle_type.slice(1)})` : '';

                    const servicesHTML = showServices ? `
                        <div class="form-group">
                            <div class="form-group-controls">
                                <label><i class="fas fa-utensils"></i> Services Provided</label>
                                <div class="services-block">
                                    <label>
                                        <input type="checkbox" name="day_${day.id}_service_breakfast" value="B" ${hasBreakfast ? 'checked' : ''}> 
                                        <span>Breakfast (B)</span>
                                    </label>
                                    <label>
                                        <input type="checkbox" name="day_${day.id}_service_lunch" value="L" ${hasLunch ? 'checked' : ''}> 
                                        <span>Lunch (L)</span>
                                    </label>
                                    <label>
                                        <input type="checkbox" name="day_${day.id}_service_dinner" value="D" ${hasDinner ? 'checked' : ''}> 
                                        <span>Dinner (D)</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    ` : '';

                    // Hotel always spans full width on its own row
                    const hotelSpanClass = 'style="grid-column: 1 / -1;"';

                    dayContentWrapper.innerHTML = `
                        <div style="font-size: 1.1rem; font-weight: 600; margin-bottom: 12px; border-bottom: 1px dashed var(--border-light); padding-bottom: 8px;">
                            Day ${dayCounter} – ${dateString}
                        </div>
                        <div class="assignments-grid">
                            ${showGuide ? `
                            <div class="form-group">
                                <div class="form-group-controls">
                                    <label for="day_${day.id}_guide_id"><i class="fas fa-user-tie"></i> Guide</label>
                                    <div class="custom-select">
                                        <select id="day_${day.id}_guide_id" name="day_${day.id}_guide_id">${guideOptions}</select>
                                    </div>
                                    ${createInformedSwitch(day.id, 'guide', day.guide_informed)}
                                </div>
                            </div>
                            ` : ''}
                            ${showVehicle ? `
                            <div class="form-group">
                                <div class="form-group-controls">
                                    <label for="day_${day.id}_vehicle_id"><i class="fas fa-car"></i> Vehicle${vehicleTypeLabel}</label>
                                    <div class="custom-select">
                                        <select id="day_${day.id}_vehicle_id" name="day_${day.id}_vehicle_id">${vehicleOptions}</select>
                                    </div>
                                    ${createInformedSwitch(day.id, 'vehicle', day.vehicle_informed)}
                                </div>
                            </div>
                            ` : ''}
                            <div class="hotel-group" ${hotelSpanClass}>
                                <div class="hotel-fields">
                                    <div class="form-group">
                                        <div class="form-group-controls">
                                            <label for="day_${day.id}_hotel_id"><i class="fas fa-hotel"></i> Hotel ${hotelBadge}</label>
                                            <div class="custom-select">
                                                <select id="day_${day.id}_hotel_id" name="day_${day.id}_hotel_id">${hotelOptions}</select>
                                            </div>
                                            ${createInformedSwitch(day.id, 'hotel', day.hotel_informed)}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-group-controls">
                                            <label><i class="fas fa-door-open"></i> Rooms</label>
                                            <div class="room-quantities-grid">
                                                <div class="room-quantity-item">
                                                    <label for="day_${day.id}_rooms_double">Double</label>
                                                    <input type="number" id="day_${day.id}_rooms_double" name="day_${day.id}_rooms_double" min="0" max="50" value="${roomQuantities.double}">
                                                </div>
                                                <div class="room-quantity-item">
                                                    <label for="day_${day.id}_rooms_twin">Twin</label>
                                                    <input type="number" id="day_${day.id}_rooms_twin" name="day_${day.id}_rooms_twin" min="0" max="50" value="${roomQuantities.twin}">
                                                </div>
                                                <div class="room-quantity-item">
                                                    <label for="day_${day.id}_rooms_single">Single</label>
                                                    <input type="number" id="day_${day.id}_rooms_single" name="day_${day.id}_rooms_single" min="0" max="50" value="${roomQuantities.single}">
                                                </div>
                                                <div class="room-quantity-item">
                                                    <label for="day_${day.id}_rooms_triple">Triple</label>
                                                    <input type="number" id="day_${day.id}_rooms_triple" name="day_${day.id}_rooms_triple" min="0" max="50" value="${roomQuantities.triple}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                ${servicesHTML}
                            </div>
                            <div class="form-group notes-section">
                                <label for="day_${day.id}_notes"><i class="fas fa-list-ul"></i> Activities & Notes</label>
                                <textarea id="day_${day.id}_notes" name="day_${day.id}_notes" placeholder="Add activities, destinations, or special instructions...">${day.notes || ''}</textarea>
                            </div>
                        </div>
                    `;
                    itineraryGrid.appendChild(dayContentWrapper);
                    
                    const hotelSelect = document.getElementById(`day_${day.id}_hotel_id`);
                    if (hotelSelect) {
                        hotelSelect.addEventListener('change', function() {
                            const dayElement = this.closest('.day-content-wrapper');
                            const hotelGroup = dayElement.querySelector('.hotel-group');
                            
                            // Remove any existing services blocks first
                            const existingServicesBlocks = hotelGroup.querySelectorAll('.form-group');
                            existingServicesBlocks.forEach(block => {
                                if (block.querySelector('.services-block')) {
                                    block.remove();
                                }
                            });
                            
                            // Add services block only if hotel is selected
                            if (this.value && this.value !== '') {
                                const servicesHTML = `
                                    <div class="form-group services-form-group">
                                        <div class="form-group-controls">
                                            <label><i class="fas fa-utensils"></i> Services Provided</label>
                                            <div class="services-block">
                                                <label>
                                                    <input type="checkbox" name="day_${day.id}_service_breakfast" value="B">
                                                    <span>Breakfast (B)</span>
                                                </label>
                                                <label>
                                                    <input type="checkbox" name="day_${day.id}_service_lunch" value="L">
                                                    <span>Lunch (L)</span>
                                                </label>
                                                <label>
                                                    <input type="checkbox" name="day_${day.id}_service_dinner" value="D">
                                                    <span>Dinner (D)</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                `;
                                hotelGroup.insertAdjacentHTML('beforeend', servicesHTML);
                            }
                        });
                    }
                    
                    
                    if (dayCounter === itinerary_days.length) {
                        setTimeout(setupInformedToggles, 0); 
                        // Attach room-type propagation after all day sections exist
                        setTimeout(setupRoomTypePropagation, 0);
                        // Setup hotel change listeners for red dot indicators
                        setTimeout(setupHotelChangeListeners, 0);
                        // Initial update of missing hotel indicators
                        setTimeout(updateMissingHotelIndicators, 200);
                    }
                    
                    // Add guide conflict checking to guide select
                    const guideSelect = document.getElementById(`day_${day.id}_guide_id`);
                    if (guideSelect) {
                        guideSelect.addEventListener('change', async function() {
                            const guideId = this.value;
                            const dayDate = day.day_date;
                            
                            // Remove any existing conflict warning for this day
                            const existingWarning = this.parentElement.querySelector('.guide-conflict-warning');
                            if (existingWarning) {
                                existingWarning.remove();
                            }
                            
                            if (guideId && guideId !== '') {
                                try {
                                    const response = await fetch(`${API_URL}?action=checkGuideAvailability&guide_id=${guideId}&day_date=${dayDate}&trip_id=${tripId}`);
                                    const result = await response.json();
                                    
                                    if (!result.available && result.conflicts.length > 0) {
                                        const conflict = result.conflicts[0];
                                        const warningDiv = document.createElement('div');
                                        warningDiv.className = 'guide-conflict-warning';
                                        warningDiv.style.cssText = 'color: #ff4444; font-size: 0.8rem; margin-top: 4px; padding: 4px 8px; background: #ffebee; border-radius: 4px; border-left: 3px solid #ff4444;';
                                        warningDiv.innerHTML = `⚠️ Conflict: This guide is already assigned to "${conflict.customer_name}" (${conflict.tour_code}) on this date.`;
                                        this.parentElement.appendChild(warningDiv);
                                    }
                                } catch (error) {
                                    console.error('Error checking guide availability:', error);
                                }
                            }
                        });
                    }
                    
                    dayCounter++;
                });
            };

            const createSelectOptions = (items, selectedId, type) => {
                let html = (type === 'room_type') ? '<option value="">Select Room Type</option>' : '<option value="">Not assigned</option>';
                items.forEach(item => {
                    const isSelected = item.id == selectedId ? 'selected' : '';
                    let subtext = '';
                    
                    if (type === 'guide') {
                        subtext = item.language ? ` (${item.language})` : '';
                } else if (type === 'vehicle') {
                        if (item.capacity && item.number_plate) {
                            subtext = ` (Seats: ${item.capacity}, Plate: ${item.number_plate})`;
                        } else if (item.number_plate) {
                            subtext = ` (Plate: ${item.number_plate})`;
                        } else if (item.capacity) {
                            subtext = ` (Seats: ${item.capacity})`;
                        } else {
                            subtext = '';
                        }
                    }

                    const nameKey = item.name || item.vehicle_name || 'N/A';
                    html += `<option value="${item.id}" ${isSelected}>${nameKey}${subtext}</option>`;
                });
                return html;
            };

            const createInformedSwitch = (dayId, fieldName, isChecked) => {
                const checked = isChecked ? 'checked' : '';
                const toggleText = isChecked ? 'Informed' : 'Uninformed';
                
                // Disable guide/vehicle/hotel toggles if they are already informed (can't change back)
                const isDisabled = ((fieldName === 'guide' || fieldName === 'vehicle' || fieldName === 'hotel') && isChecked) ? 'disabled' : '';
                
                let labelStatusText = 'Status:';
                if (!isChecked && fieldName === 'hotel') {
                    labelStatusText = 'Status: <span style="color: var(--error); font-size: 0.7rem; font-weight: 700;">(Required)</span>';
                } else if ((fieldName === 'guide' || fieldName === 'vehicle' || fieldName === 'hotel') && isChecked) {
                    labelStatusText = 'Status: <span style="color: var(--success); font-size: 0.7rem; font-weight: 700;">(Locked)</span>';
                }

                const inputId = `day_${dayId}_${fieldName}_informed_toggle`; 

                return `
                    <div class="informed-switch-container">
                        <label class="informed-status-label">${labelStatusText}</label>
                        <div class="informed-toggle">
                            <input type="checkbox" id="${inputId}" name="day_${dayId}_${fieldName}_informed" class="informed-input" ${checked} ${isDisabled} />
                            <label class="toggle-label" for="${inputId}">${toggleText}</label>
                        </div>
                    </div>
                `;
            };
            
            const updateStatusLabel = (inputId, isChecked) => {
                const container = document.getElementById(inputId).closest('.informed-switch-container');
                const statusLabel = container ? container.querySelector('.informed-status-label') : null;
                const input = document.getElementById(inputId);
                
                if (statusLabel) {
                    const fieldName = inputId.split('_')[2]; 
                    
                    if ((fieldName === 'guide' || fieldName === 'vehicle' || fieldName === 'hotel') && isChecked && input.disabled) {
                        statusLabel.innerHTML = 'Status: <span style=\"color: var(--success); font-size: 0.7rem; font-weight: 700;\">(Locked)</span>';
                    } else if (isChecked) {
                        statusLabel.innerHTML = 'Status:';
                    } else if (fieldName === 'hotel') {
                         statusLabel.innerHTML = 'Status: <span style=\"color: var(--error); font-size: 0.7rem; font-weight: 700;\">(Required)</span>';
                    } else {
                        statusLabel.innerHTML = 'Status:';
                    }
                }
            };
            
            const setupInformedToggles = () => {
                const informedInputs = document.querySelectorAll('.informed-input');
                
                informedInputs.forEach(input => {
                    const label = document.querySelector(`label[for="${input.id}"]`);
                    
                    if (input.disabled) {
                        if (label) {
                            label.textContent = 'Informed';
                            label.style.cursor = 'not-allowed';
                            label.style.opacity = '0.7';
                        }
                        return; 
                    }

                    if (label) {
                        label.textContent = input.checked ? 'Informed' : 'Uninformed';
                        updateStatusLabel(input.id, input.checked);
                    }

                    const newLabel = label.cloneNode(true);
                    label.parentNode.replaceChild(newLabel, label);

                    newLabel.addEventListener('click', (e) => {
                        // Prevent clicking on disabled guide toggles
                        if (input.disabled) {
                            e.preventDefault();
                            return false;
                        }
                        
                        setTimeout(() => {
                            const isChecked = input.checked;
                            newLabel.textContent = isChecked ? 'Informed' : 'Uninformed';
                            updateStatusLabel(input.id, isChecked);
                            
                            if (summaryToggleBtn.dataset.viewMode === 'summary') {
                                renderSummaryCards(currentItineraryDays);
                            }
                        }, 50); 
                    });
                });
            };

            const scrollTabs = (direction) => {
                const scrollAmount = 200;
                dayTabsContainer.scrollBy({ 
                    left: direction === 'left' ? -scrollAmount : scrollAmount, 
                    behavior: 'smooth' 
                });
            };

            // Propagate Day 1 room quantities to all other days automatically
            const setupRoomTypePropagation = () => {
                const firstDayWrapper = document.querySelector('.day-content-wrapper[data-day-number="1"]');
                if (!firstDayWrapper) return;
                
                // Get all room quantity inputs from day 1
                const firstDayInputs = {
                    double: firstDayWrapper.querySelector('input[name$="_rooms_double"]'),
                    twin: firstDayWrapper.querySelector('input[name$="_rooms_twin"]'),
                    single: firstDayWrapper.querySelector('input[name$="_rooms_single"]'),
                    triple: firstDayWrapper.querySelector('input[name$="_rooms_triple"]')
                };
                
                // Function to propagate room quantities from day 1 to all other days
                const propagateRoomQuantities = () => {
                    const roomQuantities = {
                        double: parseInt(firstDayInputs.double?.value || 0),
                        twin: parseInt(firstDayInputs.twin?.value || 0),
                        single: parseInt(firstDayInputs.single?.value || 0),
                        triple: parseInt(firstDayInputs.triple?.value || 0)
                    };
                    
                    // Apply to all other days (skip day 1)
                    const allDayWrappers = document.querySelectorAll('.day-content-wrapper:not([data-day-number="1"])');
                    allDayWrappers.forEach(dayWrapper => {
                        const dayInputs = {
                            double: dayWrapper.querySelector('input[name$="_rooms_double"]'),
                            twin: dayWrapper.querySelector('input[name$="_rooms_twin"]'),
                            single: dayWrapper.querySelector('input[name$="_rooms_single"]'),
                            triple: dayWrapper.querySelector('input[name$="_rooms_triple"]')
                        };
                        
                        // Set values for each room type
                        Object.keys(roomQuantities).forEach(roomType => {
                            if (dayInputs[roomType]) {
                                dayInputs[roomType].value = roomQuantities[roomType];
                            }
                        });
                    });
                    
                    // Update summary if in summary mode
                    if (summaryToggleBtn.dataset.viewMode === 'summary') {
                        renderSummaryCards(currentItineraryDays);
                    }
                    
                    // Update missing hotel indicators
                    setTimeout(updateMissingHotelIndicators, 50);
                };
                
                // Add event listeners to all day 1 room quantity inputs
                Object.values(firstDayInputs).forEach(input => {
                    if (input) {
                        input.addEventListener('input', propagateRoomQuantities);
                        input.addEventListener('change', propagateRoomQuantities);
                    }
                });
                
                // Initial propagation if day 1 already has values
                const hasValues = Object.values(firstDayInputs).some(input => 
                    input && parseInt(input.value || 0) > 0
                );
                if (hasValues) {
                    propagateRoomQuantities();
                }
            };
            
            // Function to update red dot indicators for missing hotel assignments
            const updateMissingHotelIndicators = () => {
                const dayButtons = document.querySelectorAll('.tab-button');
                
                dayButtons.forEach(button => {
                    const dayId = button.dataset.dayId;
                    if (!dayId) return;
                    
                    // Check if hotel is assigned for this day
                    const hotelSelect = document.querySelector(`[name="day_${dayId}_hotel_id"]`);
                    const hasHotel = hotelSelect && hotelSelect.value && hotelSelect.value !== '';
                    
                    // Remove existing indicator
                    const existingIndicator = button.querySelector('.missing-hotel-indicator');
                    if (existingIndicator) {
                        existingIndicator.remove();
                    }
                    
                    // Add red dot if hotel is missing
                    if (!hasHotel) {
                        const indicator = document.createElement('div');
                        indicator.className = 'missing-hotel-indicator';
                        indicator.title = 'Hotel assignment missing';
                        button.appendChild(indicator);
                    }
                });
            };
            
            // Function to setup hotel change listeners to update indicators
            const setupHotelChangeListeners = () => {
                const hotelSelects = document.querySelectorAll('select[name$="_hotel_id"]');
                hotelSelects.forEach(select => {
                    select.addEventListener('change', () => {
                        setTimeout(updateMissingHotelIndicators, 50);
                    });
                });
            };

            // Missing assignments wizard (small modal)
            let missingWizardState = { type: 'hotel', items: [], index: 0 };
            const missingModal = document.getElementById('missingAssignModal');
            const missingTitle = document.getElementById('missingAssignTitle');
            const missingInfo = document.getElementById('missingAssignInfo');
            const missingLabel = document.getElementById('missingAssignLabel');
            const missingSelect = document.getElementById('missingAssignSelect');
            const missingClose = document.getElementById('missingAssignClose');
            const missingSkip = document.getElementById('missingAssignSkip');
            const missingSave = document.getElementById('missingAssignSave');

            const openMissingModal = () => { if (missingModal) { missingModal.style.display = 'flex'; } };
            const closeMissingModal = () => { if (missingModal) { missingModal.style.display = 'none'; } };

            function collectMissing(type) {
                const items = [];
                const dayWrappers = document.querySelectorAll('.day-content-wrapper');
                dayWrappers.forEach(dw => {
                    const dayId = dw.dataset.dayId;
                    if (type === 'hotel') {
                        const sel = dw.querySelector('select[name$="_hotel_id"]');
                        if (sel && (!sel.value || sel.value === '')) { items.push({ dayId, sel }); }
                    } else if (type === 'vehicle') {
                        const sel = dw.querySelector('select[name$="_vehicle_id"]');
                        if (sel && (!sel.value || sel.value === '')) { items.push({ dayId, sel }); }
                    }
                });
                return items;
            }

            function populateSelectFor(type, currentValue) {
                let opts = '<option value="">Not assigned</option>';
                if (type === 'hotel') {
                    allHotels.forEach(h => { const sel = (String(h.id)===String(currentValue))?'selected':''; opts += `<option value="${h.id}" ${sel}>${h.name}</option>`; });
                } else if (type === 'vehicle') {
                    allVehicles.forEach(v => { const plate = v.number_plate ? ` (${v.number_plate})` : ''; const sel=(String(v.id)===String(currentValue))?'selected':''; opts += `<option value="${v.id}" ${sel}>${v.vehicle_name}${plate}</option>`; });
                }
                missingSelect.innerHTML = opts;
            }

            function showCurrentMissing() {
                if (!missingWizardState.items.length || missingWizardState.index >= missingWizardState.items.length) {
                    // Move to next type or finish
                    if (missingWizardState.type === 'hotel') {
                        missingWizardState = { type: 'vehicle', items: collectMissing('vehicle'), index: 0 };
                        if (missingWizardState.items.length) { showCurrentMissing(); return; }
                        closeMissingModal();
                        return;
                    } else {
                        closeMissingModal();
                        return;
                    }
                }
                const { dayId, sel } = missingWizardState.items[missingWizardState.index];
                missingTitle.textContent = missingWizardState.type === 'hotel' ? 'Assign Hotel' : 'Assign Vehicle';
                missingLabel.textContent = missingWizardState.type === 'hotel' ? 'Select Hotel' : 'Select Vehicle';
                const dayBtn = document.querySelector(`.tab-button[data-day-id="${dayId}"]`);
                const dayText = dayBtn ? dayBtn.textContent : '';
                missingInfo.textContent = `Missing ${missingWizardState.type} for ${dayText}`;
                populateSelectFor(missingWizardState.type, sel.value);
                openMissingModal();
            }

            function startMissingAssignmentsWizard() {
                missingWizardState = { type: 'hotel', items: collectMissing('hotel'), index: 0 };
                if (missingWizardState.items.length) {
                    showCurrentMissing();
                } else {
                    // No missing hotels, check vehicles
                    missingWizardState = { type: 'vehicle', items: collectMissing('vehicle'), index: 0 };
                    if (missingWizardState.items.length) showCurrentMissing();
                }
            }

            if (missingClose) missingClose.addEventListener('click', closeMissingModal);
            if (missingSkip) missingSkip.addEventListener('click', () => { missingWizardState.index++; showCurrentMissing(); });
            if (missingSave) missingSave.addEventListener('click', () => {
                const item = missingWizardState.items[missingWizardState.index];
                if (!item) { closeMissingModal(); return; }
                const { sel } = item;
                sel.value = missingSelect.value || '';
                // Trigger change to update indicators
                sel.dispatchEvent(new Event('change'));
                missingWizardState.index++;
                showCurrentMissing();
            });

            const renderTabsAndSwitch = (itinerary_days) => {
                dayTabsContainer.innerHTML = '';
                itinerary_days.forEach((day, index) => {
                    const dayNumber = index + 1;
                    const dayDate = new Date(day.day_date + 'T00:00:00');
                    const dateString = dayDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                    
                    const button = document.createElement('button');
                    button.className = 'tab-button';
                    button.dataset.dayNumber = dayNumber;
                    button.dataset.dayId = day.id;
                    button.innerHTML = `Day ${dayNumber} <span style="font-weight: 400; color: var(--text-light); margin-left: 4px;">(${dateString})</span>`;
                    
                    dayTabsContainer.appendChild(button);
                });
                
                // Update missing hotel indicators after tabs are created
                setTimeout(updateMissingHotelIndicators, 100);

                dayTabsContainer.addEventListener('click', function(e) {
                    if (e.target.classList.contains('tab-button')) {
                        switchDay(e.target.dataset.dayNumber);
                    }
                });

                if (itinerary_days.length > 0) {
                    switchDay(1);
                }
            };

            const switchDay = (dayNumber) => {
                if (!dayNumber || summaryToggleBtn.dataset.viewMode === 'summary') return;

                const dayButtons = document.querySelectorAll('.tab-button');
                const dayContents = document.querySelectorAll('.day-content-wrapper');

                dayButtons.forEach(btn => {
                    if (btn.dataset.dayNumber === dayNumber.toString()) {
                        btn.classList.add('active');
                        btn.scrollIntoView({ behavior: 'smooth', inline: 'center' });
                    } else {
                        btn.classList.remove('active');
                    }
                });

                dayContents.forEach(content => {
                    if (content.dataset.dayNumber === dayNumber.toString()) {
                        content.classList.add('active');
                    } else {
                        content.classList.remove('active');
                    }
                });
            };

            const renderSummaryCards = (daysData) => {
                let html = '<div class="summary-card-grid">';
                const currentData = getCurrentFormData(daysData);

                currentData.forEach((day, index) => {
                    const dayDate = new Date(day.day_date + 'T00:00:00');
                    const dateString = dayDate.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' });
                    
                    const assignments = [
                        { icon: 'fas fa-user-tie', label: 'Guide', value: day.guide_name, informed: day.guide_informed },
                        { icon: 'fas fa-car', label: 'Vehicle', value: day.vehicle_name, informed: day.vehicle_informed },
                        { icon: 'fas fa-hotel', label: 'Hotel', value: day.hotel_name, informed: day.hotel_informed },
                    ];
                    
                    let itemsHtml = '';
                    assignments.forEach(item => {
                        const assigned = item.value && item.value !== 'Not assigned';
                        const statusClass = assigned ? (item.informed ? 'status-informed' : 'status-uninformed') : 'status-unassigned';
                        const statusText = assigned ? (item.informed ? 'Informed' : 'Uninformed') : 'Unassigned';
                        
                        itemsHtml += `
                            <div class="summary-item">
                                <span class="summary-label">
                                    <i class="${item.icon}"></i> ${item.label}
                                </span>
                                <span class="summary-value">${item.value || 'N/A'}</span>
                                <span class="summary-status ${statusClass}">${statusText}</span>
                            </div>
                        `;
                    });
                    
                    const servicesDisplay = day.services_provided 
                        ? day.services_provided.split(',').map(s => `<span class="service-tag">${s.trim()}</span>`).join('')
                        : '<span style="color: var(--text-light);">No services</span>';
                    
                    itemsHtml += `
                        <div class="summary-item">
                            <span class="summary-label">
                                <i class="fas fa-utensils"></i> Services
                            </span>
                            <span class="summary-value">${servicesDisplay}</span>
                        </div>
                    `;

                    html += `
                        <div class="summary-card">
                            <div class="summary-header">
                                Day ${index + 1} (${dateString})
                            </div>
                            ${itemsHtml}
                        </div>
                    `;
                });

                html += '</div>';
                summaryView.innerHTML = html;
            };

            const toggleView = (mode) => {
                const isSummaryMode = mode === 'summary';
                
                if (isSummaryMode) {
                    itineraryGrid.style.display = 'none';
                    summaryView.style.display = 'block';
                    summaryToggleBtn.classList.add('active');
                    summaryToggleBtn.innerHTML = '<i class="fas fa-list-ul"></i> <span>View Details</span>';
                    summaryToggleBtn.dataset.viewMode = 'summary';
                    dayTabsContainer.parentNode.style.display = 'none';
                    renderSummaryCards(currentItineraryDays);
                } else {
                    itineraryGrid.style.display = 'block';
                    summaryView.style.display = 'none';
                    summaryToggleBtn.classList.remove('active');
                    summaryToggleBtn.innerHTML = '<i class="fas fa-list-check"></i> <span>View Summary</span>';
                    summaryToggleBtn.dataset.viewMode = 'details';
                    dayTabsContainer.parentNode.style.display = 'flex';
                    
                    const activeDayBtn = document.querySelector('.tab-button.active');
                    if (activeDayBtn) {
                        switchDay(activeDayBtn.dataset.dayNumber);
                    } else if (currentItineraryDays.length > 0) {
                        switchDay(1);
                    }
                }
            };

            const getCurrentFormData = (daysData) => {
                const currentData = [];
                daysData.forEach(day => {
                    const dayId = day.id;
                    
                    const guideSelect = document.querySelector(`[name="day_${dayId}_guide_id"]`);
                    const vehicleSelect = document.querySelector(`[name="day_${dayId}_vehicle_id"]`);
                    const hotelSelect = document.querySelector(`[name="day_${dayId}_hotel_id"]`);
                    
                    // Get room quantity inputs
                    const roomsDouble = document.querySelector(`[name="day_${dayId}_rooms_double"]`);
                    const roomsTwin = document.querySelector(`[name="day_${dayId}_rooms_twin"]`);
                    const roomsSingle = document.querySelector(`[name="day_${dayId}_rooms_single"]`);
                    const roomsTriple = document.querySelector(`[name="day_${dayId}_rooms_triple"]`);
                    
                    const guideInformedCheck = document.querySelector(`[name="day_${dayId}_guide_informed"]`);
                    const vehicleInformedCheck = document.querySelector(`[name="day_${dayId}_vehicle_informed"]`);
                    const hotelInformedCheck = document.querySelector(`[name="day_${dayId}_hotel_informed"]`);

                    const notesTextarea = document.querySelector(`[name="day_${dayId}_notes"]`);
                    
                    const serviceBreakfast = document.querySelector(`[name="day_${dayId}_service_breakfast"]`);
                    const serviceLunch = document.querySelector(`[name="day_${dayId}_service_lunch"]`);
                    const serviceDinner = document.querySelector(`[name="day_${dayId}_service_dinner"]`);
                    
                    const services = [];
                    if (serviceBreakfast && serviceBreakfast.checked) services.push('B');
                    if (serviceLunch && serviceLunch.checked) services.push('L');
                    if (serviceDinner && serviceDinner.checked) services.push('D');
                    const servicesProvided = services.join(', ');

                    const guideId = guideSelect ? guideSelect.value : day.guide_id;
                    const vehicleId = vehicleSelect ? vehicleSelect.value : day.vehicle_id;
                    const hotelId = hotelSelect ? hotelSelect.value : day.hotel_id;
                    
                    // Collect room quantities
                    const roomQuantities = {
                        double: roomsDouble ? parseInt(roomsDouble.value) || 0 : 0,
                        twin: roomsTwin ? parseInt(roomsTwin.value) || 0 : 0,
                        single: roomsSingle ? parseInt(roomsSingle.value) || 0 : 0,
                        triple: roomsTriple ? parseInt(roomsTriple.value) || 0 : 0
                    };
                    
                    // Create room summary for display
                    const roomSummary = [];
                    if (roomQuantities.double > 0) roomSummary.push(`${roomQuantities.double} Double`);
                    if (roomQuantities.twin > 0) roomSummary.push(`${roomQuantities.twin} Twin`);
                    if (roomQuantities.single > 0) roomSummary.push(`${roomQuantities.single} Single`);
                    if (roomQuantities.triple > 0) roomSummary.push(`${roomQuantities.triple} Triple`);
                    const roomSummaryText = roomSummary.length > 0 ? roomSummary.join(', ') : 'No rooms';
                    
                    // Check if any rooms are assigned
                    const hasRooms = roomQuantities.double > 0 || roomQuantities.twin > 0 || roomQuantities.single > 0 || roomQuantities.triple > 0;
                    
                    const guideObj = allGuides.find(g => g.id == guideId);
                    const vehicleObj = allVehicles.find(v => v.id == vehicleId);
                    const hotelObj = allHotels.find(h => h.id == hotelId);
                    
                    const guideInformed = guideInformedCheck ? guideInformedCheck.checked : day.guide_informed;
                    const vehicleInformed = vehicleInformedCheck ? vehicleInformedCheck.checked : day.vehicle_informed;
                    const hotelInformed = hotelInformedCheck ? hotelInformedCheck.checked : day.hotel_informed;

                    currentData.push({
                        id: day.id,
                        day_date: day.day_date,
                        guide_id: guideId,
                        vehicle_id: vehicleId,
                        hotel_id: hotelId,
                        room_quantities: roomQuantities,
                        room_summary: roomSummaryText,
                        has_rooms: hasRooms,
                        notes: notesTextarea ? notesTextarea.value : day.notes,
                        services_provided: servicesProvided || day.services_provided,
                        guide_name: guideObj ? guideObj.name : 'Not assigned',
                        guide_informed: guideInformed,
                        vehicle_name: vehicleObj ? vehicleObj.vehicle_name : 'Not assigned',
                        vehicle_informed: vehicleInformed,
                        hotel_name: hotelObj ? hotelObj.name : 'Not assigned',
                        hotel_informed: hotelInformed,
                    });
                });

                return currentData;
            };
            
            const exportToCSV = () => {
                const data = getCurrentFormData(currentItineraryDays);
                if (data.length === 0) {
                    showToast('No itinerary data to export.', 'error');
                    return;
                }

                const headers = ['Day', 'Date', 'Guide', 'Guide Informed', 'Vehicle', 'Vehicle Informed', 'Hotel', 'Hotel Informed', 'Services'];
                const csvRows = data.map((d, index) => [
                    `Day ${index + 1}`,
                    d.day_date,
                    d.guide_name,
                    d.guide_informed ? 'Yes' : 'No',
                    d.vehicle_name,
                    d.vehicle_informed ? 'Yes' : 'No',
                    d.hotel_name,
                    d.hotel_informed ? 'Yes' : 'No',
                    d.services_provided || 'None',
                ]);

                const csvContent = [
                    headers.join(','),
                    ...csvRows.map(row => row.join(','))
                ].join('\n');

                const tripTitle = document.getElementById('tripTitle').textContent.replace(/[^a-z0-9]/gi, '_').toLowerCase();
                const filename = `${tripTitle}_itinerary.csv`;
                
                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement("a");
                
                if (link.download !== undefined) { 
                    const url = URL.createObjectURL(blob);
                    link.setAttribute("href", url);
                    link.setAttribute("download", filename);
                    link.style.visibility = 'hidden';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    showToast('Data exported successfully!', 'success');
                }
            };
            
            // MODIFIED: Function to send email to ALL uninformed hotels for the trip
// MODIFIED: Function to send email to ALL uninformed hotels for the trip
            const sendHotelEmail = async () => {
                // First, get current form data to check for unsaved changes
                const currentData = getCurrentFormData(currentItineraryDays);
                
                // Debug: Log hotel_id values
                currentData.forEach((day, i) => {
                    console.log(`Day ${i+1}: hotel_id="${day.hotel_id}" (type: ${typeof day.hotel_id})`);
                });
                
                // Check for missing hotel assignments
                const missingHotels = currentData.filter(day => !day.hotel_id || day.hotel_id === '' || day.hotel_id === '0' || day.hotel_id === 0);
                const missingRooms = currentData.filter(day => day.hotel_id && day.hotel_id !== '' && day.hotel_id !== '0' && day.hotel_id !== 0 && !day.has_rooms);
                
                if (missingHotels.length > 0 || missingRooms.length > 0) {
                    openEmailStatusPanel();
                    clearEmailStatus();
                    
                    if (missingHotels.length > 0) {
                        const missingDays = missingHotels.map(day => {
                            const dayIndex = currentData.findIndex(d => d.id === day.id);
                            return `Day ${dayIndex + 1}`;
                        }).join(', ');
                        addEmailStatusItem('error', `Missing hotel assignments: ${missingDays}`);
                    }
                    
                    if (missingRooms.length > 0) {
                        const missingRoomDays = missingRooms.map(day => {
                            const dayIndex = currentData.findIndex(d => d.id === day.id);
                            return `Day ${dayIndex + 1}`;
                        }).join(', ');
                        addEmailStatusItem('error', `Missing room quantities: ${missingRoomDays}`);
                    }
                    
                    addEmailStatusItem('info', 'Please complete all hotel assignments and room quantities before sending emails.');
                    showToast('Complete hotel assignments and room quantities first.', 'error');
                    return;
                }

                const uninformedHotels = currentData.some(day => day.hotel_id && !day.hotel_informed);
                if (!uninformedHotels) {
                    showToast('All assigned hotels have already been informed.', 'info');
                    return;
                }

                // Save changes first before sending emails
                emailHotelsBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
                emailHotelsBtn.disabled = true;

                try {
                    // Save current form data
                    const itinerary_days_data = currentData.map(d => ({
                        id: d.id,
                        guide_id: d.guide_id || null,
                        vehicle_id: d.vehicle_id || null,
                        hotel_id: d.hotel_id || null,
                        room_type_data: d.room_quantities ? JSON.stringify(d.room_quantities) : JSON.stringify({double: 0, twin: 0, single: 0, triple: 0}),
                        guide_informed: d.guide_informed ? 1 : 0,
                        vehicle_informed: d.vehicle_informed ? 1 : 0,
                        hotel_informed: d.hotel_informed ? 1 : 0,
                        notes: d.notes || '',
                        services_provided: d.services_provided || '',
                    }));

                    const saveResponse = await fetch(`${API_URL}?action=updateItinerary`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ itinerary_days: itinerary_days_data })
                    });

                    const saveResult = await saveResponse.json();
                    if (saveResult.status !== 'success') {
                        throw new Error('Failed to save changes: ' + saveResult.message);
                    }

                    // Show panel and initial queued message
                    openEmailStatusPanel();
                    clearEmailStatus();
                    addEmailStatusItem('queued', 'Processing hotel emails...');

                    emailHotelsBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

                    const response = await fetch('../src/services/send_hotel_email.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            trip_id: tripId
                        })
                    });

                    if (!response.ok) {
                        const errorText = await response.text();
                        throw new Error(`Server Error ${response.status}: ${errorText}`);
                    }

                    const result = await response.json();
                    
                    // Update status panel with server messages
                    clearEmailStatus();
                    if (result.messages && result.messages.length) {
                        result.messages.forEach(m => addEmailStatusItem(m.type || 'info', m.text || ''));
                    } else {
                        addEmailStatusItem(result.status || 'info', result.message || 'No messages');
                    }

                    const activeDayBeforeFetch = document.querySelector('.tab-button.active')?.dataset.dayNumber;

                    // Re-fetch itinerary to get the latest 'informed' status and re-render the UI
                    await fetchItinerary();
                    
                    // After re-rendering, try to switch back to the previously active day
                    if(activeDayBeforeFetch) {
                        setTimeout(() => switchDay(activeDayBeforeFetch), 100);
                    }

                    if (result.status === 'success') {
                        showToast('Hotel emails processed.', 'success');
                    } else {
                        showToast(result.message, result.status || 'error');
                    }

                } catch (error) {
                    clearEmailStatus();
                    addEmailStatusItem('error', 'Request failed: ' + error.message);
                    showToast('Request Failed: ' + error.message, 'error');
                } finally {
                    emailHotelsBtn.innerHTML = '<i class="fas fa-envelope"></i> <span>Email Hotels</span>';
                    emailHotelsBtn.disabled = false;
                }
            };
            
                // Function to send email to ALL uninformed vehicles for the trip
            const sendVehicleEmail = async () => {
                const currentData = getCurrentFormData(currentItineraryDays);
                const assignedVehicles = currentData.filter(day => day.vehicle_id && day.vehicle_id !== '' && day.vehicle_id !== '0' && day.vehicle_id !== 0);
                if (assignedVehicles.length === 0) { showToast('No vehicle assignments found for this trip.', 'info'); return; }
                const anyUninformed = assignedVehicles.some(day => !day.vehicle_informed);
                if (!anyUninformed) { showToast('All assigned vehicles have already been informed.', 'info'); return; }

                emailVehiclesBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
                emailVehiclesBtn.disabled = true;
                try {
                    const itinerary_days_data = currentData.map(d => ({
                        id: d.id,
                        guide_id: d.guide_id || null,
                        vehicle_id: d.vehicle_id || null,
                        hotel_id: d.hotel_id || null,
                        room_type_data: d.room_quantities ? JSON.stringify(d.room_quantities) : JSON.stringify({double:0,twin:0,single:0,triple:0}),
                        guide_informed: d.guide_informed ? 1 : 0,
                        vehicle_informed: d.vehicle_informed ? 1 : 0,
                        hotel_informed: d.hotel_informed ? 1 : 0,
                        notes: d.notes || '',
                        services_provided: d.services_provided || ''
                    }));
                    const saveResponse = await fetch(`${API_URL}?action=updateItinerary`, { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ itinerary_days: itinerary_days_data })});
                    const saveResult = await saveResponse.json();
                    if (saveResult.status !== 'success') { throw new Error('Failed to save changes: ' + saveResult.message); }

                    openEmailStatusPanel();
                    clearEmailStatus();
                    addEmailStatusItem('queued','Processing vehicle emails...');

                    emailVehiclesBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
                    const response = await fetch('../src/services/send_vehicle_email.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ trip_id: tripId })});
                    if (!response.ok) { const txt = await response.text(); throw new Error(`Server Error ${response.status}: ${txt}`); }
                    const result = await response.json();
                    clearEmailStatus();
                    if (result.messages && result.messages.length) { result.messages.forEach(m => addEmailStatusItem(m.type||'info', m.text||'')); } else { addEmailStatusItem(result.status||'info', result.message||'No messages'); }

                    const activeDayBeforeFetch = document.querySelector('.tab-button.active')?.dataset.dayNumber;
                    await fetchItinerary();
                    if (activeDayBeforeFetch) setTimeout(() => switchDay(activeDayBeforeFetch), 100);

                    if (result.status === 'success') showToast('Vehicle emails processed.','success'); else showToast(result.message, result.status||'error');
                } catch (error) {
                    clearEmailStatus(); addEmailStatusItem('error','Request failed: ' + error.message); showToast('Request Failed: ' + error.message, 'error');
                } finally {
                    emailVehiclesBtn.innerHTML = '<i class="fas fa-truck"></i> <span>Email Vehicles</span>';
                    emailVehiclesBtn.disabled = false;
                }
            };

            // Function to send email to ALL uninformed guides for the trip
            const sendGuideEmail = async () => {
                // First, get current form data to check for unsaved changes
                const currentData = getCurrentFormData(currentItineraryDays);
                
                // Debug: Log guide_id values
                currentData.forEach((day, i) => {
                    console.log(`Day ${i+1}: guide_id="${day.guide_id}" (type: ${typeof day.guide_id})`);
                });
                
                // Check if there are any guide assignments at all
                const assignedGuides = currentData.filter(day => day.guide_id && day.guide_id !== '' && day.guide_id !== '0' && day.guide_id !== 0);
                
                if (assignedGuides.length === 0) {
                    showToast('No guide assignments found for this trip.', 'info');
                    return;
                }

                const uninformedGuides = assignedGuides.some(day => !day.guide_informed);
                if (!uninformedGuides) {
                    showToast('All assigned guides have already been informed.', 'info');
                    return;
                }

                // Save changes first before sending emails
                emailGuidesBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
                emailGuidesBtn.disabled = true;

                try {
                    // Save current form data
                    const itinerary_days_data = currentData.map(d => ({
                        id: d.id,
                        guide_id: d.guide_id || null,
                        vehicle_id: d.vehicle_id || null,
                        hotel_id: d.hotel_id || null,
                        room_type_data: d.room_quantities ? JSON.stringify(d.room_quantities) : JSON.stringify({double: 0, twin: 0, single: 0, triple: 0}),
                        guide_informed: d.guide_informed ? 1 : 0,
                        vehicle_informed: d.vehicle_informed ? 1 : 0,
                        hotel_informed: d.hotel_informed ? 1 : 0,
                        notes: d.notes || '',
                        services_provided: d.services_provided || '',
                    }));

                    const saveResponse = await fetch(`${API_URL}?action=updateItinerary`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ itinerary_days: itinerary_days_data })
                    });

                    const saveResult = await saveResponse.json();
                    if (saveResult.status !== 'success') {
                        throw new Error('Failed to save changes: ' + saveResult.message);
                    }

                    // Show panel and initial queued message
                    openEmailStatusPanel();
                    clearEmailStatus();
                    addEmailStatusItem('queued', 'Processing guide emails...');

                    emailGuidesBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

                    const response = await fetch('../src/services/send_guide_email.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            trip_id: tripId
                        })
                    });

                    if (!response.ok) {
                        const errorText = await response.text();
                        throw new Error(`Server Error ${response.status}: ${errorText}`);
                    }

                    const result = await response.json();
                    
                    // Update status panel with server messages
                    clearEmailStatus();
                    if (result.messages && result.messages.length) {
                        result.messages.forEach(m => addEmailStatusItem(m.type || 'info', m.text || ''));
                    } else {
                        addEmailStatusItem(result.status || 'info', result.message || 'No messages');
                    }

                    const activeDayBeforeFetch = document.querySelector('.tab-button.active')?.dataset.dayNumber;

                    // Re-fetch itinerary to get the latest 'informed' status and re-render the UI
                    await fetchItinerary();
                    
                    // After re-rendering, try to switch back to the previously active day
                    if(activeDayBeforeFetch) {
                        setTimeout(() => switchDay(activeDayBeforeFetch), 100);
                    }

                    if (result.status === 'success') {
                        showToast('Guide emails processed.', 'success');
                    } else {
                        showToast(result.message, result.status || 'error');
                    }

                } catch (error) {
                    clearEmailStatus();
                    addEmailStatusItem('error', 'Request failed: ' + error.message);
                    showToast('Request Failed: ' + error.message, 'error');
                } finally {
                    emailGuidesBtn.innerHTML = '<i class="fas fa-user-tie"></i> <span>Email Guides</span>';
                    emailGuidesBtn.disabled = false;
                }
            };
            
            // Function to check for guide conflicts
            const checkGuideConflicts = async (currentData) => {
                const conflicts = [];
                
                for (const dayData of currentData) {
                    if (dayData.guide_id) {
                        try {
                            const response = await fetch(`${API_URL}?action=checkGuideAvailability&guide_id=${dayData.guide_id}&day_date=${dayData.day_date}&trip_id=${tripId}`);
                            const result = await response.json();
                            
                            if (!result.available && result.conflicts.length > 0) {
                                const guideName = allGuides.find(g => g.id == dayData.guide_id)?.name || 'Unknown Guide';
                                const dayDate = new Date(dayData.day_date + 'T00:00:00').toLocaleDateString('en-US', { 
                                    weekday: 'long', 
                                    month: 'long', 
                                    day: 'numeric' 
                                });
                                
                                conflicts.push({
                                    day: dayDate,
                                    guide: guideName,
                                    conflictDetails: result.conflicts[0] // Take first conflict for display
                                });
                            }
                        } catch (error) {
                            console.error('Error checking guide availability:', error);
                        }
                    }
                }
                
                return conflicts;
            };
            
            itineraryForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
                saveBtn.disabled = true;

                const currentData = getCurrentFormData(currentItineraryDays);
                const activeDayBeforeSave = document.querySelector('.tab-button.active')?.dataset.dayNumber;
                
                // Check for guide conflicts before saving
                const conflicts = await checkGuideConflicts(currentData);
                if (conflicts.length > 0) {
                    let conflictMessage = 'Guide conflicts detected:\n\n';
                    conflicts.forEach(conflict => {
                        conflictMessage += `• ${conflict.guide} is already assigned on ${conflict.day} to trip "${conflict.conflictDetails.customer_name}" (${conflict.conflictDetails.tour_code})\n`;
                    });
                    conflictMessage += '\nPlease resolve these conflicts before saving.';
                    
                    alert(conflictMessage);
                    saveBtn.innerHTML = '<i class="fas fa-check"></i> <span>Save Changes</span>';
                    saveBtn.disabled = false;
                    return;
                }

                const itinerary_days_data = currentData.map(d => ({
                    id: d.id,
                    guide_id: d.guide_id || null,
                    vehicle_id: d.vehicle_id || null,
                    hotel_id: d.hotel_id || null,
                    room_type_data: d.room_quantities ? JSON.stringify(d.room_quantities) : JSON.stringify({double: 0, twin: 0, single: 0, triple: 0}),
                    guide_informed: d.guide_informed ? 1 : 0,
                    vehicle_informed: d.vehicle_informed ? 1 : 0,
                    hotel_informed: d.hotel_informed ? 1 : 0, 
                    notes: d.notes || '',
                    services_provided: d.services_provided || '',
                }));
                

                try {
                    const response = await fetch(`${API_URL}?action=updateItinerary`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ itinerary_days: itinerary_days_data })
                    });

                    const result = await response.json();
                    showToast(result.message, result.status);
                    
                    if (result.status === 'success') {
                        await fetchItinerary();
                        // After re-rendering, try to switch back to the previously active day
                        if(activeDayBeforeSave) {
                            setTimeout(() => switchDay(activeDayBeforeSave), 100);
                        }
                    }
                } catch (error) {
                    showToast('Error saving itinerary: ' + error.message, 'error');
                } finally {
                     saveBtn.innerHTML = '<i class="fas fa-check"></i> <span>Save Changes</span>';
                    saveBtn.disabled = false;
                }
            });
            
            fetchItinerary();
        });
    </script>

</body>
</html>