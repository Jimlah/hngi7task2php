<?php declare(strict_types=1);

use GuzzleHttp\Client;

if (! function_exists('response')) {
	/**
	 * Emit response to client.
	 *
	 * @param  array|string  $body_content  The contents of the response.
	 * @param  int           $status_code   The HTTP status code.
	 * @param  bool          $json          Set the content type to json
	 * @return void
	 */
	function response($body_content, int $status_code = 200, bool $json = true): void
	{
		http_response_code($status_code);
		
		if ($json) {
			header('Content-Type: application/json');
			echo json_encode($body_content);

			exit;
		}

		echo htmlspecialchars((string) $body_content, ENT_QUOTES, 'UTF-8');
		exit;
	}
}

if (! function_exists('resizeImageViaGet')) {
	/**
	 * Resize image posted via the GET method
	 *
	 * @param  string       $dimensions  Image dimensions
	 * @param  string|null  $image_uri   The link to the image resource
	 * @return void
	 */
	function resizeImageViaGet(string $dimensions, ?string $image_uri)
	{
		$dimensions = explode(',', $dimensions);

		// We only expect the height and width dimensions
		if (count($dimensions) > 2) {
			return response([
				'error' => true,
				'msg' => 'Wrong Format. Consult the documentation on proper consumption of the API',
			], 400);
		}

		foreach ($dimensions as $dimension) {
			$options = explode('_', $dimension);

			if ($options[0] === 'h') {
				$height = $options[1];
			}
			if ($options[0] === 'w') {
				$width = $options[1];
			}

		}

		$height = isset($height) ? $height : -1;
		$width  = isset($width) ? $width : 'auto';

		return processImage((int)$height, (int)$width, $image_uri, ['url' => true]);
	}
}

if (! function_exists('resizeImageViaPost')) {
	/**
	 * Resize image posted via the GET method
	 *
	 * @param  string       $dimensions  Image dimensions
	 * @param  string|null  $image_uri   The link to the image resource
	 * @return void
	 */
	function resizeImageViaPost()
	{
		$width  = isset($_POST['i_width']) ? (empty($_POST['i_width']) ? -1 : $_POST['i_width']) : 'auto';
		$height = isset($_POST['i_height']) ? (empty($_POST['i_height']) ? -1 : $_POST['i_height']) : -1;

		if (isset($_FILES['rimage'])) {
			$filename = $_FILES['rimage']['tmp_name'];
			$originalFName = $_FILES['rimage']['name'];
		} else {
			response('No image submitted for procession', 422);
		}

		if ($_FILES['rimage']['error'] == UPLOAD_ERR_OK) {
			return processImage((int)$height, (int)$width, $filename, ['url' => false, 'oN' => $originalFName]);
		}

		response('Unable to process image', 400);
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


if (! function_exists('processImage')) {
	/**
	 * Resize image posted via the GET method
	 *
	 * @param  int      $height
	 * @param  int      $width
	 * @param  mixed    $imageData
	 * @param  sring[]  $options
	 * @return mixed
	 */
	function processImage(int $height, int $width, $imageData, array $options = [])
	{
		$config = require 'config.php';
		if (is_null($imageData)) {
			// There is no image to be process, we bail out
			return response([
				'error' => true,
				'img' => 'No image submitted for procession',
			], 422);
		}

		if (isset($options['url']) && true === $options['url']) {
			$data    = getImageDataFromURL($imageData, ['width' => $width, 'height' => $height]);
			$cache   = $data['cache'];
			$data    = $data['file'];
			$display = true;
		} else {
			$filename = $width . 'x'. $height . $options['oN'];
			$cache   = file_exists('uploads/'.$filename);

			if (move_uploaded_file($imageData, $data = 'pimage/'.$filename)) {
			} else {
				response('Cannot fetch image', 400);
			}

			$display = false;
		}

		try {
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			if(! strstr(finfo_file($finfo, $data), 'image')) {
				unlink($data);
				response([
					'error' => true,
					'msg' => 'Invalid image'
				], 415);
			}
		} catch (ErrorException $e) {
			response([
				'error' => true,
				'msg' => 'Invalid image'
			], 415);
		}

		list($original_width, $original_height, $mime)   = getimagesize($data);

		if ($cache && $display) {
			header("Content-type: " . image_type_to_mime_type($mime));
			echo file_get_contents($data);
			return;
		}

		if ($cache) {
			response([
				'resized_image' => $config['app_url'] . 'uploads/'. $options['oN'],
			]);
		}

		$im = imagecreatefromstring(file_get_contents($data));

		$width = ($width === 'auto') ? round($original_width*.5) : $width;
		$resizeImage = imagescale($im, $width, $height);

		$filename = explode('/', $data)[1];

		$resizeImage = saveImage($resizeImage, $filename, $mime);
		imagedestroy($im);
		unlink($data);

		if ($display) {
			header("Content-type: " . image_type_to_mime_type($mime));
			echo file_get_contents($resizeImage);

			return;
		}

		response([
			'resized_image' => $config['app_url'] . $resizeImage,
		]);
	}
}

if (! function_exists('saveImage')) {
	/**
	 * Save image to local storage.
	 *
	 * @param  resource  $image
	 * @param  int       $mime
	 * @param  string    $filename
	 * @return string
	 */
	function saveImage($image, string $filename, int $mime): string
	{
		if (! is_dir('uploads')) {
			mkdir('uploads');
		}

		switch ($mime) {
			case IMAGETYPE_JPEG:
				imagejpeg($image, "uploads/$filename");
				break;
			case IMAGETYPE_GIF:
				imagegif($image, "uploads/$filename");
				break;
			case IMAGETYPE_WEBP:
				imagewebp($image, "uploads/$filename");
				break;
			case IMAGETYPE_BMP:
				imagebmp($image, "uploads/$filename");
				break;
			case IMAGETYPE_WBMP:
				imagewbmp($image, "uploads/$filename");
				break;
			case IMAGETYPE_XBM:
				imagexbm($image, "uploads/$filename");
				break;
			case IMAGETYPE_PNG:
			case IMAGETYPE_ICO:
			default:
				// Let us preserve transparency
				imagealphablending($image, true);
				imagesavealpha($image, true);

				imagepng($image, "uploads/$filename");
				break;
		}

		return "uploads/$filename";
	}
}

if (! function_exists('getImageDataFromURL')) {
	/**
	 * Get an image via CURL.
	 *
	 * @param  string  $url
	 * @return mixed
	 */
	function getImageDataFromURL(string $url, array $params)
	{
		$config = require 'config.php';
		$filename = $params['width'].'x'.$params['height'].str_replace(
			'/',
			'-',
			ltrim(strstr($url, '://'), '://')
		);

		// To guard against image url with query parameters
		$filename = strtok($filename, '?');

		if (file_exists("uploads/$filename")) {
			return [
				'cache' => true,
				'file' => "uploads/$filename"
			];
		}

		try {
			$image = (new Client())->get($url);
		} catch (Exception $e) {
			response('Cannot fetch image', 400);
		}

		if ($image->getStatusCode() === 200) {

			// We will not process more than the set MAX_FILE_SIZE_PROCESSED
			if ($image->getHeader('Content-Length')[0] > $config['max_file_size_upload']) {
				return response('Maximum filesize exceeded', 413);
			}

			$file = $image->getBody()->getContents();

			if (! is_dir('pimage')) {
				mkdir('pimage');
			}

			$handle   = fopen('pimage/'.$filename, 'x');
			
			fwrite($handle, $file);
			fclose($handle);

			return [
				'cache' => false,
				'file' => 'pimage/'.$filename
			];
		}

		response('Cannot fetch image', 400);
	}
}

