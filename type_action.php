<?php
header("Content-type: application/json; charset=utf-8"); 
require_once("mysql.class.php");
$name = $_REQUEST["name"];
$pid = $_REQUEST["pid"];
$id = $_REQUEST["id"];
$db = new Mysql();
$db->open();

if(isset($id)&&isset($name)){
	$result = $db->query("update type set type_name='".$name."' where id = ".$id);
	$result = $db->query("select * from type where id = ".$id);
	$row = mysql_fetch_array($result);
	$db->close();
	echo json_encode(array('code'=>0,'data'=>array('id'=>$row['id'],'pid'=>$row['pid'],'name'=>$row['type_name'],'depth'=>$row['depth'])));
}else if(isset($id)&&!isset($name)){
	$db->query("delete from type where id = ".$id);
	echo json_encode(array('code'=>0));
}else{
	$result = $db->query("select depth from type where id = ".$pid);
	if($row = mysql_fetch_array($result)) {
		$depth = $row['depth'];
	}
	
	$names=array('`type_name`','`pid`','`depth`');
	$values=array("'".$name."'",$pid,$depth+1);
	$db->query("insert into type (".implode(',',$names).") values (".implode(',',$values).")");
	$result = $db->query("select * from type where id = ".mysql_insert_id());
	$row = mysql_fetch_array($result);
	$db->close();
	echo json_encode(array('code'=>0,'data'=>array('id'=>$row['id'],'pid'=>$row['pid'],'name'=>$row['type_name'],'depth'=>$row['depth'])));
}

?>