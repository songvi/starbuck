<?php

namespace AuthStack\Auths;

class AbstractAuth{
    protected $name;
    protected $type;
    protected $order;

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

    public function setOrder($order){
        $this->order = $order;
    }

    public function getOrder(){
        return $this->order;
    }
}
