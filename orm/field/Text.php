<?php
namespace Orm\Field;

class Text extends Field {
    public function init() {
        $this->type = 'text';
        $this->value = '';
    }
}
