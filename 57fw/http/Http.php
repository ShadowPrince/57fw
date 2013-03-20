<?php
namespace Http;

/**
 * Service for HTTP workflow
 */
class Http extends \Core\Service {
    protected $response, $request;

    public function __construct($config=array()) {
        parent::__construct($config);

        if (!$this->config()) {
            $this->request = new \Http\Request(
                $_SERVER,
                $_GET,
                $_POST,
                $_FILES,
                $_COOKIE
            ); 
        } else {
            $this->request = new \Http\Request(
                $this->config('server'),
                $this->config('get'),
                $this->config('post'),
                $this->config('files'),
                $this->config('cookie')
            );
        }
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

    public function setRequest($req) {
        $this->request = $req;

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
