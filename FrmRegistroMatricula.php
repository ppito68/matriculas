<?php
    // //Continuación de sesión
    // session_start();
    // if (!isset($_SESSION["user"])) {
    //     header("location:LoginStaff.php"); //echo "<script>location.href='FrmLogin.php';</script>"; 
    //     exit;
    // }
?>

<!DOCTYPE html>
<html lang="es">

    <head>

        <script 
            src="https://code.jquery.com/jquery-3.3.1.js" 
            integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" 
            crossorigin="anonymous">
        </script>

    </head>

    <?php

        $idSolicitud = $_GET["idSolicitud"];
        $login = $_GET["login"];
        $pass = $_GET["pass"];

        require_once("../Db.php");
        require_once("../T_Alumno.php");
        require_once("./FunctionsGetHtmlFillSelects.php");  

        $reg=GetSolicitud($idSolicitud);
        $alumno=new T_Alumno($reg["IdAlumno"]);

    ?>


    <body>

        <form action="ConfirmacionSolicitudAdmin.php" method="get">
            <input name="pass"type="hidden" id="pass" value="<?php echo $pass; ?>">
            <input name="login" type="hidden" id="login" value="<?php echo $login; ?>">
            <input name="IdAlumno" id="idAlumno" type="hidden" value="<?php echo $reg["IdAlumno"];?>">
            <input name="email" type="hidden" value="<?php echo $alumno->GetEmail();?>">
            <input name="idCursoElegido" id="idCursoElegido" type="hidden" value="<?php echo $reg["IdCurso"]; ?>">
            <input name="idDiasElegido" id="idDiasElegido" type="hidden" value="<?php echo $reg["idDia"]; ?>">
            <input name="idHorarioElegido" id="idHorarioElegido" type="hidden" value="<?php echo $reg["idHorario"]; ?>">
            <input name="idCentroElegido" id="idCentroElegido" type="hidden" value="<?php echo $reg["idCentro"]; ?>">

            <div>Nombre del Alumno:
                <input readonly type="text" 
                        value="<?php echo $alumno->GetNombreCompleto(); ?>">
            </div>                                                                       

            <div>
            <label>Selecciona un centro</label>
            <select id="cbxCentros" name="cbxCentros" required >
                    <?php echo GetHtmlFillSelectCentros($reg["idCentro"]); ?>    
                </select>
            </div>

            <div>
                <label>Selecciona un Curso</label>
                <select id="cbxCursos" name="cbxCursos" required>
                </select> 
                <label id="plazas"></label>
            </div>

            <div>
                <label>Selecciona días/Semana</label>
                <select id="cbxDias" name="cbxDias" required>
                </select>
            </div>

            <div>
                <label>Selecciona Horario</label>
                <select required id="cbxHorarios" name="cbxHorario">
                </select>
            </div>

            <div>Observaciones
                <textarea name="observaciones" rows="4" cols="90" ><?php echo $reg["Observaciones"] ?></textarea>
            </div>

            <input type="submit" name="Enviar" value="CONFIRMAR SOLICITUD">

            <div id="msgServer"></div>

        </form>

        <a href="Panel.php?login=<?php echo $login?>&pass=<?php echo $pass?>">VOLVER</a>


    </body>


</html>


<script type="text/javascript">
    $(document).ready(function() {

        GetListaCursos();

        $('#cbxCentros').change(function() {
            $("#idCentroElegido").val($("#cbxCentros").val());
            GetListaCursos();
        });

        $('#cbxCursos').change(function() {
            $("#idCursoElegido").val($('#cbxCursos').val());
            GetListaDias();
        });

        $('#cbxDias').change(function() {
            $("#idDiasElegido").val($("#cbxDias").val());
            GetListaHorarios();
        });

        $('#cbxHorarios').change(function() {
            $("#idHorarioElegido").val($("#cbxHorarios").val());
            ActualizaPlazas();
        });

    })


    function GetListaCursos() {

        var params = {
            idCentro: $('#cbxCentros').val(),
            idCursoSeleccionado: $('#idCursoElegido').val()
        };

        $.ajax({
            type: "post",
            url: "../CreaListaCursos.php",
            data: params,
            success: function(r) {
                $('#cbxCursos').html(r);
                // $('#cbxCursos').formSelect();
                GetListaDias();
            }
        });
    }

    function GetListaDias() {

        const param = {
            idCentro: $('#cbxCentros').val(),
            idCurso: $('#cbxCursos').val(),
            idDiasSeleccionado: $('#idDiasElegido').val()
        };

        $.ajax({
            type: "post",
            url: "../CreaListaDiasSemana.php",
            data: param,
            success: function(r) {
                $('#cbxDias').html(r);
                // $('#cbxDias').formSelect();
                GetListaHorarios();
            }
        });
    }

    function GetListaHorarios() {

        const param = {
            idCentro: $('#cbxCentros').val(),
            idCurso: $('#cbxCursos').val(),
            idDias: $('#cbxDias').val(),
            idHorarioSeleccionado: $('#idHorarioElegido').val()
        };

        $.ajax({
            type: "post",
            url: "../CreaListaHorarios.php",
            data: param,
            success: function(r) {
                $('#cbxHorarios').html(r);
                // $('#cbxHorarios').formSelect();
            }
        });
    }

    function ActualizaPlazas() {
        const param = {
            idCentro: $('#cbxCentros').val(),
            idCurso: $('#cbxCursos').val(),
            idDia: $('#cbxDias').val(),
            idHorario: $('#cbxHorarios').val(),
        }

        $.ajax({
            type: "post",
            url: "../ObtenerPlazas.php",
            data: param,
            success: function(r) {
                $('#plazas').html(r);
            }
        });

    }

</script>