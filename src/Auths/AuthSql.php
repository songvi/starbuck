<?php

namespace AuthStack\Auths;

use AuthStack\Configs\AuthType;
use AuthStack\Configs\MySQLConfig;
use AuthStack\Exceptions\KeyRequireException;
use Dibi\Connection;
use \Dibi;
use Defuse\Crypto\Key;
use ParagonIE\PasswordLock\PasswordLock;
use Psr\Log\LoggerInterface;

class AuthSql extends LocalAuth implements IAuthStorage{

    protected  $key;
    protected  $config;
    protected  $logger;

    public function __construct(MySQLConfig $config, LoggerInterface $logger)
    {
        $this->setType(AuthType::SQL);
        $this->setName($config->authName);
        $this->logger = $logger;

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
        if(empty($config->asciikey)) {
            throw new KeyRequireException();
        }
        $asciikey = $config->asciikey;
        $this->key = KEY::loadFromAsciiSafeString($asciikey);
    }

    public function createUser($uid, $passphrase = null){
        if(!$this->isExist($uid)){
            if($passphrase) {
                $pass = $this->getHash($passphrase);
            }else{
                $pass = null;
            }
            $data = ["userid" => $uid,
                "password" => $pass
            ];

            $result = $this->conn->query("INSERT INTO [" . $this->config->table . "] ", $data);
            $this->logger->info("[AuthSQL] create new user: ". $uid);
            return true;
        }
        return false;
    }

    public function setPassword($uid, $passphrase){
        if(empty($uid) || empty($passphrase)) return false;

        if(!$this->isExist($uid)){
            $pass = $this->getHash($passphrase);
            $data = ["userid " => $uid,
                "password" => $pass];
            $result = $this->conn->query("INSERT INTO [".$this->config->table."] ", $data);
            return true;
        }
        return false;
    }

    public function isExist($uid){
        $result = $this->conn->query("SELECT COUNT(*) from  ".$this->config->table."  WHERE [".$this->config->useridcol."] = %s", $uid);
        return (intval($result->fetchSingle()) > 0);
    }

    public function updatePassword($uid, $password){
        $result = $this->conn->query("UPDATE  [".$this->config->table."]  set  [".$this->config->pwdcol."]  = %s WHERE [uid] = %s", $this->getHash($password), $uid);
        $this->logger->info("[AuthSQL] user : ". $uid. " changed password");
        return true;
    }

    protected function getHash($clearPassPhrase){
        return PasswordLock::hashAndEncrypt($clearPassPhrase, $this->key);
    }

    /**
     *
     */
    public function checkPassword($uid, $passphrase){
        return $this->_checkPassword($uid, $passphrase);
    }

    /**
     *
     */
    protected function _checkPassword($uid, $passphrase){
        if(!$encryptedPassPhrase = $this->getPassPhrase($uid)) {
            $this->logger->info("[AuthSQL] user: ". $uid. " login failed");
        }
        if(PasswordLock::decryptAndVerify($passphrase, $encryptedPassPhrase, $this->key)) {
            $this->logger->info("[AuthSQL] user: " . $uid . " login ok");
            return true;
        }
        return false;
    }

    public function getPassPhrase($uid){
        $result = $this->conn->query("SELECT [".$this->config->pwdcol."] FROM [".$this->config->table."] WHERE [".$this->config->useridcol."] = %s", $uid);
        $pass = $result->fetchSingle();
        return $pass;
    }

    public function delete($uid){
        if($this->isExist($uid)){
            $result = $this->conn->query("DELETE FROM [".$this->config->table."] WHERE [".$this->config->useridcol."] = %s", $uid);
            $this->logger->info("[AuthSQL] delete user : ". $uid. " ok");
            return $result;
        }
        return false;
    }

    public function listUser(){
        $result = $this->conn->query("SELECT [".$this->config->useridcol."] from [".$this->config->table."]");
        return intval($result->fetchSingle());
    }
}