<?php
session_start();

require_once 'funciones/conexion.php';
$MiConexion=ConexionBD(); 

$_SESSION['Mensaje']='';
$_SESSION['Estilo']='warning';
$_SESSION['vEmail_Temporal']="";

if (!empty($_POST['BotonClave'])) {
    require_once 'funciones/validacion_de_datos.php';
    if(Validar_Email($_POST['Email'])===true){
        $_SESSION['vEmail_Temporal']= $_POST['Email'];
        require_once 'funciones/datos_login.php';
        Email_Recuperacion($_POST['Email'], $MiConexion) ;   
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

    <script type="text/javascript" src="https://cdn.emailjs.com/dist/email.min.js"></script>

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
                        <form action="recuperar_clave.php" method="post">
                            <?php if (!empty($_SESSION['Mensaje'])) { ?> 
                                <div class="alert alert-<?php echo $_SESSION['Estilo'] ?> alert-dismissable">
                                    <?php echo $_SESSION['Mensaje']; ?> 
                                </div>
                            <?php }  ?>
                            <fieldset>
                                <div class="form-group text-center">
                                    Ingrese su correo electronico.
                                </div>
                                <div class="form-group">
                                    <input class="form-control" placeholder="E-mail" name="Email" type="email" value="<?php echo !empty($_POST['Email']) ? $_POST['Email'] : ''; ?>">
                                </div>
                                <div class="form-group text-center">
                                    <button type="submit" class="btn btn-default" value="Recuperar_Clave" name="BotonClave" >Recuperar contraseña</button>                                 
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

    <!-- Bootstrap Core JavaScript -->
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="vendor/metisMenu/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="dist/js/sb-admin-2.js"></script>

    <?php  $_SESSION['Mensaje'] =""; ?>
</body>

</html>