<?php
namespace Orm\Field;

class Varchar extends Field {

    public function __construct($len) {
        $this->length = $len;

        parent::__construct();
    }
    public function init() {
        $this->type = 'varchar(%d)';
        $this->value = '';
    }

    public function getType() {
        return sprintf($this->type, $this->length); 
    }
}
