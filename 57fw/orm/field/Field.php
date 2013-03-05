<?php
namespace Orm\Field;

abstract class Field {
    protected $type, $val;
    protected $params = array();

    public function __construct($params=null) {
        $this->params = $params;
    }

    public function param($k) {
        if (isset($this->params[$k]))
            return $this->params[$k];
        else return false;
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
     * @param mixed 
     * @return \Orm\Field\Field
     */
    public function setValue($val) {$this->value = $val; return $this;}

    /**
     * set field value with no validation (useful when setting values from db)
     * @param mixed 
     * @return \Orm\Field\Field
     */
    public function forceValue($val) {$this->value = $val; return $this;}
}
