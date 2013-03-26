<?php
namespace Core;

/**
 * Main engine class
 */
class Engine extends ConfiguredInstance {
    protected $apps = array();
    protected $services = array();
    protected $handlers = array();
    public $cache;

    public function __construct($config=array()) {
        $this->config = array(
            'debug' => false
        );
        parent::__construct($config);
    }

    /**
     * Getter for apps
     * @param string
     * @return mixed
     */
    public function __get($name) {
        if (isset($this->apps[$name]))
            return $this->apps[$name];
        else
            throw new Ex\AppNotFoundException($name);
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

    public function engageApp($app) {
        return $app->engage($this);
    }

    /**
     * Engage engine
     * @return string
     */
    public function engage() {
        $responses = array();
        if ($this->apps) foreach ($this->apps as $name => $instance) {
            $callback = null;
            if ($instance instanceof AppDispatcher) {
                $callback = array($instance, 'engage');
            } else if (is_callable($instance)) {
                if (is_string($instance) || is_array($instance)) {
                    $f = new \ReflectionMethod($instance);
                } else if ($instance instanceof \Closure) {
                    $f = new \ReflectionFunction($instance);
                }

                if (
                    count($f->getParameters()) == 1
                    &&
                    $f->getParameters()[0]->getName() == 'e'
                ) {
                    $callback = $instance($this);
                }
            }

            if ($callback) {
                try {
                    $responses[] = call_user_func($callback, $this); 
                } catch (\Exception $ex) {
                    $this->engageHandlers($ex);
                }
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
     * @param string
     * @return bool
     */
    public function appExists($name) {
        return isset($this->apps[$name]);
    }

    /**
     * Get all apps
     * @param string
     * @return array
     */
    public function getApps() {
        return $this->apps;
    }

    public function engageHandlers($ex) {
        if ($this->handlers) foreach ($this->handlers as $handler) {
            $res = $handler($ex);
            if (is_object($res))
                throw $res;
        } else throw $ex;
    }

    public function handle($fn) {
        $this->handlers = array_merge(array($fn), $this->handlers);

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
