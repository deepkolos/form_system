//算了全部自己再写一一遍,当作是自己的复习

//@NetWork

function ajax(method,url,arg,content,func){
	var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function(){
        if((xhr.status >= 200 && xhr.status < 300 && xhr.readyState == 4 ) || xhr.readyState == 304){
            func(xhr.responseText);
        }
    };
    xhr.open(method,url+arrToUrlArg(arg),true);
    xhr.send(content);
}
function arrToUrlArg(obj){
    var arg = '?';
    for(var p in obj)
        arg += p+'='+obj[p]+'&';
    return arg.slice(0,-1);
}

function setCookie(key,value,expiredays){
    var exdate = new Date();
    exdate.setDate(exdate.getDate() + expiredays);
    document.cookie = key+'='+escape(value)+((expiredays==null)?'':';expires='+exdate.toGMTString());
}
function getCookie(key){
    var cookies = document.cookie.split('; ');
    for(var i=0 ; i < cookies.length ; i++){
        if(cookies[i].indexOf(key+'=') != -1)
            return unescape(cookies[i].split(key+'=').join(''));
    }
    return -1;
}

//@DOM

function $(elem){
    return document.getElementById(elem);
}

function $$(elem){
    return document.getElementsByClassName(elem);
}

function css(elem,style){
    for(var p in style){
        elem.style[p] = style[p];
    }
}

//array

function checkIn(arg,arr){
    for(var i = 0; i < arr.length; i++){
        if(arr[i] == arg )
            return true;
    }
    return false;
}

function strToDom(str){
    var div = document.createElement('div');
    div.innerHTML = str;
    return div.firstElementChild;
}
function dump(dom){
    for(var p in dom){
        console.log(p+':'+dom[p]);
    }
}

function inputHit(dom_){
    return new function(){
        // var dom_ = dom;//不需要转存的,
        var _orginal_text = dom_.getAttribute('defalut');
        var _orginal_type = dom_.getAttribute('originalType') || dom_.getAttribute('type');
        //再js设置默认
        dom_.value = _orginal_text;
        dom_.onfocus = function(){
            if(this.value == _orginal_text){
                this.value = '';
                css(this,{
                    color:'black'
                });
            }
            if(_orginal_type.toLocaleLowerCase() == 'password'){
                this.setAttribute('type','password');
            }
        }
        dom_.onblur = function(){
            if(this.value == ''){
                this.value = _orginal_text;
                css(this,{
                    color:'lightgray'
                });
                if(_orginal_type.toLocaleLowerCase() == 'password'){
                    this.setAttribute('type','text');
                }
            }
        }
        this.value = function(){
            return dom_.value;
        }
        this.defalutText = function(){
            return _orginal_text;
        }
        this.isNotDefalut = function(){
            return _orginal_text == dom_.value;
        }
        //需要添加密码框的提示
    }
}