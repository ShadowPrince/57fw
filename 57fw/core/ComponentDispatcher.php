<?php
namespace Core;

/**
 * Dispatcher to work with component
 */
class ComponentDispatcher extends AppDispatcher {
    protected $namespace;
    protected $component;
    protected $models;

    /**
     * @param string
     * @param array
     */
    public function __construct($component, $config=array()) {
        $this->component = new $component($config);
        $this->config = $this->component->getConfig();

        $ns = explode('\\', $component);
        array_pop($ns);
        $this->namespace = implode('\\', $ns);

        parent::__construct($this->config);
    }

    public function __call($fn, $args) {
        return call_user_func_array(array($this->component, $fn), $args);
    }

    public function config($k=null) {
        return call_user_func_array(array($this->component, 'config'), func_get_args());
    }

    /**
     * Prepare database for app
     * @param \Core\Engine
     * @param array
     * @param callback 
     */
    public function prepareDatabase($e, $opts, $print_callback) {
        $this->models = $this->getModels();
        foreach ($this->models as $model) {
            $m = $e->man($model);
            $m->prepare($opts, $print_callback);
        }
    }

    /**
     * Engage component
     * @param \Core\Engine
     */
    public function engage($e) {
        $this->component->engage($e);
    }
   
    /**
     * Get namespace of component
     * @return string
     */
    public function getNamespace() {
        return $this->namespace;
    }

    /**
     * Get models
     * @return array
     */
    public function getModels() {
        return $this->getClasses($this->namespace . '\\Model');
    }

    /**
     * Get all classes at namespace
     * @param string
     * @return array
     */
    protected function getClasses($path) {
        $realpath = strtolower(str_replace('\\', '/', $path));
        if (substr($realpath, 0, 1) == '/')
            $realpath = substr($realpath, 1); 

        $classes = array();

        if (!is_dir($realpath))
            return array();

        $dir = opendir($realpath); 
        if (!$dir)
            return array();

        while (false !== ($file = readdir($dir))) {
            $info = pathinfo($realpath . '/' . $file);
            if ($info['extension'] == 'php') {
                $classes[] = $path . '\\' . $info['filename'];
            }
        }

        return $classes;
    }
}
