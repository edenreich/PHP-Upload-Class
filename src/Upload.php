<?php

namespace Reich;

use Reich\Classes\Input;
use Reich\Classes\Validator;
use Reich\Classes\Request;
use Reich\Classes\Upload as UploadClass;

use Reich\Interfaces\UploadInterface;

use Reich\Types\Rule;
use Reich\Types\MimeType;
use Reich\Types\Extension;

class Upload
{
    /**
     * Create an upload for pictures.
     * 
     * @param string  $name
     * @param array  $rules
     * @return \Reich\Interfaces\UploadInterface
     */
    public static function picture(string $name, array $rules = []): UploadInterface
    {
        $input = new Input($name);
        $validator = new Validator($input, $rules);
        $request = new Request;

        $validator->setRule(Rule::MimeTypes, [ MimeType::JPG, MimeType::PNG ]);
        $validator->setRule(Rule::Extensions, [ Extension::JPG, Extension::JPEG, Extension::PNG ]);

        return new UploadClass($input, $validator, $request);
    }

    /**
     * Create an upload for generic files.
     * 
     * @param string  $name
     * @param array  $rules
     * @return \Reich\Interfaces\UploadInterface
     */
    public static function file(string $name, array $rules = []): UploadInterface
    {
        $input = new Input($name);
        $validator = new Validator($input, $rules);
        $request = new Request;

        return new UploadClass($input, $validator, $request);
    }

    public static function __callStatic($method, $args)
    {
        return UploadClass::$method(...$args);
    }
}
