<?php
namespace Orm;

abstract class Model {
    public function __construct() {
        $this->init();
        $this->standartNames();
    }

    public abstract function init();

    public function standartNames() {
        $vars = get_object_vars($this);
        if ($vars) foreach ($vars as $k => $var) {
            if ($var instanceof Field\Field) {
                if ($var->name == null)
                    $var->name = $k;
            }
        }
        $class = explode('\\', get_class($this));
        $class = array_pop($class);

        $this->table = strtolower($class);
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

    public function getFields() {
        $fields = [];
        $vars = get_object_vars($this);
        if ($vars) foreach ($vars as $k => $var) {
            if ($var instanceof Field\Field) {
                $fields[] = $var;
            }
        }

        return $fields;
    }

    public function save() {
    }
}
