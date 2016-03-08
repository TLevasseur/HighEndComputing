<?php
session_start();

include('header.php');
include('Net/SFTP.php');

if(isset($_SESSION['login'])) {

    $sftp = new Net_SFTP(SSH_HOST);
    if (!$sftp->login($_SESSION["id"], $_SESSION["passwd"])) {
        header("location:signin.php?error=1");
    }

    $ls = array_filter(explode("\n", $sftp->exec("cd \$w; ls -d *\/")));
    if(!in_array("meshslicer/", $ls)) {
        $sftp->exec("cd \$w; mkdir meshslicer; cd meshslicer; mkdir conf; mkdir results; cd conf; touch config.ini; echo '0 0 0 0 0 0 0 1 2 0\nnone' > config.ini");
        $sftp->put('/scratch/'.$_SESSION["id"].'/meshslicer/conf/StandAlone', 'uploads/StandAlone' , NET_SFTP_LOCAL_FILE);
        $sftp->exec("cd \$w/meshslicer/conf/; chmod 777 StandAlone");
    }

    $_SESSION["fileList"] = array_map('trimFile', array_filter(explode("\n", $sftp->exec("cd \$w/meshslicer; find . -maxdepth 1 -not -type d"))));
    unset($_SESSION["login"]);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>MeshSlicer - Dashboard</title>

    <!-- Bootstrap Core CSS -->
    <link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="dist/css/sb-admin-2.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- Dropzone CSS -->
    <link href="css/dropzone.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>
    <script>p={};</script>
    <div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php"><i class="fa fa-pie-chart fa-fw"></i> MeshSlicer - <?php echo $_SESSION["id"]; ?></a>
            </div>
            <!-- /.navbar-header -->

            

            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li>
                            <a href="dashboard.php"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                        </li>
                        <li>
                            <a href="signout.php"><i class="fa fa-power-off  fa-fw"></i> Log out</a>
                        </li>
                        <li>
                            <a href="contact.php"><i class="fa fa-paper-plane fa-fw"></i> Contact</a>
                        </li>
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>

        <!-- Page Content -->
        <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header"><i class="fa fa-pie-chart fa-1x"></i> MeshSlicer</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
                <div id="output" style="display: none;">
                    <div class="row">
                         <div class="col-lg-3 col-md-6">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-xs-3">
                                            <i class="fa fa-scissors fa-5x"></i>
                                        </div>
                                        <div class="col-xs-9 text-right">
                                            <div class="huge" id="edgecutVal"></div>
                                            <div>Edge-cut</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="panel panel-green">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-xs-3">
                                            <i class="fa fa-arrows-alt fa-5x"></i>
                                        </div>
                                        <div class="col-xs-9 text-right">
                                            <div class="huge" id="volumeVal"></div>
                                            <div>Volume</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="panel panel-yellow">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-xs-3">
                                            <i class="fa fa-balance-scale fa-5x"></i>
                                        </div>
                                        <div class="col-xs-9 text-right">
                                            <div class="huge" id="balanceVal"></div>
                                            <div>Imbalance</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="panel panel-red">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-xs-3">
                                            <i class="fa fa-clock-o fa-5x"></i>
                                        </div>
                                        <div class="col-xs-9 text-right">
                                            <div class="huge" id="timeVal"></div>
                                            <div>Execution time</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.row -->

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    Partitioned Graph
                                </div>
                                <div class="panel-body" id="container">
                                    <!--<img style="width:50%" src="img/graph.png" />-->
                                    <style>
                                        #container {
                                          height: 750px;
                                        }
                                        #graph-container {
                                            /*position: absolute;*/
                                            width: 100%;
                                            height: 100%;
                                        }
                                    </style>
                                    <div id="graph-container"></div>
                            
                                </div>
                            </div>
                        </div>
                        <!-- /.col-lg-12 -->
                    </div>
                    <!-- /.row -->
                </div>
                <div class="row">
                    <div class="col-lg-7">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                Astral Explorer
                            </div>
                            <div class="panel-body">
                                
                                <ol class="breadcrumb">
                                  <li><a href="#">.</a></li>
                                  <li><a href="#">scratch</a></li>
                                  <li><a href="#"><?php echo $_SESSION["id"]; ?></a></li>
                                  <li class="active">meshslicer</li>
                                </ol>

                                <form id="dropzone" action="upload-file.php" class="dropzone needsclick" id="demo-upload">
                                    <table id="fileListTable" class="table table-hover">
                                        <tr>
                                            <th></th><th style="width:75%">Filename</th><th>Actions</th>
                                        </tr>
                                        <?php
                                        foreach ($_SESSION["fileList"] as $file) {
                                            echo '
                                            <tr class="file">
                                            <td><i class="fa fa-file-o"></i></td><td>';
                                            echo $file;
                                            echo '</td><td>';
                                            if(preg_match('#parts.[0-9]*.[0-9]*#', $file)) {
                                                echo '<i class="fa fa-eye displayPart"></i> ';
                                            }
                                            else {
                                                echo '<input type="radio" name="file" value="0"> ';
                                            }
                                            echo '<i class="fa fa-download getFile"></i> <i class="fa fa-times deleteFile"></i></td>
                                            </tr>';
                                        }
                                        ?>
                                    </table>
                                </form>
                            </div>
                        </div>
                        <div class="panel panel-red launchPartition">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-xs-12 text-center">
                                        <div class="huge">Launch partitioning <i class="fa fa-cut"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel loadPartition" style="display:none;">
                            <div class="panel-body">
                                <img src="img/ajax-loader.gif" />
                            </div>
                        </div>
                    </div>

                    
                    <!-- /.col-lg-7 -->


                    <div class="col-lg-5">
                         <div class="btn-group col-xs-12" role="group">
                            <button type="button" class="btn btn-primary col-xs-6" id="graph" onclick="graphClick()">Partition a graph</button>
                            <button type="button" class="btn btn-primary col-xs-6" id="mesh" onclick="meshClick()">Partition a mesh</button>
                          </div>
                          <div class="btn-group col-xs-12" id="method" role="group" style = "display:none">
                            <button type="button" class="btn btn-secondary col-xs-6" id="bisection" onclick="bisectionClick()">Recursive Bisection</button>
                            <button type="button" class="btn btn-secondary col-xs-6" id="kway" onclick="kwayClick()">K-way Partition</button>
                          </div>
                          <br><br><br><br>
                            <div class="col-xs-12" id="furtherOption" style = "display:none">
                                <div class="form-group mesh-only">
                                    <label>Mesh Type</label>
                                        <div class="radio">
                                        <label>
                                            <input type="radio" name="gtype" value="0" checked>Dual Graph
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="gtype" value="1">Nodal Graph
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group kway-only">
                                    <label>Partition Objective</label>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="objtype" value="0" checked>Edgecut minimization
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="objtype" value="1">Communication minimization
                                        </label>
                                    </div>
                                </div> 
                                <div class="form-group">
                                    <label>Coarsening Method</label>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="ctype" value="0" checked>Random matching
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="ctype" value="1">Heavy edge matching
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group bisection-only">
                                    <label>Initial Partition</label>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="iptype" value="0" checked>Greedy Bisectioning
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="iptype" value="1">Random bisection + Refinement
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group kway-only">
                                    <label>Reduction system</label>
                                    <div class="radio" id>
                                        <label>
                                            <input type="radio" name="contig" value="0" checked>Default
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="contig" value="1">Contiguous Reduction
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="ufactor">Maximum imbalance factor x:</label>
                                    <input type="number" min="1" max="1000" value="1" class="form-control" id="ufactor"></input>
                                </div>
                                <div class="form-group">
                                    <label for="nparts">Number of partitions</label>
                                    <input type="number" min="1" value="2" class="form-control" id="nparts"></input>
                                </div>
                                <button type="button" class="btn btn-primary" id="submitIniFile">Update parameters</button>
                            </div>
                        </div>
                        <script src="bower_components/jquery/dist/jquery.min.js"></script>

                        <script>
                        $('#submitIniFile').click(function(){
                            /*value 3,4,5,*/
                            p.partitionParam=$('input[name=objtype]:checked').val();
                            p.coarseningSheme=$('input[name=ctype]:checked').val();
                            p.initialPatition=$('input[name=iptype]:checked').val();
                            p.contiguousPartition=$('input[name=contig]:checked').val();
                            p.imbalance=$('#ufactor').val();
                            p.nbParts=$('#nparts').val();
                            p.meshBase=$('input[name=gtype]:checked').val();
                            msg=p.fileType+" "+
                                p.partitionMethod+" "+
                                p.partitionParam+" "+
                                p.coarseningSheme+" "+
                                p.initialPatition+" "+
                                "0 "+
                                p.contiguousPartition+" "+
                                p.imbalance+" "+
                                p.nbParts+" "+
                                p.meshBase;
                             $.ajax({
                                url: 'configfile.php',
                                type: 'post',
                                data: 'str=' + msg,
                                success: function(response) {
                                    var tabElement = eval("(" + response + ")");
                                    if (tabElement.Error == '1') {
                                        alert(tabElement.Message);
                                    }
                                    else {
                                        
                                    }
                                }
                            }); 
                        });
                        function graphClick(){
                            $('#graph').addClass('active');
                            $('#mesh').removeClass('active');
                            $('#method').show();
                            $( ".mesh-only" ).hide();
                            p.fileType=0;
                        }
                        function meshClick(){
                            $('#mesh').addClass('active');
                            $('#graph').removeClass('active');
                            $('#method').show();
                            $( ".mesh-only" ).show();
                            p.fileType=1;
                        }

                        function bisectionClick(){
                            $('#bisection').addClass('active');
                            $('#kway').removeClass('active');
                            $('#furtherOption').show();
                            $( ".kway-only" ).hide();
                            $( ".bisection-only" ).show();
                            p.partitionMethod=0;
                        }

                        function kwayClick(){
                            $('#kway').addClass('active');
                            $('#bisection').removeClass('active');
                            $('#furtherOption').show();
                            $( ".kway-only" ).show();
                            $( ".bisection-only" ).hide();
                            p.partitionMethod=1;
                        }

                        </script>
                    </div>
                    <!-- /.col-lg-5 -->


                </div>
                <!-- /.row -->

            </div>
            <!-- /.container-fluid -->
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- jQuery -->

    <!-- Bootstrap Core JavaScript -->
    <script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="bower_components/metisMenu/dist/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="dist/js/sb-admin-2.js"></script>

    <script src="js/dropzone.js"></script>

</body>

<script>
function addFileRow(fileName) {
    var rows = $('<tr>');
    rows.append('<td><i class="fa fa-file-o"></i></td>');
    rows.append('<td>'+fileName+'</td>');

    if(fileName.match('/parts.[0-9]*.[0-9]*/')) {
        rows.append('<td><i class="fa fa-eye displayPart"></i> <i class="fa fa-download getFile"></i> <i class="fa fa-times deleteFile"></i></td>');
    }
    else {
        rows.append('<td><input type="radio" name="file" value="0">  <i class="fa fa-download getFile"></i> <i class="fa fa-times deleteFile"></i></td>');
    }
    
    rows.hide();
    $('#fileListTable tr').eq(index).after(rows);
    rows.fadeIn("slow");

    setEvents();
}
Dropzone.options.dropzone = {
    previewTemplate: '<div id="ajax-loader"><img src="img/ajax-loader.gif" /></div>',
    init: function() {
        this.on("success", function(file) {
            this.element.classList.remove("dz-started");
            $("#ajax-loader").remove();

            addFileRow(file.name);
            
        });
    },
    addedfile: function(file) {
        var _results;
        if (this.element === this.previewsContainer) {
            this.element.classList.add("dz-started");
        }
        if (this.previewsContainer) {
            file.previewElement = Dropzone.createElement(this.options.previewTemplate.trim());
            file.previewTemplate = file.previewElement;
            this.previewsContainer.appendChild(file.previewElement);
            return _results;
        }
    }   
};


function setEvents() {
    $('.launchPartition').css('cursor', 'pointer');

    $('.getFile').css('cursor', 'pointer');

    $('.deleteFile').css('cursor', 'pointer');
    $('.displayPart').css('cursor', 'pointer');

    $('.getFile').click(function() {
        var element = $(this);
        element.removeClass("fa-download").addClass("downloading");
        element.next().hide();
        element.prev().hide();
        $.ajax({
            url: 'download-file.php',
            type: 'GET',
            data: 'file=' + element.parent().prev().text(),
            success: function(response) {
                var tabElement = eval("(" + response + ")");
                if (tabElement.Error == '1') {
                    alert(tabElement.Message);
                }
                else {
                    element.removeClass("downloading").addClass("fa-download");
                    element.next().show();
                    element.prev().show();
                    window.open('uploads/'+element.parent().prev().text(), '_blank', null);
                }
            }
        }); 
    });

    $('.deleteFile').click(function() {
        var element = $(this);
        element.removeClass("fa-times").addClass("downloading");
        element.prev().hide();
        element.prev().prev().hide();
        $.ajax({
            url: 'delete-file.php',
            type: 'GET',
            data: 'file=' + element.parent().prev().text(),
            success: function(response) {
                var tabElement = eval("(" + response + ")");
                if (tabElement.Error == '1') {
                    alert(tabElement.Message);
                }
                else {
                    var row = element.parent().parent().fadeOut('slow');
                }
            }
        });
    });
    $('.displayPart').click(function(){
        var element = $(this);
        element.removeClass("fa-eye").addClass("downloading");
        element.prev().hide();
        $.ajax({
            url: 'graphOutput.php',
            type: 'GET',
            data: 'file=' + element.parent().prev().text(),
            success: function(response) {
                var tabElement = eval("(" + response + ")");
                if (tabElement.Error == '1') {
                    alert(tabElement.Message);
                }else {
                    var stats = tabElement.Stats;
                    $('#edgecutVal').text();
                    $('#volumeVal').text();
                    $('#balanceVal').text();
                    $('#timeVal').text();

                    Nodes=tabElement.Nodes;
                    Color=tabElement.Color;
                    element.removeClass("downloading").addClass("fa-eye");
                    element.next().show();
                    var i,
                        s,
                        o,
                        N = Nodes.length,
                        E = 45000,
                        C = parseInt(element.parent().prev().text().split(".parts.",1)[0].split('.').pop()),
                        d = 0.5,
                        cs = [],
                        g = {
                          nodes: [],
                          edges: []
                        };

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
                        if(C>2)cs[2].color="#5CB85C";
                        if(C>3)cs[3].color="#D9534F";

                    Object.keys(Nodes).forEach(function (key) {
                        g.nodes.push({id: key,
                                    //label: key,
                                    x:Math.random()*100,
                                    y:Math.random()*100,
                                    size:2,
                                    color:cs[Color[key-1]].color});
                    });
                    i=0;
                    Object.keys(Nodes).forEach(function (key) {
                        Object.keys(Nodes[key]).forEach(function (key2) {
                            g.edges.push({id: ++i ,source:key,target:Nodes[key][key2]});
                        });
                    });
                    $("#output").fadeIn(3000);

                    s = new sigma({
                      graph: g,
                      container: 'graph-container',
                      settings: {
                        drawEdges: true,
                        enableHovering : false,
                        scalingMode : "outside",
                        minArrowSize: 1,
                        zoomMin: 0.2
                      }
                    });


                    s.startForceAtlas2({worker: true, barnesHutOptimize: false});
                }
            }
        });
    });
    $('.launchPartition').click(function() {
        var fileName = $('input[name=file]:checked', '#dropzone').parent().prev().text();
        if (fileName == '') {
            alert("No graph/mesh file selected.");
        }
        else {
            var element = $(this);
            element.hide();
            element.next().show();
            $.ajax({
                url: 'partition.php',
                type: 'GET',
                data: 'file=' + fileName,
                success: function(response) {
                    var tabElement = eval("(" + response + ")");
                    if (tabElement.Error == '1') {
                        alert(tabElement.Message);
                    }
                    else {
                        element.next().hide();
                        element.show();
                        var reg1 = /.mesh$/;
                        var reg2 = /.graph$/;
                        if(reg1.test(fileName)) {
                            addFileRow(fileName + '.eout.parts.' + tabElement.Partitions + '.' + tabElement.Index);
                            addFileRow(fileName + '.nout.parts.' + tabElement.Partitions + '.' + tabElement.Index);
                        }
                        else if (reg2.test(fileName)) {
                            addFileRow(fileName + '.out.parts.' + tabElement.Partitions + '.' + tabElement.Index);
                        }
                    }
                }
            });
        }
    });
}

$(function() {
   setEvents(); 
})

</script>
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
<script src="plugins/sigma.layout.forceAtlas2/worker.js"></script>
<script src="plugins/sigma.layout.forceAtlas2/supervisor.js"></script>
</html>
