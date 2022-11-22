<?php

namespace App\Http\Resources;

use App\Classes\Creator;
use Illuminate\Http\Resources\Json\JsonResource;

class DeletedPostInformationResource extends JsonResource
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
            'content' => $this->content,
            'created at' => $this->created_at,
            'updated at' => $this->updated_at,
            'deleted at' => $this->deleted_at,
            'author' => new AuthorResource($this->resource->author),
            'comments count' => count($this->resource->deletedComments),
            'comments link' => Creator::createDeletedPostCommentsLink($this->id)
        ];
    }
}
