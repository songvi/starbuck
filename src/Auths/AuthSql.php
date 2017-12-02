<?php

namespace AuthStack\Auths;

use AuthStack\Configs\AuthType;
use AuthStack\Configs\MySQLConfig;
use AuthStack\Exceptions\IdentityNotFoundException;
use AuthStack\Exceptions\KeyRequireException;
use Dibi\Connection;
use \Dibi;
use Defuse\Crypto\Key;
use ParagonIE\PasswordLock\PasswordLock;
use Psr\Log\LoggerInterface;

class AuthSql extends LocalAuth{
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

    protected function getHash($clearPassPhrase){
        return PasswordLock::hashAndEncrypt($clearPassPhrase, $this->key);
    }

    /**
     *
     */
    protected function _checkPassword($uid, $passphrase){
        if(!$encryptedPassPhrase = $this->getPassPhrase($uid)) {
            $this->logger->info("[AuthSQL] user: ". $uid. " login failed");
            throw new IdentityNotFoundException();
        }
        if(PasswordLock::decryptAndVerify($passphrase, $encryptedPassPhrase, $this->key)) {
            $this->logger->info("[AuthSQL] user: " . $uid . " login ok");
            return true;
        }
        return false;
    }

    /**
     * @param $uid
     * @return string
     */
    protected function getPassPhrase($uid){
        $result = $this->conn->query("SELECT [".$this->config->pwdcol."] FROM [".$this->config->table."] WHERE [".$this->config->useridcol."] = %s", $uid);
        $pass = $result->fetchSingle();
        return $pass;
    }

    /**
     *
     */
    public function checkPassword($uid, $passphrase){
        return $this->_checkPassword($uid, $passphrase);
    }

    public function listUser(){
        $result = $this->conn->query("SELECT [".$this->config->useridcol."] from [".$this->config->table."]");
        return intval($result->fetchSingle());
    }

    public function isExist($uid){
        $result = $this->conn->query("SELECT COUNT(*) from  ".$this->config->table."  WHERE [".$this->config->useridcol."] = %s", $uid);
        return (intval($result->fetchSingle()) > 0);
    }

    public function getUser($userId){}
}