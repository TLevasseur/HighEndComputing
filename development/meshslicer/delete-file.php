<?php
session_start();

include('header.php');
include('Net/SFTP.php');

$file = $_GET['file'];

$sftp = new Net_SFTP(SSH_HOST);
if ($sftp->login($_SESSION['id'], $_SESSION['passwd'])) {

	$sftp->delete('/scratch/'.$_SESSION['id'].'/meshslicer/'.$file);

	$reply = json_encode(array('Error' => '0', 'Message' => ""));

	unset($_SESSION['fileList'][array_search($file, $_SESSION['fileList'])]);

}
else { 
	$reply = json_encode(array('Error' => '1', 'Message' => "Astral problem. Please try again."));
}

echo $reply;

?>