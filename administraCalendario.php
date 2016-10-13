<?php
// Antes de nada inicio una nueva sesión o reanudo la existente
session_start();

// Insertamos la clase para utilizar la base de datos
require_once '/./clases/operacionesBD.php';
// Inserto la clase para mostra datos
require_once '/./clases/mostrar.php';

// Comprobamos que se envía el año para consultar
if(isset($_SESSION['añoConsulta']))
    $añoConsulta = $_SESSION['añoConsulta'];
else
    $añoConsulta = "";

// Comprobamos que el usuario está identificado
if (!isset($_SESSION['usuario'])) {    
    // Borramos los datos de la sesión
    session_unset();
    // Redirigimos a la pantalla inicial
    header("Location: index.php");
}

?>


<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <title>Administra Calendario</title>
        <!-- Incluimos el archivo de estilos -->        
        <link href="estilos/estiloAdministraCalendario.css" rel="stylesheet" type="text/css">
        <!-- Incluimos el archivo de operaciones con javaScript -->        
        <script type="text/javascript" src="clases/operacionesJS.js"></script>        
    </head>
    
    <body>
        
        <div id="zonaTitulo">Administrar año: <?php echo $añoConsulta; ?></div>
        <div id="zonaAdministrar">   
            <div id="zonaCalendario">
                <?php
                    // Creamos el nuevo calendario
                    mostrar::crearCalendarioAdministraFestivos($añoConsulta);
                ?>   
            </div>
            <div id="zonaFestivos">
                <fieldset>
                    <legend class="textoMenu">Festivos definidos:</legend>
                </fieldset>
                
            </div>
            <div class="cancelarFlotantes"></div>
        </div>

        <?php
        // Cargamos los días INHABILES
        $diasInhabiles = operacionesBD::encontrarFestivos($añoConsulta);
        // Pasamos los días inhabiles para marcar con color ROJO
        echo "<script>mostrarDiasInhabiles(".json_encode($diasInhabiles).");</script>";        
        ?>

    </body>
</html>
