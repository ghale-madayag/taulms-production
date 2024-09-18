<?php

namespace App\Http\Resources;

use App\Models\Lesson;
use Illuminate\Http\Resources\Json\JsonResource;

class SubjectLessonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return  [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'code' => $this->code,
            'thumbnail' => $this->thumbnail,
            'description' => $this->description,
            'student_courses' => $this->student_courses,
            'lesson_term' => $this->lessonTerm,
            'lesson' => LessonAttachementResource::collection($this->lesson->where('term',1)->sortBy('position')),
            'lesson_fin' => LessonAttachementResource::collection($this->lesson->where('term',2)->sortBy('position')),
        ];
    }
}
