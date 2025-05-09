<?php
session_start();

//si tengo vacio mi elemento de sesion me redirige a cerrarsesion y luego al login
if(empty($_SESSION['id_usuario']) || empty($_SESSION['id_nivel'])){
    header('Location: cerrarsesion.php');
    exit;
}

require_once 'funciones/conexion.php';
$MiConexion = ConexionBD();

require_once 'funciones/select_funciones.php';
$ListadoArticulos = Listar_Articulos($MiConexion);
$ListadoCategorias = Listar_Tipo_Articulos($MiConexion); 

require_once 'header.inc.php'; 
?>
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<style>
/************************* Estilos específicos para impresión ***********************/
   @media print {
    .container {
        margin: 0;
        padding: 0;
        width: 100%;
    }
    .card {
        break-inside: avoid; /* Evita que las tarjetas se rompan */
        page-break-inside: avoid; /* No permite el salto de página dentro de las tarjetas */
        margin-bottom: 1rem;
    }
    .row {
        display: flex;
        flex-wrap: wrap;
    }
    .col-md-4 {
        width: 33.33%; /* Ajusta el ancho de las columnas en impresión */
        break-inside: avoid;
        page-break-inside: avoid;
    }
  }
</style>
</head>

<body>

    <div class="container mt-5">
        <div class="text-center">
            <img class="img-fluid" src='dist/img/LoginChef.png' />
            <h1 class="text-center" style="font-size: 42px;">----- Nuestra Carta -----</h1>
        </div>
        </br>
        <?php
        foreach ($ListadoCategorias as $categoria){ 
         $ListadoPorCategorias= Separar_Articulos_Por_Categoria($ListadoArticulos, $categoria['ID_TIPO']);
        ?>
        <h2 class="text-left" style="font-size: 36px;"><u><?= $categoria['NOMBRE'].'s' ?></u></h2>
        <div class="row">
            <?php foreach ($ListadoPorCategorias as $articulo){ ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="dist/img/imagen_menu/<?php echo htmlspecialchars($articulo['IMAGEN']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($articulo['NOMBRE']); ?>">
                        <div class="card-body">
                            <h5 class="card-title" style="font-size: 24px;"><?php echo htmlspecialchars($articulo['NOMBRE']); ?></h5>
                            <p class="card-text" style="font-size: 24px;"><?php echo htmlspecialchars($articulo['DESCRIPCION']); ?></p>
                            <p class="card-text" style="font-size: 24px;"><strong>Precio: $<?php echo number_format($articulo['PRECIO_UNITARIO'], 2); ?></strong></p>
                        </div>
                    </div>
                </div>
            <?php }; ?>
        </div>
        <?php } ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script> 
        // Ejecuta la impresión sin alterar la estructura
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500); // Retraso para garantizar que se cargue todo
        };
    </script>

<?php require_once 'footer.inc.php'; ?>
