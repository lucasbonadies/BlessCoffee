<?php 
session_start();

// Verificar si la sesión es válida
if (empty($_SESSION['id_usuario']) || empty($_SESSION['id_nivel'])) {
    header('Location: cerrarsesion.php');
    exit;
}

// Incluir conexión y funciones necesarias
require_once 'funciones/conexion.php';
require_once 'funciones/select_funciones.php';

$MiConexion = ConexionBD();
$pedidosPorEstado = [
    1 => ['estado' => 6, 'titulo' => 'Pedidos pendientes'],
    2 => ['estado' => 8, 'titulo' => 'Pedidos entregados'],
    3 => ['estado' => 9, 'titulo' => 'Pedidos cancelados']
];

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
            <?php require_once 'navbar.inc.php'; ?>           
        </nav>

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Registro de pedidos</h1>
                </div>
            </div>

            <?php 
            // Ciclo a través de los diferentes estados de pedidos
            foreach ($pedidosPorEstado as $estadoPedido) {
                $ListadoPedidos = Listar_Pedidos($MiConexion, $_SESSION['id_persona'], $estadoPedido['estado']);
                $titulo = $estadoPedido['titulo'];

                if (empty($ListadoPedidos)) {
                    $_SESSION['Mensaje'] = "No tienes pedidos para mostrar.";
                    $_SESSION['Estilo'] = 'info';
                } else {
                    $_SESSION['Mensaje'] = "";
                    $_SESSION['Estilo'] = "";
                }
            ?>

            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <?php echo $titulo; ?>
                        </div>
                        <div class="panel-body">
							<?php if (!empty($_SESSION['Mensaje'])) { ?>
								<div class="alert alert-<?php echo $_SESSION['Estilo']; ?> alert-dismissable">
								<?php echo $_SESSION['Mensaje'] ?>
								</div>
                            <?php } ?>

                            <?php if (!empty($ListadoPedidos)) { ?>
                            <div class="table-responsive table-bordered">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Pedido N°</th>
                                            <th>Fecha / Hora</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($ListadoPedidos as $pedido) { ?>
                                        <tr class="<?php echo asignarClaseEstado($pedido['ID_ESTADO']); ?>">
                                            <td><?php echo $pedido['ID_PEDIDO']; ?></td>
                                            <td><?php echo date("d/m/Y - H:i:s", strtotime($pedido['FECHA_PEDIDO'])); ?></td>
                                            <td><?php echo ucfirst($pedido['NOMBRE_ESTADO']); ?></td>
                                            <td>
                                                <a href="detalle_pedido.php?ID_PEDIDO=<?php echo $pedido['ID_PEDIDO']; ?>&ID_PERSONA=<?php echo $pedido['ID_PERSONA']; ?>" class="btn btn-success btn-circle btn-info" title="Ver">
                                                    <i class="fa fa-info"></i>
                                                </a>
                                                <?php if ($pedido['ID_ESTADO'] != 9 && $pedido['ID_ESTADO'] != 8) { ?>
                                                <a href="modificar_pedido.php?ID_PEDIDO=<?php echo $pedido['ID_PEDIDO']; ?>&ID_PERSONA=<?php echo $pedido['ID_PERSONA']; ?>" class="btn btn-warning btn-circle" title="Modificar">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                                <a href="cancelar_pedido.php?ID_PEDIDO=<?php echo $pedido['ID_PEDIDO']; ?>" class="btn btn-danger btn-circle" title="Cancelar" 
                                                onclick="return confirm('Confirma que quiere CANCELAR este Pedido?')">
                                                    <i class="fa fa-times"></i>
                                                </a>
                                                <?php } ?>
                                            </td>
                                        </tr>  
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>

<?php 
$_SESSION['Mensaje'] = "";
$_SESSION['Estilo'] = "";
require_once 'footer.inc.php'; 
?>
