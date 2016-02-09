# upload.class.php
Php Class for uploading file or files to the server

# How to use this class

## you could use this class on your front-end simple as that:

###### *Please make sure your upload html input name is called 'file'*


###### *instatiate the class*

```php
$upload = new Upload(); 
```


###### *set the directory where you want to upload the files, by default it will upload to your main directroy*

```php
$upload->setFilePath('img/'); 
```


###### *set an array of allowed file extentions, by default every file type allowed*

```php
$upload->setAllowedExtentions( array('jpg', 'png') );
```


###### *set the limit for upload size by Kilobyte, on the example under 2MB is allowed*

```php
$upload->setMaxSize( 2000000 );
```


###### *set this only if you want to have a random file names(optional)*

```php
$upload->generateFileName();
```


###### *check wether there are errors and if there arent errors, proccess the upload*

```php
if( empty( $upload->uploadErrors() ) ) 
{
  // create folder if not exist(optional)
  $upload->createFoldersIfNotExists();

  // start the upload proccess and save the file(s)
  $upload->save();

  // check for errors again to see if there was some invalid files that couldnt be uploaded
  // could be because of the php.ini configuration file not allowing or any other reason
  if( !empty( $upload->uploadErrors() ) )
  {
  	// get all the errors of the files that couldnt be uploaded for some reason
    $errors = $upload->uploadErrors(); 

    foreach( $errors as $error )
    {
  	  // output the errors with the invalid files
      echo $error;
    }
  }
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