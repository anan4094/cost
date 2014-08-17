$._select = {
	init:function(el,op){
		var sel=$("<select/>");
		sel.append("<option value=''>[\u65E0]</option>");
		var container=$("<div/>").addClass('selected');
		el = $(el);
		el.append(sel);
		el.append(container);
		sel.css({width:140}).chosen({search_contains:true,allow_single_deselect:false});
		var curID=[];
		function A(){
			sel.change(function(){
				var value = +sel.val();
				if (value) {
					curID[curID.length]=value;
					var a = $('<a/>',{'href':'javascript:void(0);'});
					var text=$("<span/>").addClass("text").html(sel.find("option:selected").text());
					var remove=$("<span/>").addClass("remove");
					container.append(a.append(text).append(remove));
					remove.click(D);
					C();
				};
			})
		}
		function B(){
			if (!$._select.data) {
				$.ajax({
					url:'type.php'
					,type:'get'
					,data:{}
					,success:function(data){
						$._select.data=data.data;
						C();
					}
				});
			}else{
				C();
			}

		}
		function C(){
			var data=$._select.data;
			for(var i=0;i<curID.length;i++){
				var isFound=false;
				for(var j=0;j<data.length;j++){
					if (data[j].id==curID[i]) {
						data=data[j].values;
						isFound=true;
						break;
					};
				}
				if (!isFound) {
					data=[];
				};
			}
			if (!data) {data=[];};
			sel.html('');
			var opt=$('<option/>').html('[\u65E0]').attr({value:'0'});
			if(curID.length>0){
				sel.data('value',curID[curID.length-1]);
			}else{
				sel.data('value',0);
			}

			sel.append(opt);
			for(var i=0;i<data.length;i++){
				sel.append('<option value="'+data[i].id+'">'+data[i].name+'</option>')
			}
			sel.trigger("liszt:updated");
		}
		function D(){
			var remove=$(this);
			var a = remove.parent();
			var ind = a.index();
			if(ind==0){
				a.parent().find('a').detach();
			}else{
				a.parent().find('a:gt('+(ind-1)+')').detach();
			}
			while(curID.length>ind){
				curID.pop();
			}
			C();
		}
		A();
		B();
	}
}
$.fn.typeSelect = function(options){
	return this.each(function(){
    	(new $._select.init(this, options));
	});
};

$(function(){
	$('.chosen').css({'width':200}).chosen();
	$('.type_content').typeSelect();
});



window._alert = window.alert;
window.alert=function(msg){
    var box = $('.alert_container');
    if(box.length==0){
        box = $('<div class="alert_container"></div>');
        $('body').append(box);
    }
    if(window.alert_fade_id){
        clearTimeout(window.alert_fade_id);
        window.alert_fade_id=0;
    }
    box.html(msg);
    box.stop().css({'opacity':'1'});
    box.show();
    box.css({'left':Math.ceil(($(window).width()-box.width())/2)+'px'});
    window.alert_fade_id =  setTimeout(function(){
        box.fadeOut(3000);
        window.alert_fade_id=0;
    },3000);
}



