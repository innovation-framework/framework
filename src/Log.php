<?php
namespace App\Library;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Carbon\Carbon;
use App\Library\Config;

/**
 * This class will handle logs of this application
 */
class Log extends Logger
{
    protected $frequency = 'monthly';
    protected $path;
    protected $configs;
    protected $carbon;


    function __construct($name = 'logger')
    {
        parent::__construct($name);

        $config = new Config('app');
        $this->configs = $config->get()->getObject()->app->logs;

        $this->carbon = new Carbon();
    }

    /**
     * This funtion will log info
     * 
     * @param  string $message
     * @return log success
     */
    public function writeWarning($message = '')
    {
        $this->frequency = $this->configs->warnings->frequency;
        $this->pushHandler(new StreamHandler($this->configs->warnings->path . DIRECTORY_SEPARATOR . $this->frequency . DIRECTORY_SEPARATOR . $this->getFile()), self::WARNING);

        return $this->addWarning($message);
    }

    /**
     * This funtion will write error logs
     * 
     * @param  string $message
     * @return log success
     */
    public function writeError($message = '')
    {
        $this->frequency = $this->configs->errors->frequency;
        $this->pushHandler(new StreamHandler($this->configs->errors->path . DIRECTORY_SEPARATOR . $this->frequency . DIRECTORY_SEPARATOR . $this->getFile()), self::ERROR);

        return $this->addError($message);
    }

    /**
     * This function get file log name
     * 
     * @return string fileName
     */
    protected function getFile()
    {
        if ($this->frequency == 'daily') {
            return $this->carbon->now()->format('Y-m-d') . '.log';
        } else if ($this->frequency == 'monthly') {
            return $this->carbon->now()->format('Y-m') . '.log';
        } else if ($this->frequency == 'yearly') {
            return $this->carbon->now()->format('Y') . '.log';
        } else if ($this->frequency == 'weekly') {
            return $this->carbon->now()->format('Y-W') . '.log';
        }
        return $this->carbon->now()->format('Y-m-d_H-m-s') . '.log';
    }
}