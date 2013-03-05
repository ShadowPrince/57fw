<?php
namespace Orm\Field;

class Int extends Field {
    protected $type = 'int';
    protected $value = 0;

    public function setValue($val) {
        if ($val != (string) (int) $val)
            throw new \Orm\Ex\FieldValueException($this, $val);

        return parent::setValue($val);
    }
}
