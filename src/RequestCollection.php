<?php

namespace Pephpit\HttpClient;

/**
 * Send parallel requests
 */
class RequestCollection extends AbstractCollection
{

	// @property ressource $mh Curl Multi Handler
	protected $mh;

	/**
	 * Sets a Request in the collection at the specified key/index.
	 *
	 * @param string|integer $key   The key/index of the element to set.
	 * @param \Pephpit\HttpClient\Request $request Request
	 */
	public function add($request) {
		if($request instanceof Request === false)
			throw new \InvalidArgumentException('$request must be an instance of \Pephpit\HttpClient\Request');

		parent::add($request);
	}

	/**
	 * Sets a Request in the collection at the specified key/index.
	 *
	 * @param string|integer $key   The key/index of the element to set.
	 * @param \Pephpit\HttpClient\Request $request Request
	 */
	public function set($key, $request) {
		if($request instanceof Request === false)
			throw new \InvalidArgumentException('$request must be an instance of \Pephpit\HttpClient\Request');

		parent::set($key, $request);
	}

	/**
	 * Gets the element at the specified key/index.
	 *
	 * @param string|integer $key The key/index of the element to retrieve.
	 *
	 * @return \Pephpit\HttpClient\Request
	 */
	public function get($key) {
		return parent::get($key);
	}

	/**
	 * init multi request from Request instance
	 */
	protected function init() {

		$this->mh = curl_multi_init();

		foreach ($this->collection as $key => $request) {
			$request->init();
			curl_multi_add_handle($this->mh, $request->getCurlHandler());
		}
	}

	/**
	 * Execute requests
	 * @return \Pephpit\HttpClient\ResponseCollection
	 */
	public function send() {

		$this->init();

		$active = null;
		$selectTimeout = 0.001;

		//execute the handles
		do {
			$status = curl_multi_exec($this->mh, $active);

			if ($active && curl_multi_select($this->mh, $selectTimeout) === -1) {
				// Perform a usleep if a select returns -1: https://bugs.php.net/bug.php?id=61141
				usleep(150);
			}
		} while ($status === CURLM_CALL_MULTI_PERFORM || $active);

		$responses = new ResponseCollection();

		foreach ($this->collection as $key => $request) {
			$responses[$key] = new Response(curl_multi_getcontent($request->getCurlHandler()),
											curl_getinfo($request->getCurlHandler()),
											curl_error($request->getCurlHandler()));
		}

		return $responses;
	}

	//close the handles
	public function __destruct() {

		if (empty($this->mh))
			return;

		foreach ($this->collection as $key => $request) {
			curl_multi_remove_handle($this->mh, $request->getCurlHandler());
		}

		curl_multi_close($this->mh);
	}

}
