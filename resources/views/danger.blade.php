@extends('layouts.app')
@section('content')
    <style>
        .danger-table th,td{
            /* border: solid 2px; */
            height: 50px;
        }
        .danger-table{
            margin: 0 auto;
            width: 100%;
            font-size: 20px;
            color: #f8f8f8;
            text-align: center;
        }
        .danger-table tr:nth-child(odd) td,th {
            background-color: #74a0c3;   /* 奇数行の背景色 */
        }
        .danger-table tr:nth-child(even) td,th {
            background-color: #195788;   /* 偶数行の背景色 */
        }
        .flag_icon img{
            width: 15px;
        }
    </style>
    <div class="main">
        <table class="danger-table">
            <thead>
                <tr>
                    <th></th>
                    <th>観測場所</th>
                    <th>雨量</th>
                    <th>雨量（公認）</th>
                    <th>累加雨量</th>
                    <th>気温</th>
                    <th>風向</th>
                    <th>風速</th>
                    <th>水位</th>
                    <th>指示の内容</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($datas as $data)
                    <tr>
                        <td class="flag_icon"><img src="{{ asset('img/sample_w.png')}}"></td>
                        <td>{{$data->place}}</td>
                        <td>{{round($data->rain_hour,1)}}mm</td>
                        <td>-</td>
                        <td>{{round( $data->rain_sum,1)}}mm</td>
                        <td>{{$data->tmp}}℃</td>
                        <td>{{$data->direction}}</td>
                        <td>{{$data->wind}}m</td>
                        <td>-</td>
                        <td>指示なし</td>
                        @if ($data->rain_hour > 0)
                            <td><button class="btn-danger">送信</button></td>
                        @else
                            <td></td>
                        @endif
                    </tr>
                @endforeach
                {{-- sample --}}
                <tr>
                    <td class="flag_icon"><img src="{{ asset('img/sample_y.png')}}"></td>
                    <td>Sample</td>
                    <td style="color: yellow">65mm</td>
                    <td>-</td>
                    <td>68mm</td>
                    <td>25.7℃</td>
                    <td>西</td>
                    <td>1.2m</td>
                    <td>-</td>
                    <td style="color: yellow">避難準備</td>
                    <td><button class="btn-danger">送信</button></td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection
