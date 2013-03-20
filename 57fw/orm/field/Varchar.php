<?php
namespace Orm\Field;

class Varchar extends Field {
    protected $type = 'varchar(%d)';

    public function __construct($len, $params=array()) {
        $this->length = $len;

        parent::__construct($params);
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

        if (strlen($val) > $this->length) {
            throw new \Orm\Ex\FieldValueException($this, 'value "' . $val . '" longer that ' . $this->length . ' (' . strlen($val) . ')');
        }

        return parent::setValue($val);
    }
}
