<?php
namespace Orm\Ex;

class RowNotFoundException extends OrmException {
    public function __construct($search) {
        parent::__construct('Row not found: ' . $search);
    }
}
