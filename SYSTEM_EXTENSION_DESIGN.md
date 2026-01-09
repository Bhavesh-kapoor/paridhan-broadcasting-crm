# Paridharan CRM - System Extension Design
## Company-wise Conversation Tracking & Revenue Attribution

**Version:** 1.0  
**Date:** January 2026  
**Architect:** Senior Laravel Architect

---

## 📋 TABLE OF CONTENTS

1. [Database Schema Changes](#database-schema-changes)
2. [Model Relationships](#model-relationships)
3. [Service Layer Design](#service-layer-design)
4. [Data Flow Architecture](#data-flow-architecture)
5. [Example Eloquent Queries](#example-eloquent-queries)
6. [Migration Strategy](#migration-strategy)
7. [Backward Compatibility](#backward-compatibility)

---

## 1. DATABASE SCHEMA CHANGES

### 1.1 NEW TABLE: `conversations`

**Purpose:** Timeline/log system for company-wise conversation tracking

```php
Schema::create('conversations', function (Blueprint $table) {
    $table->ulid('id')->primary();
    
    // Company Context (Exhibitor/Company)
    $table->ulid('exhibitor_id')->comment('The company/exhibitor having the conversation');
    
    // Lead/Visitor Context
    $table->ulid('visitor_id')->nullable()->comment('Visitor/Lead contact ID');
    $table->string('visitor_phone')->nullable()->comment('Phone if visitor not in contacts');
    
    // Employee Context
    $table->string('employee_id')->comment('Employee who handled conversation');
    
    // Location & Stall Context
    $table->unsignedBigInteger('location_id')->nullable()->comment('Location where conversation happened');
    $table->unsignedBigInteger('table_id')->nullable()->comment('Stall/Table number');
    
    // Campaign Attribution
    $table->ulid('campaign_id')->nullable()->comment('Source campaign if lead came from campaign');
    
    // Conversation Details
    $table->enum('outcome', ['busy', 'interested', 'materialised'])->comment('Conversation outcome');
    $table->text('notes')->nullable()->comment('Conversation notes/comments');
    $table->timestamp('conversation_date')->useCurrent()->comment('When conversation happened');
    
    // Related Records
    $table->unsignedBigInteger('follow_up_id')->nullable()->comment('Linked follow-up if exists');
    $table->ulid('booking_id')->nullable()->comment('Linked booking if materialised');
    
    $table->timestamps();
    
    // Indexes for Performance
    $table->index('exhibitor_id');
    $table->index('visitor_id');
    $table->index('visitor_phone');
    $table->index('employee_id');
    $table->index('location_id');
    $table->index('table_id');
    $table->index('campaign_id');
    $table->index('conversation_date');
    $table->index('outcome');
    $table->index('follow_up_id');
    $table->index('booking_id');
    
    // Composite Indexes for Common Queries
    $table->index(['exhibitor_id', 'conversation_date']);
    $table->index(['campaign_id', 'outcome']);
    $table->index(['location_id', 'table_id', 'conversation_date']);
    
    // Foreign Keys
    $table->foreign('exhibitor_id')->references('id')->on('contacts')->onDelete('cascade');
    $table->foreign('visitor_id')->references('id')->on('contacts')->onDelete('set null');
    $table->foreign('employee_id')->references('id')->on('users')->onDelete('restrict');
    $table->foreign('location_id')->references('id')->on('location_mngt')->onDelete('set null');
    $table->foreign('table_id')->references('id')->on('location_mngt_table_details')->onDelete('set null');
    $table->foreign('campaign_id')->references('id')->on('campaigns')->onDelete('set null');
    $table->foreign('follow_up_id')->references('id')->on('follow_ups')->onDelete('set null');
    $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('set null');
});
```

**Notes:**
- Uses ULID for scalable primary key
- `visitor_phone` allows tracking even if visitor not in contacts table
- Multiple nullable fields for backward compatibility
- Composite indexes for common query patterns
- Foreign keys with appropriate cascade/restrict behaviors

---

### 1.2 MODIFY TABLE: `follow_ups`

**Purpose:** Add location, table, and campaign context to follow-ups

```php
Schema::table('follow_ups', function (Blueprint $table) {
    // Company & Visitor Context
    $table->ulid('exhibitor_id')->nullable()->after('phone')->comment('Company/Exhibitor context');
    $table->ulid('visitor_id')->nullable()->after('exhibitor_id')->comment('Visitor/Lead contact ID');
    
    // Location & Stall Context
    $table->unsignedBigInteger('location_id')->nullable()->after('visitor_id');
    $table->unsignedBigInteger('table_id')->nullable()->after('location_id');
    
    // Campaign Attribution
    $table->ulid('campaign_id')->nullable()->after('table_id')->comment('Source campaign ID');
    
    // Conversation Link
    $table->ulid('conversation_id')->nullable()->after('campaign_id')->comment('Linked conversation');
    
    // Indexes
    $table->index('exhibitor_id');
    $table->index('visitor_id');
    $table->index('location_id');
    $table->index('table_id');
    $table->index('campaign_id');
    $table->index('conversation_id');
    
    // Composite Index
    $table->index(['exhibitor_id', 'status', 'created_at']);
    
    // Foreign Keys
    $table->foreign('exhibitor_id')->references('id')->on('contacts')->onDelete('cascade');
    $table->foreign('visitor_id')->references('id')->on('contacts')->onDelete('set null');
    $table->foreign('location_id')->references('id')->on('location_mngt')->onDelete('set null');
    $table->foreign('table_id')->references('id')->on('location_mngt_table_details')->onDelete('set null');
    $table->foreign('campaign_id')->references('id')->on('campaigns')->onDelete('set null');
    $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('set null');
});
```

**Notes:**
- All new columns are nullable for backward compatibility
- Links to both exhibitor and visitor contacts
- Maintains existing `phone` field for backward compatibility

---

### 1.3 MODIFY TABLE: `bookings`

**Purpose:** Add proper foreign keys, campaign attribution, and company context

```php
Schema::table('bookings', function (Blueprint $table) {
    // Change existing string fields to proper foreign keys
    // Note: This is a data migration - convert existing data first
    
    // Company & Visitor Context
    $table->ulid('exhibitor_id')->nullable()->after('phone')->comment('Company/Exhibitor');
    $table->ulid('visitor_id')->nullable()->after('exhibitor_id')->comment('Visitor/Lead contact');
    
    // Convert location from string to FK (requires data migration)
    $table->unsignedBigInteger('location_id')->nullable()->after('visitor_id');
    
    // Convert table_no from string to FK (requires data migration)
    $table->unsignedBigInteger('table_id')->nullable()->after('location_id');
    
    // Campaign Attribution
    $table->ulid('campaign_id')->nullable()->after('table_id')->comment('Source campaign');
    
    // Conversation Link
    $table->ulid('conversation_id')->nullable()->after('campaign_id');
    
    // Keep old fields temporarily for migration period (can drop later)
    // $table->string('booking_location', 255)->nullable()->change();
    // $table->string('table_no')->nullable()->change();
    
    // Proper type for price and amount_paid
    $table->decimal('price', 15, 2)->nullable()->change();
    $table->decimal('amount_paid', 15, 2)->nullable()->change();
    
    // Indexes
    $table->index('exhibitor_id');
    $table->index('visitor_id');
    $table->index('location_id');
    $table->index('table_id');
    $table->index('campaign_id');
    $table->index('conversation_id');
    $table->index(['campaign_id', 'amount_paid']);
    
    // Foreign Keys
    $table->foreign('exhibitor_id')->references('id')->on('contacts')->onDelete('cascade');
    $table->foreign('visitor_id')->references('id')->on('contacts')->onDelete('set null');
    $table->foreign('location_id')->references('id')->on('location_mngt')->onDelete('restrict');
    $table->foreign('table_id')->references('id')->on('location_mngt_table_details')->onDelete('restrict');
    $table->foreign('campaign_id')->references('id')->on('campaigns')->onDelete('set null');
    $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('set null');
});
```

**Notes:**
- Data migration required for `booking_location` and `table_no` string-to-FK conversion
- Price fields converted to decimal for proper calculations
- Old string fields kept during migration period for backward compatibility

---

### 1.4 MODIFY TABLE: `campaigns`

**Purpose:** Add revenue tracking fields (calculated, not stored for accuracy)

```php
// No schema changes needed - revenue calculated on-the-fly
// However, we can add cache fields if needed for performance:

Schema::table('campaigns', function (Blueprint $table) {
    // Optional: Cache fields for quick access (updated via events/jobs)
    $table->integer('total_leads_generated')->default(0)->after('sent_at');
    $table->integer('total_bookings_created')->default(0)->after('total_leads_generated');
    $table->decimal('total_revenue', 15, 2)->default(0)->after('total_bookings_created');
    
    // Indexes
    $table->index('total_revenue');
    
    // Note: These are denormalized cache fields
    // Real values always calculated from source data
});
```

**Notes:**
- Cache fields optional - can be calculated on-the-fly
- Use Laravel events to update cache when bookings created
- Always verify with source data for accuracy

---

## 2. MODEL RELATIONSHIPS

### 2.1 NEW MODEL: `Conversation`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Conversation extends Model
{
    use HasUlids;

    protected $fillable = [
        'exhibitor_id',
        'visitor_id',
        'visitor_phone',
        'employee_id',
        'location_id',
        'table_id',
        'campaign_id',
        'outcome',
        'notes',
        'conversation_date',
        'follow_up_id',
        'booking_id',
    ];

    protected $casts = [
        'conversation_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the exhibitor (company) for this conversation
     */
    public function exhibitor(): BelongsTo
    {
        return $this->belongsTo(Contacts::class, 'exhibitor_id');
    }

    /**
     * Get the visitor/lead for this conversation
     */
    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Contacts::class, 'visitor_id');
    }

    /**
     * Get the employee who handled the conversation
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    /**
     * Get the location where conversation happened
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(LocationMngt::class, 'location_id');
    }

    /**
     * Get the table/stall where conversation happened
     */
    public function table(): BelongsTo
    {
        return $this->belongsTo(LocationMngtTableDetail::class, 'table_id');
    }

    /**
     * Get the source campaign
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

    /**
     * Get the linked follow-up
     */
    public function followUp(): BelongsTo
    {
        return $this->belongsTo(FollowUp::class, 'follow_up_id');
    }

    /**
     * Get the linked booking
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    /**
     * Scope: Get conversations for a specific exhibitor
     */
    public function scopeForExhibitor($query, $exhibitorId)
    {
        return $query->where('exhibitor_id', $exhibitorId);
    }

    /**
     * Scope: Get conversations from a campaign
     */
    public function scopeFromCampaign($query, $campaignId)
    {
        return $query->where('campaign_id', $campaignId);
    }

    /**
     * Scope: Get conversations by outcome
     */
    public function scopeByOutcome($query, $outcome)
    {
        return $query->where('outcome', $outcome);
    }

    /**
     * Scope: Get recent conversations
     */
    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('conversation_date', 'desc')->limit($limit);
    }
}
```

---

### 2.2 UPDATED MODEL: `FollowUp`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FollowUp extends Model
{
    protected $fillable = [
        'phone',
        'status',
        'next_followup_date',
        'next_followup_time',
        'comment',
        'employee_id',
        // New fields
        'exhibitor_id',
        'visitor_id',
        'location_id',
        'table_id',
        'campaign_id',
        'conversation_id',
    ];

    protected $casts = [
        'next_followup_date' => 'date',
        'next_followup_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Existing relationships
    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    // NEW relationships
    public function exhibitor(): BelongsTo
    {
        return $this->belongsTo(Contacts::class, 'exhibitor_id');
    }

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Contacts::class, 'visitor_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(LocationMngt::class, 'location_id');
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(LocationMngtTableDetail::class, 'table_id');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class, 'conversation_id');
    }

    /**
     * Get contact via phone (backward compatibility)
     */
    public function contact()
    {
        return Contacts::where('phone', $this->phone)->first();
    }

    /**
     * Scope: Get follow-ups for an exhibitor
     */
    public function scopeForExhibitor($query, $exhibitorId)
    {
        return $query->where('exhibitor_id', $exhibitorId);
    }

    /**
     * Scope: Get follow-ups from a campaign
     */
    public function scopeFromCampaign($query, $campaignId)
    {
        return $query->where('campaign_id', $campaignId);
    }
}
```

---

### 2.3 UPDATED MODEL: `Booking`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'phone',
        'booking_date',
        'booking_location', // Keep for backward compatibility
        'table_no', // Keep for backward compatibility
        'price',
        'amount_status',
        'amount_paid',
        'employee_id',
        // New fields
        'exhibitor_id',
        'visitor_id',
        'location_id',
        'table_id',
        'campaign_id',
        'conversation_id',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'price' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // NEW relationships
    public function exhibitor(): BelongsTo
    {
        return $this->belongsTo(Contacts::class, 'exhibitor_id');
    }

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Contacts::class, 'visitor_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(LocationMngt::class, 'location_id');
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(LocationMngtTableDetail::class, 'table_id');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class, 'conversation_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    /**
     * Get contact via phone (backward compatibility)
     */
    public function contact()
    {
        return Contacts::where('phone', $this->phone)->first();
    }

    /**
     * Scope: Get bookings for an exhibitor
     */
    public function scopeForExhibitor($query, $exhibitorId)
    {
        return $query->where('exhibitor_id', $exhibitorId);
    }

    /**
     * Scope: Get bookings from a campaign
     */
    public function scopeFromCampaign($query, $campaignId)
    {
        return $query->where('campaign_id', $campaignId);
    }

    /**
     * Scope: Get bookings with revenue
     */
    public function scopeWithRevenue($query)
    {
        return $query->whereNotNull('amount_paid')->where('amount_paid', '>', 0);
    }
}
```

---

### 2.4 UPDATED MODEL: `Campaign`

```php
<?php

namespace App\Models;

// ... existing code ...

class Campaign extends Model
{
    // ... existing code ...

    // NEW relationships
    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    public function followUps()
    {
        return $this->hasMany(FollowUp::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get total leads generated from this campaign
     */
    public function getTotalLeadsGeneratedAttribute()
    {
        return $this->followUps()->count();
    }

    /**
     * Get total bookings created from this campaign
     */
    public function getTotalBookingsCreatedAttribute()
    {
        return $this->bookings()->count();
    }

    /**
     * Get total revenue from this campaign
     */
    public function getTotalRevenueAttribute()
    {
        return $this->bookings()
            ->withRevenue()
            ->sum('amount_paid');
    }

    /**
     * Get conversion percentage (bookings / leads)
     */
    public function getConversionPercentageAttribute()
    {
        $leads = $this->total_leads_generated;
        if ($leads === 0) {
            return 0;
        }
        return ($this->total_bookings_created / $leads) * 100;
    }

    /**
     * Scope: Get campaigns with revenue
     */
    public function scopeWithRevenue($query)
    {
        return $query->has('bookings');
    }
}
```

---

### 2.5 UPDATED MODEL: `Contacts` (Exhibitor)

```php
<?php

namespace App\Models;

// ... existing code ...

class Contacts extends Model
{
    // ... existing code ...

    // NEW relationships (only for exhibitors)
    public function conversations()
    {
        return $this->hasMany(Conversation::class, 'exhibitor_id');
    }

    public function followUps()
    {
        return $this->hasMany(FollowUp::class, 'exhibitor_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'exhibitor_id');
    }

    /**
     * Get recent conversations (last 10)
     */
    public function recentConversations()
    {
        return $this->conversations()->recent(10);
    }

    /**
     * Get total leads for this exhibitor
     */
    public function getTotalLeadsAttribute()
    {
        return $this->followUps()->count();
    }

    /**
     * Get total bookings for this exhibitor
     */
    public function getTotalBookingsAttribute()
    {
        return $this->bookings()->count();
    }

    /**
     * Get total revenue for this exhibitor
     */
    public function getTotalRevenueAttribute()
    {
        return $this->bookings()
            ->withRevenue()
            ->sum('amount_paid');
    }

    /**
     * Get campaigns contributing to this exhibitor's revenue
     */
    public function contributingCampaigns()
    {
        return Campaign::whereHas('bookings', function ($query) {
            $query->where('exhibitor_id', $this->id);
        })->get();
    }
}
```

---

## 3. SERVICE LAYER DESIGN

### 3.1 NEW SERVICE: `ConversationService`

```php
<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\FollowUp;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;

class ConversationService
{
    /**
     * Create a conversation record
     * 
     * This should be called when:
     * - Follow-up is created
     * - Booking is created
     * - Manual conversation entry
     */
    public function create(array $data): Conversation
    {
        DB::beginTransaction();
        try {
            $conversation = Conversation::create([
                'exhibitor_id' => $data['exhibitor_id'],
                'visitor_id' => $data['visitor_id'] ?? null,
                'visitor_phone' => $data['visitor_phone'] ?? null,
                'employee_id' => $data['employee_id'],
                'location_id' => $data['location_id'] ?? null,
                'table_id' => $data['table_id'] ?? null,
                'campaign_id' => $data['campaign_id'] ?? null,
                'outcome' => $data['outcome'],
                'notes' => $data['notes'] ?? null,
                'conversation_date' => $data['conversation_date'] ?? now(),
                'follow_up_id' => $data['follow_up_id'] ?? null,
                'booking_id' => $data['booking_id'] ?? null,
            ]);

            // Link back to follow-up if provided
            if (isset($data['follow_up_id'])) {
                FollowUp::where('id', $data['follow_up_id'])
                    ->update(['conversation_id' => $conversation->id]);
            }

            // Link back to booking if provided
            if (isset($data['booking_id'])) {
                Booking::where('id', $data['booking_id'])
                    ->update(['conversation_id' => $conversation->id]);
            }

            DB::commit();
            return $conversation->load(['exhibitor', 'visitor', 'employee', 'location', 'table', 'campaign']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get conversation timeline for an exhibitor
     */
    public function getExhibitorTimeline($exhibitorId, array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = Conversation::forExhibitor($exhibitorId)
            ->with(['visitor', 'employee', 'location', 'table', 'campaign', 'followUp', 'booking'])
            ->orderBy('conversation_date', 'desc');

        // Apply filters
        if (isset($filters['outcome'])) {
            $query->byOutcome($filters['outcome']);
        }

        if (isset($filters['campaign_id'])) {
            $query->fromCampaign($filters['campaign_id']);
        }

        if (isset($filters['location_id'])) {
            $query->where('location_id', $filters['location_id']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('conversation_date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('conversation_date', '<=', $filters['date_to']);
        }

        return $query->get();
    }

    /**
     * Get recent conversations for an exhibitor
     */
    public function getRecentConversations($exhibitorId, int $limit = 10)
    {
        return Conversation::forExhibitor($exhibitorId)
            ->recent($limit)
            ->with(['visitor', 'employee', 'location', 'table', 'campaign'])
            ->get();
    }

    /**
     * Create conversation from follow-up
     */
    public function createFromFollowUp(FollowUp $followUp, array $additionalData = []): Conversation
    {
        return $this->create(array_merge([
            'exhibitor_id' => $followUp->exhibitor_id,
            'visitor_id' => $followUp->visitor_id,
            'visitor_phone' => $followUp->phone,
            'employee_id' => $followUp->employee_id,
            'location_id' => $followUp->location_id,
            'table_id' => $followUp->table_id,
            'campaign_id' => $followUp->campaign_id,
            'outcome' => $followUp->status, // Map status to outcome
            'notes' => $followUp->comment,
            'conversation_date' => $followUp->created_at,
            'follow_up_id' => $followUp->id,
        ], $additionalData));
    }

    /**
     * Create conversation from booking
     */
    public function createFromBooking(Booking $booking, array $additionalData = []): Conversation
    {
        return $this->create(array_merge([
            'exhibitor_id' => $booking->exhibitor_id,
            'visitor_id' => $booking->visitor_id,
            'visitor_phone' => $booking->phone,
            'employee_id' => $booking->employee_id,
            'location_id' => $booking->location_id,
            'table_id' => $booking->table_id,
            'campaign_id' => $booking->campaign_id,
            'outcome' => 'materialised',
            'notes' => "Booking created - Table: {$booking->table_no}, Amount: {$booking->price}",
            'conversation_date' => $booking->booking_date,
            'booking_id' => $booking->id,
        ], $additionalData));
    }
}
```

---

### 3.2 UPDATED SERVICE: `FollowUpService`

```php
<?php

namespace App\Services;

use App\Models\FollowUp;
use App\Models\Booking;
use App\Services\ConversationService;
use Illuminate\Support\Str;

class FollowUpService
{
    protected ConversationService $conversationService;

    public function __construct(ConversationService $conversationService)
    {
        $this->conversationService = $conversationService;
    }

    /**
     * Create follow-up with conversation tracking
     */
    public function create(array $data): FollowUp
    {
        DB::beginTransaction();
        try {
            // Create follow-up
            $followUp = FollowUp::create([
                'phone' => $data['hidden_id'], // phone number
                'status' => $data['status'],
                'comment' => $data['comment'],
                'next_followup_date' => $data['next_followup_date'] ?? null,
                'next_followup_time' => $data['next_followup_time'] ?? null,
                'employee_id' => auth()->id(),
                // NEW: Add context fields
                'exhibitor_id' => $data['exhibitor_id'] ?? null,
                'visitor_id' => $data['visitor_id'] ?? null,
                'location_id' => $data['location_id'] ?? null,
                'table_id' => $data['table_id'] ?? null,
                'campaign_id' => $data['campaign_id'] ?? null,
            ]);

            // If Materialised → create booking
            if ($data['status'] === 'materialised') {
                $booking = Booking::create([
                    'id' => (string) Str::ulid(),
                    'phone' => $data['hidden_id'],
                    'booking_date' => $data['booking_date'],
                    'booking_location' => $data['booking_location'] ?? null, // Backward compat
                    'table_no' => $data['table_no'] ?? null, // Backward compat
                    'price' => $data['price'],
                    'amount_status' => $data['amount_status'],
                    'amount_paid' => $data['amount_paid'],
                    'employee_id' => auth()->id(),
                    // NEW: Add context fields
                    'exhibitor_id' => $data['exhibitor_id'] ?? null,
                    'visitor_id' => $data['visitor_id'] ?? null,
                    'location_id' => $data['location_id'] ?? null,
                    'table_id' => $data['table_id'] ?? null,
                    'campaign_id' => $data['campaign_id'] ?? null,
                ]);

                // Create conversation from booking
                $this->conversationService->createFromBooking($booking);
            } else {
                // Create conversation from follow-up
                $this->conversationService->createFromFollowUp($followUp);
            }

            DB::commit();
            return $followUp->load(['exhibitor', 'visitor', 'location', 'table', 'campaign', 'conversation']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    // ... rest of existing methods ...
}
```

---

### 3.3 NEW SERVICE: `CampaignAnalyticsService`

```php
<?php

namespace App\Services;

use App\Models\Campaign;
use Illuminate\Support\Facades\DB;

class CampaignAnalyticsService
{
    /**
     * Get campaign revenue statistics
     */
    public function getCampaignRevenue($campaignId): array
    {
        $campaign = Campaign::findOrFail($campaignId);

        $stats = [
            'campaign_id' => $campaign->id,
            'campaign_name' => $campaign->name,
            'total_messages_sent' => $campaign->recipients()->where('status', 'sent')->count(),
            'total_leads_generated' => $campaign->followUps()->count(),
            'total_bookings_created' => $campaign->bookings()->count(),
            'total_revenue' => $campaign->bookings()->withRevenue()->sum('amount_paid'),
            'conversion_percentage' => 0,
            'revenue_by_status' => [
                'paid' => $campaign->bookings()->where('amount_status', 'paid')->sum('amount_paid'),
                'partial' => $campaign->bookings()->where('amount_status', 'partial')->sum('amount_paid'),
                'unpaid' => $campaign->bookings()->where('amount_status', 'unpaid')->sum('amount_paid'),
            ],
        ];

        // Calculate conversion percentage
        if ($stats['total_leads_generated'] > 0) {
            $stats['conversion_percentage'] = 
                ($stats['total_bookings_created'] / $stats['total_leads_generated']) * 100;
        }

        return $stats;
    }

    /**
     * Get all campaigns with revenue statistics
     */
    public function getAllCampaignsRevenue(): \Illuminate\Database\Eloquent\Collection
    {
        return Campaign::withCount([
            'recipients as messages_sent_count' => function ($query) {
                $query->where('status', 'sent');
            },
            'followUps as leads_count',
            'bookings as bookings_count',
        ])
        ->withSum(['bookings as total_revenue'], 'amount_paid')
        ->get()
        ->map(function ($campaign) {
            $campaign->conversion_percentage = $campaign->leads_count > 0
                ? ($campaign->bookings_count / $campaign->leads_count) * 100
                : 0;
            return $campaign;
        });
    }
}
```

---

### 3.4 NEW SERVICE: `CompanyDashboardService`

```php
<?php

namespace App\Services;

use App\Models\Contacts;
use App\Services\ConversationService;

class CompanyDashboardService
{
    protected ConversationService $conversationService;

    public function __construct(ConversationService $conversationService)
    {
        $this->conversationService = $conversationService;
    }

    /**
     * Get company dashboard data for an exhibitor
     */
    public function getCompanyDashboard($exhibitorId): array
    {
        $exhibitor = Contacts::findOrFail($exhibitorId);

        if (!$exhibitor->isExhibitor()) {
            throw new \Exception('Contact must be an exhibitor');
        }

        return [
            'exhibitor' => $exhibitor,
            'recent_conversations' => $this->conversationService->getRecentConversations($exhibitorId, 10),
            'total_leads' => $exhibitor->followUps()->count(),
            'total_bookings' => $exhibitor->bookings()->count(),
            'total_revenue' => $exhibitor->bookings()->withRevenue()->sum('amount_paid'),
            'contributing_campaigns' => $this->getContributingCampaigns($exhibitorId),
            'stall_performance' => $this->getStallPerformance($exhibitorId),
            'conversation_timeline' => $this->conversationService->getExhibitorTimeline($exhibitorId),
        ];
    }

    /**
     * Get campaigns contributing to exhibitor's revenue
     */
    protected function getContributingCampaigns($exhibitorId): array
    {
        return Campaign::whereHas('bookings', function ($query) use ($exhibitorId) {
            $query->where('exhibitor_id', $exhibitorId);
        })
        ->withCount([
            'bookings as bookings_count' => function ($query) use ($exhibitorId) {
                $query->where('exhibitor_id', $exhibitorId);
            }
        ])
        ->withSum([
            'bookings as revenue' => function ($query) use ($exhibitorId) {
                $query->where('exhibitor_id', $exhibitorId);
            }
        ], 'amount_paid')
        ->get()
        ->map(function ($campaign) {
            return [
                'id' => $campaign->id,
                'name' => $campaign->name,
                'bookings_count' => $campaign->bookings_count,
                'revenue' => $campaign->revenue ?? 0,
            ];
        })
        ->toArray();
    }

    /**
     * Get stall-wise performance summary
     */
    protected function getStallPerformance($exhibitorId): array
    {
        return DB::table('conversations')
            ->where('exhibitor_id', $exhibitorId)
            ->whereNotNull('table_id')
            ->join('location_mngt_table_details', 'conversations.table_id', '=', 'location_mngt_table_details.id')
            ->select(
                'location_mngt_table_details.id as table_id',
                'location_mngt_table_details.table_name',
                'location_mngt_table_details.location_mngt_id',
                DB::raw('COUNT(*) as total_conversations'),
                DB::raw('SUM(CASE WHEN outcome = "materialised" THEN 1 ELSE 0 END) as bookings_count'),
                DB::raw('COALESCE(SUM(bookings.amount_paid), 0) as revenue')
            )
            ->leftJoin('bookings', function ($join) {
                $join->on('conversations.booking_id', '=', 'bookings.id')
                     ->whereNotNull('conversations.booking_id');
            })
            ->groupBy('location_mngt_table_details.id', 'location_mngt_table_details.table_name', 'location_mngt_table_details.location_mngt_id')
            ->get()
            ->toArray();
    }
}
```

---

## 4. DATA FLOW ARCHITECTURE

### 4.1 Complete Flow: Campaign → Lead → Conversation → Booking → Revenue

```
┌─────────────────────────────────────────────────────────────────────┐
│                     CAMPAIGN SENT                                   │
│  Campaign: "Exhibition Invitation"                                  │
│  Recipients: [Visitor1, Visitor2, Visitor3]                        │
└───────────────────────┬─────────────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────────────┐
│              VISITOR RECEIVES MESSAGE                               │
│  Visitor1: Opens message, interested in Exhibitor A                 │
│  Visitor2: Opens message, interested in Exhibitor B                 │
└───────────────────────┬─────────────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────────────┐
│              LEAD CREATED (Follow-up)                               │
│  FollowUp Record:                                                    │
│  - phone: Visitor1 phone                                            │
│  - status: "interested"                                             │
│  - exhibitor_id: Exhibitor A ID                                     │
│  - visitor_id: Visitor1 contact ID                                  │
│  - campaign_id: Campaign ID (attribution)                           │
│  - location_id: Exhibition Location                                 │
│  - table_id: Exhibitor A's Stall                                    │
│  - employee_id: Employee who handled                                │
└───────────────────────┬─────────────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────────────┐
│           CONVERSATION RECORD CREATED                               │
│  Conversation Record:                                                │
│  - exhibitor_id: Exhibitor A ID                                     │
│  - visitor_id: Visitor1 ID                                          │
│  - campaign_id: Campaign ID                                         │
│  - location_id: Exhibition Location                                 │
│  - table_id: Stall Number                                           │
│  - outcome: "interested"                                            │
│  - follow_up_id: FollowUp ID                                        │
│  - conversation_date: Now                                           │
└───────────────────────┬─────────────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────────────┐
│        FOLLOW-UP CONTINUES (Status: "busy" or "interested")         │
│  Another Conversation:                                               │
│  - outcome: "busy"                                                  │
│  - next_followup_date: Scheduled                                    │
└───────────────────────┬─────────────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────────────┐
│           MATERIALISED (Booking Created)                            │
│  FollowUp Updated:                                                   │
│  - status: "materialised"                                           │
│                                                                      │
│  Booking Created:                                                    │
│  - exhibitor_id: Exhibitor A ID                                     │
│  - visitor_id: Visitor1 ID                                          │
│  - campaign_id: Campaign ID (attribution persists)                  │
│  - location_id: Exhibition Location                                 │
│  - table_id: Stall Number                                           │
│  - price: 50000                                                     │
│  - amount_status: "partial"                                         │
│  - amount_paid: 25000                                               │
└───────────────────────┬─────────────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────────────┐
│        CONVERSATION RECORD UPDATED                                  │
│  Conversation Record:                                                │
│  - outcome: "materialised"                                          │
│  - booking_id: Booking ID                                           │
│  - notes: "Booking created - Amount: 50000"                         │
└───────────────────────┬─────────────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────────────┐
│              REVENUE ATTRIBUTED                                     │
│  Campaign Analytics:                                                 │
│  - Total Messages Sent: 1000                                        │
│  - Total Leads Generated: 150                                       │
│  - Total Bookings Created: 25                                       │
│  - Total Revenue: 12,50,000 (Sum of amount_paid)                    │
│  - Conversion %: 16.67% (25/150)                                    │
│                                                                      │
│  Company Dashboard (Exhibitor A):                                    │
│  - Total Leads: 15                                                  │
│  - Total Bookings: 5                                                │
│  - Total Revenue: 2,50,000                                          │
│  - Contributing Campaigns: [Campaign ID, Revenue]                   │
└─────────────────────────────────────────────────────────────────────┘
```

---

### 4.2 Attribution Persistence Logic

```php
// When follow-up created with campaign context:
FollowUp::create([
    'phone' => $visitorPhone,
    'campaign_id' => $campaignId, // ✅ Stored
    'exhibitor_id' => $exhibitorId,
    // ...
]);

// When booking created from follow-up:
Booking::create([
    'phone' => $followUp->phone,
    'campaign_id' => $followUp->campaign_id, // ✅ Persists from follow-up
    'exhibitor_id' => $followUp->exhibitor_id, // ✅ Persists
    // ...
]);

// Campaign revenue calculation:
$revenue = Booking::where('campaign_id', $campaignId)
    ->sum('amount_paid'); // ✅ Accurate attribution
```

---

## 5. EXAMPLE ELOQUENT QUERIES

### 5.1 Campaign Revenue Queries

```php
// Get total revenue for a campaign
$campaign = Campaign::find($campaignId);
$totalRevenue = $campaign->bookings()
    ->whereNotNull('amount_paid')
    ->where('amount_paid', '>', 0)
    ->sum('amount_paid');

// Get campaign statistics
$stats = Campaign::withCount([
    'recipients as messages_sent' => fn($q) => $q->where('status', 'sent'),
    'followUps as leads_generated',
    'bookings as bookings_created',
])
->withSum('bookings as revenue', 'amount_paid')
->find($campaignId);

// Get revenue by payment status for a campaign
$revenueByStatus = Booking::where('campaign_id', $campaignId)
    ->select('amount_status', DB::raw('SUM(amount_paid) as total'))
    ->whereNotNull('amount_paid')
    ->groupBy('amount_status')
    ->get();

// Get top performing campaigns by revenue
$topCampaigns = Campaign::withSum('bookings as revenue', 'amount_paid')
    ->having('revenue', '>', 0)
    ->orderByDesc('revenue')
    ->limit(10)
    ->get();
```

---

### 5.2 Company Conversation Queries

```php
// Get conversation timeline for an exhibitor (company)
$timeline = Conversation::where('exhibitor_id', $exhibitorId)
    ->with([
        'visitor:id,name,phone',
        'employee:id,name,email',
        'location:id,loc_name,address',
        'table:id,table_name',
        'campaign:id,name',
        'followUp:id,status,comment',
        'booking:id,price,amount_paid,amount_status'
    ])
    ->orderBy('conversation_date', 'desc')
    ->get();

// Get recent conversations (last 10)
$recent = Conversation::forExhibitor($exhibitorId)
    ->recent(10)
    ->with(['visitor', 'employee', 'campaign'])
    ->get();

// Get conversations filtered by outcome
$materialisedConversations = Conversation::where('exhibitor_id', $exhibitorId)
    ->where('outcome', 'materialised')
    ->with('booking')
    ->get();

// Get conversations from a specific campaign for an exhibitor
$campaignConversations = Conversation::where('exhibitor_id', $exhibitorId)
    ->where('campaign_id', $campaignId)
    ->with(['visitor', 'booking'])
    ->get();
```

---

### 5.3 Stall-wise Performance Queries

```php
// Get stall-wise bookings and revenue
$stallPerformance = DB::table('bookings')
    ->where('exhibitor_id', $exhibitorId)
    ->whereNotNull('table_id')
    ->join('location_mngt_table_details', 'bookings.table_id', '=', 'location_mngt_table_details.id')
    ->join('location_mngt', 'location_mngt_table_details.location_mngt_id', '=', 'location_mngt.id')
    ->select(
        'location_mngt.loc_name as location_name',
        'location_mngt_table_details.table_name',
        DB::raw('COUNT(*) as total_bookings'),
        DB::raw('SUM(amount_paid) as total_revenue'),
        DB::raw('AVG(amount_paid) as average_booking_value')
    )
    ->whereNotNull('amount_paid')
    ->groupBy('location_mngt.id', 'location_mngt.loc_name', 'location_mngt_table_details.id', 'location_mngt_table_details.table_name')
    ->orderByDesc('total_revenue')
    ->get();

// Get stall-wise conversations count
$stallConversations = Conversation::where('exhibitor_id', $exhibitorId)
    ->whereNotNull('table_id')
    ->with('table:id,table_name,location_mngt_id')
    ->select('table_id', DB::raw('COUNT(*) as conversation_count'))
    ->groupBy('table_id')
    ->get();
```

---

### 5.4 Company Dashboard Queries

```php
// Get complete company dashboard data
$exhibitor = Contacts::where('id', $exhibitorId)
    ->where('type', 'exhibitor')
    ->first();

$dashboardData = [
    'exhibitor' => $exhibitor,
    'recent_conversations' => Conversation::where('exhibitor_id', $exhibitorId)
        ->with(['visitor', 'employee', 'location', 'table', 'campaign'])
        ->orderBy('conversation_date', 'desc')
        ->limit(10)
        ->get(),
    
    'total_leads' => FollowUp::where('exhibitor_id', $exhibitorId)->count(),
    
    'total_bookings' => Booking::where('exhibitor_id', $exhibitorId)->count(),
    
    'total_revenue' => Booking::where('exhibitor_id', $exhibitorId)
        ->whereNotNull('amount_paid')
        ->sum('amount_paid'),
    
    'contributing_campaigns' => Campaign::whereHas('bookings', function($q) use ($exhibitorId) {
        $q->where('exhibitor_id', $exhibitorId);
    })
    ->withCount([
        'bookings as bookings_count' => fn($q) => $q->where('exhibitor_id', $exhibitorId)
    ])
    ->withSum([
        'bookings as revenue' => fn($q) => $q->where('exhibitor_id', $exhibitorId)
    ], 'amount_paid')
    ->get(),
];
```

---

### 5.5 Campaign Attribution Queries

```php
// Get all leads generated from a campaign
$campaignLeads = FollowUp::where('campaign_id', $campaignId)
    ->with(['exhibitor', 'visitor', 'location', 'table'])
    ->get();

// Get all bookings from a campaign with exhibitor breakdown
$campaignBookings = Booking::where('campaign_id', $campaignId)
    ->with(['exhibitor', 'visitor'])
    ->select('exhibitor_id', DB::raw('COUNT(*) as bookings_count'), DB::raw('SUM(amount_paid) as revenue'))
    ->whereNotNull('amount_paid')
    ->groupBy('exhibitor_id')
    ->get();

// Get conversion funnel for a campaign
$funnel = [
    'messages_sent' => CampaignRecipient::where('campaign_id', $campaignId)
        ->where('status', 'sent')
        ->count(),
    
    'leads_generated' => FollowUp::where('campaign_id', $campaignId)->count(),
    
    'bookings_created' => Booking::where('campaign_id', $campaignId)->count(),
    
    'revenue_generated' => Booking::where('campaign_id', $campaignId)
        ->whereNotNull('amount_paid')
        ->sum('amount_paid'),
];
```

---

## 6. MIGRATION STRATEGY

### 6.1 Migration Order

```php
// Step 1: Create conversations table
php artisan make:migration create_conversations_table

// Step 2: Add new columns to follow_ups (nullable for backward compat)
php artisan make:migration add_context_fields_to_follow_ups_table

// Step 3: Add new columns to bookings (nullable for backward compat)
php artisan make:migration add_context_fields_to_bookings_table

// Step 4: Data migration - populate existing data (optional)
php artisan make:migration populate_conversation_context_data
```

### 6.2 Data Migration Script

```php
<?php
// populate_conversation_context_data.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // For existing follow-ups:
        // Try to match phone to contacts to populate visitor_id
        DB::statement("
            UPDATE follow_ups fu
            LEFT JOIN contacts c ON fu.phone = c.phone
            SET fu.visitor_id = c.id
            WHERE c.id IS NOT NULL
        ");

        // For existing bookings:
        // Convert booking_location string to location_id FK
        DB::statement("
            UPDATE bookings b
            LEFT JOIN location_mngt lm ON b.booking_location = lm.loc_name
            SET b.location_id = lm.id
            WHERE lm.id IS NOT NULL
        ");

        // Convert table_no string to table_id FK (if pattern matches)
        // This may require manual intervention for complex cases
        // ...
    }

    public function down(): void
    {
        // Reverse data migration if needed
    }
};
```

### 6.3 Backward Compatibility Checklist

✅ **All new columns are nullable** - Existing records won't break  
✅ **Old string fields kept in bookings** - Can still read old data  
✅ **Phone-based lookup maintained** - Follow-ups still work with phone  
✅ **Gradual migration** - Can populate context data over time  
✅ **Service layer handles both** - Checks for new fields, falls back to old  

---

## 7. BACKWARD COMPATIBILITY

### 7.1 Existing Code Compatibility

**Follow-up Creation (Old Code Still Works):**
```php
// Old code (still works):
FollowUp::create([
    'phone' => $phone,
    'status' => 'busy',
    'comment' => '...',
    'employee_id' => $employeeId,
]);
// New fields are nullable, so this doesn't break

// New code (with context):
FollowUp::create([
    'phone' => $phone,
    'status' => 'busy',
    'comment' => '...',
    'employee_id' => $employeeId,
    'exhibitor_id' => $exhibitorId, // NEW
    'location_id' => $locationId,   // NEW
    'campaign_id' => $campaignId,   // NEW
]);
```

**Booking Creation (Old Code Still Works):**
```php
// Old code (still works):
Booking::create([
    'phone' => $phone,
    'booking_date' => $date,
    'booking_location' => 'Location Name', // String still works
    'table_no' => 'Table 1', // String still works
    'price' => 50000,
    'employee_id' => $employeeId,
]);
// New FK fields are nullable, old string fields kept

// New code (with proper FKs):
Booking::create([
    'phone' => $phone,
    'booking_date' => $date,
    'location_id' => $locationId,  // NEW FK
    'table_id' => $tableId,        // NEW FK
    'exhibitor_id' => $exhibitorId, // NEW
    'campaign_id' => $campaignId,   // NEW
    'price' => 50000,
    'employee_id' => $employeeId,
]);
```

### 7.2 Query Compatibility

**Old Queries Still Work:**
```php
// Still works - phone-based lookup
$followUps = FollowUp::where('phone', $phone)->get();

// Still works - old booking_location string
$bookings = Booking::where('booking_location', 'Mumbai')->get();

// New queries available when context is populated
$followUps = FollowUp::where('exhibitor_id', $exhibitorId)->get();
$bookings = Booking::where('location_id', $locationId)->get();
```

---

## 8. IMPLEMENTATION CHECKLIST

### Phase 1: Database Setup ✅
- [ ] Create conversations table migration
- [ ] Add columns to follow_ups table
- [ ] Add columns to bookings table
- [ ] Add indexes for performance
- [ ] Add foreign key constraints

### Phase 2: Models ✅
- [ ] Create Conversation model
- [ ] Update FollowUp model with relationships
- [ ] Update Booking model with relationships
- [ ] Update Campaign model with relationships
- [ ] Update Contacts model (exhibitor) with relationships

### Phase 3: Services ✅
- [ ] Create ConversationService
- [ ] Update FollowUpService
- [ ] Create CampaignAnalyticsService
- [ ] Create CompanyDashboardService

### Phase 4: Data Migration ✅
- [ ] Create data migration script
- [ ] Test data migration on staging
- [ ] Run data migration on production
- [ ] Verify data integrity

### Phase 5: Testing ✅
- [ ] Unit tests for new services
- [ ] Integration tests for data flow
- [ ] Performance tests for large datasets
- [ ] Backward compatibility tests

### Phase 6: Documentation ✅
- [ ] Update API documentation
- [ ] Update user guide
- [ ] Update developer guide

---

## END OF DOCUMENTATION

**Next Steps:**
1. Review and approve this design
2. Create migration files
3. Implement models and services
4. Test thoroughly
5. Deploy to staging
6. Monitor and optimize


