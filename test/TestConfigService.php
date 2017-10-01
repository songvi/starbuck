<?php

use PHPUnit\Framework\TestCase;
use AuthStack\Services\ConfigService;


require '../vendor/autoload.php';

class TestConfigService extends TestCase
{
    public function setUp(){
        $config = file_get_contents(__DIR__.DIRECTORY_SEPARATOR."data".DIRECTORY_SEPARATOR."config.yaml");
        file_put_contents(__DIR__.DIRECTORY_SEPARATOR."data".DIRECTORY_SEPARATOR."config.error.yaml", $config);
        file_put_contents(__DIR__.DIRECTORY_SEPARATOR."data".DIRECTORY_SEPARATOR."config.error.yaml", "testet\nste", FILE_APPEND);
    }

    public function testLoadConfig(){
        $filePath = "/var/www/starbuck/test/data/config.yaml";
        $confService = new ConfigService();
        $confService->init($filePath);
        $authStack = $confService->getAuthStack();
        $this->assertEquals(count($authStack), 2);
        $this->assertEquals($authStack[0]->getName(), "mysql");
    }

    public function testConfigNotFoundException(){
        $filePath = __DIR__.DIRECTORY_SEPARATOR."data".DIRECTORY_SEPARATOR."config.errdsfsor.yaml";
        $this->expectException("AuthStack\\Exceptions\\ConfigNotFoundException");
        $confService = new ConfigService();
        $confService->init($filePath);
    }

    public function testConfigSyntaxException(){
        $filePath = __DIR__.DIRECTORY_SEPARATOR."data".DIRECTORY_SEPARATOR."config.error.yaml";
        $this->expectException("AuthStack\\Exceptions\\ConfigSyntaxException");
        $confService = new ConfigService();
        $confService->init($filePath);
    }
    public function tearDown(){
        unlink(__DIR__.DIRECTORY_SEPARATOR."data".DIRECTORY_SEPARATOR."config.error.yaml");
    }
}

