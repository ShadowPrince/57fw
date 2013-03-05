<?php
namespace Orm\Field;

class PrimaryKey extends Field {
    public function getType() {
        return $this->type . ' NOT NULL PRIMARY KEY AUTO_INCREMENT';
    }
}
