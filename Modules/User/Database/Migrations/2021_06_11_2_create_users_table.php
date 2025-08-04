<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Kalnoy\Nestedset\NestedSet;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('group_id');
            $table->string('name', 500);
            $table->string('username', 100);
            $table->string('email', 320);
            $table->string('password', 255);
            $table->string('avatar', 700)->nullable();
            $table->text('role', 700)->nullable();
            $table->string('locale', '2')->default('th');
            $table->boolean('status')->default(0);
            $table->boolean('api_enable')->default(0);
            $table->dateTime('last_logedin_at')->nullable();
            $table->string('remember_token', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('user_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('description', 700)->nullable();
            $table->text('default_role')->nullable();;
            $table->boolean('status')->default(0);
            NestedSet::columns($table);
            $table->timestamps();
        });

        Schema::create('user_password_resets', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index();
            $table->string('token')->index();
            $table->timestamps();
        });

        Schema::create('user_login_history', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->index();
            $table->string('email', 320)->index();
            $table->string('action', 32)->index();
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
        Schema::dropIfExists('users');
        Schema::dropIfExists('user_groups');
        Schema::dropIfExists('user_password_resets');
    }
}
