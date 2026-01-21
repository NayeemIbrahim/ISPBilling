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
            $name = str_replace('-', '', ucwords($url[0], '-'));
            $controllerName = ucfirst($name) . 'Controller';

            if (file_exists(__DIR__ . '/../Controllers/' . $controllerName . '.php')) {
                $this->controller = $controllerName;
                array_shift($url);
            }
        }

        require_once __DIR__ . '/../Controllers/' . $this->controller . '.php';
        $controllerClass = '\\App\\Controllers\\' . $this->controller;
        $controllerName = $this->controller;

        // 2. Check for Method
        if (isset($url[0])) {
            if (method_exists($controllerClass, $url[0])) {
                $this->method = $url[0];
                array_shift($url);
            } else {
                $camelMethod = lcfirst(str_replace('-', '', ucwords($url[0], '-')));
                if (method_exists($controllerClass, $camelMethod)) {
                    $this->method = $camelMethod;
                    array_shift($url);
                }
            }
        }

        if (!method_exists($controllerClass, $this->method)) {
            $this->method = 'index';
        }

        // 3. Auth Middleware check
        \App\Middleware\AuthMiddleware::handle($controllerName, $this->method);

        $this->controller = new $controllerClass();

        // 4. Params
        $this->params = $url ? array_values($url) : [];

        // 5. Call method
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
            $url = $_GET['url'];
        } else {
            // Robust fallback for subfolder environments
            $requestUri = explode('?', $_SERVER['REQUEST_URI'])[0];
            $scriptName = $_SERVER['SCRIPT_NAME'];

            // Normalize path separators
            $requestUri = str_replace('\\', '/', $requestUri);
            $scriptName = str_replace('\\', '/', $scriptName);

            // Identify the base project directory
            $baseDir = str_replace('/public/index.php', '', $scriptName);
            $baseDir = str_replace('/index.php', '', $baseDir);

            $url = $requestUri;

            // Strip the base project directory from the request URI
            if ($baseDir !== '' && $baseDir !== '/' && strpos($url, $baseDir) === 0) {
                $url = substr($url, strlen($baseDir));
            }
        }

        $url = filter_var(rtrim($url, '/'), FILTER_SANITIZE_URL);
        return array_values(array_filter(explode('/', $url)));
    }
}
