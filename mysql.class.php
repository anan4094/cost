<?php
/**
*
*/
class Mysql{
	var $mysql_server_name;
	var $mysql_username;
	var $mysql_password;
	var $mysql_database;
	var $conn;
	function Mysql(){
		$this->mysql_server_name='192.168.1.103';
		$this->mysql_username='root';
		$this->mysql_password='550533221';
		$this->mysql_database='cost';
	}
	function open(){
		$this->conn=mysql_connect($this->mysql_server_name,$this->mysql_username,$this->mysql_password,$this->mysql_database);
		if (!$this->conn) {
			return mysql_error();
		}
		mysql_query('set names utf8');
		mysql_select_db($this->mysql_database,$this->conn);
		return true;
	}
	function query($sql){
		return mysql_query($sql);
	}
	function close(){
		mysql_close($this->con);
	}
}
?>