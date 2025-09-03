<?php

/**
 * Script untuk mengganti semua teks bahasa Inggris ke Indonesia di view files
 * Jalankan dengan: php translate-views.php
 */

// Dictionary terjemahan dari English ke Indonesian
$translations = [
    // Common UI Elements
    'View' => 'Lihat',
    'Edit' => 'Edit',
    'Delete' => 'Hapus',
    'Create' => 'Buat',
    'Add' => 'Tambah',
    'Remove' => 'Hapus',
    'Save' => 'Simpan',
    'Cancel' => 'Batal',
    'Submit' => 'Kirim',
    'Update' => 'Perbarui',
    'Clear' => 'Hapus',
    'Reset' => 'Reset',
    'Search' => 'Cari',
    'Filter' => 'Saring',
    'Sort' => 'Urutkan',
    'Upload' => 'Unggah',
    'Download' => 'Unduh',
    'Export' => 'Ekspor',
    'Import' => 'Impor',
    'Print' => 'Cetak',
    'Send' => 'Kirim',
    
    // Navigation & Menu
    'Home' => 'Beranda',
    'Dashboard' => 'Dasbor',
    'Profile' => 'Profil',
    'Settings' => 'Pengaturan',
    'Logout' => 'Keluar',
    'Login' => 'Masuk',
    'Register' => 'Daftar',
    'Back' => 'Kembali',
    'Next' => 'Selanjutnya',
    'Previous' => 'Sebelumnya',
    'Continue' => 'Lanjutkan',
    
    // Status
    'Active' => 'Aktif',
    'Inactive' => 'Tidak Aktif',
    'Pending' => 'Tertunda',
    'Approved' => 'Disetujui',
    'Rejected' => 'Ditolak',
    'Completed' => 'Selesai',
    'Processing' => 'Sedang Diproses',
    'Draft' => 'Draf',
    'Published' => 'Dipublikasikan',
    'Available' => 'Tersedia',
    'Unavailable' => 'Tidak Tersedia',
    'In Stock' => 'Tersedia',
    'Out of Stock' => 'Habis',
    'Delivered' => 'Terkirim',
    'Complete' => 'Selesai',
    
    // Common Phrases
    'All' => 'Semua',
    'None' => 'Tidak Ada',
    'Yes' => 'Ya',
    'No' => 'Tidak',
    'Total' => 'Total',
    'Subtotal' => 'Subtotal',
    'Price' => 'Harga',
    'Amount' => 'Jumlah',
    'Quantity' => 'Jumlah',
    'Date' => 'Tanggal',
    'Time' => 'Waktu',
    'Actions' => 'Aksi',
    'Status' => 'Status',
    'Name' => 'Nama',
    'Description' => 'Deskripsi',
    'Category' => 'Kategori',
    'Type' => 'Jenis',
    'Details' => 'Detail',
    'Information' => 'Informasi',
    'Notes' => 'Catatan',
    'Comments' => 'Komentar',
    'Message' => 'Pesan',
    'Messages' => 'Pesan',
    
    // Products & Shopping
    'Products' => 'Produk',
    'Product' => 'Produk',
    'Browse Products' => 'Jelajahi Produk',
    'Shopping Cart' => 'Keranjang Belanja',
    'Cart' => 'Keranjang',
    'Add to Cart' => 'Tambah ke Keranjang',
    'Remove from Cart' => 'Hapus dari Keranjang',
    'Checkout' => 'Checkout',
    'Proceed to Checkout' => 'Lanjut ke Checkout',
    'Continue Shopping' => 'Lanjutkan Belanja',
    'Order' => 'Pesanan',
    'Orders' => 'Pesanan',
    'My Orders' => 'Pesanan Saya',
    'Order History' => 'Riwayat Pesanan',
    'Place Order' => 'Buat Pesanan',
    'Track Order' => 'Lacak Pesanan',
    'Payment' => 'Pembayaran',
    'Shipping' => 'Pengiriman',
    'Delivery' => 'Pengiriman',
    'Address' => 'Alamat',
    'Phone' => 'Telepon',
    'Email' => 'Email',
    
    // Forms & Validation
    'Required' => 'Wajib',
    'Optional' => 'Opsional',
    'Please fill in this field' => 'Mohon isi kolom ini',
    'Invalid email format' => 'Format email tidak valid',
    'Password must be at least' => 'Password minimal',
    'Confirm Password' => 'Konfirmasi Password',
    'Old Password' => 'Password Lama',
    'New Password' => 'Password Baru',
    
    // Messages
    'Success' => 'Berhasil',
    'Error' => 'Error',
    'Warning' => 'Peringatan',
    'Info' => 'Informasi',
    'Loading' => 'Memuat',
    'Please wait' => 'Mohon tunggu',
    'No data found' => 'Data tidak ditemukan',
    'No results found' => 'Hasil tidak ditemukan',
    'Something went wrong' => 'Terjadi kesalahan',
    
    // Time & Date
    'Today' => 'Hari Ini',
    'Yesterday' => 'Kemarin',
    'Tomorrow' => 'Besok',
    'Last Week' => 'Minggu Lalu',
    'This Week' => 'Minggu Ini',
    'Next Week' => 'Minggu Depan',
    'Last Month' => 'Bulan Lalu',
    'This Month' => 'Bulan Ini',
    'Next Month' => 'Bulan Depan',
    
    // Specific to your app
    'Quotations' => 'Penawaran',
    'Quotation' => 'Penawaran',
    'Request Quote' => 'Minta Penawaran',
    'Projects' => 'Proyek',
    'Project' => 'Proyek',
    'My Projects' => 'Proyek Saya',
    'Testimonials' => 'Testimoni',
    'Testimonial' => 'Testimoni',
    'My Reviews' => 'Ulasan Saya',
    'Portfolio' => 'Portofolio',
    'Notifications' => 'Notifikasi',
    'Notification' => 'Notifikasi',
    
    // Longer phrases (perlu dikustomisasi per konteks)
    'Track and manage your product orders' => 'Lacak dan kelola pesanan produk Anda',
    'Discover our products and add them to your cart' => 'Temukan produk kami dan tambahkan ke keranjang Anda',
    'Review your items before checkout' => 'Tinjau item Anda sebelum checkout',
    'Your cart is empty' => 'Keranjang Anda kosong',
    'Start adding some products to your cart' => 'Mulai menambahkan beberapa produk ke keranjang Anda',
    'Are you sure you want to remove this item' => 'Apakah Anda yakin ingin menghapus item ini',
    'Are you sure you want to clear your entire cart' => 'Apakah Anda yakin ingin mengosongkan seluruh keranjang Anda',
    'Failed to update quantity' => 'Gagal memperbarui jumlah',
    'Failed to remove item' => 'Gagal menghapus item',
    'Failed to clear cart' => 'Gagal mengosongkan keranjang',
    
    // Alert messages
    'Confirm Delivery' => 'Konfirmasi Penerimaan',
    'Mark all read' => 'Tandai semua dibaca',
    'Quick View' => 'Lihat Cepat',
    'View Details' => 'Lihat Detail',
    'Show more' => 'Tampilkan lebih banyak',
    'Show less' => 'Tampilkan lebih sedikit',
    'Load more' => 'Muat lebih banyak',
    
    // Sorting & Filtering
    'Sort By' => 'Urutkan',
    'Name A-Z' => 'Nama A-Z', 
    'Price Low-High' => 'Harga Rendah-Tinggi',
    'Newest First' => 'Terbaru',
    'Featured' => 'Unggulan',
    'All Prices' => 'Semua Harga',
    'All Products' => 'Semua Produk',
    'Clear all filters' => 'Hapus semua filter',
    'Price Range' => 'Rentang Harga',
    'Availability' => 'Ketersediaan',
    'Under' => 'Dibawah',
    'Over' => 'Diatas',
    'Quote Required' => 'Perlu Penawaran',
    
    // Numbers display
    'Showing' => 'Menampilkan',
    'to' => 'sampai',
    'of' => 'dari',
    'products' => 'produk',
    'items' => 'item',
    'results' => 'hasil',
];

// Path ke direktori views
$viewsPath = __DIR__ . '/resources/views/client';

if (!is_dir($viewsPath)) {
    echo "Error: Views directory not found: $viewsPath\n";
    exit(1);
}

/**
 * Fungsi untuk melakukan translasi file
 */
function translateFile($filePath, $translations) {
    echo "Processing: $filePath\n";
    
    $content = file_get_contents($filePath);
    $originalContent = $content;
    
    $changesMade = 0;
    
    foreach ($translations as $english => $indonesian) {
        // Skip jika sudah sama (untuk avoid mengganti yang sudah benar)
        if ($english === $indonesian) {
            continue;
        }
        
        // Pattern untuk mencocokkan teks dalam konteks yang tepat
        // Hindari mengganti dalam kode PHP, hanya dalam konten HTML/text
        
        // Pattern 1: Dalam content tag HTML (seperti <h1>Text</h1>)
        $pattern1 = '/>([\s]*' . preg_quote($english, '/') . '[\s]*)</';
        if (preg_match($pattern1, $content)) {
            $content = preg_replace($pattern1, '>$1<', str_replace($english, $indonesian, $content));
            $changesMade++;
        }
        
        // Pattern 2: Dalam atribut seperti title, placeholder, alt
        $attributes = ['title', 'placeholder', 'alt', 'aria-label'];
        foreach ($attributes as $attr) {
            $pattern = '/' . $attr . '=["\']([^"\']*' . preg_quote($english, '/') . '[^"\']*)["\']/' ;
            if (preg_match($pattern, $content)) {
                $content = preg_replace_callback($pattern, function($matches) use ($english, $indonesian) {
                    return str_replace($english, $indonesian, $matches[0]);
                }, $content);
                $changesMade++;
            }
        }
        
        // Pattern 3: Dalam JavaScript strings (alert, confirm, dll)
        $jsPatterns = [
            "/alert\(['\"]([^'\"]*" . preg_quote($english, '/') . "[^'\"]*)['\"]/" ,
            "/confirm\(['\"]([^'\"]*" . preg_quote($english, '/') . "[^'\"]*)['\"]/" 
        ];
        
        foreach ($jsPatterns as $jsPattern) {
            if (preg_match($jsPattern, $content)) {
                $content = preg_replace_callback($jsPattern, function($matches) use ($english, $indonesian) {
                    return str_replace($english, $indonesian, $matches[0]);
                }, $content);
                $changesMade++;
            }
        }
        
        // Pattern 4: Standalone text (lebih hati-hati)
        // Hanya untuk kata/frase yang berdiri sendiri
        $standalonePattern = '/\b' . preg_quote($english, '/') . '\b/';
        if (preg_match($standalonePattern, $content) && !preg_match('/\$|->|::|function|class|namespace/', $content)) {
            $content = preg_replace($standalonePattern, $indonesian, $content);
            $changesMade++;
        }
    }
    
    // Tulis file jika ada perubahan
    if ($changesMade > 0 && $content !== $originalContent) {
        file_put_contents($filePath, $content);
        echo "  ✓ Made $changesMade changes\n";
        return true;
    } else {
        echo "  - No changes needed\n";
        return false;
    }
}

/**
 * Fungsi rekursif untuk memproses semua file .blade.php
 */
function processDirectory($dir, $translations) {
    $files = scandir($dir);
    $processedFiles = 0;
    $changedFiles = 0;
    
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        
        $filePath = $dir . '/' . $file;
        
        if (is_dir($filePath)) {
            // Proses direktori secara rekursif
            list($subProcessed, $subChanged) = processDirectory($filePath, $translations);
            $processedFiles += $subProcessed;
            $changedFiles += $subChanged;
        } elseif (pathinfo($file, PATHINFO_EXTENSION) === 'php' && strpos($file, '.blade.') !== false) {
            // Proses file .blade.php
            $processedFiles++;
            if (translateFile($filePath, $translations)) {
                $changedFiles++;
            }
        }
    }
    
    return [$processedFiles, $changedFiles];
}

echo "🚀 Starting translation process...\n";
echo "Views directory: $viewsPath\n";
echo "Dictionary contains " . count($translations) . " translations\n\n";

// Backup reminder
echo "⚠️  IMPORTANT: Make sure you have backed up your files before running this script!\n";
echo "Press Enter to continue or Ctrl+C to cancel...";
fgets(STDIN);

echo "\nProcessing files...\n";
echo str_repeat('-', 50) . "\n";

list($totalFiles, $changedFiles) = processDirectory($viewsPath, $translations);

echo str_repeat('-', 50) . "\n";
echo "✅ Translation complete!\n";
echo "📊 Summary:\n";
echo "   - Total files processed: $totalFiles\n";
echo "   - Files modified: $changedFiles\n";
echo "   - Files unchanged: " . ($totalFiles - $changedFiles) . "\n";

if ($changedFiles > 0) {
    echo "\n💡 Recommended next steps:\n";
    echo "   1. Test your application thoroughly\n";
    echo "   2. Check for any broken functionality\n";
    echo "   3. Review the changes in your version control\n";
    echo "   4. Make any manual adjustments if needed\n";
}

echo "\n🎉 Done!\n";