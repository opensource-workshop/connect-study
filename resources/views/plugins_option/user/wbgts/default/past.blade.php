{{--
 * 表示画面テンプレート（過去データ）
 *
 * @author 永原　篤 <nagahara@opensource-workshop.jp>
 * @copyright OpenSource-WorkShop Co.,Ltd. All Rights Reserved
 * @category 暑さ指数チェック・プラグイン
 --}}
<?php
use App\ModelsOption\User\Wbgts\WbgtDaily;
?>
@extends('core.cms_frame_base')

@section("plugin_contents_$frame->id")

<style>
.table-fixed th:first-child, td:first-child {
  position: sticky;  position: -webkit-sticky;
  left: 0;
}
.table-fixed tr:nth-of-type(odd) th:first-child {
  background-color: #eee;
}
.table-fixed tr:nth-of-type(odd) td:first-child {
  background-color: #eee;
}
.table-fixed tr:nth-of-type(even) th:first-child {
  background-color: #fff;
}
.table-fixed tr:nth-of-type(even) td:first-child {
  background-color: #fff;
}
</style>

<script type="text/javascript">
    // 年の変更
    function change_year() {
        form_year.submit();
    }
</script>

<div class="alert alert-info" role="alert">
    過去のデータが一覧で表示されます。
</div>

<form action="{{url('/')}}/plugin/wbgts/viewPast/{{$page->id}}/{{$frame_id}}#frame-{{$frame->id}}" method="post" name="form_year" class="mb-2">
    {{csrf_field()}}
    <select class="form-control col-md-2 col-sm-3" name="select_year" onchange="javascript:change_year();">
        <option value="">年選択</option>
        @foreach ($years as $year)
            @if ($year->year == $select_year)
                <option value="{{$year->year}}" selected class="text-white bg-primary">{{$year->year}}年</option>
            @else
                <option value="{{$year->year}}">{{$year->year}}年</option>
            @endif
        @endforeach
    </select>
</form>

<div style="overflow-x: scroll;">
    <table class="table table-bordered table-hover table-striped table-fixed">
    <thead>
        <tr>
            <th nowrap style="background-color: #fff;">日付</th>
            @foreach ($daily_all as $day)
            <th nowrap>{{date('n/j', mktime(0, 0, 0, substr($day->create_yohou_date, 4, 2), substr($day->create_yohou_date, 6, 2), substr($day->create_yohou_date, 0, 4)))}}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>

        @for ($i = 3; $i <= 24; $i = $i+3)
            @php
                $wbgt_no = 'wbgt_' . sprintf('%02d', $i);
            @endphp
        <tr>
            <th nowrap>{{$i}}時</th>
            @foreach ($daily_all as $day)
                @if (empty($day->$wbgt_no))
                    <td nowrap></td>
                @else
                    <td nowrap>{!!WbgtDaily::getLevelStr($day->$wbgt_no)!!}</td>
                @endif
            @endforeach
        </tr>
        @endfor

        <tr>
            <td nowrap>作成時間</td>
            @foreach ($daily_all as $day)
            <td nowrap>{{$day->create_yohou_time}}</td>
            @endforeach
        </tr>
    </tbody>
    </table>
</div>

@endsection
