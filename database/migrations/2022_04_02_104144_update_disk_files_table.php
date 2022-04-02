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
            $table->string('thumb_litle')->nullable()->comment("Миниатюра для файла")->after('downloads');
            $table->string('thumb_middle')->nullable()->comment("Миниатюра для просмотра")->after('thumb_litle');
            $table->timestamp('thumb_at')->nullable()->comment("Дата создания миниатюры")->after('thumb_middle');
            $table->index(['mime_type', 'is_uploads', 'thumb_at'], 'thumb_index');
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
                'thumb_litle',
                'thumb_middle',
                'thumb_at',
            ]);
            $table->dropIndex('thumb_index');
        });
    }
};
