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
        Schema::table('disk_files', function (Blueprint $table) {
            $table->boolean('is_dir')->default(false)->comment('Является каталогом')->after('mime_type');
            $table->boolean('is_hide')->default(false)->comment('Скрытый файл')->after('is_dir');
            $table->boolean('is_uploads')->default(false)->comment('В процессе загрузки')->after('is_hide');
            $table->timestamp('last_modified')->nullable()->comment('Время последнего изменения файла')->after('downloads');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('disk_files', function (Blueprint $table) {
            $table->dropColumn([
                'is_dir',
                'is_hide',
                'is_uploads',
                'last_modified',
            ]);
        });
    }
};
