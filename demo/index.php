<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Reich\Upload;

if (Upload::submitted()) {
  // give the constructor the name of the html input field
  $upload = Upload::picture('file');

  $upload->loadConfig();

  $upload->async(true);

  $upload->setDirectory('images')->create(true);

  $upload->encryptFileNames(true)->only('png');

  $upload->start();

  $upload->success(function($file) {
    // handle successful uploads.
  });

  $upload->error(function($file) {
    // handle faliure uploads.
  });
}

?><!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<title>Files Uploader</title>
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
</head>
<?php

if (Upload::submitted()) {
  if ($upload->unsuccessfulFilesHas()) {
    $upload->displayErrors();
  }
  elseif ($upload->successfulFilesHas()) {
    $upload->displaySuccess();
  }
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
