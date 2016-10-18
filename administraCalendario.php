<?php
// Antes de nada inicio una nueva sesión o reanudo la existente
session_start();

// Insertamos la clase para utilizar la base de datos
require_once '/./clases/operacionesBD.php';
// Inserto la clase para mostra datos
require_once '/./clases/mostrar.php';


// Establecemos por defecto la zona horaria
date_default_timezone_set('Europe/Berlin');


// Comprobamos que se envía el año para consultar
if(isset($_SESSION['añoConsulta']))
    $añoConsulta = $_SESSION['añoConsulta'];
else
    $añoConsulta = "";

// Comprobamos que el usuario está identificado y tiene el permiso adecuado
if (!isset($_SESSION['usuario']) && $_SESSION['rolUsuario'] != 0) {    
    // Borramos los datos de la sesión
    session_unset();
    // Redirigimos a la pantalla inicial
    header("Location: index.php");
}

// Comprobamos si queremos grabar los festivos
if(isset($_POST['botonGrabar'])) {
    // Definimos la lista de festivos
    $listaFestivos = array();
    // Variable de control
    $errores = FALSE;
    // Limpiamos la variable de errores
    unset($_SESSION['errores']);
    
    // Recorremos todos datos enviados
    foreach ($_POST as $nombre => $valor) {
        // Recorremos todos los $_POST excepto el boton de grabar.
        if($nombre != "botonGrabar"){
            // Separamos la fecha recuperada
            $porciones = explode(" / ", $valor);
            // Comprobamos que la fecha introducida es correcta
            if (!checkdate($porciones[1], $porciones[0], $porciones[2])) { 
                $_SESSION['errores'][] = "Error en:";
                $_SESSION['errores'][] = $valor;
                $errores = TRUE;
            }
            else
                $listaFestivos[] = $porciones[2] . "-" . $porciones[1] . "-" . $porciones[0];
        }
    }
    
    // Si no tenemos errores guardamos los días inhabiles
    if($errores == FALSE) {
        operacionesBD::guardarNuevosFestivos($añoConsulta, $listaFestivos);
    }
    
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
    
    <body onload="ocultarZonaTrabajando()">
        
        <!-- Zona de Información -->
        <div id="bloqueInformacion">
            <div id="contenedorBotonInformacion">
                <input type="button" id="botonInformacion" name="botonInformacion" value="" />
                 <div id="contenedorInformacion">
                     <div id="contenedorTextoIndormacion">
                         <?php mostrar::mostrarInformacion()?>                         
                     </div>
                </div>
            </div>           
            <div class="cancelarFlotantes"></div>
        </div>
        
        
        <!-- Zona Trabajando -->
        <div id="zonaTrabajando">
            <div id="contenImgTrabaja">
                <div id="textoZonaTrabajando">¡ RECOPILANDO INFORMACION !</div>
                <img src="imagenes/buscando.png" />
            </div>
        </div>
        
        
        <!-- Zona PROGRAMA -->
        <div id="zonaPrograma">
            
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

                        <form id="formularioFestivos" name="formularioFestivos" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                            <div id="contenFestivos">                        
                            </div>
                            <div id="contenBontonsErrores">
                                <div id="zonaInformacionErrores">                
                                    <?php
                                    // Comprobamos si existen errores para mostrar            
                                    if (isset($_SESSION['errores'])) {
                                        echo "<textarea id='contenedorErrores' cols='16' rows='21' readonly>";
                                        foreach ($_SESSION['errores'] as $miError) {
                                            echo $miError . "\n";
                                        }
                                        echo "</textarea>";
                                    }
                                    ?>                                
                                </div>                           

                                <div id="zonaInformacionResultados">                
                                    <?php
                                    // Comprobamos si existen errores para mostrar            
                                    if (isset($_SESSION['incidencias'])) {
                                        echo "<textarea id='contenedorIncidencias' cols='16' rows='21' readonly>";
                                        foreach ($_SESSION['incidencias'] as $miIncidencia) {
                                            echo $miIncidencia . "\n";
                                        }
                                        echo "</textarea>";
                                    }
                                    ?>                                
                                </div> 


                                <div class="zonaBotones">
                                    <input type="submit" id="botonGrabar" class="botonMenu" name="botonGrabar" value="GRABAR" onclick="mostrarZonaTrabajando()" />
                                </div>
                                <div class="zonaBotones">
                                    <input type="submit" id="botonCancelar" class="botonMenu" name="botonCancelar" value="Cancelar" onclick="mostrarZonaTrabajando()" />
                                </div>
                            </div>                    
                        </form>

                        <div class="cancelarFlotantes"></div>
                    </fieldset>

                </div>
                <div class="cancelarFlotantes"></div>
            </div>

        </div>

        <?php
        // Cargamos los días INHABILES
        $diasInhabiles = operacionesBD::encontrarFestivos($añoConsulta);
        // Pasamos los días inhabiles para marcar con color ROJO
        echo "<script>mostrarDiasInhabiles(".json_encode($diasInhabiles).");</script>";        
        // Mostramos los días festivos en la página
        echo "<script>mostrarFestivos(".json_encode($diasInhabiles).");</script>";
        
        // Si tenemos errores los mostramos
        if (isset($_SESSION['errores'])) {
            echo "<script>mostrarErroresGrabar();</script>";
            // Borramos la variable errores una vez enseñados
            unset($_SESSION['errores']);
        }
        
        // Si tenemos incidencias las mostramos
        if (isset($_SESSION['incidencias'])) {
            echo "<script>mostrarIncidencias();</script>";
            // Borramos la variable errores una vez enseñados
            unset($_SESSION['incidencias']);
        }
        
        ?>

    </body>
</html>
