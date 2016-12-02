# Upload.class.php
PHP Class for uploading file or files to the server

# How to use this class

## Setting it up:

### Few things need to be done first:
##### 1) Make sure you change the random 32 Character key inside the class file.
##### 2) You can also use Upload::generateMeAKey() command, then just copy it and past it in the KEY const.
##### 3) Please open the example index.php file I created to follow and get a better understanding



#### make sure the form is submitted

```php
if(Upload::formIsSubmitted())
{
  // rest of the code goes here
}
```


#### instatiate the class

```php
$upload = new Upload(YOUR-HTML-INPUT-NAME); 
```


#### set the directory where you want to upload the files, by default it will upload to your main directroy

```php
$upload->setDirectory('img/'); 
```
#### you may also specify that you want to create this directory if it's not exists

```php
$upload->setDirectory('img/')->create(true); 
```


#### set an array of allowed file extensions, by default only 'jpg' and 'png' are allowed

```php
$upload->setAllowedExtensions(array('jpg', 'png'));
```


#### set the limit for upload size by Kilobyte, on the example under 2MB is allowed

```php
$upload->setMaxSize(2000); // This will only take effect if your php.ini config file allow this size to be uploaded
```


#### set this only if you want to have a encrypt file names(optional for security)

```php
$upload->encryptFileNames(true);
```

#### you may also specify that you want only certain file type to be encrypted like so:

```php
$upload->encryptFileNames(true)->only(array('jpg')); // only jpg files will be encrypted
```


#### after all is set just run the following command

```php
$upload->start();
``` 



## Error Handling

#### check wether there are errors and if there arent errors, proccess the upload

```php
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
}
```

#### here is another method to show you useful errors if something went wrong(normally if you didnt set the KEY)

```php
print_r($upload->errorsForDeveloper()); // There are some errors only you should look at while setting this up
```

### If you liked this script please feel to contact me so we can develop it further :-)



## Upcomming feautres:

##### - A way to allow you to add your custom error messages(still thinking which syntax will be easy to use).
##### - Nice method to display the errors easily with bootstrap design by calling $errorUpload->feedbackDisplay();