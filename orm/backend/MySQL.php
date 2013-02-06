<?php
namespace Orm\Backend;

class MySQL implements GeneralBackend {
    public function __construct($e) {
        $this->e = $e;
    }

    public function select($manager, $kv) {
        $qw = 'SELECT * FROM %s WHERE %s';
        $kv = $this->paramsImplode($kv); 
        $qw = sprintf($qw, $manager->model->table, $kv);
        return array(
            'id' => 1,
            'name' => 'Vasya',
            'text' => 'Shit',
        );
    }
    public function update($manager, $kv, $wh) {
        $qw = 'UPDATE %s SET %s WHERE %s';
        $kv = $this->paramsImplode($kv);
        $wh = $this->paramsImplode($wh);
        print sprintf($qw, $manager->model->table, $kv, $wh);
    }
    public function insert($manager, $kv) {
        return 'INSERT INTO %s SET %s';
    }
    public function paramsImplode($kv) {
        $params = array();
        foreach ($kv as $k => $v) {
           $params[] = $k . ' = ' . $v; 
        }
        return implode(', ', $params);
    }
}
