<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LessonAttachementResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'short_text' => $this->short_text,
            'full_text' => $this->full_text,
            'position' => $this->position,
            'thumbnail' => $this->thumbnail,
            'term' => $this->term,
            'user_id' => $this->user_id,
            'published' => $this->published,
            'attachement' => $this->attachement,
        ];
    }
}
