<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class CampaignRecipient extends Model
{
    use HasFactory, HasUlids;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'campaign_id',
        'contact_id',
        'email',
        'phone',
        'status',
        'sent_at',
        'delivered_at',
        'read_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the campaign this recipient belongs to
     */
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * Get the contact this recipient represents
     */
    public function contact()
    {
        return $this->belongsTo(Contacts::class);
    }

    /**
     * Scope for pending recipients
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for sent recipients
     */
    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    /**
     * Scope for delivered recipients
     */
    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    /**
     * Scope for failed recipients
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Check if recipient is pending
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if recipient is sent
     */
    public function isSent()
    {
        return $this->status === 'sent';
    }

    /**
     * Check if recipient is delivered
     */
    public function isDelivered()
    {
        return $this->status === 'delivered';
    }

    /**
     * Check if recipient is failed
     */
    public function isFailed()
    {
        return $this->status === 'failed';
    }

    /**
     * Get conversations from this campaign recipient
     */
    public function conversations()
    {
        return $this->hasMany(Conversation::class, 'campaign_recipient_id');
    }

    /**
     * Get follow-ups from this campaign recipient
     */
    public function followUps()
    {
        return $this->hasMany(FollowUp::class, 'campaign_id', 'campaign_id')
            ->where('visitor_id', $this->contact_id);
    }

    /**
     * Get bookings from this campaign recipient
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'campaign_id', 'campaign_id')
            ->where('visitor_id', $this->contact_id);
    }
}
