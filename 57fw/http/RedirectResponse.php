<?php
namespace Http;

class RedirectResponse extends Response {
    public function __construct($url) {
        parent::__construct(/**@TODO: html redirect here**/);

        $this->redirect($url);
    }
}
