<?php

class MainTestSuite extends PHPUnit_Framework_TestSuite {
    public static function suite() {
        $suite = new MainTestSuite();
        $suite->addTestSuite('Test\Core\EngineTest');
        $suite->addTestSuite('Test\Http\HttpTest');
        $suite->addTestSuite('Test\Routing\RoutingTest');
        $suite->addTestSuite('Test\Twig\TwigTest');
        $suite->addTestSuite('Test\Db\DBTest');
        $suite->addTestSuite('Test\Orm\ModelTest');

        return $suite;
    }

}
