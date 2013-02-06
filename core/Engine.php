<?php
namespace Core;

class Engine {
    public $config;
    public function __construct($config) {
        $this->config = $config;
        $this->apps = $config->apps;
    }

    public function __call($func, $args) {
        return $this->service($func);
    }

    public function proceedLevel($lvl) {
        $responses = array();
        if (count($this->apps[$lvl])) 
            foreach ($this->apps[$lvl] as $classname) {
                $responses[] = (new $classname())->proceed($this); 
            } 

        return implode($responses, '');
    }

    public function register($name, $instance) {$this->services[$name] = $instance;}
    public function service($name) {return $this->services[$name];}
    public function getConfig() {return $this->config;}
}
