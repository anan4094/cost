<?php
require_once("db/db.php");
date_default_timezone_set('PRC');
?>
<!DOCTYPE HTML><head>
	<meta charset="UTF-8">
    <title>消费条目</title>
</head>
<html>

<link href="lib/js/chosen/chosen.css" type="text/css" rel="stylesheet" />
<link href="base.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="lib/js/jquery-1.5.2.min.js"></script>
<script type="text/javascript" src="lib/js/chosen/chosen.jquery.js"></script>
<script type="text/javascript" src="base.js"></script>
<script type="text/javascript">
$(function(){
    function order_val(label){
        var value = $('.order_form .order_'+label).find('input,select').val();
        if(value===undefined)return undefined;
        value = value.replace(/^\s*/gi,'').replace(/\s*$/gi,'');
        return value;
    }
    function item_val(ind,label){
        var inp=$('.order_form .item_container:eq('+ind+') .item_'+label).find('input,select');
        var value = inp.is('select')?inp.data('value'):inp.val();
        if(value===undefined)return undefined;
        if (typeof value=='number') {return value;};
        value = value.replace(/^\s*/gi,'').replace(/\s*$/gi,'');
        return value;
    }
    function del_item(){
        var _this = $(this);
        window._this=_this;
        var item = _this.parents('.item_container');
        if(_this.parents('.items_container').find('.item_container').not('.hidden').length==1){
            alert('至少保留一条条目');
            return;
        };
        item.addClass('hidden').slideUp(400,function(){
            item.detach();
        });
    }
    function init_item(item){
        item.find('.type_content').typeSelect();
        item.find('.item_del').click(del_item);
    }
    $('.item_container').each(function(){
        init_item($(this));
    })
	$('#add_item').click(function(){
        var items_container = $('.order_form .items_container');
        var item = $('<div class="item_container">'+
            ' <div class="item_desc small"><input placeholder="物品具体名称"/></div>'+
            ' <div class="item_spend"><input placeholder="金额"/></div>'+
            ' <div class="item_type"><div class="type_content"></div></div>'+
            ' <div class="right">'+
            ' <button class="btn item_del">删除</button>'+
            ' </div>'+
            '</div>');
        items_container.append(item);
        init_item(item);

    });
    $('#add_order').click(function(){
        var data={};
        data.desc=order_val('desc');
        if(!data.desc){
            alert('请填写消费说明');
            return;
        }
        data.fund=order_val('fund');
        if(!data.fund){
            alert('请选择支付方式');
            return;
        }
        data.time=order_val('time');
        if(!data.time){
            alert('请填写消费时间');
            return;
        }else if(!/^\d{4}-\d{2}-\d{2}$/.test(data.time)){
            alert('消费时间格式不正确');
            return;
        }
        data.items=[];
        var item_count=$('.order_form .item_container').length;
        for(var i=0;i<item_count;i++){
            var item_data={};
            item_data.desc = item_val(i,'desc');
            if(!item_data.desc){
                alert('请填写第'+(i+1)+'条目的描述');
                return;
            }
            item_data.spend = item_val(i,'spend');
            if(!item_data.spend){
                alert('请填写第'+(i+1)+'条目的金额');
                return;
            }
            if (!/^\d+(\.\d{0,2})?$/.test(item_data.spend)) {
                alert('请正确地填写第'+(i+1)+'条目的金额');
                return;
            };
            item_data.type = item_val(i,'type');
            if(!item_data.type){
                alert('请选择第'+(i+1)+'条目的类别');
                return;
            }
            data.items.push(item_data);
        }
        data.action='add';
        $.ajax({
            url:'item_action.php'
            ,data:data
            ,success:function(e){
                alert(JSON.stringify(e));
            }
        })
    });
})
</script>
<style type="text/css">
    a.page{
        margin: 0 20px 15px 0;
    }
</style>
<body>

<div class="orders_container">
    <div class="order_container order_form">
        <div class="order_content">
            <div class="order_desc small">
                <input placeholder='消费说明(地点、事件、物品等)'/>
            </div>
            <div class="order_fund">
                <select class='chosen'>
                <?php
                $result = getDB()->GetAll("select * from fund");
                foreach($result as $row) {
                    $name = $row['fund_name'];
                    $id= $row['id'];
                    echo '<option value="'.$id.'">'.$name.'</option>';
                }
                ?>
                </select>
            </div>
            <div class="order_time">
                <input value='<?php echo date('Y-m-d');?>'/>
            </div>
            <div class="right">
                <button class='btn' id='add_item'>添加</button>
                <button class='btn' id='add_order'>提交</button>
            </div>
        </div>
        <div class="items_container">
            <div class="item_container">
                <div class="item_desc small"><input placeholder='物品具体名称'/></div>
                <div class="item_spend"><input placeholder='金额'/></div>
                <div class="item_type"><div class="type_content"></div></div>
                <div class="right">
                    <button class='btn item_del'>删除</button>
                </div>
             </div>
        </div>
    </div>

    <?php
    if(isset($_GET['t'])){
        $last_time = $_GET['t'];
    }else{
        $row = getDB()->GetRow("select DATE_FORMAT(time,'%Y-%m-%d') as ftime from orders order by time desc");
        if($row!==false){
            $last_time = $row['ftime'];
            error_log($last_time);
        }else{
            $last_time=date('Y-m-d');
        }
    }
    //$last_time=date('Y-m').'-01';
    $row = getDB()->GetRow("select DATE_FORMAT(time,'%Y-%m-%d') as ftime from orders where time < ? order by time desc",$last_time);
    error_log(json_encode($row));
    if($row!==false){
        $prev_time = $row['ftime'];
    }
    $row = getDB()->GetRow("select DATE_FORMAT(time,'%Y-%m-%d') as ftime from orders where time > ? order by time asc",$last_time);
    if($row!==false){
        $next_time = $row['ftime'];
    }

    if(isset($prev_time)){
        echo '<a class="page" href="?t='.$prev_time.'">'.$prev_time.'</a>';
    }
    if(isset($next_time)){
        echo '<a class="page" href="?t='.$next_time.'">'.$next_time.'</a>';
    }

    $result = getDB()->GetAll("select aa.*,DATE_FORMAT(aa.time,'%Y-%m-%d') as time_format,bb.description as order_desc,bb.spend as order_spend,cc.type_name,dd.fund_name from items aa left join orders bb on aa.order_id = bb.id left join type cc on aa.type_id = cc.id left join fund dd on bb.fund_id = dd.id"
        ." where aa.time = ? "
        ." order by aa.time desc,order_id desc",$last_time);
    $order_id = 0;
    foreach ($result as $row) {
    	$cur_order_id=$row['order_id'];
    	if($cur_order_id!=$order_id){
    		if($order_id!=0){
    			echo '</div></div>';
    		}
    		$order_id=$cur_order_id;
    		echo '<div class="order_container">';
        	echo '<div class="order_content">';
            echo '<div class="order_desc">'.$row['order_desc'].'</div>';
            echo '<div class="order_spend">'.$row['order_spend'].'</div>';
            echo '<div class="order_fund">'.$row['fund_name'].'</div>';
            echo '<div class="order_time">'.$row['time_format'].'</div>';
            echo '</div>';

    		echo '<div class="items_container">';
            echo '<div class="item_container">';
            echo '<div class="item_desc">['.$row['type_name'].']'.$row['description'].'</div>';
            echo '<div class="item_spend">'.$row['spend'].'</div>';
            echo '</div>';
    	}else{
    		echo '<div class="item_container">';
            echo '<div class="item_desc">['.$row['type_name'].']'.$row['description'].'</div>';
            echo '<div class="item_spend">'.$row['spend'].'</div>';
            echo '</div>';
    	}
    }
    ?>
    </div>

</body>
</html>