<?php

function getYoutubeId ($str) {
	if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $str, $match)) {
	    return $match[1];
	}
	error_log('no matchs');
	return FALSE;
}
function getVimeoId ($str) {
	preg_match('~(?:<iframe[^>]*src=")?(?:https?:\/\/(?:[\w]+\.)*vimeo\.com(?:[\/\w]*\/videos?)?\/([0-9]+)[^\s]*)"?(?:[^>]*></iframe>)?(?:<p>.*</p>)?~ix', $arr['content_video'], $matches);
	if (!empty($matches)) {
		return $matches[1];
	}
	return FALSE;
}
function getYoutubeImage ($id) {
	return 'http://i3.ytimg.com/vi/' . $id . '/hqdefault.jpg';
}
function getVimeoImage ($id, $info = 'thumbnail_large') {
	if (!function_exists('curl_init')) {
		return 'CURL is not installed!';
	}
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "http://vimeo.com/api/v2/video/$id.php");
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	$output = unserialize(curl_exec($ch));
	$output = $output[0][$info];
	curl_close($ch);
	return $output;
}
function getVideoType ($url) {
	if (preg_match('~youtu~i', $url)) {
		return 'youtube';
	}
	if (preg_match('vimeo', $url)) {
		return 'vimeo';
	}
	return FALSE;
}
function getVideoId ($type, $url) {
	if ($type == 'youtube') {
		return getYoutubeId($url);
	}
	if ($type == 'vimeo') {
		return getVimeoId($url);
	}
	return false;
}
function getVideoImage ($type, $id) {
	if ($type == 'youtube') {
		return getYoutubeImage($id);
	}
	if ($type == 'vimeo') {
		return getVimeoImage($id);
	}
	return false;
}