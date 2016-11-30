<?php
require_once 'Upload.class.php';

if(Upload::formIsSubmitted())
{


$upload = new Upload();

$upload->setDirectory('images')
       ->setAllowedExtentions(['jpg', 'png'])
       ->setMaxSize(2000)
       ->encryptFileNames(false)
       ->start();
}
// $upload->setFilePath('img/'); 

// $upload->setAllowedExtentions( array('jpg', 'png') );

// $upload->setMaxSize( 2000000 );

// $upload->generateFileName();

// if( empty( $upload->uploadErrors() ) ) 
// {
//   // start the upload proccess and save the file(s)
//   $upload->save();

//   // check for errors again to see if there was some invalid files that couldnt be uploaded
//   if( !empty( $upload->uploadErrors() ) )
//   {
//    // get all the errors of the files that couldnt be uploaded for some reason
//     $errors = $upload->uploadErrors(); 

//     foreach( $errors as $error )
//     {
//      // output the errors with the invalid files
//       echo $error;
//     }
//   }
// }
// else
// {
//   // get all the errors that stored in array
//   $errors = $upload->uploadErrors(); 

//   foreach( $errors as $error )
//   {
//    // output the errors
//     echo $error;
//   }
// }

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