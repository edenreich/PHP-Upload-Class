# upload.class.php
Php Class for uploading file or files to the server

<h1>How to use this class</h1>
---------------------

<b>you could use this class on your front-end simple as that:</b>

$upload = new Upload(); // instatiate the class

$upload->setPath('img/'); // set the directory where you want to upload the files, by default it will upload to your main directroy

$upload->setAllowedExtentions( array('jpg') ); // set an array of allowed file extentions, by default every file type allowed

$upload->generateFileName(); // only if you want to have a different file names

if( empty( $upload->uploadErrors() ) ) // check wether there are errors 
{ 
	$upload->save(); // start the upload proccess and save the file(s)
}
else
{
	$errors = $upload->uploadErrors(); // get all the errors that stored in array
	foreach( $errors as $error )
	{
		echo $error; // output the errors
	}
}