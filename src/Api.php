<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
error_reporting(0);
$width = isset($_GET['width']) ? $_GET['width'] : die();
$height = isset($_GET['height']) ? $_GET['height'] : die();

            if (!empty($width) && !empty($height)) {
              $directoryName = 'resized_images';
             

              //Check if the directory already exists.
              if (!is_dir($directoryName)) {
                //Directory does not exist, so lets create it.
                mkdir($directoryName, 0755);
              }

              function resizeImage($resourceType, $image_width, $image_height)
              {
                global $width;
                global $height;
                $resizeWidth = $width;
                $resizeHeight = $height;

                if ($resizeWidth <= 10) {      
                
               
                 // set response code - 400 bad request
                  http_response_code(400);
              
                  // tell the user
                  echo json_encode(array("message" => "Width too small"));
                  die;
                }
                if ($resizeHeight <= 10) {
                
                  // set response code - 400 bad request
                  http_response_code(400);
              
                  // tell the user
                  echo json_encode(array("message" => "Height too small"));
                  die;
                }

                $imageLayer = imagecreatetruecolor($resizeWidth, $resizeHeight);
                imagecopyresampled($imageLayer, $resourceType, 0, 0, 0, 0, $resizeWidth, $resizeHeight, $image_height, $image_width);
                return $imageLayer;

                
              }


              $imageProcess = 0;
              if (is_array($_FILES)) {
                $error = $_FILES['upload_image']['error'];
                if (!$error) {
                  
                  $quality = 50;
                  $fileName = $_FILES['upload_image']['tmp_name'];
                  $sourceProperties = getimagesize($fileName);
                  $resizeFileName = time();
                  $uploadPath = "./resized_images/";
                  $fileExt = pathinfo($_FILES['upload_image']['name'], PATHINFO_EXTENSION);
                  $uploadImageType = $sourceProperties[2];
                  $sourceImageWidth = $sourceProperties[0];
                  $sourceImageHeight = $sourceProperties[1];
                  switch ($uploadImageType) {
                    case IMAGETYPE_JPEG:
                      $resourceType = imagecreatefromjpeg($fileName);
                      $imageLayer = resizeImage($resourceType, $sourceImageWidth, $sourceImageHeight);
                      imagejpeg($imageLayer, $uploadPath . "thump_" . $resizeFileName . "." . $fileExt, $quality);
                      break;
                    case IMAGETYPE_GIF:
                      $resourceType = imagecreatefromgif($fileName);
                      $imageLayer = resizeImage($resourceType, $sourceImageWidth, $sourceImageHeight);
                      imagegif($imageLayer, $uploadPath . "thump_" . $resizeFileName . "." . $fileExt, $quality);
                      break;
                    case IMAGETYPE_PNG:
                      $resourceType = imagecreatefrompng($fileName);
                      $imageLayer = resizeImage($resourceType, $sourceImageWidth, $sourceImageHeight);
                      imagepng($imageLayer, $uploadPath . "thump_" . $resizeFileName . "." . $fileExt, $quality);
                      break;
                    default:
                      $imageProcess = 0;
                      break;
                  }

                  move_uploaded_file(@$file, $uploadPath . $resizeFileName . "." . $fileExt);
                  $imageProcess = 1;
                } else {

                  echo "<div>
                  <p class='alert alert-danger'>Sorry!!! Your file File too large</p>
                </div>";
                }
                if ($imageProcess == 1) {
                  $outputImage = $uploadPath . "thump_" . $resizeFileName . "." . $fileExt;
                //   echo "<img src= '$outputImage'/>";
                //   echo '<br>';
                //   echo "<div>
                  
                //   <p class='alert alert-success'>Image Resized Successfully</p>
                // </div>";
                 // set response code - 201 created
                 $data = array(
                  "id" =>  rand(),
                  "width" => $width,
                  "height" => $height,
                  "image" => $outputImage,           
              );
           
              // set response code - 200 OK
              http_response_code(200);
           
              // make it json format
              echo json_encode($data);
                
                } else {
                 // set response code - 400 bad request
                  http_response_code(400);
              
                  // tell the user
                  echo json_encode(array("message" => "Note! Invalid Image"));
                  die;
                }
              }
            }


            ?>