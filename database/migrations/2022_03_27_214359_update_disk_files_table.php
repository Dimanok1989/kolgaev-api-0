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
            $table->tinyInteger('is_hide')->default(0)->comment('Скрытый файл')->after('mime_type');
            $table->tinyInteger('is_uploads')->default(0)->comment('В процессе загрузки')->after('is_hide');
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
                'is_hide',
                'is_uploads',
                'last_modified',
            ]);
        });
    }
};
