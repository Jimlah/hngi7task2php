<?php declare(strict_types=1);

/*
 * This file/script handles the resizing of images.
 *
 */

namespace TeamFlash;

use Exception;
use GuzzleHttp\Client;

class ResizeImage
{
	/**
	 * Method to resize images on the fly.
	 *
	 * @return An JSON content with link to the resized image
	 */
	public function resizeImageOnPostMethod()
	{
		// Let us get the dimensions
		$this->width = isset($_POST['width'])
		? (empty($_POST['width']) ? -1 : (int) filter_var($_POST['width'], FILTER_SANITIZE_NUMBER_INT)) : -1;
		$this->height = isset($_POST['height'])
		? (empty($_POST['height']) ? -1 : (int) filter_var($_POST['height'], FILTER_SANITIZE_NUMBER_INT))  : -1;

		if (! isset($_FILES['rimage'])) {
			response('No image submitted for procession', 422);
		}

		$uploadImage = $_FILES['rimage']['tmp_name'];
		$filename = $this->generateName($_FILES['rimage']['name']);
		
		// We now check if we've already resized such an image
		if ($this->imageHasBeenResized($filename)) {
			response([
				'message' => 'Successful',
				'image_url' => Registry::get('config')['app_url'] . "uploads/$filename"
			]);
		}

		if (! move_uploaded_file($uploadImage, $imageResolvedFile = $filename)) {
			response('Cannot fetch image', 400);
		}

		// We now have the image, let's head over to process it
		$image = $this->processImage($imageResolvedFile);
		response([
			'message' => 'Successful',
			'image_url' => Registry::get('config')['app_url'] . $image
		]);
	}

	/**
	 * Method to resize images on the fly.
	 *
	 * @param  string  $dimensions  The dimensions provided by the user to be used for resizing.
	 * @param  string  $uri  		The image resource to be resized.
	 * @return An image content-type response with the resized image.
	 */
	public function resizeImageOnTheFly(string $dimensions, string $uri)
	{
		// The dimensions passed in will be of this form: `w_x,h_y`. So we will have to
		// split it out in order to get our width and height.
		$dimensions = explode(',', $dimensions);

		// Now we are left with an array of this nature: `['w_x', 'h_y']`. We further split it
		// in order to get the `x` and `y` values.
		foreach ($dimensions as $dimension) {
			$resolvedDimension = explode('_', $dimension);

			if ($resolvedDimension[0] === 'h') {
				// We will simply extract the `height` value
				$height = $resolvedDimension[1];
			}

			if ($resolvedDimension[0] === 'w') {
				// We will simply extract the `width` value
				$width = $resolvedDimension[1];
			}
		}

		// We do not require the user to pass all dimensions (height & width).
		// The user may choose to pass only the width or height value
		// in order to maintain and preserve aspect ratio. So let's check for either
		// a missing height or width. `-1` stands for auto scale.
		$this->height = isset($height) ? $height : -1;
		$this->width  = isset($width) ? $width : -1;
		$this->uri    = $uri;

		// Let us now curl or guzzle the image using the passed in URL
		$imageResolvedFile = $this->getImageDataFromURL($this->generateName($uri));
		if (is_array($imageResolvedFile)) {
			// This is a cache return
			$binaryData = file_get_contents($imageResolvedFile[0]);
			response(
				$binaryData,
				['headers' => ['Content-Type: ' . mime_content_type($imageResolvedFile[0])]],
				false
			);
		}

		// We now have the image, let's head over to process it
		$image = $this->processImage($imageResolvedFile);
		$binaryData = file_get_contents($image);
		response(
			$binaryData,
			['headers' => 'Content-Type: ' . mime_content_type($image)],
			false
		);
	}

	/**
	 * Save image to local storage.
	 *
	 * @param  resource  $image
	 * @param  string    $filename
	 * @param  int       $mime
	 * @return string
	 */
	protected function saveImage($image, string $filename, int $mime): string
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
				imagealphablending($image, false);
				imagesavealpha($image, true);
				imagepng($image, "uploads/$filename");
				break;
		}

		return "uploads/$filename";
	}

	/**
	 * This method gets an image file and processes (resize) it.
	 *
	 * @param  string  $file  The image file to be processed.
	 * @return mixed
	 */
	protected function processImage(string $file)
	{
		// We will use Fileinfo to determine if it is actually an image file. Using
		// getimagesize() for such purpose is discouraged. Legacy versions may use `exif`
		// or even getimagesize().
		try {
				// Here we are flagging for the mime type check
				$finfo = finfo_open(FILEINFO_MIME_TYPE);
				if(! strstr(finfo_file($finfo, $file), 'image')) {
					// Whoops! This is not a valid image file.
					// Let us remove the resolved file.
					unlink($file);
					response('Invalid image', 415);
				}
			} catch (Exception $e) {
				// App::get('logger')->log($e);
				unlink($file);
				response('Invalid image', 415);
			}

		list($originalWidth, $originalHeight, $mime) = getimagesize($file);

		$im = imagecreatefromstring(file_get_contents($file));
		$resizeImage = imagescale($im, (int)$this->width, (int)$this->height);
		// $filename = explode('/', $file);
		$filename = $file;

		// Let's now save the image
		$resizeImage = $this->saveImage($resizeImage, $filename, $mime);

		// We then free up memory
		imagedestroy($im);
		unlink($file);

		// We now return the resized image path
		return $resizeImage;
	}

	/**
	 * This method retrieves/resolves the image URI.
	 *
	 * @param  string  $generatedName  The generated name for the image.
	 * @return mixed
	 */
	protected function getImageDataFromURL(string $generatedName)
	{
		if ($this->imageHasBeenResized($generatedName)) {
			return ["uploads/$generatedName"];
		}

		// Making use of GuzzleHttp, we will now retrieve the image from the URL
		try {
				$resource = (new Client())->get($this->uri);
			} catch (Exception $e) {
				response('Cannot fetch image', 400);
			}

		if ($resource->getStatusCode() === 200) {
			$maxFileSize = Registry::get('config')['max_filesize'];
			// We don't want to process more than a set maximum filesize. Default is 5MB
			if ($resource->getHeader('Content-Length')[0] > $maxFileSize) {
				response('Maximum filesize '.formatBytes($maxFileSize).' exceeded', 413);
			}

			// We get the image contents which will be in a binary form
			$image = $resource->getBody()->getContents();

			$handle = fopen($generatedName, 'w');
			fwrite($handle, $image);
			fclose($handle);

			return $generatedName;
		}

		response('Cannot fetch image', 400);
	}

	/**
	 * This method determines if an image has previously been resized.
	 *
	 * @param  string  $fileName  The file name to be checked.
	 * @return string
	 */
	protected function imageHasBeenResized(string $fileName): bool
	{
		if (! is_dir('uploads')) {
			mkdir('uploads', 0755);
		}

		if (file_exists("uploads/$fileName")) {
			return true;
		}

		return false;
	}

	/**
	 * This method generates a name for the image based on the name of
	 * the file or the URI and its dimensions. It is mostly used as a cache mechanism
	 * to prevent us from downloading and resizing the same image.
	 *
	 * @param  string  $data  The data from which the name will be generated from.
	 * @return string
	 */
	protected function generateName(string $data): string
	{
		$striPos = strpos($data, '://');

		if (! $striPos) {
			return strtok($this->width . 'x' . $this->height . $data, '?');
		}

		return strtok(
			$this->width . 'x' . $this->height . str_replace('/', '-', ltrim(strstr($data, '://'), '://')),
			'?'
		);
	}
}
