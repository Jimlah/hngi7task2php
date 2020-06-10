<?php
ini_set('display_errors', 1);

// required headers
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Headers");

$documentation_url = "https://lucid.blog/mutevu/post/how-to-consume-the-image-resize-api-83a";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include_once('ImageResizer.php');

	//read content of request from POST request
	$data = json_decode(file_get_contents("php://input"));

	$object = new ImageResizer();

	//retrieve parameters from JSON POST data
	$image_url = $data->image;
	$resize_width = $data->width;


	//process downloaded image and return JSON response
	echo $object->processImage($image_url, $resize_width);
	//echo($object->checkValidImage($image_url));
}
else{
	echo json_encode(
		array(
			"message" => "Invalid request. Only POST requests are allowed. Read the documentation here: ".$documentation_url
		)
	);
}







