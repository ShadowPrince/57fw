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

        $r->register('/', '', function ($req) {
            return 'x';
        }, null);
        $this->assertEquals($r->engage(new \Http\Request, '/')->getBody(), 'x');
    }

    /** 
     * @depends testRouterCreate 
     */
    public function testRegisterFullRegex($e) {
        $r = $e->router;
        $r->register('#^/UuDdLlRrBAstart$#i', '', function ($req) {
           return 'y'; 
        }, null, true);
        $this->assertEquals($r->engage(new \Http\Request, '/UuDdLlRrBAstart')->getBody(), 'y');
        try { 
            $r->engage(new \Http\Request, '/UuDdLlRrBAstart/');
            $this->fail('add_trailing_slash = false, but works');
        } catch (\Routing\Ex\RouteNotFoundException $ex) {
        
        }

        return $e;
    }

    /** @depends testRegisterFullRegex */
    public function testDispatcher($e) {
        $e->router->register('/test/', '', function ($req) {
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

}
