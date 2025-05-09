<?php
session_start();

if (empty($_SESSION['id_usuario']) || empty($_SESSION['id_nivel'])) {
    header('Location: cerrarsesion.php');
    exit;
}

require_once 'funciones/conexion.php';
require_once 'funciones/select_funciones.php';
require_once 'funciones/validacion_de_datos.php';
require_once 'funciones/update_funciones.php'; // Incluimos el archivo con la función de actualización
require_once 'funciones/subir_archivo.php';

$MiConexion = ConexionBD();
$ListadoArticulos = Listar_Articulos($MiConexion);
$CantidadArticulos = count($ListadoArticulos);
$ListadoCategorias = Listar_Tipo_Articulos($MiConexion); 

$resultadosActualizacion = []; // Variable para almacenar los resultados de la actualización
$Categoria_Articulos="";


if (!empty($_POST['BotonActualizar'])) {

    $detallesArticulos = [];

    foreach ($ListadoArticulos as $index => $articulo) {
        $idArticulo = $_POST['idArticulo' . $index];
        $idTipo = $_POST['tipo' . $index]; // Lo tomamos para armar la clave
    
        // Armamos clave compuesta para evitar que se pisen (por ejemplo: 'articulo-1', 'promocion-1')
        $claveUnica = ($idTipo == 8) ? 'promocion-' . $idArticulo : 'articulo-' . $idArticulo;
    
        $detallesArticulos[$claveUnica] = [
            'id'     => $idArticulo, // seguimos enviando el ID por separado para usarlo en UPDATE
            'nombre' => $_POST['nombre' . $index],
            'precio' => $_POST['precio' . $index],
            'estado' => isset($_POST['estado' . $index]) ? 1 : 2,
            'imagen' => $_FILES['imagen' . $index],
            'tipo'   => $idTipo
        ];
    }
    
    if (Validar_Update_Articulo($detallesArticulos)===true) {
        if (ActualizarArticulosYPromociones($MiConexion, $detallesArticulos)) {
            $_SESSION['Mensaje'] = 'Modificación realizada de forma correcta.';
            $_POST = array(); 
            $_SESSION['Estilo']= 'success';
            // Redirigir para recargar la página y ver los valores actualizados
           header("Location: panel_menu.php");
            exit();
        }
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
                    <h1 class="page-header">Modificar menú</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <form id="menuForm" method="POST" action="" enctype="multipart/form-data">
                        <nav>
                            <ol class="breadcrumb panel-heading ">
                            <?php 
                                foreach ($ListadoCategorias as $categoria){ ?>
                                    <li class="breadcrumb-item">
                                        <button type="button" class="btn btn-info" value="<?= $categoria['NOMBRE'] ?>" onclick="mostrarCategoria('<?= $categoria['ID_TIPO'] ?>')">
                                            <?= $categoria['NOMBRE'] ?>
                                        </button>
                                    </li>
                                <?php } ?>
                                <li class="breadcrumb-item">
                                    <button type="submit" class="btn btn-success" name="BotonActualizar" value="Actualizar" >
                                        Guardar
                                    </button>
                                </li>
                            </ol>
                        </nav>
                        <br />
                        <?php if (!empty($_SESSION['Mensaje'])){ ?>
                            <div class="alert alert-<?php echo $_SESSION['Estilo']; ?> alert-dismissable">
                                <?php echo $_SESSION['Mensaje']; ?>
                            </div>
                        <?php } ?>
                        <?php foreach ($ListadoArticulos as $i => $articulo){ ?>
                            <div class="row categoria" data-categoria="<?= $articulo['ID_TIPO']; ?>" style="display: none;">
                                <div class="col-lg-10 col-md-10" >
                                    <div class="panel panel-primary">
                                        <div class="panel-heading">
                                            <div class="row" style="display: flex; align-items: center;">
                                                <div class="col-xs-3" >
                                                    <div class="form-group">
                                                        <img alt="Image placeholder" class="img-responsive" 
                                                        src="dist/img/Imagen_Menu/<?php echo $articulo['IMAGEN']; ?>" />
                                                    </div> 
                                                    <div class="form-group">
                                                        <label>Imagen: </label>
                                                        <small><span>(Solo: png, jpg, jpeg, bmp, webp)</span></small>
                                                        <input type="file" name="imagen<?php echo $i; ?>" id='Archivo' accept="image/*">
                                                    </div>
                                                </div>
                                                <div class="col-xs-8">
                                                    <div class="form-group "> 
                                                        <input class="form-control text-right" style="font-size: 4rem; height: 10rem;" type="text" name="nombre<?php echo $i; ?>" id="Nombre<?php echo $i; ?>" 
                                                        value="<?php echo $articulo['NOMBRE']; ?>" required 
                                                        onkeydown="return event.key != 'Enter';" />
                                                    </div>
                                                    <div class="form-group pull-right">
                                                    <input class="form-control text-right" style="font-size: 3rem; height: 7rem;" type="number" name="precio<?php echo $i; ?>" 
                                                        value="<?php echo $articulo['PRECIO_UNITARIO']; ?>"  required
                                                        onkeydown="return event.key != 'Enter';" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php foreach ($ListadoCategorias as $categoria){ 
                                                if($ListadoArticulos[$i]['ID_TIPO']==$categoria['ID_TIPO']){
                                                    $Categoria_Articulos=$categoria['NOMBRE'];
                                                }
                                            } ?>
                                        <div class="panel-footer">
                                            <div class="form-group">
                                                <label>Tipo de artículo:</label>
                                                <select class="form-control" name="tipo<?php echo $i; ?>" id="tipo_id" required >
                                                    <option value="<?php echo empty($_POST["tipo$i"])? $articulo['ID_TIPO'] : ""; ?>">
                                                        <?php echo empty($_POST["tipo$i"])? $Categoria_Articulos : "" ; ?>
                                                    </option>
                                                    <?php 
                                                    $selected= '';
                                                    foreach ($ListadoCategorias as $categoria) {
                                                        $selected = !empty($_POST["tipo$i"]) && $_POST["tipo$i"] == $categoria['ID_TIPO'] ? 'selected' : '';
                                                    ?>
                                                        <option value="<?php echo $categoria['ID_TIPO']; ?>" <?php echo $selected; ?>>
                                                            <?php echo $categoria['NOMBRE']; ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div>
                                                <input type="checkbox" name="estado<?php echo $i; ?>"
                                                <?php echo ($articulo['ID_ESTADO'] == 1) ? 'checked' : ''; ?> />  Artículo disponible
                                            </div>
                                        </div>
                                        <input type="hidden" name="idArticulo<?php echo $i; ?>" value="<?php echo $articulo['ID_ARTICULO']; ?>" />
                                        <div class="clearfix"></div>
                                    </div>
                                </div>                     
                            </div>
                        <?php } ?>
                    </form> 
                
                </div>
            </div>   
        </div>
    </div>

    <button id="back-to-top"><i class="fa fa-long-arrow-up"></i></button>

<script src="js/mostrar_ocultar.js"> </script>

<?php
$_SESSION['Mensaje']= '';
$_SESSION['Estilo']='';

?>

<?php require_once 'footer.inc.php'; ?>
