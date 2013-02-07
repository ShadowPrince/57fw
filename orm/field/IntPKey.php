<?php
namespace Orm\Field;

class IntPKey extends PrimaryKey {
    public function init() {
        $this->type = 'int';
        parent::init();
    }
}
