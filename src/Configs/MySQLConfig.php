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
}