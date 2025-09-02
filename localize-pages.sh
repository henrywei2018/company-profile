#!/bin/bash

# Script untuk mengubah teks bahasa Inggris ke bahasa Indonesia
# di seluruh direktori resources/views/pages

PAGES_DIR="E:\Toko\project 2025\skripsi s1 informatika\chris\app\company-profile\resources\views\pages"

echo "🚀 Mulai mengubah teks ke bahasa Indonesia..."

# Array teks yang akan diganti
declare -A replacements=(
    # Breadcrumb dan navigasi umum
    ["Home"]="Beranda"
    ["About Us"]="Tentang Kami" 
    ["About"]="Tentang"
    ["Services"]="Layanan"
    ["Portfolio"]="Portfolio"
    ["Products"]="Produk"
    ["Contact Us"]="Hubungi Kami"
    ["Contact"]="Kontak"
    
    # Meta descriptions dan titles
    ["Learn more about our company"]="Pelajari lebih lanjut tentang perusahaan kami"
    ["Meet our professional team"]="Kenali tim profesional kami"
    ["View all our services"]="Lihat semua layanan kami"
    ["Check out our portfolio"]="Lihat portfolio kami"
    ["Get in touch with us"]="Hubungi kami"
    
    # Keywords
    ["about us, company profile, team"]="tentang kami, profil perusahaan, tim"
    ["services, construction, engineering"]="layanan, konstruksi, teknik"
    ["portfolio, projects, work"]="portfolio, proyek, karya"
    ["contact, get in touch"]="kontak, hubungi kami"
    ["team, staff, professionals"]="tim, staf, profesional"
    ["blog, articles, news"]="blog, artikel, berita"
    
    # Content umum
    ["Our Team"]="Tim Kami"
    ["Our Services"]="Layanan Kami"
    ["Our Portfolio"]="Portfolio Kami"
    ["Our Mission"]="Misi Kami"
    ["Our Vision"]="Visi Kami"
    ["Why Choose Us"]="Mengapa Memilih Kami"
    ["Get Started"]="Mulai Sekarang"
    ["Learn More"]="Pelajari Lebih Lanjut"
    ["Read More"]="Baca Selengkapnya"
    ["View More"]="Lihat Selengkapnya"
    ["View All"]="Lihat Semua"
    ["See All"]="Lihat Semua"
    ["Show More"]="Tampilkan Lebih Banyak"
    
    # Statistics dan angka
    ["Years Experience"]="Tahun Pengalaman"
    ["Completed Projects"]="Proyek Selesai"
    ["Happy Clients"]="Klien Puas"
    ["Awards Won"]="Penghargaan Diraih"
    
    # Form dan interaksi
    ["Search"]="Cari"
    ["Search by"]="Cari berdasarkan"
    ["Filter"]="Filter"
    ["Categories"]="Kategori"
    ["All Categories"]="Semua Kategori"
    ["Recent Posts"]="Postingan Terbaru"
    ["Recent Articles"]="Artikel Terbaru"
    ["Popular"]="Populer"
    ["Latest"]="Terbaru"
    
    # Project dan portfolio
    ["Project Details"]="Detail Proyek"
    ["View Project"]="Lihat Proyek"
    ["Case Study"]="Studi Kasus"
    ["Client"]="Klien"
    ["Duration"]="Durasi"
    ["Budget"]="Anggaran"
    ["Status"]="Status"
    ["Completed"]="Selesai"
    ["In Progress"]="Dalam Proses"
    
    # Blog dan artikel
    ["Published on"]="Diterbitkan pada"
    ["By"]="Oleh"
    ["Author"]="Penulis"
    ["Share"]="Bagikan"
    ["Related Articles"]="Artikel Terkait"
    ["Tags"]="Tag"
    
    # CTA dan tombol
    ["Contact Us Today"]="Hubungi Kami Hari Ini"
    ["Get Quote"]="Minta Penawaran"
    ["Request Quote"]="Minta Penawaran"
    ["Get Free Quote"]="Dapatkan Penawaran Gratis"
    ["Call Now"]="Telepon Sekarang"
    ["Email Us"]="Email Kami"
    
    # Testimoni dan review
    ["Testimonials"]="Testimoni"
    ["What Our Clients Say"]="Apa Kata Klien Kami"
    ["Client Reviews"]="Review Klien"
    ["Success Stories"]="Kisah Sukses"
    
    # Services dan fitur
    ["Our Expertise"]="Keahlian Kami" 
    ["Key Features"]="Fitur Utama"
    ["What We Offer"]="Apa Yang Kami Tawarkan"
    ["Service Areas"]="Area Layanan"
    
    # Footer
    ["Quick Links"]="Tautan Cepat"
    ["Useful Links"]="Tautan Berguna"
    ["Important Links"]="Tautan Penting"
    ["Follow Us"]="Ikuti Kami"
    ["Connect With Us"]="Terhubung Dengan Kami"
    ["Stay Connected"]="Tetap Terhubung"
)

# Function untuk mengganti teks dalam file
replace_text() {
    local file="$1"
    local search="$2"  
    local replace="$3"
    
    # Gunakan sed untuk mengganti teks (Windows-compatible)
    sed -i "s|$search|$replace|g" "$file"
}

# Iterasi semua file .blade.php di direktori pages
find "$PAGES_DIR" -name "*.blade.php" -type f | while read -r file; do
    echo "📝 Memproses: $file"
    
    # Terapkan semua penggantian
    for search in "${!replacements[@]}"; do
        replace="${replacements[$search]}"
        replace_text "$file" "$search" "$replace"
    done
done

echo "✅ Selesai! Semua file telah diubah ke bahasa Indonesia."
echo "📋 Total perubahan:"
echo "   - ${#replacements[@]} pola teks telah diganti"
echo "   - Semua file .blade.php di direktori pages telah diproses"
