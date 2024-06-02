<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserLocation extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
//        return parent::toArray($request);

        return [
            'id'            => $this->id,
            'lat'           => $this->lat,
            'lng'           => $this->lng,
            'address'       => $this->address,
            'name'          => $this->name,
            'is_default'    => $this->is_default,
            'editable'      => $this->approved_by_employer  ? false : true,
            'city'          => $this->city,
            'camp'          => $this->camp,
            'street'        => $this->street,
            'plot_no'       => $this->plot_no,
            'block_no'      => $this->block_no,
            'building_no'   => $this->building_no,
            'apartment_no'  => $this->apartment_no,
            'house_type'    => $this->house_type,
        ];
    }
}
