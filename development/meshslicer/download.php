<?php
session_start();

include('Net/SFTP.php');

$fileToGet = $_GET['file'];
$localFile = 'uploads/'.$fileToGet;

echo $fileToGet;

$sftp = new Net_SFTP('hpclogin-1.central.cranfield.ac.uk');
if ($sftp->login($_SESSION['id'], $_SESSION['passwd'])) {
	$sftp->get('/scratch/'.$_SESSION['id'].'/meshslicer/'.$_GET['file'], $localFile);
	
}
else {
	
}

?>