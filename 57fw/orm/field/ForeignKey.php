<?php
namespace Orm\Field;

class ForeignKey extends KeyField {
    public $type = 'int';
    public $value = 0;

    public function __construct($model) {
        $this->model = $model; 
        parent::__construct();
    }

    public function getManager($getter) {
        return call_user_func_array($getter, array($this->model));
    }

    public function setValue($ins) {
        if ($ins instanceof \Orm\Model) {
            $this->instance = $ins;
            return parent::setValue($ins->{$ins::$pkey});
        } else {
            return parent::setValue($ins);
        }
    }

    public function getValue() {
        if ($this->instance) {
            return $this->instance;
        } else { 
            $this->instance = $this->manager->get(parent::getValue());
            return $this->instance;
        }
    }
}
