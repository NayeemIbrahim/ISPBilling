<?php
namespace App\Core;

class Controller
{

    // Load Model (Manual include for simplicity in this pattern)
    public function model($model)
    {
        require_once __DIR__ . '/../Models/' . $model . '.php';
        return new $model();
    }

    // Load View
    public function view($view, $data = [])
    {
        // Extract data array to variables
        extract($data);

        // Define path
        $viewPath = __DIR__ . '/../../resources/views/' . $view . '.php';

        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            die("View does not exist: " . $view);
        }

    }

    // Redirect helper
    public function redirect($url)
    {
        // Check if url is full path or relative
        if (strpos($url, 'http') === 0) {
            header("Location: " . $url);
        } else {
            // Remove leading slash to avoid double slash with helper
            $cleanUrl = ltrim($url, '/');
            header("Location: " . url($cleanUrl));
        }
        exit();
    }
}
