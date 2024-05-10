<?php

namespace App\ModelsOption\User\Wbgts;

use Illuminate\Database\Eloquent\Model;
use App\UserableNohistory;

/**
 * 暑さ指数チェック・プラグイン　計測値・テーブル
 *
 * @author 永原　篤 <nagahara@opensource-workshop.jp>
 * @copyright OpenSource-WorkShop Co.,Ltd. All Rights Reserved
 * @category 暑さ指数チェック・プラグイン
 * @package Controller
 */
class WbgtDaily extends Model
{
    // 保存時のユーザー関連データの保持
    use UserableNohistory;

    // 更新する項目の定義
    protected $fillable = [
        'wbgt_id', 'create_yohou_date', 'create_yohou_time',
        'wbgt_03', 'division_03',
        'wbgt_06', 'division_06',
        'wbgt_09', 'division_09',
        'wbgt_12', 'division_12',
        'wbgt_15', 'division_15',
        'wbgt_18', 'division_18',
        'wbgt_21', 'division_21',
        'wbgt_24', 'division_24',
    ];

    /**
     * 3時間ごとの予測値のどこにあたるかを判定
     * 14時だと、12時と15時の2つ。12時だと12時の一つ。
     */
    private function getTargetHours($handan_hour)
    {
        $ret = [];
        if ($handan_hour % 3 == 0) {
            $ret[] = $handan_hour;
        } else {
            $ret[] = floor($handan_hour / 3) * 3;
            $ret[] = $ret[0] + 3;
        }
        return $ret;
    }

    /**
     * レベル判定
     * $target_hour は3時間ごとの時間
     */
    private function getLevel($target_hour, $maximum_far_level = 0)
    {
        $col_name = "wbgt_" . sprintf('%02d', $target_hour);
        if ($this->$col_name > $maximum_far_level) {
            return $this->$col_name;
        }

        return $maximum_far_level;

/*
        for ($i = 0; $i < 8; $i++) {
            $yohou_index = "yohou_hour_" . sprintf('%02d', $i + 1);
            if (substr($this->$yohou_index, 0, 8) == date("Ymd") && substr($this->$yohou_index, -2) == sprintf('%02d', $target_hour)) {
                $wbgt_index = "yohou_wbgt_" . sprintf('%02d', $i + 1);
                if ($maximum_far_level > $this->$wbgt_index) {
                    return $maximum_far_level;
                } else {
                    return $this->$wbgt_index;
                }
            }
        }
        return $maximum_far_level;
*/
    }

    /**
     * レベル表示
     */
    private function getLevelStr($wbgt, $h_num = "")
    {
        $h_start = '';
        $h_end = '';
        if (!empty($h_num)) {
            $h_start = '<h' . $h_num . ' class=" d-inline">';
            $h_end = '</h' . $h_num . ' class=" d-inline">';
        }

        if ($wbgt == 0) {
            return '<h4 class=" d-inline"><span class="badge bg-secondary text-white">データが取得できていないため判定できません。</span></h4>';
        }

        if ($wbgt >= 31) {
            return $h_start . '<span class="badge bg-danger text-white">危険</span>' . $h_end;
        } elseif ($wbgt >= 28) {
            return $h_start . '<span class="badge text-white" style="background-color: #ff4500;">厳重注意</span>' . $h_end;
        } elseif ($wbgt >= 25) {
            return $h_start . '<span class="badge bg-warning">警戒</span>' . $h_end;
        } elseif ($wbgt >= 21) {
            return $h_start . '<span class="badge" style="background-color: #00ffff;">注意</span>' . $h_end;
        } else {
            return $h_start . '<span class="badge text-white" style="background-color: #0000ff;">ほぼ安全</span>' . $h_end;
        }
    }

    /**
     * 指定された時間の暑さ指数を元に危険度の判定
     */
    public function isDanger($handan_hour)
    {
        // 判断したい時間が、どの予想時間にあたるか取得
        $target_hours = $this->getTargetHours($handan_hour);

        // レベル
        $maximum_far_level = 0;

        // 合致した予測時間を判定
        foreach ($target_hours as $target_hour) {
            $maximum_far_level = $this->getLevel($target_hour, $maximum_far_level);
        }

        echo $this->getLevelStr($maximum_far_level, 1);
    }

    /**
     * 今日の各時間を表示
     */
    public function getTodayList()
    {
        $today_list = [];

        for ($i = 3; $i <= 24; $i = $i + 3) {
            $col_name_wbgt = "wbgt_" . sprintf('%02d', $i);
            $col_name_division = "division_" . sprintf('%02d', $i);
                $today_list[$i] = [
                    'wbgt' => $this->$col_name_wbgt,
                    'division' => $this->$col_name_division,
                    'comment' =>  $this->getLevelStr($this->$col_name_wbgt, 5)
                ];
/*
            if (substr($this->$col_name, 0, 8) == date("Ymd")) {
                $wbgt_index = "yohou_wbgt_" . sprintf('%02d', $i + 1);

                $today_list[substr($this->$yohou_index, -2)] = ['wbgt' => $this->$wbgt_index, 'comment' =>  $this->getLevelStr($this->$wbgt_index, 5)];
            }
*/
        }
        return $today_list;
    }
}
