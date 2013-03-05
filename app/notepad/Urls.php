<?php
namespace App\Notepad;

class Urls implements \Routing\Urls {
    public function init($e) {
        $e->router()->register('##', function ($e) {
            foreach ($e->man(new Model\Note)->find(array(
                '`created` < "%s"' => array(new \DateTime())
            ), 'limit = 1, order = id desc') as $i) {
                var_dump($i->id);
            }
        });
    }   
} 
