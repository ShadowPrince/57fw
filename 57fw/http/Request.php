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
     */
    public function __construct($get, $post, $files, $cookies) {
        $this->get = $get;
        $this->post = $post;
        $this->files = $files;
        $this->cookies = $cookies;
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
}
