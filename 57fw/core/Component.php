<?php
namespace Core;

abstract class Component {
    protected $config; 


    public function __construct($config=array()) {
        $this->config = $config;
    }

    /**
     * @param \Core\Engine
     */
    public abstract function engage($e);

    /**
     * @param mixed
     * @return mixed
     */
    public function getConfig($k) {
        return $this->config[$k];
    }
    /*
     * @param \Core\Engine
     */
}
