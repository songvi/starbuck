<?php

namespace AuthStack\Auths;

use AuthStack\Configs\AuthType;
use AuthStack\Configs\LdapConfig;
use Psr\Log\LoggerInterface;

class AuthLdap extends LocalAuth{
    public function  __construct(LdapConfig $config, LoggerInterface $logger){
        $this->setType(AuthType::LDAP);
    }
}
