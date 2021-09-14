<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Lpwa;
use App\Place;
use App\Place_list;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class HQ_MainController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }
    public function top_display(){
        $place_list = Place_list::orderBy('created_at','DESC')->first();
        return view('top',['list_num'=>$place_list->id]);
    }
    public function rain_judge(Request $request){
        $id = $request->list_num;
        try{
            $place_list = Place_list::where('id',$id)->orderBy('created_at','DESC')->first();
            $p_list = json_decode($place_list->plist,true);
            if($p_list){
                foreach($p_list as $key => $value){
                    $datas[] = Lpwa::orderBy('created_at','DESC')->where('host',$value)->first();
                }
            }
            // data取得メソッドの呼び出し
            $datas = $this->get_data($datas);
            $flag = 0;
            foreach($datas as $data){
                if($data->danger_flag !== 0){
                    $flag = 1;
                }
            }

            return response()->json(['flag' => $flag, 'data' => $datas]);
        }catch(Exception $e){
            Log::debug($e);
            return response()->json($e, 422);
        }
    }
    // data取得（地域名、累加雨量、1時間雨量、方角）
    function get_data($datas){
        foreach($datas as $data){
            // 観測場所
            $place = Place::where('lpwa_id',$data->host)->first();
            $data['place'] = $place->place_name;

            $data['danger_flag'] = 0;
            // 累加雨量
            $lists = Lpwa::where('host',$data->host)->whereRaw('created_at > NOW() - INTERVAL 6 HOUR')->orderBy('created_at','DESC')->get();
            $newdata = Lpwa::where('host',$data->host)->orderBy('id','DESC')->first();
            $newrain = $newdata->rain;
            $rain_sum = 0;
            if(count($lists)>0){
                foreach($lists as $value){
                    if($newrain !== $value->rain){
                        $rain_val = $newrain - $value->rain;
                        $newrain = $value->rain;
                        $rain_sum += $rain_val;
                    }
                }
                if($place->max_rain_sum <= $rain_sum){
                    $data['danger_flag'] = 1;
                }
            }
            $data['rain_sum'] = $rain_sum;
            // 瞬間雨量
            $list = Lpwa::where('host',$data->host)->whereRaw('created_at > "'.date($newdata->created_at).'" - INTERVAL 10 MINUTE')->orderBy('created_at','ASC')->limit(1)->get();
            $list = $list[0];
            $newrain = $newdata->rain;
            $rain_hour = 0;
            if($newrain !== $list->rain){
                $rain_val = $newrain - $list->rain;
                $rain_hour += $rain_val;
            }
            $fall=0; //1時間雨量
            if($rain_hour>0){
                // 雨量計算
                $oldtime = strtotime($list->created_at);
                $newtime = strtotime($newdata->created_at);
                //タイムスタンプの差を計算
                $difSeconds = $newtime - $oldtime;
                //分の差を取得
                $difMinutes = ($difSeconds - ($difSeconds / 60)) / 60;
                //時の差を取得
                $difHours = ($difMinutes - ($difMinutes / 60)) / 60;
                // １時間雨量
                $fall = $rain_hour / $difHours;
            }
            if($place->max_rain_hour <= $fall){
                $data['danger_flag'] = 1;
            }
            $data['rain_hour'] = $fall;
            $dir = $data->dir;
            if($dir>=0 && $dir<11.251){
                $direction = '北';
            }elseif($dir>=11.251 && $dir<33.751){
                $direction = '北北東';
            }elseif($dir>=33.751 && $dir<56.251){
                $direction = '北東';
            }elseif($dir>=56.251 && $dir<78.751){
                $direction = '東北東';
            }elseif($dir>=78.751 && $dir<101.251){
                $direction = '東';
            }elseif($dir>=101.251 && $dir<123.751){
                $direction = '東南東';
            }elseif($dir>=123.751 && $dir<146.251){
                $direction = '南東';
            }elseif($dir>=146.251 && $dir<168.751){
                $direction = '南南東';
            }elseif($dir>=168.751 && $dir<191.251){
                $direction = '南';
            }elseif($dir>=191.251 && $dir<213.751){
                $direction = '南南西';
            }elseif($dir>=213.751 && $dir<236.251){
                $direction = '南西';
            }elseif($dir>=236.251 && $dir<258.751){
                $direction = '西南西';
            }elseif($dir>=258.751 && $dir<281.251){
                $direction = '西';
            }elseif($dir>=281.251 && $dir<303.751){
                $direction = '西北西';
            }elseif($dir>=303.751 && $dir<326.251){
                $direction = '北西';
            }elseif($dir>=326.251 && $dir<348.751){
                $direction = '西北西';
            }elseif($dir>=348.751 && $dir<359.999){
                $direction = '北西';
            }else{
                $direction = null;
            }
            $data['direction'] = $direction;
        }
        return $datas;
    }
    public function setting_place(){
        return view('set_place');
    }
    public function get_place(Request $request)
    {
        try{
            $lpwa_id = $request->lpwa_id;
            if(empty($lpwa_id)){
                $result = Place::orderBy('created_at','asc')->get();
            }else{
                $result = Place::where('lpwa_id',$lpwa_id)->get();
            }
            return response()->json($result);
        }catch(Exception $e){
            Log::debug($e);
            return response()->json($e);
        }
    }
    public function store_placelist(Request $request){
        Log::debug($request);
        $list_name = $request->list_name;
        $plist = $request->p_list;
        $validator = Validator::make($request->all(),[
            'list_name' => 'required|max:255',
            'p_list' => 'required|max:255',
        ]);
        if($validator->fails()){
            Log::debug($validator);
            return response()->json($validator->errors(), 422);
        }
        try{
            DB::beginTransaction();
            $place_list = new Place_list;
            $place_list->list_name = $list_name;
            $place_list->plist = $plist;
            $place_list->save();
            DB::commit();
            return response()->json(true);
        }catch(Exception $e){
            DB::rollback();
            Log::debug($e);
            return response()->json($e);
        }
    }

    public function add_place(){
        return view('add_place');
    }
    public function store_place(Request $request){
        Log::debug($request);
        $validator = Validator::make($request->all(),[
            'lpwa_id' => 'required|max:255',
            'place_name' => 'required|max:255',
        ]);
        if($validator->fails()){
            Log::debug($validator);
            return response()->json($validator->errors(), 422);
        }
        try{
            DB::beginTransaction();
            $place = new Place;
            $place->lpwa_id = $request->lpwa_id;
            $place->address = $request->address;
            $place->place_name = $request->place_name;
            $place->max_rain_hour = $request->max_rain_hour;
            $place->max_rain_sum = $request->max_rain_sum;
            $place->save();
            DB::commit();
            return response()->json(true);
        }catch(Exception $e){
            DB::rollback();
            Log::debug($e);
            return response()->json($e);
        }
    }
}
