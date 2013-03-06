<?php
namespace Core\Ex;

class ServiceUnavaibleException extends Exception {
    public function __construct($service, $need_that) {
        parent::__construct('Service "' . $service . '" require service "' . $need_that . '"');
    }
}
