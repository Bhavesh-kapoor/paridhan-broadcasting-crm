<?php

namespace App\Services;

use App\Jobs\SendCampaignJob;
use App\Models\Campaign;
use App\Models\CampaignRecipient;
use App\Models\Contacts;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class CampaignService
{
    /**
     * Get all campaigns with pagination
     */
    public function getAllCampaigns($filters = [])
    {
        $query = Campaign::with('recipients');

        // Apply search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->paginate(10);
    }


    public function getAllCampaignsList()
    {
        $result = Campaign::withCount('recipients')->
            orderBy('created_at', 'desc');
        return DataTables::of($result)
            ->addIndexColumn()
            ->addColumn('full_type', function ($data) {
                return   '<span class="badge bg-light text-dark px-3 py-2 border">
                            <i class="bx bx-tag me-1"></i>' . $data->type . '
                        </span>';
            })
            ->addColumn('recipient_count', function ($data) {

                return '    <span class="badge bg-info px-3 py-2">
                                                <i class="lni lni-users me-1"></i>' . $data->recipients_count . ' Recipients
                                            </span>';
            })
            ->editColumn('created_at', function ($data) {
                return   $data->created_at->format('M d, Y');
            })
            ->addColumn('action', function ($data) {
                $id = $data->id;
                $button = '<div class="d-flex gap-1 justify-content-center">';
                
                $button .= '<button class="btn btn-action btn-view btnView" route="' . route('campaigns.show', $id) . '" data-bs-toggle="tooltip" data-bs-placement="top" title="View Campaign Details">
                    <i class="bx bx-show"></i>
                    <span class="d-none d-md-inline">View</span>
                </button>';

                if ($data->isDraft()):
                    $button .= '<button class="btn btn-action btn-send sendCampaign" id="' . $id . '" data-bs-toggle="tooltip" data-bs-placement="top" title="Send Campaign Now">
                        <i class="bx bx-paper-plane"></i>
                        <span class="d-none d-md-inline">Send</span>
                    </button>';
                endif;
                
                $button .= '<button class="btn btn-action btn-delete deleteBtn" id="' . $id . '" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Campaign">
                    <i class="bx bx-trash"></i>
                    <span class="d-none d-md-inline">Delete</span>
                </button>';
                
                $button .= '</div>';
                return $button;
            })


            ->addColumn('full_status', function ($data) {
                $status = $data->status;
                $button_icon_class = 'bx bx-file';
                if ($status === 'draft') {
                    $button_icon_class = 'bx bx-file';
                } else if ($status === 'sent') {
                    $button_icon_class = 'bx bx-check-circle';
                } else {
                    $button_icon_class = 'lni lni-alarm-clock';
                }

                return '<span class=" badge ' . $data->status_badge_class . ' px-2 py-2 border ">
                                            <i class="' . $button_icon_class . ' me-1"></i>
                                            ' . $data->status . '
                                        </span>';
            })
            ->rawColumns(['action', 'full_status', 'full_type', 'recipient_count'])
            ->make(true);
    }

    public function getAllCampaignsRecipientsList($id)
    {
        $query = CampaignRecipient::with('contact')
            ->where('campaign_id', $id);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('recipient_name', function ($d) {
                return $d->contact->name ?? '-';
            })

            ->addColumn('recipient_contact', function ($d) {
                if (!$d->contact) return '-';
                return $d->contact->type === 'exhibitor'
                    ? '<small class="text-muted">' . $d->contact->email . '</small>'
                    : '<small class="text-muted">' . $d->contact->phone . '</small>';
            })

            ->addColumn('recipient_type', function ($d) {
                if (!$d->contact) return '-';
                $icon = ($d->contact->type === 'exhibitor') ? 'bx bx-store-alt' : 'bx bx-user';
                return '<span class="badge bg-light text-dark px-3 py-2 border">
                <i class="' . $icon . ' me-1"></i>' . ucfirst($d->contact->type) . '
            </span>';
            })

            ->addColumn('recipient_location', function ($d) {
                return $d->contact->location ?? '-';
            })

            ->addColumn('full_status', function ($d) {
                $status = $d->status ?? 'pending';
                $icon = match ($status) {
                    'sent' => 'bx bx-check-circle',
                    'delivered' => 'bx bx-check',
                    'pending' => 'lni lni-alarm-clock',
                    'failed' => 'bx bx-x-circle',
                    default => 'bx bx-file'
                };

                $color = match ($status) {
                    'pending' => 'bg-warning',
                    'sent' => 'bg-success',
                    'delivered' => 'bg-info',
                    'failed' => 'bg-danger',
                    default => 'bg-dark'
                };

                return '<span class="badge ' . $color . ' px-2 py-2 border">
                <i class="' . $icon . ' me-1"></i>' . $status . '
            </span>';
            })

            /** SEARCH FOR RELATION FIELDS */
            ->filterColumn('recipient_name', function ($q, $keyword) {
                $q->whereHas('contact', function ($c) use ($keyword) {
                    $c->where('name', 'like', "%{$keyword}%");
                });
            })

            ->filterColumn('recipient_contact', function ($q, $keyword) {
                $q->whereHas('contact', function ($c) use ($keyword) {
                    $c->where(function ($query) use ($keyword) {
                        $query->where('email', 'like', "%{$keyword}%")
                            ->orWhere('phone', 'like', "%{$keyword}%");
                    });
                });
            })

            ->filterColumn('recipient_type', function ($q, $keyword) {
                $q->whereHas('contact', function ($c) use ($keyword) {
                    $c->where('type', 'like', "%{$keyword}%");
                });
            })

            ->filterColumn('recipient_location', function ($q, $keyword) {
                $q->whereHas('contact', function ($c) use ($keyword) {
                    $c->where('location', 'like', "%{$keyword}%");
                });
            })

            ->orderColumn('recipient_name', function ($q, $order) {
                $q->join('contacts', 'contacts.id', '=', 'campaign_recipients.contact_id')
                    ->orderBy('contacts.name', $order);
            })

            ->orderColumn('recipient_location', function ($q, $order) {
                $q->join('contacts', 'contacts.id', '=', 'campaign_recipients.contact_id')
                    ->orderBy('contacts.location', $order);
            })

            ->rawColumns(['recipient_contact', 'recipient_type', 'full_status'])
            ->make(true);
    }




    /**
     * Get campaign by ID
     */
    public function getCampaignById($id)
    {
        return Campaign::with('recipients')->findOrFail($id);
    }

    /**
     * Create a new campaign
     */
    public function createCampaign($data)
    {
        DB::beginTransaction();

        try {
            $campaign = Campaign::create([
                'id' => Str::ulid(),
                'name' => $data['name'],
                'subject' => $data['subject'],
                'message' => $data['message'],
                'type' => $data['type'],
                'template_name' => $data['template_name'] ?? null,
                'status' => 'draft',
                'scheduled_at' => $data['scheduled_at'] ?? null,
            ]);

            // Add recipients if provided
            if (!empty($data['recipients'])) {
                $this->addRecipientsToCampaign($campaign->id, $data['recipients']);
            }

            DB::commit();
            return $campaign;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update an existing campaign
     */
    public function updateCampaign($id, $data)
    {
        DB::beginTransaction();

        try {
            $campaign = $this->getCampaignById($id);

            $updateData = [
                'name' => $data['name'],
                'subject' => $data['subject'],
                'message' => $data['message'],
                'type' => $data['type'],
                'template_name' => $data['template_name'] ?? null,
                'scheduled_at' => $data['scheduled_at'] ?? null,
            ];

            $campaign->update($updateData);

            // Update recipients if provided
            if (isset($data['recipients'])) {
                $this->updateRecipientsForCampaign($campaign->id, $data['recipients']);
            }

            DB::commit();
            return $campaign;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete a campaign
     */
    public function deleteCampaign($id)
    {
        DB::beginTransaction();

        try {
            $campaign = $this->getCampaignById($id);

            // Delete recipients first
            CampaignRecipient::where('campaign_id', $id)->delete();

            $campaign->delete();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::beginTransaction();

        try {
            $batchSize = 5000; // adjust based on your DB performance

            do {
                $deleted = DB::table('campaign_recipients')
                    ->where('campaign_id', $id)
                    ->limit($batchSize)
                    ->delete();
            } while ($deleted > 0);

            DB::table('campaigns')->where('id', $id)->delete();


            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Send campaign
     */
    public function sendCampaign($id)
    {
        DB::beginTransaction();

        try {
            $campaign = $this->getCampaignById($id);

            if ($campaign->status !== 'draft') {
                throw new \Exception('Campaign can only be sent from draft status.');
            }

            $campaign->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            // Here you would integrate with your messaging service (Wati, etc.)
           dispatch(new SendCampaignJob($id));

            // For now, we'll just mark it as sent

            DB::commit();
            return $campaign;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }




    /**
     * Get contacts for campaign selection
     */
    public function getContactsForCampaign($type = null)
    {
        return DB::table('contacts')->where('type', $type)
            ->orderByDesc('created_at')
            ->simplePaginate(50);
    }

    public function getAllRecipientsIDs($type = null)
    {
        return Contacts::where('type', $type)->pluck('id');
    }




    /**
     * Add recipients to campaign
     */
    public function addRecipientsToCampaign($campaignId, $recipientIds)
    {
        $recipients = [];
        foreach ($recipientIds as $contactId) {
            $contact = Contacts::find($contactId);
            if ($contact) {
                $recipients[] = [
                    'id' => Str::ulid(),
                    'campaign_id' => $campaignId,
                    'contact_id' => $contactId,
                    'email' => $contact->email,
                    'phone' => $contact->phone,
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (!empty($recipients)) {
            CampaignRecipient::insert($recipients);
        }
    }

    /**
     * Update recipients for campaign
     */
    public function updateRecipientsForCampaign($campaignId, $recipientIds)
    {
        // Remove existing recipients
        CampaignRecipient::where('campaign_id', $campaignId)->delete();

        // Add new recipients
        $this->addRecipientsToCampaign($campaignId, $recipientIds);
    }

    /**
     * Get campaign statistics
     */
    public function getCampaignStats()
    {
        return [
            'total' => Campaign::count(),
            'draft' => Campaign::where('status', 'draft')->count(),
            'sent' => Campaign::where('status', 'sent')->count(),
            'scheduled' => Campaign::where('status', 'scheduled')->count(),
        ];
    }
}
