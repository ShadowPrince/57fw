<?php
namespace Orm\Field;

class ForeignList extends \Orm\Field\ForeignKey {
    protected $type = 'text';
    protected $value = '';

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

        $this->value = \Orm\QuerySet::implode($ids);
    }

    public function getValue() {
        return new \Orm\QuerySet(array($this->manager, 'get'),
            \Orm\QuerySet::explode($this->value), false);
    }
}
