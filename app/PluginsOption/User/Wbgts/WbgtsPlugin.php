<?php

namespace App\PluginsOption\User\Wbgts;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\LazyCollection;

use App\Models\Common\Buckets;
use App\Models\Common\Frame;
use App\Models\Core\Configs;

use App\ModelsOption\User\Wbgts\Wbgt;
use App\ModelsOption\User\Wbgts\WbgtDaily;

use App\PluginsOption\User\UserPluginOptionBase;

/**
 * Wbgts プラグイン
 *
 * https://www.wbgt.env.go.jp/data_service.php
 * https://www3.nhk.or.jp/news/heatstroke/tokyo.html
 * 関東 東京 44136 江戸川臨海 えどがわりんかい EDOGAWA-RINKAI 江戸川区臨海町
 *
 * （地点番号44136）、2023年8月の場合
 * https://www.wbgt.env.go.jp/est15WG/dl/wbgt_44136_202308.csv
 *
 * DB 定義コマンド
 * php artisan migrate --path=database/migrations_option
 *
 * @author 永原　篤 <nagahara@opensource-workshop.jp>
 * @copyright OpenSource-WorkShop Co.,Ltd. All Rights Reserved
 * @category Wbgts プラグイン
 * @package Controller
 */
class WbgtsPlugin extends UserPluginOptionBase
{
    /* オブジェクト変数 */

    // バケツ設定
    private $wbgt = null;

    /* コアから呼び出す関数 */

    /**
     * 関数定義（コアから呼び出す）
     */
    public function getPublicFunctions()
    {
        // 標準関数以外で画面などから呼ばれる関数の定義
        $functions = array();
        $functions['get'] = ['viewPast'];
        $functions['post'] = ['viewPast'];
        return $functions;
    }

    /**
     *  権限定義
     */
    public function declareRole()
    {
        // 権限チェックテーブル
        $role_check_table = array();
        return $role_check_table;
    }

    /**
     * プラグインのバケツ取得関数
     */
    private function getPluginBucket($bucket_id)
    {
        // プラグインのメインデータを取得する。
        //if (empty($this->buckets)) {
        //    return new 
        //}
        $this->wbgt = Wbgt::firstOrNew(['bucket_id' => $bucket_id]);
        return $this->wbgt;
    }

    /**
     * 時間が3の倍数か判定
     */
    private function getTime3($time)
    {
        if (empty($time) || strlen($time) < 4) {
            return false;
        }
        if (intval(substr($time, 0, 2)) % 3 == 0) {
            return true;
        }
        return false;
    }

    /**
     * 環境省からデータ取得
     */
    private function getWbgt($request_url)
    {
        // 環境省からデータ取得
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, PHP_OS);
        //curl_setopt($ch, CURLOPT_ENCODING, "gzip");

        //リクエストヘッダ出力設定
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);

        // データ取得実行
        $http_str = curl_exec($ch);

        // HTTPヘッダ取得
        $http_header = curl_getinfo($ch);
        if (empty($http_header) || !array_key_exists('http_code', $http_header) || $http_header['http_code'] != 200) {
            // データが取得できなかったため、スルー。
            return;
        }

        // ファイルに保存（デバック用）
        // \Storage::put('plugins/wbgts/yohou_44136.csv', $http_str);

        // 戻り値
        return $http_str;
    }

    /**
     * データ取得
     */
    private function getDaily()
    {
        // *** 予報データの取得 ***
        $http_str = $this->getWbgt("https://www.wbgt.env.go.jp/prev15WG/dl/yohou_" .$this->wbgt->point_code  . ".csv");

        // データベースに保存
        $file_lines = explode("\n", $http_str);

        $csv1 = str_getcsv($file_lines[0]);
        $csv2 = str_getcsv($file_lines[1]);

        // 暑さ指数のデータを保存する。
        // データは3時間ごとの予測値になっているため、該当時間のカラムに保存
        // 取得した時間より前のデータはない可能性があるので、そこは後で実績から埋める。
        $parse_date = date_parse_from_format("Y/m/d H:i", $csv2[1]);
        $daily_values = [
            'wbgt_id' => $this->wbgt->id,
            'create_yohou_date' => $parse_date["year"] . sprintf('%02d', $parse_date["month"]) . sprintf('%02d', $parse_date["day"]),
            'create_yohou_time' => sprintf('%02d', $parse_date["hour"]) . ":" . sprintf('%02d', $parse_date["minute"]),
        ];
        // 1行目（日時）をループ（最大8回 = 1日分）
        for ($i = 2; $i < 10; $i++) {
            // 対象の日（今日）か確認
            if (substr($csv1[$i], 0, 8) != date("Ymd")) {
                continue;
            }
            // 対象の時間のカラムに保存
            $daily_values['wbgt_' . substr($csv1[$i], 8, 2)] = $csv2[$i] / 10;
            $daily_values['division_' . substr($csv1[$i], 8, 2)] = 1;
        }

        $wbgt_daily = WbgtDaily::create($daily_values);

        // *** 実績データの取得 ***
        $http_str = $this->getWbgt("https://www.wbgt.env.go.jp/est15WG/dl/wbgt_" .$this->wbgt->point_code  . "_" . date("Ym") . ".csv");
        // 
        $file_lines = explode("\n", $http_str);
        foreach ($file_lines as $file_line) {
            $csv = str_getcsv($file_line);
            if ($csv[0] == date("Y/n/j") && $csv[0] == $this->getTime3($csv[1]) && !empty($csv[2])) {
                $col_name_wbgt = "wbgt_" . sprintf('%02d', $csv[1]);
                $col_name_division = "division_" . sprintf('%02d', $csv[1]);
                if ($wbgt_daily->$col_name_wbgt == 0) {
                    $wbgt_daily->$col_name_wbgt = $csv[2];
                    $wbgt_daily->$col_name_division = 2;
                }
            }
        }

        $wbgt_daily->save();
        return $wbgt_daily;

// *** 予報テーブル ***
/*
        // データベースに保存
        $file_lines = explode("\n", $http_str);

        $csv1 = str_getcsv($file_lines[0]);
        $csv2 = str_getcsv($file_lines[1]);

        // 暑さ指数のデータを最大8つまで保存する。
        // データは3時間ごとの予測値になっているため、8つあれば、その日のデータは含まれていることになる。
        // 環境省のデータ自体は、翌々日の24時まで。というフォーマットで作成されている。
        $parse_date = date_parse_from_format("Y/m/d H:i", $csv2[1]);
        $yohou_values = [
            'wbgt_id' => $this->wbgt->id,
            'create_yohou_date' => $parse_date["year"] . sprintf('%02d', $parse_date["month"]) . sprintf('%02d', $parse_date["day"]),
            'create_yohou_time' => sprintf('%02d', $parse_date["hour"]) . ":" . sprintf('%02d', $parse_date["minute"]),
        ];
        for($i = 0; $i < 8; $i++) {
            $yohou_values['yohou_hour_' . sprintf('%02d', $i + 1)] = $csv1[$i + 2];
            $yohou_values['yohou_wbgt_' . sprintf('%02d', $i + 1)] = $csv2[$i + 2] / 10;
        }

        return WbgtYohou::create($yohou_values);
*/
    }

    /* ------------------ */
    /* 画面アクション関数 */
    /* ------------------ */

    /**
     * データ初期表示関数
     * コアがページ表示の際に呼び出す関数
     *
     * @method_title 記事編集
     * @method_desc 記事一覧を表示します。
     * @method_detail
     */
    public function index($request, $page_id, $frame_id, $post_id = null)
    {
        // バケツがない場合の処理
        if (empty($this->buckets)) {
            return $this->view('no_bucket', []);
        }

        // バケツの設定データを取得する。
        $wbgt = $this->getPluginBucket($this->getBucketId());

        // バケツのデータ取得開始時間を過ぎているかチェックする。
        if ($this->wbgt->get_hour > date('H')) {
            return $this->view('still_time', [
                'massage' => 'データの取得時間になっていません。',
                'wbgt' => $this->wbgt,
            ]);
        }

        // 当日の予報＆実績データを取得する。
        $daily = WbgtDaily::where('wbgt_id', $this->wbgt->id)->where('create_yohou_date', date('Ymd'))->first();

        // データの確認と取得
        if (empty($daily)) {
            $daily = $this->getDaily();
        }

        // 表示テンプレートを呼び出す。
        return $this->view('index', [
            'wbgt' => $wbgt,
            'daily' => $daily,
        ]);
    }

    /* ------------------ */
    /* バケツ関係         */
    /* ------------------ */

    /**
     * プラグインのバケツ選択表示関数
     *
     * @method_title 選択
     * @method_desc このフレームに表示する掲示板を選択します。
     * @method_detail
     */
    public function listBuckets($request, $page_id, $frame_id, $id = null)
    {
        // 表示テンプレートを呼び出す。
        return $this->view('list_buckets', [
            'plugin_buckets' => Wbgt::orderBy('created_at', 'desc')->paginate(10, ["*"], "frame_{$frame_id}_page"),
        ]);
    }

    /**
     * データ紐づけ変更関数
     *
     * @param \Illuminate\Http\Request $request リクエスト
     * @param int $page_id ページID
     * @param int $frame_id フレームID
     */
    public function changeBuckets($request, $page_id, $frame_id)
    {
        // FrameのバケツIDの更新
        Frame::where('id', $frame_id)->update(['bucket_id' => $request->select_bucket]);
    }

    /**
     * バケツ新規作成画面
     *
     * @method_title 作成
     * @method_desc 掲示板を新しく作成します。
     * @method_detail 掲示板名やいいねボタンの表示を入力して掲示板を作成できます。
     */
    public function createBuckets($request, $page_id, $frame_id)
    {
        // 処理的には編集画面を呼ぶ
        return $this->editBuckets($request, $page_id, $frame_id);
    }

    /**
     * バケツ設定変更画面の表示
     */
    public function editBuckets($request, $page_id, $frame_id)
    {
        // コアがbucket_id なしで呼び出してくるため、bucket_id は frame_id から探す。
        if ($this->action == 'createBuckets') {
            $bucket_id = null;
        } else {
            $bucket_id = $this->getBucketId();
        }

        // 表示テンプレートを呼び出す。
        return $this->view('bucket', [
            // 表示中のバケツデータ
            'wbgt' => $this->getPluginBucket($bucket_id),
        ]);
    }

    /**
     * バケツ登録/更新のバリデーターを取得する。
     *
     * @param \Illuminate\Http\Request $request リクエスト
     * @return \Illuminate\Contracts\Validation\Validator バリデーター
     */
    private function getBucketValidator($request)
    {
        // 項目のエラーチェック
        $validator = Validator::make($request->all(), [
            'bucket_name' => [
                'required',
                'max:255'
            ],
        ]);
        $validator->setAttributeNames([
            'bucket_name' => 'バケツ名',
        ]);
        return $validator;
    }

    /**
     *  バケツ登録処理
     */
    public function saveBuckets($request, $page_id, $frame_id, $bucket_id = null)
    {
        // 入力エラーがあった場合は入力画面に戻る。
        $validator = $this->getBucketValidator($request);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $bucket_id = $this->saveWbgt($request, $frame_id, $bucket_id);

        // 登録後はリダイレクトして編集ページを開く。
        return new Collection(['redirect_path' => url('/') . "/plugin/wbgts/editBuckets/" . $page_id . "/" . $frame_id . "/" . $bucket_id . "#frame-" . $frame_id]);
    }

    /**
     * バケツを登録する。
     *
     * @param \Illuminate\Http\Request $request リクエスト
     * @param int $frame_id フレームID
     * @param int $bucket_id バケツID
     * @return int バケツID
     */
    private function saveWbgt($request, $frame_id, $bucket_id)
    {
        // バケツの取得。なければ登録。
        $bucket = Buckets::updateOrCreate(
            ['id' => $bucket_id],
            ['bucket_name' => $request->bucket_name, 'plugin_name' => 'wbgts'],
        );

        // フレームにバケツの紐づけ
        $frame = Frame::find($frame_id)->update(['bucket_id' => $bucket->id]);

        // プラグインバケツを取得(なければ新規オブジェクト)
        // プラグインバケツにデータを設定して保存
        $this->getPluginBucket($bucket->id);
        $this->wbgt->bucket_name = $request->bucket_name;
        $this->wbgt->point_code = $request->point_code;
        $this->wbgt->get_hour = $request->get_hour;
        $this->wbgt->handan_hour = $request->handan_hour;
        $this->wbgt->save();

        return $bucket->id;
    }

    /**
     *  バケツ削除処理
     *
     * @param \Illuminate\Http\Request $request リクエスト
     * @param int $page_id ページID
     * @param int $frame_id フレームID
     */
    public function destroyBuckets($request, $page_id, $frame_id, $wbgt_id)
    {
        // プラグインバケツの取得
        $wbgt = Wbgt::find($wbgt_id);
        if (empty($wbgt)) {
            return;
        }

        // FrameのバケツIDのクリア
        Frame::where('id', $frame_id)->update(['bucket_id' => null]);

        // バケツ削除
        Buckets::find($wbgt->bucket_id)->delete();

        // コンテンツ削除
        WbgtPost::where('wbgt_id', $wbgt->id)->delete();

        // プラグイン・バケツ削除
        $wbgt->delete();

        return;
    }

    /**
     *  過去データの参照
     *
     * @param \Illuminate\Http\Request $request リクエスト
     * @param int $page_id ページID
     * @param int $frame_id フレームID
     */
    public function viewPast($request, $page_id, $frame_id, $wbgt_id)
    {
        // バケツの設定データを取得する。
        $wbgt = $this->getPluginBucket($this->getBucketId());

        // 過去の予報＆実績データの年を取得する。
        $years = DB::table('wbgt_dailies')
                   ->select(DB::raw('SUBSTRING(create_yohou_date, 1, 4) as year'))
                   ->groupBy('year')
                   ->orderBy('year', 'desc')
                   ->get();

        // 年の指定を取得する。
        $select_year = $request->select_year;
        if (empty($select_year)) {
            $first_year = $years->first();
            if (!empty($first_year)) {
                $select_year = $first_year->year;
            }
        }
        if (empty($select_year)) {
            $select_year = date('Y');
        }

        // 過去の予報＆実績データを取得する。
        $daily_all = WbgtDaily::where('wbgt_id', $this->wbgt->id)
                              ->where('create_yohou_date', '>=', $select_year . '0101')
                              ->where('create_yohou_date', '<=', $select_year . '1231')
                              ->whereNotNull('wbgt_03')
                              ->whereNotNull('wbgt_24')
                              ->orderBy('create_yohou_date', 'asc')
                              ->orderBy('create_yohou_time', 'asc')->get();

        // 表示テンプレートを呼び出す。
        return $this->view('past', [
            // 表示中のバケツデータ
            'wbgt' => $wbgt,
            'years' => $years,
            'select_year' => $select_year,
            'daily_all' => $daily_all,
        ]);
    }
}
