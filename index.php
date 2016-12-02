<?php
require_once 'Upload.class.php';

if(Upload::formIsSubmitted())
{
  $upload = new Upload('file'); // give the constructor the name of the html input field

  $upload->setDirectory('images')->create(true)
         ->setAllowedExtensions(['jpg', 'png'])
         ->setMaxSize(1500)
         ->encryptFileNames(true)->only(['png'])
         ->start();

  if($upload->hasErrors())
  {
    foreach($upload->errors() as $errorUpload)
    {
      echo $errorUpload->name; // The file name
      echo $errorUpload->encryptedName; // The encrypted name of the file
      echo $errorUpload->type; // The native type name of the file(in case needed)
      echo $errorUpload->extension; // The extension of the file(in lowercase)
      echo $errorUpload->size; // The size of the file
      echo $errorUpload->error; // The native error property(in case needed)
      echo $errorUpload->message; // The error message
    }
  } else {
    // show success
  }

  print_r($upload->errorsForDeveloper()); // There are some errors only you should look at while setting this up
}
?><!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<title>Files Uploader</title>
</head>
<body>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
		<input type="file" name="file[]" multiple>
		<input type="submit" value="Upload">
	</form>
</body>
</html>