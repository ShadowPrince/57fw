<?php
namespace Form\Validator;

class NotEmpty extends Validator {
    public function validate($field) {
        return (string) $field->getValue() !== '';
    }
}
