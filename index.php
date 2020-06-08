<?php

//process image
function proccess_image($file,$maximum_resolution){
  if(file_exists($file)){
    $original_image = imagecreatefromjpeg($file);
    //get image dimension
    $original_width = imagesx($original_image);
    $original_height= imagesy($original_image);

    // dimension ratio and checking width variances

    $ratio = $maximum_resolution/$original_width;
    $new_height = $original_height * $ratio;
    $new_width = $maximum_resolution;
    
    // adjusting to width height if out of resolution.
    if($new_height > $maximum_resolution){
      $ratio = $maximum_resolution/$original_height;
      $new_height = $maximum_resolution;
      $new_width = $original_width*$ratio;

    }

    if($original_image){
      $new_image = imagecreatetruecolor($new_width,$new_height);
      // resampling based on dimensions
      imagecopyresampled($new_image,$original_image,0,0,0,0,$new_width,$new_height,$original_width,$original_height);
      imagejpeg($new_image,$file,90);// quality is 90%
    }


    


  }

}
if($_SERVER['REQUEST_METHOD'] == 'POST'){
  //check for request
  if(isset($_FILES['image']) && $_FILES['image']['type'] =='image/jpeg'){

    //move file to another destination
    move_uploaded_file($_FILES['image']['tmp_name'],$_FILES['image']['name']);

    $file = $_FILES['image']['name'];
    proccess_image($file,500);
    echo "<img src= '$file'/>";


  }

}

?>

<form method="POST" action="" enctype="multipart/form-data">

<input type="file" name="image"/><br>
<input type="submit" value="post">

</form>