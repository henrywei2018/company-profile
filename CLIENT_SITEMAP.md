# Client Area Sitemap

This document provides a comprehensive overview of all pages and functionality available in the client area of the company profile system.

## 🏠 Dashboard
- **URL**: `/client/dashboard`
- **Purpose**: Main dashboard with overview of client activities, statistics, and quick access to key features
- **Features**: 
  - Real-time statistics
  - Chart data visualization
  - Performance metrics
  - Upcoming deadlines
  - Recent activities

## 🛒 E-commerce & Products

### Products
- **URL**: `/client/products`
- **Purpose**: Browse and view available products
- **Features**:
  - Product catalog browsing
  - Category filtering: `/client/products/category/{category}`
  - Individual product details: `/client/products/{product}`
  - Add to cart functionality

### Shopping Cart
- **URL**: `/client/cart`
- **Purpose**: Manage shopping cart items
- **Features**:
  - View cart items
  - Update quantities
  - Remove items
  - Cart count display
  - Clear entire cart

### Orders Management
- **URL**: `/client/orders`
- **Purpose**: Manage product orders and transactions
- **Features**:
  - Order listing and status tracking
  - Order details: `/client/orders/{order}`
  - Checkout process: `/client/orders/checkout`
  - **Price Negotiation**:
    - View negotiation: `/client/orders/{order}/negotiate`
    - Submit negotiation requests
    - Accept negotiation terms
  - **Payment Processing**:
    - Payment form: `/client/orders/{order}/payment`
    - Upload payment proof
    - Payment verification status
  - Order cancellation capabilities

## 🏗️ Projects & Services

### Projects
- **URL**: `/client/projects`
- **Purpose**: Manage and track client projects
- **Features**:
  - Project listing with statistics
  - Project details: `/client/projects/{project}`
  - Project timeline: `/client/projects/{project}/timeline`
  - **Document Management**:
    - Project documents: `/client/projects/{project}/documents`
    - File downloads: `/client/projects/{project}/documents/{document}/download`
    - Project files: `/client/projects/{project}/files/{file}/download`
  - **Testimonials**:
    - Create testimonial: `/client/projects/{project}/testimonial/create`
    - Submit testimonials for completed projects

### Quotations
- **URL**: `/client/quotations`
- **Purpose**: Request and manage service quotations
- **Features**:
  - Quotation listing
  - Create new quotation: `/client/quotations/create`
  - Quotation details: `/client/quotations/{quotation}`
  - Edit quotations: `/client/quotations/{quotation}/edit`
  - **Attachment Management**:
    - Upload attachments
    - Download attachments: `/client/quotations/{quotation}/attachments/{attachment}/download`
    - Delete attachments
  - **Quotation Actions**:
    - Print quotation: `/client/quotations/{quotation}/print`
    - Duplicate quotation: `/client/quotations/{quotation}/duplicate`
    - Cancel quotation: `/client/quotations/{quotation}/cancel`
    - View activity log: `/client/quotations/{quotation}/activity`

## 💬 Communication System

### Messages
- **URL**: `/client/messages`
- **Purpose**: Communication with support team and order-related messaging
- **Features**:
  - Message inbox with filtering and search
  - Create new message: `/client/messages/create`
  - View message details: `/client/messages/{message}`
  - Reply to messages: `/client/messages/{message}/reply`
  - **Context-Based Messaging**:
    - Project-specific messages: `/client/messages/project/{project}`
    - **Order-specific messages**: `/client/messages/order/{order}` ⭐ *New Feature*
    - Message tagging for better organization
  - **Message Management**:
    - Mark as urgent: `/client/messages/{message}/urgent`
    - Toggle read status: `/client/messages/{message}/toggle-read`
    - Bulk actions for message management
    - File attachments with download capability
  - **Advanced Features**:
    - Message threading and conversation history
    - Priority levels (normal, urgent)
    - Auto-read functionality
    - Temp file upload for attachments

### Real-time Chat
- **URL**: `/client/chat`
- **Purpose**: Instant messaging with support team
- **Features**:
  - Live chat interface
  - Chat history: `/client/chat/history`
  - Individual chat sessions: `/client/chat/{chatSession}`
  - Real-time message updates

## 🔔 Notifications

### Notification Center
- **URL**: `/client/notifications`
- **Purpose**: Manage system notifications and alerts
- **Features**:
  - Notification listing with read/unread status
  - View individual notifications: `/client/notifications/{notification}`
  - Mark notifications as read
  - Bulk notification management:
    - Mark all as read
    - Bulk delete read notifications
    - Clear all read notifications
  - **Notification Preferences**:
    - Preference settings: `/client/notifications/preferences`
    - Customize notification types and delivery methods
  - **API Endpoints** for real-time updates:
    - Recent notifications: `/client/notifications/recent`
    - Unread count: `/client/notifications/unread-count`
    - Summary data: `/client/notifications/summary`

## 📝 Testimonials

### Testimonial Management
- **URL**: `/client/testimonials`
- **Purpose**: Manage client testimonials and reviews
- **Features**:
  - Testimonial listing
  - Create testimonial: `/client/testimonials/create`
  - View testimonial: `/client/testimonials/{testimonial}`
  - Edit testimonial: `/client/testimonials/{testimonial}/edit`
  - Preview testimonial: `/client/testimonials/{testimonial}/preview`
  - Delete testimonial capability
  - **File Management**:
    - Temporary file upload for testimonial media
    - File deletion and management

## 🚀 Key Features & Capabilities

### Authentication & Security
- All routes require authentication (`auth` middleware)
- Client-specific access control (`client` middleware)
- Session-based authentication with proper security measures

### File Management
- **Universal File Upload System**: Temporary file handling across all modules
- **Secure Downloads**: Protected file access with proper authorization
- **Multiple File Types**: Support for documents, images, and various media types

### Real-time Features
- **Live Dashboard Updates**: Real-time statistics and metrics
- **Instant Messaging**: Real-time chat capabilities
- **Notification System**: Instant alerts and updates
- **Cart Updates**: Real-time cart count and modifications

### Order & E-commerce Flow
1. **Product Discovery** → Browse products and categories
2. **Cart Management** → Add/remove/update items
3. **Order Creation** → Checkout and place orders
4. **Price Negotiation** → Request and manage price discussions ⭐
5. **Payment Processing** → Upload payment proof and track verification ⭐
6. **Order Tracking** → Monitor order status and delivery
7. **Communication** → Order-specific messaging support ⭐

### Project Management Flow
1. **Project Overview** → View assigned projects and status
2. **Timeline Tracking** → Monitor project progress and milestones
3. **Document Access** → Download project files and documents
4. **Communication** → Project-specific messaging
5. **Testimonial Submission** → Provide feedback on completed projects

### Communication Workflow
1. **Multi-channel Communication**: Messages, Chat, Notifications
2. **Context-aware Messaging**: Project and Order-specific conversations ⭐
3. **File Sharing**: Attachment support across all communication channels
4. **Priority Management**: Urgent message handling
5. **Thread Management**: Organized conversation history

## 🔧 Technical Implementation

### Route Structure
- **Prefix**: `/client`
- **Middleware**: `['auth', 'client']`
- **Naming Convention**: `client.{module}.{action}`

### API Integration
- RESTful API endpoints for AJAX functionality
- Real-time updates via polling mechanisms
- File upload APIs with temporary storage
- Statistics and metrics APIs for dashboard updates

### Performance Features
- **Caching**: Dashboard cache management
- **Pagination**: Efficient data loading for large datasets
- **Lazy Loading**: Optimized file and image loading
- **Bulk Operations**: Efficient mass data management

---

## 📊 Recent Enhancements ⭐

### Order-Message Integration
- **Order-tagged Messages**: Messages can now be specifically tagged to orders
- **Context Preservation**: Message threads maintain order context
- **Order Message View**: Dedicated view for order-specific conversations
- **Auto-subject Generation**: Automatic subject line creation for order inquiries

### Enhanced Order Management  
- **Price Negotiation System**: Full negotiation workflow with status tracking
- **Payment Processing**: Comprehensive payment upload and verification system
- **Order Status Tracking**: Detailed order lifecycle management
- **Modal-based Interactions**: Improved UX with modal confirmations and forms

### Improved Communication
- **Message Threading**: Better conversation management
- **File Attachment System**: Universal file upload across all modules
- **Priority Messaging**: Urgent message handling and escalation
- **Bulk Message Operations**: Efficient message management tools

This sitemap represents a comprehensive client portal with full e-commerce capabilities, project management, and integrated communication systems, providing clients with a complete business management solution.