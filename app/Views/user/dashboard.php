 <?= $this->extend('layout/templateUser'); ?>
 <?= $this->section('content'); ?>
 <!-- Main -->
 <main class="px-6 py-6">
     <!-- Card statistik -->
     <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
         <div class="bg-white rounded-xl shadow p-4 flex items-center gap-4">
             <i data-feather="package" class="w-6 h-6 text-gray-600"></i>
             <div>
                 <div class="text-sm text-gray-500">Barang Masuk</div>
                 <div class="text-xl font-bold text-gray-800">666</div>
             </div>
         </div>
         <div class="bg-white rounded-xl shadow p-4 flex items-center gap-4">
             <i data-feather="arrow-down" class="w-6 h-6 text-gray-600"></i>
             <div>
                 <div class="text-sm text-gray-500">Barang Dipakai</div>
                 <div class="text-xl font-bold text-gray-800">666</div>
             </div>
         </div>
         <div class="bg-white rounded-xl shadow p-4 flex items-center gap-4">
             <i data-feather="box" class="w-6 h-6 text-gray-600"></i>
             <div>
                 <div class="text-sm text-gray-500">Total Stok</div>
                 <div class="text-xl font-bold text-gray-800">666</div>
             </div>
         </div>
         <div class="bg-white rounded-xl shadow p-4 flex items-center gap-4">
             <i data-feather="alert-triangle" class="w-6 h-6 text-gray-600"></i>
             <div>
                 <div class="text-sm text-gray-500">Stok Minimum</div>
                 <div class="text-xl font-bold text-gray-800">666</div>
             </div>
         </div>
     </div>

     <!-- Riwayat table -->
     <div class="bg-white rounded-xl shadow p-4">
         <h2 class="text-lg font-semibold mb-4">Riwayat Terakhir</h2>
         <div class="overflow-x-auto">
             <table class="min-w-full text-sm text-left">
                 <thead>
                     <tr class="border-b text-gray-700 font-semibold">
                         <th class="px-4 py-2">No</th>
                         <th class="px-4 py-2">Waktu</th>
                         <th class="px-4 py-2">Barang</th>
                         <th class="px-4 py-2">Jenis</th>
                         <th class="px-4 py-2">Jumlah</th>
                     </tr>
                 </thead>
                 <tbody>
                     <?php for ($i = 1; $i <= 4; $i++): ?>
                         <tr class="border-b hover:bg-gray-50">
                             <td class="px-4 py-2"><?= $i ?></td>
                             <td class="px-4 py-2">2025-08-05</td>
                             <td class="px-4 py-2">Barang <?= $i ?></td>
                             <td class="px-4 py-2">Masuk</td>
                             <td class="px-4 py-2">100</td>
                         </tr>
                     <?php endfor; ?>
                 </tbody>
             </table>
         </div>

         <!-- Pagination -->
         <div class="mt-4 flex justify-center space-x-1">
             <?php for ($p = 1; $p <= 5; $p++): ?>
                 <a href="#" class="px-3 py-1 rounded-full <?= $p == 2 ? 'bg-gray-800 text-white' : 'bg-gray-200 text-gray-700' ?> text-sm">
                     <?= $p ?>
                 </a>
             <?php endfor; ?>
             <span class="px-3 py-1 text-gray-500">...</span>
             <a href="#" class="px-3 py-1 rounded-full bg-gray-200 text-gray-700 text-sm">20</a>
         </div>
     </div>
 </main>
 <?= $this->endSection(); ?>