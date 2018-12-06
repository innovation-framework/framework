<?php
namespace App\Library;
use App\Library\Config;

/**
 * This class will handle Language in this application
 */
class Language extends Config
{
    // Path to language file
    protected $path = '';

    /**
     * This property will tell Language class, we will read which file?
     * Example
     * + en
     * + vi
     * + fr
     */
    protected $language = '';

    // This is file name of language
    protected $fileName;

    // This property will contain all config contents
    protected $configs;

    function __construct($language = 'en', $fileName = 'app')
    {
        $this->language = $language;
        $this->fileName = $fileName;
        $this->path = APP_ROOT . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR . $this->language . DIRECTORY_SEPARATOR . $this->fileName . '.php';
    }
}