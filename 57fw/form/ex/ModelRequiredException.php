<?php
namespace Form\Ex;

class ModelRequiredException extends FormException {
    public function __construct() {
        parent::__construct('For getInstance() method form must be created with model');
    } 
}
