<?= $this->extend('layout/templateUser') ?>
<?= $this->section('content') ?>

  

<main class="px-6 py-8">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Informasi Akun -->
      <div class="bg-white p-6 rounded-lg shadow border">
        <h2 class="text-lg font-bold mb-4">Informasi Akun</h2>
        <?php if (session()->getFlashdata('error')): ?>
    <div id="errorAlert" class="error-message">
        <?= session()->getFlashdata('error'); ?>
    </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('success')): ?>
        <div id="successAlert" class="success-message">
            <?= session()->getFlashdata('success'); ?>
        </div>
    <?php endif; ?>
        <form action="<?= base_url('user/profil/update') ?>" method="post" enctype="multipart/form-data">
          <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
            <input type="text" name="nama" value="<?= $user['nama'] ?>" class="w-full border border-gray-300 px-3 py-2 rounded text-sm" required />
          </div>
          <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" value="<?= $user['email'] ?>" class="w-full border border-gray-300 px-3 py-2 rounded text-sm" required />
          </div>
          <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700 mb-1">No HP</label>
            <input type="text" name="no_hp" value="<?= $user['no_hp'] ?>" class="w-full border border-gray-300 px-3 py-2 rounded text-sm" required />
          </div>

          <!-- Upload Foto -->
          <div class="mt-4 flex items-center gap-4">
            <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
              <img src="<?= session()->get('foto') ? base_url('uploads/' . session()->get('foto')) : 'https://via.placeholder.com/64' ?>" alt="Profile" class="rounded-full object-cover w-16 h-16" />
            </div>
            <input type="file" name="foto" accept="image/*" class="text-sm" />
          </div>

          <!-- Button -->
          <div class="mt-6 flex gap-3">
            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded text-sm">Simpan</button>
            <button type="reset" class="bg-gray-300 text-gray-700 px-4 py-2 rounded text-sm">Batal</button>
          </div>
        </form>
      </div>

      <!-- Ganti Password -->
      <div class="bg-white p-6 rounded-lg shadow border">
        <h2 class="text-lg font-bold mb-4">Ganti Password</h2>
        <?php if (session()->getFlashdata('errorp')): ?>
    <div id="errorAlert" class="error-message">
        <?= session()->getFlashdata('errorp'); ?>
    </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('successp')): ?>
        <div id="successAlert" class="success-message">
            <?= session()->getFlashdata('successp'); ?>
        </div>
    <?php endif; ?>
        <form action="<?= base_url('user/profil/ganti-password') ?>" method="post">
        <div class="mb-3 relative">
          <label class="block text-sm font-medium text-gray-700 mb-1">Password Lama</label>
          <input type="password" name="password_lama" id="passwordLama" placeholder="Masukkan password lama" class="w-full border border-gray-300 px-3 py-2 rounded text-sm" required />
          <span class="toggle-password" id="togglePasswordLama" style="position:absolute;top:38px;right:12px;cursor:pointer;">
            <svg class="icon-hide" viewBox="0 5 24 24" width="20" height="20">
              <path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7zm0 12c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8a3 3 0 100 6 3 3 0 000-6z" />
            </svg>
            <svg class="icon-show" viewBox="0 6 24 24" width="20" height="20" style="display:none;">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12zm11 3a3 3 0 100-6 3 3 0 000 6z" fill="#888" />
              <line x1="4" y1="4" x2="20" y2="20" stroke="#e53935" stroke-width="2" />
            </svg>
          </span>
        </div>

        <div class="mb-5 relative">
          <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
          <input type="password" name="password_baru" id="passwordBaru" placeholder="Masukkan password baru" class="w-full border border-gray-300 px-3 py-2 rounded text-sm" required />
          <span class="toggle-password" id="togglePasswordBaru" style="position:absolute;top:38px;right:12px;cursor:pointer;">
            <svg class="icon-hide" viewBox="0 5 24 24" width="20" height="20">
              <path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7zm0 12c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8a3 3 0 100 6 3 3 0 000-6z" />
            </svg>
            <svg class="icon-show" viewBox="0 6 24 24" width="20" height="20" style="display:none;">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12zm11 3a3 3 0 100-6 3 3 0 000 6z" fill="#888" />
              <line x1="4" y1="4" x2="20" y2="20" stroke="#e53935" stroke-width="2" />
            </svg>
          </span>
        </div>
        <small id="passwordError" class="error-text"></small>

        <!-- Konfirmasi Password Baru -->
        <div class="mb-4 relative">
          <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
          <input type="password" name="konfirmasi_password" id="konfirmasiPassword" placeholder="Masukkan konfirmasi" class="w-full border border-gray-300 px-3 py-2 rounded text-sm" required />
          <span class="toggle-password" id="toggleKonfirmasiPassword" style="position:absolute;top:38px;right:12px;cursor:pointer;">
            <svg class="icon-hide" viewBox="0 5 24 24" width="20" height="20">
              <path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7zm0 12c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8a3 3 0 100 6 3 3 0 000-6z" />
            </svg>
            <svg class="icon-show" viewBox="0 6 24 24" width="20" height="20" style="display:none;">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12zm11 3a3 3 0 100-6 3 3 0 000 6z" fill="#888" />
              <line x1="4" y1="4" x2="20" y2="20" stroke="#e53935" stroke-width="2" />
            </svg>
          </span>
        </div>
        <small id="confirmPasswordError" class="error-text"></small>

        <div class="flex gap-3">
          <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded text-sm">Ubah</button>
          <button type="reset" class="bg-gray-300 text-gray-700 px-4 py-2 rounded text-sm">Batal</button>
        </div>
      </form>

        <!-- Logout -->
        <form id="logoutForm" action="<?= base_url('user/logout') ?>" method="post" class="mt-8 flex justify-center">
            <button type="submit" id="logoutBtn" class="bg-gray-700 text-white px-6 py-3 rounded text-sm font-semibold">Logout</button>
        </form> 
      </div>
    </div>
  </main>

  <!-- Tambahkan SweetAlert2 CDN sebelum penutup body -->

<?= $this->endSection() ?>

