<?php
namespace Orm;

class Manager {
    public function __construct($model) {
        $this->modelClass = $model;
        $this->model = new $model();
        $this->init();
    } 

    public function init() {}

    public function find($kv) {
        $data = $this->backend->select($this, $kv);
        $instances = [];
        if ($data) foreach ($data as $row) {
            $instance = new $this->modelClass();  
            if ($row) foreach ($row as $k => $v) {
                $instance->$k->setValue($v);
            }
            $instances[] = $instance;
        }

        return $instances;
    }

    public function get($val) {
        $pkey = $this->model->getPrimaryKey();
        $data = $this->backend->select($this, array(
            "`%s` = '%d'" => array($pkey->name, $val)
        ));

        if (!$data)
            throw new \Exception("Object not found");
        else
            $data = $data[0];

        $instance = new $this->modelClass(); 
        foreach ($data as $k => $v) {
            if ($instance->$k instanceof \Orm\Field\Field) {
                $instance->$k->setValue($v);
            }  
        } 

        return $instance;
    }

    public function prepare() {
        $this->backend->prepare($this);
    }

    public function save($instance) {
        try {
            if (!$instance->getPrimaryKey()->getValue())
                throw new \Exception();
            $this->get($instance->getPrimaryKey()->getValue());
            $wh = [
                "`%s` = '%d'" => [
                    $instance->getPrimaryKey()->getName(),
                    $instance->getPrimaryKey()->getValue()
                ]
            ];
        } catch (\Exception $e) {}
        $fields = $instance->getFields();
        $kv = [];
        if ($fields) foreach ($fields as $field) {
            $kv[$field->getName()] = $field->getValue();
        }
        if (isset($wh)) {
            $this->backend->update($this, $kv, $wh);
        } else {
            $this->backend->insert($this, $kv);
        }

    }
}
