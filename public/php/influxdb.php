<?php
/**
 * @author Stephen "TheCodeAssassin" Hoogendijk
 */
require __DIR__ . '/vendor/autoload.php';

use InfluxDB2\Client;


function influxDB_read(){
    // configファイルを変数に代入
    $config = include(__DIR__ . '/config.php');
    # You can generate a Token from the "Tokens Tab" in the UI
    $token = $config['token'];
    $org = $config['org'];
    $bucket = $config['bucket'];

    $client = new Client([
        "url" => $config['url'],
        "token" => $token,
        // "verifySSL" => false,
        // "precision" => InfluxDB2\Model\WritePrecision::NS,
    ]);
    try{
        $query = 'from(bucket: "igss") |> range(start: -10m)|> filter(fn: (r) => r["_measurement"] == "sensor")|> last()';
        // $query = 'SELECT * FROM "igss"';
        $tables = $client->createQueryApi()->query($query, $org);
    }catch(Exception $e){
        return $e;
    }
    if(count($tables) == 0){
        return false;
        exit;
    }
    foreach($tables as $table){
        $list[] = $table->records;
    }
    return $list;
}
// object=>array
function array_change($obj){
    if(!is_object($obj) && !is_array($obj)){
		return $obj;
	}
	if(is_object($obj)){
		$arr = (array)$obj;
	}else{
		$arr = $obj;
	}
	foreach($arr as &$a){
		$a = array_change($a);
	}
	return $arr;
}
// field valueの取得（[field]=>value の連想配列に変換）
// function datum_get($list,&$arr){
//     if(is_array($list)){
//         $head = '';
//         $body = '';
//         $host = '';
//         foreach($list as $key => &$value){
//             if(!is_array($value)){
//                 print_r($key);
//                 // exit;
//                 if(isset($list['host'])){$arr[$list['host']][$list['_field']] = $list['_value'];}
//             }else{
//                 datum_get($value,$arr);
//             }
//         return $arr;
//         }
//     }
// }
// field valueの取得（[field]=>value の連想配列に変換）
function data_get($list){
    if(is_array($list)){
        $host = '';
        foreach($list as $values){
            foreach($values as $value){
                foreach($value as $val){
                    if(!is_int($val)){
                        if($host !== $val['host']){
                            $host = $val['host'];
                        }
                        $arr[$host][$val['_field']] = $val['_value'];
                        $arr[$host]['created_at'] = time_cal($val['_time']);
                    }
                }
            }
        }
        return $arr;
    }
}

// 時間計算
function time_cal($time){
    $outformat= '%F %T'; //equivalent of 'Y-m-d H:i:s' or you could get just date with 'Y-m-d' and so on...
    $datePortions= explode('.', $time);
    $remadeTime= $datePortions[0] . '.' . substr(explode('Z', $datePortions[1])[0], 0, 6) . 'Z';
    $dateTime= DateTime::createFromFormat('Y-m-d\TH:i:s.u\Z', $remadeTime, new DateTimeZone('UTC'));
    if ($dateTime){
        return $result= strftime($outformat, $dateTime->getTimestamp());

    }else{
        return $result= $remadeTime . ' is not InfluxDb timeformat';
    }
}

// // 取り出し
// function data_update_time_host($data,&$list)
// {
//     if(is_array($list) && is_array($data)){
//         foreach($data as $key => &$value){
//             if(!is_array($value)){
//                 if($key == '_time'){
//                     $time = time_cal($value);
//                     $list['created_at'] = $time;
//                 }elseif($key == 'host'){
//                     $list[$key]['created_at'] = $value;
//                 }
//             }else{
//                 data_update_time_host($value,$list);
//             }
//         }
//         return $list;
//     }
// }

// mysqlに保存
function rdb_store($array)
{
    $dsn      = 'mysql:dbname=hqsample;host=localhost';
    $user     = 'root';
    $password = '';
    try{
        $dbh = new PDO($dsn, $user, $password);
        $dbh->beginTransaction();
        foreach($array as $hostkey => $value){
            $calum = "host".",";
            $val = "'".$hostkey."','";
            foreach($value as $key =>$value){
                $calum .= $key.",";
                $val .= $value."','";
            }
            $dbh->exec("INSERT INTO lpwa (".substr($calum, 0, -1).") VALUES (".substr($val, 0, -2).");");
        }
        $dbh->commit();
        return true;
    }catch(PDOException $e){
        $dbh->rollBack();
        return $e;
    }
}


$data = influxDB_read();
if($data==false){
    echo 'No Data';
    exit;
}
$arr_data = array_change($data);
print_r($arr_data);
// field valueの取得
$list = data_get($arr_data);
print_r($list);

// mysqlに保存
echo rdb_store($list);
exit;

?>
