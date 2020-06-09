<?php declare(strict_types=1);

require '../vendor/autoload.php';

set_error_handler('error_handler');

// Let's parse the URI and search for our desired path
$uri     = ltrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$uriData = explode('/', $uri);

// Basically we do not support more than 2 GET parameters
if (count($uriData) > 1) {
	return response('Resource Not Found', 404);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['img'])) {
	$imageDimensions = isset($uriData[0]) ? $uriData[0] : null;
	$postedImage = filter_input(INPUT_GET, 'img', FILTER_VALIDATE_URL);

	if (!$postedImage) {
		// response('Wrong syntax for URL', 400);
	}

	if (is_null($imageDimensions)) {
		response('Wrong syntax for URL', 400);
	}

	return resizeImageViaGet($imageDimensions, $postedImage);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST)) {
	return resizeImageViaPost();
}

// Default response
response('Welcome to Team Flash');
