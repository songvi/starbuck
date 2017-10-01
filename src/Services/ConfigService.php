<?php

namespace AuthStack\Services;

use AuthStack\Auths\AuthSql;
use AuthStack\Auths\LocalAuth;
use AuthStack\Configs\MySQLConfig;
use AuthStack\Exceptions\ConfigNotFoundExeption;
use AuthStack\Exceptions\ConfigSyntaxException;
use Symfony\Component\Yaml\Yaml;

class ConfigService{
    protected  $config;
    /**
     * init get the path to file config and return a list of auth config
     * @param $yamlFilePath
     * @return array() of auth
     * @throws ConfigNotFoundException
     * @throws ConfigSyntaxException
     */
    public function init($yamlFilePath){
        if(!is_file($yamlFilePath)) {
            throw new ConfigNotFoundException();
        };

        try {
            $this->config = Yaml::parse(file_get_contents($yamlFilePath));
        } catch (\Exception $e) {
            throw new ConfigSyntaxException($yamlFilePath);
        }
    }

    public function getAuthStack(){
        $authStack = [];
        foreach($this->config["authstack"] as $auth){
            switch (strtolower($auth["type"])){
                case 'sql':
                    $sqlConfig = new MySQLConfig($auth["config"]);
                    $authSql = new AuthSql($sqlConfig);
                    $authStack[] = $authSql;
                    break;
                case 'ldap':
                    break;
                case 'oidc':
                    break;
                case 'oauth2':
                    break;
                case 'saml':
                    break;
                case 'cas':
                    break;
                default:
                    break;
            }
        }
        return $authStack;
    }
}
