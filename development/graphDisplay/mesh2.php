<head>
 <meta charset="UTF-8">
</head>





<?php


$txt_file    = file_get_contents('metis.mesh');
$rows        = explode("\n", $txt_file);
$N =intval(explode(" ",array_shift($rows))[0]);
$Nodes=[];

$txt_file    = file_get_contents('metis.mesh.out.nparts.4.1');
$colorRows= explode("\n", $txt_file);
$color=[];

$max=0;

//Initial Set
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

?>

<script src="src/sigma.core.js"></script>
<script src="src/conrad.js"></script>
<script src="src/utils/sigma.utils.js"></script>
<script src="src/utils/sigma.polyfills.js"></script>
<script src="src/sigma.settings.js"></script>
<script src="src/classes/sigma.classes.dispatcher.js"></script>
<script src="src/classes/sigma.classes.configurable.js"></script>
<script src="src/classes/sigma.classes.graph.js"></script>
<script src="src/classes/sigma.classes.camera.js"></script>
<script src="src/classes/sigma.classes.quad.js"></script>
<script src="src/classes/sigma.classes.edgequad.js"></script>
<script src="src/captors/sigma.captors.mouse.js"></script>
<script src="src/captors/sigma.captors.touch.js"></script>
<script src="src/renderers/sigma.renderers.canvas.js"></script>
<script src="src/renderers/sigma.renderers.webgl.js"></script>
<script src="src/renderers/sigma.renderers.svg.js"></script>
<script src="src/renderers/sigma.renderers.def.js"></script>
<script src="src/renderers/webgl/sigma.webgl.nodes.def.js"></script>
<script src="src/renderers/webgl/sigma.webgl.nodes.fast.js"></script>
<script src="src/renderers/webgl/sigma.webgl.edges.def.js"></script>
<script src="src/renderers/webgl/sigma.webgl.edges.fast.js"></script>
<script src="src/renderers/webgl/sigma.webgl.edges.arrow.js"></script>
<script src="src/renderers/canvas/sigma.canvas.labels.def.js"></script>
<script src="src/renderers/canvas/sigma.canvas.hovers.def.js"></script>
<script src="src/renderers/canvas/sigma.canvas.nodes.def.js"></script>
<script src="src/renderers/canvas/sigma.canvas.edges.def.js"></script>
<script src="src/renderers/canvas/sigma.canvas.edges.curve.js"></script>
<script src="src/renderers/canvas/sigma.canvas.edges.arrow.js"></script>
<script src="src/renderers/canvas/sigma.canvas.edges.curvedArrow.js"></script>
<script src="src/renderers/canvas/sigma.canvas.edgehovers.def.js"></script>
<script src="src/renderers/canvas/sigma.canvas.edgehovers.curve.js"></script>
<script src="src/renderers/canvas/sigma.canvas.edgehovers.arrow.js"></script>
<script src="src/renderers/canvas/sigma.canvas.edgehovers.curvedArrow.js"></script>
<script src="src/renderers/canvas/sigma.canvas.extremities.def.js"></script>
<script src="src/renderers/svg/sigma.svg.utils.js"></script>
<script src="src/renderers/svg/sigma.svg.nodes.def.js"></script>
<script src="src/renderers/svg/sigma.svg.edges.def.js"></script>
<script src="src/renderers/svg/sigma.svg.edges.curve.js"></script>
<script src="src/renderers/svg/sigma.svg.labels.def.js"></script>
<script src="src/renderers/svg/sigma.svg.hovers.def.js"></script>
<script src="src/middlewares/sigma.middlewares.rescale.js"></script>
<script src="src/middlewares/sigma.middlewares.copy.js"></script>
<script src="src/misc/sigma.misc.animation.js"></script>
<script src="src/misc/sigma.misc.bindEvents.js"></script>
<script src="src/misc/sigma.misc.bindDOMEvents.js"></script>
<script src="src/misc/sigma.misc.drawHovers.js"></script>
<!-- END SIGMA IMPORTS -->
<div id="container">
  <style>
    #graph-container {
      top: 0;
      bottom: 0;
      left: 0;
      right: 0;
      position: absolute;
    }
  </style>
  <div id="graph-container"></div>
</div>
<script src="plugins/sigma.layout.forceAtlas2/worker.js"></script>
<script src="plugins/sigma.layout.forceAtlas2/supervisor.js"></script>
<script>
/**
 * Just a simple example to show how to use the sigma.layout.forceAtlas2
 * plugin:
 *
 * A random graph is generated, such that its nodes are separated in some
 * distinct clusters. Each cluster has its own color, and the density of
 * links is stronger inside the clusters. So, we expect the algorithm to
 * regroup the nodes of each cluster.
 */
var i,
    s,
    o,
    N = 15000,
    E = 45000,
    C = 5,
    d = 0.5,
    cs = [],
    g = {
      nodes: [],
      edges: []
    };
//#337AB7 bleu
//#F0AD4E jaune
//#5CB85C vert
//#D9534F rouge
//4 first color preselected
// Generate the graph:
for (i = 0; i < C; i++)
  cs.push({
    id: i,
    nodes: [],
    color: '#' + (
      Math.floor(Math.random() * 16777215).toString(16) + '000000'
    ).substr(0, 6)
  });
  cs[0].color="#337AB7";
  cs[1].color="#F0AD4E";
  cs[2].color="#5CB85C";
  cs[3].color="#D9534F";
<?php
foreach ($Nodes as $id => $n)
	echo("g.nodes.push({id:".$id.",
	 		label:".$id.",
	 		x:Math.random()*100,
	 		y:Math.random()*100,
	 		size:2,
	 		color:cs[".$color[$id-1]."].color});
			");
?><?php
foreach ($Nodes as $id => $n)
	foreach ($Nodes[$id] as $k => $v) {
	echo("g.edges.push({id:".$id."000000".$k.",source:".$id.",target:".$v."});");
}
?>


s = new sigma({
  graph: g,
  container: 'graph-container',
  settings: {
    drawEdges: true,
    enableHovering : false,
    scalingMode : "outside",
    minArrowSize: 1,
    zoomMin: 0.0001
  }
});

// Start the ForceAtlas2 algorithm:
s.startForceAtlas2({worker: true, barnesHutOptimize: false});
</script>