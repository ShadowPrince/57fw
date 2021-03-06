<?php
namespace Http;

/**
 * HTTP request class
 */
class Request {
    /**
     * @param array
     * @param array
     * @param array
     * @param array
     * @param array
     */
    public function __construct($server=array(), $get=array(), $post=array(), $files=array(), $cookies=array()) {
        $this->get = $get;
        $this->post = $post;
        $this->files = $files;
        $this->cookies = $cookies;
        $this->server = $server;
    }

    /**
     * @param mixed
     * @return mixed
     */
    public function post($k=false) {
        if (!$k)
            return $this->post;
        else if (isset($this->post[$k]))
            return $this->post[$k];
    }

    /**
     * @param mixed
     * @return mixed
     */
    public function get($k=false) {
        if (!$k)
            return $this->get;
        else if (isset($this->get[$k]))
            return $this->get[$k];
    }

    /**
     * @param mixed
     * @return mixed
     */
    public function cookie($k=false) {
        if (!$k)
            return $this->cookies;
        else if (isset($this->cookies[$k]))
            return $this->cookies[$k];
    }
    
    /**
     * @param mixed
     * @return array
     */
    public function file($k=false) {
        if (!$k)
            return $this->files;
        else if (isset($this->files[$k]))
            return $this->files[$k];
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
        else
            return '/';
    }

    /**
     * @return string
     */
    public function getDomain() {
        return $this->server['SERVER_NAME'];
    }

    /**
     * @return string
     */
    public function getRemoteAddr() {
        return $this->server['REMOTE_ADDR'];
    }

}
