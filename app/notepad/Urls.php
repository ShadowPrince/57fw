<?php
namespace App\Notepad;

class Urls implements \Routing\Urls {
    public function init($e) {
        $e->router()->register('##', function ($e) {
        });
    }   
} 
