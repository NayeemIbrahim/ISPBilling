<?php
namespace App\Core;

/**
 * Core Application Router
 * 
 * Handles URL parsing and controller/method dispatching.
 */
class App
{
    /**
     * Default controller name.
     * @var string
     */
    protected $controller = 'PageController';

    /**
     * Default method name.
     * @var string
     */
    protected $method = 'index';

    /**
     * URL parameters.
     * @var array
     */
    protected $params = [];

    /**
     * Initialize the application and dispatch the request.
     */
    public function __construct()
    {
        $url = $this->parseUrl();

        // 1. Check for Controller
        if (isset($url[0])) {
            // Convert kebab-case (complain-list) to PascalCase (ComplainList)
            $name = str_replace('-', '', ucwords($url[0], '-'));
            $controllerName = ucfirst($name) . 'Controller';

            if (file_exists(__DIR__ . '/../Controllers/' . $controllerName . '.php')) {
                $this->controller = $controllerName;
                array_shift($url);
            }
        }

        require_once __DIR__ . '/../Controllers/' . $this->controller . '.php';
        $this->controller = new ('\\App\\Controllers\\' . $this->controller);

        // 2. Check for Method
        if (isset($url[0])) {
            // Check exact match
            if (method_exists($this->controller, $url[0])) {
                $this->method = $url[0];
                array_shift($url);
            }
            // Check camelCase match (column-preview -> columnPreview)
            else {
                $camelMethod = lcfirst(str_replace('-', '', ucwords($url[0], '-')));
                if (method_exists($this->controller, $camelMethod)) {
                    $this->method = $camelMethod;
                    array_shift($url);
                }
            }
        }

        // Safety: ensure method exists, default to index
        if (!method_exists($this->controller, $this->method)) {
            $this->method = 'index';
        }

        // 3. Params
        $this->params = $url ? array_values($url) : [];

        // 4. Call method
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    /**
     * Parse the URL into an array.
     * 
     * @return array|null The parsed URL segments.
     */
    public function parseUrl()
    {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return null;
    }
}
