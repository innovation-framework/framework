<?php
namespace App\Library\Facade;
use App\Library\ArrayHandler;

/**
 * This class will mapping between facade accessor with Object
 */
class Proxy
{
    protected $containers = [];

    function __construct()
    {
        $this->generateContainers();
    }

    public function get($facade='')
    {
        if (!$facade) return null;

        return @$this->containers[$facade];
    }

    private function generateContainers()
    {
        $arrayHandler = new ArrayHandler();
        $this->containers = $arrayHandler->find($GLOBALS['filesLibrary'], '.php');

        $mapKeys = [];
        foreach ($this->containers as $containerKey => $container) {
            $container = str_replace([APP_ROOT, '.php'], '', $container);
            $mapKey = strtolower(end(explode(DIRECTORY_SEPARATOR, $container)));
            if (in_array($mapKey, $mapKeys)) $mapKey = ltrim(strtolower(str_replace(DIRECTORY_SEPARATOR, '_', $container)), '_');

            $this->containers[$mapKey] = $container;
            $mapKeys[] = $mapKey;
            unset($this->containers[$containerKey]);
        }
    }
}