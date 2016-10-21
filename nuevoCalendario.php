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
if (isset($_SESSION['usuario']) && $_SESSION['rolUsuario'] == 0) {
    // Recuperamos el último año definido
    $ultimoAñoDefinido = operacionesBD::ultimoAñoDefinidos();
    if($ultimoAñoDefinido != "") {
        $_SESSION['nuevoAño'] = ((int)$ultimoAñoDefinido) + 1;
    }
        
}else {
    // Borramos los datos de la sesión
    session_unset();
    // Redirigimos a la pantalla inicial
    header("Location: index.php");
}


// Comprobamos si queremos crear un nuevo año
if(isset($_POST['botonCrearAñoNuevo'])) {
    /* Realizamos la comprobación de que el año mostrado es realmente
     * el último de la base de datos
     */
    $ultimoAñoDefinido = operacionesBD::ultimoAñoDefinidos();
    if($ultimoAñoDefinido == ($_SESSION['nuevoAño']-1)){
        //Insertamos el nuevo año como definido
        if(operacionesBD::insertarAñoNuevo($_SESSION['nuevoAño'])) {
            
            // Insertado el nuevo año (vacio, sin festivos) cargamos los nuevos valores            
            $_SESSION['añosDefinidos'] = serialize(operacionesBD::listarAñosDefinidos());
            
            // Cargamos las opciones seleccionadas
            $opciones = array();
            // Comprobamos si queremos cargar los festivos generales
            if(isset($_POST['festivosGenerales']))                
                $opciones['festivosGenerales'] = TRUE;
            else
                $opciones['festivosGenerales'] = FALSE;
            
            // Comprobamos si queremos cargar los sábados
            if(isset($_POST['festivoSabado']))
                $opciones['festivoSabado'] = TRUE;
            else
                $opciones['festivoSabado'] = FALSE;
            
            // Comprobamos si queremos cargar los domingos
            if(isset($_POST['festivoDomingo']))
                $opciones['festivoDomingo'] = TRUE;
            else
                $opciones['festivoDomingo'] = FALSE;
                        
            // Insertamos los festivos en el calendario
            if(operacionesBD::insertarFestivos($_SESSION['nuevoAño'], $opciones)) {
                $_SESSION['errores'] = "Festivos creados CORRECTAMENTE!";
            } 
        }
        else {
            $_SESSION['errores'] = "Error: No se pudo guardar como definido.";
        }
    }
    else {
        $_SESSION['errores'] = "Error: El año nuevo no es consecutivo.";
    }
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
                
                <div id="bloqueAñoNuevo">
                    <input type="text" id="textoAñoNuevo" name="textoResultadoFinal" value="<?php if (isset($_SESSION['nuevoAño'])) echo $_SESSION['nuevoAño']; ?>" readonly />                    
                </div>               
            </fieldset>

            <!-- Zona para mostrar Errores -->
            <div id="zonaInformacionErrores">                
                <input type="text" id="textoInformacionErrores" value="" readonly />
            </div>

            <?php
            // Comprobamos si existen errores para mostrar            
            if (isset($_SESSION['errores'])) {
                // Enseñamos la zona para mostrar lo errores
                echo "<script>mostrarInformacionErrores(" . json_encode($_SESSION['errores']) . ");</script>";
                // Mostrados los errores los borramos
                unset($_SESSION['errores']);
            }
            ?>

            <div id="zonaOpcionesAñoNuevo">

                <form id="formularioAñoNuevo" name="formularioAñoNuevo" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" >
                    
                    <fieldset id="zonaOpciones">
                        <legend class="textoMenu">Opciones</legend>

                        <div class="bloqueSelecOpciones">                            
                            <input type="checkbox" id="festivosGenerales" class="selecOpcion" name="festivosGenerales" checked />
                            <div class="textoBloqueOpciones">Festivos Generales.</div>
                            <div class="cancelarFlotantes"></div>
                        </div>                        
                        <div class="cancelarFlotantes"></div>
                        <div class="bloqueSelecOpciones">                            
                            <input type="checkbox" id="festivoSabados" class="selecOpcion" name="festivoSabado" checked />
                            <div class="textoBloqueOpciones">Sábados festivos.</div>
                            <div class="cancelarFlotantes"></div>
                        </div>                        
                        <div class="cancelarFlotantes"></div>
                        <div class="bloqueSelecOpciones">                            
                            <input type="checkbox" id="festivoDomigo" class="selecOpcion" name="festivoDomingo" checked />
                            <div class="textoBloqueOpciones">Domingos festivos.</div>
                            <div class="cancelarFlotantes"></div>
                        </div>                        

                    </fieldset>

                    
                    <!-- Zona de BOTONES -->                    
                    <fieldset id="zonaAdministrarAñoNuevo">                        
                        <div class = "zonaAdministrar">
                            <input type = "submit" id = "botonCrearAñoNuevo" class="botonMenu" name = "botonCrearAñoNuevo" value = "Crear Año" onclick="mostrarZonaTrabajando()" title = "Crea un nuevo año con festivos"/>
                        </div>
                        <div class = "zonaAdministrar">
                            <input type = "button" id = "botonCancelarAño" class="botonMenu" name = "botonCancelarAño" value = "Cerrar" onclick="window.close()" title = "Cancela la creación de un nuevo año"/>
                        </div>
                        <div class="cancelarFlotantes"></div>
                    </fieldset>
                    
                </form>
            </div>


        </div>
    </body>
</html>
