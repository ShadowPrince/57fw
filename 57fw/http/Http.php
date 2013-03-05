<?php
namespace Http;

class Http {
    protected $e;

    public function __construct($e) {
        $this->e = $e;
    }
    
    public function getFullURL() {
        return $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    }

    public function getRequestPath() {
    //    return $_SERVER['PATH_INFO'];
    }
}
