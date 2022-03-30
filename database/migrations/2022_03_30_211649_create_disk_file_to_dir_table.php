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
        Schema::create('disk_file_to_dir', function (Blueprint $table) {
            $table->foreignId('dir_id')->constrained('disk_files')->cascadeOnDelete();
            $table->foreignId('file_id')->constrained('disk_files')->cascadeOnDelete();
            $table->unique(['dir_id', 'file_id'], 'dir_id_file_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('disk_file_to_dir');
    }
};
