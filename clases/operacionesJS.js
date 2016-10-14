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

// Función para mostrar los FESTIVOS EN LA PÁGINA
function mostrarFestivos(diaFestivo) {    
    
    // Creo un array que contendrá la fecha de los festivos
    var listaFestivos = new Array();
    
    var zona = document.getElementById("contenFestivos");
    for(i=0; i< diaFestivo.length; i ++) {
        var fechaFestivo = new Date(diaFestivo[i]);
        // Introduzco el nuevo festivo en la lista
        listaFestivos[i] = fechaFestivo;
        // Creo el formato de fecha para enseñar
        var formatoFechaFestivo = fechaFestivo.getDate() + " / " + (fechaFestivo.getMonth() + 1) + " / " + fechaFestivo.getFullYear();
        
        // Defino el elemento 'input' con sus propiedades
        var nuevoFestivo = document.createElement('input');
        nuevoFestivo.type = 'text';
        nuevoFestivo.className = 'textoFestivo';
        nuevoFestivo.value = formatoFechaFestivo;
        nuevoFestivo.readOnly = true;
        zona.appendChild(nuevoFestivo);
    }
    
    // Almaceno la lista de festivos en la Sesión   
    sessionStorage.setItem("listaFestivos", listaFestivos);
    
}

// Función para AÑADIR SUPRIMIR FESTIVOS
function añadeSuprimeFestivo(diaFestivo) {
    
    // Recupero la lista de festivos de la sesión
    var listaFestivos = new Array();
    listaFestivos = sessionStorage.getItem("listaFestivos");
    
    // Con el dato recuperado creamos la variable fecha
    var fechaFestivo = new Date(diaFestivo);
    var formatoFechaFestivo = fechaFestivo.getDate() + " / " + (fechaFestivo.getMonth() + 1) + " / " + fechaFestivo.getFullYear();
    
    // Comprobamos si la fecha introducida ya esta marcada como festivo
    var colorFecha = document.getElementById("dia-"+diaFestivo).style.backgroundColor;
    if(colorFecha == "red") {
        // Como es festivo lo marcamos como blanco y lo suprimimos de la lista
        document.getElementById("dia-"+diaFestivo).style.backgroundColor = "white";
    }
    else {
        document.getElementById("dia-"+diaFestivo).style.backgroundColor = "red";
    }
    
    
    var zona = document.getElementById("contenFestivos");
    

    var nuevoFestivo = document.createElement('input');
    nuevoFestivo.type = 'text';
    nuevoFestivo.className = 'textoFestivo';
    nuevoFestivo.value = formatoFechaFestivo;
    nuevoFestivo.readOnly = true;
    zona.appendChild(nuevoFestivo);
}
