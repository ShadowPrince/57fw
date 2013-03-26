<?php
namespace Form\Field;

class Field extends \Core\ConfiguredInstance {
    protected $val, $name, $placeholder;

    protected $__kv = array();
    protected $config = array(
        'html' => '',
    );
    protected $validators = array(
    );

    public function __construct($config=array()) {
        $this->config['classes'] = array('57fw_field');
        $this->createValidators();

        parent::__construct($config);
    }

    public function render($tw) {
        return $tw->render(
            $this->config('html'),
            array(
                'name' => $this->getName(),
                'placeholder' => $this->getPlaceholder(),
                'classes' => implode(', ', $this->getClasses()),
                'value' => $this->getValue(),
                'kv' => implode(' ', $this->__kv)
            )
        );
    }

    protected function createValidators() {
        foreach ($this->validators as $k => $v) {
            if (is_string($v) && substr($v, 0, 3) == 'new') {
                $eval = '$this->validators[$k] = ' . $v . ';';
                eval($eval);
            }
        }
    }

    public function isValid() {
        foreach ($this->validators as $callback) {
            if (!call_user_func(array($callback, 'validate'), $this)) {
                $this->__kv['class'] = 'error';
                return false;
            }
        }
        return true;
    }

    public function getClasses() {
        return $this->config('classes');
    }

    public function getLabel() {
        return $this->name;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($value) {
        $this->name = $value;

        return $this;
    }
    
    public function setValue($value) {
        $this->val = $value;

        return $this;
    }

    public function getValue() {
        return $this->val;
    }

    public function setPlaceholder($value) {
        $this->placeholder = $value;

        return $this;
    }

    public function getPlaceholder() {
        return $this->placeholder;
    }

    public function validate($validator) {
        $this->validators[] = $validator;

        return $this;
    }
}
