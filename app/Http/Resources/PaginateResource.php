<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaginateResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'current_page' => [
                'page_number' => $this->currentPage(),
                'items_from' => $this->firstItem(),
                'items_to' => $this->lastItem(),
                'items_count' => $this->count(),
                'has_more_pages' => $this->hasMorePages(),
                'next_page_url' => $this->nextPageUrl(),
                'previous_page_url' => $this->previousPageUrl(),
            ],
            'has_pages' => $this->hasPages(),
            'first_page_url' => $this->url(1),
            'last_page_number' => $this->lastPage(),
            'last_page_url' => $this->url($this->lastPage()),
            'items_per_page' => $this->perPage(),
            'items_total' => $this->total(),
            'path' => $request->path(),  
        ];
    }
}
