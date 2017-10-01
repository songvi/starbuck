<?php

namespace AuthStack;
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
     *
     */
    public function localCheckPassword($username, $password){
        foreach($this->stack as $order => $auth) {
            if($auth instanceof LocalAuth){
                if($auth->checkPassword($username, $password)){
                    // login ok
                    $this->logger->info("[".$auth->getName()."] Check password for user: ". $username." OK" );
                    return [true, $auth->getName()];
                    break;
                }
            }
        }
        $this->logger->notice("[".$auth->getName()."] Check password for user: ". $username." failed" );
        return null;
    }

    public function remoteCheckPassword(){

    }

    public function setStack($stack){
        $this->stack = $stack;
    }

    public function setLogger($logger){
        $this->logger = $logger;
    }
}