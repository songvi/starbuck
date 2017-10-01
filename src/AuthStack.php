<?php

namespace AuthStack;
use AuthStack\Auths\LocalAuth;

class AuthStack {
    private $stack;

    /**
     * @param $stack
     */
    public function __construct($stack){
        $this->stack = $stack;
    }

    /**
     *
     */
    public function localCheckPassword($username, $password){
        foreach($this->stack as $order => $auth) {
            if($auth instanceof LocalAuth){
                echo $auth->getName();
                if($auth->checkPassword($username, $password)){
                    // login ok
                    return [true, $auth->getName()];
                    break;
                }
            }
        }
        return null;
    }
}