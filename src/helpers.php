<?php declare(strict_types=1);

if (! function_exists('response')) {
	/**
	 * Emit response to client.
	 *
	 * @param  array|string  $body_content  The contents of the response.
	 * @param  array|string  $headers       The HTTP headers
	 * @param  bool          $json          Set the content type to json
	 * @return void
	 */
	function response($body_content, $headers = '', bool $json = true): void
	{
		if (empty($headers)) {
			http_response_code(200);
			header('Content-Type: text/html;charset=UTF-8');
		}

		if (is_int($headers)) {
			http_response_code($headers);
		}

		if (isset($headers['headers'])) {
			if (is_array($headers['headers'])) {
				foreach ($headers['headers'] as $header) {
					header($header);
				}
			} else {
				header($headers['headers']);
			}
		}

		if (isset($headers['code'])) {
			http_response_code($headers['code']);
		}

		if ($json) {
			header('Content-Type: application/json');
			echo json_encode($body_content);

			exit;
		}

		echo $body_content;
		// echo htmlspecialchars((string) $body_content, ENT_QUOTES, 'UTF-8');
		exit;
	}
}

if (! function_exists('error_handler')) {
	/**
	 * Handle errors in an exception manner.
	 *
	 * @param  int     $severity
	 * @param  string  $set_error_handler
	 * @param  string  $filename
	 * @param  string  $lineno
	 * @return void
	 */
	function error_handler($severity, $message, $filename, $lineno): void
	{
		if (!(error_reporting() & $severity)) {
			return;
		}

		throw new ErrorException($message, 0, $severity, $filename, $lineno);
	}
}

if (! function_exists('formatBytes')) {
	/**
	 * Format bytes to kilobytes, megabytes, gigabytes, etc.
	 *
	 * @param  int  $bytes
	 * @param  int  $precision
	 * @return int
	 */
	function formatBytes($bytes, $precision = 2) { 
	    $units = array('B', 'KB', 'MB', 'GB', 'TB'); 

	    $bytes = max($bytes, 0); 
	    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
	    $pow = min($pow, count($units) - 1); 

	    $bytes /= (1 << (10 * $pow)); 
	    return round($bytes, $precision) . ' ' . $units[$pow]; 
	}
}
