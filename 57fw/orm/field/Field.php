<?php
namespace Orm\Field;

abstract class Field {
    protected $type, $val, $default_val, $changed;
    protected $params = array();

    public function __construct($params=null) {
        $this->params = $params;
        $this->default_val = $this->val;
    }

    public function param($k) {
        if (isset($this->params[$k]))
            return $this->params[$k];
        else return false;
    }
    
    public function changed() {
        return $this->changed;
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
     * @return mixed
     */
    public function defaultValue() {return $this->default_value;}
    /**
     * @return mixed
     */ 
    public function getValue() {return $this->value;}
    /**
     * @return mixed
     */
    public function forcedValue() {return $this->value;}
    /**
     * @param mixed 
     * @return \Orm\Field\Field
     */
    public function setValue($val) {
        $this->value = $val; 
        $this->changed = 1;
        return $this;
    }

    /**
     * set field value with no validation (useful when setting values from db)
     * @param mixed 
     * @return \Orm\Field\Field
     */
    public function forceValue($val) {$this->value = $val; return $this;}
}
