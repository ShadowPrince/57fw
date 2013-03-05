<?php
namespace Core;

class Engine {
    public $config;
    public function __construct($config) {
        $this->config = $config;
        $this->apps = $config->apps();
    }

    public function __call($func, $args) {
        if (is_callable($this->service($func)))
            return call_user_func_array($this->service($func), $args);
        else
            return $this->service($func);
    }

    public function proceed() {
        $lvls = array(EL_PRE, EL_REQUEST, EL_RESPONSE);
        foreach ($lvls as $lvl)
            echo $this->proceedLevel($lvl);
    }

    public function proceedLevel($lvl) {
        $responses = array();
        if (count($this->apps[$lvl])) 
            foreach ($this->apps[$lvl] as $classname) {
                $responses[] = $classname->proceed($this); 
            } 

        return implode($responses, '');
    }

    public function getApps($lvl) {
        return $this->apps[$lvl];
    }

    public function register($name, $instance) {$this->services[$name] = $instance;}
    public function service($name) {return $this->services[$name];}
    public function getConfig() {return $this->config;}
}
