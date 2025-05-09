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
$MiConexion = ConexionBD();

//ahora voy a llamar el script con la funcion que genera mi listado
require_once 'funciones/select_funciones.php';

//voy a ir listando lo necesario para trabajar en este script: 
$ListadoDetallePedido = Listar_detalle_Pedidos($MiConexion, $ID_PERSONA, $ID_PEDIDO);
$CantidadDetallePedido = count($ListadoDetallePedido);
$total=0;

if (empty($ListadoDetallePedido)){
    $_SESSION['Mensaje']="No tiene Pedidos pendientes.";
    $_SESSION['Estilo']='info';
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
                    <h1 class="page-header"> Detalle del pedido </h1>
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
                                <div class="table-responsive table-bordered">
                                    <table class="table">
                                        <thead>
                                            <tr>                                             
                                                <th>Código</th>
                                                <th>Descripción</th>
                                                <th>Cantidad</th>
                                                <th>Precio</th>
                                                <th>Sub Total</th>
                                                <th>Imagen</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php for ($i=0; $i<$CantidadDetallePedido; $i++) { ?>
                                            <tr class="info">
                                                <td><?php echo $ListadoDetallePedido[$i]['ID_ARTICULO']; ?></td>
                                                <td><?php echo $ListadoDetallePedido[$i]['NOMBRE_ARTICULO']; ?></td>
                                                <td><?php echo $ListadoDetallePedido[$i]['CANTIDAD']; ?></td>
                                                <td><?php echo '$'.$ListadoDetallePedido[$i]['PRECIO_UNITARIO']; ?></td>
                                                <td><?php echo '$'.$ListadoDetallePedido[$i]['SUBTOTAL']; $total= $total + $ListadoDetallePedido[$i]['SUBTOTAL'];?></td>
                                                <td class="col-xs-1">
                                                    <img alt="Image placeholder" class="img-responsive" 
                                                    src="dist/img/imagen_menu/<?php echo $ListadoDetallePedido[$i]['IMAGEN']; ?>" />
                                                </td>                       
                                            </tr>  
                                            <?php } ?>
                                        </tbody>
                                        
                                    </table>
                                    <div class="pull-right" style="font-size: xx-large";><b> Total : $<?php echo $total ?> </b></div>
                                </div>
                                <?php } ?>

                                <!-- /.table-responsive -->
                            </div>
                            <!-- /.panel-body -->
                            <div class="alert alert-success center-block right">
                                <i class="fa fa-arrow-left"></i>
                                <a href="javascript:history.back()" class="alert-link">Volver</a>.
						    </div>
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

<?php 
 $_SESSION['Mensaje']='';
require_once 'footer.inc.php'; ?>