<?php

namespace App\Services;

use App\Models\Contacts;
use App\Services\ConversationService;
use Illuminate\Support\Facades\DB;

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
            'total_revenue' => $exhibitor->bookings()->withRevenue()->sum('amount_paid') ?? 0,
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
        return \App\Models\Campaign::whereHas('bookings', function ($query) use ($exhibitorId) {
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
                'bookings_count' => $campaign->bookings_count ?? 0,
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
            ->leftJoin('location_mngt', 'location_mngt_table_details.location_mngt_id', '=', 'location_mngt.id')
            ->leftJoin('bookings', function($join) {
                $join->on('conversations.booking_id', '=', 'bookings.id')
                     ->whereNotNull('conversations.booking_id');
            })
            ->select(
                'location_mngt.id as location_id',
                'location_mngt.loc_name as location_name',
                'location_mngt_table_details.id as table_id',
                'location_mngt_table_details.table_no as table_name',
                DB::raw('COUNT(DISTINCT conversations.id) as total_conversations'),
                DB::raw('SUM(CASE WHEN conversations.outcome = "materialised" THEN 1 ELSE 0 END) as bookings_count'),
                DB::raw('COALESCE(SUM(bookings.amount_paid), 0) as revenue')
            )
            ->whereNotNull('bookings.amount_paid')
            ->groupBy(
                'location_mngt.id',
                'location_mngt.loc_name',
                'location_mngt_table_details.id',
                'location_mngt_table_details.table_no'
            )
            ->orderByDesc('revenue')
            ->get()
            ->toArray();
    }
}


