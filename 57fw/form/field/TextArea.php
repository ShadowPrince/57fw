<?php
namespace Form\Field;

class Textarea extends Field {
    protected $config = array(
        'html' => 
        '<textarea 
            cols="{{cols}}" 
            rows="{{rows}}" 
            name="{{name}}" 
            {{kv}}
            >{{value}}</textarea>',
        'cols' => 50,
        'rows' => 20
    );
}
