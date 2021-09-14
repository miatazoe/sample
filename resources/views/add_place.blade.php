@extends('layouts.app')
@section('content')
    <div class="main">
        <form id="place_form" action="">
            <table>
                <tr>
                    <th>ID</th>
                    <td>自動発番</td>
                </tr>
                <tr>
                    <th>LPWA ID</th>
                    <td><input type="text" class="len400" name="lpwa_id" id="lpwa_id" value="" /></td>
                </tr>
                <tr>
                    <th>住所</th>
                    <td><input type="text" class="len400" name="address" id="address" value="" /></td>
                </tr>
                <tr>
                    <th>表示名</th>
                    <td><input type="text" class="len400" name="place_name" id="place_name" value="" /></td>
                </tr>
                <tr>
                    <th>1時間雨量閾値</th>
                    <td><input type="text" class="len400" name="max_rain_hour" id="max_rain_hour" value="" />mm</td>
                </tr>
                <tr>
                    <th>累加雨量閾値</th>
                    <td><input type="text" class="len400" name="max_rain_sum" id="max_rain_sum" value="" />mm</td>
                </tr>
                <div><input type="button" value="登録" onclick="store_place();" /></div>
            </table>
        </form>
    </div>
    <script>
        function store_place() {
            if (!$("#lpwa_id").val() || !$("#place_name").val()){
                alert("LPWA ID、または表示が未入力です");
                return false;
            }
            // フォームデータを取得
            var formdata = new FormData($('#place_form').get(0));
            // console.log(formdata);

            // POSTでアップロード
            $.ajax({
                url  : "/store/place",
                type : "POST",
                data : formdata,
                cache       : false,
                contentType : false,
                processData : false,
                dataType    : "json",
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            })
            .done(function(data){
                console.log(data);
                alert("登録しました");
                //location.reload();
                window.location.href = '/set/place';
            })
            .fail(function(XMLHttpRequest, status, e){
                alert("登録に失敗しました。");
                console.log(XMLHttpRequest, status, e);
            });
        }
    </script>
@endsection
