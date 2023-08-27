<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * 暑さ指数チェック計測値・テーブル
 *
 * @author 永原　篤 <nagahara@opensource-workshop.jp>
 * @copyright OpenSource-WorkShop Co.,Ltd. All Rights Reserved
 * @category 暑さ指数チェック・プラグイン
 * @package Controller
 */
class CreateWbgtDailies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wbgt_dailies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('wbgt_id');
            $table->string('create_yohou_date', 8)->comment('予想作成日(YMD)');
            $table->string('create_yohou_time', 5)->comment('予想作成時(H:i)');
            $table->float('wbgt_03')->nullable()->comment('WBGT値_03時');
            $table->integer('division_03')->nullable()->comment('予報or実測_03時');
            $table->float('wbgt_06')->nullable()->comment('WBGT値_06時');
            $table->integer('division_06')->nullable()->comment('予報or実測_06時');
            $table->float('wbgt_09')->nullable()->comment('WBGT値_09時');
            $table->integer('division_09')->nullable()->comment('予報or実測_09時');
            $table->float('wbgt_12')->nullable()->comment('WBGT値_12時');
            $table->integer('division_12')->nullable()->comment('予報or実測_12時');
            $table->float('wbgt_15')->nullable()->comment('WBGT値_15時');
            $table->integer('division_15')->nullable()->comment('予報or実測_15時');
            $table->float('wbgt_18')->nullable()->comment('WBGT値_18時');
            $table->integer('division_18')->nullable()->comment('予報or実測_18時');
            $table->float('wbgt_21')->nullable()->comment('WBGT値_21時');
            $table->integer('division_21')->nullable()->comment('予報or実測_21時');
            $table->float('wbgt_24')->nullable()->comment('WBGT値_24時');
            $table->integer('division_24')->nullable()->comment('予報or実測_24時');
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
        Schema::dropIfExists('wbgt_dailies');
    }
}
