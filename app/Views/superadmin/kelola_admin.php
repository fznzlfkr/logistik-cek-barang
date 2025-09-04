<?= $this->extend('layout/TemplateSuperAdmin') ?>

<?= $this->section('content') ?>
<!-- Main Content -->
<div class="main-content">
    <!-- Header -->
    <div class="header">
        <div class="header-content">
            <div class="header-title">
                <h1>Kelola Admin</h1>
                <p>Daftar semua Admin pada sistem.</p>
            </div>
            <div class="header-actions">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Cari...">
                </div>
                <div class="user-profile">
                    <div class="user-avatar">SA</div>
                    <a href="<?= base_url('superadmin/pengaturan-akun') ?>" class="a-info">
                        <div class="user-info">
                            <h6><?= esc($superAdmin['nama']) ?></h6>
                            <p><?= esc($superAdmin['role']) ?></p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Area -->
    <div class="content-area">

        <!-- Search & Filter -->
        <div class="mb-4 flex items-center gap-2">
            <form method="get" class="flex items-center gap-2">
                <input type="text" name="keyword" value="<?= esc(service('request')->getVar('keyword')) ?>"
                    placeholder="Cari admin..."
                    class="px-3 py-2 border border-gray-300 rounded w-64 focus:outline-none focus:ring focus:ring-blue-200 text-sm" />

                <button type="submit"
                    class="px-3 py-2 bg-blue-500 text-white text-sm rounded hover:bg-blue-600 transition">
                    Cari
                </button>

                <?php if (service('request')->getVar('keyword') || service('request')->getVar('per_page')): ?>
                    <a href="<?= current_url() ?>"
                        class="px-3 py-2 bg-gray-300 text-sm rounded hover:bg-gray-400 transition">
                        Reset
                    </a>
                <?php endif; ?>
            </form>

            <button type="button"
                onclick="openPrintModal()"
                class="ml-auto px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition">
                Tambah Admin
            </button>
        </div>

        <!-- Content Grid -->
        <div class="content-grid">

            <!-- Admin Table -->
            <div class="content-card">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm bg-white rounded shadow">
                        <thead class="bg-gray-200 text-gray-700 font-semibold">
                            <tr>
                                <th class="p-3 text-left">No</th>
                                <th class="p-3 text-left">Nama</th>
                                <th class="p-3 text-left">Email</th>
                                <th class="p-3 text-left">Status</th>
                                <th class="p-3 text-left">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($dataAdmin)): ?>
                                <?php foreach ($dataAdmin as $index => $admin): ?>
                                    <tr class="border-t hover:bg-gray-50">
                                        <td class="p-3"><?= $index + 1 ?></td>
                                        <td class="p-3"><?= esc($admin['nama']) ?></td>
                                        <td class="p-3"><?= esc($admin['email']) ?></td>
                                        <td class="p-3">
                                            <?php if ($admin['aktif'] == 1): ?>
                                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                                                    Aktif
                                                </span>
                                            <?php else: ?>
                                                <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-full">
                                                    Tidak Aktif
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="p-3 flex gap-2">
                                            <a href="<?= base_url('superadmin/edit-admin/' . $admin['id_admin']) ?>"
                                                class="px-2 py-1 bg-yellow-400 text-white rounded text-xs hover:bg-yellow-500 transition">
                                                Edit
                                            </a>
                                            <a href="<?= base_url('superadmin/hapus-admin/' . $admin['id_admin']) ?>"
                                                onclick="return confirm('Yakin ingin menghapus admin ini?')"
                                                class="px-2 py-1 bg-red-500 text-white rounded text-xs hover:bg-red-600 transition">
                                                Hapus
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="p-3 text-center">Belum ada data admin.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Footer Pagination -->
                <div class="flex justify-between items-center mt-4 text-sm">
                    <div class="flex items-center gap-2">
                        <span>Rows per page</span>
                        <form method="get">
                            <input type="hidden" name="keyword" value="<?= esc($keyword) ?>" />
                            <select name="per_page" onchange="this.form.submit()" class="border border-gray-300 px-2 py-1 rounded">
                                <option value="5" <?= ($perPage == 5) ? 'selected' : '' ?>>5</option>
                                <option value="10" <?= ($perPage == 10) ? 'selected' : '' ?>>10</option>
                                <option value="25" <?= ($perPage == 25) ? 'selected' : '' ?>>25</option>
                            </select>
                        </form>
                    </div>

                    <div class="flex items-center justify-center gap-2 mt-4">
                        <?php if ($pager): ?>
                            <div class="flex items-center space-x-1">
                                <?= $pager->simpleLinks('number', 'tailwind_pagination') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- </div> -->
            </div>
        </div>
    </div>

    <?= $this->endSection() ?>