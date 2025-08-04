<?php

namespace Modules\Default\Entities;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Kalnoy\Nestedset\NodeTrait;

class DefaultCategories extends Model
{
    use HasFactory;
    use NodeTrait;

    protected $fillable = ['id', 'name_th', 'name_en', 'desc_th', 'desc_en', 'detail_th', 'detail_en', 'image', 'default_property', 'status', 'sequence', '_lft', '_rgt', 'parent_id', 'created_at', 'updated_at'];
    protected $table = "default_categories";
    protected $primaryKey = "id";

    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s',
    ];

    protected static function newFactory()
    {
        return \Modules\Product\Database\factories\ProductCategoriesFactory::new();
    }

    public function products()
    {
        return $this->hasMany('Modules\Product\Entities\Products', 'category_id', 'id')->with(['items', 'brand', 'model']);
    }

    public function slug()
    {   
        $lang = (!empty(App::currentLocale())) ? App::currentLocale() : config('app.fallback_locale') ; 
        return $this->hasOne('Modules\Mwz\Entities\Slugs', 'data_id', 'id')->where(['lang'=>$lang,'module'=>'product','method'=>'category']);
    }
}
