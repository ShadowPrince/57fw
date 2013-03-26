<?php
namespace Core;

/** 
 * Interface for general dispatcher
 */
abstract class AppDispatcher extends ConfiguredInstance {
    /**
     * @param \Core\Engine
     */
    public abstract function engage($e);

    /**
     * @param string
     * @return \Core\AppDispatcher
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }   
}
