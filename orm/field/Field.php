<?php
namespace Orm\Field;

abstract class Field {
    public function __construct($params=null) {
        $this->init();
        $this->applyParams();
    }

    public abstract function init();

    public function applyParams() {
        if ($params) foreach ($params as $k => $v) {
            $this->$k = $v;
        }
    }

    public function getType() {return $this->type;}
    public function getName() {return $this->name;}
    public function getValue() {return $this->value;}
    public function setValue($val) {$this->value = $val;}
}
