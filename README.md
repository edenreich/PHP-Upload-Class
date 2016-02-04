# upload.class.php
Php Class for uploading file or files to the server

<h1>How to use this class</h1>
---------------------

<b>you could use this class on your front-end simple as that:</b>

$upload = new Upload(); // instatiate the class

$upload->setPath('img/'); // set the directory where you want to upload the files, by default it will upload to your main directroy

$upload->setAllowedExtentions( array('jpg') ); // set an array of allowed file extentions, by default every file type allowed

$upload->generateFileName(); // only if you want to have a different file names

if( empty( $upload->uploadErrors() ) ) // check wether there are errors<br> 
{<br> 
	$upload->save(); // start the upload proccess and save the file(s)<br>
}<br>
else<br>
{<br>
	$errors = $upload->uploadErrors(); // get all the errors that stored in array<br>
	foreach( $errors as $error )<br>
	{<br>
		echo $error; // output the errors<br>
	}<br>
}<br>