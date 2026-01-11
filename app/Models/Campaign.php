<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Campaign extends Model
{
    use HasFactory, HasUlids;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'subject',
        'message',
        'type',
        'template_name',
        'status',
        'scheduled_at',
        'sent_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the recipients for this campaign
     */
    public function recipients()
    {
        return $this->hasMany(CampaignRecipient::class);
    }

    /**
     * Get the contacts for this campaign
     */
    public function contacts()
    {
        return $this->belongsToMany(Contacts::class, 'campaign_recipients', 'campaign_id', 'contact_id');
    }

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
        return round(($this->total_bookings_created / $leads) * 100, 2);
    }

    /**
     * Get total messages sent
     */
    public function getTotalMessagesSentAttribute()
    {
        return $this->recipients()->where('status', 'sent')->count();
    }

    /**
     * Scope: Get campaigns with revenue
     */
    public function scopeWithRevenue($query)
    {
        return $query->has('bookings');
    }

    /**
     * Scope for draft campaigns
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope for sent campaigns
     */
    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    /**
     * Scope for scheduled campaigns
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    /**
     * Check if campaign is draft
     */
    public function isDraft()
    {
        return $this->status === 'draft';
    }

    /**
     * Check if campaign is sent
     */
    public function isSent()
    {
        return $this->status === 'sent';
    }

    /**
     * Check if campaign is scheduled
     */
    public function isScheduled()
    {
        return $this->status === 'scheduled';
    }

    /**
     * Get recipient count
     */
    public function getRecipientCountAttribute()
    {
        return $this->recipients()->count();
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'draft' => 'bg-secondary',
            'sent' => 'bg-success',
            'scheduled' => 'bg-warning',
            default => 'bg-light'
        };
    }
}
