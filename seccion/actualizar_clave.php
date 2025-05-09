<?php
session_start();

// Si el token e id_usuario están presentes en GET, los guardamos en la sesión
if (!empty($_GET['token']) && !empty($_GET['id_usuario'])) {
    $_SESSION['token'] = htmlspecialchars($_GET['token']);
    $_SESSION['id_usuario'] = htmlspecialchars($_GET['id_usuario']);
}

// Verificar si el token y el id_usuario están presentes en la sesión
if (empty($_SESSION['token']) || empty($_SESSION['id_usuario'])) {
    $_SESSION['Mensaje'] = "Acceso denegado. Parámetros faltantes.";
    $_SESSION['Estilo'] = "danger";
    header('Location: login.php');
    exit();
}

require_once 'funciones/conexion.php';
$MiConexion=ConexionBD(); 

$_SESSION['Mensaje']='';
$_SESSION['Estilo']='warning';

if (!empty($_POST['BotonUpdate'])) {
    require_once 'funciones/validacion_de_datos.php';
    if(Validar_Clave($_POST['Clave'])===true){
        require_once 'funciones/datos_login.php';
        if(validar_token($MiConexion, $_SESSION['token'], $_SESSION['id_usuario'])===true){
            $_SESSION['Mensaje']= "La contraseña ha sido actualizada.";
            $_SESSION['Estilo']='success';
            ActualizarEstadoUsuario($_SESSION['vEmail_Temporal'], 1, $MiConexion);
            unset($_SESSION['token']); //elimina solo $_SESSION['token']
            unset($_SESSION['id_usuario']);//elimina solo $_SESSION['id_usuario']
            unset($_SESSION['vEmail_Temporal']);
            header('Location: login.php');
        }else{
            $_SESSION['Mensaje']= "Error al intentar actualizar la contraseña. <br/>";
            $_SESSION['Mensaje'].= "Token inválido o expirado.";
            $_SESSION['Estilo']='warning';
        }    
    }else{
        $_SESSION['Mensaje']='Verifique la contraseña y vuelva a intentar';
        $_SESSION['Estilo']='warning';
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

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        [endif]
    -->
    <link rel="icon" href="dist/img/favicon-32x32.png" type="image/x-icon">
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="login-panel panel panel-default">
                    <div class="panel-body">    
                        <div class="text-center">
                            <img class="img-fluid" src='dist/img/LoginChef.png' />
                        </div>
                        <form action="actualizar_clave.php" method="post">
                            <?php if (!empty($_SESSION['Mensaje'])) { ?> 
                                <div class="alert alert-<?php echo $_SESSION['Estilo'] ?> alert-dismissable">
                                    <?php echo $_SESSION['Mensaje']; ?> 
                                </div>
                            <?php }  ?>
                            <fieldset>
                                <div class="form-group text-center">
                                    Ingrese una nueva contraseña.
                                </div>
                                <div class="form-group text-center">
                                    <input type="hidden" name="Token" value="<?php echo $_SESSION['token']; ?> ">
                                </div>
                                <div class="form-group text-center">
                                    <input type="hidden" name="Id_usuario" value="<?php echo $_SESSION['id_usuario']; ?> ">
                                </div>
                                <div class="form-group">
                                    <input ID="txtPassword" class="form-control" placeholder="Contraseña" name="Clave" type="password" required>
                                    <div class="input-group-append" style="margin-top: 10px";>
                                        <div class="input-group-append" style="margin-top: 10px";>
                                            <button id="show_password" class="btn btn-primary" type="button" onclick="mostrarPassword()" title="Ver/Ocultar Contraseña"> 
                                                <span class="fa fa-eye-slash icon"></span> 
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group text-center">
                                    <button type="submit" class="btn btn-default" value="Actualizar_Contraseña" name="BotonUpdate" >Actualizar contraseña</button>                                 
                                </div>
                                <div class="form-group text-center">
                                    <a href="login.php"> ir a Login </a>
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
    <script src="js/mostrar_ocultar_clave.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="vendor/metisMenu/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="dist/js/sb-admin-2.js"></script>

    <?php // $_SESSION['Mensaje'] =""; ?>
</body>

</html>