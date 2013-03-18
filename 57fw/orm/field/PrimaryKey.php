<?php
namespace Orm\Field;

class PrimaryKey extends Field {
    protected $params = array(
        'auto_increment' => true
    );
}
