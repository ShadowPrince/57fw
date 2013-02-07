<?php
namespace Orm\Backend;

class MySQL implements GeneralBackend {
    public function __construct($e, $connection) {
        $this->e = $e;
        mysql_connect($connection['host'], $connection['user'], $connection['password']);
        mysql_select_db($connection['database']);
    }

    public function select($manager, $wh) {
        return $this->fetchArrayResult($this->executeQuery($this->buildFQuery(
            'SELECT * FROM %s WHERE %s', 
            $manager->model->table,
            $this->whereParamsImplode($wh)
        )));
    }

    public function update($manager, $kv, $wh) {
        $this->executeQuery($this->buildFQuery(
            'UPDATE %s SET %s WHERE %s',
            $manager->model->table,
            $kv,
            $this->whereParamsImplode($wh)
        ));

        return $this->select($manager, [
            "`%s` = '%s'" => [$manager->model->getPrimaryKey()->name, $kv['id']]    
        ]);
    }

    public function insert($manager, $kv) {
        $this->executeQuery($this->buildFQuery(
            'INSERT INTO %s SET %s',
            $manager->model->table,
            $kv
        ));
        if (!mysql_insert_id()) 
            return [];

        return $this->select($manager, [
            "`%s` = '%s'" => [$manager->model->getPrimaryKey()->name, mysql_insert_id()]
        ]);
    }

    public function prepare($manager) {
        $fields = $manager->model->getFields();
        if ($fields) foreach ($fields as &$field) {
           $field = $field->getName() . ' ' . strtoupper($field->getType()); 
        }
        $this->executeQuery($this->buildFQuery(
            'CREATE TABLE IF NOT EXISTS %s (%s)',
            $manager->model->table,
            implode(', ', $fields)
        )); 
    }

    public function executeQuery($qw) {
        $res = mysql_query($qw) or die(mysql_error());
        return $res;
    }

    public function fetchArrayResult($res) {
        $data = [];
        while ($row = mysql_fetch_assoc($res))
            $data[] = $row;
        return $data;
    }

    public function fetchSingleResult($res) {
        return mysql_fetch_array($res);
    }
    
    public function buildFQuery($qw) {
        $args = func_get_args();
        foreach ($args as &$arg)
            if (is_array($arg))
                $arg = $this->paramsImplode($arg);
        return call_user_func_array('sprintf', $args); 
    }

    public function escape($mixed) {
        if (is_array($mixed)) {
            if ($mixed) foreach ($mixed as &$el)
                $el = $this->escape($el);
        } else {
            $mixed = mysql_real_escape_string($mixed);
        }
        return $mixed;
    }

    public function whereParamsImplode($kv) {
        $params = array();
        foreach ($kv as $k => $v) {
            if (is_array($v)) {
                $v = $this->escape($v);
                $params[] = call_user_func_array(
                    'sprintf',
                    array_merge([$k], $v)
                );
            } else {
                $params[] = $v;
            }
        }        

        return implode(', ', $params);
    }

    public function paramsImplode($kv) {
        $params = array();
        foreach ($kv as $k => $v) {
            $v = $this->escape($v); 
            $params[] = '`' . $k . '` = \'' . $v . '\''; 
        }
        return implode(', ', $params);
    }
}
