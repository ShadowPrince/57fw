<?php
namespace Core;

class ConfiguredInstance {
    protected $config;

    public function __construct($config=array()) {
        $this->config = $config;
    }
    
    /**
     * @param mixed
     * @return mixed
     */
    public function config($k) {
        if (isset($this->config[$k]))
            return $this->config[$k];
    }
}
