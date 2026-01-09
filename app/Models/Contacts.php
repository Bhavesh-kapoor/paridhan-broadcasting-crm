<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Contacts extends Model
{
    use HasFactory, HasUlids;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'email',
        'phone',
        'alternate_phone',
        'location',
        'product_type',
        'brand_name',
        'business_type',
        'gst_number',
        'type',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the display name for the contact type
     */
    public function getTypeDisplayNameAttribute()
    {
        return ucfirst($this->type);
    }

    /**
     * Scope to filter by type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Check if contact is an exhibitor
     */
    public function isExhibitor()
    {
        return $this->type === 'exhibitor';
    }

    /**
     * Check if contact is a visitor
     */
    public function isVisitor()
    {
        return $this->type === 'visitor';
    }

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
}
