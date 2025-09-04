# System Architecture Diagram
**Company Profile Application - Phase 1 Design Documentation**

---

## 📊 **3-Tier Architecture Overview**

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│                             PRESENTATION LAYER                                 │
├─────────────────────────────────────────────────────────────────────────────────┤
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐  ┌─────────────────────────┐ │
│  │   PUBLIC    │  │   CLIENT    │  │    ADMIN    │  │    MOBILE RESPONSIVE    │ │
│  │    AREA     │  │    AREA     │  │    AREA     │  │       INTERFACE         │ │
│  ├─────────────┤  ├─────────────┤  ├─────────────┤  ├─────────────────────────┤ │
│  │ • Homepage  │  │ • Dashboard │  │ • Dashboard │  │ • Touch Optimization    │ │
│  │ • Services  │  │ • Orders    │  │ • Orders    │  │ • Responsive Design     │ │
│  │ • Portfolio │  │ • Messages  │  │ • Messages  │  │ • Progressive Web App   │ │
│  │ • Contact   │  │ • Profile   │  │ • Users     │  │ • Offline Capability    │ │
│  │ • Blog      │  │ • Payment   │  │ • Content   │  │ • Push Notifications    │ │
│  └─────────────┘  └─────────────┘  └─────────────┘  └─────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────────────────┘
                                       │
                                       ▼
┌─────────────────────────────────────────────────────────────────────────────────┐
│                           BUSINESS LOGIC LAYER                                 │
├─────────────────────────────────────────────────────────────────────────────────┤
│  ┌───────────────────┐  ┌───────────────────┐  ┌───────────────────────────────┐ │
│  │  AUTHENTICATION   │  │   AUTHORIZATION   │  │     BUSINESS SERVICES         │ │
│  │     SERVICES      │  │     SERVICES      │  │                               │ │
│  ├───────────────────┤  ├───────────────────┤  ├───────────────────────────────┤ │
│  │ • Multi-Factor    │  │ • Role-Based      │  │ • Order Processing Service    │ │
│  │   Authentication  │  │   Access Control  │  │ • Payment Processing Service  │ │
│  │ • OTP Verification│  │ • Permission      │  │ • Message Management Service │ │
│  │ • Session Mgmt    │  │   Management      │  │ • File Upload Service        │ │
│  │ • Password Reset  │  │ • User Roles      │  │ • Notification Service       │ │
│  └───────────────────┘  └───────────────────┘  └───────────────────────────────┘ │
│                                                                                 │
│  ┌───────────────────┐  ┌───────────────────┐  ┌───────────────────────────────┐ │
│  │   WORKFLOW        │  │   INTEGRATION     │  │     ANALYTICS & REPORTING     │ │
│  │   MANAGEMENT      │  │    SERVICES       │  │           SERVICES            │ │
│  ├───────────────────┤  ├───────────────────┤  ├───────────────────────────────┤ │
│  │ • Order Workflow  │  │ • Payment Gateway │  │ • Business Intelligence       │ │
│  │ • Project Mgmt    │  │ • Email Services  │  │ • Performance Monitoring      │ │
│  │ • Negotiation     │  │ • SMS Services    │  │ • User Behavior Tracking      │ │
│  │ • Status Tracking │  │ • Cloud Storage   │  │ • Sales Analytics            │ │
│  │ • Approval Process│  │ • API Integration │  │ • Report Generation          │ │
│  └───────────────────┘  └───────────────────┘  └───────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────────────────┘
                                       │
                                       ▼
┌─────────────────────────────────────────────────────────────────────────────────┐
│                             DATA ACCESS LAYER                                  │
├─────────────────────────────────────────────────────────────────────────────────┤
│  ┌───────────────────┐  ┌───────────────────┐  ┌───────────────────────────────┐ │
│  │   ELOQUENT ORM    │  │   CACHING LAYER   │  │      DATABASE ABSTRACTION     │ │
│  ├───────────────────┤  ├───────────────────┤  ├───────────────────────────────┤ │
│  │ • Model Relations │  │ • Redis Cache     │  │ • Connection Pool Management  │ │
│  │ • Query Builder   │  │ • Session Cache   │  │ • Transaction Management      │ │
│  │ • Migrations      │  │ • Data Cache      │  │ • Query Optimization          │ │
│  │ • Seeders         │  │ • Page Cache      │  │ • Index Management            │ │
│  │ • Factories       │  │ • API Response    │  │ • Backup & Recovery           │ │
│  └───────────────────┘  └───────────────────┘  └───────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────────────────┘
                                       │
                                       ▼
┌─────────────────────────────────────────────────────────────────────────────────┐
│                               DATA STORAGE                                     │
├─────────────────────────────────────────────────────────────────────────────────┤
│  ┌───────────────────┐  ┌───────────────────┐  ┌───────────────────────────────┐ │
│  │   PRIMARY DB      │  │   FILE STORAGE    │  │        BACKUP STORAGE         │ │
│  │   MySQL 8.0       │  │                   │  │                               │ │
│  ├───────────────────┤  ├───────────────────┤  ├───────────────────────────────┤ │
│  │ • User Data       │  │ • Product Images  │  │ • Daily Database Backups      │ │
│  │ • Order Data      │  │ • Project Files   │  │ • File System Backups        │ │
│  │ • Project Data    │  │ • Payment Proofs  │  │ • Log Archives               │ │
│  │ • Message Data    │  │ • Documents       │  │ • Configuration Backups      │ │
│  │ • System Logs     │  │ • Profile Photos  │  │ • Disaster Recovery          │ │
│  └───────────────────┘  └───────────────────┘  └───────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────────────────┘
```

---

## 🔐 **Security Architecture**

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│                              SECURITY LAYERS                                   │
├─────────────────────────────────────────────────────────────────────────────────┤
│                                                                                 │
│  ┌─────────────┐ ┌─────────────┐ ┌─────────────┐ ┌─────────────────────────────┐ │
│  │   NETWORK   │ │  TRANSPORT  │ │ APPLICATION │ │       DATA SECURITY         │ │
│  │  SECURITY   │ │  SECURITY   │ │  SECURITY   │ │                             │ │
│  ├─────────────┤ ├─────────────┤ ├─────────────┤ ├─────────────────────────────┤ │
│  │ • Firewall  │ │ • HTTPS/TLS │ │ • Input     │ │ • Encryption at Rest        │ │
│  │ • DDoS      │ │ • SSL Cert  │ │   Validation│ │ • Database Encryption       │ │
│  │   Protection│ │ • Secure    │ │ • CSRF      │ │ • File Encryption           │ │
│  │ • Rate      │ │   Headers   │ │   Protection│ │ • Backup Encryption         │ │
│  │   Limiting  │ │ • HSTS      │ │ • XSS       │ │ • Key Management            │ │
│  │             │ │             │ │   Prevention│ │                             │ │
│  └─────────────┘ └─────────────┘ └─────────────┘ └─────────────────────────────┘ │
│                                                                                 │
│  ┌─────────────────────────────────────────────────────────────────────────────┐ │
│  │                        AUTHENTICATION FLOW                                 │ │
│  ├─────────────────────────────────────────────────────────────────────────────┤ │
│  │  [Login] → [Credential Validation] → [OTP Generation] → [OTP Verification] │ │
│  │     ↓               ↓                       ↓                  ↓           │ │
│  │  [Session] ← [Role Assignment] ← [Multi-Factor Auth] ← [Security Check]    │ │
│  └─────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                 │
│  ┌─────────────────────────────────────────────────────────────────────────────┐ │
│  │                      AUTHORIZATION MATRIX                                   │ │
│  ├─────────────────────────────────────────────────────────────────────────────┤ │
│  │              │ PUBLIC │ CLIENT │ ADMIN │ SUPER ADMIN │                      │ │
│  │              ├────────┼────────┼───────┼─────────────┤                      │ │
│  │ View Content │   ✓    │   ✓    │   ✓   │      ✓      │                      │ │
│  │ Place Orders │   ✗    │   ✓    │   ✓   │      ✓      │                      │ │
│  │ Manage Users │   ✗    │   ✗    │   ✓   │      ✓      │                      │ │
│  │ System Config│   ✗    │   ✗    │   ✗   │      ✓      │                      │ │
│  └─────────────────────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────────────────┘
```

---

## ⚡ **Performance Architecture**

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│                          PERFORMANCE OPTIMIZATION                              │
├─────────────────────────────────────────────────────────────────────────────────┤
│                                                                                 │
│  ┌─────────────────────┐    ┌─────────────────────┐    ┌─────────────────────┐   │
│  │    FRONTEND         │    │     BACKEND         │    │      DATABASE      │   │
│  │  OPTIMIZATION       │    │   OPTIMIZATION      │    │   OPTIMIZATION      │   │
│  ├─────────────────────┤    ├─────────────────────┤    ├─────────────────────┤   │
│  │ • Asset Compression │    │ • OPCache           │    │ • Query Optimization│   │
│  │ • Image Optimization│    │ • Response Caching │    │ • Strategic Indexes │   │
│  │ • Lazy Loading      │    │ • Database Pooling  │    │ • Partitioning      │   │
│  │ • Minification      │    │ • Queue Processing  │    │ • Read Replicas     │   │
│  │ • CDN Distribution  │    │ • Background Jobs   │    │ • Connection Pool   │   │
│  └─────────────────────┘    └─────────────────────┘    └─────────────────────┘   │
│                                                                                 │
│  ┌─────────────────────────────────────────────────────────────────────────────┐ │
│  │                         CACHING STRATEGY                                    │ │
│  ├─────────────────────────────────────────────────────────────────────────────┤ │
│  │                                                                             │ │
│  │  L1: Browser Cache ──┐                                                     │ │
│  │                      │                                                     │ │
│  │  L2: CDN Cache ──────┼──► Application Request                              │ │
│  │                      │                                                     │ │
│  │  L3: Redis Cache ────┘                                                     │ │
│  │                      │                                                     │ │
│  │  L4: Database Cache ─┘                                                     │ │
│  │                                                                             │ │
│  │  Cache Types:                                                              │ │
│  │  • Page Cache (60 minutes)                                                 │ │
│  │  • Database Query Cache (30 minutes)                                       │ │
│  │  • API Response Cache (15 minutes)                                         │ │
│  │  • Session Cache (120 minutes)                                             │ │
│  │  • Static Asset Cache (24 hours)                                           │ │
│  └─────────────────────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────────────────┘
```

---

## 🔄 **Integration Architecture**

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│                           EXTERNAL INTEGRATIONS                                │
├─────────────────────────────────────────────────────────────────────────────────┤
│                                                                                 │
│  ┌─────────────────────────────┐              ┌─────────────────────────────┐   │
│  │       PAYMENT GATEWAY       │              │      EMAIL SERVICES         │   │
│  ├─────────────────────────────┤              ├─────────────────────────────┤   │
│  │ • Bank Transfer             │              │ • SMTP Configuration        │   │
│  │ • E-Wallet Integration      │              │ • Email Templates           │   │
│  │ • Credit Card Processing    │              │ • Automated Campaigns       │   │
│  │ • Payment Verification      │ ◄────────────┤ • Transactional Emails     │   │
│  │ • Refund Management         │              │ • Notification System       │   │
│  └─────────────────────────────┘              └─────────────────────────────┘   │
│                                                                                 │
│  ┌─────────────────────────────┐              ┌─────────────────────────────┐   │
│  │       CLOUD STORAGE         │              │       ANALYTICS             │   │
│  ├─────────────────────────────┤              ├─────────────────────────────┤   │
│  │ • File Upload Management    │              │ • Google Analytics          │   │
│  │ • Image Processing          │              │ • User Behavior Tracking    │   │
│  │ • Document Storage          │ ◄────────────┤ • Conversion Tracking       │   │
│  │ • Backup Solutions          │              │ • Performance Monitoring    │   │
│  │ • CDN Integration           │              │ • Business Intelligence     │   │
│  └─────────────────────────────┘              └─────────────────────────────┘   │
│                                                                                 │
│  ┌─────────────────────────────────────────────────────────────────────────────┐ │
│  │                            API ARCHITECTURE                                 │ │
│  ├─────────────────────────────────────────────────────────────────────────────┤ │
│  │                                                                             │ │
│  │  [Mobile App] ──┐                          ┌── [Third-party Services]      │ │
│  │                 │                          │                               │ │
│  │  [Web Client] ──┼──► RESTful API Gateway ──┼── [Payment Providers]         │ │
│  │                 │                          │                               │ │
│  │  [Admin Panel] ─┘                          └── [Analytics Services]        │ │
│  │                                                                             │ │
│  │  API Features:                                                              │ │
│  │  • Rate Limiting                                                            │ │
│  │  • Authentication & Authorization                                           │ │
│  │  • Request/Response Validation                                              │ │
│  │  • Error Handling & Logging                                                 │ │
│  │  • API Documentation                                                        │ │
│  └─────────────────────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────────────────┘
```

---

## 📱 **Scalability Architecture**

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│                            SCALABILITY DESIGN                                  │
├─────────────────────────────────────────────────────────────────────────────────┤
│                                                                                 │
│  ┌─────────────────────────────────────────────────────────────────────────────┐ │
│  │                        HORIZONTAL SCALING                                   │ │
│  ├─────────────────────────────────────────────────────────────────────────────┤ │
│  │                                                                             │ │
│  │  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐   │ │
│  │  │   WEB SERVER │  │   WEB SERVER │  │   WEB SERVER │  │  LOAD BALANCER  │   │ │
│  │  │   Instance 1 │  │   Instance 2 │  │   Instance 3 │  │                 │   │ │
│  │  └──────────────┘  └──────────────┘  └──────────────┘  └──────────────┘   │ │
│  │         │                   │                   │              │           │ │
│  │         └───────────────────┼───────────────────┼──────────────┘           │ │
│  │                             │                   │                          │ │
│  │  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐                     │ │
│  │  │  DATABASE    │  │  CACHE LAYER │  │  FILE STORAGE│                     │ │
│  │  │   CLUSTER    │  │    CLUSTER   │  │   CLUSTER    │                     │ │
│  │  │              │  │              │  │              │                     │ │
│  │  │ Master/Slave │  │ Redis Cluster│  │ Distributed  │                     │ │
│  │  │ Replication  │  │              │  │   Storage    │                     │ │
│  │  └──────────────┘  └──────────────┘  └──────────────┘                     │ │
│  └─────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                 │
│  ┌─────────────────────────────────────────────────────────────────────────────┐ │
│  │                        MICROSERVICES READINESS                             │ │
│  ├─────────────────────────────────────────────────────────────────────────────┤ │
│  │                                                                             │ │
│  │  Current Monolithic Architecture ──────► Future Microservices              │ │
│  │                                                                             │ │
│  │  ┌────────────────┐                    ┌────────────────┐                  │ │
│  │  │   MONOLITH     │                    │ User Service   │                  │ │
│  │  │                │                    ├────────────────┤                  │ │
│  │  │ • All Features │         ───►       │ Order Service  │                  │ │
│  │  │ • Single DB    │                    ├────────────────┤                  │ │
│  │  │ • Single Deploy│                    │ Payment Service│                  │ │
│  │  │                │                    ├────────────────┤                  │ │
│  │  └────────────────┘                    │ Message Service│                  │ │
│  │                                        └────────────────┘                  │ │
│  │                                                                             │ │
│  │  Benefits for Future Migration:                                             │ │
│  │  • Independent Scaling                                                      │ │
│  │  • Technology Diversity                                                     │ │
│  │  • Fault Isolation                                                          │ │
│  │  • Team Independence                                                        │ │
│  └─────────────────────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────────────────┘
```

---

## 🎯 **Design Principles Applied**

### **1. SOLID Principles**
- **Single Responsibility**: Each model/controller has a single, well-defined purpose
- **Open-Closed**: System designed for extension without modification
- **Liskov Substitution**: Consistent interface contracts across similar classes
- **Interface Segregation**: Focused interfaces rather than monolithic ones
- **Dependency Inversion**: Depend on abstractions, not concrete implementations

### **2. DRY (Don't Repeat Yourself)**
- Reusable service classes for common functionality
- Shared traits for filterable models
- Common validation rules and form requests
- Centralized configuration management

### **3. KISS (Keep It Simple, Stupid)**
- Clear, descriptive naming conventions
- Straightforward business logic flow
- Minimal cognitive overhead in code structure
- Simple but effective user interfaces

### **4. Separation of Concerns**
- Clear separation between presentation, business, and data layers
- Dedicated service classes for complex business logic
- Separate concerns for authentication, authorization, and business rules
- Modular approach enabling independent testing and maintenance

---

**📝 This diagram supports LAPORAN_PROGRESS_1.md Phase 1 Design documentation**  
**🎯 Status**: Design Complete - Ready for Implementation Phase