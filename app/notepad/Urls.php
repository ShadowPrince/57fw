<?php
namespace App\Notepad;

class Urls implements \Routing\Urls {
    public function init($e) {
        $e->router()->register('##', function ($e) {
            foreach ($e->man(new Model\Note)->find(array(
                '`cat` = %s' => array($e->man(new Model\NoteCat)->get(1)->id)
            )) as $i) {
                var_dump($i);
                break;
            }

            foreach ($e->man(new Model\Simple)->find(1) as $s) {
                var_dump($s->id);
            }
        });
    }   
} 
