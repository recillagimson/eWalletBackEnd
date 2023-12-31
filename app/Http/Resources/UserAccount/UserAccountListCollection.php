<?php

namespace App\Http\Resources\UserAccount;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserAccountListCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $collection = $this->getCollection();

        $collection = $collection->transform(function ($item) {
            $tierApproval = $item->tierApprovals->first();
            return [
                'user_accounts' => [
                    'id' => $item->id,
                    'entity_id' => $item->entity_id,
                    'email' => $item->email,
                    'mobile_number' => $item->mobile_number,
                    'is_active' => $item->is_active,
                    'account_number' => $item->account_number,
                    'created_at' => $item->created_at,
                ],
                'user_details' => [
                    'last_name' => optional($item->profile)->last_name,
                    'first_name' => optional($item->profile)->first_name,
                    'middle_name' => optional($item->profile)->middle_name,
                ],
                'tier' => [
                    'name' => $item->tier->name,
                    'tier_class' => $item->tier->class,
                    'account_status' => $item->tier->account_status
                ],
                'tier_approvals' => [
                    'id' => $tierApproval ? $tierApproval->id : null,
                    'status' => $tierApproval ? $tierApproval->status : null,
                    'verified_date' => $tierApproval ? $tierApproval->approved_date : null,
                    'verified_by' => $tierApproval ? $tierApproval->approved_by : null
                ]
            ];
        });

        $pages = $this->getUrlRange(1, $this->lastPage());

        $pages = collect($pages)->map(function($page) {
            return [
                'url' => $page
            ];
        });

        return [
            'current_page' => $this->currentPage(),
            'data' => $collection,
            "first_page_url" => $this->url(1),
            "from" => (($this->currentPage() * $this->perPage()) - $this->perPage()) + 1,
            "last_page" => $this->lastPage(),
            "last_page_url" => $this->url($this->lastPage()),
            "links" => $pages,
            "next_page_url" => $this->nextPageUrl(),
            "path" => $this->getOptions()['path'],
            "per_page" => $this->perPage(),
            "prev_page_url" => $this->previousPageUrl(),
            "to" => $this->currentPage() * $this->perPage(),
            "total" => $this->total()
        ];
    }
}
