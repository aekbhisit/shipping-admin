<?php

namespace Modules\Setting\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Settings extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'logo_header', 'logo_footer', 'link_login', 'meta_title', 'meta_keywords', 'meta_description', 'seo_image', 'created_at', 'updated_at'];
    protected $table = "settings";
    protected $primaryKey = "id";
    protected static function newFactory()
    {
        return \Modules\Setting\Database\factories\SettingsFactory::new();
    }
}
