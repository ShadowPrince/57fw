<?php
namespace Orm;

class ResultSet implements \Iterator {
    protected $set;
    protected $getter;
    protected $simpleList;
    protected $limit;

    public function __construct($getter, $set, $simpleList=false) {
        $this->set = $set;
        $this->simpleList = $simpleList;
        $this->getter = $getter;
    }

    /**
     * Get length of query set
     * @return int
     */
    public function len() {
        return count($this->set);
    }

    /**
     * Set query set's limit
     * @param int
     * @return \Orm\ResultSet
     */
    public function limit($n) {
        $this->limit = $n;
        return $this;
    }

    /**
     * @return array
     */
    public function getSet() {
        return $this->set;
    }

    /**
     * Implode array or ResultSet to string
     * @param mixed
     * @return array
     */
    public static function implode($ar) {
        if (is_array($ar)) 
            return implode(':', $ar);
        else if ($ar instanceof \Orm\ResultSet) 
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
     * Various methods for Iterator
     */
    public function get($id) {
        return call_user_func_array($this->getter, array($id));
    }

    public function rewind() {
        reset($this->set);
    }

    public function current() {
        if ($this->simpleList) {
            $args = array(current($this->set));
        } else {
            $args = array(current($this->set)['id']);
        }

        return call_user_func_array($this->getter, $args);
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
