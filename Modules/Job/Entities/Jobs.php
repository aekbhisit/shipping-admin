<?php

namespace Modules\Job\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;


use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

use Modules\Customer\Entities\Customers;
use Modules\Customer\Entities\CustomerBanks;
use Modules\Customer\Entities\CustomerUsers;
use Modules\Partner\Entities\PartnerBank;

use Modules\Partner\Entities\PartnerPromotions;

use Modules\Job\Entities\ManualCredit;

use Modules\User\Entities\Users;



class Jobs extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = ['id', 'channel', 'type', 'code', 'cust_id', 'cust_user_id', 'promotion_chk', 'temp_pro_id', 'promotion_id', 'promotion_amount', 'statement_id', 'amount', 'total_amount', 'turnover', 'transfer_datetime', 'transfer_slip', 'balance_bf', 'balance_af', 'from_bank_id', 'from_bank_acc', 'from_bank_acc_no', 'from_bank_amount_bf', 'from_bank_amount_af', 'to_bank_id', 'to_bank_acc', 'to_bank_acc_no', 'to_bank_amount_bf', 'to_bank_amount_af', 'is_auto', 'status', 'note', 'locked', 'locked_by', 'locked_at', 'approved', 'approved_by', 'approved_at', 'audit', 'audit_by', 'audit_at', 'created_by', 'updated_by', 'refund_by', 'refund_at', 'refno', 'log_transfer_request', 'log_transfer_response', 'completed_at', 'created_at'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s',
        'transfer_datetime' => 'datetime:Y-m-d h:i:s',
        'locked_at' => 'datetime:Y-m-d h:i:s',
        'approved_at' => 'datetime:Y-m-d h:i:s',
        'refund_at' => 'datetime:Y-m-d h:i:s',
    ];
    protected $table = "crm_jobs";
    protected $primaryKey = "id";

    protected static function newFactory()
    {
        return \Modules\Job\Database\factories\JobsFactory::new();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->useLogName('jobs');
    }


    // public function getCreatedAtAttribute($value)
    // {
    //     return Carbon::createFromTimestamp(strtotime($value))
    //         ->timezone('Asia/Bangkok')
    //         ->toDateTimeString()
    //     ;
    // }

    // protected function castAttribute($key, $value)
    // {
    //     if ($this->getCastType($key) == 'decimal' && is_null($value)) {
    //         return 0;
    //     }

    //     if ($this->getCastType($key) == 'datetime' && is_null($value)) {
    //         return \Carbon\Carbon::parse($value)->timezone('Asia/Bangkok') ;
    //     }

    //     return parent::castAttribute($key, $value);
    // }

    public function getTypeAttribute($value)
    {
        switch ($value) {
            case 1:
                return ["value" => $value, "text" => 'ฝาก', 'type' => 'deposit', 'class' => 'table-success'];
                break;
            case 2:
                return ["value" => $value, "text" => 'ถอน', 'type' => 'withdraw', 'class' => 'table-danger'];
                break;
            default:
                return $value;
                break;
        }
    }

    public function getStatusAttribute($value)
    {
        // 0:รอ, 1:กำลังดำเนินการ, 2:prebanker, 3:auto, 8:ยกเลิก, 9:สำเร็จ
        switch ($value) {
            case 0:
                return ["value" => $value, "text" => 'รอ', 'class' => 'waiting '];
                break;
            case 1:
                return ["value" => $value, "text" => 'กำลังทำ', 'class' => 'doing'];
                break;
            case 2:
                return ["value" => $value, "text" => 'Pre Banker', 'class' => 'doing'];
                break;
            case 3:
                return ["value" => $value, "text" => 'Auto', 'class' => 'doing'];
                break;
            case 7:
                return ["value" => $value, "text" => 'โอนเงินสำเร็จ', 'class' => 'transfer_success'];
                break;
            case 8:
                return ["value" => $value, "text" => 'ยกเลิก', 'class' => 'cancel table-secondary'];
                break;
            case 9:
                return ["value" => $value, "text" => 'สำเร็จ', 'class' => 'completed'];
                break;
            default:
                return $value;
                break;
        }
    }

    public function getLockedAttribute($value)
    {
        switch ($value) {
            case 0:
                return ["value" => $value, "text" => ''];
                break;
            case 1:
                return ["value" => $value, "text" => 'ล็อก'];;
                break;
            default:
                return $value;
                break;
        }
    }

    public function from_bank()
    {
        return $this->hasOne(CustomerBanks::class, 'id', 'from_bank_id')->with('bank_names')->select(['id', 'bank_id', 'acc_name', 'acc_no']);
    }

    public function promotion_name()
    {
        return $this->hasOne(PartnerPromotions::class, 'id', 'promotion_id')->select(['id', 'pro_name']);
    }

    public function to_bank()
    {
        return $this->hasOne(CustomerBanks::class, 'id', 'to_bank_id')->with('bank_names')->select(['id', 'bank_id', 'acc_name', 'acc_no']);
    }

    public function withdraw_from_bank()
    {
        return $this->hasOne(PartnerBank::class, 'id', 'withdraw_bank_id')->with('bank_names')->select(['id', 'bank_id', 'bank_number', 'bank_account', 'bank_amount', 'bank_amount_at']);
    }

    public function customer()
    {
        return $this->hasOne(Customers::class, 'id', 'cust_id')->with('bank_transfer')->select(['id', 'name', 'mobile', 'line_id', 'current_promotion_id', 'current_promotion_amount', 'current_promotion_date', 'status']);
    }

    public function customer_user()
    {
        return $this->hasOne(CustomerUsers::class, 'id', 'cust_user_id')->with('game')->select(['id', 'gm_id', 'username', 'status']);
    }

    public function customer_bank()
    {
        return $this->hasOne(CustomerBanks::class, 'cust_id', 'cust_id')->where('status', 1)->with('bank_names')->select(['id', 'cust_id', 'bank_id', 'acc_name', 'acc_no']);
    }

    public function created_user()
    {
        return $this->hasOne(Users::class, 'id', 'created_by')->select(['id', 'name']);
    }

    public function updated_user()
    {
        return $this->hasOne(Users::class, 'id', 'updated_by')->select(['id', 'name']);
    }

    public function locked_user()
    {
        return $this->hasOne(Users::class, 'id', 'locked_by')->select(['id', 'name']);
    }

    public function approved_user()
    {
        return $this->hasOne(Users::class, 'id', 'approved_by')->select(['id', 'name']);
    }

    public function cancel_user()
    {
        return $this->hasOne(Users::class, 'id', 'cancel_by')->select(['id', 'name']);
    }

    public function refund_user()
    {
        return $this->hasOne(Users::class, 'id', 'refund_by')->select(['id', 'name']);
    }

    public function banker_user()
    {
        return $this->hasOne(Users::class, 'id', 'banker_by')->select(['id', 'name']);
    }

    public function audit_user()
    {
        return $this->hasOne(Users::class, 'id', 'audit_by')->select(['id', 'name']);
    }

    public function manual_credit()
    {
        return $this->belongsTo(ManualCredit::class, 'id', 'job_id');
    }
}
