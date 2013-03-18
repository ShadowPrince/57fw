<?php
namespace Twig;

class Twig extends \Core\AppDispatcher {
    public function __construct($config=array()) {
        parent::__construct($config);

        if ($this->config('loader') == 'string') {
            $this->loader = new \Twig_Loader_String();
        } else {
            $this->loader = new \Twig_Loader_Filesystem($this->config('path'));
        }
        $this->env = new \Twig_Environment($this->loader, $config);

    }

    public function engage($e) {
        if ($e->http) {
            $this->addGlobal('req', $e->http->getRequest());
        }
    }

    public function __call($func, $args) {
        if (method_exists($this, $func)) {
            return call_user_func_array(array($this, $func), $args);
        } else {
            $res = call_user_func_array(array($this->env, $func), $args);
            if (!$res)
                return $this;
            else
                return $res;
        }
    }
}
