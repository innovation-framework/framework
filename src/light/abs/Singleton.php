<?php
namespace Light\Abs;

/**
 * This class handle singleton object
 * and this Base class but it haven't implemented 
 * finish, will need to complete in the future
 */
class Singleton
{
    protected static $instance = null;
    /**
     * Call this method to get singleton
     *
     */
    public static function getInstance()
    {
        $cls = get_called_class(); // late-static-bound class name
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static;
        }
        return self::$instances[$cls];
    }
}