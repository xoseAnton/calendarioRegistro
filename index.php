<?php

// Antes de nada inicio una nueva sesión o reanudo la existente
session_start();

// Inserto la clase para mostra datos
require_once '/./clases/mostrar.php';

// Variables generales
$mensaje = "";
$selecUsuario = "";
// Establecemos por defecto la zona horaria
date_default_timezone_set('Europe/Berlin');

// INFORMACIÓN DEL PROYECTO
$_SESSION['InformacionProyecto'] = array();
$_SESSION['InformacionProyecto'][] = array("Realizado por: ", "Jose Antonio Mariño Outeiro");
$_SESSION['InformacionProyecto'][] = array("Para: ", "Registro de la Propiedad de Negreira");
$_SESSION['InformacionProyecto'][] = array("Versión: ", "1.0 - Calendario Registro");
$_SESSION['InformacionProyecto'][] = array("Base de datos: ", "MySQL");
$_SESSION['InformacionProyecto'][] = array ("Servidor web: ", "Microsoft-IIS/8.5");
$_SESSION['InformacionProyecto'][] = array ("Versión de PHP: ", "5.5.36");


// Compruebo que no tenemos cargados los usuarios:
if (!isset($_SESSION['listaUsuarios'])) {
    // Creamos el array de objetos usuarios y lo serializamos
    $_SESSION['listaUsuarios'] = serialize(operacionesBD::listarUsuariosActivos());   
}

// Comprobamos si hemos enviado el formulario con el usuario
if (isset($_POST['entrar'])) {
    if (!empty($_POST['usuario'])) {
        // Guardamos el intento de entrada del usuario para seleccionarlo nuevamente si introduce mal la contraseña
        $selecUsuario = $_POST['usuario'];

        // Verifico el usuario y contraseña en la base de datos
        if (operacionesBD::verificaUsuario($_POST['usuario'], $_POST['contraseña'])) {
            // Verificado el usuario lo introduzco en una variable de la sesión
            $_SESSION['usuario'] = $_POST['usuario'];   
            $_SESSION['rolUsuario'] = Usuario::buscarRol($_SESSION['listaUsuarios'], $_POST['usuario']);
            
            // Definio el nuevo array de incidencias del usuario
            $_SESSION['incidencias'] = array();            
            $_SESSION['incidencias'][] = date("H:i:s")."-> Se conecta el usuario: ".$_POST['usuario'].".";
            
            // Redirijo a la pantalla de procedimientos
            header("Location: calculoDias.php");
        } else {
            $mensaje = "Usuario y contraseña incorrectos!";
        }
    }
    else
        $mensaje = "Deberá seleccionar un Usuario!";
}
?>


<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <link href="estilos/estiloAcceso.css" rel="stylesheet" type="text/css">
        <title>Acceso Diario Paralelo</title>
    </head>
        
    <body>
        
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
        
        <!-- Zona de control de Usuario -->
        <div id="zonaControlUsuario">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <fieldset>
                    <legend><span id="textoAcceso">Acceso - </span><span id="textoCalendario">Calendario</span></legend>
                    
                     <!-- Zona de MENSAJES -->
                    <div>
                        <span class="error"><?php echo $mensaje; ?></span>
                    </div>
                     
                     <!-- Zona de USUARIO -->
                    <div class="campo">
                        <label>Usuario:</label><br/>                         
                        <select id="usuario" name="usuario" required>
                            <?php mostrar::optionUsuarios($selecUsuario); ?>
                        </select><br/>
                    </div>
                     
                      <!-- Zona de CONTRASEÑA -->
                    <div class="campo">
                        <label for="password" >Contraseña:</label><br/>
                        <input type="password" name="contraseña" id="contraseña" required /><br/>
                    </div>
                      
                      <!-- Zona de BOTONES -->
                    <div class="campo">                       
                        <input type="submit" id="botonEntrar" name="entrar" value="Entrar" />                                                                                              
                    </div>
                    
                </fieldset>            
            </form>            
        </div>
    </body>
</html>
