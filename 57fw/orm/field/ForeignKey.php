<?php
namespace Orm\Field;

class ForeignKey extends KeyField {
    protected $type = 'int';
    protected $value = 0;
    protected $model;

    public function __construct($model) {
        if (is_string($model))
            $this->model = $model; 
        else
            $this->model = get_class($model);

        parent::__construct();
    }

    public function getManager($getter) {
        return call_user_func_array($getter, array($this->model));
    }

    public function setValue($ins) {
        if ($ins instanceof \Orm\Model) {
            $this->instance = $ins;
            return parent::setValue($ins->{$ins::$pkey});
        } else if ((string) (int) $ins == $ins) {
            return parent::setValue($ins);
        } else throw new \Orm\Ex\FieldValueException($this, $ins);
    }

    public function getValue() {
        if ($this->instance) {
            return $this->instance;
        } else { 
            if ($this->manager && $this->value)
                $this->instance = $this->manager->get(parent::getValue());
            else
                return null;
            return $this->instance;
        }
    }

    public function getModel() {
        return $this->model;
    }
}
