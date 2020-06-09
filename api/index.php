<?php
// required headers
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Headers");

include_once('resize.php');

//read content of request from POST request
$data = json_decode(file_get_contents("php://input"));

$object = new ImageResizer();

//retrieve parameters from JSON POST data
$image_url = $data->image;
$resize_width = $data->width;
$resize_height = $data->height;


//process downloaded image
$object->processImage($image_url, $resize_width, $resize_height);

/**
echo json_encode(
	array("image_url"=> $imageName)
);

**/



