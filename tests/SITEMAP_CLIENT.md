# Sitemap Diagram - Area Client (Authenticated Users)

## Akses & Permissions
- **Middleware**: `['auth', 'client']`
- **Role**: Client role required
- **Authentication**: Login + OTP Verification required
- **Route Prefix**: `/client/*`

---

## Struktur Navigasi Client Area

```
🏠 Client Dashboard (/client/dashboard)
├── 📊 Dashboard Widgets & Stats
├── ⚡ Realtime Data & Charts  
├── 🔔 Quick Notifications
└── 📈 Performance Metrics

📂 Main Navigation Sections:
├── 💼 Projects (/client/projects)
│   ├── Projects List - Daftar project client
│   ├── Project Detail (/client/projects/{id}) - Detail project
│   ├── Documents (/client/projects/{id}/documents) - File project
│   ├── Timeline (/client/projects/{id}/timeline) - Progress timeline
│   ├── File Downloads (/client/projects/{id}/files/{file}/download)
│   └── Testimonial (/client/projects/{id}/testimonial) - Buat testimonial
│
├── 🛍️ Products & Orders (/client/products & /client/orders)
│   ├── Product Browse (/client/products) - Katalog produk
│   ├── Product Detail (/client/products/{id}) - Detail produk
│   ├── Category Browse (/client/products/category/{category})
│   ├── 🛒 Cart (/client/cart) - Shopping cart
│   ├── Orders List (/client/orders) - Daftar pesanan
│   ├── Order Detail (/client/orders/{id}) - Detail pesanan
│   ├── Checkout (/client/orders/checkout) - Proses checkout
│   ├── Negotiation (/client/orders/{id}/negotiate) - Negosiasi harga
│   └── Payment (/client/orders/{id}/payment) - Upload bukti bayar
│
├── 💰 Quotations (/client/quotations)
│   ├── Quotations List - Daftar quotation
│   ├── Create Quotation (/client/quotations/create) - Buat quotation
│   ├── Edit Quotation (/client/quotations/{id}/edit) - Edit quotation
│   ├── Quotation Detail (/client/quotations/{id}) - Detail quotation
│   ├── Duplicate (/client/quotations/{id}/duplicate) - Duplikasi
│   ├── Cancel (/client/quotations/{id}/cancel) - Batalkan quotation
│   ├── Print (/client/quotations/{id}/print) - Print quotation
│   ├── Activity Log (/client/quotations/{id}/activity) - Log aktivitas
│   └── Attachments - Upload/download lampiran
│
├── 📧 Messages (/client/messages)
│   ├── Messages List - Daftar pesan
│   ├── Create Message (/client/messages/create) - Buat pesan baru
│   ├── Message Detail (/client/messages/{id}) - Detail pesan
│   ├── Reply Messages (/client/messages/{id}/reply) - Balas pesan
│   ├── Project Messages (/client/messages/project/{id}) - Pesan per project
│   ├── Order Messages (/client/messages/order/{id}) - Pesan per order
│   ├── Mark Urgent - Tandai penting
│   ├── Bulk Actions - Aksi massal
│   └── Attachments - Upload/download lampiran
│
├── ⭐ Testimonials (/client/testimonials)
│   ├── Testimonials List - Daftar testimonial
│   ├── Create Testimonial (/client/testimonials/create) - Buat testimonial
│   ├── Edit Testimonial (/client/testimonials/{id}/edit) - Edit testimonial
│   ├── Testimonial Detail (/client/testimonials/{id}) - Detail testimonial
│   ├── Preview (/client/testimonials/{id}/preview) - Preview testimonial
│   └── Image Upload - Upload foto testimonial
│
├── 💬 Live Chat (/client/chat)
│   ├── Chat Interface - Interface chat
│   ├── Chat History (/client/chat/history) - Riwayat chat
│   └── Chat Session (/client/chat/{session}) - Session chat tertentu
│
└── 🔔 Notifications (/client/notifications)
    ├── Notifications List - Daftar notifikasi
    ├── Notification Detail (/client/notifications/{id}) - Detail notifikasi
    ├── Preferences (/client/notifications/preferences) - Pengaturan notifikasi
    ├── Mark as Read - Tandai dibaca
    ├── Mark All Read - Tandai semua dibaca
    └── Bulk Actions - Aksi massal notifikasi
```

---

## Detail Fungsi per Bagian

### 🏠 **Client Dashboard**
```
/client/dashboard
├── Overview Stats - Project, quotation, message counts
├── Recent Activities - Aktivitas terbaru
├── Upcoming Deadlines - Deadline yang akan datang
├── Performance Metrics - Metrik performa
├── Realtime Stats - Data realtime
├── Chart Data - Data untuk grafik
└── Notification Widgets - Widget notifikasi
```

### 💼 **Projects Management**
```
/client/projects/
├── index → Daftar semua project client
├── {project} → Detail project dengan milestone & progress
├── {project}/documents → Download file project
├── {project}/timeline → Timeline progress project
├── {project}/files/{file}/download → Download file tertentu
├── {project}/testimonial/create → Form buat testimonial
└── statistics → Statistik project client
```

### 🛍️ **Product Orders System**
```
/client/products/
├── index → Browse katalog produk
├── {product} → Detail produk + add to cart
└── category/{category} → Produk per kategori

/client/cart/
├── index → View cart items
├── add → Tambah ke cart
├── update-quantity → Update jumlah item
├── remove → Hapus item dari cart
├── clear → Kosongkan cart
└── count → Get cart count (AJAX)

/client/orders/
├── index → Daftar pesanan client
├── checkout → Proses checkout dari cart
├── store → Submit order baru
├── {order} → Detail pesanan
├── {order}/cancel → Batalkan pesanan
├── {order}/negotiate → Form negosiasi harga
├── {order}/accept-negotiation → Terima hasil negosiasi
├── {order}/payment → Upload bukti pembayaran
└── Cart Actions → Add, remove, update cart items
```

### 💰 **Quotations System**
```
/client/quotations/
├── index → Daftar quotation client
├── create → Form quotation baru
├── store → Submit quotation
├── {quotation} → Detail quotation
├── {quotation}/edit → Edit quotation
├── {quotation}/update → Update quotation
├── {quotation}/duplicate → Duplikasi quotation
├── {quotation}/cancel → Batalkan quotation
├── {quotation}/activity → Log aktivitas quotation
├── {quotation}/print → Print quotation
├── Attachment Management → Upload/delete/download files
└── Temp File Handling → Handle temporary uploads
```

### 📧 **Messages System**
```
/client/messages/
├── index → Daftar pesan client
├── create → Form pesan baru
├── store → Kirim pesan
├── {message} → Detail thread pesan
├── {message}/reply → Balas pesan
├── {message}/mark-urgent → Tandai urgent
├── {message}/toggle-read → Toggle status baca
├── project/{project} → Pesan khusus project
├── order/{order} → Pesan khusus order
├── bulk-action → Aksi massal pesan
├── Attachments → Upload/download lampiran
└── API Endpoints → Statistics, mark read, toggle
```

### ⭐ **Testimonials Management**
```
/client/testimonials/
├── index → Daftar testimonial client
├── create → Form testimonial baru
├── store → Submit testimonial
├── {testimonial} → Detail testimonial
├── {testimonial}/edit → Edit testimonial
├── {testimonial}/update → Update testimonial
├── {testimonial}/preview → Preview testimonial
├── {testimonial}/destroy → Hapus testimonial
└── Image Upload → Upload foto testimonial
```

### 💬 **Live Chat System**
```
/client/chat/
├── index → Interface chat utama
├── history → Riwayat semua chat
└── {chatSession} → Chat session tertentu
```

### 🔔 **Notifications Center**
```
/client/notifications/
├── index → Daftar semua notifikasi
├── {notification} → Detail notifikasi
├── preferences → Pengaturan notifikasi
├── {notification}/read → Mark as read
├── mark-all-read → Mark semua as read
├── bulk-mark-as-read → Bulk mark as read
├── bulk-delete → Bulk hapus notifikasi
├── clear-read → Hapus yang sudah dibaca
└── API Endpoints → Recent, summary, unread count
```

---

## Fitur Keamanan & Performance

### 🛡️ **Security Features**
- **Rate Limiting**: Berbagai endpoint dengan limit berbeda
  - Messages Reply: 10 attempts/minute
  - Bulk Actions: 20 attempts/minute  
  - File Upload: 30 attempts/minute
  - API calls: 60-120 attempts/minute
- **File Upload Security**: Temporary file handling dengan cleanup
- **Permission Checks**: Client role validation pada setiap route

### ⚡ **Performance Features**
- **AJAX Endpoints**: Realtime stats, counts, summaries
- **Caching**: Dashboard cache dengan clear function
- **Lazy Loading**: Chart data loaded separately
- **Bulk Operations**: Efficient mass actions untuk messages & notifications

### 📱 **User Experience**
- **Responsive Design**: Mobile-friendly interface
- **Real-time Updates**: Live dashboard stats
- **File Management**: Drag-drop upload dengan progress
- **Search & Filter**: Advanced filtering di setiap module
- **Pagination**: Efficient data loading
- **Toast Notifications**: User feedback untuk setiap aksi

---

## API Endpoints

### 📊 **Dashboard APIs**
```
/client/api/
├── dashboard/stats → Realtime statistics
├── notifications/count → Unread notifications count  
├── projects/stats → Project statistics
└── quotations/stats → Quotation statistics
```

### 📧 **Messages APIs** 
```
/client/messages/api/
├── statistics → Message statistics
├── mark-all-read → Mark all messages as read
└── {message}/toggle-read → Toggle read status
```

---

## User Journey Flow

### 🎯 **Typical Client Workflow**
```
1. Login → Dashboard (Overview stats & activities)
   ↓
2a. Browse Products → Add to Cart → Checkout → Order Management
2b. Request Quotation → Review → Accept → Convert to Project
   ↓
3. Project Management → View Progress → Download Files → Give Testimonial
   ↓
4. Messages → Communication with Admin → Track Issues
   ↓
5. Notifications → Stay updated on project status
```

### 🔄 **Order Processing Flow**
```
Client: Browse Products → Add to Cart → Checkout → Submit Order
  ↓
Admin: Review Order → Process/Negotiate
  ↓  
Client: View Order Status → Upload Payment → Receive Product
  ↓
Optional: Negotiate Price → Accept/Counter → Finalize
```

### 💬 **Communication Channels**
- **Messages**: Formal communication dengan threading
- **Live Chat**: Real-time support  
- **Notifications**: System updates & alerts
- **Order Comments**: Order-specific communication

---

*Generated: 2025-08-23*  
*Type: Client Area Sitemap (Authenticated Users)*  
*Version: 1.0*