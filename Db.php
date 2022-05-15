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
            $sql="SELECT * FROM alumnos WHERE id = :id";
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

    function GetPromocion($id){
        $con=PdoOpenCon();
        $sql="SELECT * FROM ssPromociones where id = " . $id; 
        $recSet=$con->prepare($sql);
        $recSet->execute();
        return $recSet->fetch(PDO::FETCH_ASSOC); 
    }

    function GetProfesores(){
        $con=PdoOpenCon();
        $sql="SELECT * FROM stProfesores order by nombre"; 
        $recSet=$con->prepare($sql);
        $recSet->execute();
        return $recSet; 
    }

    // 08/09/21.- Para agregar un combo de promociones para el curso 21/22
    function GetPromociones(){
        $con=PdoOpenCon();
        $sql="SELECT * FROM ssPromociones order by promocion desc"; 
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
                                    ELT(WEEKDAY(fecha) + 1, 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab', 'Dom'), 
                                    ' - ', lpad(day(fecha),2,0), '/', lpad(month(fecha),2,0), '/', year(fecha)
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
    function GetGruposAInformar($fechaImparticion, $centro, $idAula, $curso, $horario, $idPromocion){
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
                WHERE idPromocion = " . $idPromocion . $filtroDias . $filtroCentro . $filtroAula . $filtroCurso . $filtroHorario;
        
        // --- LINEA DE DEPURACION-> echo "Consulta del Grupo: " . $sql . "\n"; 
        
        $recSet=$con->prepare($sql);
        $recSet->execute();
        return $recSet; 
    }

    // Devuelve coleccion con los los alumnos de un grupo en concreto por orden de asistencia online
    //  donde los primeros son los que MENOS han echo clases online. Los alumnos que hayan hecho las mismas clases online se 
    //   ordenan por orden descendente segun su numero, por poner un orden.
    // Params: $fechaImparticion.- Tomará los datos con fecha anterior a ésta 
    function GetCuadranteMatriculas($centro, $idAula, $fechaImparticion, $idCurso, $idHorario, $idPromocion){
        
    // LINEA DE DEPURACION var_dump($centro, $idAula, $fechaImparticion, $curso, $horario, $idPromocion);

        $mes = date("m", strtotime($fechaImparticion));
        $filtroCentro = $centro > 0 ? " AND au.idCentro = " . $centro : "";
        $filtroAula = $idAula > 0 ? " AND idAula = " . $idAula : "";
        $filtroCurso = $idCurso == 0  ? "" : " AND idCurso = " . $idCurso ;
        $filtroHorario = $idHorario == 0 ? "" : " AND idHorario = " . $idHorario ;

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
        $subCadenaDias = "";
        // solo obtiene los días del calendario si la fecha tiene un valor, si no, no los carga porque cargaría todos los días y la consulta se hace muy grande y muy pesada
        $diasRecSet=GetDiasCalendario($idPromocion, $fechaImparticion);
        while($diaCal=$diasRecSet->fetch(PDO::FETCH_ASSOC)){
            $subCadenaDias = $subCadenaDias . ", ( SELECT modoAsistencia FROM stControlAsistencia c1 
            WHERE c1.idMatricula = fm.id AND c1.fecha = '" . $diaCal['fecha'] . "') AS '" . $diaCal['fecha'] . "', 
            ( SELECT modoAsistenciaReal FROM stControlAsistencia c2
            WHERE c2.idMatricula = fm.id AND c2.fecha = '" . $diaCal['fecha'] . "') AS 'real" . $diaCal['fecha'] . "',
            ( SELECT fechaHoraComunicacion FROM stControlAsistencia c3 
            WHERE c3.idMatricula = fm.id AND c3.fecha = '" . $diaCal['fecha'] . "') AS 'com" . $diaCal['fecha'] . "',
            ( SELECT fechaHoraRecibido FROM stControlAsistencia c4
            WHERE c4.idMatricula = fm.id AND c4.fecha = '" . $diaCal['fecha'] . "') AS 'rec" . $diaCal['fecha'] . "', 
            ( SELECT asignacionManual FROM stControlAsistencia c5
            WHERE c5.idMatricula = fm.id AND c5.fecha = '" . $diaCal['fecha'] . "') AS 'man" . $diaCal['fecha'] . "' ";
        }
        // Cierra el cursor para liberar recursos
        $diasRecSet->closeCursor();

        // Consulta principal
        $sql = "SELECT fm.id as idMatricula, au.Aula, alu.numeroAlumno as numero, CONCAT(alu.apellidos, ', ' , alu.Nombre) as nombre, fm.idCurso, cur.descripcion as curso,
                 fm.idHorario, hor.horario, fm.dias, p.nombre as profesor, fm.comunicarAsistencia, fm.idAlumno,
                    (select ifnull(enviado1,-1) from notas where idMatricula = fm.id) as notasEnviadas,
                    (  
                        (select count(idMatricula) 
                            from stControlAsistencia  
                            where fecha <= :fecha 
                                and modoAsistencia = 'a' 
                                and modoAsistenciaReal is null 
                                and idMatricula= fm.id)     + 
                                                                        (select count(idMatricula) 
                                                                            from stControlAsistencia  
                                                                            where fecha <= :fecha 
                                                                                and modoAsistenciaReal = 'a' 
                                                                                and idMatricula = fm.id)    
                    ) as totalPresenciales, 
	                (
                        (select count(idMatricula) 
                            from stControlAsistencia  
     	                    where fecha <= :fecha 
                                and modoAsistencia = 'o' 
                                and modoAsistenciaReal is null
                                and idMatricula = fm.id)     + 
                                                                        (select count(idMatricula) 
                                                                            from stControlAsistencia  
                                                                            where fecha <= :fecha 
                                                                                and  modoAsistenciaReal = 'o' 
                                                                                and idMatricula = fm.id)
                    ) as totalRemotos,
                    (
                        (select count(idMatricula) 
                            from stControlAsistencia  
     	                    where fecha <= :fecha 
                                and modoAsistencia = 'n' 
                                and modoAsistenciaReal is null
                                and idMatricula = fm.id)     + 
                                                                        (select count(idMatricula) 
                                                                            from stControlAsistencia  
                                                                            where fecha <= :fecha 
                                                                                and  modoAsistenciaReal = 'n' 
                                                                                and idMatricula = fm.id)
                    ) as totalAusencias" . // No se pone coma despues de este campo, ya la pone la subcadena
                $subCadenaDias . 
                " FROM matriculas fm 
                    INNER JOIN ssAulas au ON au.id = fm.idAula
                    INNER JOIN alumnos alu on alu.id = fm.idAlumno
                    INNER JOIN  sscursos cur on cur.id = fm.idCurso
                    INNER JOIN ssHorarios hor on hor.id = fm.idHorario
                    LEFT JOIN stProfesores p ON p.id = fm.idProfesor
                WHERE fechaBaja is null AND idPromocion = :idPromocion " . $filtroCentro . $filtroAula . $filtroDiaSemana . $filtroCurso . $filtroHorario .
                " GROUP BY au.Aula, alu.numeroAlumno, alu.nombre, alu.apellidos, fm.idCurso, fm.idHorario, fm.dias
                ORDER BY fm.horario, idAula, alu.apellidos, alu.numeroAlumno desc";
// var_dump($sql);                 
        $recSet=$con->prepare($sql);
        $recSet->execute(array(':fecha' => $fechaImparticion, ':idPromocion' => $idPromocion ));
        return $recSet; 
    }






    // Devuelve los dias lectivos del calendario hasta la fecha recibida como parametro inclusive. 
    // Solo devuelve los dias pertenecientes al mes de la fecha.
    // Devuelve sólo los L-M ó M-J, segun el día de la fecha recibida.
    // Si el parametro $filtrarMes viene a true, solo muestra los dias del mes de la fechaHasta recibida.
    // Si $incluyeFechaHasta viene a true, incluye la $fechaHasta recibida, si no, se excluye.
    function GetDiasCalendario($idPromocion, $fechaHasta, $filtrarMes = true, $incluyeFechaHasta = true){
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
            $filtroFecha = " AND fecha " . $filtroIncluyeFechaHasta . "'" . $fechaHasta . "'" . $filtroMes . $filtroDias;
        }
        $con=PdoOpenCon();
        $sqlDias = "SELECT * FROM stCalendario WHERE idPromocion = " . $idPromocion . $filtroFecha . " order by fecha"; 
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
    function GetAsistenciasGeneradas($modoAsistencia, $fecha, $centro, $idAula, $curso, $horario, $idPromocion){
        $con=PdoOpenCon();
        $sql="SELECT count(modoAsistencia) as modos
                from stControlAsistencia c 
                    inner join matriculas fm ON fm.id = c.idMatricula
                WHERE c.modoAsistencia = '" . $modoAsistencia . "' and fm.idCentro = :centro and fm.idAula = :idAula 
                    and c.fecha = :fecha and fm.curso = :curso and fm.horario = :horario and fm.idPromocion = " . $idPromocion;
        $recSet=$con->prepare($sql);
        $recSet->execute(array(':centro'=>$centro, ':idAula'=>$idAula, ':curso'=>$curso, ':horario'=>$horario, ':fecha'=>$fecha));
        $cantidad=$recSet->fetch(PDO::FETCH_ASSOC);
        return $cantidad["modos"]; 
    }

    // Devuelve la cantidad total de asisitidos A CLASE en una fecha, centro, aula, curso y horario determinado
    function GetTotalAsistidos($fecha, $centro, $idAula, $curso, $horario, $idPromocion){
        return GetAsistenciasGeneradas('a', $fecha, $centro, $idAula, $curso, $horario, $idPromocion);
    }

    // Devuelve la cantidad total de asisitidos ONLINE en una fecha, centro, aula, curso y horario determinado
    function GetTotalOnLine($fecha, $centro, $idAula, $curso, $horario, $idPromocion){
        return GetAsistenciasGeneradas('o', $fecha, $centro, $idAula, $curso, $horario, $idPromocion);
    }

      // Devuelve la cantidad total de AUSENCIAS en una fecha, centro, aula, curso y horario determinado
    function GetTotalAusencias($fecha, $centro, $idAula, $curso, $horario, $idPromocion){
        return GetAsistenciasGeneradas('n', $fecha, $centro, $idAula, $curso, $horario, $idPromocion);
    }

    // Obtiene los alumnos de un grupo con el total de asistencias A CLASE y el total de asistencias ONLINE, ordenados por
    //  los alumnos que mas han asistidos a clase hasta los que menos. Estos totales son calculados con la fecha ANTERIOR  a la 
    // recibida como parametro $fecha, ya que las nuevas asignaciones se van a realizar en la fecha recibida.
    // La ultima columna _asignado_ obtiene los modos preasignados en la fecha. El algoritmo que asigna las asistencias automaticamente respetará esta asignacion si está preasignada.
    // Esta funcion es usada para asignarle la asitencia ON Line
    // por el mismo orden mencionado.
    function GetAsistenciasGrupo($fecha, $centro, $idAula, $curso, $horario, $idPromocion){

        $diaSemana = date("N", strtotime($fecha)); // obtiene el dia de la semana en cifra
        $filtroDias = "";
        if($diaSemana == 1 || $diaSemana == 3){ // Lunes y Miercoles
            $filtroDias = " AND dias = 'M-W' ";
        }elseif($diaSemana == 2 || $diaSemana == 4){ // Martes y Jueves
            $filtroDias = " AND dias = 'T-TH' ";
        }
        
        $con=PdoOpenCon();
        $sql="SELECT fm.id as idMatricula, alu.numeroAlumno as numero, alu.Nombre, alu.Apellidos, fm.url, fm.email, fm.comunicarAsistencia,
                    (select count(idMatricula) from stControlAsistencia 
                        where fecha < :fecha 
                            and idMatricula = fm.id
                            and modoAsistencia = 'a'
                            and modoAsistenciaReal is null )  + 
                                                                (select count(idMatricula) from stControlAsistencia 
                                                                    where fecha < :fecha 
                                                                        and idMatricula = fm.id
                                                                        and modoAsistenciaReal = 'a')
                        as asiste, 

                    (select count(idMatricula) from stControlAsistencia 
                        where fecha < :fecha 
                            and idMatricula = fm.id
                            and modoAsistencia = 'o'
                            and modoAsistenciaReal is null )  + 
                                                                (select count(idMatricula) from stControlAsistencia 
                                                                    where fecha < :fecha 
                                                                        and idMatricula = fm.id
                                                                        and modoAsistenciaReal = 'o')
                        as remoto, 

                    ( select  modoAsistencia from stControlAsistencia where idMatricula = fm.id and fecha = :fecha) as asignado,

                    (select id from stControlAsistencia where idMatricula = fm.id and fecha = :fecha) as idControlAsistencia
                FROM matriculas fm 
                    INNER JOIN ssAulas au ON au.id = fm.idAula 
                    INNER JOIN alumnos alu ON alu.id = fm.idAlumno
                WHERE idPromocion = " . $idPromocion . " AND fm.idCentro = :centro AND idAula = :idAula AND fm.curso = :curso and fm.horario = :horario " . $filtroDias . 
                " GROUP BY au.Aula, alu.numeroAlumno, alu.nombre, alu.apellidos, fm.curso, fm.horario, fm.dias 
                ORDER BY remoto, alu.numeroAlumno desc";
        
        // --- LINEA DE DEPURACION->echo "Consulta de los alumnos del grupo:" . $sql . "\n";            
        
        $recSet=$con->prepare($sql);
        $recSet->execute(array(':centro'=>$centro, ':idAula'=>$idAula, ':curso'=>$curso, ':horario'=>$horario, ':fecha'=>$fecha));
        return $recSet;
    }

    // Devuelve 2 si el dia de la fecha hizo ONLINE (o tenia previsto ONLINE si no tenia confirmada la asistencia)
    //  Devuelve 1 si el dia de la fecha tenia previsto ONLINE pero no Asistió 
    // Devuelve 0 para los demas casos
    // function getPuntosPorOnLine($numeroAlumno, $fecha){
    //     $con=PdoOpenCon();
    //     $sql="select fecha, if(modoAsistenciaReal is null, 
    //                                     if(modoAsistencia = 'o', 
    //                                             2,
    //                                             0), 
    //                                     if(modoAsistenciaReal = 'o', 
    //                                             2, 
    //                                             if(modoAsistenciaReal = 'n' and modoAsistencia = 'o',
    //                                                         1, 
    //                                                         0)
    //                                     )
    //                         ) as puntos
    //             from stControlAsistencia where numeroAlumno = :numero and fecha = :fecha";
    //     $recSet=$con->prepare($sql);
    //     $recSet->execute(array(':fecha'=>$fecha, ':numero'=>$numeroAlumno));
    //     if($reg=$recSet->fetch(PDO::FETCH_ASSOC)){
    //         return $reg['puntos'];
    //     }else{
    //         return 0;
    //     }
    // }



    function GrabaAsistencias($asistencias, $fecha){

        try {
            $con=PdoOpenCon();
            $con->beginTransaction();

            foreach($asistencias as $asist){

                if( $asist['asignado'] == 'o' || $asist['asignado'] == 'a' ){
                    $sql = "insert into stControlAsistencia  
                                ( numeroAlumno, fecha, modoAsistencia, idMatricula)
                            values
                                ( " . $asist["numero"] . ", '" . $fecha . "', '" . $asist['asignado'] . "', " . $asist["idMatricula"] . ")";
                    $con->exec($sql);
                }

            }

            $con->commit();



        } catch (Exception $e) {

            $con->rollBack();
            echo $e->getMessage();

        }
       
    }

    function EliminarAsistencias($asistencias, $fecha){

        try {
            $con=PdoOpenCon();
            $con->beginTransaction();

            foreach($asistencias as $asist){

                $sql = "DELETE FROM stControlAsistencia WHERE idMatricula = " . $asist['idMatricula'] .
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
    function GetAsistencia($fecha, $idMatricula){
        $con=PdoOpenCon();
        $sql="SELECT * FROM stControlAsistencia c 
                WHERE c.fecha = :fecha AND c.idMatricula = :idMatricula ";
        $recSet=$con->prepare($sql);
        $recSet->execute(array(":fecha"=>$fecha, ":idMatricula"=>$idMatricula));
        return $recSet->fetch(PDO::FETCH_ASSOC);
    }

    function PutAsistencia($fecha, $idMatricula, $modoAsistencia, $asignacionManual){
        $con=PdoOpenCon();
        $sql="INSERT INTO stControlAsistencia (numeroAlumno, fecha, modoAsistencia, asignacionManual, idMatricula ) 
                VALUES (:numero, :fecha, :modo, :asignacionManual, :idMatricula)";
        $recSet=$con->prepare($sql);
        $result=$recSet->execute(array(":numero"=>$idMatricula, // este campo se podria omitir, pero por respetar la grabacion antigua...
                                        ":fecha"=>$fecha, 
                                        ":modo"=>$modoAsistencia,
                                        ":asignacionManual" => $asignacionManual,
                                        ":idMatricula"=> $idMatricula
                                       )
                                );
        return $result; 
    }

    // Modifica el modo asistencia de un alumno en una fecha
    function UpdateAsistencia($fecha, $idMatricula, $modoAsistencia, $asignacionManual){
        $con=PdoOpenCon();
        $sql="UPDATE stControlAsistencia 
                SET modoAsistencia = :modoAsistencia, asignacionManual = :asignacionManual, fechaHoraComunicacion = Null,
                    fechaHoraRecibido = Null
                WHERE fecha = :fecha AND idMatricula = :idMatricula ";
        $recSet=$con->prepare($sql);
        $result=$recSet->execute(array(":fecha"=>$fecha, 
                                        ":idMatricula"=>$idMatricula, 
                                        ":modoAsistencia"=>$modoAsistencia,
                                        ":asignacionManual" => $asignacionManual
                                    )
                                );
        return $result; 
    }

    function DeleteAsistencia($fecha, $idMatricula){
        $con=PdoOpenCon();
        $sql="DELETE FROM stControlAsistencia
                WHERE fecha = :fecha AND idMatricula = :idMatricula ";
        $recSet=$con->prepare($sql);
        $result=$recSet->execute(array(":fecha"=>$fecha, ":idMatricula"=>$idMatricula));
        return $result; 
    }

    // function CovGetAllCursos(){
    //     $sql="select distinct curso from stFM2021 order by curso";
    //     $con=PdoOpenCon();
    //     $recSet=$con->prepare($sql);
    //     $recSet->execute(); 
    //     return $recSet; 
    // }

    // esta funcion la quito el 6/5/22 porque no se llama ya desde ningun sitio y construyo la que viene a continuacion para el curso 22-23 con una nueva optimizacion de la tabla 
    //      "matriculas" que no incluia el campo idCurso y que se incluye para esta promocion
    // function GetCursos($idCentro, $idAula){

    //     // Crea el filtro del Aula. Este filtro hay que activarlo tanto si filtra el Aula como si filtra el Centro, 
    //     // ya que las clausulas INNER para enlazar con las aulas es intermediaria para enlazar con los centros. 
    //     $filtroInnerAula = $idAula > 0 || $idCentro > 0
    //         ? " INNER JOIN Grupos g ON g.IdCurso = cur.id
    //             INNER JOIN ssAulas au ON au.id = g.IdAula "
    //         : "";
    //     $filtroWhereAula = $idAula > 0 ? " AND au.id = " . $idAula  : "";
        
    //     // Crea el filtro del Centro
    //     $filtroInnerCentro = $idCentro > 0 
    //         ? " INNER JOIN ssCentros cen ON cen.id = au.idCentro  "
    //         : "";
    //     $filtroWhereCentro = $idCentro > 0 ? ' AND au.idCentro = :idCentro ' : '';

        
    //     // Monta la cadena completa de la consulta
    //     $sql="SELECT DISTINCT cur.* FROM sscursos cur " . $filtroInnerAula . $filtroInnerCentro . 
    //         " WHERE 1 = 1 " . $filtroWhereCentro . $filtroWhereAula . " ORDER BY Descripcion";

    //         echo $sql;

    //     // Abre conexion, prepara la consulta, la ejecuta y devuelve el array
    //     $con=PdoOpenCon();
    //     $recSet=$con->prepare($sql);
    //     $recSet->execute(array(":idCentro"=>$idCentro)); //, ":idAula"=>$idAula));
    //     return $recSet; 
    // }

    // esta fucnion la creé para el curso 22-23
    function GetCursos(){

        // cadena de la consulta
        $sql="SELECT * FROM sscursos ORDER BY Descripcion";

        // Abre conexion, prepara la consulta, la ejecuta y devuelve el array
        $con = PdoOpenCon();
        $recSet = $con->prepare($sql);
        $recSet->execute(); //, ":idAula"=>$idAula));
        return $recSet; 
    }

    // La variable $orden: si 1 ordena por horario que es valor por defecto, si otro valor ordena por curso
    function GetCursosFromMatriculas($idPromocion, $idCentro, $idAula, $diasSemana, $orden = 1 ){
        $filtroCentro = $idCentro != 0 ? " AND fm.idCentro = " . $idCentro : ""; 
        $filtroAula = $idAula != 0 ? " AND fm.idAula = " . $idAula : "";
        $filtroDias = empty($diasSemana) ? "" : " AND dias = '" . $diasSemana . "'"; 
        $sOrden = ($orden == 1) ? " ORDER BY fm.curso " : " ORDER BY fm.horario ";

        $sql="select distinct curso from matriculas fm
                where idPromocion = " . $idPromocion . $filtroCentro . $filtroDias . $filtroAula . $sOrden;  //fm.centro = :centro and fm.idAula = :idAula and fm.dias = :dias
        $con=PdoOpenCon();
        $recSet=$con->prepare($sql);
        $recSet->execute(); //array(":centro"=>$idCentro, ":idAula"=>$idAula, ":dias"=>$diasSemana));
        return $recSet; 
    }

    function CovGetHorarios($fecha, $idCentro, $idAula, $curso, $idPromocion ){
        $diaSemana = empty($fecha) ? 0 : date("N", strtotime($fecha)); // obtiene el dia de la semana en cifra
        $filtroDias = "";
        if($diaSemana == 1 || $diaSemana == 3){ // Lunes y Miercoles
            $filtroDias = " AND dias = 'M-W' ";
        }elseif($diaSemana == 2 || $diaSemana == 4){ // Martes y Jueves
            $filtroDias = " AND dias = 'T-TH' ";
        }
        $filtroCentro = $idCentro > 0 ? " AND fm.idCentro = " . $idCentro : ""; 
        $filtroAula = $idAula > 0 ? " AND fm.idAula = " . $idAula : "";
        $filtroCurso = $curso != "" ? " AND fm.curso = '" . $curso . "' " : ""; 
        $con=PdoOpenCon();
        $sql="select distinct horario from matriculas fm
                where idPromocion = " . $idPromocion . $filtroCentro . $filtroAula . $filtroCurso . $filtroDias . 
                ' order by horario';
        $recSet=$con->prepare($sql);
        $recSet->execute();
        return $recSet; 
    }

    // -> 7/5/22.- para la promocion 22-23 no se muestran los horarios de de la tabla matriculas sino la tabla "sshorarios"
    // function GetAllHorarios($idPromocion){
    //     $con=PdoOpenCon();
    //     $sql="select distinct horario from matriculas where idPromocion = " . $idPromocion . " order by horario";
    //     $recSet=$con->prepare($sql);
    //     $recSet->execute();
    //     return $recSet; 
    // }
    function GetAllHorarios(){
        $con=PdoOpenCon();
        $sql="select * from ssHorarios order by horario";
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
    function AforoGrupo($fecha, $centro, $idAula, $curso, $horario, $idPromocion){

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
            $sql="SELECT * FROM viewGrupos WHERE idPromocion = " . $idPromocion . $filtroCentro . $filtroAula . $filtroCurso .
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

    function GrabacionAlumno($numero, $nombre, $apellidos, $codigoPostal, $domicilio, $email, $email2, $fechaNacimiento, $municipio, $nif, $observaciones){
        $con=PdoOpenCon();
        $sql="INSERT INTO alumnos (NumeroAlumno, nombre, apellidos, CodigoPostal, Domicilio, emailPrincipal, emailTutor2, FechaNacimiento, Municipio, NIf, Observaciones) 
                VALUES (:numero, :nombre, :apellidos, :codigoPostal, :domicilio, :email, :email2, :fechaNacimiento, :municipio, :nif, :observaciones )";
        $op=$con->prepare($sql);
        $result=$op->execute(array(":numero"=>$numero, ":nombre"=>$nombre, ":apellidos"=>$apellidos, ":codigoPostal"=>$codigoPostal, ":domicilio"=>$domicilio, ":email"=>$email, 
                                    ":email2"=>$email2, ":fechaNacimiento"=>$fechaNacimiento, ":municipio"=>$municipio, ":nif"=>$nif, ":observaciones"=>$observaciones )
                            );
        return $result; 
    }

    function ModificacionAlumno($id, $nombre, $apellidos, $codigoPostal, $domicilio, $email, $email2, $fechaNacimiento, $municipio, $nif, $observaciones){
        $con=PdoOpenCon();
        $sql="UPDATE alumnos SET nombre = :nombre, apellidos = :apellidos, CodigoPostal = :codigoPostal, Domicilio = :domicilio, 
                    emailPrincipal = :email, emailTutor2 = :email2, FechaNacimiento = :fechaNacimiento, Municipio = :municipio, Nif = :nif, 
                    Observaciones = :observaciones   
                WHERE id = :id";
        $op=$con->prepare($sql);
        $result=$op->execute(array(":id"=>$id, ":nombre"=>$nombre, ":apellidos"=>$apellidos, ":codigoPostal"=> $codigoPostal, 
                                    ":domicilio"=> $domicilio, ":email"=>$email, ":email2"=>$email2, ":fechaNacimiento"=>$fechaNacimiento, 
                                    ":municipio"=>$municipio, ":nif"=>$nif, ":observaciones"=>$observaciones)
                            );
        return $result; 
    }


    function BajaMatricula($idMatricula, $fechaBaja){

        try {
            $con=PdoOpenCon();
            $sql = "UPDATE matriculas set fechaBaja = :fechaBaja WHERE id = :id";
            $op = $con->prepare($sql);
            $result = $op->execute(     array ( ":id" => $idMatricula, ":fechaBaja" => $fechaBaja )    );
            return $result; 
    
        } catch (\Throwable $e) {

            throw $e;
        }


    }

    function EliminarMatricula($idMatricula){

        try {
            $con=PdoOpenCon();
            $con->beginTransaction();


            // Elimina el alumno
            $sql = "DELETE FROM matriculas WHERE id = " . $idMatricula;
            $con->exec($sql);

            // Elimina las asistencias del alumno
            $sql = "DELETE FROM stControlAsistencia WHERE idMatricula = " . $idMatricula;
            $con->exec($sql);

            $con->commit();

        } catch (\Throwable $e) {

            $con->rollBack();
            throw $e;
        }

        return true;
       
    }

    function GetAlumno($numeroAlumno){
        $con=PdoOpenCon();
        $sql="SELECT * FROM alumnos WHERE NumeroAlumno = :numeroAlumno";
        $recSet=$con->prepare($sql);
        $recSet->execute(array(":numeroAlumno"=>$numeroAlumno));
        return $recSet; 
    }

    function GetAlumnoPorMatricula($idMatricula){
        $con=PdoOpenCon();
        $sql="SELECT a.*, p.nombre as profesor, m.curso, m.horario, m.dias, prom.Promocion  FROM alumnos a 
                                inner join matriculas m on a.id = m.idAlumno 
                                inner join stProfesores p on p.id = m.idProfesor
                                inner join ssPromociones prom on prom.Id = m.idPromocion
                WHERE m.id = :idMatricula";
        $recSet=$con->prepare($sql);
        $recSet->execute(array(":idMatricula"=>$idMatricula));
        return $recSet; 
    }


    // busca una matricula según el id del alumno y la id promocion
    function GetMatricula($idAlumno, $idPromocion, $excluirBajas){

        $filtroExcluirBajas = ( $excluirBajas ) ? ' AND fechaBaja is null  ' : '' ;

        $con=PdoOpenCon();
        $sql="SELECT * FROM matriculas WHERE idAlumno = :idAlumno AND idpromocion = :idPromocion" . $filtroExcluirBajas;
        $recSet=$con->prepare($sql);
        $recSet->execute(array(":idAlumno"=>$idAlumno, ":idPromocion"=>$idPromocion));
        return $recSet; 
    }

    function GrabacionMatricula($idAlumno, $idPromocion, $comunicarAsistencia, $idCurso, $dias, $email, $email2, $idHorario, $idAula, $idCentro, $idProfesor, $url){
        $con=PdoOpenCon();
        $sql="INSERT INTO matriculas (idAlumno, idPromocion, comunicarAsistencia, idCurso, dias, email, email2, idHorario, idAula, idCentro, idProfesor, url ) 
                VALUES (:idAlumno, :idPromocion, :comunicarAsistencia, :idCurso, :dias, :email, :email2, :idHorario, :idAula, :idCentro, :idProfesor, :url  )";
        $op=$con->prepare($sql);
        $result=$op->execute(   array(":idAlumno"=>$idAlumno, ":idPromocion"=>$idPromocion, ":comunicarAsistencia"=> $comunicarAsistencia, ":idCurso"=>$idCurso, 
                                   ":dias"=>$dias, ":email"=>$email, ":email2"=>$email2, ":idHorario"=>$idHorario, ":idAula"=>$idAula, ":idCentro"=>$idCentro, 
                                   ":idProfesor"=>$idProfesor, ":url"=>$url)      );
        return $result; 
    }

    function ModificacionMatricula($idAlumno, $idPromocion, $comunicarAsistencia, $idCurso, $dias, $email, $email2, $idHorario, $idAula, $idCentro, $idProfesor, $url){
        $con=PdoOpenCon();
        $sql="UPDATE matriculas SET comunicarAsistencia = :comunicarAsistencia, idCurso = :idCurso, dias = :dias, email = :email, email2 = :email2, idHorario = :idHorario,
                                    idAula = :idAula, idCentro = :idCentro, idProfesor = :idProfesor, url = :url
                WHERE idAlumno = :idAlumno AND idPromocion = :idPromocion";
        $op=$con->prepare($sql);
        $result=$op->execute(   array(":idAlumno"=>$idAlumno, ":idPromocion"=>$idPromocion, ":comunicarAsistencia"=> $comunicarAsistencia, ":idCurso"=>$idCurso, 
                                ":dias"=>$dias, ":email"=>$email, ":email2"=>$email2, ":idHorario"=>$idHorario, ":idAula"=>$idAula, ":idCentro"=>$idCentro, 
                                ":idProfesor"=>$idProfesor, ":url"=>$url)      );
        return $result; 
    }



    function GetTotalAlumnos($idCentro, $idPromocion){
        $con=PdoOpenCon();
        $sql="SELECT count(*) as total FROM matriculas WHERE idCentro = :idCentro AND idPromocion = :idPromocion";
        $recSet=$con->prepare($sql);
        $recSet->execute(array(":idCentro"=>$idCentro, ":idPromocion"=>$idPromocion));
        return $recSet->fetch(PDO::FETCH_ASSOC);
    }


    function SeAusentoDiaAnteriorSinAvisar($numeroAlumno, $fecha){
        $con=PdoOpenCon();
        $sql="SELECT * FROM stControlAsistencia c
                WHERE c.numeroAlumno = :numero AND fecha < :fecha
                ORDER BY c.fecha desc
                LIMIT 1";
        $recSet=$con->prepare($sql);
        $recSet->execute(array(':fecha'=>$fecha, ':numero'=>$numeroAlumno));
        if($reg=$recSet->fetch(PDO::FETCH_ASSOC)){
            return ( $reg['modoAsistencia'] === 'a' && $reg['modoAsistenciaReal'] === 'n' );
        }else{
            return false;
        }

    }


    function VinoAClaseCuandoEraOnline($numeroAlumno, $fecha){
        $con=PdoOpenCon();
        $sql="SELECT * FROM stControlAsistencia c
                WHERE c.numeroAlumno = :numero AND fecha < :fecha
                ORDER BY c.fecha desc
                LIMIT 1";
        $recSet=$con->prepare($sql);
        $recSet->execute(array(':fecha'=>$fecha, ':numero'=>$numeroAlumno));
        if($reg=$recSet->fetch(PDO::FETCH_ASSOC)){
            return ( $reg['modoAsistencia'] === 'o' && $reg['modoAsistenciaReal'] === 'a' );
        }else{
            return false;
        }

    }


    // Esta funcion obtiene las notas del alumno y los datos de éste de la tabla stFM2021. Esto es provisional para V.1. 
    // function GetNotas($idMatricula){
    //     $con=PdoOpenCon();
    //     $sql = "SELECT n.*, fm.*, prof.nombre as nombreProfesor FROM notas n 
    //                         inner join stFM2021 fm on fm.id = n.idMatricula 
    //                         inner join stProfesores prof on prof.id = fm.idProfesor
    //             WHERE n.idMatricula = :idMatricula";
    //     $recSet=$con->prepare($sql);
    //     $recSet->execute(array(":idMatricula"=>$idMatricula));
    //     return $recSet; 
    // }


    function GetNotas($idMatricula){
        $con=PdoOpenCon();
        $sql = "SELECT  * FROM notas WHERE idMatricula = :idMatricula";
        $recSet=$con->prepare($sql);
        $recSet->execute(array(":idMatricula"=>$idMatricula));
        return $recSet; 
    }

    function NotasEnviadas($idMatricula){
        $con=PdoOpenCon();
        $sql = "SELECT enviado1 from notas WHERE idMatricula = :idMatricula";
        $recSet=$con->prepare($sql);
        $recSet->execute(array(":idMatricula"=>$idMatricula));
        $notas=$recSet->fetch(PDO::FETCH_ASSOC);
        return $notas['enviado1'] == 1; 
    }

    function MarcaComoNotasEnviadas($idMatricula){
        $con=PdoOpenCon();
        $sql="UPDATE notas SET enviado1 = 1 WHERE idMatricula = :idMatricula";
        $op=$con->prepare($sql);
        $result=$op->execute(   array(":idMatricula"=>$idMatricula)   );
        return $result; 
    }

    function GrabacionNotas($idMatricula, $speaking1, $speaking2, $listening1, $listening2, $writing1, $writing2, $reading1, $reading2,
                            $exEscrito1, $exEscrito2, $participacion1, $participacion2, $comportamiento1, $comportamiento2, $examenOral2,
                            $unit1, $unit2, $unit3, $unit4, $unit5, $unit6, $unit7, $unit8, $unit9, $unit10, $unit11, $unit12, $unit13,
                            $unit14, $unit15, $unit16, $unit17, $unit18, $unit19, $unit20, $comentarios){
        $con=PdoOpenCon();
        $sql="INSERT INTO notas ( idMatricula, speaking1, speaking2, listening1, listening2, writing1, writing2, reading1, reading2, 
                                    examenEscrito1, examenEscrito2, participacion1, participacion2, comportamiento1, comportamiento2, 
                                    examenOral2, unit1, unit2, unit3, unit4, unit5, unit6, unit7, unit8, unit9, unit10, unit11, unit12,
                                     unit13, unit14, unit15, unit16, unit17, unit18, unit19, unit20, comentarios, enviado1, enviado2 )
                            VALUES (:idMatricula, :speaking1, :speaking2, :listening1, :listening2, :writing1, :writing2, :reading1, :reading2, 
                                :exEscrito1, :exEscrito2, :participacion1, :participacion2, :comportamiento1, :comportamiento2, :examenOral2,
                                :unit1, :unit2, :unit3, :unit4, :unit5, :unit6, :unit7, :unit8, :unit9, :unit10, :unit11, :unit12, :unit13, :unit14, :unit15, 
                                :unit16, :unit17, :unit18, :unit19, :unit20, :comentarios, 0, 0)";
        $op=$con->prepare($sql);
        $result=$op->execute(   array(":idMatricula"=>$idMatricula, ":speaking1"=>$speaking1, ":speaking2"=>$speaking2, ":listening1"=>$listening1, ":listening2"=>$listening2, 
                                    ":writing1"=>$writing1, ":writing2"=>$writing2, ":reading1"=>$reading1, ":reading2"=>$reading2, ":exEscrito1"=>$exEscrito1, ":exEscrito2"=>$exEscrito2, 
                                    ":participacion1"=>$participacion1, ":participacion2"=>$participacion2, ":comportamiento1"=>$comportamiento1, ":comportamiento2"=>$comportamiento2, 
                                    ":examenOral2"=>$examenOral2, ":unit1"=>$unit1, ":unit2"=>$unit2, ":unit3"=>$unit3, ":unit4"=>$unit4, ":unit5"=>$unit5, ":unit6"=>$unit6, ":unit7"=>$unit7, 
                                    ":unit8"=>$unit8, ":unit9"=>$unit9, ":unit10"=>$unit10, ":unit11"=>$unit11, ":unit12"=>$unit12, ":unit13"=>$unit13,":unit14"=>$unit14,":unit15"=>$unit15,
                                    ":unit16"=>$unit16, ":unit17"=>$unit17, ":unit18"=>$unit18, ":unit19"=>$unit19, ":unit20"=>$unit20, ":comentarios"=>$comentarios)
                            );
        return $result; 
    }

    function ModificaNotas($idMatricula, $speaking1, $speaking2, $listening1, $listening2, $writing1, $writing2, $reading1, $reading2,
                            $exEscrito1, $exEscrito2, $participacion1, $participacion2, $comportamiento1, $comportamiento2, $examenOral2,
                            $unit1, $unit2, $unit3, $unit4, $unit5, $unit6, $unit7, $unit8, $unit9, $unit10, $unit11, $unit12, $unit13,
                            $unit14, $unit15, $unit16, $unit17, $unit18, $unit19, $unit20, $comentarios, $enviado1, $enviado2){
        $con=PdoOpenCon();
        $sql="UPDATE notas SET speaking1 = :speaking1, speaking2 = :speaking2, listening1 = :listening1, listening2 = :listening2, writing1 = :writing1,
                writing2 = :writing2, reading1 = :reading1, reading2 = :reading2, examenEscrito1 = :examenEscrito1, examenEscrito2 = :examenEscrito2, 
                participacion1 = :participacion1, participacion2 = :participacion2, comportamiento1 = :comportamiento1, comportamiento2 = :comportamiento2, 
                examenOral2 = :examenOral2, unit1 = :unit1, unit2 = :unit2, unit3 = :unit3, unit4 = :unit4, unit5 = :unit5, unit6 = :unit6, unit7 = :unit7,
                unit8 = :unit8, unit9 = :unit9, unit10 = :unit10, unit11 = :unit11, unit12 = :unit12, unit13 = :unit13, unit14 = :unit14, unit15 = :unit15,
                unit16 = :unit16, unit17 = :unit17, unit18 = :unit18, unit19 = :unit19, unit20 = :unit20, comentarios = :comentarios, enviado1 = :enviado1, 
                enviado2 = :enviado2
              WHERE idMatricula = :idMatricula";
        $op=$con->prepare($sql);
        $result=$op->execute(   array(":idMatricula"=>$idMatricula, ":speaking1"=>$speaking1, ":speaking2"=>$speaking2, ":listening1"=>$listening1, ":listening2"=>$listening2, 
                                    ":writing1"=>$writing1, ":writing2"=>$writing2, ":reading1"=>$reading1, ":reading2"=>$reading2, ":examenEscrito1"=>$exEscrito1, ":examenEscrito2"=>$exEscrito2, 
                                    ":participacion1"=>$participacion1, ":participacion2"=>$participacion2, ":comportamiento1"=>$comportamiento1,   ":comportamiento2"=>$comportamiento2, 
                                    ":examenOral2"=>$examenOral2, ":unit1"=>$unit1, ":unit2"=>$unit2, ":unit3"=>$unit3, ":unit4"=>$unit4, ":unit5"=>$unit5, ":unit6"=>$unit6, ":unit7"=>$unit7, 
                                    ":unit8"=>$unit8, ":unit9"=>$unit9, ":unit10"=>$unit10, ":unit11"=>$unit11, ":unit12"=>$unit12, ":unit13"=>$unit13,":unit14"=>$unit14,":unit15"=>$unit15,
                                    ":unit16"=>$unit16,":unit17"=>$unit17,":unit18"=>$unit18,":unit19"=>$unit19,":unit20"=>$unit20,":comentarios"=>$comentarios, ":enviado1"=>$enviado1, 
                                    ":enviado2"=>$enviado2)   );
        return $result; 
    }


    

 
    







?>
