<?php

class DbConnection {
	var $dbh;
	var $stmt;

	var $mem;
	var $dsnkey;

	var $dsn;
	var $username;
	var $passwd;
	var $options;

	function getConn() {
		$this->dbh = new PDO ( $this->dsn, $this->username, $this->passwd,$this->options );
	}

	function __construct($dsn, $username, $passwd, $options=null) {
		$this->dsn = $dsn;
		$this->username = $username;
		$this->passwd = $passwd;
		$this->options = $options;

		$this->dbh = null;
		$this->mem = null;

		$this->dsnkey = crypt($dsn.$username,$passwd);
	}


	function Insert_ID() {
		return $this->dbh->lastInsertId();
	}
	function Affected_Rows() {
		return $this->stmt->rowCount();
	}

	function GetAll($sql, $inputarr = false) {
		$this->stmtExecute ( $sql, $inputarr );
		return $this->stmt->fetchAll (PDO::FETCH_ASSOC);
	}
	function GetRow($sql, $inputarr = false) {
		$this->stmtExecute ( $sql . " LIMIT 1", $inputarr );
		$result = $this->stmt->fetchAll (PDO::FETCH_ASSOC);
		if ($result) {
			$result = $result [0];
		}
		return $result;
	}
	function GetOne($sql, $inputarr = false) {
		$this->stmtExecute ( $sql . " LIMIT 1", $inputarr );
		return $this->stmt->fetchColumn ();
	}
	function GetOneInt($sql, $inputarr = false) {
		return intval($this->GetOne($sql,$inputarr));
	}
	function GetCol($sql, $inputarr = false) {
		$this->stmtExecute ( $sql, $inputarr );
		return $this->stmt->fetchAll ( PDO::FETCH_COLUMN, 0 );
	}
	function GetLimit($sql, $nrows = -1, $offset = -1, $inputarr = false) {
		if ($offset < 0 && $nrows < 0) {
			$this->stmtExecute ( $sql, $inputarr );
		} else if ($offset < 0) {
			$this->stmtExecute ( $sql . " LIMIT " . intval ( $nrows ), $inputarr );

		//		}else if($nrows<0) {
		//			$this->stmtExecute ( $sql . " LIMIT ".intval($nrows), $inputarr );
		} else {
			$this->stmtExecute ( $sql . " LIMIT " . intval ( $offset ) . "," . intval ( $nrows ), $inputarr );
		}
		return $this->stmt->fetchAll (PDO::FETCH_ASSOC);
	}

	function Execute($sql, $inputarr = false) {
		return $this->stmtExecute ( $sql, $inputarr );
	}
	function Replace($table, $fieldArray,$keys=null) {
		$keys = array_keys($fieldArray);
		$values = array_values($fieldArray);

		$poses = array_fill(0, count($keys), '?');

		$sql = 'replace into '.$table.' ('.'`'.implode('`,`', $keys).'`'.' ) values ('.implode(',', $poses).')';

		return $this->stmtExecute ( $sql,$values );
	}
	function CacheGetAll($secs2cache, $sql = false, $inputarr = false) {

		if (! is_numeric ( $secs2cache )) {
			$inputarr = $sql;
			$sql = $secs2cache;
			$secs2cache = MEM_PDO_TIME;
		}

		if ($this->mem == null) {
			$this->mem = getMem();
		}
		$key = serialize ( $inputarr ).$sql.$this->dsnkey;
		if ($secs2cache < 0) {
			$this->mem->delete ( $key );
			return false;//负值 仅表示清除缓存
//			$rs = false;
		} else {
			$rs = $this->mem->get ( $key );
		}
		if ($rs===false) {
			$this->stmtExecute($sql,$inputarr);
			$rs = $this->stmt->fetchAll (PDO::FETCH_ASSOC);
			if ($secs2cache>0) {
				$this->mem->set ( $key,$rs, 0,$secs2cache);
			}
		}
		return $rs;
	}
	function CacheGetRow($secs2cache, $sql = false, $inputarr = false) {
		$result = $this->CacheGetAll($secs2cache,$sql,$inputarr);
		if ($result) {
			$result = $result[0];
		}
		return $result;
	}
	function CacheGetCol($secs2cache, $sql = false, $inputarr = false) {
		$results = $this->CacheGetAll($secs2cache,$sql,$inputarr);
		if ($results) {
			foreach ( $results as $result ) {
				$col [] = array_shift($result);
			}
			return $col;
		}
		return $results;
	}
	function CacheGetOne($secs2cache, $sql = false, $inputarr = false) {
		$result = $this->CacheGetAll($secs2cache,$sql,$inputarr);
		if ($result) {
			$result = array_shift($result[0]);
		}
		return $result;
	}
	function CacheGetOneInt($secs2cache, $sql = false, $inputarr = false) {
		return intval($this->CacheGetOne($secs2cache,$sql,$inputarr));
	}
	private function stmtExecute($sql, $inputarr = false) {
		if ($this->dbh == null) {
			$this->getConn();
		}
		$this->stmt = $this->dbh->prepare ( $sql );
		if (! ($inputarr === false)) {
			if (! is_array ( $inputarr )) {
				$inputarr = array ($inputarr );
			}
			$result = $this->stmt->execute ( $inputarr );
		} else {
			$result = $this->stmt->execute ();
		}
		if (!$result && defined('ERROR_SQL_FILE')) {
			$str='';
			if ($inputarr) {
				$str= print_r($inputarr,true);
				$str = str_replace("\n", "\t", $str);
			}
			$errInfo = $this->stmt->errorInfo();
			$str .= " // ".$errInfo[2]."\n\n";
			file_put_contents ( ERROR_SQL_FILE, "error: ".$sql." //with// ".$str, FILE_APPEND );

		}
		return $result;
	}
}

