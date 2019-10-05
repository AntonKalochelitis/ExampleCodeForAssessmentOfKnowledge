<?php

namespace system\core\db;

class DatabaseMysqli {
	private		$show_log = 0;
	private		$memcache_enable = 0;
	private 	$memcache_ip = '127.0.0.1';
	private		$memcache_port = '11211';
	
	public		$link;
	private		$result;
	private		$memcache_load;
	private		$memcache = 0;
	private		$query = '';
	private		$start_time;
	
	private $ip;
	private $login;
	private $mysql_base;
	
	public function __construct($memcache_enable = 0) {
	    $this->memcache_enable = $memcache_enable;
	    
	    if ($this->memcache_enable == 1) {
		// Start Connect with memcache
		$this->memcache_load = new Memcache;
		$this->memcache_load->connect($this->memcache_ip, $this->memcache_port) or exit ("Could not connect");
	    }
	}
	
	public function connect($ip = null, $login = null, $pass = null, $mysql_base = null) {
		$this->ip		= $ip;
		$this->login		= $login;
		$this->mysql_base	= $mysql_base;
		
		$this->link = mysqli_connect($ip, $login, $pass) or exit ("Could not connect to MySQLi");
		if ($this->link) {
			mysqli_query($this->link, "SET CHARACTER SET utf8");
			mysqli_query($this->link, "set character_set_client='utf8'");
			mysqli_query($this->link, "set character_set_results='utf8'");
			mysqli_query($this->link, "set collation_connection='utf8_general_ci'");
			mysqli_query($this->link, "SET NAMES utf8");
			mysqli_select_db ($this->link, $mysql_base) or exit ("Could not select database");
		}
	}
	
	public function disconnect() {
		mysqli_close($this->link);
	}
	
	public function query($query) {
	
	    if ($this->show_log == 1) {
		// Start CountTime
		$this->start_time = microtime(true);
	    }
	
	    if ($this->memcache_enable == 1) {
		$this->query = $query;
		$get_result = $this->memcache_load->get(md5($this->mysql_base . $query));
	    } else {
		$get_result = 0;
	    }
	
	    if (empty($get_result)) {
		$this->memcache = 0;
		
		if ($this->link) {
			if ($this->result = mysqli_query($this->link, $query)) {
				if ($this->show_log == 1) {
				    $exec_time = microtime(true) - $this->start_time;
				    echo $query . PHP_EOL;
				    file_put_contents(dirname(__FILE__) . "/sql.log", "sql - " . $query . PHP_EOL . $exec_time . PHP_EOL . PHP_EOL, FILE_APPEND);
				}
				return true;
			} else {
				echo $query." - Could not mysqli_query IP - " . $this->ip . "; Login - " . $this->login . "; BASE - " . $this->mysql_base . "<br/>" . PHP_EOL;
				
				return false;
			}
		} else {
			echo "Could not connect to MySQLi<br/>" . PHP_EOL;
			return false;
		}
	    } else {
		$this->result = 1;
		$this->memcache = 1;
		return true;
	    }
	}

	public function ID(){
        return $this->link ? mysqli_insert_id($this->link) : false;
    }
	
	public function row() {
		$i = 0;
		$return = Array();

		if (isset($this->result)) {
			if ($this->memcache == 1) {
			    if ($this->show_log == 1) {
				$this->exec_time = microtime(true) - $this->start_time;
				file_put_contents(dirname(__FILE__) . "/sql.log", "memcache - ".$this->query . PHP_EOL . $this->exec_time . PHP_EOL . PHP_EOL, FILE_APPEND);
			    }
			    $return = $this->memcache_load->get(md5($this->mysql_base . $this->query));
			    $this->memcache_load->set(md5($this->mysql_base . $this->query), $return, false, 600);
			    $this->memcache = 0;
			} else {
			    
			    if (mysqli_num_rows($this->result) > 0) {
				while ($row = mysqli_fetch_assoc($this->result)) {
				    $return[$i++] = $row;
				}
				if ($this->memcache_enable == 1) {
				    $this->memcache_load->set(md5($this->mysql_base . $this->query), $return, false, 600);
				}
			    }
			}
		}
		
		unset($this->result);
		
		return $return;
	}
	
	public function Escape($value) {
		if (get_magic_quotes_gpc()) {
			$value = stripslashes($value);
		}

		if (strtoupper($value) !== 'NULL') {
			$value = "'" . mysqli_real_escape_string($this->link, $value) . "'";
		}
		return $value;
	}
}