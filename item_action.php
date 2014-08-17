<?php
header("Content-type: application/json; charset=utf-8"); 
require_once("mysql.class.php");
$action = $_REQUEST["action"];
$db = new Mysql();
$db->open();
$ret = array();
if($action=='add'){
	if(isset($_REQUEST['desc'])){
		$description = "'".$_REQUEST['desc']."'";
	}else{
		$description = 'NULL';
	}
	$fund = $_REQUEST['fund']; 
	$time = "'".$_REQUEST['time']."'";

	$items = $_REQUEST['items'];
	$spend = 0;
	foreach ($items as $item) {
		$spend +=$item['spend'];
	}

	$db->query("insert into orders(description,fund_id,spend,time,create_time)values(".$description.",".$fund.",".$spend.",".$time.",now())");
	$last_id=mysql_insert_id();

	$db->query('insert into items(type,order_id,description,spend,create_time,time)values()')
}

echo json_encode($ret);
?>