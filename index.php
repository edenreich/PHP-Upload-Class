<?php
require_once 'Upload.class.php';

if(Upload::formIsSubmitted())
{


$upload = new Upload();

$upload->setDirectory('images')
       ->setAllowedExtentions(['jpg', 'png'])
       ->setMaxSize(1500)
       ->encryptFileNames(true)->only(['jpg'])
       ->start();

if($upload->hasErrors())
  print_r($upload->errors());

}


?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<title>Another Files Test</title>
</head>
<body>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
		<input type="file" name="file[]" multiple>
		<input type="submit" value="Upload">
	</form>
</body>
</html>