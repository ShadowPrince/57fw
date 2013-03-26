<?php
namespace Form\Validator;

class Empty extends Validator {
    public function validate($field) {
        return (string) $field->getValue() === '';
    }
}
