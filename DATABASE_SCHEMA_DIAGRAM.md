# Database Schema Visualization
**Company Profile Application - Database Design Documentation**

---

## 🗄️ **Entity Relationship Overview**

```
                            COMPANY PROFILE DATABASE SCHEMA
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│                                   USER MANAGEMENT                                      │
├─────────────────────────────────────────────────────────────────────────────────────────┤
│                                                                                         │
│  ┌──────────────┐     ┌──────────────┐     ┌──────────────┐     ┌──────────────────┐   │
│  │    USERS     │────▶│    ROLES     │◄────│ PERMISSIONS  │     │   OTP_TOKENS     │   │
│  ├──────────────┤     ├──────────────┤     ├──────────────┤     ├──────────────────┤   │
│  │ PK: id       │     │ PK: id       │     │ PK: id       │     │ PK: id           │   │
│  │    name      │     │    name      │     │    name      │     │ FK: user_id      │   │
│  │    email     │     │    slug      │     │    slug      │     │    token         │   │
│  │    phone     │     │    color     │     │    guard_name│     │    type          │   │
│  │    password  │     └──────────────┘     └──────────────┘     │    expires_at    │   │
│  │    role      │              │                     │          └──────────────────┘   │
│  │    avatar    │              └─────────────────────┘                               │
│  │    status    │                        M:N                                         │
│  └──────────────┘                                                                     │
│         │                                                                             │
│         │ 1:M                                                                         │
│         ▼                                                                             │
│  ┌──────────────┐     ┌──────────────┐                                               │
│  │USER_PROFILES │     │   USER_META  │                                               │
│  ├──────────────┤     ├──────────────┤                                               │
│  │ PK: id       │     │ PK: id       │                                               │
│  │ FK: user_id  │     │ FK: user_id  │                                               │
│  │    bio       │     │    key       │                                               │
│  │    company   │     │    value     │                                               │
│  │    address   │     │    type      │                                               │
│  │    website   │     └──────────────┘                                               │
│  └──────────────┘                                                                     │
└─────────────────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────────────────────┐
│                                   CONTENT MANAGEMENT                                   │
├─────────────────────────────────────────────────────────────────────────────────────────┤
│                                                                                         │
│  ┌──────────────┐     ┌──────────────┐     ┌──────────────┐     ┌──────────────────┐   │
│  │   SERVICES   │     │   PRODUCTS   │     │  CATEGORIES  │     │    BLOG_POSTS    │   │
│  ├──────────────┤     ├──────────────┤     ├──────────────┤     ├──────────────────┤   │
│  │ PK: id       │     │ PK: id       │     │ PK: id       │     │ PK: id           │   │
│  │    name      │     │    name      │     │    name      │     │    title         │   │
│  │    slug      │     │    slug      │     │    slug      │     │    slug          │   │
│  │ FK: category │     │    price     │     │    parent_id │     │ FK: author_id    │   │
│  │    price     │     │ FK: category │     │    image     │     │ FK: category_id  │   │
│  │    features  │     │    images    │     │    sort      │     │    content       │   │
│  │    status    │     │    status    │     │    status    │     │    excerpt       │   │
│  └──────────────┘     └──────────────┘     └──────────────┘     │    status        │   │
│         │                       │                   │           │    published_at  │   │
│         └───────────────────────┼───────────────────┘           └──────────────────┘   │
│                                 │                                        │              │
│                                 │ M:N                                    │ M:N          │
│                                 ▼                                        ▼              │
│  ┌──────────────────────────────────────────────────────────────────────────────────┐   │
│  │                        PRODUCT_CATEGORIES                                        │   │
│  │                             BLOG_TAGS                                           │   │
│  └──────────────────────────────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────────────────────┐
│                                   ORDER MANAGEMENT                                     │
├─────────────────────────────────────────────────────────────────────────────────────────┤
│                                                                                         │
│  ┌──────────────────┐     ┌─────────────────────┐     ┌──────────────────────────────┐ │
│  │ PRODUCT_ORDERS   │────▶│ PRODUCT_ORDER_ITEMS │     │         QUOTATIONS          │ │
│  ├──────────────────┤     ├─────────────────────┤     ├──────────────────────────────┤ │
│  │ PK: id           │     │ PK: id              │     │ PK: id                       │ │
│  │    order_number  │     │ FK: order_id        │     │    quote_number              │ │
│  │ FK: client_id    │     │ FK: product_id      │     │ FK: client_id                │ │
│  │    status        │     │    quantity         │     │ FK: service_id               │ │
│  │    payment_stat  │     │    price            │     │    requirements              │ │
│  │    total_amount  │     │    subtotal         │     │    budget_range              │ │
│  │    notes         │     │    notes            │     │    timeline                  │ │
│  │    created_at    │     └─────────────────────┘     │    status                    │ │
│  └──────────────────┘              │                  │    valid_until               │ │
│         │                          │                  └──────────────────────────────┘ │
│         │ 1:M                      │ M:1                           │                    │
│         ▼                          ▼                               │ 1:1                │
│  ┌──────────────────┐     ┌─────────────────────┐                 ▼                    │
│  │    PAYMENTS      │     │     NEGOTIATIONS    │     ┌──────────────────────────────┐ │
│  ├──────────────────┤     ├─────────────────────┤     │          PROJECTS            │ │
│  │ PK: id           │     │ PK: id              │     ├──────────────────────────────┤ │
│  │ FK: order_id     │     │ FK: order_id        │     │ PK: id                       │ │
│  │    amount        │     │    message          │     │ FK: quotation_id             │ │
│  │    method        │     │    requested_total  │     │ FK: client_id                │ │
│  │    status        │     │    status           │     │    title                     │ │
│  │    proof_file    │     │    responded_at     │     │    status                    │ │
│  │    verified_at   │     └─────────────────────┘     │    progress_percentage       │ │
│  └──────────────────┘                                 │    start_date                │ │
│                                                        │    end_date                  │ │
│                                                        └──────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────────────────────┐
│                               PROJECT & PORTFOLIO                                      │
├─────────────────────────────────────────────────────────────────────────────────────────┤
│                                                                                         │
│  ┌──────────────────┐     ┌─────────────────────┐     ┌──────────────────────────────┐ │
│  │    PROJECTS      │────▶│   PROJECT_IMAGES    │     │       PROJECT_FILES          │ │
│  ├──────────────────┤     ├─────────────────────┤     ├──────────────────────────────┤ │
│  │ PK: id           │     │ PK: id              │     │ PK: id                       │ │
│  │    title         │     │ FK: project_id      │     │ FK: project_id               │ │
│  │    slug          │     │    image_path       │     │    file_name                 │ │
│  │    description   │     │    alt_text         │     │    file_path                 │ │
│  │ FK: client_id    │     │    is_featured      │     │    file_type                 │ │
│  │ FK: category_id  │     │    sort_order       │     │    file_size                 │ │
│  │    status        │     └─────────────────────┘     │    uploaded_at               │ │
│  │    featured      │              │                  └──────────────────────────────┘ │
│  │    year          │              │ 1:M                           │                    │
│  │    budget        │              ▼                               │ 1:M                │
│  │    progress      │     ┌─────────────────────┐                 ▼                    │
│  └──────────────────┘     │ PROJECT_MILESTONES  │     ┌──────────────────────────────┐ │
│         │                 ├─────────────────────┤     │      PROJECT_UPDATES         │ │
│         │ 1:M             │ PK: id              │     ├──────────────────────────────┤ │
│         ▼                 │ FK: project_id      │     │ PK: id                       │ │
│  ┌──────────────────┐     │    title            │     │ FK: project_id               │ │
│  │  TESTIMONIALS    │     │    description      │     │    title                     │ │
│  ├──────────────────┤     │    due_date         │     │    content                   │ │
│  │ PK: id           │     │    completed_at     │     │    progress_percentage       │ │
│  │ FK: project_id   │     │    status           │     │    created_at                │ │
│  │ FK: client_id    │     └─────────────────────┘     └──────────────────────────────┘ │
│  │    content       │                                                                  │
│  │    rating        │                                                                  │
│  │    featured      │                                                                  │
│  │    status        │                                                                  │
│  └──────────────────┘                                                                  │
└─────────────────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────────────────────┐
│                               COMMUNICATION SYSTEM                                     │
├─────────────────────────────────────────────────────────────────────────────────────────┤
│                                                                                         │
│  ┌──────────────────┐     ┌─────────────────────┐     ┌──────────────────────────────┐ │
│  │    MESSAGES      │────▶│   MESSAGE_FILES     │     │      NOTIFICATIONS           │ │
│  ├──────────────────┤     ├─────────────────────┤     ├──────────────────────────────┤ │
│  │ PK: id           │     │ PK: id              │     │ PK: id                       │ │
│  │ FK: sender_id    │     │ FK: message_id      │     │ FK: user_id                  │ │
│  │ FK: recipient_id │     │    file_name        │     │    title                     │ │
│  │ FK: order_id     │     │    file_path        │     │    message                   │ │
│  │ FK: project_id   │     │    file_type        │     │    type                      │ │
│  │    subject       │     │    file_size        │     │    data                      │ │
│  │    body          │     └─────────────────────┘     │    read_at                   │ │
│  │    priority      │              │                  │    created_at                │ │
│  │    is_read       │              │ 1:M              └──────────────────────────────┘ │
│  │    replied_at    │              ▼                               │                    │
│  │    created_at    │     ┌─────────────────────┐                 │ 1:M                │
│  └──────────────────┘     │  MESSAGE_THREADS    │                 ▼                    │
│         │                 ├─────────────────────┤     ┌──────────────────────────────┐ │
│         │ 1:M             │ PK: id              │     │       ACTIVITY_LOGS          │ │
│         ▼                 │ FK: parent_id       │     ├──────────────────────────────┤ │
│  ┌──────────────────┐     │ FK: message_id      │     │ PK: id                       │ │
│  │  CONVERSATIONS   │     │    thread_title     │     │ FK: user_id                  │ │
│  ├──────────────────┤     │    participants     │     │    action                    │ │
│  │ PK: id           │     └─────────────────────┘     │    description               │ │
│  │ FK: user1_id     │                                 │    ip_address                │ │
│  │ FK: user2_id     │                                 │    user_agent                │ │
│  │    title         │                                 │    created_at                │ │
│  │    status        │                                 └──────────────────────────────┘ │
│  │    created_at    │                                                                  │
│  └──────────────────┘                                                                  │
└─────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 🔗 **Key Relationships Summary**

### **One-to-Many (1:M) Relationships**
```
┌─────────────────┬─────────────────────────────────────────┐
│ PARENT          │ CHILDREN                                │
├─────────────────┼─────────────────────────────────────────┤
│ Users           │ → Orders, Projects, Messages, Tokens   │
│ Orders          │ → Order Items, Payments, Negotiations  │
│ Projects        │ → Images, Files, Milestones, Updates   │
│ Messages        │ → Message Files, Threads               │
│ Categories      │ → Products, Services, Projects, Posts  │
│ Services        │ → Quotations, Projects                 │
│ Quotations      │ → Project (one-to-one)                 │
└─────────────────┴─────────────────────────────────────────┘
```

### **Many-to-Many (M:N) Relationships**
```
┌─────────────────┬─────────────────────────────────────────┐
│ ENTITY 1        │ ENTITY 2                    │ PIVOT     │
├─────────────────┼─────────────────────────────────────────┤
│ Users           │ Roles                       │ user_roles│
│ Roles           │ Permissions                 │ role_perms│
│ Products        │ Categories                  │ pivot     │
│ Blog Posts      │ Tags                        │ post_tags │
│ Projects        │ Technologies                │ proj_tech │
└─────────────────┴─────────────────────────────────────────┘
```

---

## 📊 **Database Normalization Strategy**

### **Third Normal Form (3NF) Implementation**

```
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│                              NORMALIZATION EXAMPLES                                    │
├─────────────────────────────────────────────────────────────────────────────────────────┤
│                                                                                         │
│  ❌ DENORMALIZED (Before)                  ✅ NORMALIZED (After)                       │
│  ┌──────────────────────────────┐         ┌──────────────────────────────────────────┐ │
│  │        ORDERS                │         │        ORDERS                            │ │
│  ├──────────────────────────────┤         ├──────────────────────────────────────────┤ │
│  │ id                           │         │ id                                       │ │
│  │ client_name                  │ ───────▶│ client_id (FK to users)                  │ │
│  │ client_email                 │         │ order_number                             │ │
│  │ client_phone                 │         │ status                                   │ │
│  │ product1_name                │         │ total_amount                             │ │
│  │ product1_price               │         └──────────────────────────────────────────┘ │
│  │ product1_qty                 │                          │                          │ │
│  │ product2_name                │                          │ 1:M                     │ │
│  │ product2_price               │                          ▼                          │ │
│  │ product2_qty                 │         ┌──────────────────────────────────────────┐ │
│  │ ...                          │         │        ORDER_ITEMS                       │ │
│  └──────────────────────────────┘         ├──────────────────────────────────────────┤ │
│                                           │ id                                       │ │
│                                           │ order_id (FK)                            │ │
│                                           │ product_id (FK)                          │ │
│                                           │ quantity                                 │ │
│                                           │ price                                    │ │
│                                           │ subtotal                                 │ │
│                                           └──────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## ⚡ **Indexing Strategy**

### **Primary Indexes**
```sql
-- High-traffic query optimization
CREATE INDEX idx_orders_client_status ON product_orders(client_id, status);
CREATE INDEX idx_orders_date_status ON product_orders(created_at, status);
CREATE INDEX idx_messages_recipient_read ON messages(recipient_id, is_read);
CREATE INDEX idx_projects_client_status ON projects(client_id, status);

-- Search functionality
CREATE FULLTEXT INDEX idx_products_search ON products(name, description);
CREATE FULLTEXT INDEX idx_services_search ON services(name, description);
CREATE FULLTEXT INDEX idx_blog_search ON blog_posts(title, content, excerpt);

-- Performance critical paths
CREATE INDEX idx_users_email_status ON users(email, status);
CREATE INDEX idx_files_entity ON project_files(project_id, file_type);
CREATE INDEX idx_payments_order_status ON payments(order_id, status);
```

### **Composite Indexes for Complex Queries**
```sql
-- Admin dashboard analytics
CREATE INDEX idx_orders_analytics ON product_orders(status, payment_status, created_at);

-- Client dashboard filtering
CREATE INDEX idx_client_orders ON product_orders(client_id, status, created_at);

-- Message system performance
CREATE INDEX idx_messages_conversation ON messages(sender_id, recipient_id, created_at);

-- Project portfolio queries
CREATE INDEX idx_projects_portfolio ON projects(status, featured, is_active, year);
```

---

## 🔒 **Data Integrity Constraints**

### **Foreign Key Relationships**
```sql
-- User relationships
ALTER TABLE product_orders ADD FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE;
ALTER TABLE projects ADD FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE;
ALTER TABLE messages ADD FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE;

-- Order relationships  
ALTER TABLE product_order_items ADD FOREIGN KEY (order_id) REFERENCES product_orders(id) ON DELETE CASCADE;
ALTER TABLE payments ADD FOREIGN KEY (order_id) REFERENCES product_orders(id) ON DELETE CASCADE;

-- Project relationships
ALTER TABLE project_images ADD FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE;
ALTER TABLE project_files ADD FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE;

-- Content relationships
ALTER TABLE blog_posts ADD FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL;
```

### **Business Rules Constraints**
```sql
-- Status validation
ALTER TABLE product_orders ADD CONSTRAINT chk_order_status 
CHECK (status IN ('pending', 'confirmed', 'processing', 'ready', 'delivered', 'completed'));

-- Payment validation
ALTER TABLE payments ADD CONSTRAINT chk_payment_amount CHECK (amount > 0);

-- Project progress validation
ALTER TABLE projects ADD CONSTRAINT chk_progress_percentage 
CHECK (progress_percentage >= 0 AND progress_percentage <= 100);

-- Rating validation
ALTER TABLE testimonials ADD CONSTRAINT chk_rating_range 
CHECK (rating >= 1 AND rating <= 5);
```

---

## 📈 **Performance Optimization**

### **Query Performance Strategies**
```
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│                              QUERY OPTIMIZATION                                        │
├─────────────────────────────────────────────────────────────────────────────────────────┤
│                                                                                         │
│  1. EAGER LOADING (N+1 Problem Solution)                                               │
│     • Orders with Items: $orders = Order::with('items')->get()                         │
│     • Projects with Images: $projects = Project::with('images')->get()                 │
│     • Messages with Files: $messages = Message::with('files')->get()                   │
│                                                                                         │
│  2. SELECTIVE COLUMN LOADING                                                            │
│     • Order List: Order::select('id', 'order_number', 'status', 'total')               │
│     • Product Catalog: Product::select('id', 'name', 'price', 'image')                 │
│                                                                                         │
│  3. PAGINATION STRATEGY                                                                 │
│     • Large datasets: paginate(20) instead of get()                                    │
│     • Cursor pagination for real-time data                                             │
│                                                                                         │
│  4. CACHING LAYERS                                                                      │
│     • Query Results: Cache::remember('products', 3600, $callback)                      │
│     • Expensive Calculations: Cache computed totals and counts                         │
│     • Static Data: Long-term caching for categories, services                          │
└─────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 🔐 **Security Implementation**

### **Data Protection Measures**
```
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│                                DATA SECURITY                                           │
├─────────────────────────────────────────────────────────────────────────────────────────┤
│                                                                                         │
│  🔐 ENCRYPTION AT REST                                                                  │
│     • Password Hashing: bcrypt with cost factor 12                                     │
│     • Sensitive Data: AES-256 encryption for payment info                              │
│     • File Storage: Encrypted storage for uploaded documents                           │
│                                                                                         │
│  🛡️ INPUT VALIDATION                                                                   │
│     • Laravel Form Requests: Comprehensive validation rules                            │
│     • SQL Injection Prevention: Eloquent ORM parameter binding                         │
│     • XSS Prevention: Blade templating auto-escaping                                   │
│                                                                                         │
│  🔒 ACCESS CONTROL                                                                      │
│     • Role-Based Permissions: Spatie Permission package                                │
│     • Row-Level Security: Owner-based access restrictions                              │
│     • API Authentication: Laravel Sanctum token-based auth                             │
│                                                                                         │
│  📝 AUDIT LOGGING                                                                       │
│     • Activity Logs: Track all CRUD operations                                         │
│     • Login Attempts: Monitor authentication events                                    │
│     • Data Changes: Log critical data modifications                                     │
└─────────────────────────────────────────────────────────────────────────────────────────┘
```

---

**📝 This diagram supports LAPORAN_PROGRESS_1.md Phase 1 Database Design documentation**  
**🎯 Status**: Database Schema Complete - Normalized & Optimized for Implementation