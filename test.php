<?php


$documentRoot = $_SERVER['DOCUMENT_ROOT'];
$documentRoot = str_replace(['\\','/'], '\\', $documentRoot);
define('WEBBASEDIR', str_replace($documentRoot, '', __DIR__));
echo WEBBASEDIR;

// var_dump(getIps());

function getIps() //获取用户IP
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

// preg_match('/([\d]+)_([\d]+)/','multichoose12_13',$match);
// var_dump($match);

// preg_match('/[\d]+/','multichoose123',$match);
// var_dump($match);

// $test_arr = [1,2];
// $test_arr['class'] = 'ok';
// var_dump($test_arr);
$input_arr = str_split('test',1);
var_dump($input_arr);
$input = implode('',$input_arr);
var_dump($input);