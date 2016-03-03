<?php
session_start();

include('Net/SFTP.php');

$str = $_POST['str'];

$sftp = new Net_SFTP('hpclogin-1.central.cranfield.ac.uk');
if ($sftp->login($_SESSION['id'], $_SESSION['passwd'])) {

	file_put_contents('uploads/config.ini', $str);
	$sftp->put('/scratch/'.$_SESSION["id"].'/meshslicer/conf/config.ini', 'uploads/config.ini' , NET_SFTP_LOCAL_FILE);

	$reply = json_encode(array('Error' => '0', 'Message' => ""));
	unlink('uploads/config.ini');

}
else { 
	$reply = json_encode(array('Error' => '1', 'Message' => "Astral problem. Please try again."));
}

echo $reply;
?>