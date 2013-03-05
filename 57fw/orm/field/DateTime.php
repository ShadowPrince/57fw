<?php
namespace Orm\Field;

class DateTime extends Field {
    protected $type = 'timestamp';
    protected $value = '';
    protected $format = 'Y-m-d H:i:s';

    public function getValue() {
        if (!$this->value && $this->auto)
            $this->value = (new \DateTime())->format($this->format);
        $dt = \DateTime::createFromFormat($this->format, $this->value);

        return $dt;
    }

    public function setValue($val) {
        if ($val instanceof \DateTime)
            parent::setValue($val->format($this->format));
        else try {
            return $this->setValue(new \DateTime($val));
        } catch (\Exception $e) {
            throw new \Orm\Ex\FieldValueException($this, $val);
        }
    }
}
