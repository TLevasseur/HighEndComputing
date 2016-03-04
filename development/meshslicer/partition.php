<?php
session_start();

include('Net/SFTP.php');

$file = $_GET['file'];

$sftp = new Net_SFTP('hpclogin-1.central.cranfield.ac.uk');
if ($sftp->login($_SESSION['id'], $_SESSION['passwd'])) {

	$sftp->exec('cd $w/meshslicer/conf/; sed -i "2s/.*/'.$file.'/" config.ini');
	$sftp->exec('cd $w/meshslicer/conf/; module load metis; ./StandAlone > output.txt');

}
else { 
	$reply = json_encode(array('Error' => '1', 'Message' => "Astral problem. Please try again."));
}

echo $reply;
?>