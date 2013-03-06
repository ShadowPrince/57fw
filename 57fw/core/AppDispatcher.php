<?php
namespace Core;

class AppDispatcher implements \Core\EngineDispatcher {
    protected $namespace;
    protected $urls;
    protected $models;

    public function __construct($appNamespace) {
        $this->namespace = $appNamespace;    
        $this->urls = $this->namespace . '\\Urls';
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
     * Proceed app
     * @param \Core\Engine
     */
    public function engage($e) {
        (new $this->urls())->engage($e);
    }
   
    /**
     * Get name of app
     * @return string
     */
    public function getName() {
        return str_replace('\App\\', '', $this->namespace);
    }

    /**
     * get all classes at namespace
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
