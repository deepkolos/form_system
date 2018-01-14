<?php

require_once('./include.php');

$preview = false;
if(isset($_GET['preview'])){
    $preview = $_GET['preview'];
}
$styleData = htmlspecialchars_decode($form->style_setting);

$userID = web_encode($form->user->ID);
$formID = web_encode($form->ID);

if(($form->ID == null || $form->isReleased == false) && !isset($_GET['preview'])){
    $content = <<<HTMLDTR
    <span>该表单尚未启用,或者已经关闭</span>
HTMLDTR;
}elseif($preview == 'code'){
    $content = <<<HTMLDTR
    <p style="text-align: center;">预览页面</p>
    <form action="action.php?action=submit" method="post">
        {$styleData}
        <div id="" class="bt_submit"><span>提交</span></div>
    </form>
HTMLDTR;
}else{
    //添加提交按钮
    $content = <<<HTMLDTR
    <form action="action.php?action=submit&login={$userID}&form={$formID}" method="post">
        {$styleData}
        <div id="bt_submit" class="bt_submit"><span>提交</span></div>
    </form>
HTMLDTR;
}

$html = <<<HTMLSTR
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title>{$form->name}--表单</title>
    <link href="css/basic.css" rel='stylesheet' tyle='text/css'>
    <link href="css/formShow.css" rel='stylesheet' tyle='text/css'>
</head>
<body>
    {$content}
    <div class="me">
        @技术部<a href='http://deepkolos.cn'>DeepKolos~</a>
    </div>
</body>
<script src="js/utility.js"></script>
<script src="js/formShow.js"></script>
</html>
HTMLSTR;


echo $html;
?>