
// !function(){
//     var loginOrRegister = 0;//0 for login ;1 for register
//     //注册事件
//     var nameAvailable = false;
//     $$('bt1')[0].onclick = function(){//切换到登陆
//         loginOrRegister = 0;
//         //清空input
//         $('id').value = 'name';
//         $('pw').value = 'password';
//         $('pw').setAttribute('type','text');
//         //设置样式
//         css($$('bt1')[0],{
//             'background':'black',
//             'color':'white'
//         });
//         css($$('bt2')[0],{
//             'background':'white',
//             'color':'black'
//         });
//     };
//     $$('bt2')[0].onclick = function(){//切换到注册
//         loginOrRegister = 1;
//         $('id').value = 'name';
//         $('pw').value = 'password';
//         $('pw').setAttribute('type','text');
//         //设置样式
//         css($$('bt2')[0],{
//             'background':'black',
//             'color':'white'
//         });
//         css($$('bt1')[0],{
//             'background':'white',
//             'color':'black'
//         });
//     };
//     $$('bt3')[0].onclick = function(){//submit
//         var id = web_encode($('id').value);
//         var pw = web_encode($('pw').value);
//         if(checkValue($('id').value) && checkValue($('pw').value))
//         if(!loginOrRegister){//login
            // ajax('post','action.php',{'action' : 'login'},JSON.stringify({
            //     'name':id,
            //     'pw':pw
            // }),function(rep){
            //     var result = JSON.parse(rep);
            //     if(result.IsSucceed){
            //         location.href = 'admin.php';
            //     }else{
            //         alert('密码,或者用户名错误~');
            //     }
            // });
//         }else{//register
//             if(nameAvailable)
//                 ajax('post','action.php',{'action' : 'register'},JSON.stringify({
//                     'name':id,
//                     'pw':pw
//                 }),function(rep){
//                     var result = JSON.parse(rep);
//                     if(result.IsSucceed){
//                         //切换到登陆
//                         alert('注册成功~');
//                     }else{
//                         alert('未知错误~');
//                     }
//                 });
//             else{
//                 alert('用户名被占用了~~');
//             }
//         }
//     };
//     //js设置默认值
//     $('id').onfocus = function(){
//         if(this.value == 'name' || this.value == 'password'){
//             this.value = '';
//         }
//     };
//     $('pw').onfocus = function(){
//         if(this.value == 'name' || this.value == 'password'){
//             this.value = '';
//             this.setAttribute('type','password');
//         }
//     };
//     $('id').onblur = function(){
//         var _this = this;
//         if(this.value == ''){
//             this.value = 'name';
//         }else if(loginOrRegister){
//             //checkName
//             ajax('post','action.php',{'action' : 'checkname'},JSON.stringify({
//                 'name':this.value,
//             }),function(rep){
//                 var result = JSON.parse(rep);
//                 if(result.IsSucceed){
//                     //set status available
//                     _this.style.border = '';
//                     nameAvailable = true;
//                 }else{
//                     //set status none available
//                     nameAvailable = false;
//                 }
//             });
//         }
//     };
//     $('pw').onblur = function(){
//         if(this.value == ''){
//             this.value = 'password';
//             this.setAttribute('type','text');
//         }
//     };
    // function checkValue(str){
    //     //输入字串不包含特殊字符
    //     if(str.length < 25 && str.search('/[\'|"|]/g') == -1){
    //         return true;
    //     }
    //     return false;
    // }
// }


var date = new Date();
var wd = date.getDay();
if(wd == 0) wd = 7;

var week_day = ['一','二','三','四','五','六','日'];
var weekly = ['','单','双'];

var userName = inputHit($('userName'));
var userPW = inputHit($('userPW'));
var userPWAgain = inputHit($('userPWAgain'));

//注册,登陆
var loginOrRegister = new option($$('swtichLogInRegister')[0],'span','selected');
//不行,需要抽象一下,打包成一个option
function option(father,subType,toogleClass){
    var child = father.getElementsByTagName(subType);
    this.value = child[0].getAttribute('value');
    console.log(child);
    function resetStyle(){
        for(var i = 0; i < child.length ;i++){
            child[i].setAttribute('class','');
        }
    }
    this.set = function(which){
        resetStyle();
        which.setAttribute('class',toogleClass);
        this.value = which.getAttribute('value');
    };

}
//比较多互斥的事件
$('setLogin').onclick = function(){
    loginOrRegister.set(this);
    css($('userPWAgain'),{
        'display':'none'
    });
}
$('setRegister').onclick = function(){
    loginOrRegister.set(this);
    css($('userPWAgain'),{
        'display':'block'
    });
}

//注册时候检查输入用户名是否有效,这里需要addEventlistener
$('userName').addEventListener('blur',function(){
    if(loginOrRegister.value == 'register'){
        //checkname
        var userName = $('userName').value;
        if(checkValue(userName)){
            ajax('post','action.php',{'action' : 'checkname'},JSON.stringify({
                'name':userName,
            }),function(rep){
                var result = JSON.parse(rep);
                if(result.IsSucceed){
                    //set status available
                    useNameAvailable = true;
                }else{
                    //set status none available
                    useNameAvailable = false;
                    alert('名字已被占用了~');
                }
            });
        }
    }
});

$$('buttom_submit')[0].addEventListener('click',function(){
    if($('userName').value == userName.defalutText() || $('userPW').value == userPW.defalutText()){
        alert('空即是色,色既是空~');
        return 0;
    }
    if(loginOrRegister.value == 'login'){
        ajax('post','action.php',{'action' : 'login'},JSON.stringify({
            'name':userName.value(),
            'pw':userPW.value()
        }),function(rep){
            var result = JSON.parse(rep);
            if(result.IsSucceed){
                location.href = 'admin.php';
            }else{
                alert('密码,或者用户名错误~');
            }
        });
    }else if(loginOrRegister.value == 'register'){
        if(userPW.value() != userPWAgain.value()){
            alert('两次密码输入不同~');
        }else{
            ajax('post','action.php',{'action' : 'register'},JSON.stringify({
                'name':userName.value(),
                'pw':userPW.value()
            }),function(rep){
                var result = JSON.parse(rep);
                if(result.IsSucceed){
                    alert('注册成功~');
                    //切换到登陆
                    $('setLogin').onclick();
                }else{
                    alert('未知错误~');
                }
            });
        }
    }
});

//样式初始化

$$('logo')[0].getElementsByTagName('img')[0].src =  'img/hello-'+wd+'.jpg';


function checkValue(str){
    //输入字串不包含特殊字符
    if(str.length < 25 && str.search('/[\'|"|]/g') == -1){
        return true;
    }
    return false;
}