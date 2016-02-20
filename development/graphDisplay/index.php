<?php
$txt_file    = file_get_contents('small.graph');
$rows        = explode("\n", $txt_file);
$N =explode(" ",array_shift($rows))[0];
?> 
<head>
 <meta charset="UTF-8">
</head>

<!-- START SIGMA IMPORTS -->
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
    N = 1500,
    E = 5000,
    C = 1,
    d = 0.5,
    cs = [],
    g = {
      nodes: [],
      edges: []
    };

// Generate the graph:
for (i = 0; i < C; i++)
  cs.push({
    id: i,
    nodes: [],
    color: '#' + (
      Math.floor(Math.random() * 16777215).toString(16) + '000000'
    ).substr(0, 6)
  });

for (i = 1; i <<?php echo $N+1; ?>; i++) {
  o = cs[(Math.random() * C) | 0];
  g.nodes.push({
    id: i,
    x: 100 * Math.cos(2 * i * Math.PI / N),
    y: 100 * Math.sin(2 * i * Math.PI / N),
    size: 0.0001,
    color: o.color
  });
  o.nodes.push('n' + i);
}


<?php
$i=1;
foreach($rows as $row => $data)
{
    $edges=explode(" ",array_shift($rows));
    array_shift($edges);
    array_pop($edges);
    foreach ($edges as $edge) {
      echo 'g.edges.push({id:';
      echo $i;
      echo ',source: ';
      echo $row+1;
      echo ',target: ';
      echo $edge;
      echo '});';
      $i++;
    }
}
?> 


s = new sigma({
  graph: g,
  container: 'graph-container',
  settings: {
    drawEdges: true
  }
});

// Start the ForceAtlas2 algorithm:
s.startForceAtlas2({worker: true, barnesHutOptimize: false});
</script>