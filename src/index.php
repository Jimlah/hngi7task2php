<?php declare(strict_types=1);

// Register the autoloader
require_once '../vendor/autoload.php';

set_error_handler('error_handler');

// Get the requested URI
$uri = $_SERVER['REQUEST_URI'];
$uri = ltrim(parse_url($uri, PHP_URL_PATH), '/');

// Let's bind some objects to the application
TeamFlash\Registry::bind('config', require 'config.php');
TeamFlash\Registry::bind('resizer', (new TeamFlash\ResizeImage));

if (preg_match('@^([hw0-9\_,]+)/([\s\S]+)$@D', $uri, $matches)) {
	// We've got a valid API URI for resizing images on the fly.
	// But this route supports only the GET method. So we have to bail
	// out when a POST request is sent to this route.
	if ($_SERVER['REQUEST_METHOD'] != 'GET') {
		response('Only GET method is supported for this route', 405);
	}

	TeamFlash\Registry::get('resizer')->resizeImageOnTheFly($matches[1], $matches[2]);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	TeamFlash\Registry::get('resizer')->resizeImageOnPostMethod();
}

// If the request still reaches this point, then we can serve the frontend to the users.
// Most precisely, a HTML form for users to resize their images. For simplicity sake, I will
// only display a welcome page in the HTML format.
response("Welcome to <strong>TeamFlash</strong> image resizer microservice", [], false);
