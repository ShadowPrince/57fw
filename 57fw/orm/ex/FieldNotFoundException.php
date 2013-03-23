<?php
namespace Orm\Ex;

class FieldNotFoundException extends OrmException {
    public function __construct($field) {
        parent::__construct('Field not found: ' . $field);
    }
}
