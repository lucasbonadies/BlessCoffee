<?php 
session_start();

//Añado una verificacion extra para que Alguien que no tenga acceso pueda entrar a un Scrip no permitido 
if($_SESSION['id_nivel'] !=1 && $_SESSION['id_nivel'] !=5 && $_SESSION['id_nivel'] !=2 ){
    header('Location: index.php');
    exit;
}

if(empty($_SESSION['id_usuario']) || empty($_SESSION['id_nivel'])){
    header('Location: cerrarsesion.php');
    exit;
 }

require_once 'funciones/conexion.php';
$MiConexion = ConexionBD();

require_once 'funciones/select_funciones.php';
$ListadoArticulos = Listar_Articulos($MiConexion);
$CantidadArticulos = count($ListadoArticulos);

require_once 'funciones/validacion_de_datos.php';
require_once 'funciones/insert_funciones.php';

$_SESSION['Mensaje'] = '';
$_SESSION['Estilo'] = 'warning';

if(Validar_Pedido($CantidadArticulos) === false) { //Valido que no haya ningun articulo en el pedido.
	$_SESSION['postData'] = $_POST;  // Guardar valores de $_POST en sesión. para que cuando vuelva a cargar la página no se pierdan los datos.
	header('Location: panel_menu.php');
	exit;
}

$productos_seleccionados = array();

foreach ($_POST as $key => $value) {
    if (strpos($key, 'Cantidad') === 0 && intval($value) > 0) {
        $index = substr($key, 8); // Agarramos el número //Extraé la subcadena que empieza desde el carácter número 8 en adelante.

        if (isset($_POST["Articulo$index"])) {
            $id = intval($_POST["Articulo$index"]);
            $cantidad = intval($value);
            $tipo = isset($_POST["articuloNombre$index"]) ? 'articulo' : 'promocion';

            $productos_seleccionados[] = array(
                'tipo' => $tipo,
                'id' => $id,
                'cantidad' => $cantidad
            );
        }
    }
}

echo "<h3>DEBUG: Datos que llegan a productos_seleccionados</h3>";
echo "<pre>";
print_r($productos_seleccionados);
echo "</pre>";
exit; // Detenemos la ejecución para verificar los datos

if (!empty($_POST['BotonGenerarPedido'])) { 
    if (!empty($productos_seleccionados)) {


        if (InsertarPedido($MiConexion, $_SESSION['id_persona']) && InsertarDetalle($MiConexion, $productos_seleccionados)) {
            $_SESSION['Mensaje'] = 'Su pedido se ha registrado correctamente.';
            $_POST = array(); 
            $_SESSION['Estilo']  = 'success';
        } else {
            $_SESSION['Mensaje'] = 'Error al intentar generar el pedido.';
            $_SESSION['Estilo']  = 'danger';
        }
    } else {
        $_SESSION['Mensaje'] = 'No se seleccionaron productos. Por favor, seleccione al menos uno antes de generar el pedido. <a href="panel_menu.php">MENÚ</a>';
        $_SESSION['Estilo'] = 'warning';
    }
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
                <a class="navbar-brand" href="index.php">Volver al inicio</a>
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
                    <h1 class="page-header">Pedidos</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
			<form role="form" method='post'>
				<div class="row">
					<div class="alert alert-info col-lg-12">
						Aquí puede ver el detalle del pedido antes de finalizarlo.
						<!-- /.panel -->
					</div>
					<!-- /.col-lg-12 -->
				</div>
				<!-- /.row -->
				<br />
				<?php if (!empty($_SESSION['Mensaje'])) { ?>
					<div class="alert alert-<?php echo $_SESSION['Estilo'] ; ?> alert-dismissable">
				<?php echo $_SESSION['Mensaje']; ?>
					</div>
				<?php } ?>
				<!-- /.row -->
				<div id="SuPedido">
					<h3>Su Pedido</h3>
				</div>
				<br />
				<?php if ($_SESSION['id_nivel']==1 or $_SESSION['id_nivel']==5) { 

					if(!empty($_POST)){
					// se muestran solo los alimentos seleccionados por el cliente 
				?>	
				<button type="submit" class="btn btn-success" value="GenerarPedido" name="BotonGenerarPedido">
					Pedir
				</button>
				<?php }else{ ?>
					<button type="button" class="btn btn-warning" onclick="window.location.href='panel_menu.php';">
						Volver
					</button>				
				<?php } } ?>
				<br />
				<br />

				<?php for ($i=0; $i< $CantidadArticulos; $i++){ ?>
					<?php	if (isset($_POST["Articulo$i"]) && $ListadoArticulos[$i]['ID_ARTICULO']==$_POST["Articulo$i"]){
						// se muestran solo los alimentos seleccionados por el cliente 
				?>	
					<div class="row">
						<div class="col-lg-10 col-md-10">
							<div class="panel panel-primary">
								<div class="panel-heading">
									<div class="row">
										<div class="col-xs-3">
											<img alt="Image placeholder" class="img-responsive" 
											src="dist/img/imagen_menu/<?php echo $ListadoArticulos[$i]['IMAGEN']; ?>" />
										</div>
										<div class="col-xs-9 text-right">
											<div class="huge"> <?php echo $ListadoArticulos[$i]['NOMBRE']; ?> </div>
											<div><?php echo '$'.$ListadoArticulos[$i]['PRECIO_UNITARIO']; ?></div>
											<!--<div>Stock disponible</div> POR EL MOMENTO NO PRECISAMOS EL STOCK DISPONIBLE-->
										</div>
									</div>
								</div>
								
								<div class="panel-footer">
									<!--<a href="#"><i class="fa fa-plus-circle fa-fw"></i><span class="pull-left">Ver detalle</span></a>-->
										<div class="form-group pull-right">
											<label for="tentacles">Cantidad: <?php echo $_POST["Cantidad$i"]; ?></label>
											<!--<input type="number"  name="Cantidad<?php //echo $i ?>" id="cantidad" min="0" max="20" 
											 value= "<?php // echo $_POST["Cantidad$i"]; ?>" > -->
											<input type="hidden" name="Articulo<?php echo $i ?>" id="articulo_id" value="<?php echo $ListadoArticulos[$i]['ID_ARTICULO'];?>">
											<input type="hidden" name="Cantidad<?php echo $i ?>" id="cantidad"
											 value= "<?php echo $_POST["Cantidad$i"]; ?>" >
											<!--<label class="inline">
												<input type="checkbox" name="Articulo<?php //echo $i ?>" id="agregar" value="<?php //echo $ListadoArticulos[$i]['ID_ARTICULO'];?>" > Pedir
											</label>-->
										</div>
									<div class="clearfix"></div>
								</div>						
							</div>
						</div>					
					</div>
				<?php }	
				}	?>
			</form>
        </div>
        <!-- /#page-wrapper -->
    </div>
    <!-- /#wrapper -->
	 
	<button id="back-to-top"><i class="fa fa-long-arrow-up"></i></button>

	<script src="js/mostrar_ocultar.js"> </script>

	<?php require_once 'footer.inc.php'; ?>