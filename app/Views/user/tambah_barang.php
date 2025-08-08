<?= $this->extend('layout/templateUser'); ?>

<?= $this->section('content'); ?>
<style>
    .main-container {
        padding: 20px;
        background: #f8f9fa;
        min-height: 100vh;
    }
    
    .btn-trigger {
        background: #6c757d;
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .btn-trigger:hover {
        background: #5a6268;
        transform: translateY(-1px);
    }
    
    /* Modal Styles */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }
    
    .modal-overlay.active {
        opacity: 1;
        visibility: visible;
    }
    
    .modal-content {
        background: white;
        border-radius: 8px;
        width: 90%;
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
        transform: scale(0.7);
        transition: transform 0.3s ease;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    }
    
    .modal-overlay.active .modal-content {
        transform: scale(1);
    }
    
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 25px 15px;
        border-bottom: 1px solid #e9ecef;
    }
    
    .modal-title {
        font-size: 18px;
        font-weight: 600;
        color: #333;
        margin: 0;
    }
    
    .btn-close {
        background: none;
        border: none;
        font-size: 24px;
        color: #6c757d;
        cursor: pointer;
        padding: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        transition: all 0.2s ease;
    }
    
    .btn-close:hover {
        background: #f8f9fa;
        color: #333;
    }
    
    .modal-body {
        padding: 25px;
    }
    
    .form-row {
        display: flex;
        gap: 15px;
        margin-bottom: 20px;
    }
    
    .form-group {
        flex: 1;
        margin-bottom: 20px;
    }
    
    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 500;
        color: #555;
        margin-bottom: 6px;
    }
    
    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
        color: #333;
        background: #fff;
        transition: border-color 0.2s ease;
        box-sizing: border-box;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.1);
    }
    
    .form-control::placeholder {
        color: #aaa;
        font-style: italic;
    }
    
    .form-select {
        appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 16px;
        padding-right: 40px;
    }
    
    .barcode-section {
        background: #f8f9fa;
        border-radius: 6px;
        padding: 20px;
        text-align: center;
    }
    
    .barcode-title {
        font-size: 14px;
        font-weight: 500;
        color: #666;
        margin-bottom: 15px;
    }
    
    .barcode-placeholder {
        width: 120px;
        height: 80px;
        background: #e9ecef;
        border-radius: 4px;
        margin: 0 auto 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6c757d;
        font-size: 12px;
        position: relative;
        overflow: hidden;
    }
    
    .barcode-placeholder::before {
        content: '';
        position: absolute;
        top: 20px;
        left: 20px;
        right: 20px;
        bottom: 20px;
        border: 2px dashed #adb5bd;
        border-radius: 2px;
    }
    
    .barcode-icon {
        font-size: 24px;
        color: #adb5bd;
        margin-bottom: 5px;
    }
    
    .btn-generate {
        background: #6c757d;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .btn-generate:hover {
        background: #5a6268;
    }
    
    .modal-footer {
        display: flex;
        justify-content: flex-start;
        gap: 10px;
        padding: 20px 25px;
        border-top: 1px solid #e9ecef;
    }
    
    .btn-primary {
        background: #6c757d;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .btn-primary:hover {
        background: #5a6268;
    }
    
    .btn-secondary {
        background: #f8f9fa;
        color: #6c757d;
        border: 1px solid #dee2e6;
        padding: 10px 20px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .btn-secondary:hover {
        background: #e9ecef;
        border-color: #adb5bd;
    }
    
    @media (max-width: 768px) {
        .modal-content {
            width: 95%;
            margin: 10px;
        }
        
        .form-row {
            flex-direction: column;
            gap: 0;
        }
        
        .modal-body {
            padding: 20px;
        }
        
        .modal-footer {
            flex-direction: column;
            gap: 8px;
        }
        
        .btn-primary,
        .btn-secondary {
            width: 100%;
        }
    }
</style>

<div class="main-container">
    <h2 style="margin-bottom: 30px; color: #333;">Kelola Barang</h2>
    
    <!-- Trigger Button -->

    
    <!-- Modal -->
    <div class="modal-overlay" id="modalTambahBarang">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Tambah Barang</h3>
                <button class="btn-close" onclick="closeModal()">&times;</button>
            </div>
            
            <form action="<?= base_url('user/simpan_barang'); ?>" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <!-- Barang Masuk Section -->
                    <div style="margin-bottom: 25px;">
                        <h4 style="font-size: 16px; font-weight: 600; color: #333; margin-bottom: 15px;">Barang Masuk</h4>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Nama barang</label>
                                <input type="text" class="form-control" name="nama_barang" placeholder="Masukkan nama barang" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Jenis</label>
                                <select class="form-control form-select" name="jenis" required>
                                    <option value="">Pilih</option>
                                    <option value="Elektronik">Elektronik</option>
                                    <option value="Furniture">Furniture</option>
                                    <option value="Alat Tulis">Alat Tulis</option>
                                    <option value="Peralatan">Peralatan</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Jumlah</label>
                                <input type="number" class="form-control" name="jumlah" placeholder="Masukkan jumlah barang" min="1" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Satuan</label>
                                <input type="text" class="form-control" name="satuan" placeholder="Masukkan satuan barang" required>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Barcode Section -->
                    <div class="barcode-section">
                        <div class="barcode-title">Barcode</div>
                        <div class="barcode-placeholder" id="barcodePreview">
                            <div>
                                <div class="barcode-icon">📊</div>
                                <div style="font-size: 11px;">Barcode Preview</div>
                            </div>
                        </div>
                        <button type="button" class="btn-generate" onclick="generateBarcode()">Generate</button>
                        <input type="hidden" name="barcode" id="barcodeValue">
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="submit" class="btn-primary">Tambah</button>
                    <button type="button" class="btn-secondary" onclick="closeModal()">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openModal() {
    const modal = document.getElementById('modalTambahBarang');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    const modal = document.getElementById('modalTambahBarang');
    modal.classList.remove('active');
    document.body.style.overflow = 'auto';
    
    // Reset form
    document.querySelector('form').reset();
    resetBarcode();
}

// Close modal when clicking overlay
document.getElementById('modalTambahBarang').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Generate barcode function
function generateBarcode() {
    const namaBarang = document.querySelector('input[name="nama_barang"]').value;
    const jenis = document.querySelector('select[name="jenis"]').value;
    
    if (!namaBarang || !jenis) {
        alert('Mohon isi nama barang dan jenis terlebih dahulu');
        return;
    }
    
    // Generate barcode (simple format: first 3 letters + random numbers)
    const prefix = namaBarang.substring(0, 3).toUpperCase();
    const randomNum = Math.floor(Math.random() * 1000000).toString().padStart(6, '0');
    const barcode = `BC${prefix}${randomNum}`;
    
    // Update barcode preview
    const preview = document.getElementById('barcodePreview');
    preview.innerHTML = `
        <div style="font-family: monospace; font-size: 12px; font-weight: bold; color: #333;">
            ${barcode}
        </div>
        <div style="margin-top: 5px;">
            <div style="display: flex; justify-content: space-between; height: 20px;">
                ${generateBarcodeLines()}
            </div>
        </div>
    `;
    
    // Set hidden input value
    document.getElementById('barcodeValue').value = barcode;
}

function generateBarcodeLines() {
    let lines = '';
    for (let i = 0; i < 30; i++) {
        const height = Math.random() > 0.3 ? '100%' : '70%';
        const width = Math.random() > 0.5 ? '2px' : '1px';
        lines += `<div style="background: #333; width: ${width}; height: ${height};"></div>`;
    }
    return lines;
}

function resetBarcode() {
    const preview = document.getElementById('barcodePreview');
    preview.innerHTML = `
        <div>
            <div class="barcode-icon">📊</div>
            <div style="font-size: 11px;">Barcode Preview</div>
        </div>
    `;
    document.getElementById('barcodeValue').value = '';
}

// Auto-generate barcode when form fields change
document.querySelector('input[name="nama_barang"]').addEventListener('blur', function() {
    const jenis = document.querySelector('select[name="jenis"]').value;
    if (this.value && jenis && !document.getElementById('barcodeValue').value) {
        generateBarcode();
    }
});

document.querySelector('select[name="jenis"]').addEventListener('change', function() {
    const namaBarang = document.querySelector('input[name="nama_barang"]').value;
    if (this.value && namaBarang && !document.getElementById('barcodeValue').value) {
        generateBarcode();
    }
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const requiredFields = this.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.style.borderColor = '#dc3545';
            isValid = false;
        } else {
            field.style.borderColor = '#ddd';
        }
    });
    
    if (!isValid) {
        e.preventDefault();
        alert('Mohon lengkapi semua field yang wajib diisi');
    }
    
    // Auto-generate barcode if not exists
    if (!document.getElementById('barcodeValue').value) {
        generateBarcode();
    }
});

// Escape key to close modal
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});
</script>

<?= $this->endSection(); ?>