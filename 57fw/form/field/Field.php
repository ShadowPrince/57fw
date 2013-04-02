<?php
namespace Form\Field;

class Field extends \Core\ConfiguredInstance {
    protected $val;
    protected $name;
    protected $placeholder;
    protected $error;

    protected $config = array(
        'html' => '',
    );
    protected $validators = array();

    public function __construct($config=array()) {
        $this->createValidators();

        $this->setParam('classes', array());
        parent::__construct($config);
    }

    public function render($tw) {
        return $tw->render(
            $this->config('html'),
            array(
                'name' => $this->getName(),
                'placeholder' => $this->getPlaceholder(),
                'classes' => implode(' ', $this->getClasses()),
                'value' => $this->getValue()
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

    public function isRequired() {
        return $this->hasValidator('\Form\Validator\NotEmpty');
    }

    public function isValid() {
        foreach ($this->getValidators() as $callback) {
            if (!call_user_func(array($callback, 'validate'), $this)) {
                $this->setError(true);
                return false;
            }
        }
        return true;
    }

    public function addClass($class) {
        $this->setParam('classes', array_merge($this->config('classes'), array($class)));
        return $this;
    }

    public function getClasses() {
        return $this->config('classes');
    }

    public function setError($error) {
        $this->error = $error;

        return $this;
    }

    public function isError() {
        return $this->error;
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

    public function hasValidator($class) {
        foreach ($this->getValidators() as $instance) {
            if ($instance instanceof $class)
                return true;
        }

        return false;
    }

    public function getValidators() {
        return $this->validators;
    }

    public function validate($validator) {
        $this->validators[] = $validator;

        return $this;
    }
}
