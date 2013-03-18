<?php
namespace Core;

/** 
 * Interface for general dispatcher
 */
abstract class AppDispatcher extends \Core\ConfiguredInstance {
    /**
     * @param \Core\Engine
     */
    public abstract function engage($e);
}
