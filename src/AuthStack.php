<?php

namespace AuthStack\Auths;

class AuthStack {
    private $stack;

    public function __construct($stack){
        $this->stack = $stack;
    }

    public function localCheckPassword($username, $password){
        foreach($this->stack as $order => $auth) {
            if($auth instanceof LocalAuth){
                if($auth->checkPassword($username, $password)){
                    // login ok
                    return [true, $auth->getName()];
                }
            }
        }
        return null;
    }
}