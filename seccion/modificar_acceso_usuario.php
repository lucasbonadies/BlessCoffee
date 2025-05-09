<?php
session_start();

if(empty($_SESSION['id_usuario']) || empty($_SESSION['id_nivel'])){
    header('Location: cerrarsesion.php');
    exit;
}

require_once 'funciones/conexion.php';
$MiConexion=ConexionBD(); 

require_once 'funciones/select_usuarios.php';

if($_SESSION['id_nivel']==1 || $_SESSION['id_nivel']==5  ){
    $ID_USUARIO_UPDATE = filter_var($_GET['ID_USUARIO'], FILTER_VALIDATE_INT); //limpio valores ingresados por GET Y VERIFICO QUE SOLO SEAN ENTEROS
    $ID_ESTADO_UPDATE = filter_var($_GET['ID_ESTADO'], FILTER_VALIDATE_INT);
    if (Modificar_Acceso_Usuario($ID_USUARIO_UPDATE, $ID_ESTADO_UPDATE, $MiConexion ) != false){
            $_SESSION['Mensaje']='Se ha modificado el acceso del usuario. <br /> ';
            $_SESSION['Estilo']='success';
            header('Location: mis_datos.php');
            exit;
    }else{
        $_SESSION['Mensaje']='No se ha modificado el acceso. <br /> ';
        $_SESSION['Estilo']='danger';
        header('Location: mis_datos.php');
        exit;
    }
}else if($_SESSION['id_nivel']==1){
    if (Modificar_Acceso_Usuario($_SESSION['id_usuario'], 2, $MiConexion) != false){
        header('Location: cerrarsesion.php');
        exit;
    }else{
        $_SESSION['Mensaje']='No se ha modificado el acceso. <br /> ';
        $_SESSION['Estilo']='danger';
        header('Location: mis_datos.php');
        exit; 
    }
}else{
    header('Location: index.php');
    exit;
}
?>