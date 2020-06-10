
<?php required_once('./include/header.php');?>
	<section class="container">
		<div class="form-group custom-input-space has-feedback">
<div class ="page-heading">
<h3 class="post-title">Image upload and resize using PHP</h3>
</div>

<div class="page-body clearfix">
<div class="row">
<div class="col-md-offset-2 col-md-8">
<div class= "panel panel-default">
<div class="panel-heading"> Image Upload and Resize it:</div>
<div class="panel-body">

<form action="" method="post" enctype="multipart/form-data">
<div class="form-group">
<label class="">select image</label>
<input type="file" name="upload_image" />
</div>

<input type="submit" name="form_submit" class="btn-primary" value="submit" /> </form>


<?php
function resizeImage ($resourceType,$image_width,$image_height)
{
$resizeWidth = 250;
$resizeHeight = 250;
$imagelayer =- imagecreatetruecolor($resizeWidth,$resizeHeight);
imagecopyresampled($imageLayer,$resourceType,0,0,0,0,$resizeWidth,$resizeHeight, $image_width,$image_height);
return $imageLayer
}

if(isset($_POST["form_submit]))
{
$imageProcess = 0;
if(is_array($_FILES))
{$fileName = $_FILES['upload_image']['tmp_name'];
 $sourceProperties = getimagesize($fileName);
 $resizeFileName = time();
 $uploadPath = ",/upload/";
 $fileExt = pathinfo($_FILES['upload_image']['name'], PATHINFO_EXTENSION);
 $uploadImageType = $sourceProperties[2];
 $sourceImageWidth = $sourceProperties[0];
 $sourceimageHeight = $sourceproperties[1];

switch ($uploadImageType)
{
case IMAGETYPE_JPEG:
     $resourceType = imagecreatefromjpeg($fileName);
     $imageLayer = resizeImage($resourceType,$sourceImageWidth,$resourceImageHeight);
     imagejpeg($imageLayer,$uploadPath."thump_".$resizeFileName.'.'.$fileExt);
     break;

case IMAGETYPE_GIF:
     $resourceType = imagecreatefromgif($fileName);
     $imageLayer = resizeImage($resourceType,$sourceImageWidth,$resourceImageHeight);
     imagegif($imageLayer,$uploadPath."thump_".$resizeFileName.'.'.$fileExt);
     break;

case IMAGETYPE_PNG:
     $resourceType = imagecreatefrompng($fileName);
     $imageLayer = resizeImage($resourceType,$sourceImageWidth,$resourceImageHeight);
     imagepng($imageLayer,$uploadPath."thump_".$resizeFileName.'.'.$fileExt);
     break;

     default:
	$imageProcess = 0;
	break;

}

move_uploaded_file($file, $uploadPath. $resizeFileName. ".". $fileExt);
$imageProcess =1;
}

if($imageProcess == 1){

?>

<div class="alert icon-alert width-arrow alert-succes form-alert" role="alert">
	<i class="fa-fw fa-check-circle"></i>
	<strong> Success ! </strong> <span class="success-message"> Image Resize Successfully </span>
</div>

<?php
}else{
?>

<div class="alert icon-alert width-arrow alert-succes form-alert" role="alert">
	<i class="fa-fw fa-check-circle"></i>
	<strong> Note ! </strong> <span class="warning-message"> Invalid Image </span>
</div>

<?php
}
$imageProcess = 0;
}
?>

</div>
</div>
</div>

</div>
</div>
</div>

</selection>
</div>


<script src= "js/jquerry.min.js"></script>

<script src= "js/bootstrap.min.js"></script>
