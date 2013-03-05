<?php
namespace Orm;

abstract class Manager {
    public function __construct($e, $model) {
        $this->e = $e;
        $this->modelClass = $model;
        $this->createBackend();
    } 

    /**
     * Find instances by $kv and return queryset
     * @param array
     * @return \Orm\QuerySet
     */
    public function find($kv) {
        $php_is_dummy = $this->modelClass;
        $data = $this->backend->select($this, $kv, array(
            $php_is_dummy::$pkey
        ));
        return new \Orm\QuerySet($this, $data);
    }

    /**
     * Find instance by primary key
     * @param int
     * @throws Exception
     * @return \Orm\Model
     */
    public function get($val) {
        $php_is_dummy = $this->modelClass;
        $data = $this->backend->select($this, array(
            "`%s` = '%d'" => array($php_is_dummy::$pkey, $val)
        ), array('*'));
        if (!$data)
            throw new \Exception("Object not found");
        else
            $data = $data[0];
        $instance = new $this->modelClass(); 
        foreach ($data as $k => $v) {
            if ($instance->fields[$k] instanceof \Orm\Field\KeyField) {
                $instance->fields[$k]->setupManager(array($this->e, 'manager'));
            }
            if ($instance->fields[$k] instanceof \Orm\Field\Field) {
                $instance->fields[$k]->setValue($v);
            }  
        } 
        return $instance;
    }

    /**
     * Prepare database for model
     */
    public function prepare() {
        $this->backend->prepare($this);
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
        $php_is_dummy = $this->modelClass;
        return $php_is_dummy::$table;
    }

    /**
     * Create backend by string class var
     */
    private function createBackend() {
        $eval = '$this->backend = ' . $this->backend . ';';
        eval($eval);
    }

    /**
     * Get string value of various objects
     * @param mixed
     * @return string
     */
    private function value($instance) {
        if ($instance instanceof \Orm\Model) {
            return $instance->{$instance::$pkey};
        }
        return $instance;
    }
}
