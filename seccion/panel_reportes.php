<?php 
session_start();

if(empty($_SESSION['id_usuario']) || empty($_SESSION['id_nivel'])){
    header('Location: cerrarsesion.php');
    exit;
}
//Añado una verificacion extra para que Alguien que no tenga acceso pueda entrar a un Scrip no permitido 
if($_SESSION['id_nivel'] !=5  ){
    header('Location: index.php');
    exit;
}

require_once 'funciones/conexion.php';
$MiConexion=ConexionBD(); 

/*$_SESSION['Mensaje']='';*/
$_SESSION['Estilo']='warning';

require_once 'funciones/select_funciones.php';
$ListadoEstados = Listar_Estados_Pedidos($MiConexion);

// Recuperar valores de $_POST guardados en la sesión si existen
if (!empty($_SESSION['postData'])) {
    $_POST = $_SESSION['postData'];
    unset($_SESSION['postData']); // Limpiar después de usarlos
}

// Obtener la fecha actual en el formato YYYY-MM-DD
$currentDate = date('Y-m-d');
require_once 'header.inc.php'; ?>
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
                <a class="navbar-brand" href="index.php">
                    Usuario:  <?php echo $_SESSION['nombre'].' '.$_SESSION['apellido']; ?>
                </a>
            </div>
            <!-- /.navbar-header -->

            <?php require_once 'user.inc.php'; ?>
            <!-- /.navbar-top-links -->
            
            <?php require_once 'navbar.inc.php'; ?>           
            <!-- /.navbar-static-side -->
        </nav>

        <div id="page-wrapper">
            <div class="text-center">
                <img class="img-fluid" src='dist/img/LoginChef.png' />
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Reportes</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-10">
                    <div class="panel panel-default">
						<?php if (!empty($_SESSION['Mensaje'])) { ?>
						<div class="alert alert-<?php echo $_SESSION['Estilo']; ?> alert-dismissable">
						<?php echo $_SESSION['Mensaje']; ?>
						</div>
						<?php } ?>
						<div class="panel-heading ">
                            Selecciona un periodo de tiempo para ver el reporte
                       	</div>
						<!-- /.panel-heading -->
							<div class="panel-body ">
								<div class="row">
									<form method='post' action="reporte_pedidos.php"  enctype="multipart/form-data" >
										<div class="col-lg-5">
											<div class="form-group">
												<label>Desde:</label>
												<input class="form-control" type="date" name="Desde" id="desde" min="1924-01-01" required
												value="<?php echo !empty($_POST['Desde'])?  $_POST['Desde'] : ''; ?>"> 
											</div>
                                           
											<div class="form-group">
												<label>Estado:</label>
												<select class="form-control" name="Estado" id="estado">
													<option value=''>Todos</option>
													<?php 
													$selected='';
													foreach ($ListadoEstados as $estado) {
														$selected = !empty($_POST['Estado'])  && $_POST['Estado'] ==  $estado['ID_ESTADO'] ? 'selected' : '' ;	
													?>
														<option value="<?php echo $estado['ID_ESTADO']; ?>" <?php echo $selected; ?>  >
															<?php echo $estado['NOMBRE_ESTADO']; ?>
														</option>
													<?php } ?>
												</select>
											</div>
											<button type="submit" class="btn btn-default" value="Buscar" name="BotonBuscar" > BUSCAR </button>     
										</div> 
										<!-- /.col-lg-5 -->
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Hasta:</label>
                                                <input class="form-control" type="date" name="Hasta" id="hasta" min="1924-01-01" max="<?php echo $currentDate ?>" required
                                                value="<?php echo !empty($_POST['Hasta'])?  $_POST['Hasta'] : ''; ?>"> 
                                            </div>
                                        </div>
									</form>
								</div>
								<!-- /.row -->
							</div>
							<!-- /.panel-body -->
						<div class="alert alert-info center-block right">
							<i class="fa fa-arrow-left"></i>
							<a href="index.php" class="alert-link">Volver</a>.
						</div>
					</div>	 
                    <!-- /.panel panel -->
                </div>
                <!-- /.col-lg-12 -->
			</div>
            <!-- /.row -->
        </div>
        <!-- /#page-wrapper -->
    </div>
    <!-- /#wrapper -->
<?php $_SESSION['Mensaje']=''; ?>
<?php require_once 'footer.inc.php'; ?>