<?php
  require 'php/config.php';
  require 'php/db.php';
  require "php/com.solvemedia/solvemedia.php";
?>
<?php
  $db=new DBOperator($dbHost,$dbName,$dbUser,$dbPassword);
?>
<?php
  switch($_SERVER['REQUEST_METHOD']) {
	  case 'POST':
	    $captcha=solvemedia_check_answer($smvkey,$_SERVER["REMOTE_ADDR"],$_POST["adcopy_challenge"],$_POST["adcopy_response"],$smhkey);
		if (!$captcha->is_valid) {
	      switch($captcha->error) {
			  case 'wrong answer':
			  header('HTTP/2.0 307 Permament Redirect');
			  header('Location: /mod/'.$_GET['id'].'/undefined?reason=bad_captcha');
			  break;
		  }
        } else {
			if(!$db->_addComment($_POST['name'],$_POST['commentBody'],$_GET['id'],time())) {
			  header('HTTP/2.0 307 Permament Redirect');
			  header('Location: /mod/'.$_GET['id'].'/undefined?reason=server_error');
			} else {
			  header('HTTP/2.0 307 Permament Redirect');
			  header('Location: /mod/'.$_GET['id'].'/undefined');
			}
        }
		break;
	  case 'GET':
	    if(!(isset($_GET['id'])&&isset($_GET['quantity'])&&isset($_GET['offset']))) {
			header('HTTP/2.0 400 Bad Request',true,400);
			exit(json_encode(array('result'=>'failure','cause'=>'bad request')));
		}
		$db=new DBOperator($dbHost,$dbName,$dbUser,$dbPassword);
		header('HTTP/2.0 200 OK',true,200);
		exit(json_encode($db->_getCommentsAsArray($_GET['id'],$_GET['offset'],$_GET['quantity'])));
  }
?>