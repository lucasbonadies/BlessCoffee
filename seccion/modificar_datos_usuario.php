<?php 
session_start();

if(empty($_SESSION['id_usuario']) || empty($_SESSION['id_nivel'])){
    header('Location: cerrarsesion.php');
    exit;
}

require_once 'funciones/conexion.php';
$MiConexion=ConexionBD(); 

require_once 'funciones/select_usuarios.php';
$DatosUsuario=array();
$ID_USUARIO_UPDATE= $_SESSION['id_usuario'];
if (!empty($ID_USUARIO_UPDATE)){
	//se estan trayendo los datos de la base
	$DatosUsuario = EncontrarUsuario($ID_USUARIO_UPDATE, $MiConexion);
	if(empty($DatosUsuario)){
		//si llega vacio $DatosUsuario: 
		$_SESSION['Mensaje']='No se encontraró ningún registro del usuario.';
	}
}else {
	//si llega vacia la variable de $_GET: 
	$_SESSION['Mensaje']='Sin datos para mostrar...';	
}

if (!empty($_POST['BotonModificar'])) {

	$email= $_SESSION['email'];

	require_once 'funciones/validacion_de_datos.php';
	$pass= false; // variable para confirmar si se puedo subir los datos o no antes de entrar al siguiente if...   

	if(Existe_Clave($_POST['Clave'], $email, $MiConexion)===true){
		if(!empty($_POST['ClaveNueva'])){
			if(Validar_Clave($_POST['ClaveNueva'])===true){
				$pass= true;
			}
		}else{
			$pass= true;
		}
	}
	if($pass===true){
		if (Validar_Modificacion_Datos_Personales($_POST['ClaveNueva'])=== true) {  //salio bien la validacion
			/**** subo el archivo al servidor si es necesario***/
			require_once 'funciones/subir_archivo.php';
			if (SubirArchivo('Imagen_Perfil')=== true) { // Por parametro envio el nombre del $_FILE que tiene que ser igual al nombre de carpeta de alojamiento
				//modifico en la base de datos
				if (Update_Usuario($MiConexion, $DatosUsuario['ID_USUARIO']) != false
					&& Update_Persona($MiConexion, $DatosUsuario['ID_PERSONA']) != false) {
					$_SESSION['Mensaje'].= "Tus datos se han actualizado.</br>";
					$_SESSION['Estilo'] = "success";
				}else {
					$_SESSION['Mensaje'].= "Tus datos no pudieron ser actualizados.";
					$_SESSION['Estilo'] = "warning";
				}
				if(Update_Imagen_Perfil($MiConexion, $DatosUsuario['ID_PERSONA']) != false) {
					$_SESSION['Estilo'] = "success";
					header("Location: modificar_datos_usuario.php?ID_USUARIO=$ID_USUARIO_UPDATE");
        			exit;
				}else {
					$_SESSION['Mensaje'].= "La Imagen no pudo ser actualizada.";
					$_SESSION['Estilo'] = "warning";
				}
			}
		}
	}	
				 
}
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
                <a class="navbar-brand" href="#">
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
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Datos del usuario</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-10">
                    <div class="panel panel-<?php
					//si no tengo datos de usuario
						if (empty($DatosUsuario))
							echo 'default';
						else {
							//el array tiene valores
							echo $DatosUsuario['ID_ESTADO']==1 ? 'success' : 'danger'; 
						} ?>  ">
						<?php if (!empty($_SESSION['Mensaje'])) { ?>
						<div class="alert alert-<?php echo $_SESSION['Estilo']; ?> alert-dismissable">
						<?php echo $_SESSION['Mensaje']; ?>
						</div>
						<?php } ?>
						<div class="panel-heading ">
                            Usuario <?php echo $DatosUsuario['ID_ESTADO']==1 ? 'activo' : 'inactivo' ; ?>
                       	</div>
						<!-- /.panel-heading -->
							<div class="panel-body ">
								<div class="row">
									<form method='post' enctype="multipart/form-data" >
										<div class="col-lg-5">
											<div class="form-group">
												<label>Id Usuario:</label>
												<input class="form-control" type="text" name="Id_usuario" id="id_usuario" readonly
												value="<?php echo $DatosUsuario['ID_USUARIO'];?>">
											</div>
											<div class="form-group">
												<label>Nombre:<span class="text-danger"> *</span></label>
												<input class="form-control" type="text" name="Nombre" id="nombre" required
												value="<?php echo !empty($_POST['Nombre'])? $_POST['Nombre'] : $DatosUsuario['NOMBRE']; ?>">
											</div>
											<div class="form-group">
												<label>Apellido:<span class="text-danger"> *</span></label>
												<input class="form-control" type="text" name="Apellido" id="apellido" required
												value="<?php echo !empty($_POST['Apellido'])? $_POST['Apellido'] : $DatosUsuario['APELLIDO']; ?>">
											</div>
											<div class="form-group">
												<label>Dni:</label>
												<input class="form-control" type="number" name="Dni" id="dni" readonly
												value="<?php echo $DatosUsuario['DNI'];?>">
											</div>
											<div class="form-group">
												<label>Fecha de nacimiento:<span class="text-danger"> *</span></label>
												<input class="form-control" type="date" name="FechaNacimiento" id="fechaNacimiento" min="1924-01-01" max="2006-01-01" required
												value="<?php echo !empty($_POST['FechaNacimiento'])?  $_POST['FechaNacimiento'] : $DatosUsuario['FECHA_NACIMIENTO']; ?>"> <!-- FALTA REALIZAR DESDE PHP PARA QUE SE ACTUALICE SOLO MAXIMO Y MINIMO PARA LA FECHA DE NACIMIENTO -->
											</div>
											<div class="form-group">
												<label>Telefono:<span class="text-danger"> *</span></label>
												<small>Formato: 2235959595</small>
												<input class="form-control" type="tel" name="Telefono" id="telefono" required
												value="<?php echo !empty($_POST['Telefono'])? $_POST['Telefono'] : $DatosUsuario['NRO_TELEFONO']; ?>">  <!-- probar con required para la comprobacion de valores y  pattern="[0-9]{3}-[0-9]{1}-[0-9]{6}" para la forma del numero-->
											</div>
											<div class="form-group">
												<label>Email:</label>
												<input class="form-control" type="email" name="Email" id="email" readonly
												value="<?php echo $DatosUsuario['EMAIL'];?>">
											</div>
											<div class="form-group">
												<label>Contraseña Actual:<span class="text-danger"> *</span></label>
												<small><span class="text-danger">Para confirmar los cambios, ingrese la contraseña</span></small>
												<input class="form-control" type="password" name="Clave" id="clave" value="" required>
											</div>
											<?php if($_SESSION['id_nivel']!=5){ ?>
											<div class="form-group">
												<label>Contraseña Nueva:</label>	
												<input class="form-control" type="password" name="ClaveNueva" id="claveNueva" value="">
											</div>
											<div class="form-group">
												<label>Reingresar Contraseña:</label>
												<input class="form-control" type="password" name="ReClave" id="reclave" value="">
											</div>
											<?php } ?>
											<div class="form-group">
												<label>Subi tu imagen <span class="text-danger">(png, jpg, jpeg, bmp)</span></label>
												<input type="file" name="Imagen_Perfil" id='Archivo' accept="image/*">
											</div>
											<button type="submit" class="btn btn-default" value="Modificar" name="BotonModificar" > GUARDAR </button>     
										</div> 
										<!-- /.col-lg-5 -->
										<div class="col-lg-3">
											<div class="form-group">
												<img alt="Mi Imagen de Perfil" class="img-responsive" 
												src='dist/img/Imagen_Perfil/<?php echo $DatosUsuario['IMG']; ?>' />
											</div>
										</div>
									</form>
								</div>
								<!-- /.row -->
							</div>
							<!-- /.panel-body -->
						<div class="alert alert-info center-block right">
							<i class="fa fa-arrow-left"></i>
							<a href="mis_datos.php" class="alert-link">Volver</a>.
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