<?php
session_start();

include('Net/SFTP.php');

$sftp = new Net_SFTP('hpclogin-1.central.cranfield.ac.uk');
if ($sftp->login($_SESSION['id'], $_SESSION['passwd'])) {

	

}
else { 
	$reply = json_encode(array('Error' => '1', 'Message' => "Astral problem. Please try again."));
}

echo $reply;
?>