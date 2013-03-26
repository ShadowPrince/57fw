<?php
namespace Routing;

/**
 * Router service
 * @TODO: make url
 */
class Router extends \Core\Service {
    protected $routings = array();
    protected $request;
    protected $response;

    public function __construct() {
        $this->config = array(
            'add_trailing_slash' => false
        );

        call_user_func_array('parent::__construct', func_get_args());
    }

    /**
     * Make url from bind name 
     *
     * First param - bind, others - vars to regex.
     * Last param (?) - array of named params.
     * If array of named params not provided all args parses to "?name=value",
     * to let pass named vars trough twig
     *
     * @param string
     * @param ...
     * @return string
     */
    public function make() {
        $args = func_get_args();
        $bind = array_shift($args);
        // get named args (must be last arg)
        if (is_array(end($args))) {
            $named = array_pop($args);
        } else {
            $named = array();
            // parse inline named syntax
            foreach ($args as $k=>$arg) {
                // is there inline named syntax?
                if (preg_match_all('#^\?(.+)\=(.+)$#i', $arg, $m)) {
                    // push it to named params
                    $named[reset($m[1])] = reset($m[2]);
                    // and remove it from ordinary 
                    unset($args[$k]);
                }
            }
        }

        if (isset($this->routings[$bind])) {
            // route regex
            $regex = $this->routings[$bind]->getRegex();
        } else {
            throw new Ex\RouteNotExistsException($bind);
        }
        // get all regex vars
        preg_match_all('#\([^)]+\)#', $regex, $matches);
        $regex_vars = $matches[0];

        $replace = array();

        if ($regex_vars) foreach ($regex_vars as $var) {
            // is variable named?
            if (false !== strpos($var, '?P')) {
                // get his name into $m[1][0]
                preg_match_all('#\(\?P\<(\w+)\>#', $var, $m);
                $name = $m[1][0];
                // is there param with this name?
                if (isset($named[$name])) {
                    // yeah, get named param into replace array
                    $replace[] = $named[$name];
                } else {
                    // get ordinary param into replace array
                    $replace[] = array_shift($args);
                }
            } else {
                // get ordinary param into replace array
                $replace[] = array_shift($args);
            }
        }

        // drop regex's ## and ^$
        $regex = preg_replace('#^\#[\^]*(.*?)[\$]*\#(\w*)$#', '$1', $regex);

        // replace regex vars with params in replace array
        return str_replace(
            $regex_vars,
            $replace,
            '/index.php' . $regex
        );
    }

    /**
     * Get new Route instance
     * @param string
     * @param callable
     * @return \Routing\Route
     */

    public function register() {
        $route = (new \ReflectionClass('\Routing\Route'))
            ->newInstanceArgs(
                array_merge(array($this), func_get_args())
            );

        return $route;
    }

    /**
     * Register binded instance
     * @param string
     * @param \Routing\Route
     */
    public function registerInstance($instance) {
        if (!isset($this->routings[$instance->getBind()]))
            $this->routings[$instance->getBind()] = $instance;
        else
            throw new Ex\RouteExistsException($instance);
    }

    /**
     * Find instance by $url and return callback with args
     * @param string
     * @return array
     */
    public function find($url) {
        $routings = array();
        if ($this->routings) foreach ($this->routings as $route) {
            if (preg_match($route->getRegex(), $url, $matches)) {
                array_shift($matches);
                $routings[] = array(
                    $route,
                    (strpos($route->getRegex(), '?P')), // is there named params
                    $matches // all params 
                );
            }
        }
        return $routings;
    }

    /**
     * Call instance (callback with args)
     * @param mixed
     * @param array
     * @param array
     * @return mixed
     */
    public function call($req, $route, $args, $named) {
        $fargs = array(
            $req
        );
        if ($named)
            $fargs = array_merge($fargs, array($args));
        else
            $fargs = array_merge($fargs, $args);

        return call_user_func_array(
            array($route, 'getCallback'), 
            $fargs
        );
    }

    /**
     * engage router for $url
     * @param string
     * @return mixed
     */
    public function engage($req, $url) {
        $matches = $this->find($url);
        if ($matches) foreach ($matches as $data) {
            $res = \Http\Http::createResponse(
                $this->call($req, $data[0], $data[2], $data[1])
            );
            if ($res)
                return $res;
        } else {
            $url_arr = str_split($url);
            if (array_pop($url_arr) != '/' && $this->config('add_trailing_slash'))
                return $this->engage($req, $url . '/');
            else
                throw new Ex\RouteNotFoundException($url);
        }
    }

}
