<?php
namespace Admin;

class Admin extends \Core\Component {
    protected $e;
    protected $models = array();

    public function register($instance) {
        if ($instance instanceof \Core\ComponentDispatcher) {
            if (!isset($this->models[$instance->getName()]))
                $this->models[$instance->getName()] = array();

            foreach ($instance->getModels() as $v) {
                $this->models[$instance->getName()][] = $v;
            }
        }
    }

    public function engage($e) {
        $this->e = $e;
    }
}
