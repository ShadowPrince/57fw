<?php
namespace Core;

/**
 * Main engine class
 */
class Engine extends \Core\ConfiguredInstance {
    protected $config;
    protected $apps;
    protected $services;
    public $cache;

    /**
     * Getter for apps
     * @param string
     * @return mixed
     */
    public function __get($name) {
        if (isset($this->apps[$name]))
            return $this->apps[$name];
        else
            throw new \Core\Ex\AppNotFoundException($name);
    }

    /**
     * Magick method for services
     * @param string
     * @param array
     * @return mixed
     */
    public function __call($func, $args) {
        return call_user_func_array($this->apps[$func], array_merge(array($this), $args));
    }

    /**
     * Engage engine
     * @return string
     */
    public function engage() {
        $responses = array();
        if ($this->apps) foreach ($this->apps as $name => $instance) {
            if ($instance instanceof \Core\AppDispatcher) {
                $responses[] = $instance->engage($this); 
            }
        } 

        return implode($responses, '');
    }

    /**
     * Register app
     * @param string
     * @param \Core\AppDispatcher
     * @return \Core\Engine
     */
    public function register($name, $app) {
        $this->apps[$name] = $app;

        return $this;
    }

    public function registerArray($apps) {
        if ($apps) foreach ($apps as $k => $app) {
            $this->register($k, $app);
        }

        return $this;
    }

    /**
     * Get all apps
     * @param string
     * @return array
     */
    public function getApps() {
        return $this->apps;
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
