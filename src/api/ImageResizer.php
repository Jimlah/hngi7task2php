<?php
class ImageResizer{

    public function processImage($url, $width){
        //check dimensions given by user to confirm if they satisfy limit
        if($this->checkDimensions($width)){
            return $this->checkDimensions($width, $height);
        }
        else{

            if($this->checkValidImage($url) != "Valid"){
                return $this->checkValidImage($url);
            }

            //set dimension to width
            $dimension = $width;

            //set quality
            $quality = 100;

            //download the image if validation passess
            $fileName = $this->downloadImage($url);

            //check size of image
            if($this->checkImageSize($fileName)){
                return $this->checkImageSize($fileName);
            }

            //resized images folder creation
            $directoryName = '../resized_images';
     
            //Check if the directory already exists.
            if(!is_dir($directoryName)){
                //Directory does not exist, so lets create it.
                mkdir($directoryName, 0755);
            }

            //get properties of image
            $sourceProperties = getimagesize("../temp/".$fileName);
            $resizeFileName = time();
            $uploadPath = "../resized_images/";

            //get file extension
            $fileExt = pathinfo("../temp/".$fileName, PATHINFO_EXTENSION);

            //get image properties
            $uploadImageType = $sourceProperties[2];
            $sourceImageWidth = $sourceProperties[0];
            $sourceImageHeight = $sourceProperties[1];
            $ratio = $sourceImageWidth / $sourceImageHeight;

            if ($ratio < 1) {
                $new_Width = $dimension * $ratio;
                $new_Height = $dimension;
              } else {
                $new_Width = $dimension * $ratio;
                $new_Height = $dimension;
              }

            //resize image according to image format
            switch ($uploadImageType){
                case IMAGETYPE_JPEG:
                    $resourceType = imagecreatefromjpeg("../temp/".$fileName);
                    $imageLayer = $this->resizeImage($resourceType, $sourceImageWidth, $sourceImageHeight, $new_Width, $new_Height);
                    imagejpeg($imageLayer, $uploadPath .$fileName, $quality);
                    break;
                case IMAGETYPE_GIF:
                    $resourceType = imagecreatefromgif("../temp/".$fileName);
                    $imageLayer = $this->resizeImage($resourceType, $sourceImageWidth, $sourceImageHeight, $new_Width, $new_Height);
                    imagegif($imageLayer, $uploadPath .$fileName, $quality);
                    break;
                case IMAGETYPE_PNG:
                    $resourceType = imagecreatefrompng("../temp/".$fileName);
                    $imageLayer = $this->resizeImage($resourceType, $sourceImageWidth, $sourceImageHeight, $new_Width, $new_Height);
                    imagepng($imageLayer, $uploadPath .$fileName, 9);
                    break;
                default:
                    $imageProcess = 0;
                    break;
                    
            }
            //save resized file
            move_uploaded_file(@$file, $uploadPath. $resizeFileName. ".". $fileExt);

            //construct path of resized file to generate url
            $relative_path = $uploadPath . $fileName;

            //delete file in temp folder after resizing
            unlink("../temp/".$fileName);

            $path = "http://" .$_SERVER['SERVER_NAME'] ."/src/api/" .$relative_path;

            //return JSON response
            return json_encode(
                array(
                    "filename" => $fileName,
                    "message" => "Successful",
                    "image_url" => $path,
                    "file_size" => filesize($relative_path)/1000 ."kb",
                    "image_format" => $fileExt,
                    "initial_height" => $sourceImageHeight."px",
                    "initial_width" => $sourceImageWidth."px",
                    "resized_height" => $new_Height."px",
                    "resized_width" => $new_Width."px",
                )
            );           
        }


    }
    
    protected function downloadImage($url){
        $directoryName = '../temp';
 
        //Check if the directory already exists.
        if(!is_dir($directoryName)){
            //Directory does not exist, so lets create it.
            mkdir($directoryName, 0755);
        }

        //get details of image from url
        $pathinfo = pathinfo($url); 

        //get filename and extension
        $filename = time();
        $ext = $pathinfo['extension'];

        //new filename
        $newFilename = $pathinfo["filename"] ."_".$filename ."." .$ext;

        //get image from url
        $img_content = file_get_contents($url);

        //write downloaded file into output file
        $new_img = fopen("../temp/".$newFilename, "w");      
        $scrape = fwrite($new_img, $img_content);    

        if($scrape == true){
            return $newFilename;
        }
    }


    protected function resizeImage($resourceType, $image_width, $image_height, $resizeWidth, $resizeHeight){
        //resize image and return image layer
        $imageLayer = imagecreatetruecolor($resizeWidth, $resizeHeight);
        imagecopyresampled($imageLayer,$resourceType,0,0,0,0,$resizeWidth,$resizeHeight,$image_height,$image_width);
        return $imageLayer;

    }

    protected function checkDimensions($width){
        if ($width <= 10) {
            http_response_code(422);
            return json_encode(
                array("message" => "Image width is below limit")
            );
            die();
        }
    }

    private function checkValidImage($url){
        if($url == ""){
            http_response_code(400);
            return json_encode(
                array(
                    "message" => "No image specified"
                )
            );
            die();          
        }
        $extension = strtolower(substr($url, -3));
        if($extension == "jpg" OR $extension == "png" OR $extension == "gif" OR $extension == "jpeg"){
            return "Valid";
        }
        else{
            http_response_code(422);
            return json_encode(
                array(
                    "message" => "Invalid image. Please check URL"
                )
            );
            die();
        }
/**
        if (exif_imagetype($url) != IMAGETYPE_GIF) {
            return json_encode(
                array("message" => "Invalid")
            ); 
        }
        elseif(exif_imagetype($url) != IMAGETYPE_JPEG) {
            return json_encode(
                array("message" => "Invalid image")
            ); 
        }
        elseif(exif_imagetype($url) != IMAGETYPE_PNG) {
            return json_encode(
                array("message" => "Invalid image")
            ); 
        }
        **/
    }

    protected function checkImageSize($image){
        $relative_path = '../temp/'.$image;

        if(filesize($relative_path) > 100000000){
            //delete file in temp folder after resizing
            unlink($relative_path);

            
            http_response_code(400);
            return json_encode(
                array(
                    "message" => "File size exceeds set limit of 100MB"
                )
            );
            die();
        }
    }



}