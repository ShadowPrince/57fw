<?php
namespace Http;

/**
 * HTTP response class
 */
class Response {
    protected $headers = array();
    protected $body = '';
    protected $cookies = array();

    /**
     * @param string
     */
    public function __construct($body) {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getBody() {
        return $this->body;
    }

    /**
     * @param string
     * @return \Http\Response
     */
    public function setBody($body) {
        $this->body = $body;
        return $this;
    }

    /**
     * @param string
     * @return \Http\Response
     */
    public function addHeader($header) {
        if (is_array($header))
            return $this->addHeaders($header);
        $this->headers[] = $header;
        return $this;
    }

    /**
     * @param array
     * @return \Http\Response
     */
    public function addHeaders($headers) {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders() {
        return $this->headers;
    }

    /**
     * @return \Http\Response
     */
    public function setCookie() {
        $this->cookies[] = func_get_args();
        return $this;
    }

    /**
     * @return array
     */
    public function getCookies() {
        return $this->cookies;
    } 

}
