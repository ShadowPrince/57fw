<?php
namespace Core;

class AppDispatcher implements \Core\EngineDispatcher {

    public function __construct($appNamespace) {
        $this->namespace = $appNamespace;    
        $this->models = $this->getClasses($this->namespace . '\\Model');
        $this->urls = $this->namespace . '\\Urls';
    }

    public function getClasses($path) {
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

    public function prepareDatabase($e) {
        foreach ($this->models as $model) {
            $m = $e->manager($model);
            $m->prepare();
        }
    }

    public function proceed($e) {
        (new $this->urls())->register($e);
    }
}
