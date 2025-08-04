<?php

namespace Modules\Job\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

use Modules\Customer\Entities\Customers;
use Modules\Customer\Entities\CustomerUsers;
use Modules\User\Entities\Users;

class ManualCredit extends Model
{
    use HasFactory, LogsActivity ;

    public $table = 'crm_manual_credits';
    protected $primaryKey = "id";

    protected $fillable = ['id','job_id','job_type','cust_id','cust_user_id','cust_user_name','amount','ref_code','reason','status','updated_at','created_at','created_by','updated_by'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->useLogName('manual-credit');
    }

    protected function castAttribute($key, $value)
    {
        if ($this->getCastType($key) == 'decimal' && is_null($value)) {
            return 0;
        }

        return parent::castAttribute($key, $value);
    }

    protected static function newFactory()
    {
        return \Modules\Job\Database\factories\ManualCreditFactory::new();
    }

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

    public function customer(){
        return $this->hasOne(Customers::class,'id','cust_id')->select(['id','name','mobile','line_id','status']);
    }

    public function customer_user(){
        return $this->hasOne(CustomerUsers::class,'id','cust_user_id')->with('game')->select(['id','gm_id','username','status']);
    }

    public function created_user(){
        return $this->hasOne(Users::class,'id','created_by')->select(['id','name']);
    }

    public function updated_user(){
        return $this->hasOne(Users::class,'id','updated_by')->select(['id','name']);
    }

 
}
