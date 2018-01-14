$('add').onclick = function(){
    //根据表格新建一个表单,然后返回到list去,唉,因为实现简单嘛
    //弹出一个配置框
    css($('addWindow'),{
        'display':'block'
    });
};
$('addWindow').getElementsByClassName('bt_ok')[0].onclick = function(){
    ajax('post','action.php',{'action':'newForm'},JSON.stringify({
        'name':$('add_name').value,
        'description':$('add_description').value
    }),function(rep){
        var result = JSON.parse(rep);
        if(result.IsSucceed){
            //跳转到list
            location.href = 'admin.php?action=list';
        }else{
            alert('未知错误~');
        }
    });
};
$('addWindow').getElementsByClassName('bt_cancel')[0].onclick = function(){
    $('add_name').value = '';
    $('add_description').value = '';
    css($('addWindow'),{
        'display':'none'
    });
};
$('skin').onclick = function(){
    location.href = 'admin.php?action=skin';
};
$('list').onclick = function(){
    location.href = 'admin.php?action=list';
};

var list = $$('form_list_item');
if(list.length > 0){
    for(var i=0;i < list.length;i++){
        //设置状态颜色
        if(list[i].getAttribute('isrelease') == '1'){
            css(list[i].getElementsByClassName('status')[0],{
                'background':'lightgreen'
            });
        }
        //绑定事件
        list[i].onclick = new function(){
            var formid = list[i].getAttribute('formid');
            return function(){//跳转到某个form的edit
                location.href = 'admin.php?action=about&form='+formid;
            };
        };
    }
}

if($editingForm != null){
    css($('home_nav'),{
        'display':'none'
    });
    css($('edit_nav'),{
        'display':'block'
    });
    $('back').onclick = function(){
        location.href='admin.php';
    };
    $('setting').onclick = function(){
        location.href='admin.php?action=setting&form='+$editingForm;
    };
    $('edit').onclick = function(){
        if($('swtich').getAttribute('isrelease') == '1'){
            css($('editFormConfirm'),{
                'display':'block'
            });
        }else
            location.href='admin.php?action=edit&form='+$editingForm;
    };
    $('editFormConfirm').getElementsByClassName('bt_cancel')[0].onclick= function(){
        css($('editFormConfirm'),{
                'display':'none'
            });
    }
    $('editFormConfirm').getElementsByClassName('bt_ok')[0].onclick= function(){
        location.href='admin.php?action=edit&form='+$editingForm;
    }
    $('result').onclick = function(){
        location.href='admin.php?action=result&form='+$editingForm;
    };
    //初始化样式

    if($('swtich').getAttribute('isrelease') == '1'){
        css($('swtichOn'),{
            'color':'white'
        });
        css($('swtichOff'),{
            'color':'#bebebe'
        });
    }else{
        css($('swtichOn'),{
            'color':'#bebebe'
        });
        css($('swtichOff'),{
            'color':'white'
        });
    }
    $('swtich').onclick = function(){
        if($('swtich').getAttribute('isrelease') == '1')
            ajax('post','action.php',{'action':'formUnRelease','form':$editingForm},'',function(rep){
                var result = JSON.parse(rep);
                if(result.IsSucceed){
                    //改变样式
                    css($('swtichOn'),{
                        'color':'#bebebe'
                    });
                    css($('swtichOff'),{
                        'color':'white'
                    });
                    $('swtich').setAttribute('isrelease','0');
                    location.href='admin.php?action=about&form='+$editingForm;
                }else{
                    alert('未知错误~');
                }
            });
        else
            ajax('post','action.php',{'action':'formRelease','form':$editingForm},'',function(rep){
                var result = JSON.parse(rep);
                if(result.IsSucceed){
                    //改变样式
                    css($('swtichOn'),{
                        'color':'white'
                    });
                    css($('swtichOff'),{
                        'color':'#bebebe'
                    });
                    $('swtich').setAttribute('isrelease','1');
                    css($('releaseUrlShowWindows'),{
                        'display':'block'
                    });
                }else{
                    alert('未知错误~');
                }
            });
    };
    $('releaseUrlShowWindows').getElementsByClassName('bt_close')[0].onclick = function(){
        css($('releaseUrlShowWindows'),{
            'display':'none'
        });
    }
    if($isFromSetting){
        $('bt_setting_submit').onclick = function(){
            ajax('post','action.php',{'action':'formSetting','form':$editingForm},JSON.stringify({
                'name':$('setting_name').value,
                'description':$('setting_description').value,
                'start_time':$('setting_start_time').value,
                'end_time':$('setting_end_time').value,
                'submit_per_ip':$('setting_submit_per_ip').value,
            }),function(rep){
                var result = JSON.parse(rep);
                if(result.IsSucceed){
                    alert('设置成功~');
                }else{
                    alert('未知错误~');
                }
            });
        };
        $('bt_setting_cancel').onclick = function(){
            history.back();
        };
        $('bt_form_empty').onclick = function(){
            ajax('post','action.php',{'action':'formEmpty','form':$editingForm},'',function(rep){
                var result = JSON.parse(rep);
                if(result.IsSucceed){
                    //设置成功~
                    alert('数据表已清空~');
                }else{
                    alert('未知错误~');
                }
            });
        };
        $('bt_form_del').onclick = function(){
            ajax('post','action.php',{'action':'formDel','form':$editingForm},'',function(rep){
                var result = JSON.parse(rep);
                if(result.IsSucceed){
                    //设置成功~
                    alert('数据表已删除~');
                    location.href='admin.php?action=list';
                }else{
                    alert('未知错误~');
                }
            });
        };
    }
}

//提示框
var hit_message_t;
function hit_message(text){
    $('hitMessageBox').getElementsByTagName('span')[0].innerHTML = text;
    css($('hitMessageBox'),{
        'display':'block'
    });
    if(!hit_message_t)
        hit_message_t = setTimeout(function(){
            css($('hitMessageBox'),{
                'display':'none'
            });
            hit_message_t = null;
        },1500);
}

//预览页面
$('preview').onclick = function(){
    window.open('submit.php?preview=code&form='+$editingForm);
};

if($isShowResult){
    if($result != null){
        var len = $result.length;
        var ul;
        for(var i = 1 ; i < len ; i++){
            ul = strToDom('\
            <ul class="result_list">\
                <li>\
                    <span>'+$result[i][0]+'</span>\
                    <div>\
                    </div>\
                </li>\
            </ul>\
            ');
            //生成条目
            var data = '';
            for(var i0 = 1;i0 < $result[i].length; i0++){
                data += '<p>'+$result[0][i0]+'：'+$result[i][i0]+'</p>';
            }
            ul.getElementsByTagName('div')[0].innerHTML = data;
            $$('contentleft')[0].appendChild(ul);
        }
    }
}