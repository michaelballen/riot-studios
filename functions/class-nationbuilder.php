<?php

class NationBuilder {
	private
		$uID = '58f9a414d5e4bef9f50b90b20d333574bbf0c711cc0b370c6d730622e8df03d7',
		$secret = 'f4fbbb6545aaacf6c300306f5a4aad71e8b583c272d77fd7e621934eb9a0365e';
	function __construct () {
		$this->acessToken = $this->_getAccessToken();
	}
	private function _getAccessToken () {
		$ch = curl_init(); 
		// set url
		curl_setopt($ch, CURLOPT_URL, "https://riot.nationbuilder.com/oauth/authorize?response_type=code&client_id=" . $this->uID . "&redirect_uri=http%3A%2F%2Friotstudios.com"); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		// $output contains the output string
		$output = json_decode(curl_exec($ch));
		// close curl resource to free up system resources 
		curl_close($ch);
		return $output;
	}
}