<?php
namespace Http;

class Request {
    public function __construct($get, $post, $files, $cookies) {
        $this->get = $get;
        $this->post = $post;
        $this->files = $files;
        $this->cookies = $cookies;
    }

    public function post($k=false) {
        if (!$k)
            return $this->post;
        else if (isset($this->post[$k]))
            return $this->post[$k];
    }

    public function get($k=false) {
        if (!$k)
            return $this->get;
        else if (isset($this->get[$k]))
            return $this->get[$k];
    }

    public function cookie($k) {
        if (isset($this->cookies[$k]))
            return $this->cookies[$k];
        else
            return null;
    }
}
