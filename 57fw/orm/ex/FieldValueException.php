<?php
namespace Orm\Ex;

class FieldValueException extends \Orm\Ex\OrmException {
    public function __construct($field, $value) {
        parent::__construct('For field "' . $field->getName() . '" of type "' . $field->getType() . '" value "' . $value . '" incorrect');
    }
}
