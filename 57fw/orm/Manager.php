<?php
namespace Orm;

/**
 * Class for model manager
 */
abstract class Manager extends \Core\Service {
    protected $model;
    protected $e;
    public $backend;

    /**
     * @param \Core\Engine
     * @param string
     * @param \Orm\Backend\GeneralBackend
     */
    public function __construct($e, $model, $backend) {
        $this->model = $model;
        $this->backend = $backend;
        $this->e = $e;
    } 

    /**
     * Get queryset
     * @return \Orm\QuerySet
     */
    public function find() {
        return $this->backend->getQuerySet($this);
    }

    /**
     * Find instance by primary key
     * @param int
     * @throws \Orm\Ex\OrmException
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

    /**
     * Dissassembly instance and return $kv. if $changed will return only changed fields
     * @param \Orm\Model
     * @param bool
     * @return array
     */
    public function dissassembleInstance($instance, $changed=false) {
        $kv = array();
        foreach ($instance->getFields() as $field) {
            if ($field->isChanged() || !$changed) {
                $kv[$field->getName()] = $field->forcedValue();
            }
        } 
        return $kv;
    }

    /**
     * Build instance from $kv
     * @param array
     * @return \Orm\Model
     */
    public function buildInstance($kv) {
        $cls = $this->getModel();
        $instance = new $cls();
        foreach ($kv as $k => $v) {
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
     * @param array
     * @param callable
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
     * @param bool
     */
    public function save($instance, $iknownopk=false) {
        if ($instance->getPKey()) {
            if ($instance->getPKey()->getValue()) {
                $wh = array(
                    array(
                        '`' . $instance::$pkey . '` = %s',
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

}
