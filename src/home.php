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
  <link rel="stylesheet" type="text/css" href="style/style.css">
  <!--====== TITLE TAG ======-->
  <title>Image Resizer</title>

  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

</head>

<body>
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


              $imageProcess = 0;
              if (is_array($_FILES)) {
                $error = $_FILES['upload_image']['error'];
                if (!$error) {

                  //to xheck the filesize
                  $filesize = getfilesize('upload_image');
                  if ($dimension > 10 && $filesize < 100000) {
                    $quality = 100;
                    $fileName = $_FILES['upload_image']['tmp_name'];
                    $imageName = $_FILES['upload_image']['name'];
                    $uploadPath = "./resized_images/";
                    $fileExt = pathinfo($_FILES['upload_image']['name'], PATHINFO_EXTENSION);
                    $image_size = getimagesize($fileName);
                    $uploadImageType = $image_size[2];
                    //checking if image type allowed
                    // if (!$uploadImageType in_array(1,2,3)) {die('invalid image')};

                    $width = $image_size[0];
                    $height = $image_size[1];
                    $ratio = $width / $height;


                    echo "the dimension before resizing (" . $height . ', ' . $width . ')<br>';



                    if ($ratio < 1) {
                      $new_Width = $dimension * $ratio;
                      $new_Height = $dimension;
                    } else {
                      $new_Width = $dimension * $ratio;
                      $new_Height = $dimension;
                    }

                    echo "the dimension after resizing (" . $new_Height . ', ' . $new_Width . ')<br>';



                    switch ($uploadImageType) {
                      case IMAGETYPE_JPEG:
                        $src = imagecreatefromstring(file_get_contents($fileName));
                        $destination = imagecreatetruecolor($new_Width, $new_Height);
                        imagecopyresampled($destination, $src, 0, 0, 0, 0, $new_Width, $new_Height, $width, $height);
                        imagejpeg($destination, $uploadPath . "thump_" . $imageName, 100);
                        break;
                      case IMAGETYPE_GIF:
                        $src = imagecreatefromstring(file_get_contents($fileName));
                        $destination = imagecreatetruecolor($new_Width, $new_Height);
                        imagecopyresampled($destination, $src, 0, 0, 0, 0, $new_Width, $new_Height, $width, $height);
                        imagegif($destination, $uploadPath . "thump_" . $imageName, $quality);
                        break;
                      case IMAGETYPE_PNG:
                        $src = imagecreatefromstring(file_get_contents($fileName));
                        $destination = imagecreatetruecolor($new_Width, $new_Height);
                        imagecopyresampled($destination, $src, 0, 0, 0, 0, $new_Width, $new_Height, $width, $height);
                        imagepng($destination, $uploadPath . "thump_" . $imageName, 9);
                        break;
                      default:
                        $src = imagecreatefromstring(file_get_contents($fileName));
                        $destination = imagecreatetruecolor($new_Width, $new_Height);
                        imagecopyresampled($destination, $src, 0, 0, 0, 0, $new_Width, $new_Height, $width, $height);
                        imagepng($destination, $uploadPath . "thump_" . $imageName, $quality);
                        break;
                    }

                    move_uploaded_file(@$file, $uploadPath . $imageName);
                    $imageProcess = 1;
                  } else {
                    echo "<div>
                  <p class='alert alert-danger'>Sorry!!! Your dimension is too small</p>
                </div>";
                    die;
                  }
                } else {

                  echo "<div>
                  <p class='alert alert-danger'>Sorry!!! Your file File too large</p>
                </div>";
                  die;
                }




                if ($imageProcess == 1) {
                  $outputImage = $uploadPath . "thump_" . $imageName;
                  echo "<img src= '$outputImage'/>";
                  echo '<br>';
                  echo "<div>
                  <p class='alert alert-success'>Image Resized Successfully</p>
                  
                </div>";
                  echo '<p><a class="btn btn-primary" href="download.php?file=' . urlencode("thump_" . $imageName) . '">Download Image</a></p>';
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