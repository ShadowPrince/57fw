<?php
namespace Core;

/**
 * Interface for components
 */
abstract class Component extends \Core\ConfiguredInstance {
    /**
     * @param \Core\Engine
     */
    public abstract function engage($e);
}
