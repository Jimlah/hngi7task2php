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


    protected function resizeImage($resourceType, $image_width, $image_height){
        //resize image and return image layer
        $imageLayer = imagecreatetruecolor($resizeWidth, $resizeHeight);
        imagecopyresampled($imageLayer,$resourceType,0,0,0,0,$resizeWidth,$resizeHeight,$image_height,$image_width);
        return $imageLayer;

    }

    protected function checkDimensions($width, $height){
        if ($width <= 10) {
            echo json_encode(
                array("error" => "Image width is below limit")
            );
            die();
        }
        if ($height <= 10) {
            echo json_encode(
                array("error" => "Image height is below limit")
            );     
            die();
        }
    }

    public function processImage($url, $width, $height){
        //check dimensions given by user to confirm if they satisfy limit
        $this->checkDimensions($width, $height);

        //download the image if validation passess
        $this->downloadImage($url);



    }



    public function test($url){
        //get image from url
        $img_content = file_get_contents($url);

        $new_img = fopen("original_images/img.jpg", "w");

        $scrape = fwrite($new_img, $img_content);    
    }

}