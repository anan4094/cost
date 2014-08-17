<?php
header("Content-type: application/json; charset=utf-8");
require_once("db/db.php");
$action = $_REQUEST["action"];

$ret = array();
if($action=='add'){
	if(isset($_REQUEST['desc'])){
		$description = $_REQUEST['desc'];
	}else{
		$description = null;
	}
	$fund = $_REQUEST['fund'];
	$time = $_REQUEST['time'];

	$items = $_REQUEST['items'];
	$ret['items']=$items;
	$spend = 0;
	foreach ($items as $item) {
		$spend +=$item['spend'];
	}

	getDB()->Execute("insert into orders(description,fund_id,spend,time,create_time)values(?,?,?,?,now())"
		,array($description,$fund,$spend,$time));
	$last_id=getDB()->Insert_ID();
	$ret['last_id']=$last_id;

	foreach ($items as $item){
		getDB()->Execute('insert into items(type_id,order_id,description,spend,create_time,time)values(?,?,?,?,now(),?)'
			,array($item['type'],$last_id,$item['desc'],$item['spend'],$time));
	}
	$order = getDB()->GetRow('select * from orders where id = ?',$last_id);

	getDB()->Execute('update fund set balance=balance-? where id =?',array($spend,$fund));

	$ret['order']=$order;
}

echo json_encode($ret);
?>