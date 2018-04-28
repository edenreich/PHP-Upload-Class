<?php

namespace Reich\Traits;

trait AsyncRequest
{
	/**
	 * Indicates if the upload should be async.
	 *
	 * @var bool
	 */
	protected $async;

	/**
	 * Stores the curl handlers.
	 *
	 * @var array
	 */
	protected $curlHandlers = [];

	/**
	 * Stores the curl multi handler.
	 *
	 * @var resource
	 */
	protected $curlMultiHandle = null;

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
	public function shouldBeAsync()
	{
		if (! empty($this->async)) {
			return $this->async;
		}

		if (! empty($this->config)) {
			return $this->config['async'];
		}
	}

	/**
	 * Registers asyncrouns post handler.
	 *
	 * @param string | $url
	 * @param array | $data
	 * @return void
	 */
	public function register($url , $data = []) 
	{
		if (empty($data)) {
			return;	
		}

		if (is_null($this->curlMultiHandle)) {
			$this->curlMultiHandle = curl_multi_init();
		}

	    $curlHandler = curl_init($url);

	    $file = [
		    'file[0]' => new \CurlFile($data['tmp_name'], $data['type'], $data['name'])
		];

	    $options = [
	        CURLOPT_ENCODING => 'gzip',
	        CURLOPT_FOLLOWLOCATION => true,
	        CURLOPT_RETURNTRANSFER => true,
	        CURLOPT_POST => 1,
	        CURLOPT_POSTFIELDS => $file,
	        CURLOPT_USERAGENT => 'Curl'
	    ];

	    curl_setopt_array($curlHandler, $options);

	    $this->curlHandlers[] = $curlHandler;
	    curl_multi_add_handle($this->curlMultiHandle, $curlHandler);
	}

	/**
	 * Executes the post request handlers.
	 *
	 * @return string
	 */
	public function executeAll() 
	{
	    $responses = [];

	    $running = null;
	    
	    do {
	    	if (curl_multi_select($this->curlMultiHandle) == -1) {
		        usleep(100);
		    }

	        curl_multi_exec($this->curlMultiHandle, $running);
	    } while ($running > 0);

	    foreach ($this->curlHandlers as $id => $handle) {
	        $responses[$id] = curl_multi_getcontent($handle);
	        curl_multi_remove_handle($this->curlMultiHandle, $handle);
	    }
	    
	    curl_multi_close($this->curlMultiHandle);

	    return $responses;
	}
}
