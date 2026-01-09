<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'bookings';

    protected $primaryKey = 'id';

    public $incrementing = false;
    protected $keyType = 'string';

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
        'released_at',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'price' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'released_at' => 'datetime',
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
     * Get payment history for this booking
     */
    public function paymentHistory(): HasMany
    {
        return $this->hasMany(PaymentHistory::class, 'booking_id')->orderBy('payment_date', 'desc');
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
