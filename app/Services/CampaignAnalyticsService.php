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

        // Get recipients who became leads (have conversations)
        $recipientsWithConversations = \App\Models\CampaignRecipient::where('campaign_id', $campaignId)
            ->whereHas('conversations')
            ->count();

        // Get recipients who became bookings (have materialised conversations)
        $recipientsWithBookings = \App\Models\CampaignRecipient::where('campaign_id', $campaignId)
            ->whereHas('conversations', function($query) {
                $query->where('outcome', 'materialised');
            })
            ->count();

        $stats = [
            'campaign_id' => $campaign->id,
            'campaign_name' => $campaign->name,
            'total_messages_sent' => $campaign->recipients()->where('status', 'sent')->count(),
            'total_recipients' => $campaign->recipients()->count(),
            'recipients_with_conversations' => $recipientsWithConversations,
            'total_leads_generated' => $campaign->followUps()->count(),
            'total_bookings_created' => $campaign->bookings()->count(),
            'recipients_with_bookings' => $recipientsWithBookings,
            'total_revenue' => $campaign->bookings()->withRevenue()->sum('amount_paid') ?? 0,
            'conversion_percentage' => 0,
            'recipient_to_lead_conversion' => 0,
            'lead_to_booking_conversion' => 0,
            'recipient_to_booking_conversion' => 0,
            'revenue_by_status' => [
                'paid' => $campaign->bookings()->where('amount_status', 'paid')->sum('amount_paid') ?? 0,
                'partial' => $campaign->bookings()->where('amount_status', 'partial')->sum('amount_paid') ?? 0,
                'unpaid' => $campaign->bookings()->where('amount_status', 'unpaid')->sum('amount_paid') ?? 0,
            ],
        ];

        // Calculate conversion percentages
        $totalRecipients = $stats['total_recipients'];
        $totalLeads = $stats['total_leads_generated'];
        $totalBookings = $stats['total_bookings_created'];

        if ($totalRecipients > 0) {
            $stats['recipient_to_lead_conversion'] = round(($recipientsWithConversations / $totalRecipients) * 100, 2);
            $stats['recipient_to_booking_conversion'] = round(($recipientsWithBookings / $totalRecipients) * 100, 2);
        }

        if ($totalLeads > 0) {
            $stats['lead_to_booking_conversion'] = round(($totalBookings / $totalLeads) * 100, 2);
            $stats['conversion_percentage'] = $stats['lead_to_booking_conversion'];
        }

        return $stats;
    }

    /**
     * Get all campaigns with revenue statistics
     */
    public function getAllCampaignsRevenue()
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
                ? round(($campaign->bookings_count / $campaign->leads_count) * 100, 2)
                : 0;
            return $campaign;
        });
    }

    /**
     * Get revenue breakdown by exhibitor for a campaign
     */
    public function getRevenueByExhibitor($campaignId)
    {
        return DB::table('bookings')
            ->where('campaign_id', $campaignId)
            ->join('contacts', 'bookings.exhibitor_id', '=', 'contacts.id')
            ->select(
                'contacts.id as exhibitor_id',
                'contacts.name as exhibitor_name',
                DB::raw('COUNT(*) as bookings_count'),
                DB::raw('COALESCE(SUM(bookings.amount_paid), 0) as total_revenue')
            )
            ->whereNotNull('bookings.amount_paid')
            ->groupBy('contacts.id', 'contacts.name')
            ->orderByDesc('total_revenue')
            ->get();
    }
}

