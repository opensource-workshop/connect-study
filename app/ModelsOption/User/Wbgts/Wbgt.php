<?php

namespace App\ModelsOption\User\Wbgts;

use Illuminate\Database\Eloquent\Model;
use App\UserableNohistory;

/**
 * 暑さ指数チェック・プラグイン　バケツ・モデル
 *
 * @author 永原　篤 <nagahara@opensource-workshop.jp>
 * @copyright OpenSource-WorkShop Co.,Ltd. All Rights Reserved
 * @category 暑さ指数チェック・プラグイン
 * @package Controller
 */
class Wbgt extends Model
{
    // 保存時のユーザー関連データの保持
    use UserableNohistory;

    // 更新する項目の定義
    protected $fillable = ['bucket_id', 'bucket_name', 'point_code', 'get_hour', 'handan_hour'];
}
