<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppTemplate extends Model
{
    protected $fillable = [
        'template_id',
        'name',
        'language',
        'category',
        'status',
        'components',
        'allow_category_change',
        'synced_at',
    ];

    protected $casts = [
        'components' => 'array',
        'allow_category_change' => 'boolean',
        'synced_at' => 'datetime',
    ];

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'APPROVED' => 'bg-success',
            'PENDING' => 'bg-warning',
            'REJECTED' => 'bg-danger',
            'PAUSED' => 'bg-secondary',
            default => 'bg-light'
        };
    }

    /**
     * Get category badge class
     */
    public function getCategoryBadgeClassAttribute()
    {
        return match($this->category) {
            'MARKETING' => 'bg-info',
            'UTILITY' => 'bg-primary',
            'AUTHENTICATION' => 'bg-purple',
            default => 'bg-light'
        };
    }

    /**
     * Scope for approved templates
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'APPROVED');
    }

    /**
     * Get body text from components
     */
    public function getBodyTextAttribute()
    {
        if (empty($this->components)) {
            return '';
        }

        foreach ($this->components as $component) {
            if (isset($component['type']) && $component['type'] === 'BODY') {
                return $component['text'] ?? '';
            }
        }

        return '';
    }
}
