<?= $this->extend('layout/templateUser') ?>
<?= $this->section('content') ?>
    <main class="px-6 py-8">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Informasi Akun -->
      <div class="bg-white p-6 rounded-lg shadow border">
        <h2 class="text-lg font-bold mb-4">Informasi Akun</h2>
        <form>
          <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
            <input type="text" value="Nama User" class="w-full border border-gray-300 px-3 py-2 rounded text-sm" />
          </div>
          <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" value="user@gmail.com" class="w-full border border-gray-300 px-3 py-2 rounded text-sm" />
          </div>
          <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700 mb-1">No HP</label>
            <input type="text" value="0881022662782" class="w-full border border-gray-300 px-3 py-2 rounded text-sm" />
          </div>

          <!-- Upload Foto -->
          <div class="mt-4 flex items-center gap-4">
            <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center">
              <img src="https://via.placeholder.com/64" alt="Profile" class="rounded-full" />
            </div>
            <button type="button" class="bg-gray-700 text-white px-3 py-2 rounded text-sm">Pilih</button>
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
        <form>
          <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700 mb-1">Password Lama</label>
            <input type="password" placeholder="Masukkan password lama" class="w-full border border-gray-300 px-3 py-2 rounded text-sm" />
          </div>
          <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
            <input type="password" placeholder="Masukkan password baru" class="w-full border border-gray-300 px-3 py-2 rounded text-sm" />
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
            <input type="password" placeholder="Masukkan konfirmasi" class="w-full border border-gray-300 px-3 py-2 rounded text-sm" />
          </div>

          <div class="flex gap-3">
            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded text-sm">Ubah</button>
            <button type="reset" class="bg-gray-300 text-gray-700 px-4 py-2 rounded text-sm">Batal</button>
          </div>
        </form>

        <!-- Logout -->
        <div class="mt-8 flex justify-center">
          <button class="bg-gray-700 text-white px-6 py-3 rounded text-sm font-semibold">Logout</button>
        </div>
      </div>
    </div>
  </main>
<?= $this->endSection() ?>