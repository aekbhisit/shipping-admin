<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('logo_header', 200)->nullable();
            $table->string('logo_footer', 200)->nullable();
            $table->string('link_login', 200)->nullable();
            $table->string('meta_title', 250)->nullable();
            $table->text('meta_keywords')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('seo_image', 250)->nullable();
            $table->timestamps();
        });
        Schema::create('slugs', function (Blueprint $table) {
            $table->id();
            $table->integer('type')->nullable();
            $table->integer('level')->nullable();
            $table->string('slug_uid', 700)->nullable();
            $table->string('slug', 700)->nullable();
            $table->string('lang', 700)->nullable();
            $table->string('module', 700)->nullable();
            $table->string('method', 700)->nullable();
            $table->integer('data_id')->nullable();
            $table->string('param', 700)->nullable();
            $table->string('meta_auther', 700)->nullable();
            $table->string('meta_title', 700)->nullable();
            $table->string('meta_keywords', 700)->nullable();
            $table->string('meta_description', 700)->nullable();
            $table->string('meta_image', 700)->nullable();
            $table->string('meta_robots', 700)->nullable();
            $table->timestamps();
        });
        Schema::create('tags_analytics', function (Blueprint $table) {
            $table->id();
            $table->string('type',700)->nullable();
            $table->text('head')->nullable();
            $table->text('body')->nullable();
            $table->integer('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('slugs');
        Schema::dropIfExists('tags_analytics');
        
    }
}
