<?php
$file="fores/dl/".$_GET["id"]."/".$_GET["file"];
  if(!file_exists($file)) {
	  header("HTTP/2.0 404 Not Found");
	  exit();
  }
	header("HTTP/1.1 200 ok");
	header("Content-Type: multipart/encrypted");
	header("Content-Disposition: attachment; filename=\"2CRAFT__".$_GET["file"]."\"");
	header("Content-Length: ".filesize($file));
	readfile($file);
?>