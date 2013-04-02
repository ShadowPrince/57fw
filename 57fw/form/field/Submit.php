<?php
namespace Form\Field;

class Submit extends Field {
    protected $config = array(
        'html' =>
        '<input 
            type="submit" 
            value="{{value}}" 
            name="{{name}}"
            class="{{ class }}"
        />'
    );

    protected $name = '__submit';
    protected $val = 'Send';
    protected $validators = array();

    public function setName($v) {}
    public function validate($test) {}
    public function getLabel() {return '';}
}
