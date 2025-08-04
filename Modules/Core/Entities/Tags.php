<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tags extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'type', 'head', 'body', 'status', 'created_at', 'updated_at'];
    protected $table = "mwz_tags";
    protected $primaryKey = "id";
    protected static function newFactory()
    {
        return \Modules\Core\Database\factories\TagsFactory::new();
    }
}
