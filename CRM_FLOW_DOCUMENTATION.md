# Paridharan CRM - Complete System Flow Documentation
================================================================

## Table of Contents
1. [System Overview](#system-overview)
2. [Authentication & Authorization Flow](#authentication--authorization-flow)
3. [Admin Module Flow](#admin-module-flow)
4. [Employee Module Flow](#employee-module-flow)
5. [Contact Management Flow](#contact-management-flow)
6. [Campaign Management Flow](#campaign-management-flow)
7. [WhatsApp Template Management Flow](#whatsapp-template-management-flow)
8. [Lead & Follow-up Management Flow](#lead--follow-up-management-flow)
9. [Booking Management Flow](#booking-management-flow)
10. [Location Management Flow](#location-management-flow)
11. [Dashboard Overview](#dashboard-overview)
12. [Data Flow & Relationships](#data-flow--relationships)

================================================================
## 1. System Overview
================================================================

**Paridharan CRM** is a comprehensive Customer Relationship Management system designed for managing exhibitions, visitors, campaigns, leads, and bookings.

### Key Features:
- Multi-role authentication (Admin & Employee)
- Contact Management (Exhibitors & Visitors)
- WhatsApp Campaign Management
- Lead Tracking & Follow-ups
- Booking Management
- Location & Table Management
- WhatsApp Template Management
- Bulk Data Import/Export

### Technology Stack:
- Backend: Laravel (PHP)
- Frontend: Bootstrap 5, jQuery, DataTables
- Database: MySQL
- External APIs: WhatsApp Business API (Meta)
- Authentication: Laravel Session-based Authentication

================================================================
## 2. Authentication & Authorization Flow
================================================================

### 2.1 User Login Flow
```
┌─────────────────┐
│  Landing Page   │
│   (/)           │
└────────┬────────┘
         │ Redirect
         ▼
┌─────────────────┐
│   Login Page    │
│  /sign-in       │
└────────┬────────┘
         │ POST credentials
         ▼
┌─────────────────────────────────┐
│  AuthController@validate        │
│  - Validate email/password      │
│  - Check user status            │
│  - Create session               │
└────────┬────────────────────────┘
         │
         ├─── Admin ───► /admin/dashboard
         │
         └─── Employee ───► /employee/dashboard
```

### 2.2 Role-Based Access Control

**Admin Role** (`role = 'admin'`):
- Access: All admin routes (`/admin/*`)
- Middleware: `checkRole:admin`
- Permissions:
  * Full system access
  * Employee management
  * Campaign management
  * Template management
  * Location management
  * View all leads & bookings

**Employee Role** (`role = 'employee'`):
- Access: Employee routes (`/employee/*`)
- Middleware: `checkRole:employee`
- Permissions:
  * Lead management
  * Follow-up creation
  * Booking creation
  * View assigned contacts

### 2.3 Middleware Protection
```
Request → Auth Middleware → CheckRole Middleware → Controller
                              │
                              ├── Valid Role → Proceed
                              │
                              └── Invalid Role → 403 Forbidden
```

================================================================
## 3. Admin Module Flow
================================================================

### 3.1 Dashboard Flow
```
Admin Dashboard
├── Statistics Overview
│   ├── Total Employees (Active/Inactive)
│   ├── Total Contacts (Exhibitors/Visitors)
│   ├── Campaign Statistics (Draft/Sent/Scheduled)
│   ├── Template Statistics (Total/Approved/Pending)
│   ├── Location Statistics
│   ├── Follow-up Statistics
│   └── Booking Statistics
│
├── Recent Activity
│   ├── Recent Employees
│   ├── Recent Exhibitors
│   ├── Recent Visitors
│   └── Recent Campaigns
│
└── Quick Actions
    ├── Add Employee
    ├── Add Contact
    ├── Create Campaign
    └── Manage Templates
```

### 3.2 Employee Management Flow

**Create Employee:**
```
1. Admin clicks "Add Employee" → Offcanvas form opens
2. Fill employee details:
   - Name, Email, Phone
   - Position, Salary
   - Date of Birth, Hire Date
   - Address
   - Password (auto-generated or manual)
3. Submit → EmployeeService@createEmployee
4. Create User with role='employee', status='active'
5. Show success message → Refresh table
```

**Update Employee:**
```
1. Click "Edit" button on employee row
2. Load employee data into offcanvas form
3. Modify fields → Submit
4. EmployeeService@updateEmployee updates record
5. Refresh table with updated data
```

**Toggle Employee Status:**
```
1. Click "Activate/Deactivate" button
2. AJAX POST to /admin/employees/{id}/toggle-status
3. EmployeeService toggles status (active ↔ inactive)
4. Update UI immediately
```

**Delete Employee:**
```
1. Click "Delete" button → SweetAlert confirmation
2. AJAX DELETE to /admin/employees/{id}
3. EmployeeService@deleteEmployee removes record
4. Refresh table
```

**Change Password:**
```
1. Navigate to /admin/employees/{id}/change-password
2. Enter new password → Confirm password
3. EmployeeService@changePassword updates password (hashed)
4. Redirect with success message
```

### 3.3 Contact Management Flow (Exhibitors & Visitors)

**Create Contact:**
```
1. Navigate to Contacts (Exhibitor/Visitor)
2. Click "Add [Type]" button
3. Fill contact form:
   - Basic: Name, Phone, Email, Location
   - Exhibitor-specific: Product Type, Brand Name, Business Type, GST Number
   - Visitor-specific: Interests, Company
4. Submit → ContactService@createContact
5. Store in 'contacts' table with type='exhibitor' or 'visitor'
6. Show success → Refresh table
```

**Bulk Import Contacts:**
```
1. Navigate to Contacts page
2. Click "Import Contacts" → File upload modal
3. Upload CSV/Excel file
4. ProcessLargeFile Job processes file in background
5. Validate each row:
   - Required fields check
   - Duplicate phone/email check
   - Format validation
6. Insert valid records → Show import summary
7. Log errors for invalid rows
```

**Contact List Management:**
```
- View all contacts with filters (Location, Type)
- Search by name, phone, email
- Edit contact details
- Delete contacts
- Export to CSV/Excel
```

### 3.4 Campaign Management Flow

**Campaign Creation Flow:**
```
1. Navigate to Campaigns → Click "Create Campaign"
2. Campaign Create Form:
   ├── Basic Info:
   │   ├── Campaign Name (required)
   │   ├── Subject (required)
   │   └── Message (required)
   │
   ├── Campaign Type Selection:
   │   ├── Email
   │   ├── SMS
   │   └── WhatsApp (default)
   │
   ├── Template Selection (for WhatsApp):
   │   └── Select approved template from dropdown
   │
   ├── Recipient Selection:
   │   ├── Select Contacts (Exhibitors/Visitors)
   │   ├── Bulk selection by location
   │   └── CSV upload for recipients
   │
   └── Scheduling (optional):
       └── Schedule for future date/time
3. Submit → CampaignService@createCampaign
4. Create Campaign record (status='draft')
5. Store template_name if WhatsApp type
6. Store recipients in campaign_recipients pivot table
7. Redirect to campaign list
```

**Campaign Status Flow:**
```
Draft → [Send Now] → Sent
Draft → [Schedule] → Scheduled → [Auto-send at scheduled time] → Sent

States:
- draft: Newly created, not sent
- scheduled: Scheduled for future sending
- sent: Campaign has been sent
```

**Sending Campaign Flow:**
```
1. Admin clicks "Send Campaign" on draft campaign
2. CampaignController@send:
   ├── Validate campaign status
   ├── Get all recipients from campaign_recipients
   ├── Dispatch SendCampaignJob for each recipient
   └── Update campaign status to 'sent'
   
3. SendCampaignJob (Queue Worker):
   ├── For WhatsApp Campaigns:
   │   ├── Fetch template details from cache/API
   │   ├── Validate template is approved
   │   ├── Build dynamic payload based on template:
   │   │   ├── Header component (IMAGE/TEXT/VIDEO/DOCUMENT)
   │   │   ├── Body component with variables ({{1}}, {{2}})
   │   │   └── Map campaign data to template variables
   │   ├── Construct API endpoint:
   │   │   └── {base_url}/V23.0/{phone_number_id}/messages
   │   ├── Send request to WhatsApp API:
   │   │   ├── POST with Bearer token
   │   │   ├── Payload: messaging_product, recipient_type, template, components
   │   │   └── Include image URL (default or campaign-specific)
   │   ├── Update campaign_recipients:
   │   │   ├── status='sent' on success
   │   │   └── status='failed' with error message on failure
   │   └── Log results (campaign_progress.log)
   │
   ├── For Email Campaigns:
   │   └── Send via Laravel Mail system
   │
   └── For SMS Campaigns:
       └── Send via SMS gateway
```

**Campaign Recipients Management:**
```
1. View Campaign → Show Recipients tab
2. Add Recipients:
   ├── Search contacts
   ├── Bulk add by location
   └── Import from CSV
3. Remove Recipients:
   └── Delete from campaign_recipients table
```

### 3.5 WhatsApp Template Management Flow

**Fetch Templates from Meta API:**
```
1. Navigate to Templates page
2. System fetches templates:
   ├── Check cache (whatsapp_templates) - 5 min TTL
   ├── If cache miss → TemplateController@fetchTemplatesFromAPI
   │   ├── GET request to:
   │   │   └── {base_url}/{version}/{waba_id}/message_templates
   │   ├── Headers:
   │   │   ├── Authorization: Bearer {token}
   │   │   └── Content-Type: application/json
   │   ├── Parse response:
   │   │   ├── Extract templates array
   │   │   ├── Format: id, name, language, status, category, components
   │   │   └── Store in cache
   │   └── Return formatted data
   └── Display in DataTable with filters
```

**Create New Template:**
```
1. Click "Create Template" → Template Create Form
2. Fill Template Details:
   ├── Basic Info:
   │   ├── Name (unique, required)
   │   ├── Language (en, hi, etc.)
   │   └── Category (MARKETING/UTILITY/AUTHENTICATION)
   │
   ├── Header Component (optional):
   │   ├── Type: TEXT/IMAGE/VIDEO/DOCUMENT
   │   └── Content: Text or Media URL
   │
   ├── Body Component (required):
   │   ├── Text with variables {{1}}, {{2}}, etc.
   │   └── Variables replaced during campaign sending
   │
   ├── Footer Component (optional):
   │   └── Static text (no variables allowed)
   │
   └── Buttons (optional):
       └── Quick Reply, Call-to-Action, etc.

3. Submit → TemplateController@store
4. Build components array
5. POST to Meta API:
   ├── Endpoint: {base_url}/{version}/{waba_id}/message_templates
   ├── Payload:
   │   ├── name, category, language
   │   ├── allow_category_change: false
   │   └── components: [header, body, footer, buttons]
   ├── Handle Response:
   │   ├── Success → Clear cache → Show success
   │   └── Error → Parse error codes:
   │       ├── 132000, 131048, 131047: Template already exists
   │       └── Other errors: Show detailed message
   └── Refresh template list
```

**Template Status Management:**
```
Template Statuses:
- APPROVED: Ready to use in campaigns
- PENDING: Under review by Meta
- REJECTED: Not approved (check rejection_reason)
- PENDING_DELETION: Scheduled for deletion
- LIMITED: Limited functionality
- PAUSED: Temporarily paused

Flow:
1. Template created → Status: PENDING
2. Meta reviews → APPROVED/REJECTED
3. Approved templates can be selected in campaign creation
4. Rejected templates show rejection reason
```

**Template Refresh:**
```
1. Click "Refresh Templates" button
2. TemplateController@refreshCache:
   ├── Clear cache (Cache::forget('whatsapp_templates'))
   ├── Fetch fresh data from API
   └── Update cache with new data
3. Reload table
```

**Delete Template:**
```
1. Click "Delete" on template (if not DELETED status)
2. TemplateController@destroy:
   ├── DELETE request to:
   │   └── {base_url}/{version}/{waba_id}/message_templates/{template_id}
   ├── Handle response
   └── Clear cache → Refresh list
```

### 3.6 Location Management Flow

**Create Location:**
```
1. Navigate to Locations → Click "Add Location"
2. Location Form:
   ├── Location Name
   ├── Address
   ├── City, State, Country
   ├── Contact Info
   └── Status (Active/Inactive)
3. Submit → LocationService@createLocation
4. Store in 'location_mngts' table
5. Show success → Refresh table
```

**Add Tables to Location:**
```
1. View Location → Click "Manage Tables"
2. Table Management:
   ├── Add Table:
   │   ├── Table Number/Name
   │   ├── Capacity
   │   ├── Price
   │   └── Availability Status
   ├── Edit Table Details
   └── Delete Table
3. Store in 'location_mngt_table_details' table
```

================================================================
## 4. Employee Module Flow
================================================================

### 4.1 Employee Dashboard Flow
```
Employee Dashboard
├── Assigned Statistics
│   ├── Total Leads Assigned
│   ├── Pending Follow-ups
│   └── Completed Bookings
│
└── Quick Actions
    ├── Add New Lead
    ├── View Follow-ups
    └── Create Booking
```

### 4.2 Lead Management Flow

**View All Leads:**
```
1. Navigate to Leads → LeadController@index
2. Display leads table with filters:
   ├── Filter by Lead Type:
   │   ├── Interested
   │   ├── Busy
   │   └── Materialised
   ├── Search by phone/name
   └── DataTables server-side processing
```

**Lead Data Structure:**
```
Leads are stored in 'follow_ups' table:
- phone: Unique identifier (contact phone number)
- status: busy/interested/materialised
- comment: Follow-up notes
- next_followup_date: Scheduled follow-up date
- next_followup_time: Scheduled follow-up time
- employee_id: Assigned employee
- created_at: Lead creation timestamp
```

**Create Follow-up (Add Lead):**
```
1. Click "Add Follow-up" → Offcanvas form opens
2. Enter Lead Details:
   ├── Phone Number (required, unique identifier)
   ├── Status Selection:
   │   ├── Busy:
   │   │   ├── Comment (required)
   │   │   ├── Next Follow-up Date (required)
   │   │   └── Next Follow-up Time (required)
   │   │
   │   ├── Interested:
   │   │   ├── Comment (required)
   │   │   └── Optional follow-up scheduling
   │   │
   │   └── Materialised (Booking):
   │       ├── Comment (required)
   │       ├── Booking Date (required)
   │       ├── Booking Location (required)
   │       ├── Table Number (required)
   │       ├── Price (required)
   │       ├── Amount Status: paid/partial/unpaid
   │       └── Amount Paid (required if paid/partial)
   │
   └── Employee ID (auto-set to current logged-in employee)

3. Submit → FollowUpService@create
4. Save Follow-up:
   ├── Insert into 'follow_ups' table
   └── If status='materialised':
       └── Also create Booking record (see Booking Flow)
5. Show success → Refresh table
```

**View Follow-up History:**
```
1. Click on Lead row → Show follow-up modal
2. Display all follow-ups for that phone number:
   ├── Follow-up Date/Time
   ├── Status
   ├── Comments
   ├── Next Follow-up Date (if scheduled)
   └── Employee who created it
3. Ordered by most recent first
```

**Edit Lead:**
```
1. Click "Edit" on lead row
2. Load existing follow-up data into form
3. Modify fields → Submit
4. FollowUpService@update updates record
5. Refresh table
```

================================================================
## 5. Contact Management Flow
================================================================

### 5.1 Contact Types

**Exhibitor Contact:**
```
Fields:
- name, email, phone, alternate_phone
- location
- type: 'exhibitor'
- product_type, brand_name, business_type
- gst_number
- created_at, updated_at

Use Cases:
- Manage exhibitor information
- Send marketing campaigns
- Track business details
```

**Visitor Contact:**
```
Fields:
- name, email, phone, alternate_phone
- location
- type: 'visitor'
- created_at, updated_at

Use Cases:
- Manage visitor information
- Send promotional messages
- Track visitor interests
```

### 5.2 Contact Workflow
```
Create Contact → Store in DB → Available for:
├── Campaign Recipients
├── Lead Creation (phone lookup)
├── Follow-up Tracking
└── Booking Management
```

================================================================
## 6. Campaign Management Flow
================================================================

### 6.1 Campaign Lifecycle
```
┌──────────┐
│  Create  │
│ Campaign │
└────┬─────┘
     │
     ▼
┌──────────┐
│  Draft   │ ←── Can edit, add recipients
└────┬─────┘
     │
     ├─── [Send Now] ───► ┌──────────┐
     │                     │   Sent   │
     │                     └──────────┘
     │
     └─── [Schedule] ───► ┌─────────────┐
                          │  Scheduled  │
                          └──────┬──────┘
                                 │
                                 │ [Auto-send at time]
                                 ▼
                          ┌──────────┐
                          │   Sent   │
                          └──────────┘
```

### 6.2 Campaign Types

**WhatsApp Campaign:**
```
1. Select type='whatsapp'
2. Choose approved template from dropdown
3. Template determines payload structure:
   ├── Header: IMAGE/TEXT/VIDEO/DOCUMENT
   ├── Body: Variables {{1}}, {{2}}, etc.
   └── Footer: Static text
4. Map campaign data to template variables:
   ├── {{1}} → Campaign Name
   ├── {{2}} → Campaign Message
   └── Additional variables as needed
5. Send via WhatsApp Business API
```

**Email Campaign:**
```
1. Select type='email'
2. Compose email (subject, message)
3. Send via Laravel Mail system
4. Track delivery status
```

**SMS Campaign:**
```
1. Select type='sms'
2. Compose SMS message (160/320 char limit)
3. Send via SMS gateway
4. Track delivery status
```

### 6.3 Campaign Recipient Flow
```
Campaign Creation
    │
    ├─── Recipient Selection Methods:
    │    ├── Manual Selection (Checkboxes)
    │    ├── Bulk by Location
    │    ├── Bulk by Type (Exhibitor/Visitor)
    │    └── CSV Upload
    │
    └─── Store in campaign_recipients:
         ├── campaign_id
         ├── contact_id
         ├── phone (for quick lookup)
         ├── status: 'pending'
         └── created_at

Sending Process
    │
    ├─── For each recipient:
    │    ├── Dispatch SendCampaignJob
    │    ├── Job fetches recipient contact details
    │    ├── Build message payload
    │    ├── Send to API
    │    ├── Update status:
    │    │   ├── 'sent' on success
    │    │   └── 'failed' on error (store error message)
    │    └── Log progress
    │
    └─── Campaign status updated to 'sent' when all processed
```

================================================================
## 7. WhatsApp Template Management Flow
================================================================

### 7.1 Template Structure

**Template Components:**
```
Template
├── Header (optional):
│   ├── Format: TEXT/IMAGE/VIDEO/DOCUMENT
│   └── Content: Static or dynamic
│
├── Body (required):
│   ├── Text with variables: {{1}}, {{2}}, {{3}}
│   └── Variables replaced at send time
│
├── Footer (optional):
│   └── Static text only (no variables)
│
└── Buttons (optional):
    ├── Quick Reply
    ├── Call-to-Action (Phone/URL)
    └── List Picker
```

### 7.2 Template Caching Strategy
```
API Request Flow:
┌──────────────────────┐
│  Request Templates   │
└──────────┬───────────┘
           │
           ▼
┌──────────────────────┐
│  Check Cache         │
│  (TTL: 5 minutes)    │
└──────────┬───────────┘
           │
           ├─── Cache Hit ───► Return cached data
           │
           └─── Cache Miss ───► Fetch from API
                                 │
                                 ├─── Success ───► Cache → Return
                                 │
                                 └─── Error ───► Return error
                                                (Keep old cache if exists)

Cache Invalidation:
- When template created: Clear cache
- When template deleted: Clear cache
- When refresh button clicked: Clear cache
- Automatic: After 5 minutes
```

### 7.3 Dynamic Payload Generation

**During Campaign Sending:**
```
1. Campaign has template_name stored
2. SendCampaignJob fetches template from cache
3. Build components array dynamically:
   
   Header Component:
   ├── If IMAGE format:
   │   ├── Use campaign.image if available
   │   └── Fallback to default image URL
   ├── If TEXT format:
   │   └── Use template header text
   └── If VIDEO/DOCUMENT:
       └── Use campaign media URL
   
   Body Component:
   ├── Parse body text for variables: {{1}}, {{2}}
   ├── Map campaign data:
   │   ├── {{1}} → campaign.name
   │   ├── {{2}} → campaign.message
   │   └── Additional variables as needed
   └── Build parameters array
   
4. Construct final payload:
   {
     "messaging_product": "whatsapp",
     "recipient_type": "individual",
     "to": "{phone_number}",
     "type": "template",
     "template": {
       "name": "{template_name}",
       "language": {"code": "{template_language}"},
       "components": [
         {"type": "header", "parameters": [...]},
         {"type": "body", "parameters": [...]}
       ]
     }
   }
   
5. Send to WhatsApp API
```

================================================================
## 8. Lead & Follow-up Management Flow
================================================================

### 8.1 Lead Status Workflow

```
┌─────────────────┐
│  New Lead       │
│  (Phone Number) │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Create Follow-up│
│ Status Selection│
└────────┬────────┘
         │
         ├─── Busy ───► ┌──────────────────┐
         │              │ Schedule Follow-up│
         │              │ (Date + Time)     │
         │              └───────────────────┘
         │
         ├─── Interested ───► ┌─────────────────────┐
         │                    │ Continue Follow-up   │
         │                    │ (Can convert to      │
         │                    │  Materialised)       │
         │                    └─────────────────────┘
         │
         └─── Materialised ───► ┌─────────────────────┐
                                │ Create Booking      │
                                │ - Location          │
                                │ - Table             │
                                │ - Price             │
                                │ - Payment Status    │
                                └─────────────────────┘
```

### 8.2 Follow-up Scheduling

**Busy Status Flow:**
```
1. Lead marked as 'busy'
2. Employee schedules next follow-up:
   ├── Next Follow-up Date (required)
   ├── Next Follow-up Time (required)
   └── Comment: Why busy, what to discuss next
3. System stores in follow_ups table
4. Employee can view scheduled follow-ups:
   └── Filter by date range
   └── Get reminder notifications
```

**Interested Status Flow:**
```
1. Lead marked as 'interested'
2. Employee adds comment about interest
3. Can optionally schedule follow-up
4. Employee nurtures lead:
   └── Send more information
   └── Answer questions
   └── Move towards booking
```

**Materialised Status Flow:**
```
1. Lead converts to booking
2. Follow-up status set to 'materialised'
3. System automatically creates Booking record:
   ├── Link to follow-up via phone number
   ├── Store booking details
   └── Track payment status
4. Lead is now a customer
```

### 8.3 Follow-up History Tracking

**View All Follow-ups for a Lead:**
```
1. Click on Lead (identified by phone number)
2. System queries follow_ups table:
   └── WHERE phone = '{phone_number}'
   └── ORDER BY created_at DESC
3. Display:
   ├── Follow-up Date/Time
   ├── Status (badge)
   ├── Employee Name
   ├── Comment
   ├── Next Follow-up Date (if scheduled)
   └── Actions (Edit/Delete)
```

================================================================
## 9. Booking Management Flow
================================================================

### 9.1 Booking Creation Flow

**From Materialised Lead:**
```
1. Employee creates follow-up with status='materialised'
2. FollowUpService@create detects materialised status
3. Automatically creates Booking:
   ├── ID: ULID (unique)
   ├── Phone: From follow-up
   ├── Booking Date: Selected date
   ├── Booking Location: Selected location ID
   ├── Table Number: Selected table
   ├── Price: Table price or custom
   ├── Amount Status: paid/partial/unpaid
   ├── Amount Paid: Actual amount received
   └── Employee ID: Current employee
4. Booking stored in 'bookings' table
5. Link to location & table for availability tracking
```

**Table Availability Check:**
```
1. Employee selects location
2. System queries:
   └── Get all tables for location
   └── Check existing bookings for date
   └── Filter available tables
3. Display available tables:
   ├── Table Number
   ├── Capacity
   ├── Price
   └── Status (available/booked)
4. Employee selects table → System reserves it
```

### 9.2 Booking Management

**View Bookings:**
```
- Filter by:
  ├── Location
  ├── Date Range
  ├── Payment Status
  └── Employee
- Display:
  ├── Booking ID
  ├── Customer Phone/Name
  ├── Location & Table
  ├── Booking Date
  ├── Price & Payment Status
  └── Actions (Edit/Cancel)
```

**Payment Tracking:**
```
Payment Statuses:
- paid: Full amount received
- partial: Partial payment received (amount_paid < price)
- unpaid: No payment received yet

Flow:
1. Employee creates booking → Amount Status = 'unpaid'
2. Customer makes payment → Employee updates:
   ├── Amount Status → 'partial' or 'paid'
   └── Amount Paid → Actual amount received
3. System tracks:
   ├── Total Bookings
   ├── Total Revenue (sum of amount_paid)
   └── Outstanding Amount (price - amount_paid)
```

================================================================
## 10. Location Management Flow
================================================================

### 10.1 Location Structure

```
Location (location_mngts)
├── Basic Info:
│   ├── Name
│   ├── Address
│   ├── City, State, Country
│   └── Contact Info
│
└── Tables (location_mngt_table_details)
    ├── Table Number/Name
    ├── Capacity
    ├── Price
    └── Status (Active/Inactive)
```

### 10.2 Location Workflow

**Location Creation:**
```
1. Admin creates location
2. Store in location_mngts table
3. Location available for:
   ├── Contact assignment
   ├── Booking selection
   └── Campaign filtering
```

**Table Management:**
```
1. Admin views location
2. Manage tables:
   ├── Add Tables:
   │   ├── Enter table details
   │   ├── Set capacity
   │   └── Set pricing
   ├── Edit Table:
   │   └── Update capacity, price, status
   └── Delete Table:
       └── Check for existing bookings first
3. Tables used in booking system:
   └── Availability check during booking
   └── Price auto-populated from table
```

================================================================
## 11. Dashboard Overview
================================================================

### 11.1 Admin Dashboard

**Statistics Cards:**
```
┌─────────────────────────────────────────────┐
│  Dashboard Overview                         │
├─────────────────────────────────────────────┤
│                                             │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐   │
│  │Employees │ │ Contacts │ │Campaigns │   │
│  │  Total   │ │  Total   │ │  Total   │   │
│  └──────────┘ └──────────┘ └──────────┘   │
│                                             │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐   │
│  │Templates │ │Locations │ │Follow-ups│   │
│  │  Total   │ │  Total   │ │  Total   │   │
│  └──────────┘ └──────────┘ └──────────┘   │
│                                             │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐   │
│  │ Bookings │ │ Exhibitors│ │ Visitors │   │
│  │  Total   │ │  Total   │ │  Total   │   │
│  └──────────┘ └──────────┘ └──────────┘   │
│                                             │
└─────────────────────────────────────────────┘

Recent Activity:
├── Recent Employees (Last 5)
├── Recent Exhibitors (Last 3)
├── Recent Visitors (Last 3)
└── Recent Campaigns (Last 5)

Charts & Analytics:
├── Campaign Status Distribution
├── Contact Type Distribution
├── Employee Status Distribution
└── Monthly Trends (if applicable)
```

### 11.2 Employee Dashboard

**Statistics Cards:**
```
┌─────────────────────────────────────────────┐
│  Employee Dashboard                         │
├─────────────────────────────────────────────┤
│                                             │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐   │
│  │ My Leads │ │ Pending  │ │ Bookings │   │
│  │  Total   │ │Follow-ups│ │ Completed│   │
│  └──────────┘ └──────────┘ └──────────┘   │
│                                             │
└─────────────────────────────────────────────┘

Quick Actions:
├── Add New Lead
├── View Follow-ups
└── Create Booking
```

================================================================
## 12. Data Flow & Relationships
================================================================

### 12.1 Database Relationships

```
users (Admin & Employees)
│
├─── One-to-Many ───► follow_ups (employee_id)
│
└─── One-to-Many ───► bookings (employee_id)

contacts (Exhibitors & Visitors)
│
├─── Many-to-Many ───► campaigns (via campaign_recipients)
│
└─── One-to-One ───► follow_ups (via phone number)

campaigns
│
├─── One-to-Many ───► campaign_recipients
│
└─── Belongs-to ───► template (via template_name)

campaign_recipients
│
├─── Belongs-to ───► campaigns
│
└─── Belongs-to ───► contacts

follow_ups
│
├─── Belongs-to ───► users (employee_id)
│
└─── One-to-One ───► bookings (via phone number)

bookings
│
├─── Belongs-to ───► users (employee_id)
│
├─── Belongs-to ───► location_mngts (booking_location)
│
└─── Belongs-to ───► location_mngt_table_details (table_no)

location_mngts
│
└─── One-to-Many ───► location_mngt_table_details

whatsapp_templates (Cache)
│
└─── Referenced-by ───► campaigns (template_name)
```

### 12.2 Key Data Flows

**Campaign Sending Flow:**
```
Campaign → campaign_recipients → SendCampaignJob → 
WhatsApp API → Update recipient status → 
Update campaign status
```

**Lead to Booking Flow:**
```
Contact → Follow-up (busy/interested) → 
Follow-up (materialised) → Booking → 
Location & Table Assignment
```

**Template Usage Flow:**
```
Template Created → Meta API Approval → 
Cached in System → Selected in Campaign → 
Dynamic Payload Generation → WhatsApp API
```

### 12.3 External API Integration

**WhatsApp Business API:**
```
Endpoints Used:
1. GET /{version}/{waba_id}/message_templates
   - Fetch all templates
   - Used in: Template listing

2. POST /{version}/{waba_id}/message_templates
   - Create new template
   - Used in: Template creation

3. DELETE /{version}/{waba_id}/message_templates/{template_id}
   - Delete template
   - Used in: Template deletion

4. POST /{version}/{phone_number_id}/messages
   - Send template message
   - Used in: Campaign sending (SendCampaignJob)

Authentication:
- Bearer Token: From WHATSAPP_BEARER_TOKEN env
- API Key: From WHATSAPP_API_KEY env (fallback)

Configuration (config/services.php):
- base_url: https://meta.webpayservices.in
- api_version: V23.0
- phone_number_id: For sending messages
- waba_id: For template management
```

================================================================
## End of Documentation
================================================================

**Version:** 1.0
**Last Updated:** January 2026
**Maintained By:** Development Team

**Note:** This documentation reflects the current state of the Paridharan CRM system. 
For implementation details, refer to the source code in respective controller and service files.

