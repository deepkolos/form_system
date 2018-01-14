function focusConatiner(){
    var isfocus = false
    function focus(e){
        if(!isfocus){
            isfocus = true
            if( !this.getAttribute("contenteditable") || !isfocus){
            if(this.onfocus) this.onfocus()
            !function(_this){
                function blur(){
                    isfocus = false
                    if(_this.onblur) _this.onblur()
                    window.removeEventListener("click",blur)
                }
                e.stopPropagation()
                window.addEventListener("click",blur)
                // setTimeout(function(){
                //     window.addEventListener("click",blur)
                // },20)
                //还是上面那种方便一些
            }(this)
            }
        }else
            e.stopPropagation();
    } 
    return focus;
}
//usage
/*
writer.addEventListener("click",new focusConatiner)
writer.onfocus = function(){
    
}
writer.onblur = function(){
    
    
}
*/