<?php

namespace Modules\Setting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tags extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'type', 'head', 'body', 'status', 'created_at', 'updated_at'];
    protected $table = "tags_analytics";
    protected $primaryKey = "id";
    
    protected static function newFactory()
    {
        return \Modules\Setting\Database\factories\TagsFactory::new();
    }
}
