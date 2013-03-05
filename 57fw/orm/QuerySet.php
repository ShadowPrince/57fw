<?php
namespace Orm;

class QuerySet implements \Iterator {
    protected $set;
    protected $getter;
    protected $simpleList;

    public function __construct($manager, $set, $simpleList=false) {
        $this->set = $set;
        $this->simpleList = $simpleList;
        if ($this->simpleList)
            $this->getter = array($manager, 'buildInstance');
        else
            $this->getter = array($manager, 'get');
    }

    /**
     * Various methods for Iterator
     */
    public function len() {
        return count($this->set);
    }

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
        $var = ($key !== NULL && $key !== FALSE);
        return $var;
    }
}
