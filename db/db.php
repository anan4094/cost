<?php


define("DB_HOST", '127.0.0.1');//192.168.1.103');
define("DB_USER", 'root');
define("DB_PASS", '');//550533221');
define("DB_DBNAME", 'cost');

define('MEM_PDO_TIME', 7200 );
define('ERROR_SQL_FILE','/var/log/sql_err.log');


require_once 'pdo.php';
function getDB() {
	if (! isset ( $GLOBALS ['DATABASE_CONNECTION'] )) {
		$hostname = DB_HOST;
		$username = DB_USER;
		$password = DB_PASS;
		$dbname = DB_DBNAME;
		$dbh = new DbConnection ( "mysql:host={$hostname};dbname={$dbname}"
			, $username
			, $password
			,array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'',PDO::ATTR_PERSISTENT => true));
//		$dbh = new DbConnection ( "mysql:host={$hostname};dbname={$dbname}", $username, $password ,array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));

		$GLOBALS ['DATABASE_CONNECTION'] = &$dbh;
	}
	return $GLOBALS ['DATABASE_CONNECTION'];
}
