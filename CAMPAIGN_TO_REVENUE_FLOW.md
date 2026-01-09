# Campaign to Revenue Flow - Complete Connection

## Overview
This document explains the complete flow from Campaign → Campaign Recipient → Conversation → Follow-up → Booking → Revenue, with all connections properly established.

---

## Complete Flow Diagram

```
┌─────────────────────────────────────────────────────────────────────┐
│                    CAMPAIGN CREATED & SENT                          │
│  Campaign: "Exhibition Invitation"                                   │
│  Type: WhatsApp/Email/SMS                                            │
└───────────────────────┬─────────────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────────────┐
│              CAMPAIGN RECIPIENTS CREATED                            │
│  CampaignRecipient Records:                                          │
│  - campaign_id: Campaign ID                                         │
│  - contact_id: Visitor/Exhibitor Contact ID                        │
│  - phone/email: Contact details                                     │
│  - status: 'sent' (after message sent)                             │
└───────────────────────┬─────────────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────────────┐
│         RECIPIENT RESPONDS / BECOMES INTERESTED                     │
│  Employee creates Follow-up:                                        │
│  - phone: Recipient's phone                                         │
│  - status: 'busy' / 'interested' / 'materialised'                  │
│  - campaign_id: Campaign ID (auto-detected from recipient)          │
│  - visitor_id: Contact ID (auto-detected from phone)               │
│  - exhibitor_id: Selected company                                  │
│  - location_id, table_id: Selected location/stall                  │
└───────────────────────┬─────────────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────────────┐
│           CONVERSATION AUTOMATICALLY CREATED                        │
│  Conversation Record:                                               │
│  - campaign_recipient_id: Links to exact recipient                 │
│  - campaign_id: Campaign ID                                         │
│  - exhibitor_id: Company having conversation                        │
│  - visitor_id: Visitor/Lead contact                                 │
│  - employee_id: Employee handling                                  │
│  - location_id, table_id: Where conversation happened               │
│  - outcome: 'busy' / 'interested' / 'materialised'                 │
│  - follow_up_id: Linked follow-up                                  │
│  - notes: Conversation details                                     │
└───────────────────────┬─────────────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────────────┐
│         IF MATERIALISED → BOOKING CREATED                           │
│  Booking Record:                                                    │
│  - campaign_id: Campaign ID (persists from follow-up)              │
│  - exhibitor_id: Company                                            │
│  - visitor_id: Visitor contact                                      │
│  - location_id, table_id: Booking location/stall                    │
│  - price, amount_paid: Revenue amount                               │
│  - amount_status: 'paid' / 'partial' / 'unpaid'                    │
└───────────────────────┬─────────────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────────────┐
│      CONVERSATION UPDATED WITH BOOKING                              │
│  Conversation Record:                                               │
│  - booking_id: Linked booking                                      │
│  - outcome: 'materialised'                                          │
│  - notes: "Booking created - Amount: ₹X"                            │
└───────────────────────┬─────────────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────────────┐
│              REVENUE ATTRIBUTED TO CAMPAIGN                          │
│  Campaign Analytics:                                                │
│  - Total Recipients: Count of campaign_recipients                   │
│  - Recipients with Conversations: Count via conversations           │
│  - Recipients with Bookings: Count via materialised conversations   │
│  - Total Revenue: SUM(bookings.amount_paid WHERE campaign_id)        │
│  - Conversion Rates:                                                 │
│    • Recipient → Lead: (conversations / recipients) * 100          │
│    • Recipient → Booking: (bookings / recipients) * 100              │
│    • Lead → Booking: (bookings / leads) * 100                        │
└─────────────────────────────────────────────────────────────────────┘
```

---

## Database Relationships

### Key Foreign Keys

1. **conversations.campaign_recipient_id** → **campaign_recipients.id**
   - Direct link to the exact recipient who received the campaign
   - This is the PRIMARY connection point

2. **conversations.campaign_id** → **campaigns.id**
   - Also links to campaign for quick filtering

3. **conversations.follow_up_id** → **follow_ups.id**
   - Links conversation to follow-up record

4. **conversations.booking_id** → **bookings.id**
   - Links conversation to booking (when materialised)

5. **follow_ups.campaign_id** → **campaigns.id**
   - Tracks which campaign generated the lead

6. **bookings.campaign_id** → **campaigns.id**
   - Tracks which campaign generated the booking/revenue

---

## Service Layer Flow

### 1. FollowUpService::create()
```php
// When follow-up is created:
1. Find campaign_recipient by campaign_id + visitor_id/phone
2. Create follow-up with campaign_id
3. Create conversation (auto-links campaign_recipient)
4. If materialised → create booking → update conversation
```

### 2. ConversationService::createFromFollowUp()
```php
// Automatically:
1. Finds campaign_recipient using campaign_id + visitor_id
2. Creates conversation with campaign_recipient_id
3. Links to follow_up_id
```

### 3. ConversationService::createFromBooking()
```php
// When booking is created:
1. Finds campaign_recipient using campaign_id + visitor_id
2. Creates/updates conversation with:
   - campaign_recipient_id
   - booking_id
   - outcome: 'materialised'
```

### 4. CampaignAnalyticsService::getCampaignRevenue()
```php
// Calculates:
1. Recipients with conversations (via campaign_recipient_id)
2. Recipients with bookings (via conversations → booking)
3. Total revenue (via bookings → amount_paid)
4. All conversion percentages
```

---

## UI Flow

### Campaign Show Page
- **Recipients Table** shows:
  - Message Status (sent/delivered/pending)
  - Conversation Status (busy/interested/materialised)
  - Booking Status (Booked/No Booking)
  - Revenue (₹X.XX per recipient)

### Campaign Analytics
- **Conversion Funnel**:
  - Recipients → Leads (conversations)
  - Recipients → Bookings
  - Leads → Bookings
- **Revenue Breakdown**:
  - By Exhibitor
  - By Payment Status (paid/partial/unpaid)

### Company Dashboard
- **Conversation Timeline** shows:
  - Campaign source (if from campaign)
  - Campaign Recipient ID (traceable)
  - Full flow: Campaign → Conversation → Booking → Revenue

---

## Key Features

### ✅ Automatic Campaign Recipient Linking
- When follow-up is created with `campaign_id`, system automatically finds and links `campaign_recipient_id`
- Works by matching:
  - `campaign_id` + `visitor_id` (contact_id)
  - OR `campaign_id` + `phone`

### ✅ Complete Traceability
- Every conversation can be traced back to:
  - Exact campaign recipient
  - Campaign
  - Follow-up
  - Booking (if materialised)
  - Revenue

### ✅ Revenue Attribution
- Revenue is accurately attributed to:
  - Campaign (via campaign_id)
  - Campaign Recipient (via campaign_recipient_id → conversations → bookings)
  - Exhibitor (via exhibitor_id)
  - Location/Stall (via location_id, table_id)

### ✅ Conversion Tracking
- Track conversion at every stage:
  - Recipient → Lead (conversation created)
  - Recipient → Booking (materialised)
  - Lead → Booking (follow-up → booking)

---

## Example Query: Get Revenue from Campaign Recipient

```php
// Get all revenue from a specific campaign recipient
$recipient = CampaignRecipient::find($recipientId);

$revenue = $recipient->conversations()
    ->where('outcome', 'materialised')
    ->whereHas('booking')
    ->with('booking')
    ->get()
    ->sum(function($conversation) {
        return $conversation->booking->amount_paid ?? 0;
    });
```

---

## Example Query: Get Campaign Funnel

```php
$campaign = Campaign::find($campaignId);

$funnel = [
    'total_recipients' => $campaign->recipients()->count(),
    'recipients_with_conversations' => $campaign->recipients()
        ->whereHas('conversations')
        ->count(),
    'recipients_with_bookings' => $campaign->recipients()
        ->whereHas('conversations', function($q) {
            $q->where('outcome', 'materialised')
              ->whereHas('booking');
        })
        ->count(),
    'total_revenue' => $campaign->bookings()
        ->withRevenue()
        ->sum('amount_paid'),
];
```

---

## Data Integrity

### Automatic Linking Logic

1. **When Follow-up Created:**
   ```php
   if (campaign_id && visitor_id) {
       campaign_recipient = CampaignRecipient::where('campaign_id', campaign_id)
           ->where('contact_id', visitor_id)
           ->first();
   }
   ```

2. **When Conversation Created:**
   ```php
   if (campaign_recipient found) {
       conversation->campaign_recipient_id = campaign_recipient->id;
   }
   ```

3. **When Booking Created:**
   ```php
   // Booking inherits campaign_id from follow-up
   // Conversation links to booking
   // Revenue is sum of all bookings with campaign_id
   ```

---

## Summary

✅ **Campaign Recipients** are the starting point  
✅ **Conversations** link recipients to follow-ups and bookings  
✅ **Follow-ups** track lead progression  
✅ **Bookings** track revenue  
✅ **Revenue** is accurately attributed to campaigns and recipients  

The complete flow is now connected and traceable from campaign send to revenue generation!

