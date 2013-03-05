<?php
namespace Orm\Field;

class Varchar extends Field {
    public $type = 'varchar(%d)';
    public $value = '';

    public function __construct($len) {
        $this->length = $len;

        parent::__construct();
    }

    public function getType() {
        return sprintf($this->type, $this->length); 
    }
}
