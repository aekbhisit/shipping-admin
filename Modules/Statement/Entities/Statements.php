<?php

namespace Modules\Statement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\User\Entities\Users;
use Modules\Partner\Entities\PartnerBank ;

class Statements extends Model
{
    use HasFactory;

    protected $fillable = [
        'id', 'sms_id', 'temp_id', 'acc_id', 'report_time', 'report_datetime', 'report_channel', 'report_value', 'bank_balance', 'report_detail', 'app_report_detail', 'report_acc', 'report_name',
        'report_same_acc', 'report_check', 'report_hash', 'report_row', 'report_status', 'hash_text', 'hash_code', 'app_hash_text', 'app_hash_code', 'app_hash_check', 'app_report_at', 'member_id',
        'report_note', 'audit_note', 'tran_id', 'job_id', 'user_id', 'statement_at', 'statement_by', 'notes', 'notes_at', 'notes_by', 'created_at', 'updated_at', 'source_from', 'report_send',
        'match_count', 'match_bank'
    ];
    protected $table = "crm_statements";
    protected $primaryKey = "id";

    protected static function newFactory()
    {
        return \Modules\Statement\Database\factories\StatementsFactory::new();
    }
    public function sms()
    {
        return $this->hasOne(Users::class, 'id', 'sms_id');
    }
    public function temp()
    {
        return $this->hasOne(Users::class, 'id', 'temp_id');
    }
    public function acc()
    {
        return $this->hasOne(Users::class, 'id', 'acc_id');
    }
    public function member()
    {
        return $this->hasOne(Users::class, 'id', 'member_id');
    }
    public function tran()
    {
        return $this->hasOne(Users::class, 'id', 'tran_id');
    }
    public function job()
    {
        return $this->hasOne(Users::class, 'id', 'job_id');
    }
    public function user()
    {
        return $this->hasOne(Users::class, 'id', 'user_id');
    }

    public function statement_user()
    {
        return $this->hasOne(Users::class, 'id', 'statement_by')->select(['id', 'name', 'username']);
    }

    public function noted_user()
    {
        return $this->hasOne(Users::class, 'id', 'notes_by')->select(['id', 'name', 'username']);
    }

    public function bank()
    {
        return $this->hasOne(PartnerBank::class,'id','acc_id')->with('bank_names')->select(['id','bank_id','bank_number','bank_account']);
    }

    public function bank_web()
    {
        return $this->hasOne(PartnerBank::class,'id','acc_id')->with('bank_names')->select(['id','bank_id','bank_number','bank_account']);
    }
}
