<?php

$dimension = 300;
$filename = $_FILES['upload_image']['tmp_name'];
$file_name = $_FILES['upload_image']['name'];

$image_size = getimagesize($filename);
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


$src = imagecreatefromstring(file_get_contents($filename));
$destination = imagecreatetruecolor($new_Width, $new_Height);

imagecopyresampled($destination, $src, 0, 0, 0, 0, $new_Width, $new_Height, $width, $height);

imagedestroy($src);
imagedestroy($destination);

move_uploaded_file($filename, $file_name);


// ////////// from Index.php page

$filename = $_FILES['upload_image']['tmp_name'];
$file_name = $_FILES['upload_image']['name'];

$image_size = getimagesize($filename);
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


$src = imagecreatefromstring(file_get_contents($filename));
$destination = imagecreatetruecolor($new_Width, $new_Height);

imagecopyresampled($destination, $src, 0, 0, 0, 0, $new_Width, $new_Height, $width, $height);
imagepng($destination, $filename, $quality);
move_uploaded_file($filename, $file_name);
imagedestroy($src);
imagedestroy($destination);
$imageProcess = 1;
