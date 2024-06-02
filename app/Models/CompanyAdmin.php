<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticableTrait;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class CompanyAdmin extends Model implements Authenticatable
{
    use AuthenticableTrait;
    use HasRoles;


    protected $table = 'company_admins';
    protected $fillable =
        [
            'company_id','sub_company_id','type','badge_id','active','name','username','password','email','phone','image'
        ];

    public function setRememberToken($value)
    {
        return null;
    }


    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function setImageAttribute($value)
    {
        if ($value){
            // $name = unique_file($value->getClientOriginalName());
            // $value->move(base_path().'/public/companies/admins',$name);
            // $this->attributes['image'] = $name;
            $this->attributes['image'] = $value;
        }
    }
}
