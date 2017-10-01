<?php

namespace AuthStack\Auths;

class RemoteAuth extends AbstractAuth{
    private $callbackURL;

    public function setCallbackUrl($url){
        $this->callbackURL = $url;
    }

    public function getCallbackUrl(){
        return $this->callbackURL;
    }
}
