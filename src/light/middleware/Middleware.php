<?php
namespace Light\Middleware;
use Light\Request;
use Light\Session;

/**
 * This is base Object that is used to filter all request 
 * before go to controller
 */
class Middleware
{
    protected $request;
    protected $ignore = [];
    protected $action;
    protected $session;
    protected $role = [];

    function __construct()
    {
        $this->session = new Session();
    }

    public function handle($request) {
        $this->request = $request;
    }

    /**
     * To all requests are gone controller so 
     * we need to pass this 
     * 
     * @return boolean
     */
    public function go()
    {
        if ($this->isAuthentication()) return true;

        return false;
    }

    /**
     * This function check current user is authentication or not
     * 
     * @return boolean
     */
    public function isAuthentication()
    {
        if (in_array($this->action, $this->ignore)) return true;

        // check timeout
        if ($this->session->isUserTimeout()) return false;
        if (!$this->hasPermission()) return false;
        return $this->session->get('user');
    }

    /**
     * This function is used to check current user permission
     * Example
     * + You're login with user role, you're setting userRole="logout" 
     *     so this user only view logout page
     * + You're login with admin role, you're setting userRole="*"
     *     so this user can view all pages in this application
     * 
     * @return boolean
     */
    public function hasPermission()
    {
        if (!$this->role) return true;

        $session = $this->session->get('user');
        foreach ($this->role as $role=> $permissions) {
            if (@$session['role'] == $role) {
                foreach ($permissions as $permission) {
                    if ($permission == '*') return true;
                    if ($this->action == $permission) return true;
                }
            }
        }

        return false;
    }

    /**
     * This function will set action for middleware object
     * 
     * @param string $action
     */
    public function setAction($action = '')
    {
        $this->action = $action;
    }

    /**
     * Get action of middleware object
     * 
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }
}