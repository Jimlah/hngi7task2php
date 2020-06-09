<?php
class ImageResizer{

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
            echo json_encode(
                array("message" => "Image width is below limit")
            );
            die();
        }
        if ($height <= 10) {
            echo json_encode(
                array("message" => "Image height is below limit")
            );     
            die();
        }
    }

    public function processImage($url, $width, $height){
        //check dimensions given by user to confirm if they satisfy limit
        $this->checkDimensions($width, $height);

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
                imagejpeg($imageLayer, $uploadPath."thump_".$resizeFileName.".".$fileExt);
                break;
            case IMAGETYPE_GIF:
                $resourceType = imagecreatefromgif("original_images/".$fileName);
                $imageLayer = $this->resizeImage($resourceType, $sourceImageWidth, $sourceImageHeight, $width, $height);
                imagegif($imageLayer, $uploadPath."thump_".$resizeFileName.".".$fileExt);
                break;
            case IMAGETYPE_PNG:
                $resourceType = imagecreatefrompng("original_images/".$fileName);
                $imageLayer = $this->resizeImage($resourceType, $sourceImageWidth, $sourceImageHeight, $width, $height);
                imagepng($imageLayer, $uploadPath."thump_".$resizeFileName.".".$fileExt);
                break;
            default:
                $imageProcess = 0;
                break;
                
        }
        //save resized file
        move_uploaded_file(@$file, $uploadPath. $resizeFileName. ".". $fileExt);

        //construct path of resized file to generate url
        $image_name = "thump_".$resizeFileName.".".$fileExt;

        $relative_path = $uploadPath . $image_name;

        $path = $_SERVER['SERVER_NAME'] ."/src/api/" .$relative_path;

        //return JSON response
        echo json_encode(
            array(
                "message" => "Successful",
                "image_url" => $path
            )
        );
    }


}