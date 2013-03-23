<?php
namespace Core\Ex;

class AppNotFoundException extends EngineException {
    public function __construct($app) {
        parent::__construct('App "' . $app . '" not found!');
    }
}
