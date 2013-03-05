<?php
namespace Routing;

class Router {
    private $e;
    private $routings;
    public function __construct($e) {
        $this->e = $e; 
    }

    /**
     * Register callback
     * @var string
     * @var mixed 
     */
    public function register($regex, $instance) {
        $this->routings[$regex] = $instance;
    }

    /**
     * Find instance by $url and return callback with args
     * @var string
     * @return array
     */
    public function find($url) {
        if ($this->routings) foreach ($this->routings as $regex => $ins) {
            if (preg_match($regex, $url, $matches)) {
                array_shift($matches);
                return array(
                   $ins,
                   (strpos($regex, '?P')),
                   array_unique(array_reverse($matches)),
                   array_unique($matches)
                );
            }
        }
    }

    /**
     * Call instance (callback with args)
     * @var mixed
     * @var array
     * @var array
     * @return mixed
     */
    public function callInstance($ins, $args, $named) {
        if ($named)
            return call_user_func_array($ins, array($this->e, $args));
        else
            return call_user_func_array(
                $ins,
                array_merge(array($this->e), $args)
            );
    }

    /**
     * Proceed router for $url
     * @var string
     * @return mixed
     */
    public function proceed($url) {
        $data = $this->find($url);
        if ($data)
            return $this->callInstance($data[0], $data[2], $data[1]);
        else
            return '404';
    }
}
