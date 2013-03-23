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
     * Register callback
     * @param string
     * @param mixed 
     * @param mixed
     * @param bool
     * @return \Routing\Router
     */
    public function register($regex, $bind, $instance, $component=null, $full_regex=false) {
        $this->routings[$regex] = array(
            'instance' => $instance, 
            'full_regex' => $full_regex,
            'url_prefix' => $component instanceof \Core\Component ? $component->config('url_prefix') : '' 
        );

        return $this;
    }

    /**
     * Register array of routings
     *
     * @param array
     * @return \Routing\Router
     */
    public function registerArray($array) {
        foreach ($array as $k => $v) {
            $this->register(
                $v['regex'],
                $k,
                $v['instance'],
                isset($v['component']) ? $v['component'] : null,
                isset($v['full_regex']) ? $v['full_regex'] : null
            );
        } 

        return $this;
    }

    /**
     * Find instance by $url and return callback with args
     * @param string
     * @return array
     */
    public function find($url) {
        if ($this->routings) foreach ($this->routings as $regex => $ins) {
            if ($ins['url_prefix']) {
                if ($ins['full_regex']) {
                    throw new Ex\RouteRegistrationException(
                        'Route cant provide full_regex and url_prefix at the same time!'
                    ); 
                } else {
                    $regex = '#^' . $ins['url_prefix'] . $regex . '$#';
                }
            } else if (!$ins['full_regex']) {
                $regex = '#^' . $regex . '$#';
            }

            if (preg_match($regex, $url, $matches)) {
                array_shift($matches);
                return array(
                   $ins['instance'], // instance
                   (strpos($regex, '?P')), // is there named params
                   $matches // all params 
                );
            }
        }
    }

    /**
     * Call instance (callback with args)
     * @param mixed
     * @param array
     * @param array
     * @return mixed
     */
    public function callInstance($req, $ins, $args, $named) {
        $fargs = array(
            $req
        );
        if ($named)
            $fargs = array_merge($fargs, array($args));
        else
            $fargs = array_merge($fargs, $args);

        return call_user_func_array($ins, $fargs);
    }

    /**
     * engage router for $url
     * @param string
     * @return mixed
     */
    public function engage($req, $url) {
        $data = $this->find($url);
        if ($data) {
            return \Http\Http::createResponse(
                $this->callInstance($req, $data[0], $data[2], $data[1])
            );
        } else {
            $url_arr = str_split($url);
            if (array_pop($url_arr) != '/' && $this->config('add_trailing_slash'))
                return $this->engage($req, $url . '/');
            else
                throw new Ex\RouteNotFoundException($url);
        };
    }

}
