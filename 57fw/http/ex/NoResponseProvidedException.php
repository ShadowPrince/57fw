<?php
namespace Http\Ex;

class NoResponseProvidedException extends HttpException {
    public function __construct() {

        parent::__construct('No response provided!');
    }
}
