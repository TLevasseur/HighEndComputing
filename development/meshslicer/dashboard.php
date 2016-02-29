<?php
include('Net/SSH2.php');

function trimFile ($str) {
    return ltrim($str, "./");
}

session_start();

/*$ssh = new Net_SSH2('hpclogin-1.central.cranfield.ac.uk');
if (!$ssh->login($_SESSION["id"], $_SESSION["passwd"])) {
    header("location:signin.php?error=1");
}

$ls = array_filter(explode("\n", $ssh->exec("cd \$w; ls -d *\/")));
if(!in_array("meshslicer/", $ls)) {
    $ssh->exec("cd \$w; mkdir meshslicer");
}

$_SESSION["fileList"] = array_map('trimFile', array_filter(explode("\n", $ssh->exec("cd \$w/meshslicer; find . -maxdepth 1 -not -type d"))));*/

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
                <a class="navbar-brand" href="index.html"><i class="fa fa-pie-chart fa-fw"></i> MeshSlicer - <?php echo $_SESSION["id"]; ?></a>
            </div>
            <!-- /.navbar-header -->

            

            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li>
                            <a href="index.html"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-power-off  fa-fw"></i> Log out</a>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-envelope fa-fw"></i> Contact</a>
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

                <div class="row">
                     <div class="col-lg-3 col-md-6">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <i class="fa fa-scissors fa-5x"></i>
                                    </div>
                                    <div class="col-xs-9 text-right">
                                        <div class="huge">14</div>
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
                                        <div class="huge">20</div>
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
                                        <div class="huge">7%</div>
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
                                        <div class="huge">164ms</div>
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
                            <div class="panel-body" style="text-align:center">
                                <!--<img style="width:50%" src="img/graph.png" />-->
                            </div>
                            <div class="panel-footer">
                                Save as : PNG / JPG / TXT
                            </div>
                        </div>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->

                <div class="row">
                    <div class="col-lg-7">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                Astral Explorer
                            </div>
                            <div class="panel-body" id="dropzone">
                                <form action="upload-file.php" class="dropzone needsclick" id="demo-upload">
                                    <ol class="breadcrumb">
                                      <li><a href="#">.</a></li>
                                      <li><a href="#">scratch</a></li>
                                      <li><a href="#"><?php echo $_SESSION["id"]; ?></a></li>
                                      <li class="active">meshslicer</li>
                                    </ol>

                                    <table class="table table-hover">
                                        <tr>
                                            <th></th><th style="width:80%">Filename</th><th>Actions</th>
                                        </tr>
                                        <?php
                                        foreach ($_SESSION["fileList"] as $file) {
                                            echo '
                                            <tr class="file">
                                            <td><i class="fa fa-file-o"></i></td><td>';
                                            echo $file;
                                            echo '</td><td><i class="fa fa-download"></i> <i class="fa fa-times"></i></td>
                                            </tr>';
                                        }
                                        ?>
                                    </table>
                                </form>
                            </div>
                        </div>

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Upload a file
                            </div>
                            <div class="panel-body">
                                 <div class="form-group">
                                    <label>Choose a file</label>
                                    <input type="file">
                                </div>
                                <button type="button" class="btn btn-primary">Upload</button>
                            </div>
                        </div>
                    </div>
                    <!-- /.col-lg-8 -->


                    <div class="col-lg-5">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Settings
                            </div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <label>Coarsening method</label>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="coarse" id="optionsRadios1" value="option1" checked>Random matching
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="coarse" id="optionsRadios2" value="option2">Heavy edge matching
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Partitioning method</label>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="partition" id="optionsRadios1" value="option1" checked>Bisectioning
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="partition" id="optionsRadios2" value="option2">K-way partioning
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Minimization</label>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="minim" id="optionsRadios1" value="option1" checked>Edge-cut
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="minim" id="optionsRadios2" value="option2">Volume
                                        </label>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-primary">Update</button>
                            </div>
                        </div>
                    </div>
                    <!-- /.col-lg-2 -->
                </div>
                <!-- /.row -->

                <div class="row">
                    
                    <!-- /.col-lg-8 -->
                </div>
                <!-- /.row -->

            </div>
            <!-- /.container-fluid -->
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- jQuery -->
    <script src="bower_components/jquery/dist/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="bower_components/metisMenu/dist/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="dist/js/sb-admin-2.js"></script>

    <script src="js/dropzone.js"></script>

</body>

<script>
$('.table > tbody > .file').dblclick(function() {
    $('.table > tbody > .file').css("background-color", "");
    this.style.backgroundColor = "#f0f0f0";
});

$('.table > tbody > .folder').dblclick(function() {
    
});

$("#dropzone").dropzone({ url: "upload-file.php" });
</script>

</html>
