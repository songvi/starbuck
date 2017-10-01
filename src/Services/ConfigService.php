<?php

namespace AuthStack\Services;

use AuthStack\Auths\AuthLdap;
use AuthStack\Auths\AuthSql;
use AuthStack\Configs\AuthType;
use AuthStack\Configs\LdapConfig;
use AuthStack\Configs\MySQLConfig;
use AuthStack\Exceptions\ConfigNotFoundException;
use AuthStack\Exceptions\ConfigSyntaxException;
use AuthStack\Logs\LogFile;
use AuthStack\Logs\LogType;
use Psr\Log\LoggerInterface;
use Symfony\Component\Yaml\Yaml;

class ConfigService
{
    protected $config;

    /**
     * init get the path to file config and return a list of auth config
     * @param $yamlFilePath
     * @return array() of auth
     * @throws ConfigNotFoundException
     * @throws ConfigSyntaxException
     */
    public function init($yamlFilePath)
    {
        if (!is_file($yamlFilePath)) {
            throw new ConfigNotFoundException();
        };

        try {
            $this->config = Yaml::parse(file_get_contents($yamlFilePath));
        } catch (\Exception $e) {
            throw new ConfigSyntaxException($yamlFilePath);
        }
    }

    public function setAuthStack($config){
        $this->config["authstack"] = $config;
    }

    public function setLogger($logger){
        $this->config["log"] = $logger;
    }

    public function getAuthStack()
    {
        $stack = [];
        $logger = $this->getLogger();
        foreach ($this->config["authstack"] as $auth) {
            switch (strtolower($auth["type"])) {
                case AuthType::SQL:
                    $sqlConfig = new MySQLConfig($auth["config"]);
                    $authSql = new AuthSql($sqlConfig, $logger);
                    $authSql->setName($auth["name"]);
                    $stack[] = $authSql;
                    break;
                case AuthType::LDAP:
                    $ldapConfig = new LdapConfig($auth["config"]);
                    $authLdap = new AuthLdap($ldapConfig, $logger);
                    $authLdap->setName($auth["name"]);
                    $stack[] = $authLdap;
                    break;
                case AuthType::OIDC:
                    break;
                case AuthType::OAUTH2:
                    break;
                case AuthType::SAML:
                    break;
                case AuthType::CAS:
                    break;
                default:
                    break;
            }
        }
        return $stack;
    }

    public function  getLogger()
    {
        if($this->config["log"] instanceof LoggerInterface){
            return $this->config["log"];
        }

        // If logger is an array, try to construct to object.
        $logger = $this->config["log"];
            switch (strtolower($logger["type"])) {
                case LogType::FILE:
                    return new LogFile($logger["filePath"]);
                    break;
                case LogType::SQL:
                    break;
                default:
                    break;
            }
    }
}
