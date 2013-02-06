<?php
namespace Orm;

class Model {
    public function __construct() {
        $this->init();
        $this->standartNames();
    }

    public function init() {}

    public function standartNames() {
        $vars = get_object_vars($this);
        if ($vars) foreach ($vars as $k => $var) {
            if ($var instanceof Field\Field) {
                if ($var->name == null)
                    $var->name = $k;
            }
        }
    }

    public function getPrimaryKey() {
        if ($this->id) {
            return $this->id;
        } else {
            $vars = get_object_vars($this);
            if ($vars) foreach ($vars as $k => $var) {
                if ($var instanceof Field\PrimaryKey) {
                    return $var;
                }
            }
            throw new \Exception('There is no primary key!');
        } 
    }

    public function save() {
    }
}
