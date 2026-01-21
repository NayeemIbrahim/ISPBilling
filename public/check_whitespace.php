<?php
$files = [
    '../app/Core/App.php',
    '../app/Core/Helpers.php',
    '../app/Core/Controller.php',
    '../app/Controllers/AuthController.php',
    '../app/Controllers/UserController.php',
    '../app/Middleware/AuthMiddleware.php',
    'index.php',
    '../index.php'
];

foreach ($files as $file) {
    if (!file_exists($file))
        continue;
    $content = file_get_contents($file);
    if (strpos($content, '<?php') !== 0) {
        echo "ISSUE: $file starts with something else than <?php\n";
        echo "First 10 chars: '" . substr($content, 0, 10) . "'\n";
    } else {
        echo "OK: $file\n";
    }
}
