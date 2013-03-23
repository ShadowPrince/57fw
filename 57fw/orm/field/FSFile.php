<?php
namespace Orm\Field;

class FSFile extends Text {
    public function __construct() {
        call_user_func_array('parent::__construct', func_get_args());

        if (!$this->param('dir'))
            throw new \Orm\Ex\FieldParamException($this, 'dir', 'can\'t be null');
        if (!$this->param('allowed_types') && !$this->param('all_types'))
            throw new \Orm\Ex\FieldParamException($this, 'allowed_types', 'can\'t be empty (and "all_types" not provided)');

    }

    public function setValue($val) {
        if (!$val)
            throw new \Orm\Ex\FieldValueException($this, 'null or empty');

        $dir = $this->param('dir');  
        $allowed = false;
        foreach ($this->param('allowed_types') as $regex) {
            if (preg_match($regex, $val['type'])) {
                $allowed = true;
                break;
            }
        }
        if (!$allowed)
            throw new \Orm\Ex\FieldValueException($this, $val['name'] . ' of ' . $val['type']);
        $ex = explode('.', $val['name']);
        $ex = array_pop($ex);
        $path = $dir . DIRECTORY_SEPARATOR . md5($val['name']) . '.' . $ex;

        copy($val['tmp_name'], $path);

        parent::setValue($val['name']);
    }

    public function getValue() {
        $ex = explode('.', parent::getValue());
        $ex = array_pop($ex);
        $path = $this->param('dir')  
            . DIRECTORY_SEPARATOR 
            . md5(parent::getValue())
            . '.' 
            . $ex;

        return $path;
    }
}
