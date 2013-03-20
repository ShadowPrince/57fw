<?php

/**
 * @TODO: orm testcase
 */
class TestCase extends PHPUnit_Framework_TestSuite {
    public static function suite() {
        $suite = new TestCase();
        $suite->addTestSuite('Test\Core\EngineTest');
        $suite->addTestSuite('Test\Http\HttpTest');
        $suite->addTestSuite('Test\Routing\RoutingTest');
        $suite->addTestSuite('Test\Twig\TwigTest');
        $suite->addTestSuite('Test\Orm\DBTest');
        $suite->addTestSuite('Test\Orm\ModelTest');

        return $suite;
    }

}
