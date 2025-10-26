<?php 
require_once '../utils/check_session.php';
require_once '../src/services/db_connect.php';
?>
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
            padding: 14px 22px; 
            margin-right: 12px;
            background: linear-gradient(135deg, var(--surface) 0%, #f8fafc 100%);
            color: var(--text-secondary);
            border: 2px solid var(--border);
            border-radius: 12px;
            cursor: pointer;
            font-size: 1rem; 
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            appearance: none; 
            -webkit-appearance: none;
            position: relative;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .tab-button:hover:not(.active) {
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            border-color: var(--primary-light);
            color: var(--text-primary);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
        }

        .tab-button.active {
            padding: 14px 22px; 
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            border-color: var(--primary-color);
            box-shadow: 0 6px 16px rgba(99, 102, 241, 0.3);
            transform: translateY(-2px);
            position: relative;
            z-index: 2;
        }
        
        .tab-button.active::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(135deg, var(--primary-light), var(--primary-color));
            border-radius: 14px;
            z-index: -1;
            opacity: 0.5;
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
        .insights-view-wrapper { display:none; padding: 20px; border: 1px solid var(--border); border-radius: 14px; background: var(--surface); box-shadow: var(--shadow); min-height: 400px; }
        .insights-sections { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }
        .insight-card { border:1px solid var(--border); border-radius:10px; background:#fafafa; overflow:hidden; }
        .insight-card .head { padding:10px 12px; font-weight:800; border-bottom:1px solid var(--border-light); display:flex; align-items:center; gap:8px; }
        .insight-card .head.arrival { background:#e6f4ea; color:#166534; }
        .insight-card .head.departure { background:#eef2ff; color:#1e40af; }
        .insight-card .head.file-records-header { background:#fff3e0; color:#e65100; }
        .insight-list { padding: 8px 12px; display:flex; flex-direction:column; gap:8px; }
        .insight-row { display:grid; grid-template-columns: 110px 1fr 1fr; gap:10px; align-items:center; padding:8px; background:#fff; border:1px solid var(--border-light); border-radius:8px; }
        .insight-row .meta { font-size:0.85rem; color:var(--text-secondary); }
        .insight-badge { display:inline-block; padding:2px 8px; border-radius:999px; font-weight:800; font-size:0.72rem; }
        .insight-badge.arrival { background:#dff0e7; color:#166534; border:1px solid #bbdfcc; }
        .insight-badge.departure { background:#dce9ff; color:#1e40af; border:1px solid #c7dbff; }
        .file-records-package { margin-bottom: 12px; }
        .package-header { padding: 6px 0; margin-bottom: 8px; border-bottom: 1px solid var(--border-light); }
        .package-badge { display:inline-block; padding:4px 12px; border-radius:6px; font-weight:700; font-size:0.8rem; background:#fff3e0; color:#e65100; border:1px solid #ffcc80; }
        .file-record-row { display:grid; grid-template-columns: 110px 1fr 1fr; gap:10px; align-items:center; padding:8px; background:#fff; border:1px solid var(--border-light); border-radius:8px; margin-bottom: 6px; }
        .date-badge { display:inline-block; padding:2px 8px; border-radius:999px; font-weight:800; font-size:0.72rem; background:#e8f5e8; color:#2e7d32; border:1px solid #c8e6c9; }
        @media (max-width: 1200px){ .insights-sections{ grid-template-columns:1fr 1fr; } .insight-card.file-records { grid-column: span 2; } }
        @media (max-width: 900px){ .insights-sections{ grid-template-columns:1fr; } .insight-card.file-records { grid-column: span 1; } }

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
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 10px;
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
            min-width: 230px;
        }
        
        .summary-header {
            font-size: 0.85rem;
            font-weight: 700;
            padding-bottom: 4px;
            border-bottom: 1px solid var(--border);
            color: var(--secondary-dark);
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.78rem;
            padding: 2px 0;
            gap: 8px;
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
            font-size: 0.78rem;
        }

        .summary-value {
            font-weight: 700;
            text-align: right;
            color: var(--text-primary);
            max-width: 80%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: 0.78rem;
        }
        .summary-notes {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-align: left;
            max-width: 80%;
            font-weight: 500;
            color: var(--text-primary);
            font-size: 0.78rem;
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
        /* Readonly fake select for arrival vehicles */
        .fake-select {
            border: 1px solid var(--border);
            border-radius: 7px;
            background: var(--border-light);
            padding: 8px 10px;
        }
        .fake-select .fake-option { padding: 4px 0; font-size: 0.85rem; }
        .fake-select .fake-option.header { 
            color: var(--text-secondary); 
            font-weight: 600; 
            border-bottom: 1px dashed var(--border); 
            margin-bottom: 4px; 
            padding-bottom: 4px; 
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
        .btn-success { background: #10b981 !important; border-color: #10b981 !important; }
        
        /* Report buttons */
        .btn-report { border: none; padding: 14px 16px; border-radius: 0 12px 12px 0; font-weight: 800; font-size: 1rem; cursor: pointer; box-shadow: var(--shadow-md); display: inline-flex; align-items: center; gap: 10px; letter-spacing: 0.2px; }
        .btn-report i { font-size: 1rem; }
        .btn-arrival { background: #e6f4ea; color: #166534; border: 1px solid #bfe3cd; border-left: none; }
        .btn-arrival:hover { background: #dff0e7; border-color: #acd8bd; border-left: none; }
        .btn-departure { background: #eef2ff; color: #1e40af; border: 1px solid #c7d2fe; border-left: none; }
        .btn-departure:hover { background: #e6ecff; border-color: #bfc9fd; border-left: none; }
        .report-dock { position: fixed; left: 0; top: 50%; transform: translateY(-50%); display: flex; flex-direction: column; gap: 14px; z-index: 1050; transition: left 0.25s ease; }
        .report-dock.collapsed { left: -220px; }
        .report-dock .btn-report { width: 240px; justify-content: flex-start; }
        .report-handle { position:absolute; right:-24px; top:50%; transform: translateY(-50%); width:24px; height:64px; background:#e5e7eb; border:1px solid var(--border); border-left:none; border-radius:0 8px 8px 0; display:flex; align-items:center; justify-content:center; cursor:pointer; color:#6b7280; box-shadow: var(--shadow-sm); }
        .report-handle:hover { background:#f3f4f6; }
        @media (max-width: 768px) {
            .report-dock { left: 0; top: auto; bottom: 95px; transform: none; }
            .report-dock .btn-report { width: 200px; }
        }
        
        /* Dropup menus for grouped actions */
        .menu-item-success { background:#dcfce7 !important; color:#166534 !important; }
        .menu-item-success:hover { background:#bbf7d0 !important; }
        .dropup { position: relative; display: inline-block; }
        .dropup-menu {
            position: absolute; bottom: 44px; right: 0; min-width: 180px; background: var(--surface); border: 1px solid var(--border); border-radius: 8px; box-shadow: var(--shadow);
            padding: 6px; display: none; z-index: 1050;
        }
        .dropup.open .dropup-menu { display: block; }
        .dropup-menu a { display: block; padding: 8px 10px; color: var(--text-primary); text-decoration: none; border-radius: 6px; font-size: 0.9rem; }
        .dropup-menu a:hover { background: var(--border-light); }

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
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 0.65rem;
            font-weight: 600;
            margin: 1px;
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

        /* Tablet Styles */
        @media (max-width: 1024px) {
            .container {
                padding: 15px 15px;
            }

            .page-header {
                margin-bottom: 15px;
                padding-bottom: 10px;
            }

            .header-top {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }

            .trip-info h1 {
                font-size: 1.2rem;
            }

            .tabs-and-toggle {
                margin-bottom: 15px;
            }

            .day-tabs-nav {
                padding-left: 20px;
                padding-right: 20px;
            }

            .itinerary-grid, .summary-view-wrapper {
                padding: 25px;
                min-height: 600px;
            }

            .assignments-grid {
                gap: 12px;
            }

            .hotel-fields {
                grid-template-columns: 1fr 1fr;
            }

            .room-quantities-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            /* Adjust summary cards for tablet */
            .summary-card-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 8px;
            }

            .summary-card {
                min-width: 200px;
            }

            /* Make form actions more compact */
            .form-actions-buttons {
                gap: 10px;
            }

            .btn-save, .btn-export {
                padding: 12px 18px;
                font-size: 0.9rem;
            }
        }

        /* Mobile Styles */
        @media (max-width: 768px) {
            .container {
                padding: 10px;
                max-width: none;
            }

            .page-header {
                margin-bottom: 15px;
                padding-bottom: 10px;
            }

            .header-top {
                flex-direction: column;
                gap: 10px;
                align-items: stretch;
                text-align: center;
            }

            .trip-info {
                text-align: center;
            }

            .trip-info h1 {
                font-size: 1.1rem;
            }

            .trip-meta {
                justify-content: center;
            }

            .btn-back {
                align-self: center;
                padding: 6px 12px;
                font-size: 0.85rem;
            }

            .tabs-and-toggle {
                flex-direction: column;
                align-items: stretch;
                margin-bottom: 15px;
                border-bottom: none;
            }

            .day-tabs-wrapper {
                margin-right: 0;
                margin-bottom: 10px;
                position: relative;
                overflow: hidden;
            }

            .day-tabs-nav {
                padding-left: 0;
                padding-right: 0;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .tab-scroll-btn {
                display: none; /* Hide scroll buttons on mobile */
            }

            .tab-button {
                flex-shrink: 0;
                white-space: nowrap;
                padding: 12px 18px;
                font-size: 0.9rem;
                margin-right: 8px;
            }

            .tab-button.active {
                padding: 12px 18px;
            }

            .summary-toggle-btn {
                align-self: center;
                padding: 6px 12px;
                font-size: 0.85rem;
            }

            .itinerary-grid, .summary-view-wrapper {
                padding: 15px;
                min-height: 500px;
                border-radius: 10px;
            }

            .day-content-wrapper > div:first-child {
                font-size: 1rem;
                margin-bottom: 10px;
            }

            .assignments-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .hotel-group, .hotel-fields {
                grid-column: span 1;
                grid-template-columns: 1fr;
            }

            .hotel-fields {
                gap: 10px;
            }

            .room-quantities-grid {
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }

            .room-quantity-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 4px;
            }

            .room-quantity-item label {
                margin: 0;
                font-size: 0.8rem;
            }

            .room-quantity-item input {
                width: 100%;
                padding: 6px;
                font-size: 0.85rem;
            }

            .form-group textarea {
                min-height: 120px;
                font-size: 0.9rem;
            }

            .form-actions {
                padding: 15px 10px;
                flex-direction: column;
                gap: 10px;
            }

            .save-info {
                text-align: center;
                margin-bottom: 5px;
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
                padding: 12px 8px;
                font-size: 0.85rem;
            }

            .btn-save {
                flex-basis: 100%;
                order: 4;
            }

            /* Adjust summary view for mobile */
            .summary-card-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .summary-card {
                min-width: auto;
                padding: 12px;
            }

            .summary-header {
                font-size: 0.8rem;
                padding-bottom: 3px;
            }

            .summary-item {
                font-size: 0.75rem;
                padding: 1px 0;
            }

            .summary-value {
                font-size: 0.75rem;
                max-width: 70%;
            }

            .summary-notes {
                font-size: 0.75rem;
                max-width: 70%;
            }

            /* Adjust insights view for mobile */
            .insights-sections {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .insight-card {
                border-radius: 8px;
            }

            .insight-card .head {
                padding: 8px 10px;
                font-size: 0.9rem;
            }

            .insight-list {
                padding: 6px 10px;
            }

            .insight-row {
                grid-template-columns: 100px 1fr;
                gap: 8px;
                padding: 6px;
                font-size: 0.85rem;
            }

            .file-record-row {
                grid-template-columns: 100px 1fr;
                gap: 8px;
                padding: 6px;
                font-size: 0.85rem;
            }

            /* Adjust report modals for mobile */
            .report-modal .report-card {
                width: 95vw;
                max-height: 80vh;
            }

            .report-header {
                padding: 10px 12px;
                font-size: 0.9rem;
            }

            .report-body {
                padding: 10px 12px;
            }

            .report-row {
                padding: 8px;
                margin: 6px 0;
            }

            .kv {
                gap: 8px 12px;
                font-size: 0.85rem;
            }

            /* Adjust email status panel for mobile */
            .email-status-panel {
                right: 10px;
                bottom: 80px;
                width: calc(100vw - 20px);
                max-width: 400px;
            }

            .email-status-header {
                padding: 8px 10px;
                font-size: 0.85rem;
            }

            .email-status-body {
                padding: 6px 10px;
            }

            .email-status-item {
                padding: 6px 2px;
                font-size: 0.85rem;
            }

            /* Adjust mini modal for mobile */
            .mini-modal .content {
                width: 90vw;
                max-width: 350px;
            }

            .mini-modal .header {
                padding: 8px 12px;
                font-size: 0.9rem;
            }

            .mini-modal .body {
                padding: 12px;
            }

            .mini-modal .footer {
                padding: 8px 12px;
            }

            /* Adjust toast for mobile */
            .toast {
                width: 90%;
                max-width: 350px;
                left: 50%;
                transform: translateX(-50%);
                bottom: 20px;
            }
        }

        /* Small Mobile Styles */
        @media (max-width: 576px) {
            .container {
                padding: 8px;
            }

            .page-header {
                margin-bottom: 10px;
            }

            .trip-info h1 {
                font-size: 1rem;
            }

            .trip-meta {
                font-size: 0.75rem;
            }

            .btn-back {
                padding: 5px 10px;
                font-size: 0.8rem;
            }

            .tab-button {
                padding: 10px 14px;
                font-size: 0.85rem;
                margin-right: 6px;
            }

            .tab-button.active {
                padding: 10px 14px;
            }

            .summary-toggle-btn {
                padding: 5px 10px;
                font-size: 0.8rem;
            }

            .itinerary-grid, .summary-view-wrapper {
                padding: 12px;
                min-height: 450px;
            }

            .day-content-wrapper > div:first-child {
                font-size: 0.95rem;
            }

            .section-card {
                padding: 12px;
                margin-bottom: 10px;
            }

            .form-group label {
                font-size: 0.8rem;
            }

            .custom-select select {
                padding: 6px 8px;
                font-size: 0.85rem;
            }

            .form-group textarea {
                min-height: 100px;
                padding: 10px;
                font-size: 0.85rem;
            }

            .services-block {
                padding: 10px;
            }

            .services-block label {
                font-size: 0.8rem;
                margin: 3px 0;
            }

            .form-actions {
                padding: 12px 8px;
            }

            .btn-save, .btn-export {
                padding: 10px 6px;
                font-size: 0.8rem;
            }

            .summary-card {
                padding: 10px;
            }

            .summary-header {
                font-size: 0.75rem;
            }

            .summary-item {
                font-size: 0.7rem;
            }

            /* Adjust insights for small screens */
            .insight-row, .file-record-row {
                grid-template-columns: 80px 1fr;
                padding: 4px;
                font-size: 0.8rem;
            }

            .insight-card .head {
                font-size: 0.85rem;
            }

            /* Make report modals more compact */
            .report-modal .report-card {
                width: 100vw;
                margin: 10px;
                max-height: 85vh;
            }

            .report-row {
                padding: 6px;
                margin: 4px 0;
            }

            .kv div {
                min-width: 40%;
            }

            /* Adjust email panel for small screens */
            .email-status-panel {
                right: 5px;
                bottom: 70px;
                width: calc(100vw - 10px);
            }

            .mini-modal .content {
                width: 95vw;
            }

            /* Adjust toast for small screens */
            .toast {
                width: 95%;
                bottom: 15px;
            }
        }

        /* Extra Small Screens */
        @media (max-width: 480px) {
            .container {
                padding: 5px;
            }

            .page-header {
                margin-bottom: 8px;
            }

            .header-top {
                gap: 8px;
            }

            .trip-info h1 {
                font-size: 0.95rem;
            }

            .trip-meta {
                font-size: 0.7rem;
            }

            .btn-back {
                padding: 4px 8px;
                font-size: 0.75rem;
            }

            .tabs-and-toggle {
                margin-bottom: 10px;
            }

            .tab-button {
                padding: 8px 12px;
                font-size: 0.8rem;
                margin-right: 4px;
            }

            .tab-button.active {
                padding: 8px 12px;
            }

            .summary-toggle-btn {
                padding: 4px 8px;
                font-size: 0.75rem;
            }

            .itinerary-grid, .summary-view-wrapper {
                padding: 10px;
                min-height: 400px;
            }

            .assignments-grid {
                gap: 10px;
            }

            .section-card {
                padding: 10px;
                margin-bottom: 8px;
            }

            .form-group {
                gap: 3px;
            }

            .form-group label {
                font-size: 0.75rem;
            }

            .custom-select select {
                padding: 5px 7px;
                font-size: 0.8rem;
            }

            .form-group textarea {
                min-height: 80px;
                padding: 8px;
                font-size: 0.8rem;
            }

            .services-block {
                padding: 8px;
            }

            .services-block label {
                font-size: 0.75rem;
                margin: 2px 0;
            }

            .room-quantities-grid {
                gap: 8px;
            }

            .room-quantity-item input {
                padding: 5px;
                font-size: 0.8rem;
            }

            .form-actions {
                padding: 10px 6px;
            }

            .save-info {
                font-size: 0.8rem;
            }

            .btn-save, .btn-export {
                padding: 8px 4px;
                font-size: 0.75rem;
            }

            .summary-card-grid {
                gap: 8px;
            }

            .summary-card {
                padding: 8px;
            }

            .summary-header {
                font-size: 0.7rem;
            }

            .summary-item {
                font-size: 0.65rem;
                padding: 0;
            }

            .summary-value, .summary-notes {
                font-size: 0.65rem;
                max-width: 65%;
            }

            /* Adjust insights for very small screens */
            .insights-sections {
                gap: 10px;
            }

            .insight-card {
                border-radius: 6px;
            }

            .insight-card .head {
                padding: 6px 8px;
                font-size: 0.8rem;
            }

            .insight-list {
                padding: 4px 8px;
                gap: 6px;
            }

            .insight-row, .file-record-row {
                grid-template-columns: 70px 1fr;
                gap: 6px;
                padding: 3px;
                font-size: 0.75rem;
            }

            .date-badge {
                font-size: 0.7rem;
            }

            /* Make report modals very compact */
            .report-modal .report-card {
                margin: 5px;
                max-height: 90vh;
            }

            .report-header {
                padding: 8px 10px;
                font-size: 0.85rem;
            }

            .report-body {
                padding: 8px 10px;
            }

            .report-row {
                padding: 4px;
                margin: 3px 0;
                font-size: 0.8rem;
            }

            .kv {
                gap: 6px 8px;
                font-size: 0.8rem;
            }

            /* Adjust email panel for very small screens */
            .email-status-panel {
                right: 2px;
                bottom: 60px;
                width: calc(100vw - 4px);
            }

            .email-status-header {
                padding: 6px 8px;
                font-size: 0.8rem;
            }

            .email-status-body {
                padding: 4px 8px;
            }

            .email-status-item {
                padding: 4px 1px;
                font-size: 0.8rem;
            }

            .mini-modal .content {
                width: 100vw;
                margin: 10px;
            }

            .mini-modal .header {
                padding: 6px 10px;
                font-size: 0.85rem;
            }

            .mini-modal .body {
                padding: 10px;
            }

            .mini-modal .footer {
                padding: 6px 10px;
            }

            .mini-modal .btn {
                padding: 6px 10px;
                font-size: 0.8rem;
            }

            /* Adjust toast for very small screens */
            .toast {
                width: 98%;
                bottom: 10px;
                font-size: 0.85rem;
                padding: 10px 20px;
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
                <div class="insights-view-wrapper" id="insightsView"></div>

                <div class="form-actions">
                    <div class="save-info">
                        <i class="fas fa-info-circle"></i>
                        <span>Changes will be saved to database</span>
                    </div>
                    <div class="form-actions-buttons">
                        <!-- Order: Assign (left) • Email • Export • Save (right) -->
                        
                        <div class="dropup" id="assignDropup">
                            <button type="button" id="assignDropBtn" class="btn-export">
                                <i class="fas fa-tasks"></i>
                                <span>Assign</span>
                            </button>
                            <div class="dropup-menu" id="assignMenu">
                                <a href="#" id="assignGuidesItem"><i class="fas fa-user-tie"></i> Assign Guides</a>
                                <a href="#" id="assignHotelsItem"><i class="fas fa-hotel"></i> Assign Hotels</a>
                                <a href="#" id="assignVehiclesItem"><i class="fas fa-truck"></i> Assign Vehicles</a>
                            </div>
                        </div>
                        
                        <!-- Email dropup -->
                        <div class="dropup" id="emailDropup">
                            <button type="button" id="emailDropBtn" class="btn-export">
                                <i class="fas fa-envelope"></i>
                                <span>Email</span>
                            </button>
                            <div class="dropup-menu" id="emailMenu">
                                <a href="#" id="emailGuidesItem"><i class="fas fa-user-tie"></i> Email Guides</a>
                                <a href="#" id="emailHotelsItem"><i class="fas fa-hotel"></i> Email Hotels</a>
                                <a href="#" id="emailVehiclesItem"><i class="fas fa-truck"></i> Email Vehicles</a>
                            </div>
                        </div>

                        <!-- Export CSV -->
                        <button type="button" id="exportCsvBtn" class="btn-export">
                            <i class="fas fa-file-csv"></i>
                            <span>Export to CSV</span>
                        </button>

                        <!-- Save (rightmost) -->
                        <button type="submit" class="btn-save">
                            <i class="fas fa-check"></i>
                            <span>Save Changes</span>
                        </button>

                        <!-- Hidden individual buttons kept for internal logic -->
                        <button type="button" id="emailGuidesBtn" class="btn-export" style="display:none">
                            <i class="fas fa-user-tie"></i>
                            <span>Email Guides</span>
                        </button>
                        <button type="button" id="emailHotelsBtn" class="btn-export" style="display:none">
                            <i class="fas fa-envelope"></i>
                            <span>Email Hotels</span>
                        </button>
                        <button type="button" id="emailVehiclesBtn" class="btn-export" style="display:none">
                            <i class="fas fa-truck"></i>
                            <span>Email Vehicles</span>
                        </button>
                        <button type="button" id="assignGuidesWizardBtn" class="btn-export" style="display:none" title="Assign guides on required days">
                            <i class="fas fa-person-chalkboard"></i>
                            <span>Assign Guides</span>
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

    <!-- Report Modals -->
    <style>
      .report-modal { position: fixed; inset: 0; background: rgba(0,0,0,0.35); display: none; align-items:center; justify-content:center; z-index: 1200; }
      .report-card { width: 680px; max-height: 75vh; background:#fff; border-radius:12px; box-shadow: var(--shadow-md); overflow:hidden; display:flex; flex-direction:column; }
      .report-header { padding:12px 16px; display:flex; align-items:center; justify-content:space-between; font-weight:800; }
      .report-header.arrival { background:#e8f5ee; color:#166534; border-bottom:1px solid #bbdfcc; }
      .report-header.departure { background:#e6f0ff; color:#1e40af; border-bottom:1px solid #c7dbff; }
      .report-body { padding:12px 16px; overflow:auto; }
      .report-row { border:1px solid var(--border); border-radius:8px; padding:10px; margin:8px 0; background:#fafafa; }
      .report-row .title { font-weight:700; margin-bottom:6px; }
      .kv { display:flex; flex-wrap:wrap; gap:10px 16px; font-size:0.9rem; }
      .kv div { min-width: 48%; }
      .badge { display:inline-block; padding:2px 8px; border-radius:999px; font-size:0.72rem; font-weight:800; }
      .badge.arrival { background:#dff0e7; color:#166534; border:1px solid #bbdfcc; }
      .badge.departure { background:#dce9ff; color:#1e40af; border:1px solid #c7dbff; }
      .report-close { cursor:pointer; color:#6b7280; }
    </style>
    <div id="arrivalsReportModal" class="report-modal" aria-hidden="true">
      <div class="report-card">
        <div class="report-header arrival"><span><i class="fas fa-plane-arrival"></i> Arrival Details</span><span class="report-close" data-target="arrivalsReportModal"><i class="fas fa-times"></i></span></div>
        <div class="report-body" id="arrivalsReportBody"></div>
      </div>
    </div>
    <div id="departuresReportModal" class="report-modal" aria-hidden="true">
      <div class="report-card">
        <div class="report-header departure"><span><i class="fas fa-plane-departure"></i> Departure Details</span><span class="report-close" data-target="departuresReportModal"><i class="fas fa-times"></i></span></div>
        <div class="report-body" id="departuresReportBody"></div>
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
            const emailDropBtn = document.getElementById('emailDropBtn');
            const emailStatusPanel = document.getElementById('emailStatusPanel');
            const emailStatusList = document.getElementById('emailStatusList');
            // Report modals
            const arrivalsReportBtn = document.getElementById('arrivalsReportBtn');
            const departuresReportBtn = document.getElementById('departuresReportBtn');
            const arrivalsReportModal = document.getElementById('arrivalsReportModal');
            const departuresReportModal = document.getElementById('departuresReportModal');
            const arrivalsReportBody = document.getElementById('arrivalsReportBody');
            const departuresReportBody = document.getElementById('departuresReportBody');
            const emailStatusClose = document.getElementById('emailStatusClose');
            const emailStatusMinMax = document.getElementById('emailStatusMinMax');
            
            const urlParams = new URLSearchParams(window.location.search);
            const tripId = urlParams.get('trip_id');
            const focusDateParam = urlParams.get('focus_date');
            const focusDayParam = parseInt(urlParams.get('focus_day')||'0',10) || null;

            let allGuides = [];
            let allVehicles = [];
            let allHotels = [];
            let currentItineraryDays = [];
            let packageHotels = [];
            let packageRequirements = [];
            let roomTypes = [];
            let arrivalsByDate = {};

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
                    const response = await fetch(`${API_URL}?action=getItinerary&trip_id=${tripId}&_=${cacheBuster}`, { headers: { 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' }, cache: 'no-store' });
                    const text = await response.text();
                    let result;
                    try { result = JSON.parse(text); } catch(e){
                        console.error('Non-JSON response for getItinerary:', text.substring(0,500));
                        itineraryGrid.innerHTML = `<div class=\"error-message\">Server returned invalid response.<br><small>${text.substring(0,200).replace(/</g,'&lt;')}</small></div>`;
                        return;
                    }
                    
                    if (result.status !== 'success') {
                        itineraryGrid.innerHTML = `<div class=\"error-message\">${result.message}</div>`;
                        return;
                    }

                    console.log('API response received:', result);
                    const { trip, itinerary_days, guides, vehicles, hotels, arrivals, departures } = result.data;
                    
                    allGuides = guides;
                    allVehicles = vehicles;
                    allHotels = hotels;
                    currentItineraryDays = itinerary_days;
                    window.__trip__ = trip; // expose for reports
                    window.__hotels__ = hotels || [];
                    window.__guides__ = guides || [];
                    window.__vehicles__ = vehicles || [];
                    window.__arrivals__ = arrivals || [];
                    window.__departures__ = departures || [];
                    // Compute Day 2 defaults
                    let day2Date = (itinerary_days && itinerary_days[1] && itinerary_days[1].day_date) ? itinerary_days[1].day_date : '';
                    let day2HotelId = (itinerary_days && itinerary_days[1] && itinerary_days[1].hotel_id) ? itinerary_days[1].hotel_id : '';
                    window.__day2HotelId__ = day2HotelId;
                    window.__day2Date__ = day2Date;
                    let lastDate = (itinerary_days && itinerary_days[itinerary_days.length-1] && itinerary_days[itinerary_days.length-1].day_date) ? itinerary_days[itinerary_days.length-1].day_date : '';
                    let lastHotelId = (itinerary_days && itinerary_days[itinerary_days.length-1] && itinerary_days[itinerary_days.length-1].hotel_id) ? itinerary_days[itinerary_days.length-1].hotel_id : '';
                    window.__lastHotelId__ = lastHotelId;
                    window.__lastDate__ = lastDate;
                    try {
                        // Guest info disabled for 2026/2027 files
                    } catch(_){}
                    // Build arrivals by date map for display with group codes A1, A2...
                    arrivalsByDate = {};
                    (arrivals||[]).forEach(a=>{
                        const d = a.arrival_date;
                        if (!arrivalsByDate[d]) arrivalsByDate[d] = [];
                        const idx = arrivalsByDate[d].length; // 0-based
                        const group = `A${idx+1}`;
                        const v = vehicles.find(vv=> String(vv.id)===String(a.vehicle_id));
                        let name = v? (v.vehicle_name||'Vehicle') : 'Vehicle';
                        if (v && v.number_plate) name += ` (${v.number_plate})`;
                        if (a.arrival_time) name += ` @ ${a.arrival_time}`;
                        arrivalsByDate[d].push({ group, label: name });
                    });
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
                    
                    document.getElementById('tripTitle').textContent = `${trip.file_name || trip.tour_code || 'Trip'} — ${trip.package_name}`;
                    document.getElementById('tripTitle').textContent = trip.file_name || trip.tour_code || 'Trip Details';
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
                        // Jump to requested date/day if provided
                        try {
                            if (focusDayParam && Number.isInteger(focusDayParam)) {
                                switchDay(focusDayParam);
                            } else if (focusDateParam) {
                                const idx = itinerary_days.findIndex(d => String(d.day_date) === String(focusDateParam));
                                if (idx !== -1) switchDay(idx+1);
                            }
                        } catch(_){}
                    } catch (error) {
                        console.error('Error rendering tabs/summary:', error);
                    }
                    
                    summaryToggleBtn.addEventListener('click', () => {
                        const currentMode = summaryToggleBtn.dataset.viewMode || 'details';
                        let nextMode;
                        if (currentMode === 'details') {
                            nextMode = 'summary';
                        } else if (currentMode === 'summary') {
                            nextMode = 'insights';
                        } else {
                            nextMode = 'details';
                        }
                        toggleView(nextMode);
                    });
                    
                    exportCsvBtn.addEventListener('click', exportToCSV);
                    emailHotelsBtn.addEventListener('click', sendHotelEmail);
                    emailGuidesBtn.addEventListener('click', sendGuideEmail);
                    emailVehiclesBtn.addEventListener('click', sendVehicleEmail);
                    
                    scrollLeftBtn.addEventListener('click', () => scrollTabs('left'));
                    scrollRightBtn.addEventListener('click', () => scrollTabs('right'));
                    
                    toggleView('details');
                    // Reports: wire buttons now that data loaded
                    setupReportButtons();
                    setupReportDockToggle();
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

                    // Fallback to package requirements if services missing
                    if (!servicesProvided && packageRequirements && packageRequirements.length > 0) {
                        const req = packageRequirements.find(r => r.day_number === dayCounter);
                        if (req && req.day_services) {
                            servicesProvided = req.day_services;
                            hasBreakfast = servicesProvided.includes('B');
                            hasLunch = servicesProvided.includes('L');
                            hasDinner = servicesProvided.includes('D');
                        }
                    }

                    
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
                    
                    // Check if guide/vehicle are required for this day
                    const packageReq = packageRequirements.find(req => req.day_number === dayCounter);
                    const showGuide = !packageReq || packageReq.guide_required == 1;
                    const showVehicle = !packageReq || packageReq.vehicle_required == 1;
                    const vehicleTypeLabel = packageReq && packageReq.vehicle_type ? 
                        ` (${packageReq.vehicle_type.charAt(0).toUpperCase() + packageReq.vehicle_type.slice(1)})` : '';

                    // Only show hotel, rooms, and services if package has a hotel for this day
                    const showHotelGroup = !!(packageReq && packageReq.hotel_id);
                    // Services only visible when hotel block is shown
                    showServices = showHotelGroup && (hasBreakfast || hasLunch || hasDinner || !!day.hotel_id);

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

                    const arrivalsTodayObjs = (arrivalsByDate[day.day_date]||[]);
                    // Build arrival vehicles optgroup for the day vehicle dropdown (read-only entries)
                    let arrivalsOptGroup = '';
                    let arrivalSummaryOption = '';
                    const hasArrivalsToday = arrivalsTodayObjs.length > 0;
                    if (hasArrivalsToday){
                        arrivalsOptGroup = '<optgroup label="Arrival Vehicles">' + arrivalsTodayObjs.map(o=> `<option value="" disabled>[${o.group}] ${o.label}</option>`).join('') + '</optgroup>';
                        // We will not render the dropdown at all for arrival days; keep vars for fallback
                        if (!day.vehicle_id){
                            const header = `<option value=\"\" disabled selected>Arrival Vehicles</option>`;
                            const lines = arrivalsTodayObjs.map(o=>`<option value=\"\" disabled>[${o.group}] ${o.label}</option>`).join('');
                            arrivalSummaryOption = header + lines;
                        }
                    }

                    // Build vehicle block: dropdown for normal days, read-only fake select for arrival days
                    let vehicleBlockHTML = '';
                    const vehicleLabelSuffix = hasArrivalsToday ? ' (Arrival)' : `${vehicleTypeLabel}`;
                    if (showVehicle){
                        if (hasArrivalsToday){
                            const items = arrivalsTodayObjs.map(o=>`<div class=\"fake-option\">[${o.group}] ${o.label}</div>`).join('');
                            vehicleBlockHTML = `
                            <div class=\"form-group\"> 
                                <div class=\"form-group-controls\"> 
                                    <label><i class=\"fas fa-car\"></i> Vehicle${vehicleLabelSuffix}</label>
                                    <div class=\"fake-select\">
                                        <div class=\"fake-option header\">Arrival Vehicles</div>
                                        ${items}
                                    </div>
                                    ${createInformedSwitch(day.id, 'vehicle', day.vehicle_informed)}
                                </div>
                            </div>`;
                        } else {
                            vehicleBlockHTML = `
                            <div class=\"form-group\"> 
                                <div class=\"form-group-controls\"> 
                                    <label for=\"day_${day.id}_vehicle_id\"><i class=\"fas fa-car\"></i> Vehicle${vehicleLabelSuffix}</label>
                                    <div class=\"custom-select\"> 
                                        <select id=\"day_${day.id}_vehicle_id\" name=\"day_${day.id}_vehicle_id\">${vehicleOptions}${arrivalsOptGroup}</select>
                                    </div>
                                    ${createInformedSwitch(day.id, 'vehicle', day.vehicle_informed)}
                                </div>
                            </div>`;
                        }
                    }

                    dayContentWrapper.innerHTML = `
                        <div style=\"font-size: 1.1rem; font-weight: 600; margin-bottom: 12px; border-bottom: 1px dashed var(--border-light); padding-bottom: 8px; display: flex; justify-content: space-between; align-items: center;\">
                            <span>Day ${dayCounter} – ${dateString}</span>
                        </div>
                        <div class=\"assignments-grid\">
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
                            ${vehicleBlockHTML}
                            ${showHotelGroup ? `
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
                            ` : ''}
                            <div class="form-group notes-section">
                                <label for="day_${day.id}_notes"><i class="fas fa-list-ul"></i> Activities & Notes</label>
                                <textarea id=\"day_${day.id}_notes\" name=\"day_${day.id}_notes\" placeholder=\"Add activities, destinations, or special instructions...\">${(day.notes && day.notes.length>0) ? day.notes : ((packageRequirements.find(r => r.day_number === dayCounter)?.day_notes) || '')}</textarea>
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
                        setTimeout(()=>{ try{ updateAssignMenuStatus(); }catch(e){} }, 300);
                    }
                    
                    // Guide overlap prompt on change
                    const guideSelect = document.getElementById(`day_${day.id}_guide_id`);
                    if (guideSelect){
                        guideSelect.dataset.prev = guideSelect.value || '';
                        guideSelect.addEventListener('focus', function(){ this.dataset.prev = this.value || ''; });
                        guideSelect.addEventListener('change', async function(){
                            const newVal = this.value || '';
                            const prevVal = this.dataset.prev || '';
                            if (!newVal || newVal === prevVal) { this.dataset.prev = newVal; return; }
                            try {
                                const resp = await fetch(`${API_URL}?action=checkGuideConflict`, { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ guide_id: parseInt(newVal,10), date: day.day_date, exclude_trip_id: tripId })});
                                const text = await resp.text();
                                let js; try { js = JSON.parse(text); } catch(e){ js = { status:'error' }; }
                                if (js.status==='success' && Array.isArray(js.data) && js.data.length){
                                    const c = js.data[0];
                                    // Build inline notification with actions
                                    const holder = this.parentElement;
                                    let bar = holder.querySelector('.guide-conflict-bar');
                                    if (bar) bar.remove();
                                    bar = document.createElement('div');
                                    bar.className = 'guide-conflict-bar';
                                    bar.style.cssText = 'margin-top:6px; background:#FFF4E5; border:1px solid #F59E0B; color:#92400E; padding:8px; border-radius:6px; display:flex; gap:8px; align-items:center; font-size:0.85rem;';
                                    bar.innerHTML = `<span style="flex:1;">Guide already assigned on ${c.day_date} for Trip #${String(c.trip_id).padStart(3,'0')} (${c.tour_code||'N/A'}) - ${c.customer_name}.</span>
                                                     <button type="button" class="allow btn" style="background:#10b981;color:#fff;border:none;border-radius:4px;padding:4px 8px;">Allow</button>
                                                     <button type="button" class="cancel btn" style="background:#ef4444;color:#fff;border:none;border-radius:4px;padding:4px 8px;">Cancel</button>`;
                                    holder.appendChild(bar);
                                    const allowBtn = bar.querySelector('.allow');
                                    const cancelBtn = bar.querySelector('.cancel');
                                    allowBtn.addEventListener('click', ()=>{ bar.remove(); this.dataset.prev = newVal; showToast('Overlap allowed for this guide.','info'); });
                                    cancelBtn.addEventListener('click', ()=>{ this.value = prevVal; bar.remove(); showToast('Selection reverted due to conflict.','error'); });
                                    return; // wait for user choice
                                }
                                this.dataset.prev = newVal;
                            } catch(e){ this.dataset.prev = newVal; }
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
            
            function openReport(id){ const el = document.getElementById(id); if (el) el.style.display = 'flex'; }
            function closeReport(id){ const el = document.getElementById(id); if (el) el.style.display = 'none'; }
            function setupReportDockToggle(){
                const dock = document.getElementById('reportButtonsDock');
                const toggle = document.getElementById('reportDockToggle');
                if (!dock || !toggle) return;
                toggle.addEventListener('click', ()=>{
                    dock.classList.toggle('collapsed');
                    const icon = toggle.querySelector('i');
                    if (dock.classList.contains('collapsed')) { icon.classList.remove('fa-chevron-left'); icon.classList.add('fa-chevron-right'); }
                    else { icon.classList.remove('fa-chevron-right'); icon.classList.add('fa-chevron-left'); }
                });
            }
            function setupReportButtons(){
                // Close handlers
                document.querySelectorAll('.report-close').forEach(btn=>btn.addEventListener('click', (e)=>{ const t=e.currentTarget.getAttribute('data-target'); closeReport(t); }));
                [arrivalsReportModal, departuresReportModal].forEach(m=>{ if(!m)return; m.addEventListener('click', (e)=>{ if(e.target===m) closeReport(m.id); }); });
                if (arrivalsReportBtn){ arrivalsReportBtn.addEventListener('click', ()=>{ buildArrivalsReport(); openReport('arrivalsReportModal'); }); }
                if (departuresReportBtn){ departuresReportBtn.addEventListener('click', ()=>{ buildDeparturesReport(); openReport('departuresReportModal'); }); }
            }
            function fmt(t){ return (t&&String(t).trim().length)?t:'—'; }
            function buildArrivalsReport(){
                const arr = (window.__arrivals__||[]);
                if (!arr.length){ arrivalsReportBody.innerHTML = '<div class="report-row">No arrivals recorded.</div>'; return; }
                // Group by date then assign group codes
                const byDate = {};
                arr.forEach(a=>{ const d=a.arrival_date; if(!byDate[d]) byDate[d]=[]; byDate[d].push(a); });
                const hotelName = (id)=>{ const h=allHotels.find(x=>String(x.id)===String(id)); return h? h.name : ''; };
                const vehicleLabel = (id)=>{ const v=allVehicles.find(x=>String(x.id)===String(id)); if(!v) return ''; const plate=v.number_plate?` (${v.number_plate})`:''; return `${v.vehicle_name}${plate}`; };
                let html='';
                Object.keys(byDate).sort().forEach(date=>{
                    const list = byDate[date];
                    list.forEach((a,idx)=>{
                        const group = `A${idx+1}`;
                        html += `
                        <div class="report-row">
                          <div class="title"><span class="badge arrival">${group}</span> ${date} ${fmt(a.arrival_time)}</div>
                          <div class="kv">
                            <div><strong>Flight:</strong> ${fmt(a.flight_no)}</div>
                            <div><strong>Pax:</strong> ${fmt(a.pax_count)}</div>
                            <div><strong>Pickup:</strong> ${fmt(a.pickup_location)}</div>
                            <div><strong>Drop Hotel:</strong> ${fmt(hotelName(a.drop_hotel_id))}</div>
                            <div><strong>Vehicle:</strong> ${fmt(vehicleLabel(a.vehicle_id))}</div>
                            <div><strong>Guide:</strong> ${fmt((allGuides.find(g=>String(g.id)===String(a.guide_id))||{}).name)}</div>
                            <div style="flex:1 1 100%"><strong>Notes:</strong> ${fmt(a.notes)}</div>
                          </div>
                        </div>`;
                    });
                });
                arrivalsReportBody.innerHTML = html;
            }
            function buildDeparturesReport(){
                const dep = (window.__departures__||[]);
                if (!dep.length){ departuresReportBody.innerHTML = '<div class="report-row">No departures recorded.</div>'; return; }
                const byDate = {};
                dep.forEach(d=>{ const dt=d.departure_date; if(!byDate[dt]) byDate[dt]=[]; byDate[dt].push(d); });
                const hotelName = (id)=>{ const h=allHotels.find(x=>String(x.id)===String(id)); return h? h.name : ''; };
                const vehicleLabel = (id)=>{ const v=allVehicles.find(x=>String(x.id)===String(id)); if(!v) return ''; const plate=v.number_plate?` (${v.number_plate})`:''; return `${v.vehicle_name}${plate}`; };
                let html='';
                Object.keys(byDate).sort().forEach(date=>{
                    const list = byDate[date];
                    list.forEach((d,idx)=>{
                        const group = `D${idx+1}`;
                        html += `
                        <div class="report-row">
                          <div class="title"><span class="badge departure">${group}</span> ${date} ${fmt(d.departure_time)}</div>
                          <div class="kv">
                            <div><strong>Flight:</strong> ${fmt(d.flight_no)}</div>
                            <div><strong>Pax:</strong> ${fmt(d.pax_count)}</div>
                            <div><strong>From:</strong> ${fmt(hotelName(d.pickup_hotel_id))}</div>
                            <div><strong>Guests:</strong> ${fmt(d.pickup_location)}</div>
                            <div><strong>Vehicle:</strong> ${fmt(vehicleLabel(d.vehicle_id))}</div>
                            <div><strong>Guide:</strong> ${fmt((allGuides.find(g=>String(g.id)===String(d.guide_id))||{}).name)}</div>
                            <div style="flex:1 1 100%"><strong>Notes:</strong> ${fmt(d.notes)}</div>
                          </div>
                        </div>`;
                    });
                });
                departuresReportBody.innerHTML = html;
            }

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
            // Debounced autosave after room propagation
            let autoSaveTimer = null;
            const scheduleAutoSave = () => {
                if (autoSaveTimer) clearTimeout(autoSaveTimer);
                autoSaveTimer = setTimeout(async () => {
                    try {
                        const currentData = getCurrentFormData(currentItineraryDays);
                        const payload = currentData.map(d => ({
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
                        await fetch(`${API_URL}?action=updateItinerary`, { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ itinerary_days: payload }) });
                    } catch (e) {
                        // Silent fail; user can still press Save
                    }
                }, 600);
            };

            const setupRoomTypePropagation = () => {
                const getDayWrapper = (n) => document.querySelector(`.day-content-wrapper[data-day-number="${n}"]`);
                const wrappers = [1,2,3].map(getDayWrapper).filter(Boolean);
                if (wrappers.length === 0) return;

                const getInputs = (wrapper) => ({
                    double: wrapper.querySelector('input[name$="_rooms_double"]'),
                    twin: wrapper.querySelector('input[name$="_rooms_twin"]'),
                    single: wrapper.querySelector('input[name$="_rooms_single"]'),
                    triple: wrapper.querySelector('input[name$="_rooms_triple"]')
                });

                const propagateFrom = (fromDayNum) => {
                    const fromWrapper = getDayWrapper(fromDayNum);
                    if (!fromWrapper) return;
                    const fromInputs = getInputs(fromWrapper);
                    const roomQuantities = {
                        double: parseInt(fromInputs.double?.value || 0),
                        twin: parseInt(fromInputs.twin?.value || 0),
                        single: parseInt(fromInputs.single?.value || 0),
                        triple: parseInt(fromInputs.triple?.value || 0)
                    };
                    // Apply to all subsequent days that have a hotel group
                    const allDayWrappers = document.querySelectorAll('.day-content-wrapper');
                    allDayWrappers.forEach(w => {
                        const n = parseInt(w.dataset.dayNumber, 10);
                        if (n <= fromDayNum) return;
                        const hotelGroup = w.querySelector('.hotel-group');
                        if (!hotelGroup) return; // skip days without hotel UI
                        const dayInputs = getInputs(w);
                        Object.keys(roomQuantities).forEach(rt => {
                            if (dayInputs[rt]) dayInputs[rt].value = roomQuantities[rt];
                        });
                    });
                    if (summaryToggleBtn.dataset.viewMode === 'summary') renderSummaryCards(currentItineraryDays);
                    setTimeout(updateMissingHotelIndicators, 50);
                    scheduleAutoSave();
                };

                // Attach listeners for first three days
                [1,2,3].forEach(n => {
                    const w = getDayWrapper(n);
                    if (!w) return;
                    const inputs = getInputs(w);
                    Object.values(inputs).forEach(inp => {
                        if (inp) {
                            inp.addEventListener('input', () => propagateFrom(n));
                            inp.addEventListener('change', () => propagateFrom(n));
                        }
                    });
                });

                // Initial propagation preference: day1 > day2 > day3
                for (const n of [1,2,3]) {
                    const w = getDayWrapper(n);
                    if (!w) continue;
                    const inputs = getInputs(w);
                    const hasValues = Object.values(inputs).some(i => i && parseInt(i.value||0) > 0);
                    if (hasValues) { propagateFrom(n); break; }
                }
            };
            
            // Function to update red dot indicators for missing hotel assignments
            const updateMissingHotelIndicators = () => {
                const dayButtons = document.querySelectorAll('.tab-button');
                
                dayButtons.forEach(button => {
                    const dayId = button.dataset.dayId;
                    let dayNumber = parseInt(button.dataset.dayNumber || '0', 10);
                    if ((!dayNumber || isNaN(dayNumber)) && dayId) {
                        const wrapper = document.querySelector(`.day-content-wrapper[data-day-id=\"${dayId}\"]`);
                        if (wrapper) dayNumber = parseInt(wrapper.dataset.dayNumber || '0', 10);
                    }
                    
                    // If package does not require hotel for this day, don't show indicator
                    const req = packageRequirements.find(r => r.day_number === dayNumber);
                    const requiresHotel = !!(req && req.hotel_id);
                    
                    // Remove existing indicator first
                    const existingIndicator = button.querySelector('.missing-hotel-indicator');
                    if (existingIndicator) existingIndicator.remove();
                    
                    if (!requiresHotel) return;
                    
                    // Check if hotel is assigned for this required day
                    const hotelSelect = document.querySelector(`[name=\"day_${dayId}_hotel_id\"]`);
                    const hasHotel = hotelSelect && hotelSelect.value && hotelSelect.value !== '';
                    
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
                if (type === 'guide') {
                    let opts = '<option value="">Not assigned</option>';
                    allGuides.forEach(g => { const sel = (String(g.id)===String(currentValue))?'selected':''; const lang = g.language?` (${g.language})`:''; opts += `<option value="${g.id}" ${sel}>${g.name}${lang}</option>`; });
                    missingSelect.innerHTML = opts;
                    return;
                }
                let opts = '<option value="">Not assigned</option>';
                if (type === 'hotel') {
                    allHotels.forEach(h => { const sel = (String(h.id)===String(currentValue))?'selected':''; opts += `<option value="${h.id}" ${sel}>${h.name}</option>`; });
                } else if (type === 'vehicle') {
                    allVehicles.forEach(v => { const plate = v.number_plate ? ` (${v.number_plate})` : ''; const sel=(String(v.id)===String(currentValue))?'selected':''; opts += `<option value="${v.id}" ${sel}>${v.vehicle_name}${plate}</option>`; });
                }
                missingSelect.innerHTML = opts;
            }

            function showCurrentMissing() {
                if (missingWizardState.type === 'guide_required') {
                    if (!missingWizardState.items.length || missingWizardState.index >= missingWizardState.items.length) { closeMissingModal(); return; }
                    const { dayId, currentGuideId, dayLabel } = missingWizardState.items[missingWizardState.index];
                    missingTitle.textContent = 'Assign Guide';
                    missingLabel.textContent = 'Select Guide';
                    missingInfo.innerHTML = `Required guide for ${dayLabel}`;
                    populateSelectFor('guide', currentGuideId || '');
                    openMissingModal();
                    return;
                }
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
            if (missingSave) missingSave.addEventListener('click', async () => {
                const item = missingWizardState.items[missingWizardState.index];
                if (!item) { closeMissingModal(); return; }

                if (missingWizardState.type === 'guide_required') {
                    const guideId = missingSelect.value || '';
                    // Update form select
                    const guideSelect = document.querySelector(`[name="day_${item.dayId}_guide_id"]`);
                    if (guideSelect) { guideSelect.value = guideId; }
                    // Save this day only using current form data snapshot
                    try {
                        const allData = getCurrentFormData(currentItineraryDays);
                        const one = allData.find(d => String(d.id) === String(item.dayId));
                        if (one) {
                            const payload = [{
                                id: one.id,
                                guide_id: guideId || null,
                                vehicle_id: one.vehicle_id || null,
                                hotel_id: one.hotel_id || null,
                                room_type_data: one.room_quantities ? JSON.stringify(one.room_quantities) : JSON.stringify({double:0,twin:0,single:0,triple:0}),
                                guide_informed: one.guide_informed ? 1 : 0,
                                vehicle_informed: one.vehicle_informed ? 1 : 0,
                                hotel_informed: one.hotel_informed ? 1 : 0,
                                notes: one.notes || '',
                                services_provided: one.services_provided || ''
                            }];
                            await fetch(`${API_URL}?action=updateItinerary`, { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ itinerary_days: payload }) });
                        }
                    } catch (e) { /* ignore */ }
                    missingWizardState.index++;
                    showCurrentMissing();
                    return;
                }

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

            const renderInsights = () => {
                const insightsView = document.getElementById('insightsView');
                if (!insightsView) return;
                const arr = (window.__arrivals__||[]);
                // Build arrivals section
                const vehicleLabel = (id)=>{ const v=allVehicles.find(x=>String(x.id)===String(id)); if(!v) return ''; const plate=v.number_plate?` (${v.number_plate})`:''; return `${v.vehicle_name}${plate}`; };
                const guideName = (id)=>{ const g=allGuides.find(x=>String(x.id)===String(id)); return g? g.name : ''; };
                const hotelName = (id)=>{ const h=allHotels.find(x=>String(x.id)===String(id)); return h? h.name : ''; };
                const groupByDate = {};
                arr.forEach(a=>{ const d=a.arrival_date; if(!groupByDate[d]) groupByDate[d]=[]; groupByDate[d].push(a); });
                let arrivalsHtml = '';
                Object.keys(groupByDate).sort().forEach(date=>{
                    const list = groupByDate[date];
                    list.forEach((a,idx)=>{
                        const group = `A${idx+1}`;
                        arrivalsHtml += `
                        <div class="insight-row">
                            <div><span class="insight-badge arrival">${group}</span> ${date}<div class="meta">${a.arrival_time||''}</div></div>
                            <div><strong>Vehicle:</strong> ${vehicleLabel(a.vehicle_id)}<div class="meta">Guide: ${guideName(a.guide_id)||'—'}</div></div>
                            <div><strong>Pax:</strong> ${a.pax_count||'—'}<div class="meta">Drop: ${hotelName(a.drop_hotel_id)||'—'}</div></div>
                        </div>`;
                    });
                });
                if (!arrivalsHtml) arrivalsHtml = '<div class="insight-row">No arrivals recorded.</div>';
                const dep = (window.__departures__||[]);
                let departuresHtml = '';
                dep.forEach((d,idx)=>{
                    const group = `D${idx+1}`;
                    departuresHtml += `
                    <div class="insight-row">
                        <div><span class="insight-badge departure">${group}</span> ${d.departure_date||''}<div class="meta">${d.departure_time||''}</div></div>
                        <div><strong>Flight:</strong> ${d.flight_no||'—'}<div class="meta">Pax: ${d.pax_count||'—'}</div></div>
                        <div><strong>From:</strong> ${hotelName(d.pickup_hotel_id)||'—'}<div class="meta">Vehicle: ${vehicleLabel(d.vehicle_id)||'—'}</div></div>
                    </div>`;
                });
                if (!departuresHtml) departuresHtml = '<div class="insight-row">No departures recorded.</div>';
                
                // Build file records section grouped by package
                let fileRecordsHtml = '';
                const trip = window.__trip__ || {};
                const packageName = trip.package_name || 'Unknown Package';
                const fileName = trip.file_name || trip.tour_code || 'Unknown File';
                
                // Group itinerary days by date for file records
                const daysByDate = {};
                currentItineraryDays.forEach(day => {
                    const date = day.day_date;
                    if (!daysByDate[date]) daysByDate[date] = [];
                    daysByDate[date].push(day);
                });
                
                if (Object.keys(daysByDate).length > 0) {
                    fileRecordsHtml += `
                    <div class="file-records-package">
                        <div class="package-header">
                            <span class="package-badge">${packageName}</span>
                        </div>`;
                    
                    Object.keys(daysByDate).sort().forEach(date => {
                        const days = daysByDate[date];
                        const dayNumbers = days.map(d => `Day ${d.day_number || 'N/A'}`).join(', ');
                        const hotels = days.map(d => hotelName(d.hotel_id)).filter(h => h).join(', ') || '—';
                        
                        fileRecordsHtml += `
                        <div class="file-record-row">
                            <div><span class="date-badge">${date}</span><div class="meta">${dayNumbers}</div></div>
                            <div><strong>File:</strong> ${fileName}<div class="meta">Hotels: ${hotels}</div></div>
                            <div><strong>Days:</strong> ${days.length}<div class="meta">Package: ${packageName}</div></div>
                        </div>`;
                    });
                    
                    fileRecordsHtml += `</div>`;
                } else {
                    fileRecordsHtml = '<div class="insight-row">No file records available.</div>';
                }
                
                insightsView.innerHTML = `
                    <div class="insights-sections">
                      <div class="insight-card">
                        <div class="head arrival"><i class="fas fa-plane-arrival"></i> Arrivals</div>
                        <div class="insight-list">${arrivalsHtml}</div>
                      </div>
                      <div class="insight-card">
                        <div class="head departure"><i class="fas fa-plane-departure"></i> Departures</div>
                        <div class="insight-list">${departuresHtml}</div>
                      </div>
                      <div class="insight-card file-records">
                        <div class="head file-records-header"><i class="fas fa-folder-open"></i> File Records</div>
                        <div class="insight-list file-records-list">${fileRecordsHtml}</div>
                      </div>
                    </div>`;
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
                    const rawNotes = (day.notes || '').trim();
                    const notesDisplay = rawNotes || '<span style="color: var(--text-light);">No activities</span>';
                    
                    itemsHtml += `
                        <div class="summary-item">
                            <span class="summary-label">
                                <i class="fas fa-utensils"></i> Services
                            </span>
                            <span class="summary-value">${servicesDisplay}</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">
                                <i class="fas fa-list-ul"></i> Activities
                            </span>
                            <span class="summary-notes" title="${rawNotes.replace(/"/g,'&quot;')}">${notesDisplay}</span>
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
                const isInsightsMode = mode === 'insights';
                
                if (isSummaryMode) {
                    itineraryGrid.style.display = 'none';
                    summaryView.style.display = 'block';
                    insightsView.style.display = 'none';
                    summaryToggleBtn.classList.add('active');
                    summaryToggleBtn.innerHTML = '<i class="fas fa-chart-line"></i> <span>View Insights</span>';
                    summaryToggleBtn.dataset.viewMode = 'summary';
                    dayTabsContainer.parentNode.style.display = 'none';
                    renderSummaryCards(currentItineraryDays);
                } else if (isInsightsMode) {
                    itineraryGrid.style.display = 'none';
                    summaryView.style.display = 'none';
                    insightsView.style.display = 'block';
                    summaryToggleBtn.classList.add('active');
                    summaryToggleBtn.innerHTML = '<i class="fas fa-list-ul"></i> <span>View Details</span>';
                    summaryToggleBtn.dataset.viewMode = 'insights';
                    dayTabsContainer.parentNode.style.display = 'none';
                    renderInsights();
                } else {
                    itineraryGrid.style.display = 'block';
                    summaryView.style.display = 'none';
                    insightsView.style.display = 'none';
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

                // Build compact services token like BLD (ordered, unique)
                const headers = ['Day', 'Date', 'Guide', 'Vehicle', 'Hotel', 'Services', 'Activities / Notes'];
                const csvRows = data.map((d, index) => {
                    const sv = (d.services_provided || '').toUpperCase();
                    const tokens = Array.from(new Set(sv.split(/[\s,;]+/).filter(x=>['B','L','D'].includes(x))));
                    const order = {'B':0,'L':1,'D':2};
                    tokens.sort((a,b)=>(order[a]??9)-(order[b]??9));
                    const compactServices = tokens.join('') || '';
                    const notesOneLine = (d.notes || '').replace(/\n/g,' ').trim();
                    return [
                        `Day ${index + 1}`,
                        d.day_date,
                        d.guide_name,
                        d.vehicle_name,
                        d.hotel_name,
                        compactServices,
                        notesOneLine
                    ];
                });

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
                
                // Check for missing hotel assignments (only for days where package requires a hotel)
                const requiresHotel = (dayNumber) => {
                    const req = packageRequirements.find(r => r.day_number === dayNumber);
                    return !!(req && req.hotel_id);
                };
                const missingHotels = currentData.filter((day, idx) => requiresHotel(idx + 1) && (!day.hotel_id || day.hotel_id === '' || day.hotel_id === '0' || day.hotel_id === 0));
                const missingRooms = currentData.filter((day, idx) => requiresHotel(idx + 1) && day.hotel_id && day.hotel_id !== '' && day.hotel_id !== '0' && day.hotel_id !== 0 && !day.has_rooms);
                
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
                if (emailDropBtn) { emailDropBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Email'; emailDropBtn.disabled = true; }

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

                    if (emailDropBtn) emailDropBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Email';

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
                        // Mark Email Hotels as completed (green)
                        const hotelsItem = document.getElementById('emailHotelsItem');
                        if (hotelsItem) hotelsItem.classList.add('menu-item-success');
                        const emailBtn = document.getElementById('emailDropBtn');
                        if (emailBtn) emailBtn.classList.add('btn-success');
                        // After emailing hotels, hotels are considered assigned/informed; update Assign menu status
                        try{ updateAssignMenuStatus(); }catch(e){}
                    } else {
                        showToast(result.message, result.status || 'error');
                    }

                } catch (error) {
                    clearEmailStatus();
                    addEmailStatusItem('error', 'Request failed: ' + error.message);
                    showToast('Request Failed: ' + error.message, 'error');
                } finally {
                    if (emailDropBtn) { emailDropBtn.innerHTML = '<i class="fas fa-envelope"></i> <span>Email</span>'; emailDropBtn.disabled = false; }
                }
            };
            
                // Function to send email to ALL uninformed vehicles for the trip
            const sendVehicleEmail = async () => {
                const currentData = getCurrentFormData(currentItineraryDays);
                const assignedVehicles = currentData.filter(day => day.vehicle_id && day.vehicle_id !== '' && day.vehicle_id !== '0' && day.vehicle_id !== 0);
                if (assignedVehicles.length === 0) { showToast('No vehicle assignments found for this trip.', 'info'); return; }
                const anyUninformed = assignedVehicles.some(day => !day.vehicle_informed);
                if (!anyUninformed) { showToast('All assigned vehicles have already been informed.', 'info'); return; }

                if (emailDropBtn) { emailDropBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Email'; emailDropBtn.disabled = true; }
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
                    if (emailDropBtn) { emailDropBtn.innerHTML = '<i class="fas fa-envelope"></i> <span>Email</span>'; emailDropBtn.disabled = false; }
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
                if (emailDropBtn) { emailDropBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Email'; emailDropBtn.disabled = true; }

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

                    if (emailDropBtn) emailDropBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Email';

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
                    if (emailDropBtn) { emailDropBtn.innerHTML = '<i class="fas fa-envelope"></i> <span>Email</span>'; emailDropBtn.disabled = false; }
                }
            };
            
            itineraryForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
                saveBtn.disabled = true;
                
                const currentData = getCurrentFormData(currentItineraryDays);
                const activeDayBeforeSave = document.querySelector('.tab-button.active')?.dataset.dayNumber;

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
            
            // Start wizard for guide-required days
            function startGuideRequiredWizard(){
                if (!Array.isArray(packageRequirements) || packageRequirements.length===0){ showToast('No package requirements found','info'); return; }
                // Build list of days where guide_required == 1
                const items = [];
                currentItineraryDays.forEach((day, idx) => {
                    const dn = idx+1;
                    const req = packageRequirements.find(r => r.day_number === dn);
                    if (req && (req.guide_required === 1 || req.guide_required === '1')){
                        const dayDate = new Date(day.day_date + 'T00:00:00');
                        const dayLabel = `Day ${dn} (${dayDate.toLocaleDateString('en-US', { month:'short', day:'numeric' })})`;
                        items.push({ dayId: day.id, currentGuideId: day.guide_id || '', dayLabel });
                    }
                });
                if (!items.length){ showToast('No days require a guide in this package.','info'); return; }
                missingWizardState = { type: 'guide_required', items, index: 0 };
                showCurrentMissing();
            }

            document.getElementById('assignGuidesWizardBtn').addEventListener('click', startGuideRequiredWizard);

            // Assign only hotels
            function startAssignHotelsOnly(){
                missingWizardState = { type: 'hotel', items: collectMissing('hotel'), index: 0 };
                if (missingWizardState.items.length) showCurrentMissing(); else showToast('No hotel assignments pending.','info');
            }
            // Assign only vehicles
            function startAssignVehiclesOnly(){
                missingWizardState = { type: 'vehicle', items: collectMissing('vehicle'), index: 0 };
                if (missingWizardState.items.length) showCurrentMissing(); else showToast('No vehicle assignments pending.','info');
            }

            // Dropup handlers
            function setupDropup(btnId, menuId){
                const btn = document.getElementById(btnId); const menu = document.getElementById(menuId); const wrapper = menu.parentElement;
                btn.addEventListener('click', (e)=>{ e.stopPropagation(); wrapper.classList.toggle('open'); });
                document.addEventListener('click', ()=>{ wrapper.classList.remove('open'); });
            }
            setupDropup('emailDropBtn','emailMenu');
            setupDropup('assignDropBtn','assignMenu');

            // Email menu items
            document.getElementById('emailGuidesItem').addEventListener('click', (e)=>{ e.preventDefault(); sendGuideEmail(); });
            document.getElementById('emailHotelsItem').addEventListener('click', (e)=>{ e.preventDefault(); sendHotelEmail(); });
            document.getElementById('emailVehiclesItem').addEventListener('click', (e)=>{ e.preventDefault(); sendVehicleEmail(); });
            // Assign menu items
            document.getElementById('assignGuidesItem').addEventListener('click', (e)=>{ e.preventDefault(); startGuideRequiredWizard(); });
            document.getElementById('assignHotelsItem').addEventListener('click', (e)=>{ e.preventDefault(); startAssignHotelsOnly(); });
            document.getElementById('assignVehiclesItem').addEventListener('click', (e)=>{ e.preventDefault(); startAssignVehiclesOnly(); });

            // Update Assign->Hotels status (green when all assigned)
            function updateAssignMenuStatus(){
                try{
                    const data = getCurrentFormData(currentItineraryDays);
                    const requiresHotel = (dayNumber) => {
                        const req = packageRequirements.find(r => r.day_number === dayNumber);
                        return !!(req && req.hotel_id);
                    };
                    const anyMissing = data.some((d, idx) => requiresHotel(idx+1) && (!d.hotel_id || d.hotel_id==='0' || d.hotel_id===0));
                    const hotelsItem = document.getElementById('assignHotelsItem');
                    const assignBtn = document.getElementById('assignDropBtn');
                    if (!anyMissing) { hotelsItem?.classList.add('menu-item-success'); assignBtn?.classList.add('btn-success'); }
                    else { hotelsItem?.classList.remove('menu-item-success'); assignBtn?.classList.remove('btn-success'); }
                }catch(e){/* ignore */}
            }

            fetchItinerary();
            // Re-evaluate after a short delay (post-render)
            setTimeout(updateAssignMenuStatus, 500);
            // Also update when toggling to summary (after render)
            summaryToggleBtn.addEventListener('click', ()=> setTimeout(updateAssignMenuStatus, 400));
        });
    </script>

</body>
</html>