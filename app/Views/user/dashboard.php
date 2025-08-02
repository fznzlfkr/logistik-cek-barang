 <?= $this->extend('layout/templateUser'); ?>
<?= $this->section('content'); ?>
 <div class="main-content">
        <!-- Header -->
        <div class="header">
            <h1><i class="fas fa-chart-dashboard"></i> Dashboard Admin</h1>
        </div>

        <!-- Stats Cards -->
        <div class="stats-container">
            <div class="stat-card blue">
                <div class="stat-icon">
                    <i class="fas fa-boxes"></i>
                </div>
                <div class="stat-info">
                    <h3 id="totalBarang">125</h3>
                    <p>Total Barang</p>
                </div>
            </div>
            
            <div class="stat-card green">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3 id="barangTersedia">98</h3>
                    <p>Barang Tersedia</p>
                </div>
            </div>
            
            <div class="stat-card orange">
                <div class="stat-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-info">
                    <h3>15</h3>
                    <p>Stok Menipis</p>
                </div>
            </div>
            
            <div class="stat-card red">
                <div class="stat-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>12</h3>
                    <p>Stok Habis</p>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="table-container">
            <div class="table-header">
                <h2><i class="fas fa-table"></i> Data Barang</h2>
                <div class="table-actions">
                    <button class="btn btn-primary" onclick="addNewItem()">
                        <i class="fas fa-plus"></i> Tambah Barang
                    </button>
                    <button class="btn btn-success" onclick="generateAllBarcodes()">
                        <i class="fas fa-barcode"></i> Generate All Barcodes
                    </button>
                </div>
            </div>

            <div class="search-container">
                <input type="text" class="search-input" placeholder="🔍 Cari barang..." onkeyup="searchTable()">
                <button class="btn btn-primary">
                    <i class="fas fa-search"></i> Cari
                </button>
            </div>

            <table class="data-table" id="dataTable">
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag"></i> ID</th>
                        <th><i class="fas fa-box"></i> Nama Barang</th>
                        <th><i class="fas fa-barcode"></i> Kode Barang</th>
                        <th><i class="fas fa-layer-group"></i> Kategori</th>
                        <th><i class="fas fa-cubes"></i> Stok</th>
                        <th><i class="fas fa-dollar-sign"></i> Harga</th>
                        <th><i class="fas fa-cogs"></i> Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>001</td>
                        <td>Laptop Asus ROG</td>
                        <td>LPT-ASS-001</td>
                        <td>Elektronik</td>
                        <td>15</td>
                        <td>Rp 15.000.000</td>
                        <td>
                            <div class="action-buttons">
                                <button class="action-btn btn-primary" onclick="editItem('001')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="action-btn btn-success" onclick="generateBarcode('LPT-ASS-001')">
                                    <i class="fas fa-barcode"></i>
                                </button>
                                <button class="action-btn btn-danger" onclick="deleteItem('001')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>002</td>
                        <td>Mouse Logitech MX Master 3</td>
                        <td>MSE-LGT-002</td>
                        <td>Aksesoris</td>
                        <td>25</td>
                        <td>Rp 1.500.000</td>
                        <td>
                            <div class="action-buttons">
                                <button class="action-btn btn-primary" onclick="editItem('002')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="action-btn btn-success" onclick="generateBarcode('MSE-LGT-002')">
                                    <i class="fas fa-barcode"></i>
                                </button>
                                <button class="action-btn btn-danger" onclick="deleteItem('002')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>003</td>
                        <td>Keyboard Mechanical RGB</td>
                        <td>KBD-RGB-003</td>
                        <td>Aksesoris</td>
                        <td>8</td>
                        <td>Rp 2.500.000</td>
                        <td>
                            <div class="action-buttons">
                                <button class="action-btn btn-primary" onclick="editItem('003')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="action-btn btn-success" onclick="generateBarcode('KBD-RGB-003')">
                                    <i class="fas fa-barcode"></i>
                                </button>
                                <button class="action-btn btn-danger" onclick="deleteItem('003')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>004</td>
                        <td>Monitor Dell UltraSharp 27"</td>
                        <td>MON-DLL-004</td>
                        <td>Elektronik</td>
                        <td>12</td>
                        <td>Rp 8.500.000</td>
                        <td>
                            <div class="action-buttons">
                                <button class="action-btn btn-primary" onclick="editItem('004')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="action-btn btn-success" onclick="generateBarcode('MON-DLL-004')">
                                    <i class="fas fa-barcode"></i>
                                </button>
                                <button class="action-btn btn-danger" onclick="deleteItem('004')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>005</td>
                        <td>Headset SteelSeries Arctis 7</td>
                        <td>HDS-STS-005</td>
                        <td>Audio</td>
                        <td>20</td>
                        <td>Rp 3.200.000</td>
                        <td>
                            <div class="action-buttons">
                                <button class="action-btn btn-primary" onclick="editItem('005')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="action-btn btn-success" onclick="generateBarcode('HDS-STS-005')">
                                    <i class="fas fa-barcode"></i>
                                </button>
                                <button class="action-btn btn-danger" onclick="deleteItem('005')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Barcode Modal -->
    <div id="barcodeModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeBarcodeModal()">&times;</span>
            <h2 style="margin-bottom: 20px; color: #2d3748;"><i class="fas fa-barcode"></i> Barcode Generator</h2>
            <div id="barcodeDisplay" style="text-align: center; padding: 30px;">
                <!-- Barcode will be generated here -->
            </div>
            <div style="text-align: center; margin-top: 20px;">
                <button class="btn btn-primary" onclick="downloadBarcode()">
                    <i class="fas fa-download"></i> Download Barcode
                </button>
            </div>
        </div>
    </div>
<?= $this->endSection(); ?>