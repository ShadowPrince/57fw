<?php
namespace Orm\Field;

class IntPKey extends PrimaryKey {
    protected $type = 'int';
    protected $value = 0;
}
