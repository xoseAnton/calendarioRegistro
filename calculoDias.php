<?php
// Antes de nada inicio una nueva sesión o reanudo la existente
session_start();

// Insertamos la clase para utilizar la base de datos
require_once '/./clases/operacionesBD.php';
// Inserto la clase para mostra datos
require_once '/./clases/mostrar.php';

// Establecemos por defecto la zona horaria
date_default_timezone_set('Europe/Berlin');

// Comprobamos que el usuario está identificado
if (!isset($_SESSION['usuario'])) {    
    // Borramos los datos de la sesión
    session_unset();
    // Redirigimos a la pantalla inicial
    header("Location: index.php");
}


// Comprobamos que queremos realizar calculos
if (isset($_POST['botonCalcula'])) {
    
    // Borramos las variables resultado anteriores
    unset($_SESSION['resultadoDespacho']);
    unset($_SESSION['resultadoCaducidad']);
    unset($_SESSION['resultadoCalculo']);
    
    // Variable de control de errores
    $errores = FALSE;
    // Limpiamos la variable de errores
    unset($_SESSION['errores']);    
    
    /*
         *  REALIZAMOS LA COMPROBACIÓN DE LOS DATOS INTRODUCIDOS
         */
        if (empty($_POST['fechaInicial'])) {
            $_SESSION['errores'] = "LA FECHA INICIAL no puede estar vacía.";
            $errores = TRUE;
        } else {
            // Guardamos la fecha introducida
            $_SESSION['fechaInicial'] = $_POST['fechaInicial'];

            // Separamos la fecha introducida
            $fechaIntroducida = explode("-", $_POST['fechaInicial']);
            // Comprobamos que la fecha introducida es correcta                
            if (!checkdate($fechaIntroducida[1], $fechaIntroducida[2], $fechaIntroducida[0])) {
                $_SESSION['errores'] = "LA FECHA INICIAL no es correcta.";
                $errores = TRUE;
            }
        }

        if (isset($_POST['numeroDias'])) {
            // Guardamos el número de días introducido
            $_SESSION['diasIntervalo'] = $_POST['numeroDias'];
            
            if (!is_numeric($_POST['numeroDias'])) {
                $_SESSION['errores'] = "EL DATO DÍAS introducido no es un número.";
                $errores = TRUE;
            }
        } else {
            $_SESSION['errores'] = "EL CAMPO DÍAS no puede estar vacío.";
            $errores = TRUE;
        }
    
    
    // Comprobamos que queremos calcular días hábiles
    if (isset($_POST['diasHabiles'])) {
        
        // Guardamos la opción escogida
        $_SESSION['diasHabiles'] = TRUE;
        
        // Si no tenemos errores calculamos las fechas resultantes
        if(!$errores) {
            $_SESSION['resultadoDespacho'] = operacionesBD::fechaFinal($_POST['fechaInicial'], -15);
            $_SESSION['resultadoCaducidad'] = operacionesBD::fechaFinal($_POST['fechaInicial'], -60);
            $_SESSION['resultadoCalculo'] = operacionesBD::fechaFinal($_POST['fechaInicial'], $_POST['numeroDias']);
        }
        
        
    } else { // Calculo de días naturales
        
        // Guardamos la opción escogida
        $_SESSION['diasHabiles'] = FALSE;
        
        // Si no tenemos errores calculamos la fecha resultante
        if(!$errores) {
            // Calculamos la fecha resultante
            $fechaAnterior = date_create($_POST['fechaInicial']);
            date_add($fechaAnterior, date_interval_create_from_date_string($_POST['numeroDias']." days"));
            $_SESSION['resultadoCalculo'] = date_format($fechaAnterior, "d/m/Y");
        }
        
        
    } // Fin calculo de dias naturales
    
} else {
    /* Si no pulsamos el botón de consulta
     * Seleccionamos el día de hoy
     */
    $_SESSION['fechaInicial'] = date("Y-m-d");
    $_SESSION['diasIntervalo'] = 0;
    $_SESSION['diasHabiles'] = TRUE; // Dias hábiles por defecto.
    
    // Cargamos los años con festivos definidos
    $_SESSION['añosDefinidos'] = serialize(operacionesBD::listarAñosDefinidos());
}


// Comprobamos que queremos VISUALIZAR un calendario
if (isset($_POST['botonVisualizaCalendario'])) {    
    // Guardamos en la variable de sesión el calendario consultado
    $_SESSION['añoConsulta'] = $_POST['calendario'];
    // Redirigimos a la pantalla inicial
    header("Location: calendario.php");
 
}

// Comprobamos que queremos VISUALIZAR un calendario
if (isset($_POST['botonAdministraCalendario']) && $_SESSION['rolUsuario'] == 0) {    
    // Guardamos en la variable de sesión el calendario consultado
    $_SESSION['añoConsulta'] = $_POST['calendario'];
    // Redirigimos a la pantalla inicial
    header("Location: administraCalendario.php");
 
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
        <title>Calcula días hábiles&naturales</title>
        <!-- Incluimos el archivo de estilos -->        
        <link href="estilos/estiloCalculoDias.css" rel="stylesheet" type="text/css">
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
        
        <!-- Zona de Programa -->
        <div id="zonaPrograma">            
            
            <fieldset id="zonaCalculoDias">
                <legend class="textoMenu">Cálculos</legend>
                
                <div class="bloqueCalculo">
                    <form id="formularioCalculoFechas" name="formularioCalculoFechas" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                        <div class="bloqueCalculoZona">
                            <div class="textoBloquesCalculo">Fecha Inicial:</div>
                            <input type="date" id="fechaInicial" name="fechaInicial" value="<?php if(isset($_SESSION['fechaInicial'])) echo $_SESSION['fechaInicial']; ?>" required onclick="limpiarResultadosAnteriores()" />
                        </div>
                        <div class="bloqueCalculoZona">
                            <div class="bloqueParCalculoZona">
                                <div class="textoBloquesCalculo">Días:</div>
                                <input type="number" id="numeroDias" name="numeroDias" value="<?php if(isset($_SESSION['diasIntervalo'])) echo $_SESSION['diasIntervalo']; ?>" required onclick="limpiarResultadosAnteriores()" />
                            </div>
                            <div class="bloqueParCalculoZona">
                                <div class="textoBloquesCalculo">hábiles</div>
                                <input type="checkbox" id="diasHabiles" name="diasHabiles" <?php if($_SESSION['diasHabiles']) echo "checked"; ?> onclick="limpiarResultadosAnteriores()" />
                            </div>
                            <div class="cancelarFlotantes"></div>
                        </div>
                        <div class="bloqueCalculoZona">
                            <input type="submit" id="botonCalcula" class="botonMenu" name="botonCalcula" value="CALCULA" onclick="mostrarZonaTrabajando()" title="Calcula la fecha final según los días introducidos"/>
                        </div>
                    </form>                    
                </div>
                
                <div id="bloqueDerecha" class="bloqueCalculo">
                    <div class="bloqueCalculoZona">
                        <div id="bloqueZonaDespacho">
                            <div class="textoBloquesResultado">-15 días Despacho</div>
                            <input type="text" id="textoDespacho" class="textoResultado" name="textoDespacho" value="<?php if(isset($_SESSION['resultadoDespacho']))echo $_SESSION['resultadoDespacho']; ?>" readonly />
                        </div>
                    </div>
                    
                    <div class="bloqueCalculoZona">
                        <div id="bloqueZonaCaducidad">
                            <div class="textoBloquesResultado">-60 días Caducidad</div>
                            <input type="text" id="textoCaducidad" class="textoResultado" name="textoCaducidad" value="<?php if(isset($_SESSION['resultadoCaducidad']))echo $_SESSION['resultadoCaducidad']; ?>" readonly />
                        </div>
                    </div>
                    
                    <div class="bloqueCalculoZona">
                        <div id="bloqueZonaResultadoFinal">
                            <div id="textoBloqueResultadoFinal" class="textoBloquesResultado">RESULTADO</div>
                            <input type="text" id="textoResultadoFinal" class="textoResultado" name="textoResultadoFinal" value="<?php if(isset($_SESSION['resultadoCalculo']))echo $_SESSION['resultadoCalculo']; ?>" readonly />
                        </div>
                    </div>
                    
                </div>
                
                <div class="cancelarFlotantes"></div>
            </fieldset>
            
            <div id="zonaInformacionErrores">                
                <input type="text" id="textoInformacionErrores" value="" readonly />
            </div>
            
            <?php                
                // Comprobamos si existen errores para mostrar            
                if (isset($_SESSION['errores'])) {
                    // Enseñamos la zona para mostrar lo errores
                    echo "<script>mostrarInformacionErrores(".json_encode($_SESSION['errores']).");</script>";
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
