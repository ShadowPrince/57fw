<?php
namespace Orm;

abstract class Manager extends \Core\Service {
    protected $model;
    protected $e;
    public $backend;

    public function __construct($e, $model, $backend) {
        $this->model = $model;
        $this->backend = $backend;
        $this->e = $e;
    } 

    /**
     * Find instances by $kv and return queryset
     * @param array
     * @return \Orm\QuerySet
     */
    public function find() {
        return $this->backend->getQuerySet($this);
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
            array("`" . $php_is_dummy::$pkey . "` = %s", $val)
        ), array('*'));

        if (!$data)
            throw new \Orm\Ex\RowNotFoundException('PKey = ' . $val);
        else
            $data = $data[0];

        return $this->buildInstance($data);
    }

    public function dissassembleInstance($instance, $changed=false) {
        $kv = array();
        foreach ($instance->getFields() as $field) {
            if ($field->changed() || !$changed) {
                $kv[$field->getName()] = $field->forcedValue();
            }
        } 
        return $kv;
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
        $cls = $this->getModel();
        if (!isset($cls::$pkey)) {
            $fields = $this->getModelInstance()->getFields();
            if ($fields) foreach ($fields as $f) {
                if ($f instanceof \Orm\Field\PrimaryKey) {
                    $print_callback(sprintf(
                        'Model %s has primary key %s, but static variable $pk not setted!'
                        . PHP_EOL . ' (!) THAT MY CAUSE ERRORS!', 
                        $this->getModel(),
                        $f->getName()
                    ));
                }
            }
        }
        $this->backend->prepare($this, $opts, $print_callback);
    }

    /**
     * Save (insert or update) instance
     * @param \Orm\Model
     */
    public function save($instance, $iknownopk=false) {
        if ($instance->getPKey()) {
            if ($instance->getPKey()->getValue()) {
                $wh = array(
                    "%s = %s" => array(
                        $instance::$pkey,
                        $instance->{$instance::$pkey}
                    )
                );
            }
        } else {
            if (!$iknownopk)
                throw new \Orm\Ex\OrmException('save() only insert instances with no primary key, not update. If you want to do it, provide second argument of save ($iknownopk). If you wanna update it - use queryset\'s update().');
        }

        if (isset($wh)) {
            $this->backend->update($this, $this->dissassembleInstance($instance), $wh);
        } else {
            $this->backend->insert($this, $this->dissassembleInstance($instance));
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
     * Get model instance
     * @return \Orm\Model
     */
    public function getModelInstance() {
        $cls = $this->getModel();
        if (!isset($this->modelInstance))
            $this->modelInstance = new $cls();
        return $this->modelInstance;
    }

    /**
     * Manager getter for engine service
     * @param \Core\Engine
     * @param string
     * @param string
     * @return \Orm\Manager
     */
    public static function manGetter($e, $model) {
        if (is_string($model)) {

        } else if ($model instanceof \Orm\Model) {
            $model = get_class($model);
        }

        if (!isset($e->cache['man_' . $model])) {
            if ($model::$manager) {
                $man = $model::$manager;
            } else {
                $man = get_class();
            }
            $e->cache['man_' . $model] = new $man($e, $model, $e->db());
        }

        return $e->cache['man_' . $model];
    }

    protected function unserialize($val) {
        $arr = array();

        foreach (explode(',', $val) as $v) {
            $data = explode('=', $v);
            $arr[trim($data[0])] = trim($data[1]);
        }

        return $arr;
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
        if ($instance instanceof \Orm\ResultSet) {
            return \Orm\ResultSet::implode($instance);
        }
        if ($instance instanceof \DateTime) {
            return $instance->format(\Orm\Field\DateTime::$format);
        }
        return $instance;
    }
}
