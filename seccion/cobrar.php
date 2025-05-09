<?php
// Verifica si una sesión ya está activa antes de llamar a session_start()
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
//Añado una verificacion extra para que Alguien que no tenga acceso pueda entrar a un Scrip no permitido 
if($_SESSION['id_nivel'] !=4 && $_SESSION['id_nivel'] !=5  ){
    header('Location: index.php');
    exit;
}

// Verifica si el usuario está autenticado y tiene los permisos necesarios
if (empty($_SESSION['id_usuario']) || empty($_SESSION['id_nivel'])) {
    // Si no está autenticado o no tiene permisos, redirige a cerrarsesion.php
    header('Location: cerrarsesion.php');
    exit;
}

// Incluye el archivo de conexión a la base de datos y otras funciones necesarias
require_once 'funciones/conexion.php';
require_once 'funciones/registrar_pago.php'; // Archivo con la función para registrar el pago

// Establece la conexión con la base de datos
$conexion = ConexionBD();

// Obtiene el consumo total de la mesa común
$consumo_total = ObtenerConsumoTotal($conexion);

// Inicializa la variable para almacenar el mensaje de resultado del pago
$mensaje_pago = "";

// Verifica si el método de la solicitud es POST (el formulario fue enviado)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtiene los valores del formulario
    $monto = $_POST['monto'];
    $metodo_pago = $_POST['metodo_pago'];
    $id_pedido = $_POST['id_pedido'];

    // Inicializa el id_estado por defecto a "Rechazado" (14)
    $id_estado = 14;

    // Llama a la función RegistrarPago y pasa los valores del formulario y la conexión
    $pago_exitoso = RegistrarPago($monto, $metodo_pago, $conexion, $id_pedido, $id_estado);

    // Verifica si el pago fue exitoso
    if ($pago_exitoso) {
        // Si el pago fue exitoso, establece el id_estado a "Aceptado" (13)
        $id_estado = 13;

        // Actualiza el estado del pago en la base de datos
        ActualizarEstadoPago($conexion, $id_pago, $id_estado);

        // Cierra la conexión con la base de datos
        mysqli_close($conexion);

        // Redirige a la página de cerrar mesa sin mensaje de éxito en la URL
        header('Location: cerrar_mesa.php');
        exit;
    } else {
        // Si hubo un error al registrar el pago, establece el mensaje correspondiente
        $mensaje_pago = "Hubo un error al registrar el pago.";
    }
}

// Cierra la conexión con la base de datos si no se ha redirigido
mysqli_close($conexion);

// Incluye el archivo de encabezado
require_once 'header.inc.php';
?>
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


<!-- HTML de la página -->
<div id="wrapper">
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Cobrar</h1>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12">
                <!-- Mostrar el mensaje de resultado del pago -->
                <?php if (!empty($mensaje_pago)) { echo "<p>$mensaje_pago</p>"; } ?>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-4 col-md-6">
                <div class="panel panel-red">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-3">
                                <i class="fa fa-credit-card fa-5x"></i>
                            </div>
                            <div class="col-xs-9 text-right">
                                <div class="huge">Caja</div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <!-- Formulario para registrar un pago -->
                        <form action="cobrar.php" method="post">
                            <label for="monto">Monto:</label>
                            <select id="monto" name="monto" required>
                                <?php foreach ($consumo_total as $id_pedido => $suma_total) { ?>
                                    <option value="<?php echo $suma_total; ?>">ID <?php echo $id_pedido; ?>: $<?php echo $suma_total; ?></option>
                                <?php } ?>
                            </select>
                            <br><br>
                            <label for="metodo_pago">Método de Pago:</label>
                            <select id="metodo_pago" name="metodo_pago" required onchange="mostrarInfoPago()">
                                <option value="">Selecciona un método</option>
                                <option value="efectivo">Efectivo</option>
                                <option value="transferencia">Transferencia</option>
                            </select>
                            <br><br>
                            <!-- Campo oculto para el ID del pedido -->
                            <input type="hidden" name="id_pedido" value="<?php echo $id_pedido; ?>">

                            <input type="submit" value="Registrar Pago">
                        </form>

                    </div>
                </div>
            </div>
            <!-- /.col-lg-4 col-md-6 -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /#page-wrapper -->
</div>
<!-- /#wrapper -->

<!-- Archivo JavaScript para la lógica de métodos de pago -->
<script src="js/metodos_pago.js"></script>

<?php
// Incluye el archivo de pie de página
require_once 'footer.inc.php';
?>
