<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        if (Schema::hasTable('test')) {
            // "test" 表存在...

            // "test" 表存在，并且有 "ext1" 列...'
            if (!Schema::hasColumns('users', ['ext1','ext2'])) {
                // 更新数据表
                Schema::table('test', function (Blueprint $table) {
                    $table->integer('ext1');
                    $table->string('ext2');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        // 删除数据表字段
        Schema::table('test', function (Blueprint $table) {
            $table->dropColumn(['ext1', 'ext2']);
        });
     }
}
