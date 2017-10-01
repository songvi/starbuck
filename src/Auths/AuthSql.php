<?php

namespace AuthStack\Auths;

use AuthStack\Configs\MySQLConfig;
use AuthStack\Exceptions\AuthStorageException;
use \Dibi;

class AuthSql extends LocalAuth implements IAuthStorage{

    protected  $key;
    protected  $config;

    public function __construct(MySQLConfig $config)
    {
        $this->config = $config;
        $cfg = [];
        $cfg['host']                = $config->host;
        $cfg['username']            = $config->username;
        $cfg['password']            = $config->password;
        $cfg['databasename']        = $config->dbname;
        $cfg['database']            = $config->dbname;
        $cfg['table']               = $config->table;
        $cfg['useridcolumn']        = $config->useridcol;
        $cfg['passwordcolumn']      = $config->pwdcol;

        $this->conn = new Connection($cfg);
        if($config->asciikey) $asciikey = $config->asciikey;
        $this->key = KEY::loadFromAsciiSafeString($asciikey);
    }

    public function createUser($uid, $passphrase = null){
        if(!$this->isExist($uid)){
            if($passphrase) {
                $pass = $this->getHash($passphrase);
            }else{
                $pass = null;
            }
            $data = ["uid" => $uid,
                "password" => $pass,
                "state" => UserFSM::USER_STATE_INIT
            ];

            try {
                $result = $this->conn->query("INSERT INTO [" . $this->config->table . "] ", $data);
            }
            catch(\Dibi\Exception $e){
                throw new \Exception();
            }
            return true;
        }
        return false;
    }

    public function setPassword($uid, $passphrase){
        if(empty($uid) || empty($passphrase)) return false;

        if(!$this->isExist($uid)){
            $pass = $this->getHash($passphrase);
            $data = ["uid" => $uid,
                "password" => $pass,
                "state" => UserFSM::USER_STATE_INIT];
            $result = $this->conn->query("INSERT INTO [".$this->config->table."] ", $data);
        }
    }

    public function isExist($uid){
        $result = $this->conn->query("SELECT COUNT(*) from  ".$this->config->table."  WHERE [".$this->config->useridcol."] = %s", $uid);
        return (intval($result->fetchSingle()) > 0);
    }

    public function updatePassword($uid, $password){
        $result = $this->conn->query("UPDATE  [".$this->config->table."]  set  [".$this->config->pwdcol."]  = %s WHERE [uid] = %s", $this->getHash($password), $uid);
    }

    protected function getHash($clearPassPhrase){
        return PasswordLock::hashAndEncrypt($clearPassPhrase, $this->key);
    }

    /**
     *
     */
    public function checkPassword($uid, $passphrase){
        return $this->_checkPassword($uid, $passphrase, $this->key);
    }

    /**
     *
     */
    protected function _checkPassword($uid, $passphrase, Key $key = null){
        if(!$encryptedPassPhrase = $this->getPassPhrase($uid)) return false;
        return PasswordLock::decryptAndVerify($passphrase, $encryptedPassPhrase, $this->key);
    }

    public function getPassPhrase($uid){
        $result = $this->conn->query("SELECT [".$this->config->pwdcol."] FROM [".$this->config->table."] WHERE [".$this->config->useridcol."] = %s", $uid);
        $pass = $result->fetchSingle();
        return $pass;
    }

    public function delete($uid){
        if($this->isExist($uid)){
            $result = $this->conn->query("DELETE FROM [".$this->config->table."] WHERE [".$this->config->useridcol."] = %s", $uid);
            return $result;
        }
        return false;
    }

    public function listUser(){
        $result = $this->conn->query("SELECT [".$this->config->useridcol."] from [".$this->config->table."]");
        return intval($result->fetchSingle());
    }
}