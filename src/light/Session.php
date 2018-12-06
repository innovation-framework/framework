<?php
namespace Light;
use Light\Config;

/**
 * This class will handle Session object in this application
 */
class Session
{
    protected $session;
    protected $configs;

    function __construct()
    {
        $this->session = $_SESSION;
        $configObj = new Config('app');
        if ($configObj instanceof Config) $this->configs = $configObj->get()->getObject();
    }

    /**
     * This function will start session of this application
     * 
     * @return $this
     */
    public function start()
    {
        // Start session
        ob_start();
        session_start();

        return $this;
    }

    /**
     * This function will clear session of this application
     * 
     * @return $this
     */
    public function clearAll()
    {
        session_destroy();
        return $this;
    }

    /**
     * This function will current sessions
     * 
     * @return array session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * This function will get value with a key of current sessions
     * 
     * @param  string $key
     * @return array|null
     */
    public function get($key = '')
    {
        return @$this->session[$key];
    }

    /**
     * This function will set new key=>value for current sessions
     * 
     * @param string $key
     * @param string $value
     */
    public function setSession($key = '', $value = '')
    {
        if (!$key || !$value) return $this;

        $_SESSION[$key] = $value;
        $this->session = $_SESSION;
    }

    /**
     * This fuction will check current session that 
     * is timeout or not yet after $seconds seconds
     * 
     * @param  int $seconds - how long is this sesison timeout
     * @param  int $lastest - is created time session
     * @return boolean
     */
    public function hasTimeout($seconds, $lastest)
    {
        if (!$seconds || !$lastest) return false;

        if ($seconds + $lastest < time()) {
            $this->clearAll();
            return true;
        }
        return false;
    }

    /**
     * This function will check current user is timeout or haven't yet
     * 
     * @return boolean
     */
    public function isUserTimeout()
    {
        if (!$user = $this->get('user')) return true;
        //timeout after 7200s (2hours)
        $seconds = $this->configs->app->session->timeout;
        $initTime = @$user['lastestLogin'];

        return $this->hasTimeout($seconds, $initTime);
    }
}