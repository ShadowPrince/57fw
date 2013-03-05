<?php
namespace Orm;

/**
 * Abstract class of model for inheritance
 */
abstract class Model {
    public static $table, $pkey;
    public $fields = array();

    public function __construct() {
        $this->createFields();
    }

    /**
     * Getter for getting field value instead of class instance
     * @param string
     * @return mixed
     */
    public function __get($attr) {
        if (!isset($this->fields[$attr]))
            throw new \Orm\Ex\FieldNotFoundException($attr);
        return $this->fields[$attr]->getValue();
    }

    /**
     * Setter for setting field value instead of class instance
     * @param string
     * @param mixed
     * @return mixed
     */
    public function __set($attr, $value) {
        if ($this->fields[$attr] instanceof \Orm\Field\Field) {
            $f = $this->fields[$attr];
            $f->setValue($value);
        } else
            $this->fields[$attr] = $value;
    }

    /*
     * Old setter and getter
     * @TODO: delete this 
    public function __call($fn, $args) {
        if (method_exists($this, $fn)) {
            return call_user_func_array(
                array($this, $fn),
                $args
            );
        } else if (count($args) == 1) {
            return call_user_func_array(
                array($this->$fn, 'setValue'),
                $args
            );
        } else if (count($args) == 0) {
            return $this->$fn->getValue();
        }
    } */


    /**
     * Get primary key field of model
     * @throws \Exception
     * @return \Orm\Field\PrimaryKey
     */
    public function getPKey() {
        if ($this->fields['id'] instanceof \Orm\Field\PrimaryKey) {
            return $this->fields['id'];
        } else {
            if ($this->fields) foreach ($this->fields as $k => $var) {
                if ($var instanceof \Orm\Field\PrimaryKey) {
                    return $var;
                }
            }
            throw new \Exception('There is no primary key!');
        } 
    }

    /**
     * Get all fields
     * @return array
     */
    public function getFields() {
        return $this->fields;
    }

    /**
     * Get field by name
     * @param string
     * @return \Orm\Field\Field
     */
    public function getField($name) {
        return $this->fields[$name];
    }
    
    /**
     * createFields by model vars
     */
    protected function createFields() {
        foreach (get_object_vars($this) as $k => $v) {
            if (is_string($v) && substr($v, 0, 3) == 'new') {
                $eval = '$this->fields[$k] = ' . $v . ';';
                eval($eval);
                $this->fields[$k]->name = $k;
                unset($this->$k);
            }
        }
    }
}
