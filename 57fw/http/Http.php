<?php
namespace Http;

/**
 * Service for HTTP workflow
 */
class Http extends \Core\Service {
    protected $response, $request, $abort;

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

    public function abort($res) {
        $this->abort = $res;

        return $this;
    }

    /**
     * Create response from mixed input
     * @param mixed
     * @return \Http\Response
     */
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

    public function getAbort() {
        return $this->abort;
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
        if ($this->getAbort())
            $res = $this->getAbort();
        else
            $res = $this->getResponse();

        if (!$res)
            throw new Ex\NoResponseProvidedException();

        if ($res->getCookies())
            foreach ($res->getCookies() as $args) {
                call_user_func_array('setcookie', $args);
            }

        if ($res->getHeaders())
            foreach ($res->getHeaders() as $header) {
                header($header);
            }

        return $res->getBody();
    }
}
