<?php

namespace Reich\Traits;

trait AsyncOperations
{
	/**
	 * Indicates if the upload should be async.
	 *
	 * @var bool
	 */
	protected $async;

	/**
	 * Stores the curl multi handler.
	 *
	 * @var bool
	 */
	protected $asyncMultiHandler = null;

	/**
	 * Setter for async upload.
	 *
	 * @param bool | $flag
	 * @return $this
	 */
	public function async($flag = true)
	{
		$this->async = $flag;

		return $this;
	}

	/**
	 * Checks if the upload should be asynchronously.
	 *
	 * @return bool
	 */
	protected function shouldBeAsync()
	{
		if (! empty($this->async)) {
			return $this->async;
		}

		if (! empty($this->config)) {
			return $this->config['async'];
		}
	}

	/**
	 * Adds asyncrouns post handler.
	 *
	 * @return void
	 */
	protected function addPostAsyncHandler(&$file)
	{
		if (is_null($this->asyncMultiHandler)) {
			$this->asyncMultiHandler = curl_multi_init();
		}

		$url = 'http://'. $_SERVER['HTTP_HOST']. $_SERVER['REQUEST_URI'];

		$curl = curl_init($url);
	    
	    $options = [
	        CURLOPT_HTTPHEADER => [ 'User-Agent: Curl' ],
	        CURLOPT_SSL_VERIFYPEER => false,
	        CURLOPT_ENCODING => "gzip",
	        CURLOPT_FOLLOWLOCATION => true,
	        CURLOPT_RETURNTRANSFER => true,
	        CURLOPT_POST => 1,
	        CURLOPT_POSTFIELDS => $file
	    ];

	    curl_setopt_array($curl , $options);

		curl_multi_add_handle($this->asyncMultiHandler,$curl);
	}

	/**
	 * Executes the post request handlers.
	 *
	 * @return void
	 */
	protected function postAsyncHandlers()
	{
		$running = null;
		
		do {
		    $mrc = curl_multi_exec($this->asyncMultiHandler, $running);
		    //file_put_contents('log.txt', $running);
		} while ($running > 0);

		while ($running && $mrc == CURLM_OK) {
		    if (curl_multi_select($this->asyncMultiHandler) != -1) {
		        do {
		            $mrc = curl_multi_exec($this->asyncMultiHandler, $running);
		        } while ($running > 0);
		    }
		}

		curl_multi_close($this->asyncMultiHandler);
	}
}