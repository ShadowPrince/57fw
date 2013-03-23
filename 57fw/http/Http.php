<?php
namespace Http;

/**
 * Service for HTTP workflow
 * @TODO: abort response
 * @TODO: redirect
 */
class Http extends \Core\Service {
    protected $response, $request;

    public function __construct($config=array()) {
        $this->config = array(
            'server' => $_SERVER,
            'get' => $_GET,
            'post' => $_POST,
            'files' => $_FILES,
            'cookie' => $_COOKIE
        );
        parent::__construct($config);

        $this->request = new \Http\Request(
            $this->config('server'),
            $this->config('get'),
            $this->config('post'),
            $this->config('files'),
            $this->config('cookie')
        );
    }

    public static function createResponse($res) {
        if ($res instanceof Response)
            return $res;
        else if (is_string($res))
            return new Response($res);
        else return new JSONResponse($res);
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
        if (!$this->getResponse()) 
            throw new Ex\NoResponseProvidedException();

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
