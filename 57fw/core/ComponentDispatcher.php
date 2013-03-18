<?php
namespace Core;

/**
 * Dispatcher to work with component
 */
class ComponentDispatcher extends \Core\AppDispatcher {
    protected $namespace;
    protected $component;
    protected $models;

    /**
     * @param string
     * @param array
     */
    public function __construct($component, $config=array()) {
        $this->component = $component;

        $ns = explode('\\', $component);
        array_pop($ns);
        $this->namespace = implode('\\', $ns);

        parent::__construct($config);
    }

    /**
     * Prepare database for app
     * @param \Core\Engine
     * @param array
     * @param callback 
     */
    public function prepareDatabase($e, $opts, $print_callback) {
        $this->models = $this->getClasses($this->namespace . '\\Model');
        foreach ($this->models as $model) {
            $m = $e->man($model);
            $m->prepare($opts, $print_callback);
        }
    }

    /**
     * Proceed component
     * @param \Core\Engine
     */
    public function engage($e) {
        (new $this->component($this->config))->engage($e);
    }
   
    /**
     * Get name of component
     * @return string
     */
    public function getName() {
        return $this->namespace;
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
