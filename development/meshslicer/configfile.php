<?php
session_start();

include('header.php');
include('Net/SFTP.php');

$str = $_POST['str'];

$_SESSION['config'] = explode(' ', $str);

$sftp = new Net_SFTP(SSH_HOST);
if ($sftp->login($_SESSION['id'], $_SESSION['passwd'])) {

	file_put_contents('uploads/config.ini', $str."\nnone");
	$sftp->put('/scratch/'.$_SESSION["id"].'/meshslicer/conf/config.ini', 'uploads/config.ini' , NET_SFTP_LOCAL_FILE);

	$reply = json_encode(array('Error' => '0', 'Message' => ""));
	unlink('uploads/config.ini');

}
else { 
	$reply = json_encode(array('Error' => '1', 'Message' => "Astral problem. Please try again."));
}

echo $reply;
?>