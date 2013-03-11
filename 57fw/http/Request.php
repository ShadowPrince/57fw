<?php
namespace Http;

class Request {
    public function __construct($get, $post, $files) {
        $this->get = $get;
        $this->post = $post;
        $this->files = $files;
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
}
