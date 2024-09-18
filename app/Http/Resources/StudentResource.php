<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
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
            'fullname' => $this->lname.', '.$this->fname. ' '.$this->initial.' '.$this->extname,
            'fname' => $this->fname,
            'lname' => $this->lname,
            'registration' => RegistrationResource::collection($this->registration)->first(),
        ];
    }
}
