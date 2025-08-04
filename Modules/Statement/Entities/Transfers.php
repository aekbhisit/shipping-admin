<?php

namespace Modules\Statement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transfers extends Model
{
    use HasFactory;

    protected $fillable = ['id',  'job_id',  'acc_id',  'step', 'request_body',   'request_at', 'response_body',   'response_at',  'status',  'msg',  'created_by',  'created_at',  'updated_by',  'updated_at'];
    protected $table = "bank_transfer_logs";
    protected $primaryKey = "id";

    protected static function newFactory()
    {
        return \Modules\Statement\Database\factories\TransfersFactory::new();
    }
    public function job()
    {
        return $this->hasOne(Users::class, 'id', 'job_id');
    }

    public function acc()
    {
        return $this->hasOne(Users::class, 'id', 'acc_id');
    }

    public function createdby()
    {
        return $this->hasOne(Users::class, 'id', 'created_by');
    }

    public function updatedby()
    {
        return $this->hasOne(Users::class, 'id', 'updated_by');
    }
}
