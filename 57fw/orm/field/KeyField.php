<?php
namespace Orm\Field;

abstract class KeyField extends Field {
    public function setupManager($getter) {
        $this->manager = $this->getManager($getter);
    } 
}
