<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DeletedPostIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'subject' => $this->subject,
            'created at' => $this->created_at,
            'deleted at' => $this->deleted_at,
            'summary' => $this->summary,
            'comments count' => $this->deleted_comments_count,
            'author' => new AuthorResource($this->resource->author)
        ];
    }
}
