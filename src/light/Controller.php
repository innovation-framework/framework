<?php
namespace Light;
use Light\HttpFoundation\BaseResponse;
use Light\Middleware\Middleware;
use Light\Language;
use Light\Request;
use Light\Session;

/**
 * This class will handler all controllers
 */
abstract class Controller
{
    protected $response;
    protected $middleware;
    protected $language;
    protected $request;

    /**
     * Parameters from the matched route
     * @var array
     */
    protected $route_params = [];
    /**
     * Class constructor
     *
     * @param array $route_params  Parameters from the route
     *
     * @return void
     */
    public function __construct($route_params)
    {
        $this->route_params = $route_params;
        $this->response = BaseResponse::create();
        $this->middleware = new Middleware();
        $this->request = new Request();
        $this->setLanguage();
    }

    /**
     * This function to defined language for all routes
     *
     * @return void;
     */
    public function setLanguage()
    {
        $headers = $this->request->getHeaders();

        $xLanguage = @$headers['X-Language'] ?: 'en';
        $language = new Language($this->getLanguageSession($xLanguage));
        $this->language = $language->get()->getObject()->app;
    }

    /**
     * This function will get current language
     * 
     * @param  string $language
     * @return string: vi|en|fr|...
     */
    public function getLanguageSession($language='en')
    {
        $session = new Session();
        $session->setSession('language', $language);

        return $session->get('language');
    }

    /**
     * Magic method called when a non-existent or inaccessible method is
     * called on an object of this class. Used to execute before and after
     * filter methods on action methods. Action methods need to be named
     * with an "Action" suffix, e.g. indexAction, showAction etc.
     *
     * @param string $name  Method name
     * @param array $args Arguments passed to the method
     *
     * @return void
     */
    public function __call($name, $args)
    {
        $this->middleware->setAction($name);

        if ($this->middleware->go()) {
            $method = $name . 'Action';
            if (method_exists($this, $method)) {
                if ($this->before() !== false) {
                    call_user_func_array([$this, $method], $args);
                    $this->after();
                }
            } else {
                throw new \Exception("Method $method not found in controller " . get_class($this));
            }
            return;
        }

        return $this->response->setContent([
            'error' => ['message' => $this->language->errors->notPermission],
            'code' => 200
        ]);
    }
    /**
     * Before filter - called before an action method.
     *
     * @return void
     */
    protected function before()
    {
    }
    /**
     * After filter - called after an action method.
     *
     * @return void
     */
    protected function after()
    {
    }
}