<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Slugs extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'slug_uid', 'level', 'type', 'module', 'method', 'data_id', 'lang', 'slug', 'param', 'meta_auther', 'meta_title', 'meta_keywords', 'meta_description', 'meta_image', 'meta_robots'];
    protected $table = "slugs";
    protected $primaryKey = "id";

    protected static function newFactory()
    {
        return \Modules\Core\Database\factories\SlugsFactory::new();
    }

    public function lang_slug()
    {
        return $this->hasMany('Modules\Core\Entities\Slugs', 'slug_uid', 'slug_uid');
    }
}
