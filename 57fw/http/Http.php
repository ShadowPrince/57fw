<?php
namespace Http;

/**
 * Service for HTTP workflow
 */
class Http extends \Core\Service {
    protected $response, $request;

    public function __construct() {
        $this->request = new \Http\Request(
            $_SERVER,
            $_GET,
            $_POST,
            $_FILES,
            $_COOKIE
        );
    }

    /**
     * Get request
     */
    public function getRequest() {
        return $this->request;
    }

    /**
     * Get response
     */
    public function getResponse() {
        return $this->response;
    }

    public function setResponse($res) {
        $this->response = $res;

        return $this;
    }

    /**
     * @return string
     */
    public function engageResponse() {
        if ($this->getResponse()->getCookies())
            foreach ($this->getResponse()->getCookies() as $args) {
                call_user_func_array('setcookie', $args);
            }

        if ($this->getResponse()->getHeaders())
            foreach ($this->getResponse()->getHeaders() as $header) {
                header($header);
            }

        return $this->getResponse()->getBody();
    }
}
