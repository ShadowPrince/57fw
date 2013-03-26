<?php
namespace Test\Routing;

class RoutingTest extends \PHPUnit_Framework_Testcase {
    public function setUp() {
        $this->e = new \Core\Engine();
    }

    public function testRouterCreate() {
        $this->e->register('router', new \Routing\Router());
        $this->e->register('router_dispatcher', new \Routing\RouterDispatcher());
        $this->e->register('router_engage_dispatcher', new \Routing\RouterEngageDispatcher());

        return $this->e;
    }

    /** @depends testRouterCreate */
    public function testRegister($e) {
        $r = $e->router;

        $r->register('/', function ($req) {
            return 'x';
        });

        $r->register('/(\w+)/(?P<x>\w+)/', function () {})->bind('2');
        $r->register('/(\w+)/(\w+)/', function () {})->bind('3');

        try {
            $r->register('/', function (){})->bind('2');
            $this->fail('Route exists, exception must be thrown');
        } catch (\Routing\Ex\RouteExistsException $ex) {}

        $this->assertEquals($r->engage(new \Http\Request, '/')->getBody(), 'x');
    }

    /** 
     * @depends testRouterCreate 
     */
    public function testRegisterFullRegex($e) {
        $r = $e->router;
        $r->register('#^/UuDdLlRrBAstart$#i', function ($req) {
           return 'y'; 
        })->fullRegex()->bind('0');
        $this->assertEquals($r->engage(new \Http\Request, '/UuDdLlRrBAstart')->getBody(), 'y');

        $r
            ->register('#^/(\w+)/(?P<x>\w+)/$#i', function () {})
            ->bind('1')
            ->fullRegex();

        try { 
            $r->engage(new \Http\Request, '/UuDdLlRrBAstart/');
            $this->fail('add_trailing_slash = false, but works');
        } catch (\Routing\Ex\RouteNotFoundException $ex) {
        
        }

        return $e;
    }

    /** @depends testRegisterFullRegex */
    public function testDispatcher($e) {
        $e->router->register('/test/', function ($req) {
            return new \Http\Response($req->get('x'));
        });
        $e->register('http', new \Http\Http());

        $request = new \Http\Request(
            array('PATH_INFO' => '/test/'),
            array('x' => 'test')
        );

        $e->http->setRequest($request);
        $this->assertEquals($e->engage(), 'test');
    }

    /** @depends testRegisterFullRegex */
    public function testMKUrl($e) {
        $this->assertEquals($e->router->make('0'), '/UuDdLlRrBAstart');
        $this->assertEquals($e->router->make('1', 'x', array('x' => 'y')), '/x/y/');
        $this->assertEquals($e->router->make('2', 'x', array('x' => 'y')), '/x/y/');

        $this->assertEquals($e->router->make('1', 'x', '?x=y'), '/x/y/');
        $this->assertEquals($e->router->make('2', 'x', '?x=y'), '/x/y/');

        $this->assertEquals($e->router->make('2', 'x', 'y'), '/x/y/');

        try {
            $e->router->make('404');
            $this->fail('Route not exists, exception must be throwned');
        } catch (\Routing\Ex\RouteNotExistsException $ex) {}
    }

}
