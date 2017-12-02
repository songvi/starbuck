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

    public function isExist($userId){}

    public function getUser($userId){
        $user = new AuthStackUser();
        // Todo get user form source
        // Todo map to AuthStackUser
        return $user;
    }

    public function listUser(){}
}
