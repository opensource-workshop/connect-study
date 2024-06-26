{{--
 * 表示画面テンプレート。
 *
 * @author 永原　篤 <nagahara@opensource-workshop.jp>
 * @copyright OpenSource-WorkShop Co.,Ltd. All Rights Reserved
 * @category DroneStudyプラグイン
 --}}
@extends('core.cms_frame_base')

@section("plugin_contents_$frame->id")

    @if (!Auth::check() && ($frame_configs->isEmpty() || FrameConfig::getConfigValue($frame_configs, 'dronestudy_needlogin_blockview') == 0))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            ブロックを表示してプログラムを作成するためには、ログインしてください。
        </div>

    @elseif (isset($frame) && $frame->bucket_id)
        {{-- バケツあり --}}
        @include('plugins_option.user.dronestudies.default.index_main')
    @else
        {{-- バケツなし --}}
        <div class="card border-danger">
            <div class="card-body">
                <p class="text-center cc_margin_bottom_0">フレームの設定画面から、使用するDroneStudyを選択するか、作成してください。</p>
            </div>
        </div>
    @endif
@endsection
