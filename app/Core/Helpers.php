<?php
/**
 * Core Helper Functions
 *
 * This file contains global helper functions for URL and asset management.
 */

// Define Base URL dynamically
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" :
    "http://";
$domainName = $_SERVER['HTTP_HOST'];
$scriptPath = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
$scriptPath = str_replace('/public', '', $scriptPath); // Hide public folder from URLs

define('BASE_URL', $protocol . $domainName . $scriptPath . '/');

/**
 * Generate a dynamic asset URL.
 *
 * @param string $path Path to the asset relative to the public directory.
 * @return string The full absolute URL to the asset.
 */
function asset($path)
{
    return BASE_URL . ltrim($path, '/');
}

/**
 * Generate a dynamic URL for internal routing.
 *
 * @param string $path The internal route path.
 * @return string The full absolute URL for the route.
 */
function url($path)
{
    return BASE_URL . ltrim($path, '/');
}