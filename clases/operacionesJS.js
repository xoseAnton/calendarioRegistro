// Función para mostrar los días inhábiles
function mostrarDiasInhabiles(diaInhabil) {
    for(i=0; i< diaInhabil.length; i ++) {
        document.getElementById("dia-"+diaInhabil[i]).style.backgroundColor = "red";
    }
}

// Función para mostrar el campo de errores
function mostrarInformacionErrores(errores) {    
    // Mostramos la información de los errores        
    document.getElementById("textoInformacionErrores").value = errores;
    document.getElementById("zonaInformacionErrores").style.visibility = "visible"; 
}

// Función para limpiar los resultados anteriores
function limpiarResultadosAnteriores() {
    document.getElementById("textoDespacho").value = "";
    document.getElementById("textoCaducidad").value = "";
    document.getElementById("textoResultadoFinal").value = "";
}