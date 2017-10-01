<?php

use PHPUnit\Framework\TestCase;
use AuthStack\Services\ConfigService;
require '../vendor/autoload.php';

class TestAuthSql extends TestCase
{
    public $config;
    public $stack;

    public function setUp(){
        $filePath = "/var/www/starbuck/test/data/config.yaml";
        $confService = new ConfigService();
        $confService->init($filePath);
        $this->stack = $confService->getAuthStack()[0];
        $logger = $confService->getLogger();
        $this->stack->createUser("user02", "P@ssw0rd");
    }

    public function testCheckPassword(){
        $this->assertTrue($this->stack->checkPassword("user02", "P@ssw0rd"));
        $this->assertFalse($this->stack->checkPassword("user02", "P@ssw0rjgd"));
    }

    public function tearDown(){
       $this->stack->delete("user02");
    }
}