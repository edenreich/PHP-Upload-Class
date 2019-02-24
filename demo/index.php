<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Reich\Upload;
use Reich\Types\Rule;
use Reich\Types\MimeType;
use Reich\Interfaces\File;

?><!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<title>Files Uploader</title>
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
</head>
<?php

if (Upload::submitted()) {
    
    // give the constructor the name of the html input field
    $upload = Upload::picture('file');

    $upload->setDirectory('images')->create(true);

	// Upload::picture() is already setting this by default, feel free to override this
    // $upload->validator()->setRule(Rule::MimeTypes, [ MimeType::JPG, MimeType::PNG ]);

    $upload->encryptFileNames(true)->only('png');

	$upload->onSuccess(function(File $file) {
		// handle successful uploads.
		echo '<div class="alert alert-success">file ' . $file->getName() .' has been successfully uploaded!</div><br/>';
    });

    $upload->onError(function(File $file) {
		// handle faliure uploads.
		echo '<div class="alert alert-danger">couldn\'t upload ' . $file->getName() .'. '. $file->getErrorMessage() . '</div><br/>';
    });

    $upload->start();
}

?>
<body>
  <br/>
  <div class="container">
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
      <div class="form-group">
    		<input type="file" name="file[]" class="form-control-file" multiple required>
  		</div>
      <div class="form-group">
        <input type="submit" value="Upload" class="btn btn-primary">
  	 </div>
    </form>
</div>
</body>
</html>
