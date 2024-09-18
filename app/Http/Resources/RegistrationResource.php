<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
class RegistrationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $date = "";
        if($this->validation_date != NULL){
            $date = Carbon::createFromFormat('Y-m-d H:i:s', $this->validation_date)->format('F j, Y');
        }
        return [
            'id' => $this->id,
            'term_id' => $this->term_id,
            'validation_date' => $date,
            'cnt' => $this->registration_details->count(),
        ];
    }
}
