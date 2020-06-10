<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="en">
<!--<![endif]-->

<head>
  <!--====== USEFULL META ======-->
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="" />
  <meta name="keywords" content="" />
  <script src="style/script.js"></script>
  <link rel="icon" href="favicon.ico" type='image/x-icon' />
  <title>Team Flash Software</title>
  <link rel="stylesheet" type="text/css" href="style/style.css">
  <!--====== TITLE TAG ======-->
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet">


</head>

<header>
  <a href="#" class="logo"><img src="logo.png"></a>
  <div class="dropdown">
    <button onclick="myFunction()" class="dropbtn"></button>
    <div id="myDropdown" class="dropdown-content">
      <a href="#">API Documentation</a>
      <a href="#">Resources</a>

    </div>


  </div>
</header>

<div class="banner">
  <div class="content">
    <h2>Team Flash <span>Image Resize</span> Software</h2>
  </div>
</div>
<div class="wrapper">
  <div class="container">
    <h1>Upload an Image</h1>
    <div class="upload-container">
      <div class="border-container">
        <form method="post" enctype="multipart/form-data">
          <div class="icons fa-4x">
            <i class="fas fa-file-image" data-fa-transform="shrink-3 down-2 left-6 rotate--45"></i>
            <i class="fas fa-file-alt" data-fa-transform="shrink-2 up-4"></i>
            <i class="fas fa-file-pdf" data-fa-transform="shrink-3 down-2 right-6 rotate-45"></i>
          </div>
          <!--<input type="file" id="file-upload">-->
          <p>
            <!--            Drag and drop files here, or -->
            <!--          <a href="#" id="file-browser">browse</a> your computer.-->
            <label>Choose Image</label>
            <input type="file" name="upload_image" class="button" required></p><br>
          <input type="number" name="user_width" style="width:200px" placeholder="Input Required width in pixels" required><br>
          <input type="number" name="user_height" style="width:200px" placeholder="Input Required height in pixels" required><br>

          <input type="submit" style="margin-top:30px" name="form_submit">



          <?php

          if (isset($_POST['form_submit'])) {
            $directoryName = 'resized_images';

            //Check if the directory already exists.
            if (!is_dir($directoryName)) {
              //Directory does not exist, so lets create it.
              mkdir($directoryName, 0755);
            }

            function resizeImage($resourceType, $image_width, $image_height)
            {
              $resizeWidth = $_POST['user_width'];
              $resizeHeight = $_POST['user_height'];

              if ($resizeWidth <= 10) {
                echo "Width too small";
                die();
              }


              if ($resizeHeight <= 10) {
                echo "Height too small";
                die();
              }

              $imageLayer = imagecreatetruecolor($resizeWidth, $resizeHeight);
              imagecopyresampled($imageLayer, $resourceType, 0, 0, 0, 0, $resizeWidth, $resizeHeight, $image_height, $image_width);
              return $imageLayer;
            }

            $imageProcess = 0;
            if (is_array($_FILES)) {
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
                  imagejpeg($imageLayer, $uploadPath . "thump_" . $resizeFileName . "." . $fileExt);
                  break;
                case IMAGETYPE_GIF:
                  $resourceType = imagecreatefromgif($fileName);
                  $imageLayer = resizeImage($resourceType, $sourceImageWidth, $sourceImageHeight);
                  imagegif($imageLayer, $uploadPath . "thump_" . $resizeFileName . "." . $fileExt);
                  break;
                case IMAGETYPE_PNG:
                  $resourceType = imagecreatefrompng($fileName);
                  $imageLayer = resizeImage($resourceType, $sourceImageWidth, $sourceImageHeight);
                  imagepng($imageLayer, $uploadPath . "thump_" . $resizeFileName . "." . $fileExt);
                  break;
                default:
                  $imageProcess = 0;
                  break;
              }

              move_uploaded_file(@$file, $uploadPath . $resizeFileName . "." . $fileExt);
              $imageProcess = 1;
            }
            if ($imageProcess == 1) {

              $outputImage = $uploadPath . "thump_" . $resizeFileName . "." . $fileExt;
              echo "<br><img src= '$outputImage'/>";
              echo '<br>';
              echo "Image Resized Successfully";
              echo '<p><a href="download.php?file=' . urlencode("thump_" . $resizeFileName . "." . $fileExt) . '">Download Image</a></p>';
            } else {
              echo "Note! Invalid Image";
            }

            $imageProcess = 0;
          }


          ?>



        </form>
      </div>
    </div>
  </div>
</div>


<script>
  $("#file-upload").css("opacity", "0");

  $("#file-browser").click(function(e) {
    e.preventDefault();
    $("#file-upload").trigger("click");
  });
</script>







</body>

</html>