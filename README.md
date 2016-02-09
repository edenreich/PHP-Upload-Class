# upload.class.php
Php Class for uploading file or files to the server

# How to use this class

## you could use this class on your front-end simple as that:


###### *instatiate the class*

```
$upload = new Upload(); 
```


###### *set the directory where you want to upload the files, by default it will upload to your main directroy*

```
$upload->setPath('img/'); 
```


###### *set an array of allowed file extentions, by default every file type allowed*

```
$upload->setAllowedExtentions( array('jpg') );
```


###### *only if you want to have a different file names*

```
$upload->generateFileName();
```


###### *check wether there are errors*

```
if( empty( $upload->uploadErrors() ) ) 
{
  // start the upload proccess and save the file(s)

  $upload->save(); 
}
else
{
  // get all the errors that stored in array
  $errors = $upload->uploadErrors(); 

  foreach( $errors as $error )
  {
  	// output the errors
    echo $error;
  }
}
```