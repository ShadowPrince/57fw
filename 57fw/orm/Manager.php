<?php
namespace Orm;

abstract class Manager {
    protected $e;
    protected $model;
    protected $backend;

    public function __construct($e, $model) {
        $this->e = $e;
        $this->model = $model;
        $this->createBackend();
    } 

    /**
     * Find instances by $kv and return queryset
     * @param array
     * @return \Orm\QuerySet
     */
    public function find($kv) {
        $php_is_dummy = $this->getModel();
        if ($php_is_dummy::$pkey) {
            return new \Orm\QuerySet($this,
                $this->backend->select($this, $kv, array(
                    $php_is_dummy::$pkey
                ))
            );
        } else {
            return new \Orm\QuerySet($this,
                $this->backend->select($this, $kv, array(
                    '*'
                )), true
            );
        }
    }

    /**
     * Find instance by primary key
     * @param int
     * @throws Exception
     * @return \Orm\Model
     */
    public function get($val) {
        $php_is_dummy = $this->getModel();
        if (!$php_is_dummy::$pkey)
            throw new \Orm\Ex\PKeyRequiredException('get');
        
        $data = $this->backend->select($this, array(
            "`%s` = '%d'" => array($php_is_dummy::$pkey, $val)
        ), array('*'));

        if (!$data)
            throw new \Orm\Ex\RowNotFoundException('PKey = ' . $val);
        else
            $data = $data[0];

        return $this->buildInstance($data);
    }

    public function buildInstance($data) {
        $cls = $this->getModel();
        $instance = new $cls();
        foreach ($data as $k => $v) {
            if ($instance->getField($k) instanceof \Orm\Field\KeyField) {
                $instance->getField($k)->setupManager(array($this->e, 'man'));
            }
            if ($instance->getField($k) instanceof \Orm\Field\Field) {
                $instance->getField($k)->forceValue($v);
            }  
        } 
        return $instance;
    }

    /**
     * Prepare database for model
     */
    public function prepare($opts, $print_callback) {
        $this->backend->prepare($this, $opts, $print_callback);
    }

    /**
     * Save (insert or update) instance
     * @var \Orm\Model
     */
    public function save($instance) {
        try {
            if (!$instance->{$instance::$pkey})
                throw new \Exception();
            $this->get($instance->{$instance::$pkey});
            $wh = array(
                "`%s` = '%d'" => array(
                    $instance::$pkey,
                    $instance->{$instance::$pkey}
                )
            );
        } catch (\Exception $e) {}
        $fields = $instance->getFields();
        $kv = [];
        if ($fields) foreach ($fields as $field) {
            $kv[$field->getName()] = $this->value($field->getValue());
        }
        if (isset($wh)) {
            $this->backend->update($this, $kv, $wh);
        } else {
            $this->backend->insert($this, $kv);
        }

    }

    /**
     * Get working table
     * @return string
     */
    public function getTable() {
        $php_is_dummy = $this->getModel();
        return $php_is_dummy::$table;
    }

    /**
     * Get model class
     * @return string
     */
    public function getModel() {
        return $this->model;
    }

    /**
     * Create backend by string class var
     */
    protected function createBackend() {
        $eval = '$this->backend = ' . $this->backend . ';';
        eval($eval);
    }

    /**
     * Get string value of various objects
     * @param mixed
     * @return string
     */
    protected function value($instance) {
        if ($instance instanceof \Orm\Model) {
            return $instance->{$instance::$pkey};
        }
        return $instance;
    }
}
