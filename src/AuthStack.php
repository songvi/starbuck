<?php

namespace AuthStack;
use AuthStack\Auths\AbstractAuth;
use AuthStack\Auths\LocalAuth;
use Psr\Log\LoggerInterface;

class AuthStack {
    private $stack;
    private $logger;

    /**
     * @param $stack
     * @param LoggerInterface $logger
     */
    public function __construct($stack, LoggerInterface $logger){
        $this->stack = $stack;
        $this->logger = $logger;
    }

    /**
     * @param $username string
     * @param $password string
     * @return array|bool
     */
    public function localCheckPassword($username, $password){
        $this->sort();
        foreach($this->stack as $auth) {
            if($auth instanceof LocalAuth){
                if($auth->checkPassword($username, $password)){
                    // login ok
                    $this->logger->info("[".$auth->getName()."] Check password for user: ". $username." OK" );
                    return [true, $auth->getName()];
                    break;
                }
            }
        }
        $this->logger->notice("[AuthStack] Check password for user: ". $username." failed" );
        return null;
    }

    public function isExist($username){
        foreach($this->stack as $auth){
            if($auth instanceof LocalAuth){
                if($auth->isExist($username)) return true;
            }
        }
        return false;
    }

    public function remoteCheckPassword(){

    }

    public function setStack($stack){
        $this->stack = $stack;
    }

    public function addAuth(AbstractAuth $auth){
        $this->stack[] = $auth;
        $this->sort();
    }

    protected  function sort(){
        usort($this->stack, array($this, 'compare'));
    }

    protected function compare(AbstractAuth $a, AbstractAuth $b){
        if ($a->getOrder() === $b->getOrder()) return 0;
        return  ($a->getOrder() > $b->getOrder()) ? 1 : 0;
    }

    public function setLogger($logger){
        $this->logger = $logger;
    }
}