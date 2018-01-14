<?php

const appName = 'form_system';

define("DB_USER","root");
define("DB_PSWD","sDA6kFbInOrtELb6lEI6!");
define("DB_HOST","localhost");
define("DB_CHARSET","utf8");
define("DB_NAME","form_system");

// forms_setting 关联表的格式
// input_text_$userID_$FormID
// input_file_$userID_$FormID
// input_setting_$userID_$FormID
// ip_table_$userID_$FormID

define("INPUT_","input_");
define("IP_TABLE","ip_table_");

//上传文件路径
define("UPLOAD_DIR",ROOT.PATH_SEPARATOR."file".PATH_SEPARATOR);

//约定input_setting中type的字段
// 0 => char
// 1 => int
// 2 => file
//text里面要不要再细分一下,多选,单选,单行,多行//输入只能在前端控制了,后端应该是比较通用的版本
//单选,多选 => int
//单行,多行 => char

//这里定义表type返回的类型信息
//笨啦,没必要做text表和file表的分离的,搞到如此的麻烦
function toColType($type){
    switch($type){
        case 0:return "varchar(255)";
        case 1:return "varchar(255)";
        case 2:return "varchar(255)";//也是记录长度的
        default:return false;
    }
}

$documentRoot = $_SERVER['DOCUMENT_ROOT'];
$documentRoot = str_replace(['\\','/'], '/', $documentRoot);
$root = str_replace(['\\','/'], '/', ROOT);
define('WEBBASEDIR',str_replace($documentRoot,'',$root));

date_default_timezone_set('Asia/Shanghai');

//加密key
define('SQL_KEY','dbreIn3Otlos');
define('WEB_KEY','pkokegjkedf');
define('COOKIE_KEY','aFsbdso12sd');