<?php
namespace Orm\Field;

class Text extends Field {
    protected $type = 'text';
    protected $value = '';

    public function setValue($val) {
        try {
            (string) $val;
        } catch (\Exception $e) {
            throw new \Orm\Ex\FieldValueException($this, '((VALUE DONT CONVERTS TO STR))');
        }

        return parent::setValue($val);
    }
}
