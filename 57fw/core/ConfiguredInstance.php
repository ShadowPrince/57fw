<?php
namespace Core;

/**
 * Handles configuration of apps
 */
class ConfiguredInstance {
    protected $config = array();

    /**
     * @param array
     */
    public function __construct($config=array()) {
        $this->config = array_merge($this->config, $config);
    }
    
    /**
     * Get config value or null
     * @param mixed
     * @return mixed
     */
    public function config($k=null) {
        if (!$k)
            return $this->config;
        if (isset($this->config[$k]))
            return $this->config[$k];
        return null;
    }

    public function setParam($k, $v) {
        $this->config[$k] = $v;
    }
}
