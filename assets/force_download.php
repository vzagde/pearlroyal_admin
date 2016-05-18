<?php
	$file = $_GET['file_path'];
	//echo $file;
	// Add a file type check here for security purposes so that nobody can-
	// download PHP files or other sensitive files from your server by spoofing this script
	header('Content-type: image/jpg');
	header('Content-Disposition: attachment; filename="'.$file.'"');
	readfile($file);
	exit();
?>