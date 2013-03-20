<?php
namespace Test\Orm;

// @TODO: replace this with mock
class DummyManager {
    public function getTable() {
       return 'test'; 
    }
}

class DBTest extends \PHPUnit_Framework_Testcase {
    public function testCreateDB () {
        $e = new \Core\Engine();
        $e->register('db', new \Orm\Backend\PDO\PDO(array(
            'uri' => 'mysql:host=localhost;dbname=57fw',
            'user' => 'root',
            'password' => '1',
            'debug' => true
        )));
        return $e->db;
    }

    /** @depends testCreateDB */
    public function testQuery($db) {
        $db->buildExecute('SHOW TABLES');
        $db->buildExecute('CREATE TABLE test (f1 TEXT, f2 TEXT, id INT PRIMARY KEY AUTO_INCREMENT)');
        $db->buildExecute('INSERT INTO test SET f1 = "x", f2 = "x"');

        return $db;
    }

    /** @depends testQuery */
    public function testFetch($db) {
        $kv = $db->buildExecuteFetch('SELECT * FROM test WHERE id = 1');
        $this->assertEquals($kv, array(array(
            'f1' => 'x', 
            'f2' => 'x',
            'id' => '1' 
        )));

        return $db;
    }
    
    /** @depends testFetch */
    public function testDrop($db) {
        $db->buildExecute('DROP TABLE test');
    }
}
