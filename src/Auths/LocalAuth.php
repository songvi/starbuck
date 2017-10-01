<?php

namespace AuthStack\Auths;

class LocalAuth extends AbstractAuth{

    /**
     * check password of $username.
     * @param $username username
     * @param $password password
     * @return bool
     */
    public function checkPassword($username, $password){
        return false;
    }
}
