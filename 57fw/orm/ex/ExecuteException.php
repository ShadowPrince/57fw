<?php
namespace Orm\Ex;

class ExecuteException extends \Orm\Ex\OrmException {
    public function __construct($error, $qw) {
        parent::__construct('MySQL execute error "' . $error . '" on query "' . $qw . '"');
    }
} 
