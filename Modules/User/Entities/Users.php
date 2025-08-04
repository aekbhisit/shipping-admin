<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword ;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\Notifiable;
use Modules\User\Notifications\NotiUserResetPassword ;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

use Laravel\Passport\HasApiTokens;
use Modules\User\Emails\UserEmail;

// class Users extends Authenticatable
class Users extends Authenticatable
{
    use SoftDeletes, HasFactory, HasApiTokens, LogsActivity ;
    use \Illuminate\Notifications\Notifiable;

    protected $fillable = ['id','role_id','name','username','email','password','status','api'];
    protected $table = "users";
    protected $primaryKey = "id";
   

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    // ----------------- log -------------------//
    // protected static $logAttributes = ['id','role_id','group_id','name','username','email','password','avatar','locale','status','api'] ;
    // protected static $recordEvents = ['created', 'updated', 'deleted'];
    // protected static $logName='users'; // default

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->useLogName('user');
    }
    // ----------------- log -------------------//

     protected static function newFactory()
    {
        return \Modules\User\Database\factories\UsersFactory::new();
    }

    public function role()
    {
        // Fallback if user_roles table doesn't exist
        try {
            return $this->hasOne('Modules\User\Entities\Roles','id','role_id');
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Get user role name from user_type field as fallback
     */
    public function getRoleNameAttribute()
    {
        if ($this->role) {
            return $this->role->name;
        }
        
        // Fallback to user_type field
        return $this->user_type ?? 'user';
    }

    /**
     * Scope to get only active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1)->where('status', 1);
    }

    /**
     * Scope to get only inactive users
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', 0)->orWhere('status', 0);
    }

    /**
     * User type constants and methods
     */
    const USER_TYPE_COMPANY_ADMIN = 'company_admin';
    const USER_TYPE_BRANCH_ADMIN = 'branch_admin';
    const USER_TYPE_BRANCH_STAFF = 'branch_staff';

    public static function getUserTypes()
    {
        return [
            self::USER_TYPE_COMPANY_ADMIN => 'Company Administrator',
            self::USER_TYPE_BRANCH_ADMIN => 'Branch Administrator',
            self::USER_TYPE_BRANCH_STAFF => 'Branch Staff',
        ];
    }

    public function scopeCompanyAdmins($query)
    {
        return $query->where('user_type', self::USER_TYPE_COMPANY_ADMIN);
    }

    public function scopeBranchAdmins($query)
    {
        return $query->where('user_type', self::USER_TYPE_BRANCH_ADMIN);
    }

    public function scopeBranchStaff($query)
    {
        return $query->where('user_type', self::USER_TYPE_BRANCH_STAFF);
    }

    public function sendPasswordResetNotification($token) { 
        // echo $token ;
        $noti = $this->notify(new NotiUserResetPassword($token));
    }

    public static function TestData(){
        $data = [
            'role_id'=>1,
            'name'=>'user_admin',
            'username'=>'user_admin',
            'email'=>'user_admin@gmail.com',
            'password'=>Hash::make('123456'),
            'avatar'=>'/storage/test.png',
        ];
        return $data ;
    }

}
