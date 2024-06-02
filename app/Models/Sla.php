<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sla extends Model
{
    protected $table = 'slas';
    protected $fillable = [
        'provider_id', 'category_id', 'sub_category_id', 'request_type', 'response_time',
        'assessment_time', 'rectification_time', 'created_at', 'updated_at'
    ];


    public function setArrayValue($array, $key, $value)
    {
        if ($value != null && $value != 'null' && $value != '' && $value !== 'undefined') {
            $array[$key] = $value;
        }
        return $array;
    }


    /**
     * @param $query
     * @param $user
     * @param $request
     * @return mixed
     */
    public function scopeSaveOrUpdate($query, $ngBsdPlaning, $request)
    {


        $data = [];
        $data = $this->setArrayValue($data, 'provider_id', $request->input('provider_id'));
        $data = $this->setArrayValue($data, 'category_id', $request->input('category_id'));
        $data = $this->setArrayValue($data, 'sub_category_id', $request->input('sub_category_id'));
        $data = $this->setArrayValue($data, 'request_type', $request->input('request_type'));
        $data = $this->setArrayValue($data, 'response_time', $request->input('response_time'));
        $data = $this->setArrayValue($data, 'assessment_time', $request->input('assessment_time'));
        $data = $this->setArrayValue($data, 'rectification_time', $request->input('rectification_time'));
        if(empty($request->id)){
            Sla::create($data);
        }else{
            Sla::where("id", $request->id)->firstOrFail()->update($data);
        }
        return  'success';
    }

    public function sub_cats()
    {
        return $this->hasOne(Category::class, 'id' , 'sub_category_id');
    }

}
