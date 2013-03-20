<?php
namespace Test\Http;

class HttpTest extends \PHPUnit_Framework_TestCase {
    public function testCreateRequest() {
        $req = new \Http\Request(
            array(
                'server' => true,
                'SERVER_NAME' => 'localhost',
                'REQUEST_URI' => '/',
                'PATH_INFO' => '/'
            ),

            array('get' => true),
            array('post' => true),
            array('files' => true),
            array('cookies' => true)
        );

        return $req;
    }

    public function testCreateResponse() {
        $res = new \Http\Response('response');
        
        $this->assertEquals($res->getBody(), 'response');
        return $res;
    }


    /** @depends testCreateRequest */
    public function testRequestVars($r) {
        $this->assertTrue($r->get('get'));
        $this->assertTrue($r->post('post'));
        $this->assertTrue($r->cookie('cookies'));

        $this->assertNull($r->get('get_notexists'));
        $this->assertNull($r->post('post_notexists'));
        $this->assertNull($r->cookie('cookies_notexists'));
    }

    /** @depends testCreateRequest */
    public function testRequestServerVars($r) {
        $this->assertEquals($r->getFullURL(), 'localhost/');
        $this->assertEquals($r->getRequestPath(), '/');
        $this->assertEquals($r->getDomain(), 'localhost');
    }

    
    /** @depends testCreateResponse */
    public function testResponseCookies($r) {
        $r->setCookie('k', 'v');
        $r->setCookies(array(array('k1', 'v'), array('k2', 'v')));

        $this->assertEquals($r->getCookies(), array(
            array('k', 'v'), 
            array('k1', 'v'), 
            array('k2', 'v') 
        ));

    }

    /** @depends testCreateResponse */
    public function testResponseHeaders($r) {
        $r->addHeader('header');
        $r->addHeaders(array('h1', 'h2'));

        $this->assertEquals($r->getHeaders(), array('header', 'h1', 'h2'));

        return $r;
    }
    
    /** @depends testCreateResponse */
    public function testResponseBody($r) {
        $this->assertEquals($r->getBody(), 'response');
        $this->assertEquals($r->setBody('z')->getBody(), 'z');
    }


    /** @depends testCreateResponse */
    public function testResponseEngage($r) {
        global $_COOKIE;
        $_COOKIE = array();
        $http = new \Http\Http(array(
            'globals' => true
        ));
        $r->setBody('abc');
        $http->setResponse($r);
        $str = @($http->engageResponse());
        $this->assertEquals($str, 'abc');
    }

}
