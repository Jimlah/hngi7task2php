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

  <!--====== TITLE TAG ======-->
  <title>Image Resizer</title>

  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <style>
    * {
      box-sizing: border-box;
      -moz-box-sizing: border-box;
      -webkit-box-sizing: border-box;
    }

    body {
      font-family: 'Montserrat', sans-serif;
      background: #535c68;
    }

    .wrapper {
      margin: auto;
      max-width: 640px;
      padding-top: 60px;
      text-align: center;
    }

    .container {
      background-color: #f9f9f9;
      padding: 20px;
      border-radius: 10px;
      /*border: 0.5px solid rgba(130, 130, 130, 0.25);*/
      /*box-shadow: 0 2px 3px rgba(0, 0, 0, 0.1), 
                0 0 0 1px rgba(0, 0, 0, 0.1);*/
    }

    h1 {
      color: #130f40;
      font-family: 'Varela Round', sans-serif;
      letter-spacing: -.5px;
      font-weight: 700;
      padding-bottom: 10px;
    }

    .upload-container {
      background-color: rgb(239, 239, 239);
      border-radius: 6px;
      padding: 10px;
    }

    .border-container {
      border: 5px dashed rgba(198, 198, 198, 0.65);
      border-radius: 6px;
      padding: 20px;
    }

    .border-container p {
      color: #130f40;
      font-weight: 600;
      font-size: 1.1em;
      letter-spacing: -1px;
      margin-top: 30px;
      margin-bottom: 0;
      opacity: 0.65;
    }

    #file-browser {
      text-decoration: none;
      color: rgb(22, 42, 255);
      border-bottom: 3px dotted rgba(22, 22, 255, 0.85);
    }

    #file-browser:hover {
      color: rgb(0, 0, 255);
      border-bottom: 3px dotted rgba(0, 0, 255, 0.85);
    }

    .icons {
      color: #95afc0;
      opacity: 0.55;
    }
  </style>
</head>

<body class="home-one">


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

            <div class="custom-file">
              <label class="custom-file-label" for="inputGroupFile02"></label>
              <input type="file" class="custom-file-input" name="upload_image" id="inputGroupFile02" required>

            </div>
            <br>
            <div class="text-center">
              <div class="form-group">
                <label for="width"></label>
                <input type="number" name="txt_dem" class="form-control" placeholder="Input Required width in pixels" required>
              </div>
              <!-- <div class="form-group">
                <label for=""></label>
                <input type="number" name="user_height" class="form-control" placeholder="Input Required height in pixels" required>
              </div> -->
              <input type="submit" class="btn btn-success" name="form_submit">
            </div>
            <br>





            <?php
            if (isset($_POST['form_submit'])) {
              $directoryName = 'resized_images';
              $dimension = $_POST['txt_dem'];
              $quality = 5;


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
                  echo "Width too small";
                  die;
                }
                if ($resizeHeight <= 10) {
                  echo "Height too small";
                  die;
                }

                $destination = imagecreatetruecolor($resizeWidth, $resizeHeight);
                imagecopyresampled($destination, $resourceType, 0, 0, 0, 0, $resizeWidth, $resizeHeight, $image_height, $image_width);
                return $destination;
              }


              $imageProcess = 0;
              if (is_array($_FILES)) {
                $error = $_FILES['upload_image']['error'];
                if (!$error) {
                  $quality = 50;
                  $fileName = $_FILES['upload_image']['tmp_name'];
                  $imageName = $_FILES['upload_image']['name'];
                  $uploadPath = "./resized_images/";
                  $fileExt = pathinfo($_FILES['upload_image']['name'], PATHINFO_EXTENSION);
                  $image_size = getimagesize($fileName);
                  $uploadImageType = $image_size[2];
                  $width = $image_size[0];
                  $height = $image_size[1];
                  $ratio = $width / $height;

                  if ($ratio > 1) {
                    $new_Width = $dimension;
                    $new_Height = $dimension / $ratio;
                  } else {
                    $new_Width = $dimension / $ratio;
                    $new_Height = $dimension;
                  }
                  switch ($uploadImageType) {
                    case IMAGETYPE_JPEG:
                      $src = imagecreatefromstring(file_get_contents($fileName));
                      $destination = imagecreatetruecolor($new_Width, $new_Height);
                      imagecopyresampled($destination, $src, 0, 0, 0, 0, $new_Width, $new_Height, $width, $height);
                      imagejpeg($destination, $uploadPath . "thump_" . $imageName . "." . $fileExt, $quality);
                      break;
                    case IMAGETYPE_GIF:
                      $src = imagecreatefromstring(file_get_contents($fileName));
                      $destination = imagecreatetruecolor($new_Width, $new_Height);
                      imagecopyresampled($destination, $src, 0, 0, 0, 0, $new_Width, $new_Height, $width, $height);
                      imagegif($destination, $uploadPath . "thump_" . $imageName . "." . $fileExt, $quality);
                      break;
                    case IMAGETYPE_PNG:
                      $src = imagecreatefromstring(file_get_contents($fileName));
                      $destination = imagecreatetruecolor($new_Width, $new_Height);
                      imagecopyresampled($destination, $src, 0, 0, 0, 0, $new_Width, $new_Height, $width, $height);
                      imagepng($destination, $uploadPath . "thump_" . $imageName . "." . $fileExt, $quality);
                      break;
                    default:
                      $src = imagecreatefromstring(file_get_contents($fileName));
                      $destination = imagecreatetruecolor($new_Width, $new_Height);
                      imagecopyresampled($destination, $src, 0, 0, 0, 0, $new_Width, $new_Height, $width, $height);
                      imagepng($destination, $uploadPath . "thump_" . $imageName . "." . $fileExt, $quality);
                      break;
                  }

                  move_uploaded_file(@$file, $uploadPath . $imageName . "." . $fileExt);
                  $imageProcess = 1;
                } else {

                  echo "<div>
                  <p class='alert alert-danger'>Sorry!!! Your file File too large</p>
                </div>";
                  die;
                }
                if ($imageProcess == 1) {
                  $outputImage = $uploadPath . "thump_" . $imageName . "." . $fileExt;
                  echo "<img src= '$outputImage'/>";
                  echo '<br>';
                  echo "<div>
                  <p class='alert alert-success'>Image Resized Successfully</p>
                </div>";
                } else {
                  echo "<div>
                  <p class='alert alert-danger'>Sorry!!! Invalid Image</p>
                </div>";
                }
              }
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


    $('input[type="file"]').change(function(e) {
      var fileName = e.target.files[0].name;
      $('.custom-file-label').html(fileName);
    });
  </script>







</body>

</html>