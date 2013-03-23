<?php
namespace Orm\Field;

/**
 * @todo cleanup
 */
abstract class Field {
    public $value;
    protected $type, $name, $val, $changed;
    protected $params = array();

    public function __construct($params=null) {
        if ($params) 
            $this->params = $this->params + $params;

        if ($this->param('value') !== null)
            $this->setValue($this->param('value'));

    }

    public function param($k) {
        if (isset($this->params[$k]))
            return $this->params[$k];
        else return null;
    }
    
    public function isChanged() {
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
    public function setName($str) {$this->name = $str;}
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
