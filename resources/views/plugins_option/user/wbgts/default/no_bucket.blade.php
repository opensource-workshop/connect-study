{{--
 * 表示画面テンプレート（バケツ無し）
 *
 * @author 永原　篤 <nagahara@opensource-workshop.jp>
 * @copyright OpenSource-WorkShop Co.,Ltd. All Rights Reserved
 * @category 暑さ指数チェック・プラグイン
 --}}
@extends('core.cms_frame_base')

@section("plugin_contents_$frame->id")

<div class="card border-danger">
    <div class="card-header">設定がありません。</div>
    <div class="card-body">
        管理者が設定を行ってください。
    </div>
</div>

@endsection
