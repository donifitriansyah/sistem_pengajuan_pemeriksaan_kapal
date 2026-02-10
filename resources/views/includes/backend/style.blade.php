    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

    <!-- DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #e0f2f1 0%, #b2dfdb 50%, #80cbc4 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1300px;
            margin: 0 auto;
        }

        /* Header */
        .header {
            background: white;
            padding: 20px 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo-small {
            height: 50px;
            width: auto;
        }

        .header-title h1 {
            font-size: 20px;
            color: #2c3e50;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .header-title .subtitle {
            font-size: 13px;
            color: #7f8c8d;
        }

        .header-right {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .user-info {
            font-weight: 600;
            color: #34495e;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .user-icon {
            width: 28px;
            height: 28px;
            background: linear-gradient(135deg, #4db6ac 0%, #26a69a 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
        }

        /* Buttons */
        .btn {
            padding: 10px 18px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-success {
            background: linear-gradient(135deg, #4db6ac 0%, #26a69a 100%);
            color: white;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(77, 182, 172, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(231, 76, 60, 0.4);
        }

        .btn-secondary {
            background: #95a5a6;
            color: white;
        }

        .btn-secondary:hover {
            background: #7f8c8d;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
        }

        .btn-outline {
            background: white;
            color: #26a69a;
            border: 2px solid #26a69a;
            height: 40px;
            padding: 0 18px;
        }

        .btn-outline:hover {
            background: #26a69a;
            color: white;
        }

        /* Tabs */
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            background: white;
            padding: 12px 18px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .tab {
            padding: 10px 20px;
            background: #ecf0f1;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            color: #7f8c8d;
            transition: all 0.3s ease;
        }

        .tab.active {
            background: linear-gradient(135deg, #4db6ac 0%, #26a69a 100%);
            color: white;
        }

        /* Scorecard */
        .scorecard-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .scorecard {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #26a69a;
            transition: transform 0.2s ease;
        }

        .scorecard:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .scorecard-label {
            font-size: 13px;
            color: #7f8c8d;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .scorecard-value {
            font-size: 32px;
            color: #2c3e50;
            font-weight: 700;
        }

        .scorecard.total {
            border-left-color: #3498db;
        }

        .scorecard.belum-tagihan {
            border-left-color: #95a5a6;
        }

        .scorecard.belum-bayar {
            border-left-color: #e74c3c;
        }

        .scorecard.menunggu {
            border-left-color: #f39c12;
        }

        .scorecard.lunas {
            border-left-color: #27ae60;
        }

        /* Content Card */
        .content-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            gap: 15px;
        }

        .content-header h2 {
            color: #2c3e50;
            font-size: 20px;
            font-weight: 600;
        }

        .header-actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .search-box {
            position: relative;
        }

        .search-box input {
            padding: 10px 15px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            width: 250px;
            font-family: inherit;
            height: 40px;
        }

        .search-box input:focus {
            outline: none;
            border-color: #26a69a;
            box-shadow: 0 0 0 3px rgba(38, 166, 154, 0.1);
        }

        /* Sections */
        .content-section {
            display: none;
        }

        .content-section.active {
            display: block;
        }

        /* Filter Container */
        .filter-container {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            border: 1px solid #e9ecef;
            display: grid;
            grid-template-columns: repeat(4, 1fr) auto auto;
            gap: 15px;
            align-items: center;
        }

        .filter-container-petugas {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            border: 1px solid #e9ecef;
            display: grid;
            grid-template-columns: repeat(2, 1fr) repeat(3, 1fr) auto auto;
            gap: 15px;
            align-items: end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-group label {
            font-size: 13px;
            font-weight: 600;
            color: #34495e;
            margin-bottom: 6px;
        }

        .filter-group select,
        .filter-group input {
            padding: 9px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            background: white;
            height: 40px;
        }

        .filter-group select:focus,
        .filter-group input:focus {
            outline: none;
            border-color: #26a69a;
            box-shadow: 0 0 0 3px rgba(38, 166, 154, 0.1);
        }

        .filter-actions {
            display: flex;
            gap: 10px;
        }

        .filter-container .btn,
        .filter-container-petugas .btn {
            height: 40px;
            padding: 0 18px;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ecf0f1;
            font-size: 14px;
        }

        th {
            background: #f8f9fa;
            color: #2c3e50;
            font-weight: 600;
            text-align: center;
        }

        td {
            color: #34495e;
            text-align: center;
        }

        tbody tr:hover {
            background: #f8f9fa;
        }

        /* Badge */
        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-belum-tagihan {
            background: #fff3cd;
            color: #856404;
        }

        .badge-belum-bayar {
            background: #fee;
            color: #c0392b;
        }

        .badge-menunggu {
            background: #d1ecf1;
            color: #0c5460;
        }

        .badge-lunas {
            background: #d4edda;
            color: #155724;
        }

        .badge-ditolak {
            background: #f8d7da;
            color: #721c24;
        }

        .status-lunas {
            color: #27ae60;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .status-lunas .check-icon {
            font-size: 16px;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #7f8c8d;
        }

        .empty-state p {
            font-size: 16px;
            margin-top: 15px;
        }

        /* Loading Spinner */
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .loading-overlay.show {
            display: flex;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #e0e0e0;
            border-top: 4px solid #26a69a;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }





        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #34495e;
            margin-bottom: 8px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #26a69a;
            box-shadow: 0 0 0 3px rgba(38, 166, 154, 0.1);
        }

        .time-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        #petugasContainer {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }


        .close-btn {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 28px;
            color: #95a5a6;
            cursor: pointer;
            line-height: 1;
        }

        .close-btn:hover {
            color: #e74c3c;
        }

        @media (max-width: 768px) {
            body {
                padding: 15px;
            }

            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .header-left {
                flex-direction: column;
            }

            .content-header {
                flex-direction: column;
                gap: 15px;
            }

            .filter-container,
            .filter-container-petugas {
                grid-template-columns: 1fr 1fr;
            }

            table {
                font-size: 12px;
            }

            th,
            td {
                padding: 8px;
            }

            .search-box input {
                width: 100%;
            }

            .time-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
