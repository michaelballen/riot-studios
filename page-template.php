<?php
$temp_name = $_REQUEST['temp_name'];
$temp_file = 'templates' . '/' . $temp_name . '.php';
include($temp_file);
?>