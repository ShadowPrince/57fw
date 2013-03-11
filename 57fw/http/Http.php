<?php
namespace Http;

class Http extends \Core\Service {
    public function getFullURL() {
        return $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    }

    public function getRequestPath() {
        if (isset($_SERVER['PATH_INFO']))
            return $_SERVER['PATH_INFO'];
    }
}
