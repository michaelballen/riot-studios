<?php

function fetchData ($url) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 20);
	$result = curl_exec($ch);
	curl_close($ch); 
	return $result;
}

	$result = fetchData("https://api.instagram.com/oauth/authorize?client_id=&redirect_uri=&scope=&response_type=code");
	$result = json_decode($result);
	print_r($result);
?>