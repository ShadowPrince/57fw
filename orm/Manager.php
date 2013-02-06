<?php
namespace Orm;

class Manager {
    public function __construct($model) {
        $this->modelClass = $model;
        $this->model = new $model();
        $this->init();
    } 

    public function init() {}

    public function get($val) {
        $pkey = $this->model->getPrimaryKey();
        $raw = $this->backend->select($this, array($pkey->name => $val));
        $instance = new $this->modelClass(); 
        foreach ($raw as $k => $v) {
            if ($instance->$k instanceof \Orm\Field\Field) {
                $instance->$k->setValue($v);
            }  
        } 
        return $instance;
        
    }
}
