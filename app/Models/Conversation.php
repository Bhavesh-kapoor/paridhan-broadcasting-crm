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
        'campaign_recipient_id',
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
     * Get the campaign recipient that led to this conversation
     */
    public function campaignRecipient(): BelongsTo
    {
        return $this->belongsTo(CampaignRecipient::class, 'campaign_recipient_id');
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

