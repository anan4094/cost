<!DOCTYPE HTML><head>
	<meta charset="UTF-8">
    <title>类别管理</title>
</head>
<html>

<link href="base.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="lib/js/jquery-1.5.2.min.js"></script>
<script type="text/javascript">
$(function(){
	$.ajax({
		url:'type.php'
		,type:'get'
		,data:{}
		,success:function(data){
			data={values:data.data};
			var curData=[];
			var curContainer=[];
			curData.push(data);
			curData.push(0);
			curContainer.push($('.type_container'));
			while(curData.length){
				var nowData=curData[curData.length-2];
				var nowInd=curData[curData.length-1];
				var cssID="type_"+(nowInd%2==0?"even":"odd")+"_"+(((curData.length-2)/2)%4);
				var cssContainerID="type_"+(nowInd%2==0?"even":"odd")+"_container_"+(((curData.length-2)/2)%4);
				var item=$("<div class='type_item "+cssID+"'></div>");
				item.data('depth',(curData.length-2)/2);
				item.data('item',nowData.values[nowInd]);
				var mod=$('<span class="icon pencil"></span>');
				var del=$('<span class="icon remove"></span>');
				var add=$('<span class="icon plus"></span>');
				var btns=$('<div class="btns"></div>');
				btns.append(mod).append(del).append(add);
				add.click(additem);
				del.click(delitem);
				mod.click(moditem);
				var itext=$("<div class='type_item_text'>"+nowData.values[nowInd].name+"</div>");
				itext.append(btns)
				item.empty().append(itext);
				curContainer[curContainer.length-1].append(item);
				if(nowData.values[nowInd].values){
					curData.push(nowData.values[nowInd]);
					curData.push(0);
					var newContainer=$("<div class='type_container "+cssContainerID+"'></div>");
					item.append(newContainer);
					curContainer.push(newContainer);
				}else{
					nowInd++;
					if(nowInd<nowData.values.length){
						curData[curData.length-1]=nowInd;
					}else{
						do{
							curData.pop();
							curData.pop();
							curContainer.pop();
							if(curData.length==0)break;
							curData[curData.length-1]=curData[curData.length-1]+1;
						}while(curData[curData.length-2].values.length <= curData[curData.length-1])
					}
				}
			}
		}
	});
	function additem(){
		var item = $($(this).parent().parent().parent()[0]);
		var depth=item.data('depth');
		var pid=item.data('item').id;
		var container = item.find('.type_container:eq(0)');
		if(!container.length){
			container=$('<div class="type_container type_'+(num%2==0?"even":"odd")+"_container_"+(depth%4)+'"></div>');
			item.append(container);
		}
		var num = container.find('.type_item').length;
		var newItem=$("<div class='type_item  type_"+(num%2==0?"even":"odd")+"_"+((parseInt(depth,10)+1)%4)+"'></div>");
		var name=$("<input class='input-tip' placeholder='请在这里输入类别名'/>");
		var btn=$("<input type='button' value='保存' class='input-btn'/>");
		var cancelBtn=$("<input type='button' value='取消' class='input-btn'/>");

		btn.click(function(){
			var nameEdit=$($(this).parent('.type_item')[0]).find('.input-tip');
			var nameText=nameEdit.val();
			if(/^\s*$/gi.test(nameText)){
				nameEdit.attr('placeholder','类别名不能为空').val('');
				return;
			}
			var data={pid:pid,name:nameText.replace(/(^\s*)|(\s*$)/g, "")};
			$.ajax({
				url:'type_action.php',
				data:data,
				type:'get',
				success:function(e){
					if(!e.code){
						var item = $(btn.parent('.type_item')[0]);
						item.data('depth',e.data.depth);
						item.data('item',e.data);
						var mod=$('<span class="icon pencil"></span>');
						var del=$('<span class="icon remove"></span>');
						var add=$('<span class="icon plus"></span>');
						var btns=$('<div class="btns"></div>');
						btns.append(mod).append(del).append(add);
						add.click(additem);
						del.click(delitem);
						mod.click(moditem);
						var itext=$("<div class='type_item_text'>"+e.data.name+"</div>");
						itext.append(btns);
						item.empty().append(itext);
					}
				}
			});

		});
		cancelBtn.click(function(){
			var item = $(this).parent();
			item.detach();

		});
		newItem.append(name);
		newItem.append(btn);
		newItem.append(cancelBtn);
		container.append(newItem);
		name.focus();
	}

	function moditem(){
		var item = $($(this).parent().parent().parent()[0]);
		var data=item.data('item');
		var itemText=item.find('.type_item_text:eq(0),.btns:eq(0)').detach();
		var name=$("<input class='input-tip' placeholder='请在这里输入类别名'/>");
		name.val(data.name);
		name.data('id',data.id);
		var btn=$("<input type='button' value='保存' class='input-btn'/>");
		var cancelBtn=$("<input type='button' value='取消' class='input-btn'/>");

		btn.click(function(){
			var nameEdit=$($(this).parent('.type_item')[0]).find('.input-tip');
			var nameText=nameEdit.val();
			if(/^\s*$/gi.test(nameText)){
				nameEdit.attr('placeholder','类别名不能为空').val('');
				return;
			}
			var data={id:nameEdit.data('id'),name:nameText.replace(/(^\s*)|(\s*$)/g, "")};
			$.ajax({
				url:'type_action.php',
				data:data,
				type:'get',
				success:function(e){
					if(!e.code){
						var item = $(btn.parent('.type_item')[0]);
						item.data('depth',e.data.depth);
						item.data('item',e.data);
						var mod=$('<span class="icon pencil"></span>');
						var del=$('<span class="icon remove"></span>');
						var add=$('<span class="icon plus"></span>');
						var btns=$('<div class="btns"></div>');
						btns.append(mod).append(del).append(add);
						add.click(additem);
						del.click(delitem);
						mod.click(moditem);
						item.find('.input-tip,.input-btn').detach();
						var itext=$("<div class='type_item_text'>"+e.data.name+"</div>");
						itext.append(btns);
						item.prepend(itext);

					}
				}
			});

		});
		cancelBtn.click(function(){
			var item = $(this).parent();
			var data=item.data('item');
			var mod=$('<span class="icon pencil"></span>');
			var del=$('<span class="icon remove"></span>');
			var add=$('<span class="icon plus"></span>');
			var btns=$('<div class="btns"></div>');
			btns.append(mod).append(del).append(add);
			add.click(additem);
			del.click(delitem);
			mod.click(moditem);
			item.find('.input-tip,.input-btn').detach();
			item.prepend(btns);
			var itext=$("<div class='type_item_text'>"+data.name+"</div>");
			itext.append(btns);
			item.prepend(itext);

		});
		item.prepend(cancelBtn);
		item.prepend(btn);
		item.prepend(name);

	}

	function delitem(){
		var item = $($(this).parent().parent().parent()[0]);
		var data = item.data('item');

		if(window.confirm('你确定要删除类别[ '+data.name+' ]吗？')){
			$.ajax({
				url:'type_action.php',
				data:{id:data.id},
				type:'get',
				success:function(e){
					item.detach();
				}
			});
		}
	}
})
</script>
<body>
<div class="type_container" style="width:480px;margin:2px auto">
</div>
</body>
</html>