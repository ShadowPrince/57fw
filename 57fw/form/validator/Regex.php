<?php
namespace Form\Validator;

class Regex extends Validator {
    protected $regex;
    
    public function __construct($regex, $config=array()) {
        $this->regex = $regex;
        
        parent::__construct($config);
    }

    public function validate($field) {
        return preg_match($this->regex, $field->getValue());
    }
}
