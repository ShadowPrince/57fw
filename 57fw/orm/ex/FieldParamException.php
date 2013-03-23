<?php
namespace Orm\Ex;

class FieldParamException extends OrmException {
    public function __construct($field, $param, $msg) {
        parent::__construct("Error on params of " . $field->getName() . " on " . $param . ": " . $msg);
    }
}
