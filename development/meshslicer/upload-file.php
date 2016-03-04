<?php
session_start();

include('header.php');
include('Net/SFTP.php');

$ds = DIRECTORY_SEPARATOR;
 
$storeFolder = 'uploads';
 
if (!empty($_FILES)) {

    $tempFile = $_FILES['file']['tmp_name'];             
    $targetPath = dirname( __FILE__ ) . $ds. $storeFolder . $ds;
    $targetFile =  $targetPath. $_FILES['file']['name'];
    move_uploaded_file($tempFile, $targetFile);

    $sftp = new Net_SFTP(SSH_HOST);
	if ($sftp->login($_SESSION["id"], $_SESSION["passwd"])) {
		$sftp->put('/scratch/'.$_SESSION["id"].'/meshslicer/'.$_FILES['file']['name'], $targetFile , NET_SFTP_LOCAL_FILE);
		$_SESSION['fileList'][] = $_FILES['file']['name'];
		sort($_SESSION['fileList']);
	}
	else {

	}

	unlink($targetFile);
     
}
?>