<?php
namespace Form\Field;

class Input extends Field {
    protected $config = array(
        'html' =>
        '<input 
            class="{{classes}}" 
            type="text" 
            value="{{value}}" 
            name="{{name}}" 
            placeholder="{{placeholder}}" 
            {{kv}} 
        />'
    );
}
