<?php
namespace Orm;

/**
 * Class for model manager
 */
class Manager extends \Core\Service {
    protected $model;
    protected $model_instance;
    protected $e;
    public $backend;

    /**
     * @param \Core\Engine
     * @param string
     * @param \Orm\Backend\GeneralBackend
     */
    public function __construct($e, $model, $backend) {
        if (is_string($model)) {
            $this->model = $model;
        } else {
            $this->model = get_class($model);
            $this->model_instance = $model;
        }
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
            throw new Ex\PKeyRequiredException('get');
         
        $data = $this->backend->select($this, array(
            array("`" . $php_is_dummy::$pkey . "` = ?", $val)
        ), array('*'));

        if (!$data)
            throw new Ex\RowNotFoundException('PKey = ' . $val);
        else
            $data = $data[0];

        $ins = $this->buildInstance($data);
        $ins->populate($this->e);
        return $ins;
    }

    /**
     * Delete instance by primary key
     * @param int
     * @throws \Orm\Ex\OrmException
     * @return \Orm\Model
     */
    public function delete($instance) {
        if (!$instance->getPKey())
            throw new \Orm\Ex\PKeyRequiredException('delete');
        $this->backend->delete($this, array(
            array("`" . $instance->getPKey()->getName() . "` = ?", $instance->getPKey()->getValue())
        ));
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
        $instance = $this->getModelInstance();
        foreach ($kv as $k => $v) {
            if ($instance->getField($k) instanceof Field\KeyField) {
                $instance->getField($k)->setupManager(array($this->e, 'man'));
            }
            if ($instance->getField($k) instanceof Field\Field) {
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
                if ($f instanceof Field\PrimaryKey) {
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
    public function save($instance=false, $iknownopk=false) {
        if (!$instance)
            $instance = $this->model_instance;
        if ($instance->getPKey()) {
            if ($instance->getPKey()->getValue()) {
                $wh = array(
                    array(
                        '`' . $instance::$pkey . '` = ?',
                        $instance->{$instance::$pkey}
                    )
                );
            }
        } else {
            if (!$iknownopk)
                throw new Ex\OrmException('save() only insert instances with no primary key, not update. If you want to do it, provide second argument of save ($iknownopk). If you wanna update it - use queryset\'s update().');
        }

        if (isset($wh)) {
            $this->backend->update($this, $this->dissassembleInstance($instance), $wh);
        } else {
            $id = $this->backend->insert($this, $this->dissassembleInstance($instance));
            if ($instance->getPKey())  {
                $instance->getPKey()->setValue($id);
            } 

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
        $cls = $this->model;
        if ($cls::$table == null) {
            $ns = explode('\\', $cls);
            $cls::$table = strtolower(array_pop($ns));
        }

        return $this->model;
    }

    /**
     * Get model instance
     * @return \Orm\Model
     */
    public function getModelInstance() {
        $cls = $this->getModel();
        $ins = new $cls();
        $ins->populate($this->e);
        return $ins;
    }

    /**
     * Manager getter for engine service
     * @param \Core\Engine
     * @param string
     * @param string
     * @return \Orm\Manager
     */
    public static function manGetter($e, $model) {
        $instance = $model;
        if (is_string($model)) {

        } else if ($model instanceof \Orm\Model) {
            $model = get_class($model);
        } else if ($model == null) {
            return;
        }

        if (!isset($e->cache['man_' . $model])) {
            if ($model::$manager) {
                $man = $model::$manager;
            } else {
                $man = get_class();
            }
            $e->cache['man_' . $model] = new $man($e, $instance, $e->db);
        }

        return $e->cache['man_' . $model];
    }

}
