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
