<?php
namespace Orm\Field;

class DateTime extends Field {
    protected $type = 'timestamp';
    public static $format = 'Y-m-d H:i:s';

    public function getValue() {
        if (!$this->value && $this->config('auto'))
            $this->value = (new \DateTime())->format($this->format);

        $dt = \DateTime::createFromFormat(static::$format, $this->value);

        return $dt;
    }

    public function setValue($val) {
        if (!$val) {
            throw new \Orm\Ex\FieldValueException($this, $val);
        } else if ($val instanceof \DateTime) {
            parent::setValue($val->format(static::$format));
            return $this;
        } else if (is_string($val)) {
            $dt = \DateTime::createFromFormat(
                $this::$format,
                $val
            );
        } else if (is_array($val)) {
            $dt = call_user_func_array('\DateTime::createFromFormat', $val);
        } else {
            $dt = false;
        }

        if ($dt) {
            return $this->setValue($dt);
        } else {
            throw new \Orm\Ex\FieldValueException($this, $val);
        }
    }
}
