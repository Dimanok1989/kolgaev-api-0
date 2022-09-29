<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('telegram_incomings', function (Blueprint $table) {
            $table->bigInteger('from_id')->nullable()->after('chat_id');
            $table->string('username')->nullable()->after('from_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('telegram_incomings', function (Blueprint $table) {
            $table->dropColumn(['from_id', 'username']);
        });
    }
};
