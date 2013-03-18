<?php
namespace Orm\Backend\PDO;

/**
 * PDO queryset
 */
class PDOQuerySet extends \Orm\QuerySet {
    protected $manager;
    protected $wh = array(); 
    protected $fields = array();
    protected $additional = array();
    protected $executed = false;

    /**
     * @param \Orm\Manager
     */
    public function __construct($manager) {
        $this->manager = $manager;
    } 

    /**
     * Setup required params if not setted
     * @return \Orm\QuerySet
     */
    public function getStandartizedParams() {
        $wh = $this->wh;
        $fields = $this->fields;
        $additional = $this->additional;
        $model = $this->manager->getModel();

        if (!$fields) {
            if ($model::$pkey) {
                $fields = array($model::$pkey);
            } else {
                $this->simple = true;
                $fields = array('*');
            }
        }

        if (!$wh) {
            $wh = array(1);
        }

        if (!isset($additional['order']) && isset($model::$order)) {
            $additional['order'] = $model::$order;
        }
        
        return array(
            'wh' => $wh,
            'fields' => $fields,
            'additional' => $additional
        );

    }
    
    /**
     * Update all query set. $instance = key-value array or instance of related model
     * @param mixed
     * @return \Orm\QuerySet
     */
    public function update($instance) {
        if ($instance instanceof \Orm\Model) {
            $kv = $this->manager->dissassembleInstance($instance, true);
        } else {
            $kv = $instance;
        }
        $params = $this->getStandartizedParams();
        $this->manager->backend->update(
            $this->manager,
            $kv,
            $params['wh'],
            $params['additional']
        ); 

        return $this;
    }

    /**
     * Delete entire query set
     * @return \Orm\QuerySet
     */
    public function delete() {
        $params = $this->getStandartizedParams();
        $this->manager->backend->delete(
            $this->manager,
            $params['wh'],
            $params['additional']
        ); 

        return $this;
    }

    /**
     * Execute query add fill set
     * @return \Orm\QuerySet
     */
    public function execute() {
        if (!$this->executed) {
            $this->executed = true;
            $params = $this->getStandartizedParams();

            $this->set = $this->manager->backend->select(
                $this->manager, 
                $params['wh'],
                $params['fields'],
                $params['additional']
            );
        }

        return $this;
    }

    /**
     * Count rows in query
     * @return int
     */
    public function count() {
        $params = $this->getStandartizedParams();

        return $this->manager->backend->count(
            $this->manager,
            $params['wh'],
            $params['additional']
        );
    }

    /**
     * Filter queryset
     * @param string
     * @param mixed
     * @return \Orm\QuerySet
     */
    public function filter($col_fl, $value, $op='and') {
        $col = explode(' ', $col_fl)[0];
        $fl = explode(' ', $col_fl);
        array_shift($fl); 
        $fl = implode(' ', $fl);

        $str = '`' . $col . '` ' . $fl . ' ?';
        if ($this->wh) {
            if ($op == 'and') 
                $str = 'AND ' . $str;
            else if ($op == 'or') 
                $str = 'OR ' . $str;
        }
        $this->wh[] = array($str, $this->value($value));

        return $this;
    }
    

    /**
     * Set queryset order
     * @param mixed
     * @param bool
     * @return \Orm\QuerySet
     */
    public function order($field, $desc=false) {
        if ($field == null) {
            unset($this->additional['order']);
        } else {
            if ($field instanceof \Orm\Field\Field)
                $field = $field->getName();

            $this->additional['order'] = $field . ' ' . ($desc ? 'desc' : '');
        }
        return $this;
    }

    /**
     * Set queryset limit
     * @param int
     * @param int
     * @return \Orm\QuerySet
     */
    public function limit($n, $f=false) {
        if ($n == null)
            unset($this->additional['limit']);
        else
            $this->additional['limit'] = $n . ($f ? ' , ' . $f :'');

        return $this;
    }

    /**
     * Get string value of various objects
     * @param mixed
     * @return string
     */
    protected function value($instance) {
        if ($instance instanceof \Orm\Model) 
            return $instance->{$instance::$pkey};
        if ($instance instanceof \Orm\ResultSet) 
            return \Orm\ResultSet::implode($instance);
        if ($instance instanceof \DateTime)
            return $instance->format(\Orm\Field\DateTime::$format);

        return $instance;
    }
}
