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
        Schema::create('disk_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('dir')->comment('Путь до каталога с файлом');
            $table->string('file_name')->comment('Наименование файла');
            $table->string('name')->comment('Отображаемое имя файла');
            $table->bigInteger('size')->default(0)->comment('Размер файла');
            $table->string('ext', 50)->nullable()->comment('Расширение файла');
            $table->string('mime_type')->nullable();
            $table->integer('views')->default(0)->comment('Количество просмотров');
            $table->integer('downloads')->default(0)->comment('Количество скачиваний');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('disk_files');
    }
};
