<?php
namespace Routing\Ex;

class RouteNotFoundException extends \Routing\Ex\RoutingException {
    public function __construct($url) {
        parent::__construct('Route for ' . var_str($url) . ' not found!');
    }
}
