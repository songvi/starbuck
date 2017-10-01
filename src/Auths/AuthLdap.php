<?php

namespace AuthStack\Auths;

use AuthStack\Configs\AuthType;
use AuthStack\Configs\LdapConfig;

class AuthLdap extends LocalAuth{
    public function  __construct(LdapConfig $config){
        $this->setType(AuthType::LDAP);
    }
}
