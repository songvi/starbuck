<?php

namespace AuthStack\Auths;

class AbstractAuth{
    protected $name;

    public function setName($name){
        $this->name = $name;
    }

    public function getName(){
        return $this->name;
    }
}
