<?php
session_start();

//si tengo vacio mi elemento de sesion me redirige a cerrarsesion y luego al login
if(empty($_SESSION['id_usuario']) or empty($_SESSION['id_nivel'])){
    header('Location: cerrarsesion.php');
    exit;
}

require_once 'header.inc.php';
?>

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
                <a class="navbar-brand" href="#">
                    <?php if($_SESSION['id_nivel'] ==1){ ?> 
                        Usuario: <?php echo $_SESSION['email'] ?>
                    <?php } else { ?>
                        <?php echo strtoupper($_SESSION['nombre_nivel']); ?>
                    <?php } ?>     
                </a>
            </div>
            <!-- /.navbar-header -->

            <?php require_once 'user.inc.php'; ?>
            <!-- /.navbar-top-links -->
            
            <?php require_once 'navbar.inc.php'; ?>           
            <!-- /.navbar-static-side -->
        </nav>
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header"><?php echo $_SESSION['nombre'].' '.$_SESSION['apellido'] ?></h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                  <!--  <pre><?php // print_r($_SESSION); ?></pre>-->
                    <div class="row">
                        <div class="col-lg-4 col-md-6">
                            <div class="panel panel-green">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-xs-3">
                                            <i class="fa fa-shopping-cart fa-5x"></i>
                                        </div>
                                        <div class="col-xs-9 text-right">
                                            <div class="huge">Pedidos</div>
                                            <!--<div>Estodo: Pendiente</div>-->
                                        </div>
                                    </div>
                                </div>
                                <a href="<?php echo ($_SESSION['id_nivel'] ==1 || $_SESSION['id_nivel'] ==5) ? 'mis_pedidos' : 'panel_pedidos_chef'?>.php">
                                    <div class="panel-footer">
                                        <span class="pull-left">Ir...</span>
                                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                        <div class="clearfix"></div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        
                        <div class="col-lg-4 col-md-6">
							<div class="panel panel-yellow">
								<div class="panel-heading">
									<div class="row">
										<div class="col-xs-3">
											<i class="fa fa-cutlery fa-5x"></i>
										</div>
										<div class="col-xs-9 text-right">
											<div class="huge">Menú</div>
											<div><!-- Estado --></div>
										</div>
									</div>
								</div>
								<a href="panel_menu.php">
									<div class="panel-footer">
										<span class="pull-left">Ir...</span>
										<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
										<div class="clearfix"></div>
									</div>
								</a>
							</div>
						</div>
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
			<div class="row">
                <div class="col-lg-12">
					<div class="row">
                            <?php if($_SESSION['id_nivel'] ==4 or $_SESSION['id_nivel'] ==5 ){ ?>
						<div class="col-lg-4 col-md-6">
							<div class="panel panel-red">
								<div class="panel-heading">
									<div class="row">
										<div class="col-xs-3">
											<i class="fa fa-credit-card fa-5x"></i>
										</div>
										<div class="col-xs-9 text-right">
											<div class="huge">Caja</div>
											<!--<div>Estado</div>-->
										</div>
									</div>
								</div>
								<a href="cobrar.php">
									<div class="panel-footer">
										<span class="pull-left">Ir...</span>
										<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
										<div class="clearfix"></div>
									</div>
								</a>
							</div>
						</div>
                            <?php } ?>
                            <?php if($_SESSION['id_nivel'] ==5){ ?>
						<div class="col-lg-4 col-md-6">
							<div class="panel panel-primary">
								<div class="panel-heading">
									<div class="row">
										<div class="col-xs-3">
											<i class="fa fa-credit-card fa-5x"></i>
										</div>
										<div class="col-xs-9 text-right">
											<div class="huge">Reportes</div>
											<!--<div>Reportes</div>-->
										</div>
									</div>
								</div>
								<a href="panel_reportes.php">
									<div class="panel-footer">
										<span class="pull-left">ver ...</span>
										<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
										<div class="clearfix"></div>
									</div>
								</a>
							</div>
						</div>
                            <?php } ?>
					</div>	
					<!-- /.panel -->
				</div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

<?php require_once 'footer.inc.php'; ?>