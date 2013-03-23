<?php
namespace Orm;

/**
 * Class for querysets
 */
abstract class QuerySet implements \Iterator {
    protected $set = array();
    protected $simple;
    protected $limit;

    /**
     * Filter queryset
     * @param string
     * @param mixed
     * @return \Orm\QuerySet
     */
    abstract function filter($cf, $value);
    /**
     * Set queryset limit
     * @param int
     * @param int
     * @return \Orm\QuerySet
     */
    abstract function limit($n, $f);
    /**
     * Set queryset order
     * @param string
     * @param bool
     * @return \Orm\QuerySet
     */
    abstract function order($field, $desc);
    /**
     * Update all query set. $instance = key-value array or instance of model
     * @param mixed
     * @return \Orm\QuerySet
     */
    abstract function update($instance);
    /**
     * Delete entire query set
     * @return \Orm\QuerySet
     */
    abstract function delete();
    /**
     * @return int
     */
    abstract function count();

    public function filter3($field, $flag, $value) {
        return $this->filter($field . ' ' . $flag, $value);
    }
    
    public function setExecuted($executed) {
        $this->executed = $executed;
        return $this;
    }

    public function getExecuted() {
        return $this->executed;
    }

    /**
     * Get length of query set's set
     * @return int
     */
    public function len() {
        return count($this->set);
    }

    /**
     * Get set
     * @return array
     */
    public function getSet() {
        $this->execute();
        return $this->set;
    }

    /**
     * Set set
     * @param array
     */
    public function setSet($set) {
        $this->set = $set;
        return $this;
    }

    /**
     * Implode array or ResultSet to string
     * @param mixed
     * @return array
     */
    public static function implode($ar) {
        if (is_array($ar)) 
            return implode(':', $ar);
        else if ($ar instanceof QuerySet) 
            return implode(':', $ar->set);
    }

    /**
     * Explode string to ResultSet's set
     * @param string
     * @return array
     */
    public static function explode($str) {
        $ar = explode(':', $str);
        $result = array();
        if ($ar) foreach ($ar as $id) {
            $result[]['id'] = $id;
        }

        return $result;
    }

    /**
     * Get instance from $data
     * @param mixed
     * @return \Orm\Model
     */
    public function getInstance($data) {
        if ($this->simple)
            return $this->manager->buildInstance($data);
        else {
            if ($data == 0)
                return null;
            else
                return $this->manager->get($data);
        }
    }

    /**
     * Various methods for Iterator
     */

    public function rewind() {
        $this->execute();
        reset($this->set);
    }

    public function current() {
        $this->execute();
        if ($this->simple) {
            $args = array(current($this->set));
        } else {
            $args = array(current($this->set)['id']);
        }

        return call_user_func_array(array($this, 'getInstance'), $args);
    }

    public function key() {
        return key($this->set);
    }

    public function next() {
        next($this->set);
    }

    public function valid() {
        $key = key($this->set);
        if ($this->limit && $key >= $this->limit)
            return false;
        $var = ($key !== NULL && $key !== FALSE);
        return $var;
    }
}
