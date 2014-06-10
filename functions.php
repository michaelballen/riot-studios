<?php
function require_from($dir){
	//get all files with a .php extension.
	$scripts = glob($dir . "/*.php");
	//print each file name
	foreach ($scripts as $script) {
		require_once $script;
	}
}
require_from(dirname(__FILE__) . '/functions');
global $Riot;
$Riot = new RiotWebsite();
?>