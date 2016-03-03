<?php
session_start();

include('Net/SFTP.php');

$file = $_GET['file'];

$sftp = new Net_SFTP('hpclogin-1.central.cranfield.ac.uk');
if ($sftp->login($_SESSION['id'], $_SESSION['passwd'])) {

	$sftp->delete('/scratch/'.$_SESSION['id'].'/meshslicer/'.$file);

	$reply = json_encode(array('Error' => '0', 'Message' => ""));

	unset($_SESSION[array_search($file, $_SESSION['fileList'])]);

}
else { 
	$reply = json_encode(array('Error' => '1', 'Message' => "Astral problem. Please try again."));
}

echo $reply;

?>