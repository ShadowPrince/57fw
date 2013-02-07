<?php
namespace Orm\Field;

class PrimaryKey extends Field {
    public function init() {
        $this->value = '';
    }

    public function getType() {
        return $this->type . ' NOT NULL PRIMARY KEY AUTO_INCREMENT';
    }
}
