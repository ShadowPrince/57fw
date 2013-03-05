<?php
namespace Orm\Field;

class Varchar extends Field {
    protected $type = 'varchar(%d)';
    protected $value = '';

    public function __construct($len) {
        $this->length = $len;

        parent::__construct();
    }

    public function getType() {
        return sprintf($this->type, $this->length); 
    }

    public function setValue($val) {
        try {
            (string) $val;
        } catch (\Exception $e) {
            throw new \Orm\Ex\FieldValueException($this, '((VALUE DONT CONVERTS TO STR))');
        }

        return parent::setValue($val);
    }
}
