<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyToSendersTable extends Migration
{
    /**
     * Run the migrations.
     * Add foreign key constraint for default_address_id after addresses table is created
     *
     * @return void
     */
    public function up()
    {
        Schema::table('senders', function (Blueprint $table) {
            $table->foreign('default_address_id', 'fk_senders_default_address')
                  ->references('id')
                  ->on('addresses')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('senders', function (Blueprint $table) {
            $table->dropForeign('fk_senders_default_address');
        });
    }
} 