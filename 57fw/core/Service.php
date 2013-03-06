<?php
namespace Core;

abstract class Service {
    protected $e;
    
    public function setEngine($e) {
        $this->e = $e;

        return $this;
    }
}
