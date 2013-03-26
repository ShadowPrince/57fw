<?php
namespace Test\Core;

class SimpleDispatcher extends \Core\AppDispatcher {
    public $x = 'test';

    public function engage($e) {
        if (!$this->config('no'))
            return $e->simple->x;
    }
}

class Dummy {
    public function f(Dummy $f) {

    }
}

class Foo {

}

class EngineTest extends \PHPUnit_Framework_TestCase {
    public function testCreate() {
        global $_SERVER;
        $_SERVER = array(
            'PATH_INFO' => '/'
        );  

        $e = new \Core\Engine(array('k' => 'v'));
        $this->assertEquals($e->config('k'), 'v');

        return $e;
    }

    /**
     * @depends testCreate
     */
    public function testConfiguredInstance($e) {
        $e->setParam('k', 'v');
        $this->assertEquals($e->config('k'), 'v');

        return $e;
    }

    /** @depends testCreate */
    public function testRegister($e) {
        $config = array('key' => 'value');
        $e->register('simple', new SimpleDispatcher($config));

        $e->registerArray(array(
            'method' => (function () {
                return 'x';
            }),
            'simple2' => new SimpleDispatcher(array('no'=>1))
        ));

        $this->assertEquals(count($e->getApps()), 3);

        return $e;
    }

    /** @depends testRegister */
    public function testAppGetter($e) {
        $this->assertEquals($e->method(), 'x');

        try {
            $e->not_found_123;
            $this->fail('Engine __get of not exists app must throw exception!');
        } catch (\Core\Ex\AppNotExistsException $ex) {}
    }

    /** @depends testRegister */
    public function testAppConfig($e) {
        $this->assertEquals($e->simple->config('key'), 'value');

        return $e;
    }
    /**
     * @depends testRegister
     */
    public function testEngage($e) {
        $this->assertEquals($e->engage(), $e->simple->engage($e));

        return $e;
    }

    /** 
     * @depends testCreate 
     */
    public function testRecoverableErrorHandler($e) {
        set_error_handler('\Core\Engine::fatalErrorHandler');
        try {
            (new Dummy())->f(new Foo());
        } catch (\ErrorException $ex) {
            
        }

        return $e;
    }
}
