<?php
require_once("./include.php");
if(isset($_GET["action"])){
    if($_GET["action"] == "login"){//登陆
        //获取post数据,默认通过json传数据
        $post = file_get_contents('php://input', 'r');
        $post = json_decode($post,true);
        $post["pw"] = htmlspecialchars($post["pw"],ENT_QUOTES);
        $post["name"] = htmlspecialchars($post["name"],ENT_QUOTES);
        $result = array();
        if($user->login($post["name"],$post["pw"])){
            $result = [
                "IsSucceed"=>true
            ];
        }else{
            $result = [
                "IsSucceed"=>false
            ];
        }
        logCount(appName);
        echo json_encode($result);
    }elseif($_GET["action"] == "logout"){//登出
        $result = [
            "IsSucceed"=>($user->logout())?true:false
        ];
        echo json_encode($result);
    }elseif($_GET["action"] == "register"){//注册
        //验证过滤输入的数据    
        $post = file_get_contents('php://input', 'r');
        $post = json_decode($post,true);
        $post["pw"] = htmlspecialchars($post["pw"],ENT_QUOTES);
        $post["name"] = htmlspecialchars($post["name"],ENT_QUOTES);
        $i0 = $user->register($post["name"],$post["pw"],[]);
        $result = [
            "IsSucceed"=>($i0)?true:false
        ];
        echo json_encode($result);
    }elseif($_GET["action"] == "checkname"){
        //注册的时候会检查name是否可用的
        $post = file_get_contents('php://input', 'r');
        $post = json_decode($post,true);
        $post["name"] = htmlspecialchars($post["name"],ENT_QUOTES);
        $i0 = $user->checkNameAvaible($post["name"]);
        $result = [
            "IsSucceed"=>($i0)?true:false
        ];
        echo json_encode($result);
    }elseif($_GET["action"] == "submit"){//用户表单提交,以上是非登陆用户
        //zip data
            //check release
                //check time
                    //check ip
                        //submitdata
                        //log ip
        //又要改咯,使用自带的表单上传机制
        //默认这个表的数据都会通过urlencode处理,不过挺麻烦的,涉及到多层级就需要使用递归
        $data = urlEncodeArg($_POST);
        // var_dump($data);
        $i0 = $form->submit($data);
        //主要害怕别人可以这个表单的输入表单,通过修改js参数,提交到别的表单
        //两个途径防御:
        //  1. js加闭包保护,不过直接解压到dom里面就加密不了,还有只能运行时不能修改而已,直接看源代码就可以了
        //  2. 传输的参数加密,用于确认来源
        //所以还是通过第二种方式吧
        // $dom = '<!DOCTYPE html>';
        // $dom .= '<meta charset="UTF-8">';
        // $dom .= '<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">';
        if(is_bool($i0) && $i0 == true){
            header('Location: template/success.html');
            // $dom .= '<title>提交成功~</title>';
            // $dom .= '<div style="text-align:center;">';
            // $dom .= '提交成功~';
            // $dom .= '</div>';
        }else{
            header('Location: template/error.html');
            // $dom .= '<title>提交失败~~</title>';
            // $dom .= '<div style="text-align:center;">';
            // $dom .= $i0;
            // $dom .= '</div>';
        }
        // echo $dom;
    }
    ////////////////////////登陆分割线///////////////////////////////
    //直接在这里预处理cookie的login,字段
    if(!$user->IsLogin){//以下是表单功能,先不管,需要login的操作
        exit();
    }
    if($_GET["action"] == "formRelease"){
        $i0 = $form->release();
        $result = [
            "IsSucceed"=>($i0)?true:false
        ];
        echo json_encode($result);
    }elseif($_GET["action"] == "formUnRelease"){
        $i0 = $form->unrelease();
        $result = [
            "IsSucceed"=>($i0)?true:false
        ];
        echo json_encode($result);
    }elseif($_GET["action"] == "newForm"){
        $post = file_get_contents('php://input', 'r');
        $post = json_decode($post,true);
        $post["name"] = htmlspecialchars($post["name"],ENT_QUOTES);
        $post["description"] = htmlspecialchars($post["description"],ENT_QUOTES);
        $i0 = $form->create($post);//真的害怕溢出吗?下次要用foreach处理啦,手写效率低下
        $result = [
            "IsSucceed"=>($i0)?true:false
        ];
        echo json_encode($result);
    }elseif($_GET["action"] == "formDel"){
        $i0 = $form->del();
        $result = [
            "IsSucceed"=>($i0)?true:false
        ];
        echo json_encode($result);
    }elseif($_GET["action"] == "formEmpty"){
        $i0 = $form->clear();
        $result = [
            "IsSucceed"=>($i0)?true:false
        ];
        echo json_encode($result);
    }elseif($_GET["action"] == "formSetting"){
        $post = file_get_contents('php://input', 'r');
        $post = json_decode($post,true);
        $post["name"] = htmlspecialchars($post["name"],ENT_QUOTES);
        $post["description"] = htmlspecialchars($post["description"],ENT_QUOTES);
        $post["end_time"] = htmlspecialchars($post["end_time"],ENT_QUOTES);
        $post["start_time"] = htmlspecialchars($post["start_time"],ENT_QUOTES);
        $post["submit_per_ip"] = htmlspecialchars($post["submit_per_ip"],ENT_QUOTES);
        //类型检查,数据范围检查
        $i0 = $form->setting($post);
        $result = [
            "IsSucceed"=>($i0)?true:false
        ];
        echo json_encode($result);
    }elseif($_GET["action"] == "formEdit"){
        //这是控制页面显示而已,页面显示控制还是交给前端好咯,尤其想这些富文本的编辑
        
    }elseif($_GET["action"] == "formSave"){
        $post = file_get_contents('php://input', 'r');
        $post = json_decode($post,true);
        $i0 = $form->set_style(htmlspecialchars($post['styleConfig']));
        $config = json_encode($post['columnConfig']);
        $i1 = $form->column_config(urlencode($config));
        $result = [
            "IsSucceed"=>($i0 && $i1)?true:false,
        ];
        echo json_encode($result);
    }elseif($_GET["action"] == "result"){
        $i0 = $form->export_table();
        echo json_encode($i0);
    }elseif($_GET["action"] == "get_csv"){
        $content = $form->download_csv();
        Header("Content-type: application/octet-stream");
        Header("Accept-Ranges: bytes");
        Header("Accept-Length:".strlen($content));
        Header("Content-Disposition: attachment; filename=".$form->name.".csv");
        echo $content;
    }
}elseif(isset($_GET['act'])){//通用的数据统计
    //通用的
    if($_GET['act'] == 'feeback'){
        $post = file_get_contents('php://input', 'r');
        $data = json_decode($post,true);
        $chk = insert('feeback',[
            'type'=>'表单系统',
            'problem'=> htmlspecialchars($data['content'],ENT_QUOTES)
        ]);
        if($chk){
            echo json_encode([
                'isOk'=>true
            ]);
        }
    }elseif($_GET['act'] == 'init_num'){
        $like_sql = select('like_log',['count'],['name'=>appName]);
        $use_sql = select('log',['count'],['name'=>appName]);
        if($like_sql && $use_sql){
            $like = mysql_fetch_row($like_sql)[0];
            $use = mysql_fetch_row($use_sql)[0];
        }else{
            $like = $use = 0;
        }
        echo json_encode([
            'num_of_like'=>intval($like),
            'num_of_use'=>intval($use),
        ]);
    }elseif($_GET['act'] == 'like'){
        logLike(appName);
    }
}

//action.php主要做一些输入数据的预处理,还有就是数据的输出