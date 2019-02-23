<?php

namespace Reich\Traits;

use Reich\Exceptions\InvalidEncryptionKeyException;

trait Encrypter
{
	/**
	 * Stores the encryption key.
	 *
	 * @var string
	 */	
	protected $key = 'fc01e8d00a90c1d392ec45459deb6f12';

	/**
	 * Store the file types that should be encrypted.
	 *
	 * @var array
	 */
	protected $fileTypesToEncrypt = [];

	/**
	 * Encrypt the file name.
	 *
	 * @param string  $fileName
	 * @return string
	 */
	public function encrypt(string $fileName): string
	{
	    $encryptMethod = "AES-256-CBC";

		$key = $this->config->get('encryption_key');

	    return @base64_encode(openssl_encrypt($fileName, $encryptMethod, $key));
	}

	/**
	 * Decrypt the file name.
	 *
	 * @param string  $fileName
	 * @return string
	 */
	public function decrypt(string $fileName): string
	{
		$encryptMethod = "AES-256-CBC";

		$key = $this->config->get('encryption_key');

		return openssl_decrypt(@base64_decode($fileName), $encryptMethod, $key);
	}

	/**
	 * Save the file/files with the 
	 * encrypted names on the server.
	 *
	 * @param boolean | $encrypt
	 * @return $this
	 */
	public function encryptFileNames($encrypt = false)
	{
		if ($encrypt == false) {
			return;
		}

		if (empty($this->key)) {
			throw new InvalidEcryptionKeyException;
		}

		if (! empty($this->fileInput)) {
			foreach($this->fileNames as $key => $fileName) {
				$encryptedName = $this->encrypt($fileName);
				$extension = $this->fileExtensions[$key];

				$this->files[$key]['encrypted_name'] = $encryptedName . "." . $extension;
				$this->files[$key]['encryption'] = true;
			}
		}

		return $this;
	}

	/**
	 * Allows to specify 
	 * which file types to encrypt.
	 *
	 * @param mixed | $types
	 * @return void
	 */
	public function only($types)
	{
		if (is_string($types) && $extensions = explode('|', $types)) {
			$this->fileTypesToEncrypt = $extensions;
			return;
		}

		if (! is_array($types)) {
			$this->fileTypesToEncrypt = func_get_args();
		} else {
			$this->fileTypesToEncrypt = $types;
		}
	}

	/**
	 * A simple gererator of a random
	 * key to use for encrypting.
	 *
	 * @return void
	 */
	public static function generateMeAKey()
	{
		$instance = new static;
		$key = $instance->randomString();

		echo hash('sha256', $key);
	}

	/**
	 * Checks if the file should be encrypted.
	 *
	 * @param array | $file
	 * @return boolean
	 */
	protected function shouldBeEncrypted($file)
	{
		return $file['encryption'] && $this->inOnlyArray($file);
	}

	/**
	 * Checks if only specific 
	 * file extensions were set.
	 *
	 * @return boolean
	 */
	protected function inOnlyArray($file)
	{
		if (empty($this->fileTypesToEncrypt)) {
			return false;
		}

		return in_array($file['extension'], $this->fileTypesToEncrypt);
	}

	/**
	 * Creates a random string.
	 *
	 * @param int | $length
	 * @return string
	 */
	protected function randomString($length = 64)
	{
		$string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            $bytes = random_bytes($size);

            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
	}
}