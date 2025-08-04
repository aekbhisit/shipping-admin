<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Banks extends Model
{
    use HasFactory;

    protected $fillable = ['bank_id', 'bank_ref', 'bank_code','bank_name','sort'];
    protected $table = "core_banks";
    protected $primaryKey = "bank_id";
    
    protected static function newFactory()
    {
        return \Modules\Core\Database\factories\BanksFactory::new();
    }

   
}
