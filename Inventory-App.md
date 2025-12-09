aplikasi inventori berbasis web dengan fitur tracking item 
## Alur Kerja Pengguna (User)
Alur kerja dirancang untuk memastikan setiap langkah tercatat dengan akurat.

1. *Menerima Work Instruction (WI)*
   - Pengguna melihat WI yang ditugaskan.
   - Setiap WI memiliki Nomor Seri unik dan Tenggat Waktu yang ditentukan oleh Admin.

2. *Mengeksekusi Misi & Mengisi Checklist*
   - Pengguna mengikuti instruksi dalam WI dan mengisi checklist interaktif.
   - *Untuk WI jenis Checking:*
     - Sebelum menandai item, pengguna wajib mengisi:
       - Kondisi Barang: Good atau Not Good.
       - Jumlah (QTY): Stok aktual yang ditemukan.
   - *Untuk WI jenis Ambil:*
     - Sebelum menandai item, pengguna wajib mengonfirmasi bahwa barang sudah diberikan ke line produksi yang ditentukan.

3. *Melaporkan*
   - Setelah semua item dalam WI selesai dicek atau diambil, pengguna mengirim laporan akhir.

4. *Selesai*
   - Proses diulang sampai semua WI selesai dikerjakan.

---

## Hak Akses & Fitur Sistem

### Pengguna (User)
*Tugas Utama:* Menjalankan WI harian dan melaporkan hasilnya.  
*Fitur:*
- Read and Update WI: Melihat detail WI yang ditugaskan, termasuk Tenggat Waktu.
- Check Item Location: Mengetahui lokasi item.
- Interactive Checklist:
  - Mencatat Kondisi dan Jumlah saat melakukan Checking.
  - Mengonfirmasi Pengiriman ke Line saat melakukan Ambil.
- Report: Mengirim laporan berdasarkan data checklist.

### Administrator (Admin)
*Tugas Utama:* Mengatur, mengawasi, dan mengendalikan pergerakan item serta kinerja pengguna.  
*Fitur:*

- *Pengelolaan Data Master:*
  - CRUD Item: Mengelola data item.
  - CRUD User: Mengelola data pengguna.
  - CRUD WI: Membuat WI baru, menentukan jenis (Ambil atau Checking), menugaskan pengguna, mengatur detail item, dan menentukan deadline.

- *Pengelolaan Inventori:*
  - Manage Item Location: Menetapkan/mengubah lokasi penyimpanan.
  - Adjust Item Stock: Mengubah jumlah stok secara manual.
  - Transfer Item Location: Memindahkan item antar lokasi.

- *Pelaporan & Pengawasan:*
  - View WI Detail per Item: Melihat status setiap item dalam WI (contoh status: Belum Diisi, Belum Diambil, Belum Dikonfirmasi).
  - Check Report from User: Meninjau laporan pengguna, termasuk kondisi dan jumlah item.
  - Report Stock Item: Menampilkan laporan stok terkini.
  - Report Item Movement: Melacak riwayat pergerakan item.
  - Report Low Stock: Memberi peringatan jika stok menipis.
  - Report WI Completion: Memantau status penyelesaian WI, termasuk pencapaian deadline.
"""