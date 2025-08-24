# Sitemap Diagram - Area Publik (Tanpa Autentikasi)

## Struktur Navigasi Utama

```
🏠 Homepage (/)
├── 🔧 Services (/services)
│   ├── Services Index - Daftar semua layanan
│   └── Service Detail (/services/{slug}) - Detail layanan spesifik
│
├── 💼 Portfolio (/portfolio)  
│   ├── Portfolio Index - Gallery semua project
│   └── Project Detail (/portfolio/{slug}) - Detail project spesifik
│
├── 🛍️ Products (/products)
│   ├── Products Index - Katalog produk
│   ├── Product Detail (/products/{slug}) - Detail produk
│   └── Category Products (/products/category/{categorySlug}) - Produk per kategori
│
├── 👥 About (/about)
│   ├── About Index - Info perusahaan
│   ├── Team (/about/team) - Halaman tim
│   └── Team Member (/about/team/{slug}) - Profile anggota tim
│
├── 📝 Blog (/blog)
│   ├── Blog Index - Daftar artikel
│   └── Article Detail (/blog/{slug}) - Artikel spesifik
│
├── 📞 Contact (/contact)
│   ├── Contact Form - Form kontak utama
│   └── Thank You (/contact/thank-you) - Konfirmasi pengiriman
│
├── 💰 Quotation (/quotation)
│   ├── Request Quote - Form permintaan quotation
│   └── Thank You (/quotation/thank-you) - Konfirmasi quotation
│
└── 🔐 Authentication (/auth)
    ├── Login (/login) - Form login pengguna
    ├── Register (/register) - Form registrasi akun baru
    ├── Forgot Password (/forgot-password) - Form lupa password
    ├── Reset Password (/reset-password/{token}) - Form reset password
    └── OTP Verification (/verify-otp) - Verifikasi kode OTP
```

---

## Detail Halaman per Bagian

### 🏠 **Homepage (`/`)**
- **Controller**: `HomeController@index`
- **Fungsi**: Landing page utama dengan overview perusahaan
- **Konten**: Hero section, featured services, recent projects, testimonials

### 🔧 **Services Section**
```
/services/
├── index → Daftar semua layanan yang tersedia
└── {slug} → Detail layanan dengan deskripsi lengkap, pricing, FAQ
```

### 💼 **Portfolio Section**
```
/portfolio/
├── index → Gallery project dengan filter kategori
└── {slug} → Case study project detail dengan gambar, teknologi, timeline
```

### 🛍️ **Products Section**
```
/products/
├── index → Katalog produk dengan search & filter
├── {slug} → Spesifikasi produk, gambar, harga, review
└── category/{categorySlug} → Produk dalam kategori tertentu
```

### 👥 **About Section** 
```
/about/
├── index → Company profile, visi-misi, sejarah
├── team → Halaman tim perusahaan
└── team/{slug} → Profile detail anggota tim (bio, expertise, kontak)
```

### 📝 **Blog Section**
```
/blog/
├── index → Daftar artikel dengan pagination & kategori
└── {slug} → Artikel lengkap dengan comments, sharing
```

### 📞 **Contact Section**
```
/contact/
├── index → Form kontak + info perusahaan (alamat, telepon, maps)
└── thank-you → Konfirmasi setelah submit form
```

### 💰 **Quotation System**
```
/quotation/
├── create → Form request quotation untuk project
└── thank-you → Konfirmasi quotation request
```

### 🔐 **Authentication System**
```
/login
├── GET → Form login dengan email & password
└── POST → Proses login (rate limit: 5 attempts/minute)

/register  
├── GET → Form registrasi akun baru
└── POST → Proses pendaftaran (rate limit: 3 attempts/minute)

/forgot-password
├── GET → Form input email untuk reset password
└── POST → Kirim link reset ke email (rate limit: 3 attempts/minute)

/reset-password/{token}
├── GET → Form reset password dengan token
└── POST → Update password baru (rate limit: 3 attempts/minute)

/verify-otp
├── GET → Form input kode OTP (setelah login)
└── POST → Verifikasi kode OTP + resend OTP
```

---

## Fitur Tambahan Publik

### 🤖 **SEO & Technical**
- `/sitemap.xml` - XML sitemap untuk search engines
- `/robots.txt` - Robot crawling instructions
- `/sitemap-client.xml` - Client-specific sitemap

### 💬 **Chat Widget** 
- Live chat widget tersedia di semua halaman publik
- API endpoints untuk chat status (`/api/chat/online-status`)

### 📧 **Contact Forms**
- Contact form (`POST /contact`)
- General messages (`POST /messages`) 
- Quotation requests (`POST /quotation`)

---

## Alur User Journey

### 🎯 **Visitor Flow (Guest)**
```
1. Landing di Homepage
   ↓
2. Explore Services/Products
   ↓  
3. Lihat Portfolio untuk referensi
   ↓
4. Baca About Us untuk kredibilitas  
   ↓
5. Contact atau Request Quotation
```

### 🔑 **Authentication Flow**
```
1. Guest User → Login/Register
   ├── 🆕 New User: Register → OTP Verification → Login
   └── 👤 Existing User: Login → OTP Verification → Dashboard

2. Forgot Password Flow:
   Forgot Password → Email Reset Link → Reset Password → Login

3. Security Features:
   ├── Rate Limiting (prevents spam/brute force)
   ├── OTP Verification (2FA security)
   └── Password Reset Token (secure reset)
```

### 📱 **Responsive Design**
- Semua halaman responsive dengan mobile navigation
- Dark/Light mode toggle tersedia
- Chat widget accessible di mobile

### 🔍 **SEO Optimization**
- Friendly URLs dengan slug
- Meta tags optimization
- Structured data untuk better search visibility
- XML sitemaps for crawlers

---

## Content Management

### 📊 **Dynamic Content**
- Services: Dikelola admin, slug-based routing
- Products: Katalog dengan kategori, search, filter
- Portfolio: Project showcase dengan case studies
- Blog: Article management dengan categories
- Team: Member profiles dengan bio & expertise

### 🎨 **Visual Elements**
- Image galleries untuk portfolio & products
- Team photos & bios
- Service illustrations
- Blog featured images
- Company logos & certifications

---

*Generated: 2025-08-23*
*Type: Public Area Sitemap (Non-Authenticated)*
*Version: 1.0*