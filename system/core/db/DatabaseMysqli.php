<?php

namespace system\core\db;

/**
 * Class DatabaseMysqli
 * @package system\core\db
 */
class DatabaseMysqli {
    private $showLog = 0;
    private $memcacheEnable = 0;
    private $memcacheIP = '127.0.0.1';
    private $memcachePort = '11211';

    protected $link;
    private $result;
    private $memcacheLoad;
    private $memcache = 0;
    private $query = '';
    private $startTime;

    private $ip;
    private $login;
    private $mysqlBase;

    public function __construct($memcacheEnable = 0) {
        $this->memcacheEnable = $memcacheEnable;

        if ($this->memcacheEnable == 1) {
            // Start Connect with memcache
            $this->memcacheLoad = new \Memcache;
            $this->memcacheLoad->connect($this->memcacheIP, $this->memcachePort) or exit ("Could not connect");
        }
    }

    public function connect($ip = null, $login = null, $pass = null, $mysqlBase = null, $port = 3306) {
        $this->ip = $ip;
        $this->login = $login;
        $this->mysqlBase = $mysqlBase;

        $this->link = mysqli_connect($ip, $login, $pass, $mysqlBase, $port) or exit ("Could not connect to MySQLi");
        if ($this->link) {
            mysqli_query($this->link, "SET CHARACTER SET utf8");
            mysqli_query($this->link, "set character_set_client='utf8'");
            mysqli_query($this->link, "set character_set_results='utf8'");
            mysqli_query($this->link, "set collation_connection='utf8_general_ci'");
            mysqli_query($this->link, "SET NAMES utf8");
//            mysqli_select_db ($this->link, $this->mysqlBase) or exit ("Could not select database");
        }
    }

    public function disconnect() {
        mysqli_close($this->link);
    }

    public function query($query) {
        if ($this->showLog == 1) {
            // Start CountTime
            $this->startTime = microtime(true);
        }

        if ($this->memcacheEnable == 1) {
            $this->query = $query;
            $getResult = $this->memcacheLoad->get(md5($this->mysqlBase.$query));
        } else {
            $getResult = 0;
        }

        if (empty($getResult)) {
            $this->memcache = 0;

            if ($this->link) {
                if ($this->result = mysqli_query($this->link, $query)) {
                    if ($this->showLog == 1) {
                        $execTime = microtime(true) - $this->startTime;
                        echo $query.PHP_EOL;
                        file_put_contents(dirname(__FILE__)
                            ."/sql.log", "sql - "
                            .$query
                            .PHP_EOL
                            .$execTime
                            .PHP_EOL.PHP_EOL, FILE_APPEND);
                    }
                    return true;
                } else {
                    echo $query." - Could not mysqli_query IP - "
                        .$this->ip
                        ."; Login - "
                        .$this->login
                        ."; BASE - "
                        .$this->mysqlBase
                        ."<br/>"
                        .PHP_EOL;

                    return false;
                }
            } else {
                echo "Could not connect to MySQLi<br/>".PHP_EOL;
                return false;
            }
        } else {
            $this->result = 1;
            $this->memcache = 1;
            return true;
        }
    }

    public function getID(){
        return $this->link ? mysqli_insert_id($this->link) : false;
    }

    public function row() {
        $i = 0;
        $return = Array();

        if (isset($this->result)) {
            if ($this->memcache == 1) {
                if ($this->showLog == 1) {
                    $execTime = microtime(true) - $this->startTime;
                    file_put_contents(
                        dirname(__FILE__)
                        ."/sql.log", "memcache - "
                        .$this->query
                        .PHP_EOL
                        .$execTime
                        .PHP_EOL.PHP_EOL, FILE_APPEND);
                }
                $return = $this->memcacheLoad->get(md5($this->mysqlBase.$this->query));
                $this->memcacheLoad->set(md5($this->mysqlBase.$this->query), $return, false, 600);
                $this->memcache = 0;
            } else {

                if (mysqli_num_rows($this->result) > 0) {
                    while ($row = mysqli_fetch_assoc($this->result)) {
                        $return[$i++] = $row;
                    }
                    if ($this->memcacheEnable == 1) {
                        $this->memcacheLoad->set(md5($this->mysqlBase.$this->query), $return, false, 600);
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
            $value = "'".mysqli_real_escape_string($this->link, $value)."'";
        }
        return $value;
    }
}