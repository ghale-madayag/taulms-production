<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return[
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'published' => $this->published,
            'thumbnail' => $this->thumbnail,
            'attachement' => $this->attachement,
            'created' => date_format($this->created_at,"d M,Y h:i A"),
            //'quizzes' => QuizzesResource::collection($this->quizzes),
        ];
    }
}
