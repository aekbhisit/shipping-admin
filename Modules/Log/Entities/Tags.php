<?php

namespace Modules\Log\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LogHttps extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'uri', 'method', 'req_header', 'req_body','resp_header','resp_body', 'created_at', 'updated_at'];
    protected $table = "http_logs";
    protected $primaryKey = "id";
    protected static function newFactory()
    {
        return \Modules\Mwz\Database\factories\LogHttpsFactory::new();
    }
}
