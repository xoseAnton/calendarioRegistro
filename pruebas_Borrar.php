<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        
        
        
        
        <div id="zonaAño">
                
                <!-- Bloque MES ENERO -->
                <div class="bloqueMes">
                    <div class="bloqueTituloMes">Enero</div>
                    <div class="tituloDiasSemana">
                        <div class="bloqueTextoDiaSemana">Lunes</div>
                        <div class="bloqueTextoDiaSemana">Martes</div>
                        <div class="bloqueTextoDiaSemana">Miércoles</div>
                        <div class="bloqueTextoDiaSemana">Jueves</div>
                        <div class="bloqueTextoDiaSemana">Viernes</div>
                        <div class="bloqueTextoDiaSemana">Sábado</div>
                        <div class="bloqueTextoDiaSemana">Domingo</div>
                        <div class="cancelarFlotantes"></div>
                    </div>
                    <div class="bloqueDiasSemana">
                        <input type="text" class="casillaVacia" readonly>
                        <input type="text" class="casillaVacia" readonly>
                        <input type="text" id="bloque3mes1" class="diaFestivo" value="1" readonly>
                        <input type="text" id="bloque4mes1" class="diaSemana" value="31" readonly>
                        <input type="text" id="bloque5mes1" class="diaSemana" value="31" readonly>
                        <input type="text" id="bloque6mes1" class="diaSemana" value="31" readonly>
                        <input type="text" id="bloque6mes1" class="diaSemana" value="31" readonly>                        
                        <div class="cancelarFlotantes"></div>
                    </div>
                    <div class="bloqueDiasSemana">
                        <input type="text" id="bloque8mes1" class="diaSemana" value="31" readonly>
                        <input type="text" id="bloque9mes1" class="diaSemana" value="31" readonly>
                        <input type="text" id="bloque10mes1" class="diaSemana" value="31" readonly>
                        <input type="text" id="bloque11mes1" class="diaSemana" value="31" readonly>
                        <input type="text" id="bloque12mes1" class="diaSemana" value="31" readonly>
                        <input type="text" id="bloque13mes1" class="diaSemana" value="31" readonly>
                        <input type="text" id="bloque14mes1" class="diaSemana" value="31" readonly>
                        <div class="cancelarFlotantes"></div>
                    </div>
                    <div class="bloqueDiasSemana">
                        <input type="text" id="bloque15mes1" class="diaSemana" value="31" readonly>
                        <input type="text" id="bloque16mes1" class="diaSemana" value="31" readonly>
                        <input type="text" id="bloque17mes1" class="diaSemana" value="31" readonly>
                        <input type="text" id="bloque18mes1" class="diaSemana" value="31" readonly>
                        <input type="text" id="bloque19mes1" class="diaSemana" value="31" readonly>
                        <input type="text" id="bloque20mes1" class="diaSemana" value="31" readonly>
                        <input type="text" id="bloque21mes1" class="diaSemana" value="31" readonly>
                        <div class="cancelarFlotantes"></div>
                    </div>
                    <div class="bloqueDiasSemana">
                        <input type="text" id="bloque22mes1" class="diaSemana" value="31" readonly>
                        <input type="text" id="bloque23mes1" class="diaSemana" value="31" readonly>
                        <input type="text" id="bloque24mes1" class="diaSemana" value="31" readonly>
                        <input type="text" id="bloque25mes1" class="diaSemana" value="31" readonly>
                        <input type="text" id="bloque26mes1" class="diaSemana" value="31" readonly>
                        <input type="text" id="bloque27mes1" class="diaSemana" value="31" readonly>
                        <input type="text" id="bloque28mes1" class="diaSemana" value="31" readonly>
                        <div class="cancelarFlotantes"></div>
                    </div>
                    <div class="bloqueDiasSemana">
                        <input type="text" id="bloque29mes1" class="diaSemana" value="31" readonly>
                        <input type="text" id="bloque30mes1" class="diaSemana" value="31" readonly>
                        <input type="text" id="bloque31mes1" class="diaSemana" value="31" readonly>
                        <input type="text" id="bloque32mes1" class="diaSemana" value="31" readonly>
                        <input type="text" id="bloque33mes1" class="diaSemana" value="31" readonly>
                        <input type="text" id="bloque34mes1" class="diaSemana" value="31" readonly>
                        <input type="text" id="bloque35mes1" class="diaSemana" value="31" readonly>
                        <div class="cancelarFlotantes"></div>
                    </div>
                    <div class="bloqueDiasSemana">
                        <input type="text" id="bloque36mes1" class="diaSemana" value="31" ondblclick="mostrarIdDiaSeleccionado()" readonly>
                        <input type="text" id="bloque37mes1" class="diaSemana" value="31" readonly>
                        <input type="text" id="bloque38mes1" class="diaSemana" value="31" readonly>
                        <input type="text" id="bloque39mes1" class="diaSemana" value="31" readonly>
                        <input type="text" id="bloque40mes1" class="diaSemana" value="31" readonly>
                        <input type="text" id="bloque41mes1" class="diaSemana" value="31" readonly>
                        <input type="text" id="bloque42mes1" class="diaSemana" value="31" readonly>
                        <div class="cancelarFlotantes"></div>
                    </div>
                </div>                
               
                
                <div class="bloqueMes">                
                </div>
                <div class="bloqueMes">                
                </div>
                <div class="cancelarFlotantes"></div>
                
                
                
                 <?php
                
                $fechaPropia = date_create(date("Y-m-d"));                
                
                
                echo date_format($fechaPropia, "N");
                
                echo "</br>";
                
                echo date_format($fechaPropia, "Y-m-d");
                
                
                ?>
                
                
            </div>
        
        
        
        
        
        
        <?php
        // put your code here
        ?>
    </body>
</html>
