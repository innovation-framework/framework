<?php
namespace App\Library;

/**
 * This class will handle Config object
 */
class Config
{
    // Path to all files path of this application
    protected $path = '';
    // This is file name config
    protected $fileName = '';
    // This is content of config file
    protected $configs;

    function __construct($file = '')
    {
        if (!$file) return [];

        $this->fileName = $file;
        $this->path = APP_ROOT . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . $this->fileName . '.php';
    }

    /**
     * This function will include config file into
     * Config object
     * 
     * @return array
     */
    private function readConfig()
    {
        if (file_exists($this->path)) {
            return include $this->path;
        }
        return [];
    }

    /**
     * This function will get all configs of file
     * 
     * @return $this
     */
    public function get()
    {
        $this->configs = [
            $this->fileName => $this->readConfig()
        ];
        return $this;
    }

    /**
     * This function will get config file as object
     * 
     * @return stdClass configs
     */
    public function getObject()
    {
        if (is_array($this->configs)) {
            return json_decode(json_encode($this->configs));
        }

        return $this->configs;
    }

    /**
     * This function will get cofig file as array
     * 
     * @return array configs
     */
    public function getArray()
    {
        if (is_object($this->configs)) {
            return json_decode(json_encode($this->configs), true);
        }
        return $this->configs;
    }
}