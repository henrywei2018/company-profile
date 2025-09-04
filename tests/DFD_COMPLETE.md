# Data Flow Diagram (DFD) - Company Profile Application

## 📋 **DFD Overview**

### **Symbols Legend:**
- **External Entity** = Square □
- **Process** = Circle ○  
- **Data Store** = Open Rectangle D1, D2, etc.
- **Data Flow** = Arrow →

---

## 🔍 **Level 0 - Context Diagram**

```
┌──────────────┐
│   Visitor    │ ──── browse_info ────→ ┌─────────────────────┐
│   (Guest)    │ ←─── website_content ── │                     │
└──────────────┘                        │                     │
                                        │                     │
┌──────────────┐                        │                     │
│    Client    │ ──── login_credentials → │                     │
│ (Registered  │ ←─── dashboard_data ──── │  Company Profile    │
│    User)     │ ──── order_data ──────→ │     System          │
│              │ ←─── order_status ────── │                     │
│              │ ──── message_data ─────→ │     (Process 0)     │
│              │ ←─── notifications ───── │                     │
└──────────────┘                        │                     │
                                        │                     │
┌──────────────┐                        │                     │
│ Administrator│ ──── admin_commands ──→ │                     │
│   (Admin)    │ ←─── system_reports ──── │                     │
│              │ ──── content_updates ──→ │                     │
│              │ ←─── analytics_data ──── │                     │
└──────────────┘                        └─────────────────────┘
                                                    │
                                                    │
┌──────────────┐                                   │
│ Google       │ ←─── analytics_requests ─────────────┘
│ Analytics    │ ──── analytics_response ────────────→
│   (GA4)      │
└──────────────┘

┌──────────────┐
│ Email        │ ←─── email_notifications ────────────┘
│ System       │ ──── delivery_status ────────────→
│ (SMTP)       │
└──────────────┘

┌──────────────┐
│ File         │ ←─── file_operations ─────────────────┘
│ Storage      │ ──── stored_files ────────────────→
│ System       │
└──────────────┘
```

---

## 🔄 **Level 1 - Main Processes DFD**

```
┌──────────────┐
│   Visitor    │
│   (Guest)    │
└──────┬───────┘
       │ browse_request
       ▼
┌─────────────────┐    website_content    D1│ Users        │
│ 1.0 Website     │◄─────────────────────────│              │
│ Content         │                          D2│ Posts        │
│ Management      │◄─────────────────────────│              │
└─────────────────┘                          D3│ Services     │
       │                                     │              │
       │ quotation_request                   D4│ Projects     │
       ▼                                     │              │
┌─────────────────┐                          D5│ Products     │
│ 2.0 Quotation   │────────────────────────→│              │
│ Processing      │◄────────────────────────D6│ Quotations   │
└─────────────────┘                          │              │
       │                                     │              │
       │ registration_data                   │              │
       ▼                                     │              │
┌─────────────────┐    user_data             │              │
│ 3.0 User        │────────────────────────→│              │
│ Authentication  │◄────────────────────────│              │
└─────────────────┘                          └──────────────┘
       │ authenticated_user
       ▼
┌──────────────┐
│    Client    │
│ (Registered  │
│    User)     │
└──────┬───────┘
       │ order_request
       ▼
┌─────────────────┐    order_data           D7│ Orders       │
│ 4.0 Order       │────────────────────────→│              │
│ Management      │◄────────────────────────D8│ Cart Items   │
└─────────────────┘                          │              │
       │                                     D9│ Payments     │
       │ message_request                     │              │
       ▼                                     │              │
┌─────────────────┐    message_data          D10│Messages     │
│ 5.0 Communication│───────────────────────→│              │
│ System          │◄───────────────────────D11│Chat Sessions│
└─────────────────┘                          │              │
       │                                     D12│Testimonials │
       │ project_updates                     │              │
       ▼                                     │              │
┌─────────────────┐    project_data          D13│Milestones   │
│ 6.0 Project     │────────────────────────→│              │
│ Management      │◄────────────────────────D14│Project Files│
└─────────────────┘                          │              │
       │                                     │              │
       │ admin_access                        │              │
       ▼                                     │              │
┌──────────────┐                             │              │
│ Administrator│                             │              │
│   (Admin)    │                             │              │
└──────┬───────┘                             │              │
       │ admin_commands                      │              │
       ▼                                     │              │
┌─────────────────┐    content_data          D15│Settings     │
│ 7.0 Content     │────────────────────────→│              │
│ Management      │◄────────────────────────D16│Banners      │
└─────────────────┘                          │              │
       │                                     D17│Team Members │
       │ user_management                     │              │
       ▼                                     D18│Roles        │
┌─────────────────┐    user_admin_data       │              │
│ 8.0 User        │────────────────────────→D19│Permissions  │
│ Management      │◄────────────────────────│              │
└─────────────────┘                          │              │
       │                                     D20│Notifications│
       │ analytics_request                   │              │
       ▼                                     │              │
┌─────────────────┐                          │              │
│ 9.0 Analytics   │◄────────────────────────│              │
│ & Reporting     │────────────────────────→│              │
└─────────────────┘                          └──────────────┘
       │ ▲
       │ │ analytics_data
       ▼ │
┌──────────────┐
│ Google       │
│ Analytics    │
│   (GA4)      │
└──────────────┘
```

---

## 🔍 **Level 2 - Process Decomposition**

### **2.1 Process 4.0 - Order Management (Detailed)**

```
┌──────────────┐
│    Client    │
└──────┬───────┘
       │ product_selection
       ▼
┌─────────────────┐    cart_data            D8│ Cart Items   │
│ 4.1 Shopping    │────────────────────────→│              │
│ Cart Management │◄────────────────────────│              │
└─────────────────┘                          └──────────────┘
       │ checkout_request
       ▼
┌─────────────────┐    order_details         D7│ Orders       │
│ 4.2 Order       │────────────────────────→│              │
│ Processing      │◄────────────────────────│              │
└─────────────────┘                          └──────────────┘
       │ payment_info
       ▼
┌─────────────────┐    payment_data          D9│ Payments     │
│ 4.3 Payment     │────────────────────────→│              │
│ Processing      │◄────────────────────────│              │
└─────────────────┘                          └──────────────┘
       │ negotiation_request
       ▼
┌─────────────────┐    negotiation_data      D7│ Orders       │
│ 4.4 Negotiation │────────────────────────→│  (nego_data)  │
│ Handling        │◄────────────────────────│              │
└─────────────────┘                          └──────────────┘
       │ order_completion
       ▼
┌─────────────────┐    delivery_info         D7│ Orders       │
│ 4.5 Order       │────────────────────────→│  (status)     │
│ Fulfillment     │◄────────────────────────│              │
└─────────────────┘                          └──────────────┘
```

### **2.2 Process 5.0 - Communication System (Detailed)**

```
┌──────────────┐
│    Client    │
└──────┬───────┘
       │ message_input
       ▼
┌─────────────────┐    message_threads       D10│Messages     │
│ 5.1 Message     │────────────────────────→│              │
│ Management      │◄────────────────────────│              │
└─────────────────┘                          └──────────────┘
       │ chat_request
       ▼
┌─────────────────┐    chat_data             D11│Chat Sessions│
│ 5.2 Live Chat   │────────────────────────→│              │
│ System          │◄────────────────────────D31│Chat Messages│
└─────────────────┘                          │              │
       │                                     D32│Chat Template│
       │ notification_trigger                │              │
       ▼                                     │              │
┌─────────────────┐    notification_data     D20│Notifications│
│ 5.3 Notification│───────────────────────→│              │
│ System          │◄────────────────────────│              │
└─────────────────┘                          └──────────────┘
       │ email_notification
       ▼
┌──────────────┐
│ Email System │
│   (SMTP)     │
└──────────────┘
```

### **2.3 Process 6.0 - Project Management (Detailed)**

```
┌──────────────┐
│    Admin     │
└──────┬───────┘
       │ project_creation
       ▼
┌─────────────────┐    project_info          D4│ Projects     │
│ 6.1 Project     │────────────────────────→│              │
│ Setup           │◄────────────────────────│              │
└─────────────────┘                          └──────────────┘
       │ milestone_planning
       ▼
┌─────────────────┐    milestone_data        D13│Milestones   │
│ 6.2 Milestone   │────────────────────────→│              │
│ Management      │◄────────────────────────│              │
└─────────────────┘                          └──────────────┘
       │ file_operations
       ▼
┌─────────────────┐    file_metadata         D14│Project Files│
│ 6.3 File        │────────────────────────→│              │
│ Management      │◄────────────────────────│              │
└─────────────────┘                          └──────────────┘
       │ progress_update
       ▼
┌─────────────────┐    progress_data         D4│ Projects     │
│ 6.4 Progress    │────────────────────────→│  (status)     │
│ Tracking        │◄────────────────────────│              │
└─────────────────┘                          └──────────────┘
       │ completion_request
       ▼
┌─────────────────┐    testimonial_request   D12│Testimonials │
│ 6.5 Project     │────────────────────────→│              │
│ Completion      │◄────────────────────────│              │
└─────────────────┘                          └──────────────┘
```

---

## 📊 **Data Stores Dictionary**

| ID | Data Store | Description | Key Data Elements |
|----|------------|-------------|-------------------|
| D1 | Users | User accounts and profiles | user_id, name, email, role, status |
| D2 | Posts | Blog posts and articles | post_id, title, content, category, status |
| D3 | Services | Company services catalog | service_id, name, description, price |
| D4 | Projects | Project portfolio | project_id, title, status, client_id |
| D5 | Products | Product catalog | product_id, name, price, category |
| D6 | Quotations | Client quotation requests | quotation_id, client_id, status, items |
| D7 | Orders | Product orders | order_id, client_id, status, items |
| D8 | Cart Items | Shopping cart data | cart_id, product_id, quantity |
| D9 | Payments | Payment information | payment_id, order_id, method, status |
| D10 | Messages | Communication threads | message_id, sender, recipient, content |
| D11 | Chat Sessions | Live chat sessions | session_id, client_id, operator_id |
| D12 | Testimonials | Client testimonials | testimonial_id, client_id, project_id |
| D13 | Milestones | Project milestones | milestone_id, project_id, status |
| D14 | Project Files | Project documents | file_id, project_id, path, type |
| D15 | Settings | System configuration | setting_key, setting_value |
| D16 | Banners | Marketing banners | banner_id, category, image, status |
| D17 | Team Members | Team information | member_id, name, department, role |
| D18 | Roles | User roles | role_id, name, permissions |
| D19 | Permissions | Access permissions | permission_id, name, resource |
| D20 | Notifications | System notifications | notification_id, user_id, type, data |

### **Additional Data Stores:**
| ID | Data Store | Description | Key Data Elements |
|----|------------|-------------|-------------------|
| D21 | Product Categories | Product categorization | category_id, name, parent_id |
| D22 | Service Categories | Service categorization | category_id, name, description |
| D23 | Project Categories | Project categorization | category_id, name, slug |
| D24 | Post Categories | Blog categorization | category_id, name, slug |
| D25 | Product Images | Product image gallery | image_id, product_id, path |
| D26 | Project Images | Project image gallery | image_id, project_id, path |
| D27 | Message Attachments | Message file attachments | attachment_id, message_id, path |
| D28 | Quotation Attachments | Quote file attachments | attachment_id, quotation_id, path |
| D29 | Banner Categories | Banner categorization | category_id, name, slug |
| D30 | Team Departments | Team department structure | department_id, name, description |
| D31 | Chat Messages | Chat message content | message_id, session_id, content |
| D32 | Chat Templates | Pre-defined chat responses | template_id, title, content |
| D33 | Certifications | Company certifications | cert_id, name, image, date |
| D34 | Company Profile | Company information | profile_id, field, value |
| D35 | SEO Settings | SEO configuration | seo_id, page, meta_title, meta_desc |
| D36 | Payment Methods | Available payment methods | method_id, name, details, status |
| D37 | Order Items | Individual order items | item_id, order_id, product_id, qty |
| D38 | Product Order | Product order details | order_id, status, negotiation_data |

---

## 🔄 **Major Data Flows**

### **1. Guest User Flow:**
```
Visitor → browse_request → Website Content → website_data → Visitor
Visitor → quotation_request → Quotation Processing → quotation_data → D6
```

### **2. Client Registration Flow:**
```
Visitor → registration_data → User Authentication → user_data → D1
User Authentication → otp_verification → Email System → verification_status
```

### **3. E-commerce Flow:**
```
Client → product_selection → Shopping Cart → cart_data → D8
Client → checkout_request → Order Processing → order_data → D7
Client → payment_info → Payment Processing → payment_data → D9
```

### **4. Communication Flow:**
```
Client → message_input → Message Management → message_data → D10
Client → chat_request → Live Chat System → chat_data → D11
System → notification_trigger → Notification System → notification_data → D20
```

### **5. Project Management Flow:**
```
Admin → project_creation → Project Setup → project_data → D4
Admin → milestone_planning → Milestone Management → milestone_data → D13
Admin → file_operations → File Management → file_data → D14
```

### **6. Content Management Flow:**
```
Admin → content_updates → Content Management → content_data → D2,D3,D4,D5
Admin → user_management → User Management → user_admin_data → D1,D18,D19
```

### **7. Analytics Flow:**
```
System → analytics_request → Google Analytics → analytics_data → Analytics & Reporting
Analytics & Reporting → system_reports → Admin
```

---

## 🔍 **Process Specifications**

### **Process 1.0 - Website Content Management**
- **Input**: browse_request
- **Output**: website_content
- **Function**: Serve public content (pages, posts, services, projects, products)
- **Data Stores**: D1-D5, D16, D17

### **Process 2.0 - Quotation Processing**
- **Input**: quotation_request, quotation_data
- **Output**: quotation_confirmation
- **Function**: Process client quotation requests and admin responses
- **Data Stores**: D6, D28

### **Process 3.0 - User Authentication**
- **Input**: login_credentials, registration_data
- **Output**: authenticated_user, authentication_status
- **Function**: Handle user login, registration, and OTP verification
- **Data Stores**: D1, D18, D19

### **Process 4.0 - Order Management**
- **Input**: order_request, payment_info
- **Output**: order_status, order_confirmation
- **Function**: Process e-commerce orders, payments, and negotiations
- **Data Stores**: D5, D7, D8, D9, D36, D37, D38

### **Process 5.0 - Communication System**
- **Input**: message_request, chat_request
- **Output**: message_response, chat_response
- **Function**: Handle messages, live chat, and notifications
- **Data Stores**: D10, D11, D20, D27, D31, D32

### **Process 6.0 - Project Management**
- **Input**: project_updates, milestone_data
- **Output**: project_status, progress_reports
- **Function**: Manage projects, milestones, and file deliverables
- **Data Stores**: D4, D12, D13, D14, D23, D26

### **Process 7.0 - Content Management**
- **Input**: content_updates
- **Output**: updated_content
- **Function**: Admin management of all content types
- **Data Stores**: D2-D5, D16, D17, D21-D26, D29, D30, D33-D35

### **Process 8.0 - User Management**
- **Input**: admin_commands
- **Output**: user_admin_data
- **Function**: Admin user, role, and permission management
- **Data Stores**: D1, D18, D19

### **Process 9.0 - Analytics & Reporting**
- **Input**: analytics_request, system_data
- **Output**: analytics_reports, system_reports
- **Function**: Generate business intelligence and system reports
- **External**: Google Analytics GA4 API

---

## 🎯 **Key Data Flow Characteristics**

### **📊 Data Volume & Frequency:**
- **High Volume**: Orders, Messages, Chat Sessions, Analytics
- **Medium Volume**: Projects, Users, Products, Notifications
- **Low Volume**: Settings, Roles, Permissions, Company Profile

### **🔄 Data Flow Patterns:**
- **Real-time**: Chat Messages, Notifications, Analytics
- **Batch**: Email Notifications, Reports, File Processing
- **On-demand**: Content Retrieval, User Authentication

### **🔒 Security Considerations:**
- **Encrypted Flows**: Authentication data, Payment information
- **Logged Flows**: Admin actions, User management, System changes
- **Public Flows**: Website content, Public pages

### **🎨 Integration Points:**
- **Google Analytics**: Bi-directional analytics data exchange
- **Email System**: Outbound notifications and confirmations
- **File Storage**: File upload/download operations
- **Cache System**: Performance optimization layer

---

*Generated: 2025-08-23*  
*Type: Complete Data Flow Diagram Set*  
*Levels: Context (0), Overview (1), Detailed (2)*  
*Data Stores: 38+ identified*  
*Processes: 9 main + 15 sub-processes*  
*Version: 1.0*