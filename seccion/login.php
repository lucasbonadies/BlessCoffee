<?php 
session_start();
    
require_once 'funciones/conexion.php';
$MiConexion=ConexionBD();

    if (!empty($_POST['BotonLogin'])) {       
        require_once 'funciones/validacion_de_datos.php';
        require_once 'funciones/datos_login.php';
        // Obtener la cantidad de intentos fallidos
        $intentos = ObtenerIntentosFallidos($_POST['Email'], $MiConexion);
        if(Validar_Email()===true && Existe_Email_Login($MiConexion)===true){
            if(Validar_Clave($_POST['Clave'])===true){
                $UsuarioLogueado= DatosLogin($_POST['Email'], $_POST['Clave'], $MiConexion) ;
                // Verificar si el usuario existe   
                if (!empty($UsuarioLogueado)) {
                    // Verificar el estado del usuario
                    if($UsuarioLogueado['ID_ESTADO']!=1){
                        $_SESSION['Mensaje']= 'No se encuentra activo en el sistema'; 
                        $_SESSION['Estilo']='warning';
                    }else{
                        // Reiniciar intentos en caso de éxito
                        ReiniciarIntentos($_POST['Email'], $MiConexion);
                        //Asigno los valores del usuario con su sesion
                        $_SESSION['id_usuario']= $UsuarioLogueado['ID_USUARIO'] ;
                        $_SESSION['email']= $UsuarioLogueado['EMAIL'] ;
                        $_SESSION['id_nivel']= $UsuarioLogueado['ID_NIVEL'] ;
                        $_SESSION['nombre']= $UsuarioLogueado['NOMBRE'] ;
                        $_SESSION['apellido']= $UsuarioLogueado['APELLIDO'] ;
                        $_SESSION['dni']= $UsuarioLogueado['DNI'] ;
                        $_SESSION['fecha_nacimiento']= $UsuarioLogueado['FECHA_NACIMIENTO'] ;
                        $_SESSION['imagen']= $UsuarioLogueado['IMG'] ; 
                        $_SESSION['nro_telefono']= $UsuarioLogueado['NRO_TELEFONO'] ;
                        $_SESSION['id_persona']= $UsuarioLogueado['ID_PERSONA'] ;
                        $_SESSION['nombre_nivel']= $UsuarioLogueado['NOMBRE_NIVEL'] ;
                        $_SESSION['id_estado']=$UsuarioLogueado['ID_ESTADO'];
                        header('Location: index.php');
                        exit;
                    }    
                }else {                   
                    $intentos++;
                    // Si ya tiene más de 3 intentos fallidos, actualizar a inactivo
                    if ($intentos >= 4) {
                        ActualizarEstadoUsuario($_POST['Email'], 2, $MiConexion); // 2 es inactivo
                        $_SESSION['Mensaje'] = 'Tu cuenta ha sido bloqueada tras varios intentos fallidos.';
                        $_SESSION['Estilo'] = 'danger';
                    } else {
                        // Actualizar la cantidad de intentos fallidos en la base de datos
                        ActualizarIntentosFallidos($_POST['Email'], $intentos, $MiConexion);
                        $_SESSION['Mensaje'] .= 'Datos incorrectos, intenta nuevamente. <br>';
                        $_SESSION['Mensaje'] .= "Intentos fallidos: $intentos <br>";
                        $_SESSION['Estilo'] = 'warning';
                    }
                }
            }else{
                $intentos++;
                $_SESSION['Mensaje'] =""; // Vacio los msj que vienen de la validacion de contraseña, para no facilitar a alguien con malas intenciones que quiera entrar.
                // Si ya tiene más de 3 intentos fallidos, actualizar a inactivo
                if ($intentos >= 4) {
                    ActualizarEstadoUsuario($_POST['Email'], 2, $MiConexion); // 2 es inactivo
                    $_SESSION['Mensaje'] = 'Tu cuenta ha sido bloqueada tras varios intentos fallidos.';
                    $_SESSION['Estilo'] = 'danger';
                } else {
                    // Actualizar la cantidad de intentos fallidos en la base de datos
                    ActualizarIntentosFallidos($_POST['Email'], $intentos, $MiConexion);
                    $_SESSION['Mensaje'] .= 'Datos incorrectos, intenta nuevamente. <br>';
                    $_SESSION['Mensaje'] .= "Te quedan ".(3-$intentos)." intentos <br>";
                    $_SESSION['Estilo'] = 'warning';
                }
            }    
        }
    }    

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Login</title>

    <!-- Bootstrap Core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="vendor/metisMenu/metisMenu.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <link rel="icon" href="dist/img/favicon-32x32.png" type="image/x-icon">
</head>

<body> 
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="login-panel panel panel-default">
                    <div class="panel-body">
                        
                        <div class="text-center">
                            <img class="img-fluid logoBlessCoffee" style="max-width: 223px;padding-bottom: 2rem;" src='dist/img/Imagen_logo/Logo2.jpeg' />
                        </div>
                            <form role="form" method='post'>
                                <?php if (!empty($_SESSION['Mensaje'])) { ?> 
                                    <div class="alert alert-<?php echo $_SESSION['Estilo'] ?> alert-dismissable">
                                        <?php echo $_SESSION['Mensaje']; ?> 
                                    </div>
                                <?php }  ?>
                                <fieldset>
                                    <div class="form-group">
                                        <input class="form-control" placeholder="E-mail" name="Email" type="email" autofocus required value="<?php echo !empty($_POST['Email']) ? $_POST['Email'] : ''; ?>">
                                    </div>
                                    <div class="form-group">
                                        <input ID="txtPassword" class="form-control" placeholder="Contraseña" name="Clave" type="password" required>
                                        <div class="input-group-append" style="margin-top: 10px";>
                                            <button id="show_password" class="btn btn-primary" type="button" onclick="mostrarPassword()" title="Ver/Ocultar Contraseña"> 
                                                <span class="fa fa-eye-slash icon"></span> 
                                            </button>
                                        </div>
                                    </div>
                                    <div class="form-group text-center">
                                        <a href="recuperar_clave.php" > ¿Olvidaste tu contraseña? </a>
                                    </div>
                                    <div class="form-group text-center">
                                        <button type="submit" class="btn btn-success" value="Login" name="BotonLogin" >Ingresar</button>                                 
                                    </div>
                                </fieldset>
                            </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- jQuery -->
    <script src="vendor/jquery/jquery.min.js"></script>

    <!-- Mostrar/Ocultar Clave -->
    <script src="js/mostrar_ocultar.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="vendor/metisMenu/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="dist/js/sb-admin-2.js"></script>

    <?php  $_SESSION['Mensaje'] =""; ?>
</body>

</html>
