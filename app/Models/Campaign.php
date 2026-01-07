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
        'status',
        'image',
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
