<?php

// Define Base URL dynamically or statically
// Dynamically: http://localhost/HK%20ISP%20Billing/public/
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$domainName = $_SERVER['HTTP_HOST'];
$scriptPath = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
define('BASE_URL', $protocol . $domainName . $scriptPath . '/');

// Helper function to get asset URL
function asset($path)
{
    return BASE_URL . ltrim($path, '/');
}

// Helper for internal links (same as asset for now, but semantically distinct)
function url($path)
{
    return BASE_URL . ltrim($path, '/');
}
