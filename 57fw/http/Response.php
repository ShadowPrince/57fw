<?php
namespace Http;

class Response {
    protected $headers = array();
    protected $body = '';
    protected $cookies = array();

    public function __construct($body) {
        $this->body = $body;
    }

    public function getBody() {
        return $this->body;
    }

    public function setBody($body) {
        $this->body = $body;
        return $this;
    }

    public function addHeader($header) {
        if (is_array($header))
            return $this->addHeaders($header);
        $this->headers[] = $header;
        return $this;
    }

    public function addHeaders($headers) {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    public function getHeaders() {
        return $this->headers;
    }

    public function setCookie() {
        $this->cookies[] = func_get_args();
        return $this;
    }

    public function getCookies() {
        return $this->cookies;
    } 

}
