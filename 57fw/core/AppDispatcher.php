<?php
namespace Core;

/** 
 * Interface for dispatchers
 */
abstract class AppDispatcher extends \Core\ConfiguredInstance {
    /**
     * @param \Core\Engine
     */
    public abstract function engage($e);
}
