<?php
namespace Orm\Field;

class ForeignList extends ForeignKey {
    protected $type = 'text';

    public function setValue($list) {
        $this->list = $list;
        $ids = array();
        $model = $this->getModel();
        if ($list) foreach ($list as $instance) {
            if ($instance instanceof $model) {
                $ids[] = $instance->getPKey()->getValue();
            } else if ((string) (int) $instance == $instance) {
                $ids[] = $instance;
            } else if ($instance instanceof \Orm\Model) {
                throw new \Orm\Ex\FieldValueException(
                    $this,
                    'instance of ' . get_class($instance)
                );
            } else throw new \Orm\Ex\FieldValueException(
                $this,
                'instance of ' . $instance
            );
        }

        $this->value = \Orm\QuerySet::implode($ids);
        $this->changed = 1;
    }

    public function getValue() {
        $qs = $this->manager->find();
        return $qs->setSet($qs::explode($this->value))->setExecuted(true);
    }
}
