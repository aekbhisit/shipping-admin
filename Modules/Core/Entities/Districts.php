<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Districts extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'code', 'name_th','name_en','latitude','longitude','city_id','province_id','zip_code'];
    protected $table = "core_districtes";
    protected $primaryKey = "id";
    
    protected static function newFactory()
    {
        return \Modules\Core\Database\factories\DistrictsFactory::new();
    }

    public function city()
    {
        return $this->hasOne('Modules\Core\Entities\Cities','id','city_id');
    }

    public function province()
    {
        return $this->hasOne('Modules\Core\Entities\Provinces','id','province_id');
    }
}
