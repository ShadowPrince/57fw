<?php
namespace Orm\Field;

class DateTime extends Field {
    public $type = 'timestamp';
    public $value = '';

    public function getValue() {
        if (!$this->value && $this->auto)
            $this->value = (new \DateTime())->format('U');
        return parent::getValue();
    }
}
