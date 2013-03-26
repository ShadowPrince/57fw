<?php
namespace Routing;

class Route {
    public static $counter = 0;

    protected $router;
    protected $regex;
    protected $full_regex;
    protected $callback;
    protected $prefix;
    protected $bind;
    protected $validators = array();

    /**
     * @param \Routing\Router
     * @param string
     * @param callable
     * @param \Core\Component
     */
    public function __construct($router, $regex, $callback, $app=null) {
        $this->router = $router;

        $this->regex = $regex;
        $this->callback = $callback;

        $this->defaultBind($callback);

        if ($app) {
            if ($app->config('url_prefix'))
                $this->prefix = $app->config('url_prefix');
            if ($app->config('validators'))
                $this->validators = $app->config('validators');
        }
    }

    /**
     * Register route in router
     * @return \Routing\Router
     */
    public function register() {
        $this->router->registerInstance($this);

        return $this;
    }

    /**
     * Bind route to default bind
     * @param callable 
     * @return \Routing\Route
     */
    public function defaultBind($cb) {
        if (is_array($cb)) {
            $bind = array_pop($cb);
            $cls = get_class(array_pop($cb));
            $cls = explode('\\', $cls);
            $cls = strtolower(array_pop($cls));
            $this->bind($cls . "." . $bind);
        } else if (is_string($cb)) {
            $this->bind($cb);
        } else {
            self::$counter++;
            $this->bind("unnamed_route_" . dechex(self::$counter));
        }

        return $this;
    }

    /**
     * Bind route and register it
     * @param string
     * @return \Routing\Route
     */
    public function bind($bind) {
        $this->bind = $bind;
        $this->register();

        return $this;
    }

    /**
     * Set full regex used
     * @return \Routing\Route
     */
    public function fullRegex() {
        $this->full_regex = true;

        return $this;
    }

    /**
     * Apply validator to route
     * @param callable
     * @return \Routing\Route
     */
    public function validate($callback) {
        $this->validators[] = $callback;

        return $this;
    }

    /**
     * Get raw regex
     * @return string
     */
    public function getRawRegex() {
        return $this->regex;
    }

    /**
     * Make and return regex
     * @return string
     */
    public function getRegex() {
        if ($this->isFullRegex())
            return $this->regex;

        if ($this->getPrefix())
            $prefix = $this->getPrefix();
        else
            $prefix = '';

        return '#^' . $prefix . $this->regex . '$#i';
    }

    /**
     * Return result of callback (or validator)
     * @return mixed
     */
    public function getCallback() {
        foreach ($this->validators as $callback) {
            $res = call_user_func_array($callback, func_get_args());
            if ($res !== null) {
                return $res;
            }
        }

        return call_user_func_array($this->callback, func_get_args());
    }

    /**
     * @return string
     */
    public function getBind() {
        return $this->bind;
    }

    /**
     * @return string
     */
    public function getPrefix() {
        return $this->prefix;
    }

    /**
     * @return boolean
     */
    public function isFullRegex() {
        return $this->full_regex;
    }
}
