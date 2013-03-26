<?php
namespace Routing\Ex;

class RouteNotExistsException extends RoutingException {
    public function __construct($bind) {
        parent::__construct("Route binded to " . var_str($bind) . " not exists!");
    }
}
