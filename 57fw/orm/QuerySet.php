<?php
namespace Orm;

class QuerySet implements \Iterator {
    public function __construct($manager, $set) {
        $this->set = $set;
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
        $id = current($this->set)['id'];
        return call_user_func_array($this->getter, array($id));
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
