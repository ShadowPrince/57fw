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
        if ($e->appExists('http')) {
            $this->addGlobal('req', $e->http->getRequest());
        } 
        if ($e->appExists('router')) {
            $fn = new \Twig_SimpleFunction(
                'mkurl', 
                function () use ($e) {
                    return call_user_func_array(
                        array($e->router, 'make'),
                        func_get_args()
                    );
                }
            );
            $this->addFunction(
                $fn
            );
        }
    }

    public function __call($func, $args) {
        $res = call_user_func_array(array($this->env, $func), $args);
        if (!$res)
            return $this;
        else 
            return $res;
    }
}
