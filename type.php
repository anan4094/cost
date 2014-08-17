<?php
header("Content-type: application/json; charset=utf-8"); 
require_once("mysql.class.php");

$db = new Mysql();
$db->open();
$result = $db->query("select * from type");

$arr = array();
$ret = array();
while ($row = mysql_fetch_array($result)) {
	$pid = $row['pid'];
	unset($tmp);
	$tmp = array();
	$tmp['id'] = $row['id'];
	$tmp['name'] = $row['type_name'];
	if (!isset($pid)) {
		$ret[count($ret)] = &$tmp;
	}else{
		$item= &$arr[$pid];
		$values = &$item['values'];
		if (!isset($values)) {
			$values = array();
			$item['values'] = &$values;
		}
		$tmp['pid'] = $pid;
		$values[count($values)] = &$tmp;
	}
	$arr[$tmp['id']]=&$tmp;
 }
$db->close();
echo json_encode(array('data'=>$ret));

?>