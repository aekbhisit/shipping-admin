<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdminRequestErrorLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'request',
        'request_header' ,
        'request_body' ,
        'response_code',
        'response' ,
        'created_at',
        'updated_at'
    ];
    protected $table = "admin_error_log";
    protected $primaryKey = "id";
    
    protected static function newFactory()
    {
        return \Modules\Core\Database\factories\AdminErrorLogFactory::new();
    }

   
}
