<?php
namespace Core;

/**
 * Interface for components
 */
abstract class Component extends ConfiguredInstance {
    /**
     * @param \Core\Engine
     */
    public abstract function engage($e);
}
