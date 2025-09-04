# Entity Relationship Diagram (ERD) - Company Profile Application

## 📋 **ERD Overview**

### **Notation Legend:**
- **Primary Key** = PK (underlined)
- **Foreign Key** = FK (italic)
- **Unique** = U
- **Not Null** = NN
- **Auto Increment** = AI

### **Relationship Notation:**
- **1:1** = One to One
- **1:M** = One to Many  
- **M:N** = Many to Many (with junction table)

---

## 🗃️ **Complete ERD Structure**

```
┌─────────────────────────────────────────────────────────────────────────────────────┐
│                          COMPANY PROFILE APPLICATION ERD                            │
│                                                                                     │
│  ┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐                 │
│  │      USERS      │    │      ROLES      │    │   PERMISSIONS   │                 │
│  ├─────────────────┤    ├─────────────────┤    ├─────────────────┤                 │
│  │ PK id          │    │ PK id          │    │ PK id          │                 │
│  │    name        │    │    name        │    │    name        │                 │
│  │ U  email       │    │    guard_name  │    │    guard_name  │                 │
│  │    password    │    │    created_at  │    │    created_at  │                 │
│  │    phone       │    │    updated_at  │    │    updated_at  │                 │
│  │    company     │    └─────────────────┘    └─────────────────┘                 │
│  │    address     │           │                        │                          │
│  │    city        │           │ M:N                    │ M:N                      │
│  │    state       │           ▼                        ▼                          │
│  │    postal_code │    ┌─────────────────┐    ┌─────────────────┐                 │
│  │    country     │    │  MODEL_HAS_ROLES│    │MODEL_HAS_PERMS │                 │
│  │    avatar      │    ├─────────────────┤    ├─────────────────┤                 │
│  │    is_active   │    │ FK role_id     │    │ FK permission_id│                 │
│  │    settings    │    │ FK model_id    │    │ FK model_id    │                 │
│  │    created_at  │    │    model_type  │    │    model_type  │                 │
│  │    updated_at  │    └─────────────────┘    └─────────────────┘                 │
│  │    last_login  │                                                               │
│  │    otp_code    │    ┌─────────────────┐    ┌─────────────────┐                 │
│  │    otp_expires │    │ ROLE_HAS_PERMS │    │                 │                 │
│  └─────────────────┘    ├─────────────────┤    │                 │                 │
│           │             │ FK permission_id│    │                 │                 │
│           │ 1:M         │ FK role_id     │    │                 │                 │
│           ▼             └─────────────────┘    │                 │                 │
│  ┌─────────────────┐                          │                 │                 │
│  │    PROJECTS     │◄─────────────────────────┘                 │                 │
│  ├─────────────────┤                                           │                 │
│  │ PK id          │                                           │                 │
│  │    title       │    ┌─────────────────┐                   │                 │
│  │    slug        │    │ PROJECT_CATEGORIES│                   │                 │
│  │    description │    ├─────────────────┤                   │                 │
│  │ FK client_id   │    │ PK id          │                   │                 │
│  │ FK quotation_id│    │    name        │                   │                 │
│  │ FK category_id │◄───┤    slug        │                   │                 │
│  │    status      │    │    description │                   │                 │
│  │    start_date  │    │    is_active   │                   │                 │
│  │    end_date    │    │    created_at  │                   │                 │
│  │    budget      │    │    updated_at  │                   │                 │
│  │    is_featured │    └─────────────────┘                   │                 │
│  │    created_at  │                                           │                 │
│  │    updated_at  │    ┌─────────────────┐                   │                 │
│  └─────────────────┘    │ PROJECT_IMAGES  │                   │                 │
│           │             ├─────────────────┤                   │                 │
│           │ 1:M         │ PK id          │                   │                 │
│           ▼             │ FK project_id  │◄──────────────────┘                 │
│  ┌─────────────────┐    │    image_path  │                                     │
│  │PROJECT_MILESTONES│   │    alt_text    │                                     │
│  ├─────────────────┤    │    is_featured │                                     │
│  │ PK id          │    │    sort_order  │                                     │
│  │ FK project_id  │◄───┤    created_at  │                                     │
│  │    title       │    │    updated_at  │                                     │
│  │    description │    └─────────────────┘                                     │
│  │    status      │                                                           │
│  │    start_date  │    ┌─────────────────┐                                     │
│  │    due_date    │    │ PROJECT_FILES   │                                     │
│  │    completed_at│    ├─────────────────┤                                     │
│  │    sort_order  │    │ PK id          │                                     │
│  │    created_at  │    │ FK project_id  │◄──────────────────────────────────────┘
│  │    updated_at  │    │    filename    │                                     │
│  └─────────────────┘    │    original_name│                                    │
│                         │    file_path   │                                     │
│                         │    file_size   │                                     │
│                         │    mime_type   │                                     │
│                         │    description │                                     │
│                         │    is_public   │                                     │
│                         │    created_at  │                                     │
│                         │    updated_at  │                                     │
│                         └─────────────────┘                                     │
│                                                                               │
│  ┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐             │
│  │   QUOTATIONS    │    │     SERVICES    │    │SERVICE_CATEGORIES│             │
│  ├─────────────────┤    ├─────────────────┤    ├─────────────────┤             │
│  │ PK id          │    │ PK id          │    │ PK id          │             │
│  │    quote_number│    │    title       │    │    name        │             │
│  │ FK client_id   │    │    slug        │    │    slug        │             │
│  │    subject     │    │    description │    │    description │             │
│  │    message     │    │ FK category_id │◄───┤    is_active   │             │
│  │    status      │    │    price       │    │    sort_order  │             │
│  │    priority    │    │    features    │    │    created_at  │             │
│  │    responded_at│    │    is_active   │    │    updated_at  │             │
│  │    created_at  │    │    is_featured │    └─────────────────┘             │
│  │    updated_at  │    │    sort_order  │                                     │
│  └─────────────────┘    │    created_at  │                                     │
│           │             │    updated_at  │                                     │
│           │ 1:M         └─────────────────┘                                     │
│           ▼                                                                   │
│  ┌─────────────────┐                                                         │
│  │QUOTATION_ATTACH │                                                         │
│  ├─────────────────┤                                                         │
│  │ PK id          │                                                         │
│  │ FK quotation_id│◄────────────────────────────────────────────────────────┘
│  │    filename    │                                                         │
│  │    file_path   │                                                         │
│  │    file_size   │                                                         │
│  │    mime_type   │                                                         │
│  │    created_at  │                                                         │
│  │    updated_at  │                                                         │
│  └─────────────────┘                                                         │
│                                                                              │
└─────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 🛍️ **E-commerce Module ERD**

```
┌─────────────────────────────────────────────────────────────────────────────────────┐
│                              E-COMMERCE MODULE                                      │
│                                                                                     │
│  ┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐                 │
│  │    PRODUCTS     │    │PRODUCT_CATEGORIES│   │ PRODUCT_IMAGES  │                 │
│  ├─────────────────┤    ├─────────────────┤    ├─────────────────┤                 │
│  │ PK id          │    │ PK id          │    │ PK id          │                 │
│  │    name        │    │    name        │    │ FK product_id  │◄────┐             │
│  │    slug        │    │    slug        │    │    image_path  │     │             │
│  │    description │    │    description │    │    alt_text    │     │             │
│  │ FK category_id │◄───┤    is_active   │    │    is_featured │     │             │
│  │    price       │    │    sort_order  │    │    sort_order  │     │ 1:M         │
│  │    stock       │    │    created_at  │    │    created_at  │     │             │
│  │    sku         │    │    updated_at  │    │    updated_at  │     │             │
│  │    is_active   │    └─────────────────┘    └─────────────────┘     │             │
│  │    is_featured │                                                 │             │
│  │    created_at  │                                                 │             │
│  │    updated_at  │                                                 │             │
│  └─────────────────┘                                                 │             │
│           │                                                         │             │
│           │ 1:M                                                     │             │
│           ▼                                                         │             │
│  ┌─────────────────┐    ┌─────────────────┐                         │             │
│  │   CART_ITEMS    │    │ PRODUCT_ORDERS  │                         │             │
│  ├─────────────────┤    ├─────────────────┤                         │             │
│  │ PK id          │    │ PK id          │                         │             │
│  │ FK user_id     │    │    order_number │                         │             │
│  │ FK product_id  │◄───┤ FK client_id   │                         │             │
│  │    quantity    │    │    client_name │                         │             │
│  │    price       │    │    client_email│                         │             │
│  │    created_at  │    │    client_phone│                         │             │
│  │    updated_at  │    │    status      │                         │             │
│  └─────────────────┘    │    payment_status│                        │             │
│                         │    payment_method│                        │             │
│                         │    payment_proof │                        │             │
│                         │    total_amount │                         │             │
│                         │    delivery_address│                      │             │
│                         │    notes       │                         │             │
│                         │    created_at  │                         │             │
│                         │    updated_at  │                         │             │
│                         └─────────────────┘                         │             │
│                                  │                                 │             │
│                                  │ 1:M                             │             │
│                                  ▼                                 │             │
│                         ┌─────────────────┐                         │             │
│                         │PRODUCT_ORDER_ITEMS│                       │             │
│                         ├─────────────────┤                         │             │
│                         │ PK id          │                         │             │
│                         │ FK order_id    │                         │             │
│                         │ FK product_id  │◄────────────────────────┘             │
│                         │    quantity    │                                       │
│                         │    unit_price  │                                       │
│                         │    total_price │                                       │
│                         │    created_at  │                                       │
│                         │    updated_at  │                                       │
│                         └─────────────────┘                                       │
│                                                                                 │
│  ┌─────────────────┐                                                             │
│  │ PAYMENT_METHODS │                                                             │
│  ├─────────────────┤                                                             │
│  │ PK id          │                                                             │
│  │    name        │                                                             │
│  │    description │                                                             │
│  │    account_info│                                                             │
│  │    is_active   │                                                             │
│  │    sort_order  │                                                             │
│  │    created_at  │                                                             │
│  │    updated_at  │                                                             │
│  └─────────────────┘                                                             │
│                                                                                 │
└─────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 💬 **Communication Module ERD**

```
┌─────────────────────────────────────────────────────────────────────────────────────┐
│                           COMMUNICATION MODULE                                      │
│                                                                                     │
│  ┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐                 │
│  │    MESSAGES     │    │MESSAGE_ATTACHMENTS│   │ CHAT_SESSIONS   │                 │
│  ├─────────────────┤    ├─────────────────┤    ├─────────────────┤                 │
│  │ PK id          │    │ PK id          │    │ PK id          │                 │
│  │ FK sender_id   │    │ FK message_id  │◄───┤    session_id  │                 │
│  │ FK recipient_id│    │    filename    │    │ FK client_id   │                 │
│  │ FK parent_id   │    │    file_path   │    │ FK operator_id │                 │
│  │ FK order_id    │    │    file_size   │    │    status      │                 │
│  │    subject     │    │    mime_type   │    │    started_at  │                 │
│  │    message     │    │    created_at  │    │    ended_at    │                 │
│  │    priority    │    │    updated_at  │    │    created_at  │                 │
│  │    is_read     │    └─────────────────┘    │    updated_at  │                 │
│  │    read_at     │                          └─────────────────┘                 │
│  │    created_at  │                                   │                          │
│  │    updated_at  │                                   │ 1:M                      │
│  └─────────────────┘                                   ▼                          │
│           │                                   ┌─────────────────┐                 │
│           │ 1:M                               │ CHAT_MESSAGES   │                 │
│           ▼                                   ├─────────────────┤                 │
│  ┌─────────────────┐                          │ PK id          │                 │
│  │MESSAGE_REPLIES  │                          │ FK session_id  │◄────────────────┘
│  ├─────────────────┤                          │ FK sender_id   │                 │
│  │ PK id          │                          │    message     │                 │
│  │ FK message_id  │◄─────────────────────────┤    message_type│                 │
│  │ FK sender_id   │                          │    is_read     │                 │
│  │    reply       │                          │    created_at  │                 │
│  │    created_at  │                          │    updated_at  │                 │
│  │    updated_at  │                          └─────────────────┘                 │
│  └─────────────────┘                                                             │
│                                                                                 │
│  ┌─────────────────┐    ┌─────────────────┐                                     │
│  │ CHAT_TEMPLATES  │    │ NOTIFICATIONS   │                                     │
│  ├─────────────────┤    ├─────────────────┤                                     │
│  │ PK id          │    │ PK id          │                                     │
│  │    title       │    │ FK user_id     │                                     │
│  │    content     │    │    type        │                                     │
│  │    category    │    │    title       │                                     │
│  │    is_active   │    │    message     │                                     │
│  │    usage_count │    │    data        │                                     │
│  │    created_at  │    │    is_read     │                                     │
│  │    updated_at  │    │    read_at     │                                     │
│  └─────────────────┘    │    created_at  │                                     │
│                         │    updated_at  │                                     │
│                         └─────────────────┘                                     │
│                                                                                 │
└─────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 🎨 **Content Management Module ERD**

```
┌─────────────────────────────────────────────────────────────────────────────────────┐
│                         CONTENT MANAGEMENT MODULE                                   │
│                                                                                     │
│  ┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐                 │
│  │     POSTS       │    │ POST_CATEGORIES │    │  TEAM_MEMBERS   │                 │
│  ├─────────────────┤    ├─────────────────┤    ├─────────────────┤                 │
│  │ PK id          │    │ PK id          │    │ PK id          │                 │
│  │    title       │    │    name        │    │    name        │                 │
│  │    slug        │    │    slug        │    │    slug        │                 │
│  │    content     │    │    description │    │    position    │                 │
│  │    excerpt     │    │    is_active   │    │    bio         │                 │
│  │ FK category_id │◄───┤    sort_order  │    │ FK department_id│                 │
│  │ FK author_id   │    │    created_at  │    │    email       │                 │
│  │    status      │    │    updated_at  │    │    phone       │                 │
│  │    featured_image│   └─────────────────┘    │    photo       │                 │
│  │    is_featured │                          │    is_active   │                 │
│  │    published_at│                          │    is_featured │                 │
│  │    created_at  │                          │    sort_order  │                 │
│  │    updated_at  │                          │    created_at  │                 │
│  └─────────────────┘                          │    updated_at  │                 │
│                                               └─────────────────┘                 │
│                                                        │                          │
│  ┌─────────────────┐                                   │ M:1                      │
│  │   TESTIMONIALS  │                                   ▼                          │
│  ├─────────────────┤                          ┌─────────────────┐                 │
│  │ PK id          │                          │TEAM_DEPARTMENTS │                 │
│  │ FK client_id   │                          ├─────────────────┤                 │
│  │ FK project_id  │                          │ PK id          │                 │
│  │    name        │                          │    name        │                 │
│  │    company     │                          │    description │                 │
│  │    rating      │                          │    is_active   │                 │
│  │    testimonial │                          │    sort_order  │                 │
│  │    image       │                          │    created_at  │                 │
│  │    status      │                          │    updated_at  │                 │
│  │    is_featured │                          └─────────────────┘                 │
│  │    approved_at │                                                               │
│  │    created_at  │    ┌─────────────────┐                                       │
│  │    updated_at  │    │   BANNERS       │                                       │
│  └─────────────────┘    ├─────────────────┤                                       │
│                         │ PK id          │                                       │
│  ┌─────────────────┐    │    title       │                                       │
│  │ CERTIFICATIONS  │    │    description │                                       │
│  ├─────────────────┤    │ FK category_id │                                       │
│  │ PK id          │    │    image_path  │                                       │
│  │    name        │    │    link_url    │                                       │
│  │    description │    │    is_active   │                                       │
│  │    image       │    │    sort_order  │                                       │
│  │    issued_date │    │    start_date  │                                       │
│  │    expiry_date │    │    end_date    │                                       │
│  │    is_active   │    │    created_at  │                                       │
│  │    sort_order  │    │    updated_at  │                                       │
│  │    created_at  │    └─────────────────┘                                       │
│  │    updated_at  │             │                                               │
│  └─────────────────┘             │ M:1                                           │
│                                  ▼                                               │
│                         ┌─────────────────┐                                       │
│                         │BANNER_CATEGORIES│                                       │
│                         ├─────────────────┤                                       │
│                         │ PK id          │                                       │
│                         │    name        │                                       │
│                         │    slug        │                                       │
│                         │    description │                                       │
│                         │    is_active   │                                       │
│                         │    sort_order  │                                       │
│                         │    created_at  │                                       │
│                         │    updated_at  │                                       │
│                         └─────────────────┘                                       │
│                                                                                 │
└─────────────────────────────────────────────────────────────────────────────────────┘
```

---

## ⚙️ **System Settings Module ERD**

```
┌─────────────────────────────────────────────────────────────────────────────────────┐
│                           SYSTEM SETTINGS MODULE                                   │
│                                                                                     │
│  ┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐                 │
│  │    SETTINGS     │    │COMPANY_PROFILE  │    │      SEO        │                 │
│  ├─────────────────┤    ├─────────────────┤    ├─────────────────┤                 │
│  │ PK id          │    │ PK id          │    │ PK id          │                 │
│  │    key         │    │    name        │    │    page        │                 │
│  │    value       │    │    tagline     │    │    meta_title  │                 │
│  │    type        │    │    description │    │    meta_desc   │                 │
│  │    group       │    │    address     │    │    meta_keywords│                 │
│  │    description │    │    phone       │    │    canonical   │                 │
│  │    is_public   │    │    email       │    │    og_title    │                 │
│  │    created_at  │    │    website     │    │    og_desc     │                 │
│  │    updated_at  │    │    logo        │    │    og_image    │                 │
│  └─────────────────┘    │    favicon     │    │    twitter_card│                 │
│                         │    about       │    │    created_at  │                 │
│                         │    vision      │    │    updated_at  │                 │
│                         │    mission     │    └─────────────────┘                 │
│                         │    values      │                                       │
│                         │    founded_year│                                       │
│                         │    employees   │                                       │
│                         │    created_at  │                                       │
│                         │    updated_at  │                                       │
│                         └─────────────────┘                                       │
│                                                                                 │
└─────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 📊 **Entity Attributes Detail**

### **👤 Core Entities**

#### **USERS**
| Attribute | Type | Constraints | Description |
|-----------|------|------------|-------------|
| id | INT | PK, AI, NN | Unique identifier |
| name | VARCHAR(255) | NN | Full name |
| email | VARCHAR(255) | U, NN | Email address |
| password | VARCHAR(255) | NN | Encrypted password |
| phone | VARCHAR(20) | NULL | Phone number |
| company | VARCHAR(255) | NULL | Company name |
| address | TEXT | NULL | Full address |
| city | VARCHAR(100) | NULL | City |
| state | VARCHAR(100) | NULL | State/Province |
| postal_code | VARCHAR(20) | NULL | ZIP/Postal code |
| country | VARCHAR(100) | NULL | Country |
| avatar | VARCHAR(255) | NULL | Profile image path |
| is_active | BOOLEAN | DEFAULT 1 | Account status |
| settings | JSON | NULL | User preferences |
| email_verified_at | TIMESTAMP | NULL | Email verification |
| last_login_at | TIMESTAMP | NULL | Last login time |
| login_count | INT | DEFAULT 0 | Login frequency |
| failed_login_attempts | INT | DEFAULT 0 | Security tracking |
| locked_at | TIMESTAMP | NULL | Account lock time |
| otp_code | VARCHAR(6) | NULL | 2FA code |
| otp_expires_at | TIMESTAMP | NULL | OTP expiration |
| created_at | TIMESTAMP | NN | Creation time |
| updated_at | TIMESTAMP | NN | Last update |

#### **PROJECTS**
| Attribute | Type | Constraints | Description |
|-----------|------|------------|-------------|
| id | INT | PK, AI, NN | Unique identifier |
| title | VARCHAR(255) | NN | Project title |
| slug | VARCHAR(255) | U, NN | URL-friendly identifier |
| description | TEXT | NULL | Full description |
| short_description | TEXT | NULL | Brief summary |
| client_id | INT | FK, NULL | Associated client |
| quotation_id | INT | FK, NULL | Source quotation |
| category_id | INT | FK, NULL | Project category |
| status | ENUM | NN | Project status |
| start_date | DATE | NULL | Project start |
| end_date | DATE | NULL | Project completion |
| budget | DECIMAL(15,2) | NULL | Project budget |
| is_featured | BOOLEAN | DEFAULT 0 | Featured project |
| created_at | TIMESTAMP | NN | Creation time |
| updated_at | TIMESTAMP | NN | Last update |

#### **PRODUCT_ORDERS**
| Attribute | Type | Constraints | Description |
|-----------|------|------------|-------------|
| id | INT | PK, AI, NN | Unique identifier |
| order_number | VARCHAR(50) | U, NN | Order reference |
| client_id | INT | FK, NN | Ordering client |
| client_name | VARCHAR(255) | NN | Client name |
| client_email | VARCHAR(255) | NN | Contact email |
| client_phone | VARCHAR(20) | NULL | Contact phone |
| status | ENUM | NN | Order status |
| payment_status | ENUM | NN | Payment status |
| payment_method | VARCHAR(100) | NULL | Payment method |
| payment_proof | VARCHAR(255) | NULL | Payment evidence |
| payment_notes | TEXT | NULL | Payment notes |
| total_amount | DECIMAL(15,2) | NN | Order total |
| delivery_address | TEXT | NULL | Shipping address |
| needed_date | DATE | NULL | Required delivery |
| notes | TEXT | NULL | Order notes |
| admin_notes | TEXT | NULL | Internal notes |
| needs_negotiation | BOOLEAN | DEFAULT 0 | Negotiation flag |
| negotiation_message | TEXT | NULL | Negotiation details |
| requested_total | DECIMAL(15,2) | NULL | Negotiated amount |
| negotiation_status | ENUM | NULL | Negotiation status |
| created_at | TIMESTAMP | NN | Creation time |
| updated_at | TIMESTAMP | NN | Last update |

---

## 🔗 **Key Relationships**

### **1:M Relationships**
| Parent | Child | Description |
|--------|-------|-------------|
| Users | Projects | Client projects |
| Users | ProductOrders | Client orders |
| Users | Quotations | Client quotations |
| Users | Messages | User messages |
| Users | CartItems | Shopping cart |
| Users | Testimonials | Client testimonials |
| Projects | ProjectMilestones | Project tasks |
| Projects | ProjectFiles | Project deliverables |
| Projects | ProjectImages | Project gallery |
| ProductOrders | ProductOrderItems | Order line items |
| Products | ProductImages | Product gallery |
| Products | CartItems | Shopping cart items |
| Products | ProductOrderItems | Order items |
| Messages | MessageAttachments | File attachments |
| Quotations | QuotationAttachments | Quote attachments |
| ChatSessions | ChatMessages | Chat conversation |
| Posts | PostCategories | Blog categorization |
| Services | ServiceCategories | Service grouping |
| TeamMembers | TeamDepartments | Organizational structure |
| Banners | BannerCategories | Banner organization |

### **M:N Relationships**
| Entity 1 | Entity 2 | Junction Table | Description |
|----------|----------|----------------|-------------|
| Users | Roles | model_has_roles | User role assignment |
| Users | Permissions | model_has_permissions | Direct permissions |
| Roles | Permissions | role_has_permissions | Role-based permissions |

### **Self-Referencing Relationships**
| Entity | Relationship | Description |
|--------|-------------|-------------|
| Messages | parent_id | Message threading |
| ProductCategories | parent_id | Category hierarchy |
| ServiceCategories | parent_id | Service hierarchy |

---

## 🔍 **Entity Relationship Rules**

### **Business Rules**
1. **User Management**
   - Each user must have a unique email address
   - Users can have multiple roles (M:N relationship)
   - OTP codes expire after 15 minutes
   - Failed login attempts lock account after 5 tries

2. **Project Management**
   - Projects must be associated with a client (User)
   - Projects can be created from quotations (1:1 conversion)
   - Each project can have multiple milestones and files
   - Project status follows: planning → in_progress → completed → delivered

3. **E-commerce Rules**
   - Orders must have at least one order item
   - Cart items are user-specific and temporary
   - Products must belong to a category
   - Payment proof is required for order processing

4. **Communication Rules**
   - Messages can be threaded (self-referencing)
   - Chat sessions have one client and one operator
   - Notifications are user-specific and typed
   - Attachments are linked to parent entities

5. **Content Management Rules**
   - Posts must have categories and authors
   - Team members belong to departments
   - Testimonials are linked to clients and projects
   - Banners have categories and scheduling

### **Data Integrity Constraints**
1. **Foreign Key Constraints**
   - All FK relationships enforce referential integrity
   - Cascade deletes for dependent entities
   - Soft deletes for important business entities

2. **Unique Constraints**
   - Email addresses (Users)
   - Slugs (Projects, Posts, Services, etc.)
   - Order numbers (ProductOrders)
   - SKU codes (Products)

3. **Check Constraints**
   - Email format validation
   - Status enum values
   - Positive values for amounts and quantities
   - Date range validations (start_date ≤ end_date)

---

## 📈 **Performance Considerations**

### **Indexing Strategy**
1. **Primary Indexes**: All PKs automatically indexed
2. **Foreign Key Indexes**: All FKs indexed for join performance
3. **Unique Indexes**: Email, slug, order_number, SKU
4. **Composite Indexes**: 
   - (user_id, created_at) for user activity queries
   - (status, created_at) for order processing
   - (is_active, sort_order) for content display

### **Partitioning Considerations**
- **Messages**: Partition by date for archive management
- **ChatMessages**: Partition by session for performance
- **Notifications**: Partition by user for scalability

### **Optimization Notes**
- JSON columns for flexible settings and configurations
- Soft deletes for audit trails on critical entities
- Timestamp tracking for all entities
- Status enums for controlled state management

---

*Generated: 2025-08-23*  
*Type: Complete Entity Relationship Diagram*  
*Entities: 38+ identified*  
*Relationships: 1:M, M:N, Self-referencing*  
*Business Rules: Comprehensive constraints*  
*Version: 1.0*