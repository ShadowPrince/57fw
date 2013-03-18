<?php
namespace Http;

class Http extends \Core\Service {
    protected $server; 

    public function __construct() {
        $this->server = $_SERVER;
    }

    public function getFullURL() {
        return $this->server['SERVER_NAME'] . $this->server['REQUEST_URI'];
    }

    public function getRequestPath() {
        if (isset($this->server['PATH_INFO']))
            return $this->server['PATH_INFO'];
    }

    public function getDomain() {
        return $this->server['SERVER_NAME'];
    }
}
