<?php
namespace Routing;

class Router extends \Core\Service {
    protected $routings = array();

    /**
     * Register callback
     * @param string
     * @param mixed 
     */
    public function register($regex, $instance, $full_regex=false) {
        $this->routings[$regex] = array(
            'instance' => $instance, 
            'full_regex' => $full_regex
        );

        return $this;
    }

    /**
     * Find instance by $url and return callback with args
     * @param string
     * @return array
     */
    public function find($url) {
        if ($this->routings) foreach ($this->routings as $regex => $ins) {
            if (!$ins['full_regex']) {
                $regex = '#^' . $regex . '$#i';
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
    public function callInstance($ins, $args, $named) {
        $fargs = array(
            new \Http\Request($_GET, $_POST, $_FILES)
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
    public function engage($url) {
        $data = $this->find($url);
        if ($data) {
            if ($data[1])
                return $this->callInstance($data[0], $data[2], $data[1]);
            else
                return $this->callInstance($data[0], $data[2], $data[1]);
        } else {
            $url_arr = str_split($url);
            if (array_pop($url_arr) != '/' && $this->getConfig('add_trailing_slash'))
                return $this->engage($url . '/');
            else
                throw new \Routing\Ex\RouteNotFoundException($url);
        };
    }

}
