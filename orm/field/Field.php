<?php
namespace Orm\Field;

class Field {
    public function __construct($params=null) {
        $this->value = 0;
        $this->applyParams();
    }

    public function applyParams() {
        if ($params) foreach ($params as $k => $v) {
            $this->$k = $v;
        }
    }

    public function getValue() {return $this->value;}
    public function setValue($val) {$this->value = $val;}
}
