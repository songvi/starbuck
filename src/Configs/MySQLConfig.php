<?php

namespace AuthStack\Configs;

/**
 *
 */
class MySQLConfig extends AbstractConfig{
    public $host;
    public $driver;
    public $port;
    public $username;
    public $password;
    public $dbname;
    public $table;
    public $useridcol;
    public $pwdcol;
    public $asciikey;
    public $authName;

    public function __construct($config){
        isset($config["host"])? ($this->host = $config["host"]): null;
        isset($config["username"])? ($this->username = $config["username"]): null;
        isset($config["password"])? ($this->password = $config["password"]): null;
        isset($config["driver"])? ($this->driver = $config["driver"]): null;
        isset($config["dbname"])? ($this->dbname = $config["dbname"]): null;
        isset($config["table"])? ($this->table = $config["table"]): null;
        isset($config["useridcol"])? ($this->useridcol = $config["useridcol"]): null;
        isset($config["pwdcol"])? ($this->pwdcol = $config["pwdcol"]): null;
        isset($config["asciikey"])? ($this->asciikey = $config["asciikey"]): null;
    }
}