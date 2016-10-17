<?php

// Insertamos la clase "Usuario"
require_once 'Usuario.php'; // Especifico la ruta absoluta
// Insertamos la clase para utilizar la base de datos
require_once 'operacionesBD.php';


class mostrar {

    // Función para mostrar LA INFORMACIÓN DEL PROGRAMA
    public static function mostrarInformacion() {

        // Si tenemos la información sobre el programa la mostramos
        if (isset($_SESSION['InformacionProyecto'])) {
            foreach ($_SESSION['InformacionProyecto'] as $miInformacion) {
                echo "<p class='textoInformacion'><span class='textoTituloInformacion'>" . $miInformacion[0] . "</span>" . $miInformacion[1] . "</p>";
            }
        } else
            echo "<p class='textoInformacion'><span class='textoTituloInformacion'>Aviso: </span>Información no disponible!</p>";
    }
    
    // Función para mostrar los usuarios en un "select"
    public static function optionUsuarios($selecUsuario) {

        $listaUsuarios = unserialize($_SESSION['listaUsuarios']);                
        $encontrado = FALSE; // Variable que controla el caso que no esté definido el usuario en activos
        
        if (!empty($listaUsuarios)) {
            foreach ($listaUsuarios as $miUsuario) {
                if ($miUsuario->getNombre() == $selecUsuario) {
                    echo "<option value='" . $miUsuario->getNombre() . "' selected='true'>" . $miUsuario->getNombre() . "</option>";
                    $encontrado = TRUE;
                } else
                    echo "<option value='" . $miUsuario->getNombre() . "'>" . $miUsuario->getNombre() . "</option>";
            }
        }
        
        // Para el caso de que encontremos el usuario, mostramos uno vacio
        if($encontrado) {
            if($selecUsuario != "")
                echo "<option value=''></option>";
        }
        else { // Para el caso de que no encontremos un usuario
            if($selecUsuario != "") {
                echo "<option value='".$selecUsuario."' selected='true'>".$selecUsuario."</option>";
                echo "<option value=''></option>";
            }
            else
                echo "<option value='' selected='true'></option>";
        }
    }
    
    
    /* Función para crear EL CALENDARIO DE UN AÑO DADO */

    public static function crearCalendario($añoActual) {

        if ($añoActual != "") {
            
            // Array que contiene los nombres del mes
            $textoMeses = ["1" => "Enero", "2" => "Febrero", "3" => "Marzo", "4" => "Abril", "5" => "Mayo", "6" => "Junio",
                "7" => "Julio", "8" => "Agosto", "9" => "Septiembre", "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre"];

            // Array que contiene los nombre de los dias de la semana
            $textoDiasSemana = ["1" => "Lunes", "2" => "Martes", "3" => "Miércoles", "4" => "Jueves", "5" => "Viernes", "6" => "Sábado", "7" => "Domingo"];


            // Enpezamos con un nuevo año
            echo "<div id='zonaAño'>";

            // Recorremos todos los meses
            for ($mes = 1; $mes <= 12; $mes++) {

                echo "<div class='bloqueMes'>";
                // Bloque para nombre del mes
                echo "<div class='bloqueTituloMes'>" . $textoMeses[$mes] . "</div>";

                //bloque para nombre de los días de la semana
                echo "<div class='tituloDiasSemana'>";
                for ($posicionSemana = 1; $posicionSemana <= 7; $posicionSemana++) {
                    echo "<div class='bloqueTextoDiaSemana'>" . $textoDiasSemana[$posicionSemana] . "</div>";
                }
                echo "<div class='cancelarFlotantes'></div>";
                echo "</div>";


                // Creamos la variable fecha del primer dia del mes
                $fechaActual = date_create(date($añoActual . "-" . $mes . "-" . "1"));

                // Recorremos todos los campos asignados a un mes (todos los meses con 6 semanas)
                for ($casilla = 1; $casilla <= 6; $casilla++) {

                    // Creo una nueva semana
                    echo "<div class='bloqueDiasSemana'>";

                    // Recorremos toda la semana                
                    for ($posicionSemana = 1; $posicionSemana <= 7; $posicionSemana++) {

                        // Obtenemos el número del dia de la semana correspondiente a la fecha actual
                        $posicionDia = date_format($fechaActual, "N");
                        // Obtenemos el mes correspondiente a la fecha actual
                        $mesActual = date_format($fechaActual, "n");
                        // Obtenemos el dia correspondiente a la fecha actual
                        $diaActual = date_format($fechaActual, "j");

                        // Condición para establecer las celdas vacias
                        if (($posicionDia == $posicionSemana) && ($mesActual == $mes)) {
                            echo "<input type='text' id='dia-" . date_format($fechaActual, "Y-n-j") . "' class='diaSemana' value='" . $diaActual . "' readonly >";
                            // Adelantamos un dia a la fecha actual
                            date_add($fechaActual, date_interval_create_from_date_string("+1 days"));
                        } else {
                            echo "<input type='text' class='casillaVacia' readonly>";
                        }
                    }
                    echo "<div class='cancelarFlotantes'></div>";
                    echo "</div>";
                }

                echo "</div>"; // Fin zona MES            
            }

            echo "<div class='cancelarFlotantes'></div>";

            echo "</div>"; // Fin zona Año
        }
    }
    
    
    /* Función para crear EL CALENDARIO DONDE SE ADMINISTRAN LOS FESTIVOS */

    public static function crearCalendarioAdministraFestivos($añoActual) {

        if ($añoActual != "") {
            
            // Array que contiene los nombres del mes
            $textoMeses = ["1" => "Enero", "2" => "Febrero", "3" => "Marzo", "4" => "Abril", "5" => "Mayo", "6" => "Junio",
                "7" => "Julio", "8" => "Agosto", "9" => "Septiembre", "10" => "Octubre", "11" => "Noviembre", "12" => "Diciembre"];

            // Array que contiene los nombre de los dias de la semana
            $textoDiasSemana = ["1" => "Lunes", "2" => "Martes", "3" => "Miércoles", "4" => "Jueves", "5" => "Viernes", "6" => "Sábado", "7" => "Domingo"];


            // Enpezamos con un nuevo año
            echo "<div id='zonaAño'>";

            // Recorremos todos los meses
            for ($mes = 1; $mes <= 12; $mes++) {

                echo "<div class='bloqueMes'>";
                // Bloque para nombre del mes
                echo "<div class='bloqueTituloMes'>" . $textoMeses[$mes] . "</div>";

                //bloque para nombre de los días de la semana
                echo "<div class='tituloDiasSemana'>";
                for ($posicionSemana = 1; $posicionSemana <= 7; $posicionSemana++) {
                    echo "<div class='bloqueTextoDiaSemana'>" . $textoDiasSemana[$posicionSemana] . "</div>";
                }
                echo "<div class='cancelarFlotantes'></div>";
                echo "</div>";


                // Creamos la variable fecha del primer dia del mes
                $fechaActual = date_create(date($añoActual . "-" . $mes . "-" . "1"));

                // Recorremos todos los campos asignados a un mes (todos los meses con 6 semanas)
                for ($casilla = 1; $casilla <= 6; $casilla++) {

                    // Creo una nueva semana
                    echo "<div class='bloqueDiasSemana'>";

                    // Recorremos toda la semana                
                    for ($posicionSemana = 1; $posicionSemana <= 7; $posicionSemana++) {

                        // Obtenemos el número del dia de la semana correspondiente a la fecha actual
                        $posicionDia = date_format($fechaActual, "N");
                        // Obtenemos el mes correspondiente a la fecha actual
                        $mesActual = date_format($fechaActual, "n");
                        // Obtenemos el dia correspondiente a la fecha actual
                        $diaActual = date_format($fechaActual, "j");

                        // Condición para establecer las celdas vacias
                        if (($posicionDia == $posicionSemana) && ($mesActual == $mes)) {
                            echo "<input type='text' id='dia-" . date_format($fechaActual, "Y-n-j") . "' class='diaSemana' value='" . $diaActual . "' readonly ondblclick='añadeSuprimeFestivo(".json_encode(date_format($fechaActual, "Y-n-j")).", enseñaZonaBotons(), ocultarErroresGrabar())'>";
                            // Adelantamos un dia a la fecha actual
                            date_add($fechaActual, date_interval_create_from_date_string("+1 days"));
                        } else {
                            echo "<input type='text' class='casillaVacia' readonly>";
                        }
                    }
                    echo "<div class='cancelarFlotantes'></div>";
                    echo "</div>";
                }

                echo "</div>"; // Fin zona MES            
            }

            echo "<div class='cancelarFlotantes'></div>";

            echo "</div>"; // Fin zona Año
        }
    }
    
    

}

?>