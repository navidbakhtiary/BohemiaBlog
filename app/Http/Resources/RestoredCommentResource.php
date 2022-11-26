<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RestoredCommentResource extends JsonResource
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
            'content' => $this->content,
            'created at' => $this->created_at,
            'updated at' => $this->updated_at,
            'post' => new SimplePostResource($this->resource->post),
            'user' => new SimpleUserResource($this->resource->user)
        ];
    }
}
