@extends('layouts.app')
@section('content')
    <style>
        th,td{
            border: solid 2px;
        }
        .place_list_table{
            margin: 0 auto;
        }
    </style>
    <div class="main">
        <div class="">
            <div style="margin:0 40%;"><input type='text' id='name' value=''placeholder='name' /></div>
            <table class="place_list_table">
                <thead>
                    <tr>
                        <th >NO.</th>
                        <th>LPWA ID</th>
                        <th>場所</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody id="place_list_body"></tbody>
            </table>
            <div style="display: flex;margin:0 46%;">
                <div style="width:100px;height:50px;margin:0;">
                    <input type="button" value="追加" onclick="addContent();" />&nbsp;
                </div>
                <div style="width:100px;height:50px;margin:0;">
                    <input type="button" value="登録" onclick="insertPlace();" />
                </div>
            </div>
        </div>
        <div class="">
            <table></table>
        </div>
    </div>
    <div><input type="hidden" id="cr_num" value="1" /></div>

<script>
    function confirm_del(ROWID, ID){

        if (confirm("削除してもよろしいですか")){

            //対象行を削除
            $("#" + ROWID).remove();

            // if (ID){
            //     //DB を更新する
            //     updateContentsLineup();
            // }

            var rows = $('#place_list_body').prop('rows').length;//スケジュールの削除後の行数

            //行番号の振り直し
            $("td.num-js").each(function (i) {
                i = i+1;
                $(this).text(i);
            });
            makeLineupJson();
        }
    }
    // table num
    $(function(){
        $("td.num-js").each(function (i) {
        i = i+1;
        $(this).text(i);
        });
    });
    function addContent(){
        //表示可能なコンテンツを取得する
        $.ajax({
            type     : "POST",
            url      : "/get/place",
            data     : {"repetition" : $("#repetition").prop('checked')},
            dataType : "json",
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        .done(function(data){

            var next_num = parseInt($("#cr_num").val());
            var rows     = $('#place_list_body').prop('rows').length;
            var selector = `<select id="" onchange="setContentData($(this).parent().attr('numid'),$(this).val());">`;
            selector    += `<option value="">選択してください</option>`;

            for (const i in data) {
                selector += `<option value="${data[i].lpwa_id}">${data[i].place_name}</option>`;
            }

            selector += '</select>';

            var tags  = `<tr id="newtr-${next_num}">`;
                tags += `<td class="num-js">${(rows + 1)}</td>`;
                tags += `<td class="lid" id="newlpwaid-${next_num}"></td>`;
                tags += `<td numid="${next_num}">${selector}</td>`;//name
                tags += '<td class="op">';
                tags += `<input type="button" value="削除" onclick="confirm_del('newtr-${next_num}','');" />`;
                tags += '</td>';
                tags += '</tr>';

            $("#place_list_body").append(tags);
            $("#cr_num").val(next_num + 1);


        }).fail(function(XMLHttpRequest, status, e){
            alert(e);
        });

    }
        //コンテンツのデータをセットする
    function setContentData(NUM, LID){

        //最初のオプションが選択された場合
        if (!LID){

            $("#newlpwaid-"   + NUM).html("");
            // $("#newpname-" + NUM).html("");

            return false;
        }

        //表示可能なコンテンツを取得する
        $.ajax({
            type     : "POST",
            url      : "/get/place",
            data     : {"lpwa_id" : LID},
            dataType : "json",
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        .done(function(data){
            console.log(data);
            $("#newlpwaid-"   + NUM).html(data[0].lpwa_id);
            // $("#newpname-" + NUM).html(data[0].place_name);

        }).fail(function(XMLHttpRequest, status, e){
            alert(e);
        });
    }
    //並び順の json を作成する
    function makeLineupJson(){

        var json = '{';

        $('#place_list_body > tr').each(function(i, e){

            var new_row_num = i + 1;
            var lid         = $(this).children(".lid").html();

            $(this).children(".num-js").html(new_row_num);

            json += `"${new_row_num}":"${lid}",`;
        });

        json  = json.substring(0, json.length -1);
        json += '}';

        return json;
    }
    function insertPlace(){
        //表示コンテンツを取得する
        var judge  = false;
        var tr_num = parseInt($('#place_list_body > tr').length);
        var list_json = "";//並び順の json 文字列

        if (tr_num > 0){

            $('#place_list_body > tr').each(function(i, e){

                var cid = $(this).children(".lid").html();

                if (!cid){
                    judge = true;
                }
            });

            if (judge){
                alert("入力されていない行があります");
                return false;
            } else {

                list_json = makeLineupJson();//並び順の json 文字列を取得
                console.log(list_json);
            }
        } else {
            list_json = "{}";
        }
        //INSERT のデータを取得する
        var insert_json = {
            "list_name": $("#name").val(),
            "p_list"    : list_json
        };

        $.ajax({
            type     : "POST",
            url      : "/store/placelist",
            data     : insert_json,
            dataType : "json",
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        }).done(function(data){
            if(data==true){
                alert("登録しました");
                window.location.href = '/normal';
            }else{
                alert("登録に失敗しました。")
                console.log(data);
            }
        }).fail(function(XMLHttpRequest, status, e){
            alert(e);
        });
    }
</script>
@endsection
