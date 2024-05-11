{{--
 * 表示画面テンプレート（デフォルト）
 *
 * @author 永原　篤 <nagahara@opensource-workshop.jp>
 * @copyright OpenSource-WorkShop Co.,Ltd. All Rights Reserved
 * @category 暑さ指数チェック・プラグイン
 --}}
@extends('core.cms_frame_base')

@section("plugin_contents_$frame->id")

<h5><span class="badge bg-primary text-white">判定</span></h5>
今日（{{ \Carbon\Carbon::now()->format("Y/m/d") }}）『{{$wbgt->handan_hour}}時』の暑さ指数は {{$daily->isDanger($wbgt->handan_hour)}} です。

<div id="wbgt_detail_{{$frame->id}}" class="mt-2">
    <button type="button" class="btn btn-light btn-sm border" onclick="$('#wbgt_detail_close_{{$frame->id}}').show(); $('#wbgt_detail_{{$frame->id}}').hide();">
        <i class="fas fa-angle-down"></i> 詳細を見る
    </button>
</div>
<div id="wbgt_detail_close_{{$frame->id}}" style="display: none;">

    <h5><span class="badge bg-primary text-white">設定情報</span></h5>
    <ul>
        <li>『{{$wbgt->get_hour}}時』以降に最初に画面を確認した際の予測値と実況値を取得しています。</li>
        <li>『{{$wbgt->handan_hour}}時』の暑さ指数の危険度を判定します。</li>
    </ul>

    <h5 class="mt-1"><span class="badge bg-primary text-white">表示に使用している予測データ</span></h5>
    <ul>
        <li>{{$daily->create_yohou_time}}時点の予測値と実況値を使用しています。</li>
    </ul>

    <h5 class="mt-1"><span class="badge bg-primary text-white">今日の3時間ごとの予測値</span></h5>

    <table class="table table-bordered table-hover table-striped">
    <thead>
        <th>時間</th>
        <th>危険度</th>
        <th>WBGT値</th>
        <th>区分</th>
    </thead>
    <tbody>
        @foreach ($daily->getTodayList() as $hour => $list)
        <tr>
            <td>{{$hour}}</td>
            <td>{!!$list['comment']!!}</td>
            <td>{{$list['wbgt']}}</td>
            <td>
                @if ($list['division'] == 1)
                    予測値
                @elseif ($list['division'] == 2)
                    実況値
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
    </table>

    <button type="button" class="btn btn-light btn-sm border" onclick="$('#wbgt_detail_{{$frame->id}}').show(); $('#wbgt_detail_close_{{$frame->id}}').hide();">
        <i class="fas fa-angle-up"></i> 詳細を閉じる
    </button>
</div>


<div id="wbgt_explanation_{{$frame->id}}" class="mt-2">
    <button type="button" class="btn btn-light btn-sm border" onclick="$('#wbgt_explanation_close_{{$frame->id}}').show(); $('#wbgt_explanation_{{$frame->id}}').hide();">
        <i class="fas fa-angle-down"></i> 説明を読む
    </button>
</div>

<div id="wbgt_explanation_close_{{$frame->id}}" style="display: none;">
    <p>

        <h5 class="mt-1"><span class="badge bg-primary text-white">システムの説明</span></h5>
        <ul>
            <li>このシステムでは環境省の熱中症予防情報サイトのデータを使用しています。</li>
            <li>環境省の熱中症予防情報サイトの暑さ指数電子情報提供サービスのURLは以下です。<br /><a href="https://www.wbgt.env.go.jp/data_service.php" target="_blank">https://www.wbgt.env.go.jp/data_service.php</a></li>
            <li>環境省の熱中症予防情報サイトのデータは3時間ごとの予測値があり、それを使用しています。</li>
            <li>データ確認時点より前の値は予測値ではなく実況値を使用しています。</li>
            <li>フレーム上部の「判定」は管理者がこのサイトで設定した時間の危険度を表示しています。</li>
            <li>例として、管理者がこのサイトで設定した時間が14時だった場合、その前後の12時と15時のいずれかの予測値もしくは実況値が危険と判定できる場合、危険と表示します。</li>
            <li>WBGT値と危険度の判断は以下の範囲で行っています。<br />
                <table class="table table-bordered table-sm">
                    <thead>
                    <tr>
                        <th>WBGT値</th>
                        <th>危険度</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>31以上</td>
                        <td>危険</td>
                    </tr>
                    <tr>
                        <td>28以上</td>
                        <td>厳重注意</td>
                    </tr>
                    <tr>
                        <td>25以上</td>
                        <td>警戒</td>
                    </tr>
                    <tr>
                        <td>21以上</td>
                        <td>注意</td>
                    </tr>
                    <tr>
                        <td>21未満</td>
                        <td>ほぼ安全</td>
                    </tr>
                    </tbody>
                </table>
            </li>
        </ul>

    </p>
    <button type="button" class="btn btn-light btn-sm border" onclick="$('#wbgt_explanation_{{$frame->id}}').show(); $('#wbgt_explanation_close_{{$frame->id}}').hide();">
        <i class="fas fa-angle-up"></i> 説明を閉じる
    </button>
</div>

@endsection
