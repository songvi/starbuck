<?php

namespace AuthStack\Auths;

class AbstractAuth{
    protected $name;
    protected $type;

    public function setName($name){
        $this->name = $name;
    }

    public function getName(){
        return $this->name;
    }

    public function setType($type){
        $this->type = $type;
    }

    public function getType(){
        return $this->type;
    }
}
