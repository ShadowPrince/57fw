<?php
namespace Form;

class Form extends \Core\ConfiguredInstance {
    protected $__submit = 'new \Form\Field\Submit()';

    protected $e;
    protected $fields = array();
    protected $method='POST';
    protected $action;
    protected $submit = false;
    protected $kv = array();
    protected $twig;
    protected $model;

    protected $config = array(
        'html' =>
        '<form class="{{classes}}" action="{{action}}" method="{{method}}" {{enctype}}>{{fields|raw}}</form>',
        'classes' => array('57fw_form'),
        'field' => '<p>{{label}} {{content|raw}}</p>'
    );

    public function __construct($e, $kv=array()) {
        if ($kv instanceof \Orm\Model) {
            $this->kv = $e->man(get_class($kv))->dissassembleInstance($kv);
            $this->model = $kv->getClass();
        } else if (isset($kv['__submit'])) {
            $this->submit = true;
            $this->kv = $kv;
        }
        $this->twig = $e->twig_string;
        $this->e = $e;
        $this->createFields();
    }

    protected function createFields() {
        $this->fields = array();
        if ($this->getModel()) {
            foreach ((new $this->model())->getFields() as $name => $field) {
                try {
                    if ($field instanceof \Orm\Field\Text)
                        $field = new Field\Textarea();
                    else
                        $field = new Field\Input();

                    $field->setName($name);

                    if (isset($this->kv[$name]))
                        $field->setValue($this->kv[$name]);

                    if (!$field->config('null'))
                        $field->validate(new \Form\Validator\NotEmpty());

                    $this->fields[$name] = $field;
                } catch (\ErrorException $ex) {}
            }
        }

        foreach (get_object_vars($this) as $k => $v) {
            if (is_string($v) && substr($v, 0, 3) == 'new') {
                $eval = '$this->fields[$k] = ' . $v . ';';
                eval($eval);
                $this->getField($k)->setName($k);
                if (isset($this->kv[$k]))
                    $this->getField($k)->setValue($this->kv[$k]);
            }
        }

    }

    public function isSubmitted() {
        return $this->submit;
    }   

    public function isValid() {
        if (!$this->isSubmitted()) 
            return false;

        foreach ($this->fields as $field) {
            if (!$field->isValid()) {
                return false;
            }
        }
        return true;
    }

    public function render() {
        $content = '';
        foreach ($this->getFields() as $field) {
            $content .= $this->twig->render(
                $this->config('field'), 
                array(
                    'label' => $field->getLabel() ? $field->getLabel() . ':' : '',
                    'content' => $field->render($this->twig)
                )
            );
        }
        
        return $this->twig->render(
            $this->config('html'),
            array(
                'action' => $this->getAction(),
                'method' => $this->getMethod(),
                'entype' => $this->getEnctype(),
                'classes' => implode(', ', $this->getClasses()),
                'fields' => $content
            )
        );
    }

    public function getClasses() {
        return $this->config('classes');
    }

    public function getData() {
        $data = array();
        foreach ($this->getFields() as $field) {
            $data[$field->getName()] = $field->getValue();
        }

        return $data;
    }

    public function getInstance() {
        $data = $this->getData();
        foreach ($data as $k => $v) {
            if (0 === strpos($k, '__')) {
                unset($data[$k]);
            }
        }

        return $this->e->man($this->getModel())->buildInstance($data);
    }

    public function setAction($action) {
        $this->action = $action;

        return $this;
    }

    public function setMethod($method) {
        $this->method = $method;

        return $this;
    }

    public function getAction() {
        return $this->action;
    }

    public function getMethod() {
        return $this->method;
    }

    public function getEnctype() {
        //@TODO: enctype by files in fields
        return "";
    }

    public function getModel() {
        return $this->model;
    }

    public function setModel($model) {
        $this->model = $model;
        $this->createFields();

        return $this;
    }

    public function getField($k) {
        return $this->fields[$k];
    }

    public function getFields() {
        return $this->fields;
    }
}
