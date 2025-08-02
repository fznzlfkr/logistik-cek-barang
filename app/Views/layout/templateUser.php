<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Kelola Barang</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        /* Navbar Styles */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding: 0 30px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .navbar-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
            height: 70px;
        }

        .navbar-brand {
            font-size: 24px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #2d3748;
        }

        .brand-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
        }

        .navbar-nav {
            display: flex;
            list-style: none;
            gap: 0;
            margin: 0;
            padding: 0;
        }

        .navbar-nav li {
            position: relative;
        }

        .navbar-nav a {
            display: flex;
            align-items: center;
            color: #4a5568;
            text-decoration: none;
            padding: 20px 20px;
            transition: all 0.3s ease;
            white-space: nowrap;
            font-weight: 500;
            border-radius: 8px;
            margin: 0 4px;
        }

        .navbar-nav a:hover,
        .navbar-nav a.active {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4);
        }

        .navbar-nav a i {
            margin-right: 8px;
            width: 20px;
            text-align: center;
        }

        .navbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 16px;
            background: rgba(102, 126, 234, 0.1);
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .user-info:hover {
            background: rgba(102, 126, 234, 0.2);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 16px;
        }

        .logout-btn {
            color: #e53e3e !important;
            padding: 10px 16px !important;
            border-radius: 8px !important;
            transition: all 0.3s ease !important;
            text-decoration: none !important;
            font-weight: 500 !important;
        }

        .logout-btn:hover {
            background: #e53e3e !important;
            color: white !important;
            transform: translateY(-2px) !important;
        }

        /* Mobile Menu */
        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            color: #4a5568;
            font-size: 24px;
            cursor: pointer;
            padding: 10px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .mobile-menu-toggle:hover {
            background: rgba(102, 126, 234, 0.1);
        }

        .navbar-nav.mobile {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            flex-direction: column;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border-radius: 0 0 16px 16px;
        }

        .navbar-nav.mobile.open {
            display: flex;
        }

        .navbar-nav.mobile a {
            padding: 16px 30px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            margin: 0;
            border-radius: 0;
        }

        /* Main Content */
        .main-content {
            padding: 100px 30px 30px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .header h1 {
            color: #2d3748;
            font-size: 32px;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Stats Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, var(--card-color), var(--card-color-dark));
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .stat-icon {
            width: 70px;
            height: 70px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: white;
            background: linear-gradient(135deg, var(--card-color), var(--card-color-dark));
        }

        .stat-card.blue {
            --card-color: #4299e1;
            --card-color-dark: #3182ce;
        }

        .stat-card.green {
            --card-color: #48bb78;
            --card-color-dark: #38a169;
        }

        .stat-card.orange {
            --card-color: #ed8936;
            --card-color-dark: #dd6b20;
        }

        .stat-card.red {
            --card-color: #f56565;
            --card-color-dark: #e53e3e;
        }

        .stat-info h3 {
            font-size: 36px;
            color: #2d3748;
            margin-bottom: 8px;
            font-weight: 700;
        }

        .stat-info p {
            color: #718096;
            font-size: 16px;
            font-weight: 500;
        }

        /* Data Table */
        .table-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .table-header {
            padding: 30px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05), rgba(118, 75, 162, 0.05));
        }

        .table-header h2 {
            color: #2d3748;
            font-size: 24px;
            font-weight: 700;
        }

        .table-actions {
            display: flex;
            gap: 12px;
        }

        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .btn-primary {
            background: linear-gradient(135deg, #4299e1, #3182ce);
            color: white;
        }

        .btn-success {
            background: linear-gradient(135deg, #48bb78, #38a169);
            color: white;
        }

        .btn-warning {
            background: linear-gradient(135deg, #ed8936, #dd6b20);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, #f56565, #e53e3e);
            color: white;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th,
        .data-table td {
            padding: 16px 20px;
            text-align: left;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .data-table th {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05), rgba(118, 75, 162, 0.05));
            font-weight: 700;
            color: #2d3748;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .data-table tr {
            transition: all 0.3s ease;
        }

        .data-table tbody tr:hover {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.02), rgba(118, 75, 162, 0.02));
            transform: scale(1.01);
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .action-btn {
            padding: 8px 12px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 12px;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .action-btn:hover {
            transform: translateY(-1px);
        }

        /* Search and Filter */
        .search-container {
            padding: 30px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            gap: 20px;
            align-items: center;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.02), rgba(118, 75, 162, 0.02));
        }

        .search-input {
            flex: 1;
            padding: 14px 20px;
            border: 2px solid transparent;
            border-radius: 12px;
            outline: none;
            background: white;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .search-input:focus {
            border-color: #667eea;
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.2);
        }

        .search-input::placeholder {
            color: #a0aec0;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background: white;
            margin: 5% auto;
            padding: 40px;
            border-radius: 20px;
            width: 80%;
            max-width: 500px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            position: relative;
            animation: modalSlideIn 0.3s ease;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .close {
            position: absolute;
            right: 20px;
            top: 20px;
            font-size: 24px;
            cursor: pointer;
            color: #718096;
            transition: color 0.3s ease;
        }

        .close:hover {
            color: #e53e3e;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .navbar-nav:not(.mobile) {
                display: none;
            }

            .mobile-menu-toggle {
                display: block;
            }

            .main-content {
                padding: 100px 15px 20px;
            }

            .navbar {
                padding: 0 15px;
            }

            .stats-container {
                grid-template-columns: 1fr;
            }

            .table-container {
                overflow-x: auto;
            }

            .data-table {
                min-width: 800px;
            }

            .table-actions {
                flex-direction: column;
                gap: 10px;
            }

            .search-container {
                flex-direction: column;
                align-items: stretch;
            }

            .user-info span {
                display: none;
            }

            .header {
                padding: 20px;
            }

            .header h1 {
                font-size: 24px;
            }
        }

        @media (max-width: 480px) {
            .navbar-brand {
                font-size: 18px;
            }
            
            .table-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 20px;
            }

            .stat-card {
                padding: 20px;
            }

            .stat-info h3 {
                font-size: 28px;
            }
        }

        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-content">
            <div class="navbar-brand">
                <div class="brand-icon">
                    <i class="fas fa-boxes"></i>
                </div>
                CargoWing
            </div>

            <!-- Desktop Navigation -->
            <ul class="navbar-nav">
                <li><a href="#" class="active"><i class="fas fa-chart-dashboard"></i> Dashboard</a></li>
                <li><a href="#" onclick="showKelolaBarang()"><i class="fas fa-boxes"></i> Kelola Barang</a></li>
                <li><a href="#" onclick="showBarcodeGenerator()"><i class="fas fa-barcode"></i> Generate Barcode</a></li>
                <li><a href="#"><i class="fas fa-chart-line"></i> Laporan</a></li>
                <li><a href="#"><i class="fas fa-users"></i> User Management</a></li>
                <li><a href="#"><i class="fas fa-cog"></i> Pengaturan</a></li>
            </ul>

            <!-- Mobile Navigation -->
            <ul class="navbar-nav mobile" id="mobileNav">
                <li><a href="#" class="active"><i class="fas fa-chart-dashboard"></i> Dashboard</a></li>
                <li><a href="#" onclick="showKelolaBarang()"><i class="fas fa-boxes"></i> Kelola Barang</a></li>
                <li><a href="#" onclick="showBarcodeGenerator()"><i class="fas fa-barcode"></i> Generate Barcode</a></li>
                <li><a href="#"><i class="fas fa-chart-line"></i> Laporan</a></li>
                <li><a href="#"><i class="fas fa-users"></i> User Management</a></li>
                <li><a href="#"><i class="fas fa-cog"></i> Pengaturan</a></li>
                <li><a class="logout-btn" onclick="return confirm('Apakah Anda yakin ingin keluar?')" href="#"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>

            <div class="navbar-right">
                <div class="user-info">
                    <div class="user-avatar">A</div>
                    <span>Admin User</span>
                </div>
                <a class="logout-btn" onclick="return confirm('Apakah Anda yakin ingin keluar?')" href="<?= base_url('/logout') ?>">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
                <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
  <?= $this->renderSection('content') ?>

    <script>
        function toggleMobileMenu() {
            const mobileNav = document.getElementById('mobileNav');
            mobileNav.classList.toggle('open');
        }

        function editItem(itemId) {
            alert('Edit item with ID: ' + itemId);
            // Implement edit functionality here   
        }
        function deleteItem(itemId) {
            if (confirm('Apakah Anda yakin ingin menghapus item dengan ID: ' + itemId + '?')) {
                alert('Item dengan ID ' + itemId + ' telah dihapus.');
                // Implement delete functionality here
            }
        }
    </script>
</body>
</html>