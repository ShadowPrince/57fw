<?php
namespace Routing\Ex;

class RouteRegistrationException extends \Routing\Ex\RoutingException {
    public function __construct($cause) {
        parent::__construct('Route registration: ' . $cause);
    }
}
