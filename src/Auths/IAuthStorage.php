<?php

namespace AuthStack\Auths;

interface IAuthStorage {
    public function createUser($userId);
    public function setPassword($uid, $password);
    public function isExist($uid);
    public function updatePassword($uid, $password);
    public function checkPassword($uid, $passphrase);
    public function getPassPhrase($uid);
    public function delete($uid);
    public function listUser();
}
