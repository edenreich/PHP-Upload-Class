<p align="center"><img src="https://s11.postimg.org/6rrm3zatv/elephantsmall.jpg"></p>

## Upload.php
<br/>
<p>
  <a href="#">
    <img src="https://travis-ci.org/edenreich/PHP-Upload-Class.svg?branch=master">
  </a>
  <a href="https://packagist.org/packages/reich/upload">
    <img src="https://poser.pugx.org/reich/upload/downloads">
  </a>
  <a href="https://packagist.org/packages/reich/upload">
    <img src="https://poser.pugx.org/reich/upload/v/stable">
  </a>
  <a href="#">
    <img src="https://img.shields.io/github/forks/edenreich/PHP-Upload-Class.svg">
  </a>
  <a href="#">
    <img src="https://img.shields.io/github/stars/edenreich/PHP-Upload-Class.svg">
  </a>
  <a>
     <img src="https://img.shields.io/github/issues/edenreich/PHP-Upload-Class.svg">
  </a>
  <a href="https://packagist.org/packages/reich/upload">
    <img src="https://poser.pugx.org/reich/upload/license">
  </a>
</p>
PHP Class for uploading file or files to the server

## Installing
with composer just run:
```shell 
composer require reich/upload
```

## As a Package for Laravel
This class is fully integrated into laravel framework using Laravel Auto-Discovery.
If you don't use v5.5+ you may import the necessary files path in your /config/app.php file manually:
```php
  ...
    "providers" => [
      /*
       * Package Service Providers...
       */
      Reich\Upload\Laravel\UploadServiceProvider::class,
    ],

    "aliases" => [
      "Upload" => Reich\Upload\Laravel\UploadFacade::class,
    ]
  ...
```

## Notes:
1) Run Reich\Upload::generateMeAKey(); helper and copy the generated key into your config file(optional).
2) Please open the example /demo/index.php file I created to follow and get a better understanding
3) Most of the configurations can also be set using the config file(saves some repeated code), in order for this to happen you need to call loadConfig() method.

## Usage

Make sure the form is submitted:
```php
if(Upload::submitted())
{
  // rest of the code goes here
}
```

Make an instance of the class
```php
$upload = new Upload(YOUR-HTML-INPUT-NAME); 
```

Set the directory where you want to upload the files, by default it will upload to your main directroy
```php
$upload->setDirectory('img/'); 
```

You may also specify that you want to create this directory if it's not exists
```php
$upload->setDirectory('img/')->create(true); 
```

You can set the rules you want for your upload using the following syntax:
```php
$upload->addRules([
        'size' => 2000,
        'extensions' => 'png|jpg|pdf'
]);
```
or
```php
$upload->addRules([
        'size' => 2000,
        'extensions' => ['png', 'jpg', 'pdf']
]);
```

Set this only if you want to have a encrypt file names(optional for security):
```php
$upload->encryptFileNames(true);
```

You may also specify that you want only certain file type to be encrypted like so:
```php
$upload->encryptFileNames(true)->only(['jpg']); // only jpg files will be encrypted
```
Or also the following syntax:
```php
$upload->encryptFileNames(true)->only('jpg|png|txt'); // only jpg, png and txt files will be encrypted
```

At your own risk, you may also specify that you wish the downloads to be proccessed async.
```php
$upload->async(true);
``` 

After all is set just run the following command
```php
$upload->start();
``` 


## Events
Whenever a file has been successfully uploaded.
```php
$upload->success(function($file) {
  // handle the file
});
```
If something went wrong listen to error.
```php
$upload->error(function($file) {
  // handle the file
});
```

## Error Handling

Check wether there are errors and if there arent errors, proccess the upload:
```php
if($upload->unsuccessfulFilesHas())
{
  // display all errors with bootstraps
  $upload->displayErrors();

  // now of course you may formating it differently like so
  foreach($upload->errorFiles as $file)
  {
    // do whatever you want with the file object
    // - $file->name
    // - $file->encryptedName *only if you asked to encrypt*
    // - $file->type
    // - $file->extension
    // - $file->size
    // - $file->error
    // - $file->errorMessage
  }
}
else if($upload->successfulFilesHas())
{
  $upload->displaySuccess();

  // now of course you may formating it differently like so
  foreach($upload->successFiles as $file)
  {
    // do whatever you want with the file object
    // - $file->name
    // - $file->encryptedName *only if you asked to encrypt*
    // - $file->type
    // - $file->extension
    // - $file->size
  }
}
```

#### Here is another method to show you useful errors if something went wrong:

```php
print_r($upload->debug()); // There are some errors only you should look at while setting this up
```
