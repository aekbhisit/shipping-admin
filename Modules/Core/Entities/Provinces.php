<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Provinces extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'code', 'name_th','name_en'];
    protected $table = "core_provinces";
    protected $primaryKey = "id";
    
    protected static function newFactory()
    {
        return \Modules\Core\Database\factories\ProvincesFactory::new();
    }
}
