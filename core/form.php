<?php

class Form{
    //form setting 暂存,关于变量的权限还是要一开始规定下来的,不过之后再考虑吧
    var $ID = NULL;
    var $user = NULL;
    var $name = NULL;
    var $description = NULL;
    var $isReleased = NULL;
    var $startTime = NULL;
    var $endTime = NULL;
    var $input_table_id = NULL;
    var $release_url = NULL;
    var $ip_table_id = NULL;
    var $submit_per_ip = NULL;
    var $style_setting = NULL;
    var $column_config = NULL;
    function __construct($USER){
        //把user到进来
        $this->user = $USER; 
        //解压cookie和url_arg中的FormID
        //cookie的数据过滤那里做呢?action.php还是decode的时候呢?,还是每个sql语句调用的时候做htmlspecial
        if(isset($_GET["form"])){
            $formId = web_decode($_GET["form"]);
            //检查ID是否合法,ID是数字组成的所以直接inval就可以了,但是为了结构上的一致,分开来单独处理
            $formId = intval($formId);
            $formId_sql = select("forms_setting",["*"],["form_id"=>$formId , "user_id"=>$this->user->ID]);
            if($formId_sql){
                $this->ID = $formId;
                //还是选择性解压一些吧,主要是因为每一步都是相对独立的,都要从要重新解压,这个相当于更新,不过为了防止还是要手动更新一下的
                $form_setting = mysql_fetch_assoc($formId_sql);
                if($form_setting){
                    $this->isReleased = $form_setting["is_released"];
                    $this->startTime = $form_setting["start_time"];
                    $this->name = $form_setting["name"];
                    $this->description = $form_setting["description"];
                    $this->endTime = $form_setting["end_time"];
                    $this->input_table_id = $form_setting["input_table_id"];
                    $this->release_url = $form_setting["release_url"];
                    $this->submit_per_ip = intval($form_setting["submit_per_ip"]);
                    $this->ip_table_id = $form_setting["ip_table_id"];
                    $this->style_setting = $form_setting["style_setting"];
                    $this->column_config = urldecode($form_setting["column_config"]);
                    //终于发现好处了,即便是那些提前可以知道的,但是还是要拼接起来,不如这里直接读取来的爽快
                    //直接把有关的数据项解压出来真的很方便
                }else{
                    echo '解压数据失败~';
                }
            }
        }
    }
    public function create($args){
        //页面更新,数据库更新,列表更新
        //找一个可用的form_id
        $Form_id = $this->getAvailableFormId();
        if($Form_id){
            //先新建关联表//数据表可以在release阶段建立,不过也可以,只是方便确认在删除,清空表的时候表是存在的
            $table_name = INPUT_.$this->user->ID."_".$Form_id;
            $table_ip_name = IP_TABLE.$this->user->ID."_".$Form_id;
            $i0 = newTable($table_name,"id int NOT NULL AUTO_INCREMENT,PRIMARY KEY (id)");//不能缺省col新建
            $i1 = newTable($table_ip_name,"id int NOT NULL AUTO_INCREMENT,ip varchar(128),count int(10),PRIMARY KEY (id)");
            // echo mysql_error();
            if($i0 && $i1){
                $i2 = insert("forms_setting",[
                    "user_id"=>$this->user->ID ,
                    "form_id"=>$Form_id ,
                    "name"=>$args["name"],
                    "description"=>$args["description"],
                    "is_released"=>0,
                    "start_time"=>'1970-01-02 00:00:00',//时间默认值,0代表际关闭滴
                    "end_time"=>'1970-01-02 00:00:00',
                    "input_table_id"=>$table_name,
                    "release_url"=>"submit.php?login=".web_encode($this->user->ID)."&form=".web_encode($Form_id),
                    //由userID+formID转换之后的字符串
                    "ip_table_id"=>$table_ip_name,
                    "submit_per_ip"=>0,//0是代表关闭,默认关闭吧
                    "style_setting"=>"",
                    "column_config"=>"",
                    ]);
                if($i2){
                    $this->ID = $Form_id;//更新一下
                    return true;
                }else{
                    echo mysql_error();
                }
            }
        }
        return false;
    }
    public function setting($data){
        //还是简单一点吧,数据流完全依靠前端的输入
        $i0 = update("forms_setting",[
            "start_time"=>$data["start_time"],
            "end_time"=>$data["end_time"],
            "name"=>$data["name"],
            "description"=>$data["description"],
            "submit_per_ip"=>$data["submit_per_ip"]
            ],[
                "user_id"=>$this->user->ID,
                "form_id"=>$this->ID
            ]);
        if(!$i0) echo mysql_error();
        return ($i0)?true:false;
    }
    public function del(){
        //$forms_setting = select("forms_setting",["*"],["user_id"=>$this->user->ID ,"form_id"=>$this->ID]);
        //取出这4个表
        // input__$userID_$FormID
        // input_file_$userID_$FormID
        // input_setting_$userID_$FormID
        // ip_table_$userID_$FormID
        //其实发现是不需要在foms_setting里面储存这些表的表名的,因为可以提前知道的,可以省去一次mysql_query
        //删掉删掉,还是留下来好了,不想改动,这里不进行mysql_query就好了
        //file表处理
        $table_name = INPUT_.$this->user->ID."_".$this->ID;
        $table_ip_name = IP_TABLE.$this->user->ID."_".$this->ID;
        //遍历删除文件@待写
        
        //然后可以删除这4个表了
        drop($table_name);
        drop($table_ip_name);
        //然后删除这个条目
        $i0 = remove("forms_setting",["user_id"=>$this->user->ID,"form_id"=>$this->ID]);
        return ($i0)?true:false;
    }
    public function release(){
        $i0 = update("forms_setting",["is_released"=>1],["user_id"=>$this->user->ID,"form_id"=>$this->ID]);
        if(!$i0) echo mysql_error();
        //根据column_setting来初始化表的行列
        $config = json_decode($this->column_config,true);
        //要把原来有数据的地方给清空了,主要因为标识符和顺序绑定了
        //还是不清空了,仅仅当有重复的时候就改就可以了
        // $check_sql = select($this->input_table_id,['*']);
        // $check = mysql_fetch_row($check_sql)[0];
        //如果是空数据就不会返回咯,不行
        //要重爱一个更直接的方案
        //难道真的是要把drop table,然后在新建?感觉是简单
        $i1 = drop($this->input_table_id);
        $i2 = newTable($this->input_table_id,"id int NOT NULL AUTO_INCREMENT,PRIMARY KEY (id)");
        //生成 form_singleline_input
        foreach($config['form_singleline_input'] as $key => $value){
            $i5 = add_col($this->input_table_id,$value['column_setting'],toColType(1));
            if(!$i5) echo mysql_error();
        }
        //生成 form_multiline_input
        foreach($config['form_multiline_input'] as $key => $value){
            $i6 = add_col($this->input_table_id,$value['column_setting'],toColType(1));
            if(!$i6) echo mysql_error();
        }
        //生成 form_singlechoose_input
        foreach($config['form_singlechoose_input'] as $key => $value){
            $i3 = add_col($this->input_table_id,$value['column_setting'],toColType(1));
            if(!$i3) echo mysql_error();
        }
        //生成 form_multichoose_input
        foreach($config['form_multichoose_input'] as $key => $value){
            foreach($value['column_setting'] as $col){
                $i4 = add_col($this->input_table_id,$col,toColType(1));
                if(!$i4) echo mysql_error();
            }
        }
        return ($i0)?true:false;
    }
    public function unrelease(){
        $i0 = update("forms_setting",["is_released"=>0],["user_id"=>$this->user->ID,"form_id"=>$this->ID]);
        return ($i0)?true:false;
    }
    public function clear(){//指的是清空数据表g
        $table_name = INPUT_.$this->user->ID."_".$this->ID;
        $table_ip_name = IP_TABLE.$this->user->ID."_".$this->ID;
        
        //删除文件
        
        //然后可以清空这4个表了
        $i0 = truncate($table_name);
        $i1 = truncate($table_ip_name);
        return ($i0 && $i1)?true:false;
    }
    public function export(){
        //这是返回一段二维的数组
        // $arr = array();
        // $input_setting_sql = select($this->input_setting,["*"],["user_id"=>$this->user->ID,"form_id"=>$this->ID]);
        // $input_setting_arr = array();
        // if($input_setting_sql)
        //     while($i = mysql_fetch_assoc($input_setting_sql)){
        //         array_push($input_setting_arr,$i);
        //     };
        // $len_row = sql_count($this->input_table_id,["*"]);
        // $len_col = sql_count($this->input_setting,["*"]);
        // $text_sql = select($this->input__table_id,["*"]);
        // $file_sql = select($this->input_file_table_id,["*"]);
        // for($i =0; $i < $len_row;$i++){
        //     //获取file行
        //     $text = mysql_fetch_row($text_sql);
        //     //获取text列
        //     $file = mysql_fetch_row($file_sql);
        //     $text_i = 0;
        //     $file_i = 0;
        //     for($j =0; $j < $len_col;$j++){
        //         //按照input_setting里面的顺序剔除,所以要按照这样来还原,唉,感觉没必要做这个分离的感觉数据储存都是一样的
        //         if($input_setting_arr[$j]["type"] == 0 || $input_setting_arr[$j]["type"] == 1 ){
        //             $arr[$i][$j] = $text[$text_i];
        //             $text_i++;
        //         }elseif($input_setting_arr[$j]["type"] == 2){
        //             $arr[$i][$j] = $file[$file_i];
        //             $file_i++;
        //         }
        //     }
        // }
        $arr = [
            'data'=>array(),
            'config'=>json_decode(urldecode($this->column_config),true)
        ];
        $result_sql = select($this->input_table_id,['*']);
        while($result = mysql_fetch_assoc($result_sql)){
            array_push($arr['data'],urlDecodeArg($result));
        }
        if(empty($arr['data']))
            return null;//可以用于判断存在性
        return $arr;
    }
    public function download_csv(){
        $arr = $this->export();
        $data = $arr['data'];
        $config = $arr['config'];
        if($data != null){
            //生成cvs格式的文本
            //首行,是列头描述
            //接下来是导出内容,null字段使用''替换,还有还是带上双引号吧
            //如果没有数据记录是无法生成表头的,不过这里已经判断出是有数据域的
            //id有点难去掉..所以保留输出,感觉小写输出也没啥问题,这个区别不大,又不是硬性要求
            ob_start();
            $head_arr = array();
            $value_arr = array();
            foreach($data[0] as $key => $value){
                if(!is_bool(strpos($key,'multichoose'))){
                    //提取下标
                    preg_match('/([\d]+)_([\d]+)/',$key,$subscript);
                    $key = $config['form_multichoose_input'][$subscript[1]]['column_description'][$subscript[2]];
                    array_push($value_arr,'tickMark');
                }else{
                    preg_match('/[\d]+/',$key,$subscript);
                    if(!is_bool(strpos($key,'multiline'))){
                        $key = $config['form_multiline_input'][$subscript[0]]['description'];
                        array_push($value_arr,'original');
                    }elseif(!is_bool(strpos($key,'singlechoose'))){
                        $key = $config['form_singlechoose_input'][$subscript[0]]['description'];
                        $value_descripttion = $config['form_singlechoose_input'][$subscript[0]]['value_description'];
                        $value_descripttion['class'] = 'valueDescription';
                        array_push($value_arr,$value_descripttion);
                    }elseif(!is_bool(strpos($key,'singleline'))){
                        $key = $config['form_singleline_input'][$subscript[0]]['description'];array_push($value_arr,'original');
                    }else{
                        array_push($value_arr,'original');//id
                    }
                }
                array_push($head_arr,$key);
            }
            foreach($head_arr as $key => $value){
                $head_arr[$key] = str_replace('"','""',$value);//转义双引号
            }
            echo implode($head_arr,',')."\r\n";//输出行头
            foreach($data as $i => $value){
                $j = 0;
                foreach($value as $key => $var){
                    if(is_string($value_arr[$j])){
                        if($value_arr[$j] == 'original'){
                            //do nothing
                        }elseif($value_arr[$j] == 'tickMark'){
                            if($var != null){
                                $data[$i][$key] = '√';
                            }
                        }
                    }elseif(is_array($value_arr[$j])){
                        if($value_arr[$j]['class'] == 'valueDescription'){
                            $data[$i][$key] = $value_arr[$j][$var];
                        }
                    }
                    $j++;
                }
                echo implode($data[$i],',')."\r\n";
            }
            $csv = ob_get_contents();
            ob_clean();
            return $csv;
        }else
            return '';//返回空文件
    }
    public function export_table(){
        $arr = $this->export();
        $data = $arr['data'];
        $config = $arr['config'];
        $tr_arr = array();
        if($data != null){
            $head_arr = array();
            $value_arr = array();
            foreach($data[0] as $key => $value){
                if(!is_bool(strpos($key,'multichoose'))){
                    //提取下标
                    preg_match('/([\d]+)_([\d]+)/',$key,$subscript);
                    $key = $config['form_multichoose_input'][$subscript[1]]['column_description'][$subscript[2]];
                    array_push($value_arr,'tickMark');
                }else{
                    preg_match('/[\d]+/',$key,$subscript);
                    if(!is_bool(strpos($key,'multiline'))){
                        $key = $config['form_multiline_input'][$subscript[0]]['description'];
                        array_push($value_arr,'original');
                    }elseif(!is_bool(strpos($key,'singlechoose'))){
                        $key = $config['form_singlechoose_input'][$subscript[0]]['description'];
                        $value_descripttion = $config['form_singlechoose_input'][$subscript[0]]['value_description'];
                        $value_descripttion['class'] = 'valueDescription';
                        array_push($value_arr,$value_descripttion);
                    }elseif(!is_bool(strpos($key,'singleline'))){
                        $key = $config['form_singleline_input'][$subscript[0]]['description'];array_push($value_arr,'original');
                    }else{
                        array_push($value_arr,'original');//id
                    }
                }
                array_push($head_arr,$key);
            }
            array_push($tr_arr,$head_arr);
            foreach($data as $i => $value){
                $j = 0;
                $td_arr = array();
                foreach($value as $key => $var){
                    if(is_string($value_arr[$j])){
                        if($value_arr[$j] == 'original'){
                            $td_arr[$j] = $data[$i][$key];
                        }elseif($value_arr[$j] == 'tickMark'){
                            if($var != null){
                                $td_arr[$j] = '√';
                            }else{
                                $td_arr[$j] = '';
                            }
                        }
                    }elseif(is_array($value_arr[$j])){
                        if($value_arr[$j]['class'] == 'valueDescription'){
                            if(is_string($var)){
                                $td_arr[$j] = $value_arr[$j][$var];
                            }else{
                                $td_arr[$j] = '';
                            }
                            //如果没有选择的时候怎么办
                        }
                    }
                    $j++;
                }
                array_push($tr_arr,$td_arr);
            }
            return $tr_arr;
        }else
            return null;
    }
    public function set_style($data){
        //这里的data应该只是包含DOM数据,默认已经是认为安全输入,安全过滤还是要在action部分完成
        $i0 = false;
        if($this->ID)
            $i0 = update("forms_setting",["style_setting"=>$data],["user_id"=>$this->user->ID,"form_id"=>$this->ID]);
        echo mysql_error();
        return ($i0)?true:false;
    }
    public function column_config($data){
        //这里的data应该只是包含DOM数据,默认已经是认为安全输入,安全过滤还是要在action部分完成
        $i0 = false;
        if($this->ID){
            $i0 = update("forms_setting",["column_config"=>$data],["user_id"=>$this->user->ID,"form_id"=>$this->ID]);
            $this->column_config = urldecode($data);
            if($this->isReleased == true){
                $this->release();
            }
        }
        echo mysql_error();
        return ($i0)?true:false;
    }
    public function set_input($data){
        //改了仅仅储存json就可以了
        //每次都是清空表重新插入吗?不过这也合理,没有人会编辑数据时候结搜数据
        //还是要把之前的数据导出来一下
        $char_col = array();
        $int_col = array();
        $file_col = array();//还是会横向变化的哦
        $old_input_setting_sql = select($input_setting,["*"]);
        while($old_arr = mysql_fetch_assoc($old_input_setting_sql)){
            if( $old_arr["type"]  == 0 ){
                array_push($char_col,$old_arr["name"]);
            }elseif( $old_arr["type"]  == 1 ){
                array_push($int_col,$old_arr["name"]);
            }elseif( $old_arr["type"]  == 2 ){
                array_push($file_col,$old_arr["name"]);
            }
        }
        $i0 = truncate($input_setting);
        for($i = 0 ,$len = count($data["text"]); $i < $len;$i++){
            $i1 = insert($input_setting,[
                "name"=>$data["text"][$i]["name"],
                "type"=>$data["text"][$i]["type"],
                "description"=>$data["text"][$i]["description"],
                ]);
            if(!$i){
                echo mysql_error();
                return false;
            }
        }
        //这里array还要有name字段的重复验证,不然会提示出错的,这里不过出错也就是那一条出错而已,不会有数据损失吧
        
        //然后是更新其他两个数据库的列,根据name字段,两个表自带ID的字段,不过没有啥用,占位符而已
        //不要复杂,把原来的列全部删除,然后重新增加
        //清除原来的列text,file
        $i1 = drop_col($input_text_table_id,array_merge($char_col,$int_col));
        $i2 = drop_col($input_file_table_id,$file_col);
        //插入col,以后会在细分的组件的,所以不能简单通过一个通用类型搞定,类型信息将会有type所绑定
        //生成类型信息
        $type_arr = array();
        for($i = 0 ,$len = count($data["text"]); $i < $len;$i++){
            array_push( $type_arr, toColType($data["text"][$i]["type"]) );
            //算了在这里边生成类型信息,边把$data["text"]转换为一维数组,储存的是name值
            $data["text"][$i] = $data["text"][$i]["name"];
        }
        $file_type_arr = array();
        for($i = 0 ,$len = count($data["file"]); $i < $len;$i++){
            array_push( $file_type_arr, toColType($data["file"][$i]["type"]) );
            $data["file"][$i] = $data["file"][$i]["name"];
        }
        //新建列
        $i3 = add_col($input__table_id,$data["text"],$type_arr);
        $i4 = add_col($input_file_table_id,$data["file"],$file_type_arr);
        //是否清空ip_table??@待定

        return ($i0 && $i1 && $i2 && $i3 && $i4)?true:false;
    }
    public function get_list(){//打印出表单列表
        $list_sql = select("forms_setting",["*"],["user_id"=>$this->user->ID]);
        if($list_sql){//这个表示不了空的,现在感觉很多用于错误检测的代码,感觉不靠谱
            $dom = "";
            while($form = mysql_fetch_assoc($list_sql)){
                $dom .='<div class="form_list_item" formid="'.urlencode(web_encode($form["form_id"])).'" isRelease="'.$form["is_released"].'">';
                $dom .='<a title="'.$form["description"].'" href="#">';
                $dom .='<div class="status"></div>';
                $dom .='<div class="illustration"></div>';
                $dom .='<div class="name"><span>'.$form["name"].'</span></div>';
                $dom .='</a>';
                $dom .='</div>';
            }
            return ($dom!='')?$dom:"<div style='width:100%;text-align:center;'>空</div>";
        }
    }
    public function get_setting(){
        $setting = [
            "start_time" =>$this->startTime,
            "end_time" =>$this->endTime,
            "name" =>$this->name,
            "description" =>$this->description,
            "submit_per_ip" =>$this->submit_per_ip
            ];
        return $setting;
    }
    public function edit(){//现在感觉到C和V混合在一起了
        $style_sql = select("forms_setting",["style_setting"],["user_id"=>$this->user->ID,"form_id"=>$this->ID]);
        $style = htmlspecialchars_decode(mysql_fetch_row($style_sql)[0]);
        // $input_setting_sql = select($this->input_setting,["*"]);
        // $input_setting_arr = array();
        // if($input_setting_sql)
        //     while($i = mysql_fetch_assoc($input_setting_sql)){
        //         array_push($input_setting_arr,$i);
        //     }
        // $style_json = json_encode($input_setting_arr);

        //还是拼接dom
        // $dom = '<script>';//var $styleData = '.$style_json.';
        // $dom .= 'var $isEditing = true;';
        // $dom .= '</script>';
        return $style;
    }
    // public function file_upload(){}//暂时是dateurl保存图片就可以了
    public function read_setting($setting){//给其他操作提供设置读操作
        //想这些设置到底要不要暂存到PHP变量里面呢?,如果要的话就是说之后会设置有关变量数据同步问题,其实也不大
        //通过暂存到PHP,优化的速度的第二次之后读取的速度不过几乎每次操作 ,变量都要新建一下,算了SQL能少就少了吧
        //并且感觉这一部分判断会比较多的样子,这里就是多了一个维护而已
        //把cookie的数据解析绑定到class的构造函数去?
        //所以这里置空了
    }
    public function submit($data){
        //zip data
        //check release
            //check time
                //check ip
                    //submitdata
                    //log ip
        // var_dump($this->checkIp());
        $checkTime = $this->checkTime();
        $checkIp = $this->checkIp();
        if($this->isReleased == true && $checkTime == true && $checkIp == true){
            //都是一维数组所以不怕,不过没有验证,出错直接报错了,但是ip还是算是记录的了
            // var_dump($data);
            $i0 = insert($this->input_table_id,$data);
            echo mysql_error();
            echo 'here';
            return $i0;
        }else{
            if($this->isReleased != true){
                return '表单尚未发布~';
            }
            if($checkTime != true){
                return $checkTime;
            }
            if($checkIp != true){
                return '你的提交次数超过限制了';
            }
        }
    }
    function getAvailableFormId(){
        $available = true;
        $generateCount = 0;
        $Form_id_sql = select("forms_setting",['form_id'],["user_id"=>$this->user->ID]);
        $i = 0;
        while($Form_id[$i] = mysql_fetch_row($Form_id_sql)[0]){//感觉多做了很多的无畏的工作,让计算机
            $i++;
        }//之后将会使用php提供的mysql救过导航指针进行反复遍历的
        while($available && $generateCount++ <= 15){//尝试生成ID最大次数
            $available = true;
            $id = rand(1, 9999);
            for($j = 0; $j < $i; $j++){
                if($Form_id[$j] == $id){
                    $available = false;
                    break;
                }
            }
        }
        if($available)
            return $id;
        else
            return false;
    }
    function checkIp(){
        $clentip = getClientIp();
        $ip_count_sql = select($this->ip_table_id,['count'],['ip'=>$clentip]);
        if($ip_count_sql){
            $ip_count = mysql_fetch_row($ip_count_sql)[0];
            if($this->submit_per_ip != 0){
                if($ip_count){
                    $ip_count = intval($ip_count);
                    if($this->submit_per_ip >= $ip_count){
                        $i0 = update($this->ip_table_id,['count'=>($ip_count+1)],['ip'=>$clentip]);
                    }
                    else
                        return false;
                }else{
                    $i0 = insert($this->ip_table_id,['ip'=>$clentip,'count'=>1]);
                }
                return ($i0)?true:false;
            }
        }
        return true;
    }
    function checkTime(){
        //如果没有设置time默认是strtotime(返回false)
        //update 默认时间为 1970-01-02 00:00:00
        $defaultTime = strtotime('1970-01-02 00:00:00');
        $startTime = strtotime($this->startTime);
        $endTime = strtotime($this->endTime);
        if($startTime != $defaultTime){//startTime已经设置了
            if($endTime != $defaultTime){
                if($startTime > $_SERVER['REQUEST_TIME'])
                    return '表单尚未开始~';
                if($endTime < $_SERVER['REQUEST_TIME'])
                    return '表单尚已结束~';
                return true;
            }else{
                if($startTime < $_SERVER['REQUEST_TIME'])
                    return true;
                else
                    return '表单尚未开始~';
            }
        }else{
            if($endTime != $defaultTime){//endTime已经设置了
                if($endTime > $_SERVER['REQUEST_TIME'])
                    return true;
                else
                    return '表单尚已结束~';
            }else{
                return true;
            }
        }
    }
}