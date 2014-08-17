<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
$mysql_server_name='localhost';
$mysql_username='root';
$mysql_password='550533221';
$mysql_database='cost';
$reset = $_GET['reset'];
$conn=mysql_connect($mysql_server_name,$mysql_username,$mysql_password,$mysql_database);
if (!$conn) {
	die("Connection:".mysql_error());
}
mysql_query('set names utf8');
$sql='CREATE DATABASE doc DEFAULT CHARACTER SET utf8 COLLATE utf8_bin ;
';
mysql_query($sql);
mysql_select_db($mysql_database,$conn);

if ($reset) {
	$tables = array(`item`,`type`);
	foreach ($tables as $key => $value) {
		//DROP [TEMPORARY] TABLE [IF EXISTS] tbl_name
		$sql = 'DROP TABLE IF EXISTS '.$value;
		$result=mysql_query($sql);
		if ($result) {
			echo "drop ".$value.' successfull<br/>';
		}
	}
	
}

$sql='CREATE TABLE `type`(`id` INT(255) UNSIGNED NOT NULL,`pid` INT(255) UNSIGNED,`type_name` NVARCHAR(25) NOT NULL,PRIMARY KEY(`id`));';
$result=mysql_query($sql);
if ($result) {
	echo "create type successfull<br/>";
}

$sql='CREATE TABLE `item`(`id` INT(255) UNSIGNED NOT NULL,`type_id` INT(255) UNSIGNED NOT NULL,`memo` NVARCHAR(25),`status` INT(255) DEFAULT 1,`cost` FLOAT NOT NULL,PRIMARY KEY(`id`),CONSTRAINT fk_pid FOREIGN KEY(type_id) REFERENCES type(id) on delete cascade);';
$result=mysql_query($sql);
if ($result) {
	echo "create item successfull<br/>";
}

$sql = 'insert into `type`(id,type_name)values(1,\'食\')';
mysql_query($sql);

$sql = 'insert into `type`(id,type_name)values(2,\'衣\')';
mysql_query($sql);

$sql = 'insert into `type`(id,type_name)values(3,\'衣\')';
mysql_query($sql);

$sql = 'insert into `type`(id,type_name)values(4,\'行\')';
mysql_query($sql);

mysql_close($conn);

?>