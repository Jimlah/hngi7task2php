<?php
class ImageResizer{

    public function processImage($url, $width, $height){
        //check dimensions given by user to confirm if they satisfy limit
        if($this->checkDimensions($width, $height)){
            return $this->checkDimensions($width, $height);
        }
        else{
            //download the image if validation passess
            $fileName = $this->downloadImage($url);

            //resized images folder creation
            $directoryName = 'resized_images';
     
            //Check if the directory already exists.
            if(!is_dir($directoryName)){
                //Directory does not exist, so lets create it.
                mkdir($directoryName, 0755);
            }

            //get properties of image
            $sourceProperties = getimagesize("original_images/".$fileName);
            $resizeFileName = time();
            $uploadPath = "resized_images/";

            $fileExt = pathinfo("original_images/".$fileName, PATHINFO_EXTENSION);

            //get file extension
            $uploadImageType = $sourceProperties[2];
            $sourceImageWidth = $sourceProperties[0];
            $sourceImageHeight = $sourceProperties[1];


            //resize image according to image format
            switch ($uploadImageType){
                case IMAGETYPE_JPEG:
                    $resourceType = imagecreatefromjpeg("original_images/".$fileName);
                    $imageLayer = $this->resizeImage($resourceType, $sourceImageWidth, $sourceImageHeight, $width, $height);
<<<<<<< HEAD
                    imagejpeg($imageLayer, $uploadPath .$fileName);
=======
                    imagejpeg($imageLayer, $uploadPath."thump_".$resizeFileName.".".$fileExt);
>>>>>>> 7608ba3fc5282e599d810642e535585f1e563222
                    break;
                case IMAGETYPE_GIF:
                    $resourceType = imagecreatefromgif("original_images/".$fileName);
                    $imageLayer = $this->resizeImage($resourceType, $sourceImageWidth, $sourceImageHeight, $width, $height);
<<<<<<< HEAD
                    imagegif($imageLayer, $uploadPath .$fileName);
=======
                    imagegif($imageLayer, $uploadPath."thump_".$resizeFileName.".".$fileExt);
>>>>>>> 7608ba3fc5282e599d810642e535585f1e563222
                    break;
                case IMAGETYPE_PNG:
                    $resourceType = imagecreatefrompng("original_images/".$fileName);
                    $imageLayer = $this->resizeImage($resourceType, $sourceImageWidth, $sourceImageHeight, $width, $height);
<<<<<<< HEAD
                    imagepng($imageLayer, $uploadPath .$fileName);
=======
                    imagepng($imageLayer, $uploadPath."thump_".$resizeFileName.".".$fileExt);
>>>>>>> 7608ba3fc5282e599d810642e535585f1e563222
                    break;
                default:
                    $imageProcess = 0;
                    break;
                    
            }
            //save resized file
            move_uploaded_file(@$file, $uploadPath. $resizeFileName. ".". $fileExt);

            //construct path of resized file to generate url
<<<<<<< HEAD
            $relative_path = $uploadPath . $fileName;

            $path = "http://" .$_SERVER['SERVER_NAME'] ."/src/api/" .$relative_path;
=======
            $image_name = "thump_".$resizeFileName.".".$fileExt;

            $relative_path = $uploadPath . $image_name;

            $path = $_SERVER['SERVER_NAME'] ."/src/api/" .$relative_path;
>>>>>>> 7608ba3fc5282e599d810642e535585f1e563222

            //return JSON response
            return json_encode(
                array(
<<<<<<< HEAD
                    "filename" => $fileName,
                    "message" => "Successful",
                    "image_url" => $path,
                    "file_size" => filesize($relative_path)/1000 ."kb",
                    "image_format" => $fileExt,
                    "initial_height" => $sourceImageHeight."px",
                    "initial_width" => $sourceImageWidth."px",
                    "resized_height" => $height."px",
                    "resized_width" => $width."px",
=======
                    "message" => "Successful",
                    "image_url" => $path
>>>>>>> 7608ba3fc5282e599d810642e535585f1e563222
                )
            );           
        }


    }
    
    protected function downloadImage($url){
        $directoryName = 'original_images';
 
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
        $new_img = fopen("original_images/".$newFilename, "w");      
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

    protected function checkDimensions($width, $height){
        if ($width <= 10) {
            return json_encode(
                array("message" => "Image width is below limit")
            );
            die();
        }
        if ($height <= 10) {
            return json_encode(
                array("message" => "Image height is below limit")
            );     
            
        }
    }



}