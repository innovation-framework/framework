<?php
namespace App\Library\Facade\Facades;
use App\Library\Facade\Facade;

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