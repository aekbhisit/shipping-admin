<?php

namespace Modules\Default\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Defaults extends Model
{
    use HasFactory;

    protected $fillable = ['id','name_th', 'name_en', 'desc_th', 'desc_en', 'detail_th', 'detail_en', 'image', 'status','sequence','created_at','updated_at'];

    protected $table = "defaults";
    protected $primaryKey = "id";

    protected static function newFactory()
    {
        return \Modules\Default\Database\factories\DefaultsFactory::new();
    }

}

