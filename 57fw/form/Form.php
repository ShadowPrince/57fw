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
        'action="{{action}}" method="{{method}}" {{enctype}}',
        'field' => '<p><label>{{label}}</label>{{required}} {{content|raw}}</p>',

        'field_class' => 'field',
        'field_error_class' => 'error',
        'field_required_class' => 'required',

        'exclude' => array()
    );

    public function __construct($e, $kv=array(), $config=array()) {
        if ($kv instanceof \Orm\Model) {
            $this->kv = $e->man(get_class($kv))->dissassembleInstance($kv);
            $this->model = $kv->getClass();
        } else if (isset($kv['__submit'])) {
            $this->submit = true;
            $this->kv = $kv;
        }
        $this->setParam('classes', array());
        $this->twig = $e->twig_string;
        $this->e = $e;
        $this->createFields();

        parent::__construct($config);
    }

    protected function createFields() {
        $this->fields = array();
        if ($this->getModel()) {
            foreach ((new $this->model())->getFields() as $name => $mfield) {
                if (false !== array_search($name, $this->config('exclude')))
                    continue;
                try {
                    if ($mfield instanceof \Orm\Field\Text)
                        $field = new Field\TextArea();
                    else
                        $field = new Field\Input();

                    $field->setName($name);

                    if (isset($this->kv[$name]))
                        $field->setValue($this->kv[$name]);

                    if (
                        !$mfield->config('null') 
                        && 
                        !($mfield instanceof \Orm\Field\PrimaryKey)
                    ) {
                        $field->validate(new \Form\Validator\NotEmpty());
                    }

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

    /**
     * Is form submitted?
     * @return boolean
     */
    public function isSubmitted() {
        return $this->submit;
    }   

    /**
     * Validate fields and return result
     * @return boolean
     */
    public function isValid() {
        if (!$this->isSubmitted()) 
            return false;

        $fail = false;

        foreach ($this->fields as $field) {
            if (!$field->isValid()) {
                $fail = true;
            }

        }
        return !$fail;
    }

    /**
     * Render entire form
     * @return string
     */
    public function render() {
        $content = '';
        foreach ($this->getFields() as $field) {
            $field->addClass($this->config('field_class'));
            if ($field->isError()) 
                $field->addClass($this->config('field_error_class'));
            else if ($field->isRequired())
                $field->addClass($this->config('field_required_class'));
            $content .= $this->renderField($field);
        }
        
        return $content;
    }

    /**
     * Render one field
     * @return string
     */
    public function renderField($field) {
        return $this->twig->render(
            $this->config('field'), 
            array(
                'label' => $field->getLabel() ? $field->getLabel() . ':' : '',
                'content' => $field->render($this->twig),
                'required' => $field->isRequired() ? '*' : ''
            )
        );
    }

    /**
     * Render <form> attributes
     * @return string
     */
    public function renderAttrs() {
        return $this->twig->render(
            $this->config('html'),
            array(
                'action' => $this->getAction(),
                'method' => $this->getMethod(),
                'entype' => $this->getEnctype()
            )
        );
    }

    /**
     * Get form data
     */
    public function getData() {
        $data = array();
        foreach ($this->getFields() as $field) {
            if (0 !== strpos($field->getName(), '__'))
                $data[$field->getName()] = $field->getValue();
        }

        return $data;
    }

    /**
     * Build instance if form attached to model
     * @return \Orm\Model
     */
    public function buildInstance() {
        if (!$this->getModel())
            throw new Ex\ModelRequiredException();

        $data = $this->getData();
        foreach ($data as $k => $v) {
            if (0 === strpos($k, '__')) {
                unset($data[$k]);
            }
        }

        return $this->e->man($this->getModel())->buildInstance($data);
    }

    /**
     * @param string
     * @return \Form\Form
     */
    public function setAction($action) {
        $this->action = $action;

        return $this;
    }

    /**
     * @param string
     * @return \Form\Form
     */
    public function setMethod($method) {
        $this->method = $method;

        return $this;
    }

    /**
     * @return string
     */
    public function getAction() {
        return $this->action;
    }

    /**
     * @return string
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getEnctype() {
        //@TODO: enctype by files in fields
        return "";
    }

    /**
     * @return string
     */
    public function getModel() {
        return $this->model;
    }

    /**
     * @param string
     * @return \Form\Form
     */
    public function setModel($model) {
        $this->model = $model;
        $this->createFields();

        return $this;
    }

    /**
     * @return \Form\Field\Field
     */
    public function getField($k) {
        return $this->fields[$k];
    }

    /**
     * @return array
     */
    public function getFields() {
        return $this->fields;
    }
}
