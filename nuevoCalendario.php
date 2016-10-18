<?php
// Antes de nada inicio una nueva sesión o reanudo la existente
session_start();

// Insertamos la clase para utilizar la base de datos
require_once '/./clases/operacionesBD.php';
// Inserto la clase para mostra datos
require_once '/./clases/mostrar.php';

// Establecemos por defecto la zona horaria
date_default_timezone_set('Europe/Berlin');

// Comprobamos que el usuario está identificado y tiene un rol adecuado 
if (!isset($_SESSION['usuario']) && $_SESSION['rolUsuario'] != 0) {
    // Borramos los datos de la sesión
    session_unset();
    // Redirigimos a la pantalla inicial
    header("Location: index.php");
}

?>



<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Crea un nuevo calendario</title>
        <!-- Incluimos el archivo de estilos -->        
        <link href="estilos/estiloNuevoCalendario.css" rel="stylesheet" type="text/css">
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
                        <?php mostrar::mostrarInformacion() ?>                         
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

        <!-- Zona de Programa -->
        <div id="zonaPrograma">            

            <fieldset id="zonaAñoNuevo">
                <legend class="textoMenu">Nuevo año</legend>

                <div class="bloqueAñoNuevo">
                    <div id="bloqueZonaResultadoFinal">
                        <div id="textoBloqueResultadoFinal" class="textoBloquesResultado">RESULTADO</div>
                        <input type="text" id="textoResultadoFinal" class="textoResultado" name="textoResultadoFinal" value="<?php if (isset($_SESSION['resultadoCalculo'])) echo $_SESSION['resultadoCalculo']; ?>" readonly />
                    </div>
                </div>               
            </fieldset>

            <div id="zonaInformacionErrores">                
                <input type="text" id="textoInformacionErrores" value="" readonly />
            </div>

            <?php
// Comprobamos si existen errores para mostrar            
            if (isset($_SESSION['errores'])) {
                // Enseñamos la zona para mostrar lo errores
                echo "<script>mostrarInformacionErrores(" . json_encode($_SESSION['errores']) . ");</script>";
            }
            ?>

            <div id="zonaVisualizaCalendario">

                <form id="formularioCalendario" name="formularioCalendario" action="<?php echo $_SERVER['PHP_SELF']; ?>" target="_blank" method="post" >                        

                    <fieldset id="zonaCalendario">
                        <legend class="textoMenu">Calendario</legend>

                        <div class="bloqueCalendario">
                            <select id="calendario" name="calendario" required>
                                <?php
                                $listaCalendarios = unserialize($_SESSION['añosDefinidos']);

                                // Si tenemos años definidos los recorremos todos
                                if (!empty($listaCalendarios)) {
                                    foreach ($listaCalendarios as $calendario) {
                                        echo "<option value='" . $calendario . "'>" . $calendario . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="bloqueCalendario">
                            <input type="submit" id="botonVisualizaCalendario" class="botonMenu" name="botonVisualizaCalendario" value="VIZUALIZAR" title="Muestra el calendario seleccionado con sus festivos"/>
                        </div>
                        <div class="cancelarFlotantes"></div>



                    </fieldset>

                    <?php
// Si tenemos el rol adecuado mostramos la posibilidad de ADMINISTRAR LOS FESTIVOS
                    if ($_SESSION['rolUsuario'] == 0) {
                        echo "<fieldset id='zonaAdministrarCalendario'>";
                        echo "<legend class='textoMenu'>Administrar</legend>";
                        echo "<div class = 'zonaAdministrar'>";
                        echo "<input type = 'submit' id = 'botonAdministraCalendario' class='botonMenu' name = 'botonAdministraCalendario' value = 'Festivos' title = 'Administra los festivos del calendario seleccionado'/>";
                        echo "</div>";
                        echo "<div class = 'zonaAdministrar'>";
                        echo "<input type = 'submit' id = 'botonAñadirAño' class='botonMenu' name = 'botonAñadirAño' value = 'Nuevo año' title = 'Añade un nuevo año al calendario'/>";
                        echo "</div>";
                        echo "<div class='cancelarFlotantes'></div>";
                        echo "</fieldset>";
                    }
                    ?>

                </form>
            </div>


        </div>
    </body>
</html>
