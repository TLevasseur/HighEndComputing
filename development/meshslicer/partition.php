<?php
session_start();

include('header.php');
include('Net/SFTP.php');

$file = $_GET['file'];

$sftp = new Net_SFTP(SSH_HOST);
if ($sftp->login($_SESSION['id'], $_SESSION['passwd'])) {

	$sftp->exec('cd $w/meshslicer/conf/; sed -i "2s/.*/'.$file.'/" config.ini');
	$resultFiles = array_map('trimFile', array_filter(explode("\n", $sftp->exec('cd $w/meshslicer/results/; ls'))));

	$check = true;
	$index = 1;
	while($check) {
		if (!in_array($file.'.out.parts.'.$_SESSION['config'][8].'.'.$index, $resultFiles)) {
			$check = false;
		}
		else {
			$index++;
		}
	}

	$outputFile = '../results/'.$file.'.out.parts.'.$_SESSION['config'][8].'.'.$index;


	$result = $sftp->exec('cd $w/meshslicer/conf/; module load metis; ./StandAlone > '.$outputFile);

	if($result == "") {
		if (preg_match('#.mesh$#', $file)) {
			$_SESSION['fileList'][] = $file.'.out.eparts.'.$_SESSION['config'][8].'.'.$index; // INDEX
			$_SESSION['fileList'][] = $file.'.out.nparts.'.$_SESSION['config'][8].'.'.$index; // INDEX
			sort($_SESSION['fileList']);
		}
		elseif (preg_match('#.graph$#', $file)) {
			$_SESSION['fileList'][] = $file.'.out.parts.'.$_SESSION['config'][8].'.'.$index; // INDEX
			sort($_SESSION['fileList']);
		}

		$reply = json_encode(array('Error' => '0', 'Partitions' => $_SESSION['config'][8], 'Index' => $index));
	}
	else {
		$reply = json_encode(array('Error' => '1', 'Message' => 'Astral problem. Please try again.'));
	}

}
else { 
	$reply = json_encode(array('Error' => '1', 'Message' => 'Astral problem. Please try again.'));
}

echo $reply;
?>