<?php
namespace Core;

/**
 * Class for services 
 */
abstract class Service extends ConfiguredInstance {
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
