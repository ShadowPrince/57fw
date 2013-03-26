<?php
namespace Routing\Ex;

class RouteExistsException extends RoutingException {
    public function __construct($route) {
        parent::__construct("Route binded to " . var_str($route->getBind()) . " exists");
    }
}
