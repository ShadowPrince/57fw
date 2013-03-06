<?php
namespace Core;

class Engine {
    protected $config;
    protected $apps;
    protected $services;
    public $cache;

    public function __construct() {
    }

    /**
     * Magick method for services
     * @param string
     * @param array
     * @return mixed
     */
    public function __call($func, $args) {
        if (is_callable($this->services[$func]))
            return call_user_func_array($this->services[$func], $args);
        else {
            if ($this->services[$func] instanceof \Core\Service) {
                $this->services[$func]->setEngine($this);
            }
            return $this->services[$func];
        }
    }

    /**
     * Engage engine
     * @return string
     */
    public function engage() {
        $responses = array();
        if ($this->apps) foreach ($this->apps as $classname) {
            $responses[] = $classname->engage($this); 
        } 

        return implode($responses, '');
    }

    /**
     * Register app
     * @param string
     * @param \Core\EngineDispatcher
     * @return \Core\Engine
     */
    public function register($name, $dispatcher) {
        $this->apps[$name] = $dispatcher;

        return $this;
    }

    /**
     * Get all app dispatchers
     * @param string
     * @return array
     */
    public function getApps() {
        return $this->apps;
    }

    /**
     * Register service
     * @param string
     * @param mixed
     */
    public function service($name, $instance) {
        $this->services[$name] = $instance;
        return $this;
    }

    /**
     * Fatal error handler 
     */
    public static function fatalErrorHandler($no, $str, $file, $line) {
        if ($no === E_RECOVERABLE_ERROR) {
            throw new \ErrorException($str, $no, 0, $file, $line);
        }
        return false;
    }
}

// get some fatal errors!
set_error_handler('\Core\Engine::fatalErrorHandler');
