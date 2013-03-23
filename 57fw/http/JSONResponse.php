<?php
namespace Http;

class JSONResponse extends Response {

    public function getBody() {
        return json_encode(parent::getBody());
    }
}
