<?php 
session_start();

//si tengo vacio mi elemento de sesion me redirige a cerrarsesion y luego al login
if(empty($_SESSION['id_usuario']) || empty($_SESSION['id_nivel'])){
    header('Location: cerrarsesion.php');
    exit;
}

$ID_PEDIDO = filter_var($_GET['ID_PEDIDO'], FILTER_VALIDATE_INT); //limpio valores ingresados por GET Y VERIFICO QUE SOLO SEAN ENTEROS
$ID_PERSONA= $_SESSION['id_nivel']==5 ? filter_var($_GET['ID_PERSONA'], FILTER_VALIDATE_INT) : $_SESSION['id_persona'];

//voy a necesitar la conexion: incluyo la funcion de Conexion.
require_once 'funciones/conexion.php';
require_once 'funciones/select_funciones.php';
$MiConexion = ConexionBD();

//voy a ir listando lo necesario para trabajar en este script: 
$ListadoDetallePedido = Listar_detalle_Pedidos($MiConexion, $ID_PERSONA, $ID_PEDIDO);
$CantidadDetallePedido = count($ListadoDetallePedido);
$total=0;

if (empty($ListadoDetallePedido)){
    $_SESSION['Mensaje']="No tienes pedidos pendientes.";
    $_SESSION['Estilo']='info';
}

if (!empty($_POST['BotonModificarPedido'])) {

	require_once 'funciones/validacion_de_datos.php';

    if (Validar_Update_Pedido()=== true) {  //salio bien la validacion modifico en la base de datos

        // Crear un array asociativo de artículos con sus respectivas cantidades
        $articulos = [];
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'Cantidad') === 0) {
                $idArticulo = str_replace('Cantidad', '', $key);
                $articulos[(int)$idArticulo] = (int)$value; // Guardar cantidad y el ID del artículo
            }
        }

        require_once 'funciones/update_funciones.php';

        if (Update_Pedido($MiConexion, $ID_PEDIDO, $articulos) != false){
            $_SESSION['Mensaje'].= "Pedido actualizado.</br>";
            $_SESSION['Estilo'] = "success";
            header("Location: modificar_pedido.php?ID_PEDIDO=$ID_PEDIDO&ID_PERSONA=$ID_PERSONA");
            exit;
        }else {
            $_SESSION['Mensaje'].= "Error al actualizar el pedido.";
            $_SESSION['Estilo'] = "warning";
        }
    }else{
        $_SESSION['Mensaje'].= "Error al intentar modificar el pedido.</br>";
        $_SESSION['Estilo'] = "warning";
    }
}   

?>

<?php require_once 'header.inc.php'; ?>

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
                    <h1 class="page-header"> Modificar pedido </h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                               PEDIDO N°<?php echo $ID_PEDIDO ?>
                            </div>
                            <!-- /.panel-heading -->
                            <div class="panel-body">
                                <?php if (!empty($_SESSION['Mensaje'])) { ?>
                                    <div class="alert alert-<?php echo $_SESSION['Estilo']; ?> alert-dismissable">
                                    <?php echo $_SESSION['Mensaje'] ?>
                                    </div>
                                <?php } ?>


                                <?php if (!empty($ListadoDetallePedido)) { ?>
                            <form method="POST" action="">
                                <div class="table-responsive table-bordered">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Código</th>
                                                <th>Descripción</th>
                                                <th>Cantidad</th>
                                                <th>Precio</th>
                                                <th>Subtotal</th>
                                                <th>Imagen</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($ListadoDetallePedido as $detalle) { ?>
                                                <tr class="info">
                                                    <td><?php echo $detalle['ID_ARTICULO']; ?></td>
                                                    <td><?php echo $detalle['NOMBRE_ARTICULO']; ?></td>
                                                    <td>
                                                        <input type="number" name="Cantidad<?php echo $detalle['ID_ARTICULO']; ?>" min="1" max="20"
                                                               value="<?php echo !empty($_POST["Cantidad{$detalle['ID_ARTICULO']}"]) ? $_POST["Cantidad{$detalle['ID_ARTICULO']}"] : $detalle['CANTIDAD']; ?>">
                                                    </td>
                                                    <td><?php echo '$'.$detalle['PRECIO_UNITARIO']; ?></td>
                                                    <td><?php echo '$'.$detalle['SUBTOTAL']; $total += $detalle['SUBTOTAL']; ?>  </td>
                                                    <td class="col-xs-1">
                                                        <img alt="Imagen del artículo" class="img-responsive" 
                                                             src="dist/img/imagen_menu/<?php echo $detalle['IMAGEN']; ?>">
                                                    </td>
                                                    <td>
                                                        <a href="quitar_articulo.php?ID_ARTICULO=<?php echo $detalle['ID_ARTICULO']; ?>&ID_PEDIDO=<?php echo $ID_PEDIDO; ?>&ID_PERSONA=<?php echo $ID_PERSONA; ?>"
                                                           class="btn btn-danger btn-circle" title="QUITAR"
                                                           onclick="return confirm('¿Confirma que desea quitar este artículo del pedido?')">
                                                           <i class="fa fa-trash-o"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                    <div class="pull-right" style="font-size: xx-large;"><b>Total: $<?php echo $total; ?></b></div>
                                </div>
                                <br></br>
                                <button type="submit" class="btn btn-danger" value="Modificar" name="BotonModificarPedido">
                                    Guardar
                                </button>
                            </form>
                        <?php } ?>
                </div>              
                <!-- /.col-lg-12 -->
                        <div class="alert alert-success center-block right">
							<i class="fa fa-arrow-left"></i>
							<a href="mis_pedidos.php" class="alert-link">Volver</a>.
						</div>
            </div>
            <!-- /.row -->
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

<?php 
 $_SESSION['Mensaje']='';
require_once 'footer.inc.php'; ?>