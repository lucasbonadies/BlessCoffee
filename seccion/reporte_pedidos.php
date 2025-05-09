<?php 
session_start();

if (empty($_SESSION['id_usuario']) || empty($_SESSION['id_nivel'])) {
    header('Location: cerrarsesion.php');
    exit;
}
//Añado una verificacion extra para que Alguien que no tenga acceso pueda entrar a un Scrip no permitido 
if ($_SESSION['id_nivel'] != 5) {
    header('Location: index.php');
    exit;
}

require_once 'funciones/conexion.php';
$MiConexion = ConexionBD();

$_SESSION['Mensaje'] = '';
$_SESSION['Estilo'] = 'warning';

require_once 'funciones/select_funciones.php';
require_once 'funciones/validacion_de_datos.php';

if (Validar_Reporte_Pedidos($_POST['Desde'], $_POST['Hasta'], $_POST['Estado'])=== false) {  
    $_SESSION['postData'] = $_POST;
    header('Location: panel_reportes.php');
    exit;
}

$ListadoPedidos = Seleccionar_Pedidos_Por_Fecha($MiConexion, $_POST['Desde'], $_POST['Hasta'], $_POST['Estado']);
$ListadoPedidosPorcentaje = Seleccionar_Pedidos_Por_Fecha($MiConexion, $_POST['Desde'], $_POST['Hasta'], NULL);
$totalPedidos = count($ListadoPedidosPorcentaje);
//contadores
$totalPedidosPendientes = 0;
$totalPedidosEntregados = 0;
$totalPedidosCancelados = 0;

// Contar pedidos según estado
foreach ($ListadoPedidosPorcentaje as $pedido) {
    switch ($pedido['id_estado']) {
        case 6:
            $totalPedidosPendientes++;
            break;
        case 8:
            $totalPedidosEntregados++;
            break;
        case 9:
            $totalPedidosCancelados++;
            break;
    }
}

require_once 'header.inc.php'; 
?>
<!-- Script necesario para el grafico de torta -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script> 
</head>

<body>
    <div id="wrapper">
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
            <?php require_once 'user.inc.php'; ?>
            <?php require_once 'navbar.inc.php'; ?>           
        </nav>
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Reporte pedidos</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12" style="font-size: 18px;">
                    <?php echo "Pedidos desde " . date("d/m/Y", strtotime($_POST['Desde'])) . " hasta " . date("d/m/Y", strtotime($_POST['Hasta'])); ?>
                </div>
            </div>
            <br>
            <?php if (!empty($ListadoPedidos)) { ?>
            <div class="row">
                <div class="col-lg-6">
                    <?php 
                    // Definición de estados y sus colores
                    $estados = [
                        6 => ['class' => 'panel-warning', 'label' => 'PENDIENTE'],
                        8 => ['class' => 'panel-success', 'label' => 'ENTREGADO'],
                        9 => ['class' => 'panel-danger', 'label' => 'CANCELADO'],
                    ];

                    foreach ($estados as $idEstado => $infoEstado) {
                        if ($_POST['Estado'] == $idEstado || empty($_POST['Estado'])) {
                            $totalEstado = ${"totalPedidos" . ($infoEstado['label'] === 'PENDIENTE' ? 'Pendientes' : ($infoEstado['label'] === 'ENTREGADO' ? 'Entregados' : 'Cancelados'))};
                            $porcentaje = $totalPedidos > 0 ? number_format(($totalEstado / $totalPedidos) * 100, 2) : 0;
                            ?>
                            <div class="col-lg-12 col-md-12">
                                <div class="panel <?= $infoEstado['class'] ?>">
                                    <div class="panel-heading">
                                        <div class="row">
                                            <div class="col-lg-4 col-md-3 col-xl-3 col-xs-6">
                                                <div class="huge"><?= $porcentaje ?>%</div>
                                            </div>
                                            <div class="col-lg-8 col-md-9 col-xl-9 col-xs-6 text-right">
                                                <div class="huge">Cant. de Pedidos: <?= $totalEstado ?></div>
                                                <div>ESTADO: <b><?= $infoEstado['label'] ?></b></div>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="javascript:void(0);" onclick="toggleDetalles(<?= $idEstado ?>)" style="color:black">
                                        <div class="panel-footer">
                                            <span class="pull-left">Ver detalle</span>
                                            <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                            <div class="clearfix"></div>
                                        </div>
                                    </a>
                                    <div class="table-responsive table-bordered" id="detallePedidos<?= $idEstado ?>" style="display: none;">
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
                                                <?php foreach ($ListadoPedidos as $pedido) {
                                                    if ($pedido['id_estado'] == $idEstado) { ?>
                                                        <tr class="<?= asignarClaseEstado($pedido['id_estado']); ?>">
                                                            <td><?= $pedido['id_pedido']; ?></td>
                                                            <td><?= date("d/m/Y - H:i:s", strtotime($pedido['fecha_pedido'])); ?></td>
                                                            <td><?= ucfirst($pedido['nombre_estado']); ?></td>
                                                            <td>
                                                                <a href="detalle_pedido.php?ID_PEDIDO=<?= $pedido['id_pedido']; ?>&ID_PERSONA=<?= $pedido['id_persona']; ?>" class="btn btn-success btn-circle btn-info" title="Ver">
                                                                    <i class="fa fa-info"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php }
                                                } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php 
                        }
                    }
                    ?>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        <canvas id="graficoPedidos"></canvas>
                        <script>
                            window.onload = function() {
                                crearGraficoPedidos(<?= $totalPedidosPendientes; ?>, <?= $totalPedidosEntregados; ?>, <?= $totalPedidosCancelados; ?>);
                            }
                        </script>
                        <b style="font-size: 18px;" class="pull-right">Total: <?= $totalPedidos; ?> </b>
                    </div>
                </div>
            </div>
            <?php } else {
                echo "No se encontraron pedidos para las fechas seleccionadas.<br>";
            } ?> 
            <br/>
            <button type="button" class="btn btn-info" onclick="window.location.href='panel_reportes.php';">
                Volver
            </button>
        </div>
        
    </div>

    <!-- Mostrar/Ocultar Detalle Pedidos -->
    <script src="js/mostrar_ocultar.js"></script>
     <!-- Grafico -->
    <script src="js/graficos.js"></script>

<?php require_once 'footer.inc.php'; ?>