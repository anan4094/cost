<?php
header("Content-type: application/json; charset=utf-8");
require_once("db/db.php");

$result = getDB()->GetAll("select * from type");

$arr = array();
$ret = array();
foreach ($result as $row) {
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
echo json_encode(array('data'=>$ret));

?>