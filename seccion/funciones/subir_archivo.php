<?php 

function SubirArchivo($nombreArchivo) {
    
    //https://www.php.net/manual/es/function.pathinfo.php
    $TamanioMaximo=5000000;  //expresados en bytes..  5000000 --> 5mb 
    //$DatoArchivo = pathinfo($_FILES['MiArchivo']['name']);
    $_SESSION['Mensaje']='';
    $_SESSION['Estilo']='warning';

    if (!empty($_FILES[$nombreArchivo]['name'])) { //si se sube algun archivo, opero con el
        $DatoArchivo = pathinfo($_FILES[$nombreArchivo]['name']);
        $CarpetaAlojamiento = 'dist/img/'.$nombreArchivo;

            //agregar una restriccion de tamaño de archivos
        if($_FILES[$nombreArchivo]['size']>$TamanioMaximo){
            //se asegura q el tamanio maximo sea el especificado
            $_SESSION['Mensaje'] .= 'Tu imagen supera el tamaño permitido';
            return false;

        }else {
            if (!in_array(strtolower($DatoArchivo['extension']), ['png', 'jpg', 'jpeg', 'bmp', 'webp'])) {
                //requiere que el archivo a subir sea una imagen
                $_SESSION['Mensaje'] .= 'El archivo debe ser una imagen.';
                return false;
            }else {
                //verificacion si el archivo se subio al servidor en forma correcta
                if(is_uploaded_file($_FILES[$nombreArchivo]['tmp_name'])) {
                    //si el directorio no existe, lo creamos
                    if (!is_dir($CarpetaAlojamiento)) {
                    mkdir($CarpetaAlojamiento); //creo la carpeta
                    chmod($CarpetaAlojamiento, 0777); //asigno permisos para escribir
                    }
                    //en este caso, muevo, alojo el archivo en el servidor
                    move_uploaded_file($_FILES[$nombreArchivo]['tmp_name'], $CarpetaAlojamiento.'/'.$_FILES[$nombreArchivo]['name']);
                    return true;

                } else {
                    $_SESSION['Mensaje'] .=  'Problemas al intentar subir el archivo <strong>'.$_FILES[$nombreArchivo]['name'].'</strong>';
                    return false;
                }
            }
        }
    }else {
        //si no se sube ningun archivo, no hay problema, se puede seguir
        return true;
    }
}


?>