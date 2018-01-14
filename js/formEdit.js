
if($isEditing){

    var container = $$('contentleft')[0];

    //添加按钮可见,乎~~这真混乱
    css($$('bottomBar')[0],{
        'display':'inline-block'
    });

    $('add_item').onclick = new function(){
        var state = 1;
        var li = $$('bottomBar')[0].getElementsByTagName('li');
        return function(){
            if(state){
                //展开
                css($('add_item'),{
                    'border-top-left-radius': '0px',
                    'border-top-right-radius': '0px'
                });
                css($('add_item').getElementsByTagName('div')[0],{
                    'transform':'rotate(45deg)'
                });
                
                for(var i = 0; i < li.length; i++){
                    css(li[i],{
                        'transform':'translateY('+'-'+60*(li.length-i)+'px)',
                        'border-radius':'0px',
                        'opacity': 1
                    });
                }
                state = !state;
            }else{
                //关闭
                css($('add_item'),{
                    'border-top-left-radius': '30px',
                    'border-top-right-radius': '30px'
                });
                css($('add_item').getElementsByTagName('div')[0],{
                    'transform':'rotate(0deg)'
                });
                for(var i = 0; i < li.length; i++){
                    css(li[i],{
                        'transform':'translateY(0px)',
                        'border-radius':'30px',
                        'opacity': 0
                    });
                }
                state = !state;
            }
        }
    };

    $('add_description_item').onclick = function(){
        //添加基本的Dom结构到末尾,先要考虑顺序如何改变,并且是下次编辑的时候也是可以用的
        //可以通过DOM的自带func实现,所以加入的时候可以粗暴一点,不过要重新刷新一遍事件的绑定func,
        //会显得多余,同时也可能重复了
        //通过appendChild,可以添加的时候绑定事件,并且原有的是不会影响的,不过appendChild的缺点就是构造DOM节点麻烦
        var str = '\
        <div class="form_description form_block">\
            <div class="center">\
                <div class="bt_setinput_text">文字</div>\
                <span>/</span>\
                <div class="bt_setinput_img">图片</div>\
            </div>\
            <div class="control">...\
                <div class="bt_ctrl">\
                    <div class="bt_moveUp">^</div>\
                    <div class="bt_moveDown">^</div>\
                    <div class="bt_del_item">x</div>\
                </div>\
            </div>\
        </div>\
        ';
        var dom = strToDom(str);
        bindControlEvent(dom.getElementsByClassName('control')[0]);
        bind_bt_setinput_text(dom.getElementsByClassName('bt_setinput_text')[0]);
        bind_bt_setinput_img(dom.getElementsByClassName('bt_setinput_img')[0]);
        for(var i = 0,len = dom.getElementsByClassName('input_text_title').length ; i <len ;i++){
            bind_text_title_event(dom.getElementsByClassName('input_text_title')[i]);
        }
        container.appendChild(dom);
    };

    $('add_singlechoose_item').onclick = function(){
        var str = '\
        <div class="form_singlechoose_input form_block">\
            <span class="option" option="">*</span><div class="input_text_title" contenteditable="true">说明文字</div>\
            <div class="input_container">\
                <div><input type="radio" name="" value="0"><span class="bt_del">-</span><div\ class="input_text_title" contenteditable="true">说明文字</div></div>\
                <div><span class="bt_add">+</span><span class="bt_del">-</span><input type="radio" name="" value="1"><div\ class="input_text_title" contenteditable="true">说明文字</div></div>\
            </div>\
            <div class="control">...\
                <div class="bt_ctrl">\
                    <div class="bt_moveUp">^</div>\
                    <div class="bt_moveDown">^</div>\
                    <div class="bt_del_item">x</div>\
                </div>\
            </div>\
        </div>\
        ';
        var dom = strToDom(str);
        var bt_del = dom.getElementsByClassName('bt_del');
        var bt_add = dom.getElementsByClassName('bt_add');
        //添加setting事件
        bindControlEvent(dom.getElementsByClassName('control')[0]);
        for(var i = 0; i < bt_del.length;i++){
            bind_bt_del_event(bt_del[i]);
        }
        for(i = 0; i < bt_add.length;i++){
            bind_bt_add_event(bt_add[i]);
        }
        for(var i = 0,len = dom.getElementsByClassName('input_text_title').length ; i <len ;i++){
            bind_text_title_event(dom.getElementsByClassName('input_text_title')[i]);
        }
        //添加column name
        var len = $$('form_singlechoose_input').length;
        for(i = 0 ,len0 = dom.getElementsByTagName('input').length;i<len0;i++){
            dom.getElementsByTagName('input')[i].setAttribute('name','singlechoose'+len);
            dom.getElementsByTagName('input')[i].setAttribute('value',i);
        }
        //到时候更新位置的时候需要更新?上移,下移都不需要,仅仅是删除需要全部更新一遍
        container.appendChild(dom);
    };

    $('add_multiChoice_item').onclick = function(){
        var str = '\
        <div class="form_multichoose_input form_block">\
            <span class="option" option="">*</span><div class="input_text_title" contenteditable="true">说明文字</div>\
            <div class="input_container">\
                <div><span class="bt_del">-</span><input type="checkbox" name="test" value="test"><div\ class="input_text_title" contenteditable="true">说明文字</div></div>\
                <div><span class="bt_del">-</span><span class="bt_add">+</span><input type="checkbox"\ name="test" value="test2"><div class="input_text_title" contenteditable="true">说明文字</div></div>\
            </div>\
            <div class="control">...\
                <div class="bt_ctrl">\
                    <div class="bt_moveUp">^</div>\
                    <div class="bt_moveDown">^</div>\
                    <div class="bt_del_item">x</div>\
                </div>\
            </div>\
        </div>\
        ';
        var dom = strToDom(str);
        var bt_del = dom.getElementsByClassName('bt_del');
        var bt_add = dom.getElementsByClassName('bt_add');
        //事件绑定
        bindControlEvent(dom.getElementsByClassName('control')[0]);
        for(var i = 0; i < bt_del.length;i++){
            bind_bt_del_event(bt_del[i]);
        }
        for(i = 0; i < bt_add.length;i++){
            bind_bt_add_event(bt_add[i]);
        }
        for(var i = 0,len = dom.getElementsByClassName('input_text_title').length ; i <len ;i++){
            bind_text_title_event(dom.getElementsByClassName('input_text_title')[i]);
        }
        //添加column name
        var len = $$('form_multichoose_input').length;
        for(i = 0 ,len0 = dom.getElementsByTagName('input').length;i<len0;i++){
            dom.getElementsByTagName('input')[i].setAttribute('name','multichoose'+len+'_'+i);
            dom.getElementsByTagName('input')[i].setAttribute('value',i);
        }
        container.appendChild(dom);
    };

    $('add_singleLineInput_item').onclick = function(){
        var str = '\
        <div class="form_singleline_input form_block">\
            <span class="option" option="">*</span><div class="input_text_title" contenteditable="true">说明文字</div>\
            <input type="text" name="" value="">\
            <div class="control">...\
                <div class="bt_ctrl">\
                    <div class="bt_moveUp">^</div>\
                    <div class="bt_moveDown">^</div>\
                    <div class="bt_del_item">x</div>\
                </div>\
            </div>\
        </div>\
        ';
        var dom = strToDom(str);
        bindControlEvent(dom.getElementsByClassName('control')[0]);
        for(var i = 0,len = dom.getElementsByClassName('input_text_title').length ; i <len ;i++){
            bind_text_title_event(dom.getElementsByClassName('input_text_title')[i]);
        }
        //添加column name
        var len = $$('form_singleline_input').length;
        dom.getElementsByTagName('input')[0].setAttribute('name','singleline'+len);
        container.appendChild(dom);
    };

    $('add_multiLineInput_item').onclick = function(){
        var str = '\
        <div class="form_multiline_input form_block">\
            <span class="option" option="">*</span><div class="input_text_title" contenteditable="true">说明文字</div>\
            <textarea name="" rows="5" cols="25"></textarea>\
            <div class="control">...\
                <div class="bt_ctrl">\
                    <div class="bt_moveUp">^</div>\
                    <div class="bt_moveDown">^</div>\
                    <div class="bt_del_item">x</div>\
                </div>\
            </div>\
        </div>\
        ';
        var dom = strToDom(str);
        bindControlEvent(dom.getElementsByClassName('control')[0]);
        for(var i = 0,len = dom.getElementsByClassName('input_text_title').length ; i <len ;i++){
            bind_text_title_event(dom.getElementsByClassName('input_text_title')[i]);
        }
        //添加column name
        var len = $$('form_multiline_input').length;
        dom.getElementsByTagName('textarea')[0].setAttribute('name','multiline'+len);
        container.appendChild(dom);
    };

    //果然找到到东西,HtmlStrToDom,利用innerHTML交给浏览器做转换
    //不过下一个问题是多行字符串的支持,不想通过\转义,原来ES6使用`分隔符来支持多行,可惜,我的键盘按不出这个按钮,略微悲哀
    //手动添加,column name 主要是在add的时候添加
    //生成用于数据表的column name,感觉表的建立可以放在release阶段
    //妈蛋,发现好多重复的代码怎么办??
    function update_column_name_all(){
        var list = [
            'form_singlechoose_input',
            'form_multichoose_input',
            'form_singleline_input',
            'form_multiline_input'
        ];
        var column_name = [
            'singlechoose',
            'multichoose',//又有一个特列了,value更是要要再一次弄
            'singleline',
            'multiline'//特例~
        ];
        for(var i0 = 0 ; i0 < list.length; i0++){
            for(var i1 = 0; i1 < $$(list[i0]).length ;i1++){
                var input = $$(list[i0])[i1].getElementsByTagName('input');
                input = (input.length == 0)?$$(list[i0])[i1].getElementsByTagName('textarea'):input;
                if(list[i0] == 'form_multichoose_input'){
                    for(var i2 = 0 ; i2 < input.length;i2++){
                        input[i2].setAttribute('name',column_name[i0]+i1+'_'+i2);
                    }
                }else
                    for(var i2 = 0 ; i2 < input.length;i2++){
                        input[i2].setAttribute('name',column_name[i0]+i1);
                    }
            }
        }
        //要上传一个column name的对象数组,结构大概是,
        // var struct = [
        //     {'name':column_name0,'type':3,'description':'test'},
        //     {'name':column_name1,'type':2,'description':'test2'}
        // ];
    }

    !function(){
        for(var i = 0,len = $$('input_text_title').length ; i <len ;i++){
            css($$('input_text_title')[i],{
                'background':'none'
            });
            bind_text_title_event($$('input_text_title')[i]);
        }
    }();

    function bind_text_title_event(descrition){
        css(descrition,{
            'color':'black'
        });
        descrition.setAttribute('contenteditable',true);
        descrition.addEventListener('click',new focusConatiner);
        descrition.onfocus = new function(){
            var _this = descrition;
            return function(){
                css(_this,{
                    'background':'lightgoldenrodyellow'
                });
            };
        };
        descrition.onblur = new function(){
            var _this = descrition;
            return function(){
                css(_this,{
                    'background':'none'
                });
            };
        };
    };

    //为了可以重复使用p这个变量名,每一个for都添加一个匿名闭包,感觉好那个

    // !function(){
    //     for(var i = 0,len = $$('form_singleline_input').length ; i <len ;i++){
            
    //     }
    // }();
    !function(){
        for(var i = 0,len = $$('form_description').length ; i < len ;i++){
            if($$('form_description')[i].getElementsByClassName('bt_setinput_text')[0])
                bind_bt_setinput_text($$('form_description')[i].getElementsByClassName('bt_setinput_text')[0]);
            if($$('form_description')[i].getElementsByClassName('bt_setinput_img')[0])
                bind_bt_setinput_img($$('form_description')[i].getElementsByClassName('bt_setinput_img')[0]);
        }
    }();
    $$('bottomBar')[0].getElementsByTagName('ul')[0].onclick = function(){
        $('add_item').onclick();
    }

    function bind_bt_setinput_text(dom){
        dom.onclick = function(){
            var parent = dom.parentNode;
            parent.innerHTML = '';
            var text_input = strToDom('\
            <div style="text-algin:center;" contenteditable="true"></div>\
            ');
            parent.appendChild(text_input);
        }
    }
    function bind_bt_setinput_img(dom){
        dom.onclick = function(){
            var parent = dom.parentNode;
            parent.innerHTML = '';
            var img_input = strToDom('\
            <input type="file" value="img" accept="image/*">\
            ');
            img_input.addEventListener('change',function(){
                var reader = new FileReader();
                var file = img_input.files[0];
                reader.onloadend = function(){
                    var img = document.createElement('img');
                    img.setAttribute('src',reader.result);
                    parent.innerHTML = '';
                    parent.appendChild(img);
                }
                reader.readAsDataURL(file);
            });
            parent.appendChild(img_input);
        }
    }
    !function(){
        for(var i = 0,len = $$('control').length ; i <len ;i++){
            bindControlEvent($$('control')[i]);
        }
    }();

    function bindControlEvent(ctrl){
        //ok之前写的onfocus,onblur支持有用了
        var bt_ctrl = ctrl.getElementsByClassName('bt_ctrl')[0];
        //初始化样式
        css(bt_ctrl,{
            'display':'none'
        });
        ctrl.addEventListener('click',new focusConatiner);
        ctrl.onfocus = function(){
            css(bt_ctrl,{
                'display':'block'
            });
        };
        ctrl.onblur = function(){
            css(bt_ctrl,{
                'display':'none'
            });
        };
        var bt_moveUp = bt_ctrl.getElementsByClassName('bt_moveUp')[0];
        var bt_moveDown = bt_ctrl.getElementsByClassName('bt_moveDown')[0];
        var bt_del_item = bt_ctrl.getElementsByClassName('bt_del_item')[0];
        var form_block = ctrl.parentNode;
        //上移
        bt_moveUp.onclick = function(){
            //找到这个form_block在form_block里面的序号,通过previousSibling
            //这里主要通过一个replaceChild
            //储存当前的form_block
            var form_block_pre = form_block.previousSibling;
            //解决form_block.previousSibling为#text节点的问题
            if(form_block_pre != null){
                while(form_block_pre != null && form_block_pre.nodeName == '#text'){
                    form_block_pre = form_block_pre.previousSibling;
                }
            }
            if(form_block_pre != null){
                var this_form_block = form_block.cloneNode();
                this_form_block.innerHTML = form_block.innerHTML;
                var prebious_form_block = form_block_pre.cloneNode();
                prebious_form_block.innerHTML = form_block_pre.innerHTML;
                //dom经过移动之后,事件绑定失效了~~所以要重新绑定事件
                bindControlEvent(this_form_block.getElementsByClassName('control')[0]);
                bindControlEvent(prebious_form_block.getElementsByClassName('control')[0]);
                var bt_del = this_form_block.getElementsByClassName('bt_del');
                var bt_add = this_form_block.getElementsByClassName('bt_add');
                for(var i = 0; i < bt_del.length;i++){
                    bind_bt_del_event(bt_del[i]);
                }
                for(i = 0; i < bt_add.length;i++){
                    bind_bt_add_event(bt_add[i]);
                }
                var bt_del_next = prebious_form_block.getElementsByClassName('bt_del');
                var bt_add_next = prebious_form_block.getElementsByClassName('bt_add');
                for(var i = 0; i < bt_del_next.length;i++){
                    bind_bt_del_event(bt_del_next[i]);
                }
                for(i = 0; i < bt_add_next.length;i++){
                    bind_bt_add_event(bt_add_next[i]);
                }
                for(var i = 0,len = this_form_block.getElementsByClassName('input_text_title').length ; i <len ;i++){
                    bind_text_title_event(this_form_block.getElementsByClassName('input_text_title')[i]);
                }
                for(var i = 0,len = prebious_form_block.getElementsByClassName('input_text_title').length ; i <len ;i++){
                    bind_text_title_event(prebious_form_block.getElementsByClassName('input_text_title')[i]);
                }
                form_block.parentNode.replaceChild(this_form_block,form_block_pre);
                form_block.parentNode.replaceChild(prebious_form_block,form_block);
            }
        };
        //下移
        bt_moveDown.onclick = function(){
            //找到这个form_block在form_block里面的序号,通过previousSibling
            //这里主要通过一个replaceChild
            //储存当前的form_block
            var form_block_next = form_block.nextSibling;
            //解决form_block.previousSibling为#text节点的问题
            if(form_block_next !== null){
                while(form_block_next != null && form_block_next.nodeName == '#text'){
                    form_block_next = form_block_next.nextSibling;
                }
            }
            if(form_block_next !== null){
                var this_form_block = form_block.cloneNode();
                this_form_block.innerHTML = form_block.innerHTML;
                var next_form_block = form_block_next.cloneNode();
                next_form_block.innerHTML = form_block_next.innerHTML;
                //dom经过移动之后,事件绑定失效了~~所以要重新绑定事件
                bindControlEvent(this_form_block.getElementsByClassName('control')[0]);
                bindControlEvent(next_form_block.getElementsByClassName('control')[0]);
                var bt_del = this_form_block.getElementsByClassName('bt_del');
                var bt_add = this_form_block.getElementsByClassName('bt_add');
                for(var i = 0; i < bt_del.length;i++){
                    bind_bt_del_event(bt_del[i]);
                }
                for(i = 0; i < bt_add.length;i++){
                    bind_bt_add_event(bt_add[i]);
                }
                var bt_del_next = next_form_block.getElementsByClassName('bt_del');
                var bt_add_next = next_form_block.getElementsByClassName('bt_add');
                for(var i = 0; i < bt_del_next.length;i++){
                    bind_bt_del_event(bt_del_next[i]);
                }
                for(i = 0; i < bt_add_next.length;i++){
                    bind_bt_add_event(bt_add_next[i]);
                }
                for(var i = 0,len = this_form_block.getElementsByClassName('input_text_title').length ; i <len ;i++){
                    bind_text_title_event(this_form_block.getElementsByClassName('input_text_title')[i]);
                }
                for(var i = 0,len = next_form_block.getElementsByClassName('input_text_title').length ; i <len ;i++){
                    bind_text_title_event(next_form_block.getElementsByClassName('input_text_title')[i]);
                }
                form_block.parentNode.replaceChild(this_form_block,form_block_next);
                form_block.parentNode.replaceChild(next_form_block,form_block);
            }
        };
        //删除
        bt_del_item.onclick = function(){
            form_block.parentNode.removeChild(form_block);
            //还是如何储存column name的数据结构,想在dom里面储存,这样提供对应的func进行操作就可以
            //如果通过维护一个数组或者什么东西,来进行同步的话,好像方便很多哦,不过要保证这个数组是和dom上面的column name
            //一致,并且表单项很多的时候处理速度会比较快,这个时候这个数组就像是一个缓存~~
            update_column_name_all();
        };
    }

    !function(){
        for(var i = 0,len = $$('bt_del').length ; i < len ;i++){
            bind_bt_del_event($$('bt_del')[i]);
        }
    }();

    function bind_bt_del_event(bt_del){
        var container = bt_del.parentNode.parentNode;
        var div = bt_del.parentNode;
        bt_del.onclick = function(){
            //检查是否剩下一个而已
            var input = container.getElementsByTagName('input');
            if(input.length != 1){
                //如果删除的是最后一个
                if(div == container.lastElementChild){
                    container.removeChild(div);
                    //添加按钮
                    var bt_add = strToDom('\
                    <span class="bt_add">+</span>\
                    ');
                    container.lastElementChild.insertBefore(bt_add,container.lastElementChild.getElementsByClassName('bt_del')[0]);
                    //事件绑定
                    bind_bt_add_event(bt_add);
                }else
                    container.removeChild(div);
            }
            input = container.getElementsByTagName('input');
            for(var i = 0; i < input.length ;i++){
                input[i].setAttribute('value',i);
            }
        };
    }

    !function(){
        for(var i = 0,len = $$('bt_add').length ; i < len ;i++){
            bind_bt_add_event($$('bt_add')[i]);
        }
    }();

    function bind_bt_add_event(bt_add){
        var container = bt_add.parentNode.parentNode;
        var div = bt_add.parentNode;
        bt_add.onclick = function(){
            //克隆最后一个,并初始化值
            var new_div = container.lastElementChild.cloneNode();
            new_div.innerHTML = container.lastElementChild.innerHTML;
            new_div.getElementsByClassName('input_text_title')[0].innerHTML = '说明文字';
            container.lastElementChild.removeChild(container.lastElementChild.getElementsByClassName('bt_add')[0]);
            //事件添加
            container.appendChild(new_div);
            bind_bt_add_event(new_div.getElementsByClassName('bt_add')[0]);
            bind_bt_del_event(new_div.getElementsByClassName('bt_del')[0]);
            for(var i = 0,len = new_div.getElementsByClassName('input_text_title').length ; i <len ;i++){
                bind_text_title_event(new_div.getElementsByClassName('input_text_title')[i]);
            }
            var input = container.getElementsByTagName('input');
            for(var i = 0; i < input.length ;i++){
                input[i].setAttribute('value',i);
            }
        };
    }
    
    function createColumnConfig(){
        //打补丁系列~~multichoose的name初始化有问题
        var multichoose_arr = $$('form_multichoose_input');
        var len = multichoose_arr.length;
        for(var j = 0; j < len ; j++){
            for(i = 0 ,len0 = multichoose_arr[j].getElementsByTagName('input').length;i<len0;i++){
                multichoose_arr[j].getElementsByTagName('input')[i].setAttribute('name','multichoose'+j+'_'+i);
                multichoose_arr[j].getElementsByTagName('input')[i].setAttribute('value',i);
            }
        }
        var list = [
            'form_singlechoose_input',
            'form_multichoose_input',
            'form_singleline_input',
            'form_multiline_input'
        ];
        //如果图片是dataURL化的话,那么就不需要单读作处理
        //现在需要单独给4种控件生成column setting
        //真的觉得好凌乱
        var column_config = {
            'form_singlechoose_input':[],
            'form_multichoose_input':[],
            'form_singleline_input':[],
            'form_multiline_input':[]
        };
        for(var i = 0 ;i < $$(list[0]).length;i++){
            var form_block = $$(list[0])[i];
            var value_description = [];
            var input_container = form_block.getElementsByClassName('input_container')[0];
            var item = input_container.getElementsByClassName('input_text_title');
            for(var i0 = 0 ; i0 < item.length;i0++){
                value_description.push(item[i0].innerHTML);
            }
            var form_block_config = {
                'column_setting':form_block.getElementsByTagName('input')[0].getAttribute('name'),
                'description':form_block.getElementsByClassName('input_text_title')[0].innerHTML,
                'value_description':value_description
            };
            column_config[list[0]].push(form_block_config);
        }
        for(var i = 0 ;i < $$(list[1]).length;i++){
            var form_block = $$(list[1])[i];
            var value_description = [];
            var column_setting = [];
            var input_container = form_block.getElementsByClassName('input_container')[0];
            var item_description = input_container.getElementsByClassName('input_text_title');
            var item_input = input_container.getElementsByTagName('input');
            for(var i0 = 0 ; i0 < item_description.length;i0++){
                value_description.push(item_description[i0].innerHTML);
                column_setting.push(item_input[i0].getAttribute('name'));
            }
            var form_block_config = {
                'column_setting':column_setting,
                'description':form_block.getElementsByClassName('input_text_title')[0].innerHTML,
                'column_description':value_description
                //这个感觉到歧义是因为singlechoose是描述value,但是这里是描述column_setting的,语义有问题,既然一开始就
                //打算特殊化处理了就改为column_description,value默认有值就可以了
            };
            column_config[list[1]].push(form_block_config);
        }
        for(var i = 0 ;i < $$(list[2]).length;i++){
            var form_block = $$(list[2])[i];
            var form_block_config = {
                'column_setting':form_block.getElementsByTagName('input')[0].getAttribute('name'),
                'description':form_block.getElementsByClassName('input_text_title')[0].innerHTML,
                //不需要column_description了,等同的
            };
            column_config[list[2]].push(form_block_config);
        }
        for(var i = 0 ;i < $$(list[3]).length;i++){
            var form_block = $$(list[3])[i];
            var form_block_config = {
                'column_setting':form_block.getElementsByTagName('textarea')[0].getAttribute('name'),
                'description':form_block.getElementsByClassName('input_text_title')[0].innerHTML,
                //改为textarea
            };
            column_config[list[3]].push(form_block_config);
        }
        return column_config;
    }

    //定时器保存,每7秒自动保存一次
    var autoSave = setTimeout(saveForm,7000);
    function saveForm(){
        //要把一些样式初始化原来的样子
        //不想多个循环去设置style,想通过js触发一次click事件实现,随便找一个input吧
        // document.getElementsByTagName('input')[0].click();
        //妈蛋js触发的click不会冒泡!
        //放在发布的时候初始化样式吧
        ajax('post','action.php',{'action':'formSave','form':$editingForm},JSON.stringify({
            'columnConfig':createColumnConfig(),
            'styleConfig':$$('contentleft')[0].innerHTML
        }),function(rep){
            var result = JSON.parse(rep);
            if(result.IsSucceed){
                hit_message('自动保存OK~');
            }else{
                hit_message('自动失败~');
            }
            clearTimeout(autoSave);
            autoSave = setTimeout(saveForm,7000);
        });
    }

    $('saveform').onclick = function(){
        saveForm();
    }
}


