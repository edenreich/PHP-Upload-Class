# upload.class.php
Php Class for uploading file or files to the server

# How to use this class

## you could use this class on your front-end simple as that:


###### *instatiate the class*

```ruby
$upload = new Upload(); 
```


###### *set the directory where you want to upload the files, by default it will upload to your main directroy*

```ruby
$upload->setPath('img/'); 
```


###### *set an array of allowed file extentions, by default every file type allowed*

```ruby
$upload->setAllowedExtentions( array('jpg', 'png') );
```


###### *set this only if you want to have a random file names*

```ruby
$upload->generateFileName();
```


###### *check wether there are errors*

```ruby
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