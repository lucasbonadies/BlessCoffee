<?php
session_start();

if (empty($_SESSION['id_usuario']) || empty($_SESSION['id_nivel'])) {
    header('Location: cerrarsesion.php');
    exit;
}

// Recuperar valores de $_POST guardados en la sesión si existen
if (!empty($_SESSION['postData'])) {
    $_POST = $_SESSION['postData'];
    unset($_SESSION['postData']); // Limpiar después de usarlos
}

require_once 'funciones/conexion.php';
require_once 'funciones/select_funciones.php';
require_once 'funciones/validacion_de_datos.php';

$MiConexion = ConexionBD();
$ListadoArticulos = Listar_Articulos($MiConexion);
$CantidadArticulos = count($ListadoArticulos);
$ListadoCategorias = Listar_Tipo_Articulos($MiConexion); 

if (!empty($_POST['BotonPedir'])) {

    if(Validar_Pedido($CantidadArticulos) === true) {
        header('Location: generar_pedido.php');
        exit;
    }
}

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

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Menú</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <nav>
                        <ol class="breadcrumb panel-heading">
                            <?php 
                            foreach ($ListadoCategorias as $categoria){ ?>
                                <li class="breadcrumb-item">
                                    <button type="button" class="btn btn-info" value="<?= $categoria['NOMBRE'] ?>" onclick="mostrarCategoria('<?= $categoria['ID_TIPO'] ?>')">
                                        <?= $categoria['NOMBRE'] ?>
                                    </button>
                                </li>
                            <?php } ?>
                            <?php if(($_SESSION['id_nivel'] == 1 || $_SESSION['id_nivel'] == 5)){ ?>
                                <li class="breadcrumb-item">
                                    <button type="button" class="btn btn-warning" onclick="window.location.href='modificar_menu.php';">
                                        Modificar Menú
                                    </button>
                                </li>
                            <?php } ?>
                        </ol>
                    </nav>					
                </div>
            </div>
            <form id="pedidoForm" action="generar_pedido.php" role="form" method='post'>
                    <div>
                        <button type="submit" class="btn btn-success" value="Pedir" name="BotonPedir">
                            Generar Pedido
                        </button>
                    </div>
                <br />
                <?php if (!empty($_SESSION['Mensaje'])){ ?>
                    <div class="alert alert-<?php echo $_SESSION['Estilo']; ?> alert-dismissable">
                        <?php echo $_SESSION['Mensaje']; ?>
                    </div>
                <?php } ?>

                <?php foreach ($ListadoArticulos as $i => $articulo){ 
                    if($articulo['ID_ESTADO']==1){	?>
                    <div class="row categoria" data-categoria="<?= $articulo['ID_TIPO']; ?>" style="display: none;">
                        <div class="col-lg-10 col-md-10">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col-xs-3">
                                            <img alt="- Imagen del artículo" class="img-responsive" 
                                                 src="dist/img/Imagen_Menu/<?= $articulo['IMAGEN']; ?>" />
                                        </div>
                                        <div class="col-xs-9 text-right">
                                            <div class="huge"><?= $articulo['NOMBRE']; ?></div>
                                            <div><?='$'.$articulo['PRECIO_UNITARIO']; ?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    <div class="form-group pull-right">
                                        <label for="cantidad<?= $i ?>">Cantidad</label>
                                        <input type="number" name="Cantidad<?= $i ?>" id="cantidad" min="0" max="20" 
                                               value="<?= (!empty($_POST["Cantidad$i"]) && $_POST["Cantidad$i"] > 0) ? $_POST["Cantidad$i"] : '0'; ?>" >
                                        <label class="inline">
                                            <input type="checkbox" name="Articulo<?= $i ?>" id="agregar" value="<?= $articulo['ID_ARTICULO']; ?>" 
                                            <?= (!empty($_POST["Articulo$i"]) && $_POST["Articulo$i"] == $articulo['ID_ARTICULO']) ? 'checked' : ''; ?>> Pedir
                                        </label>
                                        <div class="form-group text-center">
                                            <input type="hidden" name="<?='ProductoNombre'.$i ?>" value="<?= $articulo['NOMBRE']; ?> ">
                                            <input type="hidden" name="Tipo<?= $i ?>" value="<?=$articulo['TIPO_ITEM']?>">
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>						
                            </div>
                        </div>					
                    </div>
                <?php } } ?>
            </form>
        </div>
    </div>

    <button id="back-to-top"><i class="fa fa-long-arrow-up"></i></button>

    <script src="js/mostrar_ocultar.js"> </script>
	
	<?php
	$_SESSION['Mensaje']= '';
	$_SESSION['Estilo']='';
	?>

<?php require_once 'footer.inc.php'; ?>