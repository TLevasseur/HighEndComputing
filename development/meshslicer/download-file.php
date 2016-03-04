<?php
session_start();

include('header.php');
include('Net/SFTP.php');

$fileToGet = $_GET['file'];
$localFile = 'uploads/'.$fileToGet;


$sftp = new Net_SFTP(SSH_HOST);
if ($sftp->login($_SESSION['id'], $_SESSION['passwd'])) {
	$sftp->get('/scratch/'.$_SESSION['id'].'/meshslicer/'.$fileToGet, $localFile);
	$reply = json_encode(array('Error' => '0', 'Message' => ""));
	//unlink($localFile);
}
else { 
	$reply = json_encode(array('Error' => '1', 'Message' => "Astral problem. Please try again."));
}

echo $reply;

?>