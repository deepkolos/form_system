<?php

class User{//目前只是归类一些func而已,class的作用
    //登陆
    var $ID = NULL;
    var $Name = NULL;
    var $IsLogin = false;
    public function login($name,$pw){
        //登陆信息储存在cookie里面咯,加密储存,不过应该设置的是ID而不是name,搞错了,不过使用加密之后就不怕了
        // $name = web_decode($name);
        // $pw = web_decode($pw);
        // 传过来的是明文密码
        if(!is_bool($name) && !(is_bool($pw))){
            $name = htmlspecialchars($name,ENT_QUOTES);
            $pw = htmlspecialchars($pw,ENT_QUOTES);
            $name_sql = select("user",["*"],["name"=>$name]);
            $user = mysql_fetch_assoc($name_sql);

            if( sql_decode($user['pw']) == $pw){
                //要是使用ID作为login的标识符
                
                //set cookies在哪里设置好呢?好像是属于前面的,好像又是在这里比较合适,上下文更接近数据
                setcookie("login", cookie_encode($user['id']) , time()+3600*24,null,null,null,true);
                return true;
            }
        }
        return false;
    }
    public function logout($name){
        //cookie name = NULL;
        setcookie("login", "" , time()+3600*24,null,null,null,true);
        return true;
    }
    //改为使用构造函数初始化变量
    function __construct(){
        if(isset($_COOKIE["login"])){//login,记录是usename
            $cookie_login = cookie_decode($_COOKIE["login"]);
            $this->IsLogin = $this->checkLogin($cookie_login);//来自cookie的就是表明已登录验证的
        }
        if(isset($_GET["login"])){
            $url_login = web_decode($_GET["login"]);
            $this->checkLogin($url_login);//来自GET的仅仅是为了解压参数
        }
    }
    public function checkLogin($id){//由cookie提取出来的login字段,其实是储存了id内容
        //感觉这样判断是否登陆的条件不太好,因为如果别人遍历login字段的cookies就很容易碰撞到其中一个用户的加密
        //所以必须参杂,不过想想这是decode和encode的事情,与这里无关哦
        // $id = cookie_decode($id);
        //查库都要加一下转义,要么剔除,不过还是转义是通用点
        $id = htmlspecialchars($id,ENT_QUOTES);
        //算了,每次都要养成进调用sql语句进行把特殊字符转义,这个特殊字符其实是,分隔符
        $id_sql = select("user",["*"],["id"=>$id]);
        //login里面的字段如果改变的话,就要需要改变判断条件
        if( $id_sql = mysql_fetch_assoc($id_sql) ){
            //设置ID
            $this->ID = $id_sql['id'];
            $this->Name = $id_sql['name'];
            return true;
        }
        else
            return false;
    }
    //注册
    public function register($name,$pw,$other){//other用于以后拓展
        if($this->checkNameAvaible($name)){//再检查一遍
            //生成唯一ID
            if($id = $this->getAvailableID()){
                //加密PW
                $pw = sql_encode($pw);
                //插入到User表中
                if( insert("user",array_merge(["id"=>$id,"name"=>$name,"pw"=>$pw],$other)) ){
                    return true;//返回信息注册成功done~
                }else{
                    echo mysql_error();
                }
            }
        }
        return false;
    }
    public function checkNameAvaible($name){
        $available = true;
        $name_sql = select("user",['name']);
        while($i = mysql_fetch_row($name_sql)){
            if($i[0] == $name){
                $available = false;
                break;
            }
        }
        return $available;
    }
    function getAvailableID(){
        $available = true;
        $generateCount = 0;
        $id_sql = select("user",['id']);
        $i = 0;
        while($id_arr[$i] = mysql_fetch_row($id_sql)[0]){//感觉多做了很多的无畏的工作,让计算机
            $i++;
        }//之后将会使用php提供的mysql救过导航指针进行反复遍历的
        $id = rand(1, 999999);
        while(!$available && $generateCount++ <= 15){//尝试生成ID最大次数
            $available = true;
            $id = rand(1, 999999);
            for($j = 0; $j < $i; $j++){
                if($id_arr[$j] == $id){
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
}