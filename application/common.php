<?php

function is_mobile($tel){
    if(preg_match("/^1[345789]{1}\d{9}$/",$tel)){
        return true;
    }else{
        return false;
    }
}

/*
 * 检查图片是不是bases64编码的
 */
function is_image_base64($base64) {
    if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64, $result)){
        return true;
    }else{
        return false;
    }
}

function check_pic($dir,$type_img){
    $new_files = $dir.date("YmdHis"). '-' . rand(0,9999999) . "{$type_img}";
    if(!file_exists($new_files))
        return $new_files;
    else
        return check_pic($dir,$type_img);  
}

/**
 * 获取数组中的某一列
 * @param array $arr 数组
 * @param string $key_name  列名
 * @return array  返回那一列的数组
 */
function get_arr_column($arr, $key_name)
{
	$arr2 = array();
	foreach($arr as $key => $val){
		$arr2[] = $val[$key_name];        
	}
	return $arr2;
}

//保留两位小数
function tow_float($number){
    return (floor($number * 100) / 100); 
}

//生成订单号
function getSn($head='')
{
    @date_default_timezone_set("PRC");
    $order_id_main = date('YmdHis') . mt_rand(1000, 9999);
    //唯一订单号码（YYMMDDHHIISSNNN）
    $osn = $head.substr($order_id_main,2); //生成订单号
    return $osn;
}

/**
 * 修改本地配置文件
 *
 * @param array $name   ['配置名']
 * @param array $value  ['参数']
 * @return void
 */
function setconfig($name, $value)
{
    if (is_array($name) and is_array($value)) {
        for ($i = 0; $i < count($name); $i++) {
            $names[$i] = '/\'' . $name[$i] . '\'(.*?),/';
            $values[$i] = "'". $name[$i]. "'". "=>" . "'".$value[$i] ."',";
        }
        $fileurl = APP_PATH . "../config/app.php";
        $string = file_get_contents($fileurl); //加载配置文件
        $string = preg_replace($names, $values, $string); // 正则查找然后替换
        file_put_contents($fileurl, $string); // 写入配置文件
        return true;
    } else {
        return false;
    }
}

//生成随机用户名
function get_username()
{
    $chars1 = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $chars2 = "abcdefghijklmnopqrstuvwxyz0123456789";
    $username = "";
    for ( $i = 0; $i < mt_rand(2,3); $i++ )
    {
        $username .= $chars1[mt_rand(0,25)];
    }
    $username .='_';

    for ( $i = 0; $i < mt_rand(4,6); $i++ )
    {
        $username .= $chars2[mt_rand(0,35)];
    }
    return $username;
}

/**
 * 判断当前时间是否在指定时间段之内
 * @param integer $a 起始时间
 * @param integer $b 结束时间
 * @return boolean
 */
function check_time( $a, $b)
{
    $nowtime = time();
    $start = strtotime($a.':00:00');
    $end = strtotime($b.':00:00');
    if ($nowtime >= $end || $nowtime <= $start){
        return true;
    }else{
        return false;
    }
}

