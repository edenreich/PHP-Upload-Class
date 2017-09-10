<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Source\Upload;

if(Upload::submitted())
{
  $upload = new Upload('file'); // give the constructor the name of the html input field

  $upload->setDirectory('images')->create(true);

  $upload->addRules([
            'size' => 1500,
            'extensions' => 'jpg|png',
          ])->customErrorMessages([
            'size' => 'Please upload files that are less than 2MB size',
            'extensions' => 'Please upload only jpg, png or pdf'
          ]);

  $upload->encryptFileNames(true)->only('png|png');

  $upload->start();

  
  // if($upload->unsuccessfulFilesHas())
  // {
  //   foreach($upload->errorFiles() as $file)
  //   {
  //      // now you have the $file object to format the message how you prefer
  //   }
  // } 
 
  // if($upload->successfulFilesHas())
  // {
  //    foreach($upload->successFiles() as $file)
  //    {
  //       // now you have the $file object to format the message how you prefer
  //    }
  // }

}
?><!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<title>Files Uploader</title>
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
</head>
<?php
if(Upload::submitted())
{
  if($upload->unsuccessfulFilesHas())
  {
    $upload->displayErrors();
  }
  else if($upload->successfulFilesHas())
  {
    $upload->displaySuccess();
  }
}
?>
<body> 
  <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
		<input type="file" name="file[]" multiple>
		<input type="submit" value="Upload">
	</form>
</body>
</html>