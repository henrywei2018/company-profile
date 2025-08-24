# Sitemap Diagram - Area Admin (Administrator Access)

## Akses & Permissions
- **Middleware**: `['auth', 'admin']`
- **Roles**: Super-admin, Admin, Manager, Editor
- **Authentication**: Login + OTP Verification required
- **Route Prefix**: `/admin/*`

---

## Struktur Navigasi Admin Area

```
🏠 Admin Dashboard (/admin/dashboard)
├── 📊 Advanced Analytics & KPI Dashboard
├── ⚡ Google Analytics Integration
├── 📈 Realtime Statistics & Charts
├── 🔔 System Notifications
├── 🏥 System Health Monitoring
├── 🔧 Cache Management
└── 📤 Dashboard Export Features

🛡️ Core System Management:
├── 👥 User Management (/admin/users)
│   ├── User CRUD - Create, read, update, delete users
│   ├── Role Assignment - Assign roles to users
│   ├── User Verification - Verify client accounts
│   ├── Toggle Active Status - Enable/disable users
│   ├── Bulk Operations - Mass user management
│   ├── User Export - Export user data
│   ├── User Statistics - User analytics
│   ├── Password Reset - Admin reset user passwords
│   ├── User Impersonation - Login as other users
│   └── Welcome Email - Send welcome emails
│
├── 🔐 RBAC System (/admin/roles & /admin/permissions)
│   ├── Roles Management
│   │   ├── Role CRUD - Create, edit, delete roles
│   │   ├── Role Permissions - Assign permissions to roles
│   │   ├── Role Users - View users with specific roles
│   │   └── Role Duplication - Copy existing roles
│   ├── Permissions Management
│   │   ├── Permission CRUD - Manage individual permissions
│   │   ├── Bulk Permission Creation - Create multiple permissions
│   │   └── Permission-Role Mapping - View role assignments
│   └── RBAC Dashboard
│       ├── Access Control Overview - System-wide permissions view
│       ├── Audit Log - Track permission changes
│       └── Cache Management - Clear permission cache
│
└── ⚙️ System Settings (/admin/settings)
    ├── General Settings - Basic application configuration
    ├── SEO Settings - Meta tags, analytics codes
    ├── Email Settings - SMTP configuration & testing
    ├── Company Profile - Business information
    └── Cache Management - System cache control

🎨 Content Management:
├── 📝 Blog System (/admin/posts)
│   ├── Posts Management
│   │   ├── Post CRUD - Create, edit, delete blog posts
│   │   ├── Featured Posts - Toggle featured status
│   │   ├── Status Management - Draft, published, archived
│   │   ├── Featured Image - Upload/remove post images
│   │   ├── Post Duplication - Copy existing posts
│   │   ├── Bulk Actions - Mass post operations
│   │   ├── Export Posts - Data export functionality
│   │   └── Post Statistics - Analytics for posts
│   └── Categories Management (/admin/post-categories)
│       ├── Category CRUD - Manage blog categories
│       ├── Bulk Operations - Mass category management
│       ├── Popular Categories - Most used categories
│       ├── Category Export - Export category data
│       └── Search Categories - Advanced category search
│
├── 🎯 Services Management (/admin/services)
│   ├── Services CRUD - Full service management
│   ├── Service Categories (/admin/service-categories)
│   │   ├── Category CRUD - Manage service categories
│   │   ├── Toggle Active Status - Enable/disable categories
│   │   ├── Order Management - Reorder categories
│   │   └── Bulk Operations - Mass category actions
│   ├── Featured Services - Highlight important services
│   ├── Active Status Toggle - Enable/disable services
│   └── Service Ordering - Drag-drop reordering
│
├── 💼 Portfolio Management (/admin/projects)
│   ├── Projects CRUD - Complete project management
│   ├── Project Categories (/admin/project-categories) - Organize projects
│   ├── Featured Projects - Showcase selection
│   ├── Project Images - Upload/reorder gallery images
│   ├── Quick Updates - Fast project status changes
│   ├── Quotation Conversion - Convert projects to quotes
│   ├── Timeline Data - Project progress tracking
│   ├── Export Projects - Data export functionality
│   ├── Milestones Management (/admin/projects/{project}/milestones)
│   │   ├── Milestone CRUD - Task breakdown management
│   │   ├── Status Updates - Progress tracking
│   │   ├── Completion Tracking - Mark milestones done
│   │   ├── Bulk Updates - Mass milestone operations
│   │   ├── Calendar View - Timeline visualization
│   │   └── Statistics - Milestone analytics
│   └── File Management (/admin/projects/{project}/files)
│       ├── File Upload - Project document management
│       ├── File Downloads - Client file access
│       ├── File Preview - In-browser file viewing
│       ├── Bulk Operations - Mass file management
│       ├── Search Files - Advanced file search
│       └── File Statistics - Usage analytics
│
├── 🛍️ E-commerce Management
│   ├── Products (/admin/products)
│   │   ├── Product CRUD - Complete product management
│   │   ├── Product Categories (/admin/product-categories)
│   │   │   ├── Category CRUD - Product categorization
│   │   │   ├── Category Statistics - Usage analytics
│   │   │   ├── Export Categories - Data export
│   │   │   └── Bulk Operations - Mass category management
│   │   ├── Featured Products - Highlight products
│   │   ├── Product Status - Enable/disable products
│   │   ├── Product Images - Gallery management
│   │   ├── Product Duplication - Copy existing products
│   │   ├── Bulk Operations - Mass product actions
│   │   ├── Product Search - Advanced search functionality
│   │   └── Export Products - Data export features
│   └── Orders Management (/admin/orders)
│       ├── Order Processing - Complete order lifecycle
│       ├── Order Details - View full order information
│       ├── Payment Management (/admin/orders/{order}/payment)
│       │   ├── Payment Verification - Verify uploaded proofs
│       │   └── Payment Status - Update payment status
│       ├── Negotiation System (/admin/orders/{order}/negotiation)
│       │   ├── Negotiation Review - Handle price negotiations
│       │   └── Negotiation Response - Respond to client requests
│       ├── Delivery Management (/admin/orders/{order}/delivery)
│       │   ├── Delivery Tracking - Shipping status updates
│       │   └── Delivery Completion - Mark orders completed
│       ├── Order Statistics - Sales analytics
│       ├── Bulk Actions - Mass order processing
│       ├── Export Orders - Data export (CSV)
│       └── Convert to Quotation - Order conversion
│
├── 🎨 Visual Content Management
│   ├── Banners (/admin/banners)
│   │   ├── Banner CRUD - Promotional banner management
│   │   ├── Banner Categories (/admin/banner-categories)
│   │   │   ├── Category Management - Organize banners
│   │   │   ├── Category Statistics - Usage tracking
│   │   │   └── Bulk Operations - Mass category actions
│   │   ├── Banner Status - Enable/disable banners
│   │   ├── Image Upload - Banner image management
│   │   ├── Banner Duplication - Copy existing banners
│   │   ├── Reordering - Drag-drop banner order
│   │   ├── Preview - Banner preview functionality
│   │   └── Statistics - Banner performance metrics
│   └── Payment Methods (/admin/payment-methods)
│       ├── Payment Method CRUD - Payment option management
│       ├── Status Toggle - Enable/disable payment methods
│       └── Method Ordering - Reorder payment options

👥 Team & Content:
├── 👨‍💼 Team Management (/admin/team)
│   ├── Team Member CRUD - Employee profile management
│   ├── Departments (/admin/team-member-departments)
│   │   ├── Department CRUD - Organizational structure
│   │   ├── Department Statistics - Team analytics
│   │   └── Search Departments - Find departments
│   ├── Featured Members - Highlight key team members
│   ├── Photo Management - Team member photos
│   ├── Bulk Operations - Mass team member actions
│   └── Team Export - Export team data
│
├── ⭐ Testimonials (/admin/testimonials)
│   ├── Testimonial CRUD - Customer feedback management
│   ├── Client Integration - Link testimonials to clients
│   ├── Project Integration - Link testimonials to projects
│   ├── Approval System - Review/approve testimonials
│   ├── Featured Testimonials - Highlight best reviews
│   ├── Image Management - Testimonial photos
│   ├── Bulk Actions - Mass testimonial operations
│   └── Statistics - Testimonial analytics
│
└── 🏆 Certifications (/admin/certifications)
    ├── Certification CRUD - Company certification management
    ├── Certificate Images - Upload certification documents
    ├── Active Status - Enable/disable certifications
    └── Certificate Ordering - Reorder display sequence

💬 Communication Systems:
├── 📧 Messages (/admin/messages)
│   ├── Message Management - Complete message handling
│   ├── Message CRUD - Create, read, update, delete messages
│   ├── Reply System - Threaded message conversations
│   ├── Priority Management - Mark messages as urgent
│   ├── Status Tracking - Read/unread message status
│   ├── Bulk Operations - Mass message actions
│   ├── Message Forwarding - Forward messages to other admins
│   ├── Attachment Handling - File upload/download
│   ├── Export Messages - Message data export
│   └── Message Statistics - Communication analytics
│
├── 💬 Live Chat System (/admin/chat)
│   ├── Chat Sessions - Active chat management
│   ├── Chat Settings - System configuration
│   ├── Chat Reports - Communication analytics
│   ├── Session Management - Individual chat handling
│   ├── Operator Management - Admin availability status
│   ├── Chat Templates (/admin/chat/templates)
│   │   ├── Template CRUD - Pre-written response management
│   │   ├── Template Categories - Organize templates
│   │   ├── Usage Tracking - Template usage statistics
│   │   ├── Import/Export - Template data management
│   │   └── Quick Templates - Fast access templates
│   ├── Bulk Operations - Mass chat actions
│   ├── Session Assignment - Assign chats to operators
│   ├── Priority Management - High priority chat handling
│   └── Archive Management - Old session cleanup
│
├── 💰 Quotations (/admin/quotations)
│   ├── Quotation CRUD - Complete quote management
│   ├── Status Management - Quote approval workflow
│   ├── Quick Actions - Fast approve/reject
│   ├── Communication - Client quotation communication
│   ├── Project Conversion - Convert quotes to projects
│   ├── Quotation Duplication - Copy existing quotes
│   ├── Priority Management - Prioritize important quotes
│   ├── Client Linking - Associate quotes with clients
│   ├── Attachment Management - Quote document handling
│   ├── Bulk Operations - Mass quotation actions
│   ├── Export Quotations - Data export functionality
│   └── Statistics - Quotation analytics
│
└── 🔔 Notifications (/admin/notifications)
    ├── Notification Center - System-wide notifications
    ├── Notification Preferences - Admin notification settings
    ├── Bulk Notification Actions - Mass notification management
    ├── Export Notifications - Notification data export
    └── Test Notifications - System notification testing

📊 Company Information:
└── 🏢 Company Profile (/admin/company)
    ├── Company Information - Basic business details
    ├── SEO Management - Company SEO settings
    ├── Certificate Management - Company certifications
    ├── Export Options - Company data export
    └── PDF Generation - Company profile documents
```

---

## Fitur Advanced Analytics

### 📊 **Google Analytics Integration**
```
/admin/analytics/kpi/
├── dashboard → Complete KPI overview
├── realtime → Real-time analytics data
├── category/{category} → Specific analytics categories
├── bulk → Bulk analytics data retrieval
├── metric/{metric} → Individual metric tracking
├── compare → Period comparison analytics
├── refresh → Analytics data refresh
├── test-connection → GA4 API connection testing
└── cache/clear → Clear analytics cache
```

### 🎯 **Available Analytics Categories:**
- **Overview**: Users, sessions, pageviews
- **Traffic**: Traffic sources, acquisition channels  
- **Engagement**: Bounce rate, session duration
- **Conversion**: Goals, conversion tracking
- **Audience**: Demographics, interests
- **Acquisition**: Marketing channel performance
- **Behavior**: User behavior patterns
- **Technical**: Site performance metrics

---

## Security & Permissions

### 🛡️ **Role-Based Access Control**
- **Super Admin**: Full system access
- **Admin**: Most management features
- **Manager**: Content and user management
- **Editor**: Content creation and editing

### 🔒 **Security Features**
- **Rate Limiting**: Different limits per endpoint type
- **File Upload Security**: Temporary file handling with cleanup
- **Permission Caching**: Optimized permission checking
- **Audit Logging**: Track administrative actions
- **User Impersonation**: Secure user account access

### 🚦 **Rate Limiting Examples**
- Chat Messages: 30 requests/minute
- Bulk Operations: 30 requests/minute
- File Uploads: Various limits per feature
- Analytics API: 60-120 requests/minute

---

## File Management System

### 📁 **Universal File Handling**
- **Temporary Uploads**: All modules support temp file uploads
- **File Processing**: Images, documents, attachments
- **Bulk Operations**: Mass file management
- **Security Scanning**: File validation and cleaning
- **Storage Management**: Organized file storage structure

### 🔄 **File Workflow**
```
Upload → Temporary Storage → Validation → Processing → Final Storage → Cleanup
```

---

## Export & Import Features

### 📤 **Export Capabilities**
- **Users**: CSV/Excel export with filtering
- **Orders**: Complete order data export
- **Products**: Product catalog export
- **Analytics**: KPI data export
- **Messages**: Communication export
- **Company Profile**: PDF generation
- **Chat Reports**: Communication analytics

### 📊 **Statistics & Analytics**
- **Dashboard Metrics**: System-wide statistics
- **Module Statistics**: Per-module analytics
- **Performance Metrics**: System performance tracking
- **Usage Analytics**: Feature usage statistics

---

## API Endpoints & Integrations

### 🔌 **API Token Management**
```
/admin/api-tokens/
├── generate → Create new API tokens
├── list → View existing tokens
└── revoke → Revoke API access
```

### 📡 **AJAX Endpoints**
- **Dashboard Stats**: Real-time dashboard updates
- **Chart Data**: Dynamic chart population
- **Search Functions**: Advanced search across modules
- **Status Updates**: Quick status toggles
- **Bulk Actions**: Mass operation handling

---

## System Health & Maintenance

### 🏥 **System Monitoring**
- **Health Checks**: System component status
- **Performance Monitoring**: Response time tracking
- **Error Logging**: Comprehensive error tracking
- **Cache Management**: Multi-level cache control

### 🧹 **Maintenance Features**
- **Cache Clearing**: System-wide cache management
- **File Cleanup**: Temporary file removal
- **Data Archiving**: Old data management
- **Database Optimization**: Performance maintenance

---

## User Experience Features

### ⚡ **Performance Optimizations**
- **Lazy Loading**: Efficient data loading
- **AJAX Operations**: Smooth user interactions
- **Caching**: Multi-layer caching system
- **Bulk Operations**: Efficient mass actions

### 🎨 **Interface Features**
- **Drag & Drop**: Reordering functionality
- **Quick Actions**: Fast status toggles
- **Bulk Selection**: Mass item management
- **Advanced Filtering**: Complex data filtering
- **Search**: Global search functionality

---

*Generated: 2025-08-23*  
*Type: Admin Area Sitemap (Administrator Access)*  
*Total Routes: ~300+ routes across 20+ modules*  
*Version: 1.0*