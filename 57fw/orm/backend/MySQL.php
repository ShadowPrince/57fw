<?php
namespace Orm\Backend;

class MySQL implements GeneralBackend {
    public function __construct($e, $connection) {
        $this->e = $e;
        mysql_connect($connection['host'], $connection['user'], $connection['password']);
        mysql_select_db($connection['database']);
    }

    public function select($manager, $wh, $fields) {
        return $this->fetchArrayResult($this->executeQuery($this->buildFQuery(
            'SELECT %s FROM %s WHERE %s', 
            implode(', ', $fields),
            $manager->getTable(),
            $this->whereParamsImplode($wh)
        )));
    }

    public function update($manager, $kv, $wh) {
        $this->executeQuery($this->buildFQuery(
            'UPDATE %s SET %s WHERE %s',
            $manager->getTable(),
            $kv,
            $this->whereParamsImplode($wh)
        ));
    }

    public function insert($manager, $kv) {
        $this->executeQuery($this->buildFQuery(
            'INSERT INTO %s SET %s',
            $manager->getTable(),
            $kv
        ));
        return mysql_insert_id();
    }

    public function delete($manager, $wh) {
        $this->executeQuery($this->buildFQuery(
            'DELETE FROM %s WHERE %s',
            $manager->getTable(),
            $wh
        ));
    }

    public function count($manager, $wh) {
        $this->executeQuery($this->buildFQuery(
            'SELECT COUNT(*) FROM %s WHERE %s',
            $manager->getTable(),
            $wh
        ));
    }

    public function prepare($manager) {
        $fields = (new $manager->modelClass)->getFields();
        if ($fields) foreach ($fields as &$field) {
           $field = $field->getName() . ' ' . strtoupper($field->getType()); 
        }
        $this->executeQuery($this->buildFQuery(
            'CREATE TABLE IF NOT EXISTS %s (%s)',
            $manager->getTable(),
            implode(', ', $fields)
        )); 
    }

    /**
     * Execute query
     */
    public function executeQuery($qw) {
        $res = mysql_query($qw) or die(mysql_error());
        return $res;
    }

    /**
     * Fetch query result to rows array
     * @var mixed
     * @return array
     */
    public function fetchArrayResult($res) {
        $data = [];
        while ($row = mysql_fetch_assoc($res))
            $data[] = $row;
        return $data;
    }

    /**
     * Fetch query result to single row array
     * @var mixed
     * @return array
     */
    public function fetchSingleResult($res) {
        return mysql_fetch_array($res);
    }
    
    /**
     * Build query $qw with printf syntax
     * @return string
     */
    public function buildFQuery($qw) {
        $args = func_get_args();
        foreach ($args as &$arg)
            if (is_array($arg))
                $arg = $this->paramsImplode($arg);
        return call_user_func_array('sprintf', $args); 
    }

    /**
     * Escape array or string
     * @var mixed
     * @return mixed
     */
    public function escape($mixed) {
        if (is_array($mixed)) {
            if ($mixed) foreach ($mixed as &$el)
                $el = $this->escape($el);
        } else {
            $mixed = mysql_real_escape_string($mixed);
        }
        return $mixed;
    }

    /**
     * Implode $kv for "where" part of query
     * @var array
     * @return array
     */
    public function whereParamsImplode($kv) {
        if ($kv == '1') {
            return '1';
        }
        $params = array();
        foreach ($kv as $k => $v) {
            if (is_array($v)) {
                $v = $this->escape($v);
                $params[] = call_user_func_array(
                    'sprintf',
                    array_merge([$k], $v)
                );
            } else {
                $params[] = (string) $v;
            }
        }        

        return implode(', ', $params);
    }

    /**
     * Implode $kv for "set" part of query
     * @var array
     * @return array
     */
    public function paramsImplode($kv) {
        $params = array();
        foreach ($kv as $k => $v) {
            $v = $this->escape((string) $v); 
            $params[] = '`' . $k . '` = \'' . (string) $v . '\''; 
        }
        return implode(', ', $params);
    }
}
