<?php
/**
 * Root Redirector
 * 
 * Proxies requests to the public directory for environments where 
 * mod_rewrite or AllowOverride might be restricted.
 */
require_once __DIR__ . '/public/index.php';
