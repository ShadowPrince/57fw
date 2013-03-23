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
}
