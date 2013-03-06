<?php
namespace Routing\Ex;

class RouteNotFoundException extends \Exception {
    public function __construct($url) {
        parent::__construct('Route for "' . $url . '" not found!');
    }
}
