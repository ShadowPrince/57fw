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

    public function prepareDatabase($e, $opts, $print_callback) {
        $this->models = $this->getClasses($this->namespace . '\\Model');
        foreach ($this->models as $model) {
            $m = $e->man($model);
            $m->prepare($opts, $print_callback);
        }
    }

    public function proceed($e) {
        (new $this->urls())->init($e);
    }
   
    protected function getClasses($path) {
        $realpath = strtolower(str_replace('\\', '/', $path));
        if (substr($realpath, 0, 1) == '/')
            $realpath = substr($realpath, 1); 
        $classes = [];
        $dir = opendir($realpath);
        if (!$dir)
            return [];
        while (false !== ($file = readdir($dir))) {
            $info = pathinfo($realpath . '/' . $file);
            if ($info['extension'] == 'php') {
                $classes[] = $path . '\\' . $info['filename'];
            }
        }

        return $classes;
    }
}
