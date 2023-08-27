{{--
 * 表示画面テンプレート（データ取得時間未達）
 *
 * @author 永原　篤 <nagahara@opensource-workshop.jp>
 * @copyright OpenSource-WorkShop Co.,Ltd. All Rights Reserved
 * @category 暑さ指数チェック・プラグイン
 --}}
@extends('core.cms_frame_base')

@section("plugin_contents_$frame->id")

<div class="card border-danger">
    <div class="card-header">データの取得時間になっていません。</div>
    <div class="card-body">
        {{$wbgt->get_hour}}時以降のアクセス時にデータを取得します。
    </div>
</div>

@endsection
