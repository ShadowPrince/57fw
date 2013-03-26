<?php
namespace Test\Twig;

class TwigTest extends \PHPUnit_Framework_Testcase {
    public function testTwigCreate() {
        $e = new \Core\Engine();
        new \Twig\Twig(array(
            'path' => '.',
            'cache' => '.',
        ));
        $e->register('twig', new \Twig\Twig(array(
            'loader' => 'string',
            'debug' => true
        )));
        $e->register('http', new \Http\Http());
        $e->register('router', new \Routing\Router());

        return $e;
    }

    /** @depends testTwigCreate */
    public function testTwigEngage($e) {
        $e->engage();

        return $e;
    }

    /** @depends testTwigEngage */
    public function testTwigMethodCall($e) {
        $this->assertEquals($e->twig->render('123', array()), '123');
    }

}
