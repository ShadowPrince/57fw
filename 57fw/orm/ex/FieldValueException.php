<?php
namespace Orm\Ex;

class FieldValueException extends \Orm\Ex\OrmException {
    public function __construct($field, $value) {
        if (is_object($value)) {
            $value = '(( DONT CONVERT TO STRING ))';
        }
        parent::__construct('For field "' . $field->getName() . '" of type "' . $field->getType() . '" value "' . $value . '" incorrect');
    }
}
