<?php
namespace \Orm\Field;

class Int extends Field {
    public function init() {
        $this->type = 'int';
        $this->value = 0;
    }
}
