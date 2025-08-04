<?php

namespace Modules\Statement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\CrmCore\Entities\Banks;
use Modules\Job\Entities\Jobs;
use Modules\User\Entities\Users;

class Sms extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',  'statement_id',  'job_id',  'phone',  'port', 'message',  'bank_no',  'amount',  'balance',  'date',  'time',  'date_time',  'sms_time',  'bank_id',  'acc_id',
        'bank_account',  'bank_number',  'status',  'created_at',  'created_by',  'updated_at',  'updated_by', 'data_member_job', 'data_job',  'job_created_status',  'job_created_at', 'job_response',
        'otp',  'otp_ref',  'otp_value',  'otp_use'
    ];
    protected $table = "crm_sms";
    protected $primaryKey = "id";

    protected static function newFactory()
    {
        return \Modules\Statement\Database\factories\SmsFactory::new();
    }
    public function statement()
    {
        return $this->hasOne(Statements::class, 'id', 'statement_id');
    }

    public function job()
    {
        return $this->hasOne(Jobs::class, 'id', 'job_id');
    }
    public function bank()
    {
        return $this->hasOne(Banks::class, 'id', 'bank_id');
    }
    public function accout()
    {
        return $this->hasOne(Banks::class, 'id', 'acc_id');
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
