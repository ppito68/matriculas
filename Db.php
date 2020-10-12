<?php

    // Para SQlServer
   /* function OpenConnection(){  
       try  
       {  
           $serverName = "citylemos32.dyndns.org";  
           $connectionOptions = array("Database"=>"city",  
               "Uid"=>"citynet", "PWD"=>"lainglesaii");  
           $conn = sqlsrv_connect($serverName, $connectionOptions);  
           if($conn == false) {
                die(FormatErrors(sqlsrv_errors()));  
           } 
              
       }  
       catch(Exception $e)  
       {  
           echo("Error!");  
       }  
   }   */

    Function PdoOpenCon(){
            
            /* Esto es para SQlServer
            $serverName = "citylemos32.dyndns.org";  
            */

            // para server local
            // $host="localHOst"; //"lldf924.servidoresdns.net"; //"217.76.143.24"; 
            // $database = "city"; //"qadi365"; // "city";  
            // $uid = "root"; // "qadi365"; // "root";     ;         // = "citynet";
            // $pwd = "";                  // = "lainglesaii";  

            // server city
            $host="db524833722.db.1and1.com";
            $database = "db524833722";  
            $uid = "dbo524833722";           
            $pwd = "50031152Bba";                

            // para hosting lucre
            // $host="lldf924.servidoresdns.net"; //"217.76.143.24"; 
            // $database="qadi365";   
            // $uid ="qadi365"; 
            // $pwd ="JaLu0509";   
            

                
            try {  
                //Para SqlServer-> $conn = new PDO( "sqlsrv:server=$serverName;Database = $database", $uid, $pwd);   
                
                // $conn = new PDO( "mysql:host=" . $host . "; dbname=" . $database . "", $uid,  $pwd);
                $conn = new PDO( "mysql:host=" . $host . "; dbname=" . $database . ";charset=utf8mb4", $uid,  $pwd);

                $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
                // $conn->set_charset("utf8");
            }   
            catch( PDOException $e ) {  
                die( $e->GetMessage() . " Linea error: " . $e->GetLine());   
            }  

            return $conn;
    }

    function GetAlumnoById($id){
            $con=PdoOpenCon();
            $sql="SELECT * FROM ssalumnos WHERE id = :id";
            $recSet=$con->prepare($sql);
            $recSet->execute(array(":id"=>$id));
            return $recSet->fetch(PDO::FETCH_ASSOC);
        }

        function GetNombreYApellidosAlumno($id){
            $reg=GetAlumnoById($id);
            return $reg['Nombre'] . ' ' . $reg['Apellidos'];
        }
        
    // Obtiene el alumno con el nif recibido. Carga una columna mas si ha estado matriculado en el curso 19-20 que será null si no lo estuvo.
    function GetAlumnoNif($Nif){
        $con=PdoOpenCon();
        $sql="SELECT * FROM ssalumnos as a LEFT JOIN ssfm1920 as f ON f.NumAlumno = a.numeroAlumno WHERE a.nif = :nif";
        $recSet=$con->prepare($sql);
        $recSet->execute(array(":nif"=>$Nif));
        return $recSet->fetch(PDO::FETCH_ASSOC);
    }

    // Octiene los alumnos por email del tutor principal. Carga una columna mas si ha estado matriculado en el curso 19-20 que será null si no lo estuvo.
    function GetAlumnosPorEmail($email){
        $con=PdoOpenCon();
        $sql="SELECT a.*, f.Curso, f.Horario, f.DiasSemana FROM ssalumnos as a LEFT JOIN ssfm1920  as f ON f.NumAlumno = a.numeroAlumno 
                WHERE a.emailPrincipal = :email";
        $recSet=$con->prepare($sql);
        $recSet->execute(array(":email"=>$email));
        return $recSet; 
    }

    function GetDiasSemana($idCurso, $idCentro){
        $con=PdoOpenCon();
        $sql="SELECT distinct dias.* FROM ssDiasSemana dias 
                INNER JOIN ssGrupos gr ON gr.idDia = dias.id
                INNER JOIN ssAulas aul ON aul.id = gr.idAula
                WHERE aul.idCentro = :idCentro AND gr.IdCurso = :idCurso";
        $recSet=$con->prepare($sql);
        $recSet->execute(array(":idCentro"=>$idCentro, ":idCurso"=>$idCurso));
        return $recSet; 
    }

    function GetDia($id){
        $con=PdoOpenCon();
        $sql="SELECT * FROM ssDiasSemana WHERE id = :id";
        $recSet=$con->prepare($sql);
        $recSet->execute(array(":id"=>$id));
        $dia=$recSet->fetch(PDO::FETCH_ASSOC);
        return $dia; 
    }

    function GetNombreDia($id){
        $dia=GetDia($id);
        return $dia["dias"];
    }

    function GetCentros(){
        $con=PdoOpenCon();
        $sql="SELECT * FROM ssCentros";
        $recSet=$con->query($sql);
        return $recSet; 
    }

    function GetCentro($id){
        $con=PdoOpenCon();
        $sql="SELECT * FROM ssCentros WHERE id = :id";
        $recSet=$con->prepare($sql);
        $recSet->execute(array(":id"=>$id));
        $centro=$recSet->fetch(PDO::FETCH_ASSOC);
        return $centro;
    }

    function GetNombreCentro($id){
        $centro=GetCentro($id);
        return $centro["centro"];
    }

    function GetCursos($idCentro, $idAula){

        // Crea el filtro del Aula. Este filtro hay que activarlo tanto si filtra el Aula como si filtra el Centro, 
        // ya que las clausulas INNER para enlazar con las aulas es intermediaria para enlazar con los centros. 
        $filtroInnerAula = $idAula > 0 || $idCentro > 0
            ? " INNER JOIN Grupos g ON g.IdCurso = cur.id
                INNER JOIN ssAulas au ON au.id = g.IdAula "
            : "";
        $filtroWhereAula = $idAula > 0 ? " AND au.id = " . $idAula  : "";
        
        // Crea el filtro del Centro
        $filtroInnerCentro = $idCentro > 0 
            ? " INNER JOIN ssCentros cen ON cen.id = au.idCentro  "
            : "";
        $filtroWhereCentro = $idCentro > 0 ? ' AND au.idCentro = :idCentro ' : '';

        
        // Monta la cadena completa de la consulta
        $sql="SELECT DISTINCT cur.* FROM sscursos cur " . $filtroInnerAula . $filtroCentro . 
            " WHERE 1 = 1 " . $filtroWhereCentro . $filtroWhereAula . " ORDER BY Descripcion";

            echo $sql;

        // Abre conexion, prepara la consulta, la ejecuta y devuelve el array
        $con=PdoOpenCon();
        $recSet=$con->prepare($sql);
        $recSet->execute(array(":idCentro"=>$idCentro)); //, ":idAula"=>$idAula));
        return $recSet; 
    }


    function GetPlazasDisponibles($idCentro, $idCurso, $idDia, $idHorario){
        
        // filtros 
        $filtroCentro=$idCentro=='0' ? '' : ' AND idCentro = :idCentro ';
        $filtroCurso=$idCurso=='0' ? '' : ' AND IdCurso = :idCurso ';
        $filtroDia=$idDia=='0' ? '' : ' AND idDia = :idDia ';
        $filtroHorario=$idHorario=='0' ? '' : ' AND idHorario = :idHorario ';

        $con=PdoOpenCon();
        $sql="SELECT (SELECT SUM(plazas) FROM ssAulas au INNER JOIN ssGrupos gr ON gr.IdAula = au.Id
                        WHERE 1=1 " . $filtroCentro . $filtroCurso . $filtroDia . $filtroHorario . " ) -
                    (SELECT COUNT(DISTINCT idAlumno) FROM sssolicitudesmatriculas  
                        WHERE 1=1" . $filtroCentro . $filtroCurso . $filtroDia . $filtroHorario . " ) as plazasDisponibles ";

        // $recSet=$con->prepare("$sql"); CON TOA SU PUTA MADRE PUSE EL PARAMETRO ENTRE COMILLAS
        $recSet=$con->prepare($sql);

        $recSet->execute(array(":idCentro"=>$idCentro, ":idCurso"=>$idCurso, ":idDia"=>$idDia, ":idHorario"=>$idHorario));

         return $reg=$recSet->fetch(PDO::FETCH_ASSOC);
        
         //return $recSet->fetchAll(2)[0];  
    }

    function GetHorarios($idCurso, $idDias, $idCentro){
        $con=PdoOpenCon();
        $sql="SELECT DISTINCT h.* FROM ssHorarios h
                    INNER JOIN ssGrupos g on g.IdHorario = h.Id
                    INNER JOIN sscursos cur on cur.id = g.IdCurso 
                    INNER JOIN ssAulas au ON au.id = g.IdAula
              WHERE  cur.id = :idCurso 
                    AND g.idDia = :idDias
                    AND au.idCentro = :idCentro
                ORDER BY horario";

        $recSet=$con->prepare($sql);
        $recSet->execute(array(":idCurso"=>$idCurso, ":idDias"=>$idDias, ":idCentro"=>$idCentro));
        return $recSet; 
    }

    function GetHorario($id){
        $con=PdoOpenCon();
        $sql="SELECT * FROM ssHorarios WHERE id = :id";
        $recSet=$con->prepare($sql);
        $recSet->execute(array(":id"=>$id));
        $row=$recSet->fetch(PDO::FETCH_ASSOC); 
        return $row["horario"]; 
    }

    function GetAlumnosPorEmailConCursos($email, $pass){
        $con=PdoOpenCon();
        $sql="SELECT a.*, f.Curso as cursoAnterior, f.Horario as horarioAnterior, f.DiasSemana as diasSemanaAnterior, s.id as idMatricula, 
                s.Observaciones as ObservacionesMatricula, s.idDia as idDiasElegido, s.idHorario as idHorarioElegido, s.idCentro as idCentroElegido,
                s.IdCurso as idCursoElegido, f.IdCentro as idCentroAnterior, cen.centro as centroAnterior,  (SELECT Id FROM sscursos WHERE id = 
                                                                                    (SELECT idNext FROM sscursos WHERE id = f.IdCurso)) as idCursoSiguiente,  
                                                                               (SELECT Descripcion FROM sscursos WHERE id = 
                                                                                    (SELECT idNext FROM sscursos WHERE id = f.IdCurso)) as cursoSiguiente  
                FROM ssalumnos as a 
                    LEFT JOIN ssfm1920 as f ON f.NumAlumno = a.numeroAlumno 
                    LEFT JOIN sssolicitudesmatriculas AS s ON s.idAlumno = a.id
                    LEFT JOIN ssCentros cen ON cen.id = f.idCentro
                WHERE a.emailPrincipal = :email AND a.password = :pass AND a.password <> ''
                        AND ( s.fecha >= (select MAX(fecha) from sssolicitudesmatriculas where IdAlumno= a.id)
                              OR s.Fecha is null)";
        $recSet=$con->prepare($sql);
        $recSet->execute(array(":email"=>$email, ":pass"=>$pass));
        return $recSet; 
    }

    function login($email, $pass){
        $con=PdoOpenCon();
        $sql="SELECT id, a.password FROM ssalumnos as a WHERE a.emailPrincipal = :email AND a.password = :pass";
        $recSet=$con->prepare($sql);
        $recSet->execute(array(":email"=>$email, ":pass"=>$pass));
        return $recSet;
     
        // $reg=$recSet->fetch(PDO::FETCH_ASSOC);
        // return $reg;
        
        // ---- Con encriptación
        //if(!$reg){
        //    return false;
        //}
        //return password_verify($pass, $reg["password"]);
    }

    function loginStaff($login, $pass){
        $con=PdoOpenCon();
        $sql="SELECT * FROM ssUsuarios u WHERE u.login = :login AND u.password = :pass";
        $recSet=$con->prepare($sql);
        $recSet->execute(array(":login"=>$login, ":pass"=>$pass));
        return $recSet;
     
        // $reg=$recSet->fetch(PDO::FETCH_ASSOC);
        // return $reg;
        
        // ---- Con encriptación
        //if(!$reg){
        //    return false;
        //}
        //return password_verify($pass, $reg["password"]);
    }

    function GuardarClave($email, $pass){
        $con=PdoOpenCon();
        $sql="UPDATE ssalumnos SET password = :pass WHERE emailPrincipal = :email";
        $recSet=$con->prepare($sql);
        $result=$recSet->execute(array(":pass"=>$pass, ":email"=>$email));
        return $result; 
    }

    function GetNombreCurso($Id){
        $con=PdoOpenCon();
        $sql="SELECT Descripcion FROM sscursos WHERE Id = :idCurso";
        $recSet=$con->prepare($sql);
        $recSet->execute(array(":idCurso"=>$Id));
        if($row=$recSet->fetch(PDO::FETCH_ASSOC)){ 
            return $row["Descripcion"];
        }else{
            return null;
        }
    }

    function GuardarSolicitud($idAlumno, $idPromocion, $idCentro, $idCurso, $idHorario, $idDia, $Observaciones){
        $con=PdoOpenCon();
        $sql="INSERT INTO sssolicitudesmatriculas(IdAlumno, IdPromocion, idCentro, IdCurso, Fecha, idHorario, idDia, Observaciones) 
                VALUES (:idAlum, :idProm, :idCentro, :idCurso, NOW(), :idHorario, :idDia, :observ )";
        $recSet=$con->prepare($sql);
        $result=$recSet->execute(array(":idAlum"=>$idAlumno, ":idProm"=>$idPromocion, ":idCentro"=>$idCentro, ":idCurso"=>$idCurso, ":idHorario"=>$idHorario, 
                                        ":idDia"=>$idDia, ":observ"=>$Observaciones ));
        return $result; 
    }

    function GetSolicitudes($idCentro, $idCurso, $idDia, $idHorario, $idPromocion){
        $filtroCentro = $idCentro==0 || $idCentro=='' ? '' : ' AND cen.id = ' . $idCentro;
        $filtroCurso = $idCurso==0 || $idCurso=='' ? '' : ' AND cur.id = ' . $idCurso;
        $filtroDia = $idDia==0 || $idDia=='' ? '' : ' AND dia.id = ' . $idDia;
        $filtroHorario = $idHorario==0 || $idHorario=='' ? '' : ' AND hor.id = ' . $idHorario;
        $filtropromocion = $idPromocion==0 || $idPromocion=='' ? '' : ' AND sol.IdPromocion = ' . $idPromocion;

        $con=PdoOpenCon();
        
        $sql="SELECT sol.fecha, sol.id as idSolicitud, al.id AS idAlumno, NumeroAlumno, CONCAT(Apellidos, ', ', Nombre) as nombreAlumno, sol.Observaciones, 
                cur.Descripcion, dia.dias, hor.horario, sol.visto, (select count(idAlumno) from sssolicitudesmatriculas s where s.idAlumno = al.id) as repeticiones
                FROM sssolicitudesmatriculas sol
                    INNER JOIN ssalumnos al ON al.id = sol.IdAlumno
                    LEFT JOIN ssCentros cen ON cen.id = sol.idCentro
                    LEFT JOIN sscursos cur ON cur.id = sol.IdCurso
                    LEFT JOIN ssDiasSemana dia ON dia.id = sol.idDia
                    LEFT JOIN ssHorarios hor ON hor.id = sol.idHorario
                WHERE sol.fecha >= (select MAX(fecha) from sssolicitudesmatriculas where IdAlumno= al.id)" . $filtroCentro . $filtroCurso . $filtroDia . 
                $filtroHorario . $filtropromocion . " ORDER BY sol.fecha";

        $recSet=$con->prepare($sql);
        $recSet->execute();
        return $recSet; 
       
    }

    function GetSolicitudesMatricula($IdAlumno){
        $con=PdoOpenCon();
        $sql="SELECT * FROM sssolicitudesmatriculas WHERE IdAlumno = :IdAlumno";
        $recSet=$con->prepare($sql);
        $recSet->execute(array(":IdAlumno"=>$IdAlumno));
        return $recSet; 
    }

    function GetSolicitudesPorCursos($idCentro){

        $con=PdoOpenCon();

        $sql="SELECT s.idCurso, cur.Descripcion as curso, s.idDia, dias.dias, s.idHorario, h.horario, COUNT(distinct s.idAlumno) as solicitudes
                FROM sssolicitudesmatriculas s 
                    INNER JOIN sscursos cur ON cur.id = s.IdCurso
                    INNER JOIN ssDiasSemana dias ON dias.id = s.idDia
                    INNER JOIN ssHorarios h ON h.id = s.idHorario
                WHERE s.fecha = (SELECT max(fecha) from sssolicitudesmatriculas where idAlumno = s.IdAlumno) AND s.idCentro = :idCentro
                GROUP BY s.idCurso, s.idDia, s.idHorario
                ORDER BY cur.Descripcion, h.horario";
                
        $recSet=$con->prepare($sql);
        $recSet->execute(array(":idCentro"=>$idCentro));
        return $recSet; 

    }    

    function GetSolicitudesSinDia($idCentro){
        $con=PdoOpenCon();

        $sql='SELECT cur.descripcion as curso, count(distinct s.idAlumno) as solicitudes
                FROM sssolicitudesmatriculas s 
                    INNER JOIN sscursos cur ON s.IdCurso = cur.id 
                WHERE s.fecha = (SELECT max(fecha) from sssolicitudesmatriculas where idAlumno = s.IdAlumno)
                    AND (s.idDia = 0 or s.idHorario = 0) AND s.idCentro = :idCentro
                GROUP BY cur.descripcion';

        $recSet=$con->prepare($sql);
        $recSet->execute(array(":idCentro"=>$idCentro));
        return $recSet; 
    }

    function GetCuadrante($idCentro){

        $con=PdoOpenCon();

        $sql='SELECT cur.descripcion as curso, dias.dias, h.horario, (SELECT COUNT(DISTINCT idAlumno)  FROM sssolicitudesmatriculas s
                                                                WHERE s.idcurso = cur.id
                                                                    AND s.idcentro = :idCentro
                                                                    AND s.idDia = dias.id
                                                                    AND s.idHorario = h.id 
                                                                    AND s.fecha = (SELECT max(fecha) from sssolicitudesmatriculas where idAlumno = s.IdAlumno)) as solicitudes
                FROM sscursos cur
                INNER JOIN ssGrupos g ON g.IdCurso = cur.id
                INNER JOIN ssDiasSemana dias ON dias.id = g.idDia
                INNER JOIN ssHorarios h ON h.id = g.idHorario
                INNER join ssAulas au ON au.id = g.IdAula
                
                WHERE au.idCentro = :idCentro
                ORDER BY dias.dias, au.Aula, h.horario';

        $recSet=$con->prepare($sql);
        $recSet->execute(array(":idCentro"=>$idCentro));
        return $recSet; 
    }

   

    function GetTotalSolicitudes($idCentro){
        $con=PdoOpenCon();
        $sql="SELECT COUNT(DISTINCT idAlumno) as solicitudes FROM sssolicitudesmatriculas WHERE idcentro = :idCentro";
        $recSet=$con->prepare($sql);
        $recSet->execute(array(":idCentro"=>$idCentro));
        $cant=$recSet->fetch(PDO::FETCH_ASSOC);
        return $cant["solicitudes"];
    }
        
    function SetVisto($idSolicitud){
        $con=PdoOpenCon();
        $sql="UPDATE sssolicitudesmatriculas 
              SET visto = not visto
                WHERE Id = :idSolicitud";
        $recSet=$con->prepare($sql);
        $result=$recSet->execute(array(":idSolicitud"=>$idSolicitud));
        return $result; 
    }

 // Funciones de Matriculas definitivas
    function GetMatriculas($idCentro, $idCurso, $idDia, $idHorario, $idPromocion, $idAula){
        $filtroCentro = $idCentro==0 || $idCentro=='' ? '' : ' AND cen.id = ' . $idCentro;
        $filtroCurso = $idCurso==0 || $idCurso=='' ? '' : ' AND cur.id = ' . $idCurso;
        $filtroDia = $idDia==0 || $idDia=='' ? '' : ' AND dia.id = ' . $idDia;
        $filtroHorario = $idHorario==0 || $idHorario=='' ? '' : ' AND hor.id = ' . $idHorario;
        $filtropromocion = $idPromocion==0 || $idPromocion=='' ? '' : ' AND mat.IdPromocion = ' . $idPromocion;
        $filtroAulas = $idAula==0 || $idAula=='' ? '' : ' AND mat.idAula = ' . $idAula;

        $con=PdoOpenCon();
        
        $sql="SELECT mat.id as idMatricula, al.id AS idAlumno, al.NumeroAlumno, 
                CONCAT(al.Apellidos, ', ', al.Nombre) as nombreAlumno, 
                cur.Descripcion as descripcionCurso, dia.dias, hor.horario, mat.observacionesTutor,
                mat.observacionesSecretaria
                FROM matriculas AS mat
                    INNER JOIN ssalumnos al ON al.id = mat.idAlumno
                    INNER JOIN ssCentros cen ON cen.id = mat.idCentro
                    INNER JOIN sscursos cur ON cur.id = mat.idCurso
                    INNER JOIN ssDiasSemana dia ON dia.id = mat.idDia
                    INNER JOIN ssHorarios hor ON hor.id = mat.idHorario
                    INNER JOIN ssAulas aul ON aul.id = mat.idAula
                WHERE 1 = 1 " . $filtroCentro . $filtroCurso . $filtroDia . 
                $filtroHorario . $filtropromocion . $filtroAulas . " ORDER BY al.NumeroAlumno";

        $recSet=$con->prepare($sql);
        $recSet->execute();
        return $recSet; 
    
    }

    // Param: $idCentro.- Si 0, no filtra
    function GetAulas($idCentro=0){
        $filtroCentro = $idCentro==0 ? '' : ' WHERE idCentro = ' . $idCentro;
        $con=PdoOpenCon();
        $sql="SELECT * FROM ssAulas" . $filtroCentro;
        $recSet=$con->prepare($sql);
        $recSet->execute();
        return $recSet; 
    }

    function GetProfesores(){
        $con=PdoOpenCon();
        $sql="SELECT * FROM stProfesores order by nombre" . $filtroCentro;
        $recSet=$con->prepare($sql);
        $recSet->execute();
        return $recSet; 
    }

    function GetFechasCalendario($idPromocion, $diasArray){
        $con=PdoOpenCon();

        // Filtro de los días de la semana
        $filtroDias = "";
        $lenArray = count($diasArray);
        if( $lenArray > 0 ){
            $filtroDias = " AND (";
            for($n=0;$n < $lenArray; $n++){
                $filtroDias .= " diaSemana = " . $diasArray[$n] . ( ($n < $lenArray - 1) ? " OR" : "" );
            }
            $filtroDias .= ")";
        }

        $sql="SELECT fecha, concat( 
                ELT(WEEKDAY(fecha) + 1, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo'), 
                ' - ', day(fecha), '/', month(fecha), '/', year(fecha)
                                    ) as diaSemanaYFecha
                FROM stCalendario WHERE idPromocion = :idPromocion" . $filtroDias;
        $recSet=$con->prepare($sql);
        $recSet->execute(array(':idPromocion'=>$idPromocion));
        return $recSet; 
    }

// ********************************************************************************************************************************

    // Estas funciones son provisionales para salir al paso del problema del COVID, ya que
    //  tratan con tablas importadas de File Maker sin depurar. Todas estas funciones
    // se le antepone la palabra `Cov` para diferenciarlas de las definitivas. En su día se borrarán o se modificaran

    // Devuelve coleccion de grupos cuyas clases se imparten en el dia de la fecha recibida como parametro
    function CovGetGruposAInformar($fechaImparticion, $centro, $idAula, $curso, $horario){
        $filtroCentro = $centro == "" || $centro == '0' ? "" : " AND centro = " . $centro;
        $filtroAula = $idAula == "" || $idAula == '0' ? "" : " AND idAula = " . $idAula;
        $filtroCurso = $curso == "" || $curso == '0' ? "" : " AND curso = '" . $curso . "' ";
        $filtroHorario = $horario == "" || $horario == '0' ? "" : " AND horario = '" . $horario . "' ";

        $diaSemana = date("N", strtotime($fechaImparticion)); // obtiene el dia de la semana en cifra
        $filtroDias = "";
        if($diaSemana == 1 || $diaSemana == 3){ // Lunes y Miercoles
            $filtroDias = " AND dias = 'M-W' ";
        }elseif($diaSemana == 2 || $diaSemana == 4){ // Martes y Jueves
            $filtroDias = " AND dias = 'T-TH' ";
        }

        $con=PdoOpenCon();
        $sql=" SELECT * FROM viewGrupos g
                WHERE 1 = 1 " . $filtroDias . $filtroCentro . $filtroAula . $filtroCurso . $filtroHorario;
        
        // --- LINEA DE DEPURACION->echo "Consulta del Grupo: " . $sql . "\n";
        
        $recSet=$con->prepare($sql);
        $recSet->execute();
        return $recSet; 
    }

    // Devuelve coleccion con los los alumnos de un grupo en concreto por orden de asistencia online
    //  donde los primeros son los que MENOS han echo clases online. Los alumnos que hayan hecho las mismas clases online se 
    //   ordenan por orden descendente segun su numero, por poner un orden.
    // Params: $fechaImparticion.- Tomará los datos con fecha anterior a ésta 
    function CovGetCuadranteAsistenciasPorAlumnos($centro, $idAula, $fechaImparticion, $curso, $horario){
        
        $mes = date("m", strtotime($fechaImparticion));
        $filtroCentro = $centro > 0 ? " AND idCentro = " . $centro : "";
        $filtroAula = $idAula > 0 ? " AND idAula = " . $idAula : "";
        $filtroCurso = $curso == "" || $curso == '0' ? "" : " AND curso = '" . $curso . "' ";
        $filtroHorario = $horario != 0 ? " AND horario = '" . $horario . "' "  : "";

        $filtroDiaSemana = "";
        $diaSemana = 0;
        if($fechaImparticion!=0){
            $diaSemana = date("N", strtotime($fechaImparticion)); // obtiene el dia de la semana en cifra
            if($diaSemana!=5){ // Si es Viernes, no filtra, ya que es posible citar a cualquier alumno de cualquier dia
                $filtroDiaSemana = $diaSemana==1 || $diaSemana==3 ? " AND fm.dias = 'M-W' " : " AND fm.dias = 'T-TH' ";
            }
        }
        
        $con=PdoOpenCon();

        // Obtiene los dias del calendario escolar del mes recibido para montar la subcadena de la consulta principal
        //  que monta una columna por cada uno de los dias del calendario obtenidos con la forma de asistencia de cada dia; 
        //  el nombre de cada coluna es formado por la cadena del valor del campo fecha de asistencia.
        // Tambien crea tantas columnas como días obtenidos con los modos de asistencias REALES; para diferenciar el nombre 
        //      de estas columnas con las de las asistencia, se antepone "real" delante de la cadena formada por el valor del
        //      campo fecha de asistencia.
        // Tambien crea tantas columnas como días obtenidos con las fechas en la que se notificó por email al alumno los modos 
        //      de asistencias de cada día; para diferenciar el nombre de estas columnas con las de las asistencia, se antepone 
        //      "com" delante de la cadena formada por el valor del campo fecha de asistencia.
        // Tambien crea tantas columnas como días obtenidos con la fecha en la que el alumno leyó el email, si es que éste picó 
        //      en el enlace de acuse de recibo del mensaje recibido que se le envió en el cuerpo del email; se antepone la cadena 
        //      "rec" delante de la cadena formada por el valor del campo fecha de asistencia para diferenciarla de las otras columnas.
        // Añadida tambien tantas columnas como dias obtenidos donde se octiene si la asisgnacion del modo fue de forma manual o 
        //      generada por la aplicacion, anteponiendole la cadena 'man' delante de la fecha
        $diasRecSet=CovGetDiasCalendario($fechaImparticion);
        $subCadenaDias = "";
        while($diaCal=$diasRecSet->fetch(PDO::FETCH_ASSOC)){
            $subCadenaDias = $subCadenaDias . ", ( SELECT modoAsistencia FROM stControlAsistencia c 
            WHERE c.numeroAlumno = fm.numero AND c.fecha = '" . $diaCal['fecha'] . "') AS '" . $diaCal['fecha'] . "', 
            ( SELECT modoAsistenciaReal FROM stControlAsistencia  
            WHERE numeroAlumno = fm.numero AND fecha = '" . $diaCal['fecha'] . "') AS 'real" . $diaCal['fecha'] . "',
            ( SELECT fechaHoraComunicacion FROM stControlAsistencia c 
            WHERE c.numeroAlumno = fm.numero AND c.fecha = '" . $diaCal['fecha'] . "') AS 'com" . $diaCal['fecha'] . "',
            ( SELECT fechaHoraRecibido FROM stControlAsistencia c 
            WHERE c.numeroAlumno = fm.numero AND c.fecha = '" . $diaCal['fecha'] . "') AS 'rec" . $diaCal['fecha'] . "', 
            ( SELECT asignacionManual FROM stControlAsistencia  
            WHERE numeroAlumno = fm.numero AND fecha = '" . $diaCal['fecha'] . "') AS 'man" . $diaCal['fecha'] . "' ";
        }
        
        // Cierra el cursor para liberar recursos
        $diasRecSet->closeCursor();

        // Consulta principal
        $sql = "SELECT au.Aula, fm.numero, CONCAT(fm.apellidos, ', ' , fm.nombre) as nombre, fm.curso, fm.horario, fm.dias, 
                    p.nombre as profesor, 
                    (select count(numeroAlumno) from stControlAsistencia  
     	                where fecha <= :fecha and  modoAsistencia = 'a' and numeroAlumno = fm.numero) as asiste, 
	                (select count(numeroAlumno) from stControlAsistencia  
     	                where fecha <= :fecha and  modoAsistencia = 'o' and numeroAlumno = fm.numero) as remoto " . // No se pone coma despues de este campo, ya la pone la subcadena
                $subCadenaDias . 
                " FROM stFM2021 fm 
                    INNER JOIN ssAulas au ON au.id = fm.idAula
                    LEFT JOIN stProfesores p ON p.id = fm.idProfesor
                WHERE 1 = 1 " . $filtroCentro . $filtroAula . $filtroDiaSemana . $filtroCurso . $filtroHorario .
                " GROUP BY au.Aula, fm.numero, fm.nombre, fm.apellidos, fm.curso, fm.horario, fm.dias
                ORDER BY fm.horario, idAula, fm.apellidos, fm.numero desc";
                 
        $recSet=$con->prepare($sql);
        $recSet->execute(array(':fecha'=>$fechaImparticion));
        return $recSet; 
    }

    // Devuelve los dias lectivos del calendario hasta la fecha recibida como parametro inclusive. 
    // Solo devuelve los dias pertenecientes al mes de la fecha.
    // Devuelve sólo los L-M ó M-J, segun el día de la fecha recibida.
    // Si el parametro $filtrarMes viene a true, solo muestra los dias del mes de la fechaHasta recibida.
    // Si $incluyeFechaHasta viene a true, incluye la $fechaHasta recibida, si no, se excluye.
    function CovGetDiasCalendario($fechaHasta, $filtrarMes = true, $incluyeFechaHasta = true){
        $filtroFecha = "";
        $diasSemana = 0;
        $mes = date("m", strtotime($fechaHasta));

        $filtroMes = $filtrarMes ? " AND month(fecha) = " . $mes : "";
        $filtroIncluyeFechaHasta = $incluyeFechaHasta ? " <= " : " < ";

        // Crea el filtro de fecha si ésta no es 0
        if($fechaHasta!=0){
            $diaSemana = date("N", strtotime($fechaHasta)); // obtiene el dia de la semana en cifra
            $filtroDias = "";
            if($diaSemana == 1 || $diaSemana == 3){ // Lunes y Miercoles
                $filtroDias = " AND (diaSemana = 1 OR diaSemana = 3) ";
            }elseif($diaSemana == 2 || $diaSemana == 4){ // Martes y Jueves
                $filtroDias = " AND (diaSemana = 2 OR diaSemana = 4) ";
            }else{ // Viernes
                $filtroDias = " AND diaSemana = 5 ";
            }
            $filtroFecha = " WHERE fecha " . $filtroIncluyeFechaHasta . "'" . $fechaHasta . "'" . $filtroMes . $filtroDias;
        }
        $con=PdoOpenCon();
        $sqlDias = "SELECT * FROM stCalendario" . $filtroFecha . " order by fecha"; 
        $diasRecSet=$con->prepare($sqlDias);
        $diasRecSet->execute();
        return $diasRecSet;
    }

    function CovGetDiasSemana(){
        $con=PdoOpenCon();
        $sql="SELECT * FROM ssDiasSemana";
        $recSet=$con->prepare($sql);
        $recSet->execute();
        return $recSet; 
    }


  
    // Devuelve el total de asistencias según el modo de asistencia, fecha, centro, aula, curso y horario recibido como parametro 
    // Esta funcion es llamada por otras dos funciones: CovGetTotalAsistidos(...) y CovGetTotalOnLine(...),  que suministran a 
    // ésta el modo de asistencia, 'a' ó 'o'
    function CovGetAsistenciasGeneradas($modoAsistencia, $fecha, $centro, $idAula, $curso, $horario){
        $con=PdoOpenCon();
        $sql="SELECT count(modoAsistencia) as modos
                from stControlAsistencia c 
                    inner join stFM2021 fm ON fm.numero = c.numeroAlumno
                WHERE c.modoAsistencia = '" . $modoAsistencia . "' and fm.centro = :centro and fm.idAula = :idAula 
                    and c.fecha = :fecha and fm.curso = :curso and fm.horario = :horario";
        $recSet=$con->prepare($sql);
        $recSet->execute(array(':centro'=>$centro, ':idAula'=>$idAula, ':curso'=>$curso, ':horario'=>$horario, ':fecha'=>$fecha));
        $cantidad=$recSet->fetch(PDO::FETCH_ASSOC);
        return $cantidad["modos"]; 
    }

    // Devuelve la cantidad total de asisitidos A CLASE en una fecha, centro, aula, curso y horario determinado
    function CovGetTotalAsistidos($fecha, $centro, $idAula, $curso, $horario){
        return CovGetAsistenciasGeneradas('a', $fecha, $centro, $idAula, $curso, $horario);
    }

    // Devuelve la cantidad total de asisitidos ONLINE en una fecha, centro, aula, curso y horario determinado
    function CovGetTotalOnLine($fecha, $centro, $idAula, $curso, $horario){
        return CovGetAsistenciasGeneradas('o', $fecha, $centro, $idAula, $curso, $horario);
    }

      // Devuelve la cantidad total de AUSENCIAS en una fecha, centro, aula, curso y horario determinado
      function CovGetTotalAusencias($fecha, $centro, $idAula, $curso, $horario){
        return CovGetAsistenciasGeneradas('n', $fecha, $centro, $idAula, $curso, $horario);
    }

    // Obtiene los alumnos de un grupo con el total de asistencias A CLASE y el total de asistencias ONLINE, ordenados por
    //  los alumnos que mas han asistidos a clase hasta los que menos. Estos totales son calculados con la fecha ANTERIOR  a la 
    // recibida como parametro $fecha, ya que las nuevas asignaciones se van a realizar en la fecha recibida.
    // La ultima columna _asignado_ obtiene los modos preasignados en la fecha. El algoritmo que asigna las asistencias automaticamente respetará esta asignacion si está preasignada.
    // Esta funcion es usada para asignarle la asitencia ON Line
    // por el mismo orden mencionado.
    function CovGetAsistenciasGrupo($fecha, $centro, $idAula, $curso, $horario){

        $diaSemana = date("N", strtotime($fecha)); // obtiene el dia de la semana en cifra
        $filtroDias = "";
        if($diaSemana == 1 || $diaSemana == 3){ // Lunes y Miercoles
            $filtroDias = " AND dias = 'M-W' ";
        }elseif($diaSemana == 2 || $diaSemana == 4){ // Martes y Jueves
            $filtroDias = " AND dias = 'T-TH' ";
        }
        
        $con=PdoOpenCon();
        $sql="SELECT fm.numero, fm.nombre, fm.apellidos, fm.url, fm.email, fm.comunicarAsistencia,
                    (select count(numeroAlumno) from stControlAsistencia 
                        where fecha < :fecha and  modoAsistencia = 'a' and numeroAlumno = fm.numero) as asiste, 
                    (select count(numeroAlumno) from stControlAsistencia 
                        where fecha < :fecha and  modoAsistencia = 'o' and numeroAlumno = fm.numero) as remoto, 
                    ( select  modoAsistencia from stControlAsistencia where numeroAlumno = fm.numero and fecha = :fecha) as asignado,
                    (select id from stControlAsistencia where numeroAlumno = fm.numero and fecha = :fecha) as idControlAsistencia
            FROM stFM2021 fm 
                INNER JOIN ssAulas au ON au.id = fm.idAula 
            WHERE 1 = 1 AND idCentro = :centro AND idAula = :idAula AND fm.curso = :curso and fm.horario = :horario " . $filtroDias . 
            " GROUP BY au.Aula, fm.numero, fm.nombre, fm.apellidos, fm.curso, fm.horario, fm.dias 
            ORDER BY remoto, fm.numero desc";
        
        // --- LINEA DE DEPURACION->echo "Consulta de los alumnos del grupo:" . $sql . "\n";            
        
        $recSet=$con->prepare($sql);
        $recSet->execute(array(':centro'=>$centro, ':idAula'=>$idAula, ':curso'=>$curso, ':horario'=>$horario, ':fecha'=>$fecha));
        return $recSet;
    }

    function covEsOnline($numeroAlumno, $fecha){
        $con=PdoOpenCon();
        $sql="select fecha, if(modoAsistenciaReal is null, 
                                        if(modoAsistencia = 'o', 1, 0), 
                                        if(modoAsistenciaReal = 'o', 1, 0)
                              ) as r
                from stControlAsistencia where numeroAlumno = :numero and fecha = :fecha";
        $recSet=$con->prepare($sql);
        $recSet->execute(array(':fecha'=>$fecha, ':numero'=>$numeroAlumno));
        if($alum=$recSet->fetch(PDO::FETCH_ASSOC)){
            return $alum['r'];
        }else{
            return false;
        }
    }



    function CovGrabaAsistencias($asistencias, $fecha){

        try {
            $con=PdoOpenCon();
            $con->beginTransaction();

            foreach($asistencias as $asist){
                if($asist['asignado']=='o'){
                    $sql = "insert into stControlAsistencia  
                                ( numeroAlumno, fecha, modoAsistencia)
                            values
                                ( " . $asist["numero"] . ", '" . $fecha . "', 'o')";
                    $con->exec($sql);

                }elseif($asist['asignado']=='a'){
                    $sql = "insert into stControlAsistencia  
                                    ( numeroAlumno, fecha, modoAsistencia)
                                values
                                    ( " . $asist["numero"] . ", '" . $fecha . "', 'a')";
                    $con->exec($sql);
                }
            }

            $con->commit();

        } catch (Exception $e) {

            $con->rollBack();
            echo $e->getMessage();

        }
       
    }

    function CovEliminarAsistencias($asistencias, $fecha){

        try {
            $con=PdoOpenCon();
            $con->beginTransaction();

            foreach($asistencias as $asist){

                $sql = "DELETE FROM stControlAsistencia WHERE numeroAlumno = " . $asist['numero'] .
                            " AND fecha = '" . $fecha . "' AND asignacionManual = 0";
                $con->exec($sql);

            }

            $con->commit();

        } catch (Exception $e) {

            $con->rollBack();
            echo $e->getMessage();

        }
       
    }



    // Devuelve, si existe, la asistencia de un alumno en una fecha. 
    // Esta funcion se usa en el panel donde se asigna el modo de asistencia a un alumno. Antes de asignarle un modo de asistencia
    //  hay que buscar a ver si existe ya una asistencia de ese alumno, para eso se usa esta función.
    function CovGetAsistencia($fecha, $numeroAlumno){
        $con=PdoOpenCon();
        $sql="SELECT * FROM stControlAsistencia c 
                WHERE c.fecha = :fecha AND c.numeroAlumno = :numeroAlumno ";
        $recSet=$con->prepare($sql);
        $recSet->execute(array(":fecha"=>$fecha, ":numeroAlumno"=>$numeroAlumno));
        return $recSet->fetch(PDO::FETCH_ASSOC);
    }

    function CovPutAsistencia($fecha, $numeroAlumno, $modoAsistencia, $asignacionManual){
        $con=PdoOpenCon();
        $sql="INSERT INTO stControlAsistencia (numeroAlumno, fecha, modoAsistencia, asignacionManual ) 
                VALUES (:numero, :fecha, :modo, :asignacionManual)";
        $recSet=$con->prepare($sql);
        $result=$recSet->execute(array(":numero"=>$numeroAlumno, 
                                        ":fecha"=>$fecha, 
                                        ":modo"=>$modoAsistencia,
                                        ":asignacionManual" => $asignacionManual
                                       )
                                );
        return $result; 
    }

    // Modifica el modo asistencia de un alumno en una fecha
    function CovUpdateAsistencia($fecha, $numeroAlumno, $modoAsistencia, $asignacionManual){
        $con=PdoOpenCon();
        $sql="UPDATE stControlAsistencia 
                SET modoAsistencia = :modoAsistencia, asignacionManual = :asignacionManual, fechaHoraComunicacion = Null,
                    fechaHoraRecibido = Null
                WHERE fecha = :fecha AND numeroAlumno = :numeroAlumno ";
        $recSet=$con->prepare($sql);
        $result=$recSet->execute(array(":fecha"=>$fecha, 
                                        ":numeroAlumno"=>$numeroAlumno, 
                                        ":modoAsistencia"=>$modoAsistencia,
                                        ":asignacionManual" => $asignacionManual
                                    )
                                );
        return $result; 
    }

    function CovDeleteAsistencia($fecha, $numeroAlumno){
        $con=PdoOpenCon();
        $sql="DELETE FROM stControlAsistencia
                WHERE fecha = :fecha AND numeroAlumno = :numeroAlumno ";
        $recSet=$con->prepare($sql);
        $result=$recSet->execute(array(":fecha"=>$fecha, ":numeroAlumno"=>$numeroAlumno));
        return $result; 
    }

    // function CovGetAllCursos(){
    //     $sql="select distinct curso from stFM2021 order by curso";
    //     $con=PdoOpenCon();
    //     $recSet=$con->prepare($sql);
    //     $recSet->execute(); 
    //     return $recSet; 
    // }

    // La variable $orden: si 1 ordena por horario que es valor por defecto, si otro valor ordena por curso
    function CovGetCursos($idCentro, $idAula, $diasSemana, $orden = 1){
        $filtroCentro = $idCentro != 0 ? " AND fm.centro = " . $idCentro : ""; 
        $filtroAula = $idAula != 0 ? " AND fm.idAula = " . $idAula : "";
        $filtroDias = empty($diasSemana) ? "" : " AND dias = '" . $diasSemana . "'"; 
        $sOrden = ($orden == 1) ? " ORDER BY fm.curso " : " ORDER BY fm.horario ";

        $sql="select distinct curso from stFM2021 fm
                where 1 = 1 " . $filtroCentro . $filtroDias . $filtroAula . $sOrden;  //fm.centro = :centro and fm.idAula = :idAula and fm.dias = :dias
        $con=PdoOpenCon();
        $recSet=$con->prepare($sql);
        $recSet->execute(); //array(":centro"=>$idCentro, ":idAula"=>$idAula, ":dias"=>$diasSemana));
        return $recSet; 
    }

    function CovGetHorarios($fecha, $idCentro, $idAula, $curso ){
        $diaSemana = empty($fecha) ? 0 : date("N", strtotime($fecha)); // obtiene el dia de la semana en cifra
        $filtroDias = "";
        if($diaSemana == 1 || $diaSemana == 3){ // Lunes y Miercoles
            $filtroDias = " AND dias = 'M-W' ";
        }elseif($diaSemana == 2 || $diaSemana == 4){ // Martes y Jueves
            $filtroDias = " AND dias = 'T-TH' ";
        }
        $filtroCentro = $idCentro > 0 ? " AND fm.centro = " . $idCentro : ""; 
        $filtroAula = $idAula > 0 ? " AND fm.idAula = " . $idAula : "";
        $filtroCurso = $curso != "" ? " AND fm.curso = '" . $curso . "' " : ""; 
        $con=PdoOpenCon();
        $sql="select distinct horario from stFM2021 fm
                where 1 = 1 " . $filtroCentro . $filtroAula . $filtroCurso . $filtroDias . 
                ' order by horario';
        $recSet=$con->prepare($sql);
        $recSet->execute();
        return $recSet; 
    }

    function CovGetAllHorarios(){
        $con=PdoOpenCon();
        $sql="select distinct horario from stFM2021 order by horario";
        $recSet=$con->prepare($sql);
        $recSet->execute();
        return $recSet; 
    }

    function CovPutFechaComunicacionEmail($fecha, $numeroAlumno, $fechaEmail){
        $con=PdoOpenCon();
        $sql="UPDATE stControlAsistencia 
                SET fechaHoraComunicacion = :fechaEmail
                WHERE fecha = :fecha AND numeroAlumno = :numeroAlumno ";
        $recSet=$con->prepare($sql);
        $result=$recSet->execute(array(":fecha"=>$fecha, 
                                        ":numeroAlumno"=>$numeroAlumno, 
                                        ":fechaEmail"=>$fechaEmail));
        return $result; 
    }

    // Devuelve el aforo de un grupo en concreto. Si no vienen todos los filtros devuelve NULL
    function CovAforoGrupo($fecha, $centro, $idAula, $curso, $horario){

        // Si se han recibido todos los filtros, ejecutra la consulta, si no, devuelve null
        if($centro != 0 && $idAula != 0 && $curso != '' && $horario != '' && $fecha != 0){

            // esto es redundante, se podria quitar y poner los filtros directamente en la consulta.
            $filtroCentro = $centro > 0 ? " AND centro = " . $centro : "";
            $filtroAula = $idAula > 0 ? " AND idAula = " . $idAula : "";
            $filtroCurso = $curso != '' ? " AND curso = '" . $curso . "' " : ""; // * ojo, $curso != '' devuelve true pero $curso != 0 devuelve false ¿¿¿por qué????
            $filtroHorario = $horario != '' ? " AND horario = '" . $horario . "' " : ""; //* sin emabargo $horario != 0 devuelve true, tiene cojones.

            $diaSemana = date("N", strtotime($fecha)); // obtiene el dia de la semana en cifra
            $filtroDias = "";
            if($diaSemana == 1 || $diaSemana == 3){ // Lunes y Miercoles
                $filtroDias = " AND dias = 'M-W' ";
            }elseif($diaSemana == 2 || $diaSemana == 4){ // Martes y Jueves
                $filtroDias = " AND dias = 'T-TH' ";
            }
            $con=PdoOpenCon();
            $sql="SELECT * FROM viewGrupos WHERE 1 = 1 " . $filtroCentro . $filtroAula . $filtroCurso .
                $filtroHorario . $filtroDias;
            // where centro = :centro and idAula = :idAula 
              //      and dias = :dias and horario = :horario and curso = :curso";

            $recSet=$con->prepare($sql);
            $recSet->execute(); //array(":dias"=>$dias, 
                                         //   ":centro"=>$centro, 
                                           // ":idAula"=>$idAula,
                                            //":curso"=>$curso,
                                            //":horario"=>$horario));
            return $recSet; 

        }else{
            return null;
        }

     
      

    }

    function CovGrabacionAlumno($numero, $nombre, $apellidos, $centro, $idAula, $curso, $horario, $dias, $email, $email2, 
                                $url, $idProfesor){
        $con=PdoOpenCon();
        $sql="INSERT INTO stFM2021 (numero, nombre, apellidos, centro, idAula, curso, horario, dias, email, email2, url, idProfesor ) 
                VALUES (:numero, :nombre, :apellidos, :centro, :idAula, :curso, :horario, :dias, :email, :email2, :url, :idProfesor )";
        $op=$con->prepare($sql);
        $result=$op->execute(array(":numero"=>$numero, ":nombre"=>$nombre, ":apellidos"=>$apellidos, ":centro"=> $centro, 
                                    ":idAula"=> $idAula, ":curso"=>$curso, ":horario"=>$horario, ":dias"=>$dias, 
                                    ":email"=>$email, ":email2"=>$email2, ":url"=>$url, ":idProfesor" => $idProfesor));
        return $result; 
    }

    function CovModificacionAlumno($numero, $nombre, $apellidos, $centro, $idAula, $curso, $horario, $dias, $email, $email2,
                                     $url, $idProfesor){
        $con=PdoOpenCon();
        $sql="UPDATE stFM2021 SET nombre = :nombre, apellidos = :apellidos, centro = :centro, 
                        idAula = :idAula, curso = :curso, horario = :horario, dias = :dias, email = :email, 
                        email2 = :email2, url = :url, idProfesor = :idProfesor WHERE numero = :numero";
        $op=$con->prepare($sql);
        $result=$op->execute(array(":numero"=>$numero, ":nombre"=>$nombre, ":apellidos"=>$apellidos, ":centro"=> $centro, 
                                    ":idAula"=> $idAula, ":curso"=>$curso, ":horario"=>$horario, ":dias"=>$dias, 
                                    ":email"=>$email, ":email2"=>$email2, ":url"=>$url, ":idProfesor" => $idProfesor));
        return $result; 
    }

    function CovEliminarAlumno($numero){

        try {
            $con=PdoOpenCon();
            $con->beginTransaction();


            // Elimina el alumno
            $sql = "DELETE FROM stFM2021 WHERE numero = " . $numero;
            $con->exec($sql);

            // Elimina las asistencias del alumno
            $sql = "DELETE FROM stControlAsistencia WHERE numeroAlumno = " . $numero;
            $con->exec($sql);

            $con->commit();

        } catch (\Throwable $e) {

            $con->rollBack();
            throw $e;
        }

        return true;
       
    }

    function CovGetAlumno($numero){
        $con=PdoOpenCon();
        $sql="SELECT * FROM stFM2021 WHERE numero = :numero";
        $recSet=$con->prepare($sql);
        $recSet->execute(array(":numero"=>$numero));
        return $recSet; 
    }


    function CovGetTotalAlumnos($centro){
        $con=PdoOpenCon();
        $sql="SELECT count(*) as total FROM stFM2021 WHERE centro = :centro";
        $recSet=$con->prepare($sql);
        $recSet->execute(array(":centro"=>$centro));
        return $recSet->fetch(PDO::FETCH_ASSOC);
    }

    

 
    







?>
