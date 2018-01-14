<?php
// function issets($arr){
//     for($i = 0 ; $i < count($arr) ; $i++){
//         echo $arr[$i];
//         $seted &= isset($arr[i]);
//     }
//     unset($arg);
//     return (bool)$seted;
// }

function checkRange($arg,$form,$to){
    if($arg >= $form && $arg <= $to)
        return true;
    else
        return false;
}
function checkIn($arg,$arr){
    if( gettype($arg) == 'string'){
        foreach($arr as $value)
            if($value != $arg) return false;
    }elseif( gettype($arg) == 'array' ){
        $same_count = 0;
        foreach($arg as $key)
            foreach($arr as $value)
                if($key == $value) $same_count++;
        if($same_count != count($arg)) 
            return false;
    }
    return true;
}


//自定义的加密,@待完成,自定义加密
function sql_encode($content){
    return Little_encrypt($content,SQL_KEY);
}
function sql_decode($content){
    return Little_decrypt($content,SQL_KEY);
}

function web_encode($content){
    return Little_encrypt($content,WEB_KEY);
}
function web_decode($content){
    return Little_decrypt($content,WEB_KEY);
}

function cookie_encode($content){
    return Little_encrypt($content,COOKIE_KEY);
}
function cookie_decode($content){
    return Little_decrypt($content,COOKIE_KEY);
}



function getClientIp() //获取用户IP
{
    if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $IP = getenv('HTTP_CLIENT_IP');
    } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $IP = getenv('HTTP_X_FORWARDED_FOR');
    } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $IP = getenv('REMOTE_ADDR');
    } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $IP = $_SERVER['REMOTE_ADDR'];
    }
    return $IP ? $IP : "unknow";
}

function urlEncodeArg($array){
    $tempArr = array();
    foreach($array as $key => $value){
        if(is_array($value)){
            $tempArr[$key] = urlEncodeArg($value);
            //还有一个问题:改变这里的value不会改变原数组
        }else
            $tempArr[$key] = urlencode($value);
    }
    return $tempArr;
}
function urlDecodeArg($array){
    $tempArr = array();
    foreach($array as $key => $value){
        if(is_array($value)){
            $tempArr[$key] = urlEncodeArg($value);
            //还有一个问题:改变这里的value不会改变原数组
        }else
            $tempArr[$key] = urldecode($value);
    }
    return $tempArr;
}

function transform_HTML($string, $length = null) {
// Helps prevent XSS attacks
    // Remove dead space.
    $string = trim($string);
    // Prevent potential Unicode codec problems.
    $string = utf8_decode($string);
    // HTMLize HTML-specific characters.
    $string = htmlentities($string, ENT_NOQUOTES);
    $string = str_replace("#", "#", $string);
    $string = str_replace("%", "%", $string);
    $length = intval($length);
    if ($length > 0) {
        $string = substr($string, 0, $length);
    }
    return $string;
}

function Little_encrypt($input,$key){
    //插入校验码
    $len = strlen($input);
    $fornt = substr($input,0,$len/2);
    // echo 'fornt:'.$fornt."\n";
    $back = substr($input,$len/2);
    // echo 'back:'.$back."\n";
    $input = $fornt.$key.$back;
    //换位
    // echo 'key:'.$key."\n";
    if(strlen($input)%2 == 0){
        $len = strlen($input);
    }else{
        $len = strlen($input)-1;
    }
    $input_arr = str_split($input,1);
    for($i = 0 ; $i < $len; $i += 2){
        $temp = $input_arr[$i];
        $input_arr[$i] = $input_arr[$i+1];
        $input_arr[$i+1] = $temp;
    }
    $input = implode('',$input_arr);
    return $input;
}
function Little_decrypt($input,$key){
    //换位
    if(strlen($input)%2 == 0){
        $len = strlen($input);
    }else{
        $len = strlen($input)-1;
    }
    $input_arr = str_split($input,1);
    for($i = 0 ; $i < $len; $i += 2){
        $temp = $input_arr[$i];
        $input_arr[$i] = $input_arr[$i+1];
        $input_arr[$i+1] = $temp;
    }
    $input = implode('',$input_arr);
    //提取校验码
    $original_len = strlen($input)-strlen($key);
    $middle = $original_len/2;
    $fornt = substr($input,0,$middle);
    // echo 'fornt:'.$fornt."\n";
    $_key = substr($input,$middle,strlen($key));
    // echo '_key:'.$_key."\n";
    $back = substr($input,$middle+strlen($key));
    // echo 'back:'.$back."\n";
    $output = $fornt.$back;
    if($_key == $key){
        return $output;
    }else
        return false;
}