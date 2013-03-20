<?php
namespace Test\Orm;

/**
 * @TODO
 */
class QuerySetTest extends \PHPUnit_Framework_Testcase {
    public function testCreate() {
        $model_test = new \Test\Orm\ModelTest();
        $e = $model_test->testPrepare($model_test->testCreate());

        return $e;
    }

    /** @depends testCreate */
    public function testFill($e, $kv) {
        $man = $e->man('\Test\Orm\Model\BarModel');
        
        $instance = $man->buildInstance($kv);
    }

}
