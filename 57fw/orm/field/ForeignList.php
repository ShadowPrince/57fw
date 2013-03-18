<?php
namespace Orm\Field;

class ForeignList extends \Orm\Field\ForeignKey {
    protected $type = 'text';

    public function setValue($list) {
        $this->list = $list;
        $ids = array();
        $model = $this->getModel();
        if ($list) foreach ($list as $instance) {
            if ($instance instanceof $model) {
                $ids[] = $instance->getPKey()->getValue();
            } else throw new \Orm\Ex\FieldValueException(
                $this->getName(),
                'instance of ' . get_class($instance)
            );
        }

        $this->value = \Orm\ResultSet::implode($ids);
        $this->changed = 1;
    }

    public function getValue() {
        return new \Orm\ResultSet(array($this->manager, 'get'),
            \Orm\ResultSet::explode($this->value), false);
    }
}
