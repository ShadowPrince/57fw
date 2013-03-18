<?php
namespace Http;

/**
 * Service for HTTP workflow
 */
class Http extends \Core\Service {
    protected $server; 

    public function __construct() {
        $this->server = $_SERVER;
    }

    /**
     * @return string
     */
    public function getFullURL() {
        return $this->server['SERVER_NAME'] . $this->server['REQUEST_URI'];
    }

    /**
     * @return string
     */
    public function getRequestPath() {
        if (isset($this->server['PATH_INFO']))
            return $this->server['PATH_INFO'];
    }

    /**
     * @return string
     */
    public function getDomain() {
        return $this->server['SERVER_NAME'];
    }
}
