<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * 暑さ指数チェックバケツ・テーブル
 *
 * @author 永原　篤 <nagahara@opensource-workshop.jp>
 * @copyright OpenSource-WorkShop Co.,Ltd. All Rights Reserved
 * @category 暑さ指数チェック・プラグイン
 * @package Controller
 */
class CreateWbgts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wbgts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('bucket_id');
            $table->string('bucket_name');
            $table->string('point_code', 5)->comment('地点コード');
            $table->string('get_hour', 2)->comment('データ取得開始時間');
            $table->string('handan_hour', 2)->comment('判断時間');
            $table->integer('created_id')->nullable();
            $table->string('created_name', 255)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->integer('updated_id')->nullable();
            $table->string('updated_name', 255)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->integer('deleted_id')->nullable();
            $table->string('deleted_name', 255)->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wbgts');
    }
}
