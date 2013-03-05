<?php
namespace Orm\Field;

abstract class Field {
    public function __construct($params=null) {
        $this->applyParams();
    }

    public function applyParams() {
        if ($params) foreach ($params as $k => $v) {
            $this->$k = $v;
        }
    }

    /**
     * @return string
     */
    public function getType() {return $this->type;}
    /**
     * @return string
     */
    public function getName() {return $this->name;}
    /**
     * @return string
     */
    public function getValue() {return $this->value;}
    /**
     * @param string
     * @return \Orm\Field\Field
     */
    public function setValue($val) {$this->value = $val; return $this;}
}
