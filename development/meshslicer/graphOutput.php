<?php
session_start();

include('header.php');
include('Net/SFTP.php');

$fileToGet = $_GET['file'];
$graphPartition = 'uploads/'.$fileToGet;
if(strpos($fileToGet, '.nparts.') !== false){
    $tmp=explode(".out.nparts.", $fileToGet, 2)[0];
    $initialGraph = 'uploads/'.substr($tmp,0,strrpos($tmp, '.'));
}else if(strpos($fileToGet, '.eparts.') !== false){
    $tmp=explode(".out.eparts.", $fileToGet, 2)[0];
    $initialGraph = 'uploads/'.substr($tmp,0,strrpos($tmp, '.'));
}else{
    $tmp=explode(".parts.", $fileToGet, 2)[0];
    $initialGraph = 'uploads/'.substr($tmp,0,strrpos($tmp, '.'));
}
$statsResults = 'uploads/'.$fileToGet.'.stats';

$sftp = new Net_SFTP(SSH_HOST);
if ($sftp->login($_SESSION['id'], $_SESSION['passwd'])) {
  $sftp->get('/scratch/'.$_SESSION['id'].'/meshslicer/'.$fileToGet, $graphPartition);
  $sftp->get('/scratch/'.$_SESSION['id'].'/meshslicer/'.substr($tmp,0,strrpos($tmp, '.')), $initialGraph);
  $sftp->get('/scratch/'.$_SESSION['id'].'/meshslicer/results/'.$fileToGet, $statsResults);

  if($txt_file    = file_get_contents($initialGraph)){
    $rows        = explode("\n", $txt_file);
    $N =intval(explode(" ",array_shift($rows))[0]);
    $Nodes=[];
    if(strpos($fileToGet, '.nparts.') !== false){
        $txt_file    = file_get_contents($graphPartition);
        $colorRows= explode("\n", $txt_file);
        $color=[];
        $max=0;
        for($i=1;$i<=$N;$i++){
          $r=explode(" ",$rows[$i]);
          foreach ($r as $k => $v) {
            $val=intval($v);
            if($val>$max)$max=$val;
            foreach ($r as $k2 => $v2) {
                
                $val2=intval($v2);

                if($val>0 && $val2>0 && $k2>$k){
                  if(!isset($Nodes[$val])){
                    $Nodes[$val]=[];
                  }
                  if(!isset($Nodes[$val2])){
                    $Nodes[$val2]=[];
                  }
                  array_push($Nodes[$val],$val2);
                  array_push($Nodes[$val2],$val);
                  $Nodes[$val]=array_unique($Nodes[$val]);
                  $Nodes[$val2]=array_unique($Nodes[$val2]);
                }
            }
          }
        }
        for($i=0;$i<$max;$i++)
          $color[$i+1]=intval($colorRows[$i]);
    }else if(strpos($fileToGet, '.eparts.') !== false){
        $txt_file    = file_get_contents($graphPartition);
        $colorRows= explode("\n", $txt_file);
        $color=[];
        //Initial Set
        for($i=1;$i<=$N;$i++){
          $r=explode(" ",$rows[$i]);
          foreach ($r as $k => $v) {
            $val=intval($v);
            $color[$i]=intval($colorRows[$i-1]);
            foreach ($r as $k2 => $v2) {
                
                $val2=intval($v2);

                if($val>0 && $val2>0 && $k2>$k){
                  if(!isset($Nodes[$val])){
                    $Nodes[$val]=[];
                  }
                  if(!isset($Nodes[$val2])){
                    $Nodes[$val2]=[];
                  }
                  array_push($Nodes[$val],$val2);
                  array_push($Nodes[$val2],$val);
                  $Nodes[$val]=array_unique($Nodes[$val]);
                  $Nodes[$val2]=array_unique($Nodes[$val2]);
                }
            }
          }
        }
    }else{
        //Initial Set
        for($i=1;$i<=$N;$i++){
            $edges=[];
            $explodedLine=explode(" ",array_shift($rows));
            for($j=count($explodedLine)-1; $j>0; $j--)
                array_push($edges, intval($explodedLine[$j]));
            unset($edges[0]);
            $Nodes[$i]=$edges;
        }
        $txt_file    = file_get_contents($graphPartition);
        $rows        = explode("\n", $txt_file);
        $color=[];
        for($i=0;$i<$N;$i++) $color[$i]=intval($rows[$i]);
    }



    while(count($Nodes)>1500){
    	$key=array_rand($Nodes);
    	$key2=$Nodes[$key][array_rand($Nodes[$key])];
    	foreach ($Nodes[$key2] as $k => $v) {
    		//$Nodes[$v][array_search($key2, $Nodes[$v])]=$key;
    		foreach ($Nodes[$v] as $k2 => $v2) {
    			if($v2==$key2) $Nodes[$v][$k2]=$key;
    		}
    	}
    	$Nodes[$key]=array_unique(array_merge($Nodes[$key],$Nodes[$key2]),SORT_NUMERIC);
    	foreach ($Nodes[$key] as $k => $v){
    		if($v==$key || $v == $key2) unset($Nodes[$key][$k]);
    	}
    	unset($Nodes[$key2]);

    	//echo count($Nodes);
    	//echo "<br>";
    }



    // STATS
    $statFile = file($statsResults);
    $stats = array(0, 0, 0, 0);
    foreach($statFile as $line => $content) {
        if(preg_match('#^ dbglvl#', $content)) {
            $line2 = $line;
        }
        elseif(preg_match('#^ - Edgecut:#', $content)) {
            $line1 = $line;
        }
        elseif(preg_match('#^Timing Information#', $content)) {
            $line3 = $line;
        }
    }
    $stats[0] = explode(': ', explode(', ', $statFile[$line1])[0])[1];
    $stats[1] = explode('.', explode(': ', explode(', ', $statFile[$line1])[1])[1])[0];
    $stats[2] = explode('=', explode(', ', $statFile[$line2])[1])[1];
    $stats[3] = rtrim(explode(': ', trim($statFile[$line3+1]))[1], " sec");
    $stats[3] += rtrim(explode(': ', trim($statFile[$line3+2]))[1], " sec (METIS time)");
    $stats[3] += rtrim(explode(': ', trim($statFile[$line3+3]))[1], " sec");

    $reply=json_encode(array( 'Nodes' => $Nodes, 'Color' => $color, 'Stats' => $stats));
  }else{
    $reply = json_encode(array('Error' => '1', 'Message' => "Reading file problem"));
  }
}else{
  $reply = json_encode(array('Error' => '1', 'Message' => "Astral problem. Please try again."));
}

echo $reply;
?>
