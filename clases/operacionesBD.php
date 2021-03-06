<?php

// Insertamos la clase "Usuario"
require_once 'Usuario.php'; // Especifico la ruta absoluta

class operacionesBD {
    
    // Variables de usuario de la base de datos
    static protected $usuario = "reg.negreira";
    static protected $contrasena = "abc123.";
    
    
    /**
     * Usamos consultas preparadas para limpiar datos introducidos por el usuario y "evitar" ataques SQL injection
     * La consulta debe ser de la forma:
     * --Consulta select--
     * $sql             = SELECT * FROM tabla WHERE p1=? AND p2=?
     * $arrayParametros = array($valor1, $valor2)
     * $tipo            = 'SELECT'
     * --Consulta acción--
     * $sql             = INSERT INTO tabla VALUES (?, ?)
     * $arrayParametros = array($valor1, $valor2)
     * $tipo            = 'ACCION'
     * 
     * @param string $sql Cadena con la consulta en el formato indicado
     * @param array $arrayParametros Array con los parámetros que se le pasarán a la consulta
     * @param string $tipo Puede valer 'ACCION' o 'SELECT' según sea el tipo de la consulta
     * @return string|int El resultado de la consulta, en caso de $tipo = 'SELECT' será un array con todas las filas, en el caso $tipo = 'ACCION' será el número de filas afectadas
     */
    protected static function consultaPreparada($sql, $arrayParametros, $tipo, $baseDatos) {        
        
        $opc = array(
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", // Codificación de caracteres
            PDO::MYSQL_ATTR_FOUND_ROWS   => TRUE // Dice las filas que fueron encontradas aunque no sean modificadas
                );
        $dsn = "mysql:host=localhost;dbname=" . $baseDatos;
        
        try {
            $bbdd = new PDO($dsn, self::$usuario, self::$contrasena, $opc);
        } catch (PDOException $e) {
            die ("<h1>ERROR</h1><p>".$e->getMessage()."</p>");
        }

        $consulta = $bbdd->prepare($sql);
        if ( $consulta->execute($arrayParametros) ) { // Se ejecutó
            if ( $tipo === 'ACCION' ) {// Según el $tipo escogido, devolvemos una cosa u otra
                $resultadoConsulta = $consulta->rowCount();
            } elseif ( $tipo == 'SELECT' ) {
                $resultadoConsulta = $consulta->fetchAll();
            } else {
                die ("<h1>No puedes llamar a esta función con ese $tipo</h1>");
            }
        } else {
            die ("<h1>ERROR</h1><p>Error al llamar a la consulta:".$sql."</p>");
        }
        return $resultadoConsulta;
    }
    
    
    /*
     * Función para ejecutar consultas en la base de datos
     */
    protected static function ejecutaConsulta($sql, $baseDatos) {
        // Declaro las variables                
        $resultado = null;

        $opc = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");
        $dsn = "mysql:host=localhost;dbname=" . $baseDatos;

        // Capturo los posibles errores en la conexión
        try {
            $dwes = new PDO($dsn, self::$usuario, self::$contrasena, $opc);
            if (isset($dwes))
                $resultado = $dwes->query($sql);
            return $resultado;
        } catch (PDOException $error) {
            return $resultado;
        }
    }
    
        
    /*
     * Función para LISTAR USUARIOS
     */    
    public static function listarUsuariosActivos() {

        // Comando para la consulta
        $sql = "SELECT id, nombre, rol FROM usuarios WHERE activo = '1' ORDER BY nombre";

        // Ejecuto la consulta
        $resultado = self::ejecutaConsulta($sql, "negreira");

        // Variable que contendrá un array de objetos "Usuario"
        $listaUsuarios = array();

        // Compruebo el resultado
        if (isset($resultado)) {
            // Añadimos un elemento por cada usuario obtenido
            $row = $resultado->fetch();
            while ($row != null) {
                $listaUsuarios[] = new Usuario($row);
                $row = $resultado->fetch();
            }
        }
        // Retorno un array de objetos de la clase Usuario
        return $listaUsuarios;
    }
    
    
    /*
     * Función para verificar usuarios en la base de datos
     */
    public static function verificaUsuario($nombre, $contrasena) {
        // Defino la variable
        $verificado = false;

        /* Introduzco un filtro de saneamiento para los datos que vamos
         * introducir en la base de datos (evitar ataques xss - cross-site Scripting).
         * (Añade un caracter de escape delante de: ', ", \ y NUL)
         */
        $nombreFiltrado = filter_var($nombre, FILTER_SANITIZE_MAGIC_QUOTES);
        $contrasenaFiltrada = filter_var($contrasena, FILTER_SANITIZE_MAGIC_QUOTES);

        // Comando para la consulta
        $sql = "SELECT nombre FROM usuarios "
                . "WHERE nombre='$nombreFiltrado' "
                . "AND clave='" . md5($contrasenaFiltrada) . "'";

        // Ejecuto la consulta
        $resultado = self::ejecutaConsulta($sql, "negreira");

        // Compruebo el resultado
        if (isset($resultado)) {
            $fila = $resultado->fetch();
            if ($fila !== false)
                $verificado = true;
        }
        return $verificado;
    }
    
    
    // Función para encontrar festivos de un AÑO DADO
    public static function encontrarFestivos($año) {        
        
        // Creamos el primer dia del año
        $primerDia = date_create(date($año."-"."1"."-"."1"));
        $formatoPrimerDia = date_format($primerDia, "Y-m-d");
        
        // Creamos el último dia del año
        $ultimoDia = date_create(date($año."-"."12"."-"."31"));
        $formatoUltimoDia = date_format($ultimoDia, "Y-m-d");
        
        // Comando para la consulta
        $sql = "SELECT festivo FROM festivosregistro WHERE festivo >= '".$formatoPrimerDia."' AND festivo <= '".$formatoUltimoDia."'";

        // Ejecuto la consulta
        $resultado = self::ejecutaConsulta($sql, "negreira");

        // Variable que contendrá un array con los días Festivos
        $diasFestivos = array();
        
        // Compruebo el resultado
        if (isset($resultado)) {
            // Añadimos un elemento por cada festivo obtenido
            $row = $resultado->fetch();
            while ($row != null) {
                $fechaEncontrada = date_create(date($row[0]));
                // Lo guardamos con este formato (Año con 4 dígitos, mes 1-12 y día 1-31)
                $diasFestivos[] = date_format($fechaEncontrada, "Y-n-j");
                $row = $resultado->fetch();
            }
        }
        // Retorno un array de objetos de la clase Usuario        
        return $diasFestivos;        
    }
    
    
    // Función para CALCULAR LA FECHA FINAL DANDO LOS DÍAS HÁBILES
    public static function fechaFinal($fechaInicial, $diasHabiles) {
        
        // Transformamos la variable
        $diasHabiles = (int) $diasHabiles;
        
        // Variables de Control
        $diasHabilesIntervalo = 0;
        $signo = 1;
        $fechaControlInicial = date_create($fechaInicial);
        $fechaControlFinal = date_create($fechaInicial);
        
        // Reviso si los días hábiles introducido es negativo
        if($diasHabiles < 0)
            $signo = -1;
        
        // Establecemos los valores iniciales
        date_add($fechaControlInicial, date_interval_create_from_date_string($signo." days"));
        date_add($fechaControlFinal, date_interval_create_from_date_string($diasHabiles." days"));
        
        do {
            // Formato de busqueda - Ojo la base de datos trabaja con este formato
            $formatoFechaInicial = date_format($fechaControlInicial, "Y-m-d");
            $formatoFechaFinal = date_format($fechaControlFinal, "Y-m-d");
            
            // Construimos la orden de busqueda en la base de datos
            $sql = "SELECT COUNT(*) FROM festivosregistro WHERE festivo BETWEEN '".$formatoFechaInicial."' AND '".$formatoFechaFinal."'";
            
            // Para el caso de que la búsqueda se negativa
            if($diasHabiles < 0)
                $sql = "SELECT COUNT(*) FROM festivosregistro WHERE festivo BETWEEN '".$formatoFechaFinal."' AND '".$formatoFechaInicial."'";
            
        // Ejecuto la consulta
        $resultado = self::ejecutaConsulta($sql, "negreira");
        
        // Añadimos un elemento a una fila
        $row = $resultado->fetch();        
        $diasHabilesIntervalo = $row[0];
        
        // Si procede sumamos los días hábiles encontrados en el intervalo
        if($diasHabilesIntervalo > 0) {
            // Establecemos los nuevos valores iniciales
            $fechaControlInicial = date_create($formatoFechaFinal);
            date_add($fechaControlInicial, date_interval_create_from_date_string($signo." days"));
            date_add($fechaControlFinal, date_interval_create_from_date_string($signo*$diasHabilesIntervalo." days"));            
        }                        
        } while ($diasHabilesIntervalo > 0);
        
        /*
         * Comprobamos que la fecha Resultante está dentro de los años definidos
         */
        $encontrado = FALSE;
        
        if(isset($_SESSION['añosDefinidos'])) {
            $añoResultado = date_format($fechaControlFinal, "Y");            
            $listaCalendarios = unserialize($_SESSION['añosDefinidos']);
            foreach ($listaCalendarios as $calendario) {
                if($calendario == $añoResultado)
                    $encontrado = TRUE;               
            }
        }
        
        if($encontrado == TRUE)
            return date_format($fechaControlFinal, "d/m/Y");
        else 
            return "Indefinido";
    }
    
        
    // Función para CALCULAR LA FECHA FINAL DANDO LOS DÍAS HÁBILES
    public static function listarAñosDefinidos() {
        // Comando para la consulta
        $sql = "SELECT anos FROM anosdefinidos ORDER BY anos DESC";

        // Ejecuto la consulta
        $resultado = self::ejecutaConsulta($sql, "negreira");

        // Variable que contendrá un array de objetos "Diarios"
        $añosDefinidos = array();
        
        // Compruebo el resultado
        if (isset($resultado)) {
            // Añadimos un elemento por cada diario obtenido
            $row = $resultado->fetch();
            while ($row != null) {
                $añosDefinidos[] =$row[0];
                $row = $resultado->fetch();
            }
        }
        // Retorno un array de objetos de la clase Usuario
        return $añosDefinidos;
    }
    
    
    // Función para encontrar festivos de un AÑO DADO
    public static function guardarNuevosFestivos($año, $listaFestivos) {        
        
        if ($año != "" && !empty($listaFestivos)) {
            // Primero borramos los festivos definidos anteriormente
            // Creamos el primer dia del año
            $primerDia = date_create(date($año . "-" . "1" . "-" . "1"));
            $formatoPrimerDia = date_format($primerDia, "Y-m-d");

            // Creamos el último dia del año
            $ultimoDia = date_create(date($año . "-" . "12" . "-" . "31"));
            $formatoUltimoDia = date_format($ultimoDia, "Y-m-d");

            // Comando para la consulta
            $sql = "DELETE FROM festivosregistro WHERE festivo >= '" . $formatoPrimerDia . "' AND festivo <= '" . $formatoUltimoDia . "'";

            // Ejecuto la consulta
            $resultado = self::ejecutaConsulta($sql, "negreira");            

            // Compruebo el resultado
            if (isset($resultado)) {
               $_SESSION['incidenciasGrabar'][] = "Antiguos Borrados!";
               
               // Recorro todos los festivos que queremos añadir
               foreach ($listaFestivos as $miFestivo) {
                   // Creo el formato adecuado de fecha para cargar en la base de datos
                   $festivo = date_create(date($miFestivo));
                   $formatoFestivo = date_format($festivo, "Y-m-d");
                   
                   // Creo la consulta preparada
                   $sql = "INSERT INTO `festivosregistro` (`festivo`) VALUES (?)";                   
                   $arrayParametros = array($formatoFestivo);
                   
                   $resultado = self::consultaPreparada($sql, $arrayParametros, 'ACCION', 'negreira');
                   
                   // Compruebo el resultado
                   if ($resultado === 1 || $resultado === "1") {
                        // Se crea nueva incidencia informando sobre la insercción del festivo
                        $_SESSION['incidenciasGrabar'][] = "Creado:";
                        $_SESSION['incidenciasGrabar'][] = date_format($festivo, "d/m/Y");
                        
                    } else {
                        // Hubo un error al añadir el nuevo diario a la lista de diarios
                        // Inicializamos la variable errores
                        $_SESSION['errores'][] = "Error:";
                        $_SESSION['errores'][] = date_format($festivo, "d/m/Y");
                    }
                }
            }
            else
                $_SESSION['errores'][] = "Error al borrar los festivos anteriores!";
        } else {
            $_SESSION['errores'][] = "Año no definido:";
            $_SESSION['errores'][] = $año;
        }
    }
    
    
    // Función que devuelve el ULTIMO AÑO DEFINIDO
    public static function ultimoAñoDefinidos() {
        
        // Comando para la consulta
        $sql = "SELECT MAX(anos) FROM anosdefinidos";

        // Ejecuto la consulta
        $resultado = self::ejecutaConsulta($sql, "negreira");

        // Variable que contendrá el último año definido
        $ultimoAño = "";
        
        // Compruebo el resultado
        if (isset($resultado)) {
            // Recupero el valor obtenido
            $row = $resultado->fetch();
            $ultimoAño = $row[0];
        }
        // Retorno un array de objetos de la clase Usuario
        return $ultimoAño;
    }
    
    // Función que devuelve el ULTIMO AÑO DEFINIDO
    public static function insertarAñoNuevo($añoNuevo) {
        // Variable que devolverá la función
        $resultadoOperacion = TRUE;
        
        // Compruebo si esta identificado y tiene permiso.
        if (isset($_SESSION['usuario']) && $_SESSION['rolUsuario'] == 0) {
            // Creo la consulta preparada
            $sql = "INSERT INTO `anosdefinidos` (`anos`) VALUES (?)";
            $arrayParametros = array($añoNuevo);

            $resultado = self::consultaPreparada($sql, $arrayParametros, 'ACCION', 'negreira');

            // Compruebo el resultado
            if ($resultado === 1 || $resultado === "1") {
                $resultadoOperacion = TRUE;
            } else {
                $resultadoOperacion = FALSE;
            }
        }
        // Devolvemos el resultado
        return $resultadoOperacion;
    }
    
    
     // Función que INSERTA los festivos en el nuevo año creado
    public static function insertarFestivos($añoNuevo, $opciones) {
        // Variable que devolverá la función
        $resultadoOperacion = TRUE;
        $festivosGenerales = array();
        
        
        // Compruebo si esta identificado y tiene permiso.
        if (isset($_SESSION['usuario']) && $_SESSION['rolUsuario'] == 0) {

            // Compruebo si se quieren introducir los festivos generales
            if ($opciones['festivosGenerales']) {
                // Recupero los festivos generales introducidos en la Base de Datos
                // Comando para la consulta
                $sql = "SELECT mes, dia FROM festivosgenerales ORDER BY mes DESC";

                // Ejecuto la consulta
                $resultado = self::ejecutaConsulta($sql, "negreira");

                // Compruebo el resultado
                if (isset($resultado)) {
                    // Añadimos un elemento por cada festivo obtenido
                    $row = $resultado->fetch();
                    while ($row != null) {
                        $festivosGenerales[] = array("mes" => $row[0], "dia" => $row[1]);
                        $row = $resultado->fetch();
                    }
                }
            }
            
            
            /*
             * INSERTAMOS LOS FESTIVOS SEGÚN LAS OPCIONES SELECCIONADAS
             */
            
            // Variables iniciales                        
            $fechaFinal = date_create(date($añoNuevo . "-12-31"));
            $fechaActual = date_create(date($añoNuevo . "-1-1"));
            
            for ($fechaActual; $fechaActual <= $fechaFinal; date_add($fechaActual, date_interval_create_from_date_string("+1 days"))) {
                
                // Variables
                $mesActual = date_format($fechaActual, "n"); // Sin ceros iniciales
                $diaActual = date_format($fechaActual, "j"); // Sin ceros iniciales
                $diaSemanaActual = date_format($fechaActual, "N"); // Sin ceros iniciales
                
                // Creo la consulta preparada
                $formatoFestivo = date_format($fechaActual, "Y-m-d");
                $sql = "INSERT INTO `festivosregistro` (`festivo`) VALUES (?)";
                $arrayParametros = array($formatoFestivo);

                // Compruebo si la fecha actual es sábado o domingo
                if($diaSemanaActual >= 6) {                   

                    // Compruebo si queremos introducir los SÁBADOS como festivos
                    if($opciones['festivoSabado'] && $diaSemanaActual == 6) {
                        // Guardo el festivo
                        $resultado = self::consultaPreparada($sql, $arrayParametros, 'ACCION', 'negreira');
                        // Compruebo el resultado
                        if ($resultado === 1 || $resultado === "1") {
                            // Se guardo el festivo correctamente, pasamos al siguiente dia
                            continue;
                            
                        } else {
                            // Hubo un error al añadir el festivo, cancelamos y mostramos error                            
                            $_SESSION['errores'] = "Error: Insercción rota en la fecha:".  date_format($fechaActual, "d/m/Y");
                            $resultadoOperacion = FALSE;
                            break;
                        }
                    }
                    
                    // Compruebo si queremos introducir los DOMINGOS como festivos
                    if($opciones['festivoDomingo'] && $diaSemanaActual == 7) {
                        // Guardo el festivo
                        $resultado = self::consultaPreparada($sql, $arrayParametros, 'ACCION', 'negreira');
                        // Compruebo el resultado
                        if ($resultado === 1 || $resultado === "1") {
                            // Se guardo el festivo correctamente, pasamos al siguiente dia
                            continue;
                            
                        } else {
                            // Hubo un error al añadir el festivo, cancelamos y mostramos error                            
                            $_SESSION['errores'] = "Error: Insercción rota en la fecha:".  date_format($fechaActual, "d/m/Y");
                            $resultadoOperacion = FALSE;
                            break;
                        }
                    }                    
                }
                
                // Compruebo si se quieren introducir los festivos generales
                if ($opciones['festivosGenerales'] && !empty($festivosGenerales)) {
                    foreach ($festivosGenerales as $value) {
                        if($value['mes'] == $mesActual && $value['dia'] == $diaActual) {
                            // Guardo el festivo
                            $resultado = self::consultaPreparada($sql, $arrayParametros, 'ACCION', 'negreira');
                            // Compruebo el resultado
                            if ($resultado === 1 || $resultado === "1") {
                                // Se guardo el festivo correctamente, pasamos al siguiente dia
                                continue;
                            } else {
                                // Hubo un error al añadir el festivo, cancelamos y mostramos error                            
                                $_SESSION['errores'] = "Error: Insercción rota en la fecha:" . date_format($fechaActual, "d/m/Y");
                                $resultadoOperacion = FALSE;
                                break;
                            }
                        }
                    }
                }
                
            } // Fin bucle recorre todo el año
            
        } else {
            $resultadoOperacion = FALSE;
            $_SESSION['errores'] = "Error: No tienes permisos necesarios!";
        }
        
        return $resultadoOperacion;
    }

}
?>