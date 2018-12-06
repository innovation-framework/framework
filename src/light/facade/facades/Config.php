<?php
namespace Light\Facade\Facades;
use Light\Facade\Facade;

/**
 * Config proxy
 */
class Config extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'config'; }
}