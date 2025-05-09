<?php 
session_start();

//Añado una verificacion extra para que Alguien que no tenga acceso pueda entrar a un Scrip no permitido 
if($_SESSION['id_nivel']!=1 && $_SESSION['id_nivel'] !=5){
    header('Location: index.php');
    exit;
}

if(empty($_SESSION['id_usuario']) or empty($_SESSION['id_nivel'])){
    header('Location: cerrarsesion.php');
    exit;
 }

require_once 'funciones/conexion.php';
$MiConexion = ConexionBD();

require_once 'funciones/select_funciones.php';
$ListadoTipoArticulo=Listar_Tipo_Articulos($MiConexion);
$CantidadTipoArtiuculo = count($ListadoTipoArticulo);

require_once 'funciones/validacion_de_datos.php';
require_once 'funciones/insert_funciones.php';
require_once 'funciones/subir_archivo.php';




if (!empty($_POST['BotonInsertarArticulo'])) {
    //estoy en condiciones de poder validar los datos	
    if (Validar_Articulo()!=false) {
        /**** subo el archivo ***/
        if (SubirArchivo('Imagen_Menu') != false ) {
            if (InsertarArticulo($MiConexion) != false) {
                $_SESSION['Mensaje'] = 'Se ha registrado correctamente.';
                //$_POST = array(); 
                $_SESSION['Estilo']= 'success';
                header('Location: insertar_articulo.php');
                exit;
            }
        }
    }
}

if (!empty($_POST['BotonInsertarPromocion'])) {

    $nombrePromocion = $_POST['Nombre_Promocion'];
    $precioPromocion = $_POST['Precio_Promocion'];
    $descripcionPromocion = $_POST['Descripcion_Promocion'] ?? '';
    $id_tipo = 8; // Tipo fijo para promociones
    $imagenPromocion = empty($_FILES['Imagen_Menu']['name']) ? 'sin_imagen.jpg' : $_FILES['Imagen_Menu']['name'];
    
    // Procesar los artículos seleccionados
    $articulos = [];
    if (isset($_POST['articulos']) && is_array($_POST['articulos'])) {
        foreach ($_POST['articulos'] as $idArticulo) {
            // Verificar si existe la cantidad asociada al artículo
            if (isset($_POST['cantidad_' . $idArticulo])) {
                $cantidad = intval($_POST['cantidad_' . $idArticulo]);
                $articulos[] = ['id' => $idArticulo, 'cantidad' => $cantidad];
            }
        }
    }    

    // Subir la imagen de la promoción
    if (SubirArchivo('Imagen_Menu')===true){ 
        // Validar los datos antes de proceder
        if (Validar_Promocion($nombrePromocion, $precioPromocion, $articulos)===true) {
           // Insertar la promoción en la base de datos
            if (InsertarPromocion($MiConexion, $nombrePromocion, $precioPromocion, $descripcionPromocion, $id_tipo, $imagenPromocion, $articulos)) {
                $_SESSION['Mensaje'] = 'Promoción registrada correctamente.';
                $_SESSION['Estilo'] = 'success';
            } else {
                $_SESSION['Mensaje'] = 'Error al registrar la promoción.';
                $_SESSION['Estilo'] = 'danger';
            }
        }       
        
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
                    <h1 class="page-header">Agregar Artículos</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <?php if (!empty($_SESSION['Mensaje'])) { ?>
                <div class="alert alert-<?php echo $_SESSION['Estilo']; ?> alert-dismissable">
                <?php echo $_SESSION['Mensaje']; ?>
                </div>
            <?php } ?>

            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Complete los campos <span class="text-danger"> OBLIGATORIOS *</span> para agregar un nuevo artículo al menú.
                        </div>
                        <div class="panel-body">
                            <form role="form" method='post' enctype="multipart/form-data" >
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>Nombre del artículo:<span class="text-danger"> *</span></label>
                                            <input class="form-control" type="text" name="Nombre" id="nombre" required
                                            value="<?php echo !empty($_POST['Nombre']) ? $_POST['Nombre'] : ''; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Precio:<span class="text-danger"> *</span></label>
                                            <input class="form-control" type="number" name="Precio_unitario" id="precio_unitario" step="0.01" min="0.00" required
                                            value="<?php echo !empty($_POST['Precio_unitario']) ? $_POST['Precio_unitario'] : ''; ?>">
                                        </div>
										<div class="form-group">
                                            <label>Descripción:</label>
                                            <input class="form-control" type="text" name="Descripcion" id="descripcion" 
                                            value="<?php echo !empty($_POST['Descripcion']) ? $_POST['Descripcion'] : ''; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Imagen: </label>
                                            <small><span class="text-danger">(Solo: png, jpg, jpeg, bmp, webp)</span></small>
                                            <input type="file" name="Imagen_Menu" id='Archivo' accept="image/*">
                                        </div>
										<div class="form-group">
											<label>Tipo de artículo<span class="text-danger"> *</span></label>
											<select class="form-control" name="Tipo" id="tipo" required>
												<option value="">Seleccionar...</option>
												<?php 
												$selected='';
												for ($i=0 ; $i < $CantidadTipoArtiuculo ; $i++) {
													if (!empty($_POST['Tipo'])  && $_POST['Tipo'] ==  $ListadoTipoArticulo[$i]['ID_TIPO'] ) {
														$selected = 'selected';
													}else {
														$selected='';
													}
													?>
													<option value="<?php echo $ListadoTipoArticulo[$i]['ID_TIPO']; ?>" <?php echo $selected; ?>  >
														<?php echo $ListadoTipoArticulo[$i]['NOMBRE']; ?>
													</option>
												<?php } ?>
											</select>
										</div>
                                        <button type="submit" class="btn btn-default" value="Guardar" name="BotonInsertarArticulo" >Guardar</button>
                                       
                                    </div>
                                    <!-- /.row (nested) -->
                                </div>
                                
                            </form>

                            <!-- /.panel-body -->
                        </div>
                        <!-- /.panel -->
                        <div class="panel-body">
                        </div>
                        <!-- /.panel -->
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /#page-wrapper -->
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Crear Promociones</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Complete los campos <span class="text-danger">OBLIGATORIOS *</span> para agregar una nueva promoción.
                        </div>
                        <div class="panel-body">
                            <form role="form" method="post" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>Nombre de la promoción:<span class="text-danger"> *</span></label>
                                            <input class="form-control" type="text" name="Nombre_Promocion" id="nombre_Promocion" required>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Precio promocion:<span class="text-danger"> *</span></label>
                                            <input class="form-control" type="number" name="Precio_Promocion" step="0.01" min="0.00" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Descripción:</label>
                                            <input class="form-control" type="text" name="Descripcion_Promocion" id="descripcion_Promocion" 
                                            value="<?php echo !empty($_POST['Descripcion_Promocion']) ? $_POST['Descripcion_Promocion'] : ''; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Imagen de la promoción:</label>
                                            <small><span class="text-danger">(Solo: png, jpg, jpeg, bmp, webp)</span></small>
                                            <input type="file" name="Imagen_Menu" id='Archivo2' accept="image/*">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Seleccione los artículos para la promoción:</label>
                                            <input type="text" id="buscarArticulo" class="form-control" placeholder="Buscar artículo...">
                                            <ul id="listaArticulos" class="list-group" style="margin-top: 5px; max-height: 150px; overflow-y: auto;"></ul>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Artículos seleccionados:</label>
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Artículo</th>
                                                        <th>Cantidad</th>
                                                        <th>Acción</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="listaSeleccionados"></tbody>
                                            </table>
                                        </div>
                                        <button type="submit" class="btn btn-default" value="Guardar" name="BotonInsertarPromocion">Guardar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /#wrapper -->
            
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                let articulos = [
                    <?php 
                    $query = "SELECT id_articulo, nombre FROM articulos";
                    $result = mysqli_query($MiConexion, $query);
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "{ id: {$row['id_articulo']}, nombre: '{$row['nombre']}' },";
                    }
                    ?>
                ];
                
                let buscarInput = document.getElementById("buscarArticulo");
                let listaArticulos = document.getElementById("listaArticulos");
                let listaSeleccionados = document.getElementById("listaSeleccionados");
                
                buscarInput.addEventListener("input", function() {
                    let filtro = buscarInput.value.toLowerCase();
                    listaArticulos.innerHTML = "";
                    
                    let resultados = articulos.filter(a => a.nombre.toLowerCase().includes(filtro));
                    resultados.forEach(a => {
                        let li = document.createElement("li");
                        li.className = "list-group-item";
                        li.textContent = a.nombre;
                        li.dataset.id = a.id;
                        li.addEventListener("click", agregarArticulo);
                        listaArticulos.appendChild(li);
                    });
                });
                
                function agregarArticulo(event) {
                    let id = event.target.dataset.id;
                    let nombre = event.target.textContent;

                    // Verificar si el artículo ya está en la lista
                    let existe = Array.from(listaSeleccionados.children).some(fila => {
                        return fila.querySelector("input[name='articulos[]']").value === id;
                    });

                    if (existe) {
                        alert("El artículo ya ha sido agregado.");
                        return;
                    }

                    // Crear la fila para el artículo
                    let fila = document.createElement("tr");
                    let tdNombre = document.createElement("td");
                    tdNombre.innerText = nombre;
                    let inputHidden = document.createElement("input");
                    inputHidden.type = "hidden";
                    inputHidden.name = "articulos[]";
                    inputHidden.value = id;
                    tdNombre.appendChild(inputHidden);

                    let tdCantidad = document.createElement("td");
                    let inputCantidad = document.createElement("input");
                    inputCantidad.type = "number";
                    inputCantidad.name = "cantidad_" + id;
                    inputCantidad.className = "form-control";
                    inputCantidad.value = 1;
                    inputCantidad.min = 1;
                    tdCantidad.appendChild(inputCantidad);

                    let tdAccion = document.createElement("td");
                    let btnEliminar = document.createElement("button");
                    btnEliminar.type = "button";
                    btnEliminar.className = "btn btn-danger btn-sm";
                    btnEliminar.innerText = "Eliminar";
                    btnEliminar.onclick = function() {
                        this.closest("tr").remove();
                    };
                    tdAccion.appendChild(btnEliminar);

                    fila.appendChild(tdNombre);
                    fila.appendChild(tdCantidad);
                    fila.appendChild(tdAccion);

                    listaSeleccionados.appendChild(fila);
                    listaArticulos.innerHTML = "";
                    buscarInput.value = "";
                }
            });
        </script>
    </div>
    <!-- /#wrapper -->
    <script src="mostrar_ocultar.js"></script>
<?php  $_SESSION['Mensaje'] =""; ?>
<?php require_once 'footer.inc.php'; ?>