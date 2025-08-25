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
        <p>{{$wbgt->get_hour}}時以降のアクセス時にデータを取得します。</p>

        <div id="wbgt_past_{{$frame->id}}" class="mt-2">
            <button type="button" class="btn btn-light btn-sm border" onclick="$('#wbgt_past_button_{{$frame->id}}').show();">
                <i class="fas fa-angle-down"></i> 過去のデータについて
            </button>
        </div>

        <div id="wbgt_past_button_{{$frame->id}}" style="display: none;">
            <p class="mt-2 ml-3"><a class="btn btn-info border" href="{{url('/')}}/plugin/wbgts/viewPast/{{$page->id}}/{{$frame->id}}#frame-{{$frame->id}}" role="button">過去のデータを見る。</a></p>
        </div>
    </div>
</div>

@endsection
