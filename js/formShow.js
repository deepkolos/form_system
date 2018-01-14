!function(){
    for(var i = 0,len = $$('input_text_title').length ; i <len ;i++){
        css($$('input_text_title')[i],{
            'background':'none'
        });
        $$('input_text_title')[i].contentEditable = false;
    }
    for(var i = 0,len = $$('center').length ; i <len ;i++){
        if($$('center')[i].getElementsByTagName('div')[0])
            $$('center')[i].getElementsByTagName('div')[0].contentEditable = false;
    }
}();

//添加form提交按钮,走标准的浏览器提交还是走ajax

//ajax:提交的元素需要手动获取,并且表单提交的项非固定的,结果显示比较友好,并且可以验证一些输入数据

//标准:自动根据表单的东西提交对应的value

//下一步整合ajax需要手动获取提交表单项的值,把这一步变成和标准的一样自动化

//先走标准提交,然后location来指定成功与失败

if($('bt_submit'))//防止preview的时候,提交表单,不过使用的人,都会额外测试数据,不过还是禁止吧,因为感觉增加了release权限
    $('bt_submit').onclick = function(){
        //数据验证,这一部分也可以整合
        document.getElementsByTagName('form')[0].submit();
    }

//input按钮的点击与div绑定

for(var i = 0 ; i < $$('input_container').length; i++){
    var div = $$('input_container')[i].getElementsByClassName('input_text_title');
    for(var j = 0 ; j < div.length; j++){
        div[j].onclick = new function(){
            var _this = div[j];
            return function(){
                _this.parentNode.getElementsByTagName('input')[0].click();
            }
        }
    }
}