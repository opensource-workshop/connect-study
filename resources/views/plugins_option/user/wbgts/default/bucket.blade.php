{{--
 * バケツ編集画面テンプレート
 *
 * @author 永原　篤 <nagahara@opensource-workshop.jp>
 * @copyright OpenSource-WorkShop Co.,Ltd. All Rights Reserved
 * @category 暑さ指数チェック・プラグイン
--}}
@extends('core.cms_frame_base_setting')

@section("core.cms_frame_edit_tab_$frame->id")
    {{-- プラグイン側のフレームメニュー --}}
    @include('plugins_option.user.wbgts.wbgts_frame_edit_tab')
@endsection

@section("plugin_setting_$frame->id")

{{-- 共通エラーメッセージ 呼び出し --}}
@include('plugins.common.errors_form_line')

@if (empty($wbgt->id) && $action != 'createBuckets')
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-circle"></i> {{ __('messages.empty_bucket_setting', ['plugin_name' => '暑さ指数チェック']) }}
    </div>
@else
    <div class="alert alert-info">
        <i class="fas fa-exclamation-circle"></i>
        @if (empty($wbgt->id))
            新しい暑さ指数チェック設定を登録します。
        @else
            暑さ指数チェック設定を変更します。
        @endif
    </div>

    @if (empty($wbgt->id))
    <form action="{{url('/')}}/redirect/plugin/wbgts/saveBuckets/{{$page->id}}/{{$frame_id}}#frame-{{$frame->id}}" method="POST">
        <input type="hidden" name="redirect_path" value="{{url('/')}}/plugin/wbgts/createBuckets/{{$page->id}}/{{$frame_id}}#frame-{{$frame_id}}">
    @else
    <form action="{{url('/')}}/redirect/plugin/wbgts/saveBuckets/{{$page->id}}/{{$frame_id}}/{{$wbgt->bucket_id}}#frame-{{$frame->id}}" method="POST">
        <input type="hidden" name="redirect_path" value="{{url('/')}}/plugin/wbgts/editBuckets/{{$page->id}}/{{$frame_id}}#frame-{{$frame_id}}">
    @endif
        {{ csrf_field() }}

        <div class="form-group row">
            <label class="{{$frame->getSettingLabelClass()}}">バケツ名 <label class="badge badge-danger">必須</label></label>
            <div class="{{$frame->getSettingInputClass()}}">
                <input type="text" name="bucket_name" value="{{old('name', $wbgt->bucket_name)}}" class="form-control @if ($errors && $errors->has('bucket_name')) border-danger @endif">
                @include('plugins.common.errors_inline', ['name' => 'bucket_name'])
            </div>
        </div>

        <div class="form-group row">
            <label class="{{$frame->getSettingLabelClass()}}">地点コード <label class="badge badge-danger">必須</label></label>
            <div class="{{$frame->getSettingInputClass()}}">
                <input type="text" name="point_code" value="{{old('point_code', $wbgt->point_code)}}" class="form-control @if ($errors && $errors->has('point_code')) border-danger @endif">
                @include('plugins.common.errors_inline', ['name' => 'point_code'])
            </div>
        </div>

        <div class="form-group row">
            <label class="{{$frame->getSettingLabelClass()}}">データ取得開始時間</label>
            <div class="{{$frame->getSettingInputClass()}}">
                <select name="get_hour" class="form-control">
                    @for ($i = 0; $i < 24; $i++)
                        <option value="{{$i}}"@if(old('get_hour', $wbgt->get_hour) == $i) selected @endif>{{$i}}時</option>
                    @endfor
                </select>
                @if ($errors && $errors->has('get_hour')) <div class="text-danger">{{$errors->first('get_hour')}}</div> @endif
            </div>
        </div>

        <div class="form-group row">
            <label class="{{$frame->getSettingLabelClass()}}">判断時間</label>
            <div class="{{$frame->getSettingInputClass()}}">
                <select name="handan_hour" class="form-control">
                    @for ($i = 0; $i < 24; $i++)
                        <option value="{{$i}}"@if(old('handan_hour', $wbgt->handan_hour) == $i) selected @endif>{{$i}}時</option>
                    @endfor
                </select>
                @if ($errors && $errors->has('handan_hour')) <div class="text-danger">{{$errors->first('handan_hour')}}</div> @endif
            </div>
        </div>

        {{-- Submitボタン --}}
        <div class="form-group text-center">
            <div class="row">
                <div class="col-3"></div>
                <div class="col-6">
                    <button type="button" class="btn btn-secondary mr-2" onclick="location.href='{{URL::to($page->permanent_link)}}'">
                        <i class="fas fa-times"></i><span class="{{$frame->getSettingButtonCaptionClass('md')}}"> キャンセル</span>
                    </button>
                    <button type="submit" class="btn btn-primary form-horizontal"><i class="fas fa-check"></i>
                        <span class="{{$frame->getSettingButtonCaptionClass()}}">
                        @if (empty($wbgt->id))
                            登録確定
                        @else
                            変更確定
                        @endif
                        </span>
                    </button>
                </div>

                {{-- 既存データの場合は削除処理のボタンも表示 --}}
                @if (!empty($wbgt->id))
                <div class="col-3 text-right">
                    <a data-toggle="collapse" href="#collapse{{$frame->id}}">
                        <span class="btn btn-danger"><i class="fas fa-trash-alt"></i><span class="{{$frame->getSettingButtonCaptionClass()}}"> 削除</span></span>
                    </a>
                </div>
                @endif
            </div>
        </div>
    </form>

    <div id="collapse{{$frame->id}}" class="collapse">
        <div class="card border-danger">
            <div class="card-body">
                <span class="text-danger">暑さ指数チェック設定を削除します。<br>この暑さ指数チェック設定に登録されている内容は削除され、元に戻すことはできないため、よく確認して実行してください。</span>

                <div class="text-center">
                    {{-- 削除ボタン --}}
                    <form action="{{url('/')}}/redirect/plugin/wbgts/destroyBuckets/{{$page->id}}/{{$frame_id}}/{{$wbgt->id}}#frame-{{$frame->id}}" method="POST">
                        {{csrf_field()}}
                        <button type="submit" class="btn btn-danger" onclick="javascript:return confirm('データを削除します。\nよろしいですか？')">
                            <i class="fas fa-check"></i> 本当に削除する
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
