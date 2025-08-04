<?php

namespace Modules\Statement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TempStatements extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'acc_id', 'detail', 'source_from', 'hash', 'status', 'created_at', 'updated_at'];
    protected $table = "crm_tmp_statements";
    protected $primaryKey = "id";

    protected static function newFactory()
    {
        return \Modules\Statement\Database\factories\TempStatementsFactory::new();
    }

    public function acc()
    {
        return $this->hasOne(Users::class, 'id', 'acc_id');
    }
}
