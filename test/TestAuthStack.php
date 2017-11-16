<?php

use PHPUnit\Framework\TestCase;
use AuthStack\Services\ConfigService;
use AuthStack\AuthStack;

require '../vendor/autoload.php';

class TestAuthStack extends TestCase
{
    public $config;
    public $stack;

    public function setUp(){
        $filePath = "/var/www/starbuck/test/data/config.yaml";
        $confService = new ConfigService();
        $confService->init($filePath);
        $stack = $confService->getAuthStack();
        $logger = $confService->getLogger();
        $this->stack = new AuthStack($stack, $logger);
    }

    public function testCheckPassword(){
        $result = $this->stack->localCheckPassword("user01", "P@ssw0rd");
        $this->assertTrue($result[0]);
        $this->assertEquals($result[1], "mysql");
        $this->assertNull($this->stack->localCheckPassword("user01", "P@ssw0rjgd"));
    }

    public function tearDown(){
    }
}