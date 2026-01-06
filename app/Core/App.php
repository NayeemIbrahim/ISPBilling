<?php
namespace App\Core;

class App
{
    protected $controller = 'PageController';
    protected $method = 'index';
    protected $params = [];

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
            if (method_exists($this->controller, $url[0])) {
                $this->method = $url[0];
                array_shift($url);
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

    public function parseUrl()
    {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
    }
}
