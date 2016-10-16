// Función para mostrar los días inhábiles en el calendario
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
    
    // Zona donde se enseñaran los festivos
    var zona = document.getElementById("contenFestivos");
    
    for(i=0; i< diaFestivo.length; i ++) {        
        var fechaFestivo = new Date(diaFestivo[i]);        
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
    
}


// Función para AÑADIR SUPRIMIR FESTIVOS
function añadeSuprimeFestivo(diaFestivo) {    
    
    // Defino las variables    
    var listaFestivos = new Array();    
    
    // Zona donde se encuentran los festivos
    var zona = document.getElementById("contenFestivos");
    // Recuperamos todos los festivos de esa zona
    var listaFormatoFestivos = zona.getElementsByClassName("textoFestivo");
    
    
    for(i=0; i<listaFormatoFestivos.length; i++) {
        var formatoFestivo = listaFormatoFestivos[i].value;        
        // Separamos el valor obtenido para construir la fecha.
        var cadena = formatoFestivo.split(" / ");         
        var fechaFestivo = cadena[2]+"-"+cadena[1]+"-"+cadena[0];
        // Guardamos el festivo en el array.
        listaFestivos[i] = fechaFestivo;
    }   
    
   // Borramos todos los elementos contenidos en la zona "contenFestivos"
   while(zona.firstChild) {
       zona.removeChild(zona.firstChild);
   }   
    
    // Comprobamos si la fecha introducida ya esta marcada como festivo
    var colorFecha = document.getElementById("dia-"+diaFestivo).style.backgroundColor;
    if(colorFecha == "red") {
        // Como es festivo lo marcamos como blanco y lo suprimimos de la lista
        document.getElementById("dia-"+diaFestivo).style.backgroundColor = "white";
        // Primero buscamos la posición que ocupa en el array nuestro festivo        
        var posicionFestivo = listaFestivos.indexOf(diaFestivo);          
        // Ahora borramos el elemento que coincide con esa posición, siempre que lo encuentre
        if(posicionFestivo >= 0)
            listaFestivos.splice(posicionFestivo,1);        
    }
    else {
        // Introduzco la nueva variable en el array de días festivos
        listaFestivos.push(diaFestivo);        
        // Marco su casilla correspondiente de color rojo
        document.getElementById("dia-"+diaFestivo).style.backgroundColor = "red";
    }
    
    // Mostramos los festivos resultantes    
    mostrarFestivos(listaFestivos);
}
