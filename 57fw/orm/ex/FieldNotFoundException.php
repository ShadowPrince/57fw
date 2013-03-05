<?php
namespace Orm\Ex;

class FieldNotFoundException extends \Orm\Ex\OrmException {
    public function __construct($field) {
        parent::__construct('Field not found: ' . $field);
    }
}
