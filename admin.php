<?php

require_once('include.php');

if(!$user->IsLogin){
    header('Location: index.html');
    exit();
}
$content = "";
$config = '';
if(isset($_GET["action"])){
    if($_GET["action"] == "list"){
        $content = $form->get_list();
    }elseif($_GET["action"] == "skin"){
        $content = "<div style='width:100%;text-align:center;'>空</div>";
    }elseif($_GET["action"] == "edit"){
        $content = $form->edit();
        $config = '$isEditing = true;';
    }elseif($_GET["action"] == "result"){
        //view还是在这里处理吧,太复杂的在js中处理
        $result = json_encode($form->export_table());
        $dom = '$result = '.$result.';';
        $dom .= '$isShowResult = true;';
        $config = $dom;
        $content = '<a href="action.php?action=get_csv&form='.web_encode($form->ID).'" target="_blank"><div class="hit_download_csv">下载CSV文件完整查看~</div></a>';
    }elseif($_GET["action"] == "setting"){
        $setting = $form->get_setting();
        $dom = '';
        if($setting){
            // $dom .= '<script>';
            // $dom .= '$isFromSetting = true;';
            // $dom .= '</script>';
            $config .= '$isFromSetting = true;';
            $dom .= '<div class="form_setting">';
            $dom .= '<p><span>名字:</span><input id="setting_name" type="text" name="" value="'.$setting["name"].'"></p>';
            $dom .= '<p><span>描述:</span><input id="setting_description" type="text" name="" value="'.$setting["description"].'"></p>';
            $dom .= '<p><span>开始时间:</span><input id="setting_start_time" type="text" name="" value="'.$setting["start_time"].'"></p>';
            $dom .= '<p><span>结束时间:</span><input id="setting_end_time" type="text" name="" value="'.$setting["end_time"].'"></p>';
            $dom .= '<p><span>每个IP提交次数:</span><input id="setting_submit_per_ip" type="text" name="" value="'.$setting["submit_per_ip"].'"></p>';
            $dom .= '<p><span id="bt_form_empty" class="bt_setting_empty">清空数据表</span><span id="bt_form_del" class="bt_setting_del">删除数据表</span></p>';
            $dom .= '<div class="buttonBar">';
            $dom .= '<div id="bt_setting_cancel">取消</div>';
            $dom .= '<div id="bt_setting_submit">提交</div>';
            $dom .= '</div>';
        }
        $content = $dom;
    }elseif($_GET["action"] == "about"){
        $log_count = sql_count($form->input_table_id,["*"]);
        $content .='<div class="about">';
        $content .='<div>名字:'.$form->name.'</div>';
        $content .='<div>描述:'.$form->description.'</div>';
        $content .='<div>记录数:'.$log_count.'</div>';
        $content .='</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $user->Name;?>的表单系统</title>
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">

    <link href="css/basic.css" rel='stylesheet' tyle='text/css'>
    <link href="css/admin.css" rel='stylesheet' tyle='text/css'>
    <link href="css/formEdit.css" rel='stylesheet' tyle='text/css'>

    <script>
        var $editingForm = <?php echo ($form->ID)?'"'.web_encode($form->ID).'"':'null';?>;
        var styleData,$isEditing,$isFromSetting,$isShowResult,$result;
        <?php echo $config;?>
    </script>
</head>
<body>
    <div class="leftBar">
        <div id="headImg"><img src="img/deepkolos.jpg" alt=""/></div>
        <div id="name"><?php echo $form->user->Name;?></div>
        <div id='home_nav'>
            <div id="add">添加</div>
            <div id="list">列表</div>
            <div id="skin">皮肤</div>
        </div>
        <!--页面分割线-->
        <div id='edit_nav'>
            <div id="back">首页</div>
            <!--这个是返回按钮还是撤回按钮?@待定-->
            <div id="saveform">保存</div>
            <div id="preview">预览</div>
            <div id="swtich" isrelease="<?php echo $form->isReleased;?>"><span id='swtichOn'>开</span>/<sapn id='swtichOff'>关<span></div>
            <div id="edit">编辑</div>
            <div id="result">结果</div>
            <div id="setting">设置</div>
            <!--<div id="logout">退出</div>-->
        </div>
    </div>
    <div class="contentContianer">
        <div class="contentleft">
            <?php echo $content;?>
        </div>
    </div>
    <div id='addWindow' class="confirm_window">
        名字:<br/>
        <input id='add_name' type="text" name="name" value=""><br/>
        描述:<br/>
        <input id='add_description' type="text" name="description" value=""><br>
        <div class='bt_cancel'>取消</div>
        <div class='bt_ok'>确认</div>
    </div>
    <div id='editFormConfirm' class="confirm_window">
        <p>
            在表单发布的状态编辑,会致使已经记录的数据清空~~确认编辑表单?
        </p>
        <div class='bt_cancel'>取消</div>
        <div class='bt_ok'>确认</div>
    </div>
    <div id="releaseUrlShowWindows" style="display: none;">
        <p>请手动复制URL~</p>
        <input type="text" value="<?php echo 'http://'.$_SERVER['HTTP_HOST'].WEBBASEDIR.'/'.$form->release_url;?>">
        <div class="bt_close">确定</div>
    </div>
    <div id='hitMessageBox'>
        <span>
            
        </span>
    </div>
    <div class="bottomBar" style="display:none;">
        <span id="add_item" style="transform:rotate(0deg)"><div>+</div></span>
        <ul>
            <li id='add_description_item'>描述</li>
            <li id='add_singlechoose_item'>单选</li>
            <li id='add_multiChoice_item'>多选</li>
            <li id='add_singleLineInput_item'>单行</li>
            <li id='add_multiLineInput_item'>多行</li>
        </ul>
    </div>
    <div>
</body>
<script src="js/utility.js" type="text/javascript"></script>
<script src="js/div_onfocus_onblu_support.js" type="text/javascript"></script>
<script src="js/admin.js" type="text/javascript"></script>
<script src="js/formEdit.js" type="text/javascript"></script>
</html>