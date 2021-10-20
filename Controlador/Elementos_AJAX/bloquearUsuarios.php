<?php

  header('Content-type: application/json; charset=utf-8');
  header('Cache-Control: no-cache, must-revalidate');
  header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
  header('Content-type: application/json; charset=utf-8');

require_once($_SERVER['DOCUMENT_ROOT'].'/Changes/Modelo/DataObj.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/Changes/Sistema/Constantes/ConstantesBbdd.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/Changes/Modelo/Usuarios.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/Changes/Controlador/Validar/MisExcepciones.php');


 if(!isset($_SESSION)) 
    { 
        session_start(); 
    } 

global $conBloqueo;
$conBloqueo = Conne::connect();
global $usuBloquea;
$usuBloquea= $_SESSION["userTMP"]->devuelveId();


/**
 * 
 * @global type $conBloqueo <br>
 * Variable conexion
 * @global type usuario<br>
 * Usuario que bloquea
 * @param  type String <br>
 * Id usuario para comprobar que el usuario <br>
 * no esta ya bloqueado
 * 
 */
function comprobarUsuarioBloquear($id){
    
    try{
    $excepciones = new MisExcepciones(CONST_ERROR_BBDD_CONSULTAR_USUARIOS_BLOQUEADOS[1],CONST_ERROR_BBDD_CONSULTAR_USUARIOS_BLOQUEADOS[0]); 
    global $conBloqueo;
    global $usuBloquea;
    
   

                $sqlComprobarUsu = "select idUsuarioBloqueado from ".TBL_USUARIOS_BLOQUEADOS.
                                " where idUsuarioBloqueado = :idUsuarioBloqueado and usuario_idUsuario= :usuBloquea;";
                //echo $sqlComprobarUsu;
                $stmComprobar = $conBloqueo->prepare($sqlComprobarUsu);
                $stmComprobar->bindValue(":idUsuarioBloqueado",$id,PDO::PARAM_STR);
                $stmComprobar->bindValue(":usuBloquea",$usuBloquea , PDO::PARAM_STR);
                $stmComprobar->execute();
               
                $idsUsuBloquear = $stmComprobar->fetch();
                Conne::disconnect($conBloqueo); 
                return $idsUsuBloquear[0];
                //echo json_encode($idsUsuBloquear);
        
       
    } catch (Exception $ex) {
        Conne::disconnect($conBloqueo); 
        $excepciones->redirigirPorErrorSistema("consultarUsuariosBloqueados",false);

    }
    
    
}
 
    try{
        
         // -------- párametro opción para determinar la select a realizar -------
    if (isset($_POST['opcion'])){ 
            $opc=$_POST['opcion'];
        }else{
            if (isset($_GET['opcion'])){
                $opc=$_GET['opcion'];
         }        
    }
    
    if (isset($_POST['nickBloquear'])){ 
            $nickBloquear=$_POST['nickBloquear'];
        }else{
            if (isset($_GET['nickBloquear'])){
                $nickBloquear=$_GET['nickBloquear'];
         }        
    } 
    
    global $conBloqueo;
    global $usuBloquea;
    
    switch ($opc) {
        
        case 'bloqueoTotal':
            
            try {
                
                //Conseguimos el id del usuario a bloquear
                $sqlIdBloqueoTotal = "Select idUsuario from ".TBL_USUARIO.
                                " where nick = :nick;";
                $stmTotal = $conBloqueo->prepare($sqlIdBloqueoTotal);
                $stmTotal->bindValue(":nick", $nickBloquear, PDO::PARAM_STR);
                $stmTotal->execute();
                $idUsuBloquearTotal = $stmTotal->fetch();
                
                
                $test = comprobarUsuarioBloquear($idUsuBloquearTotal[0]);  
               
                $testUsuYaBloqueado = array("testUsuYaBloqueado" => $test);
               // echo 'testarray'. $testUsuYaBloqueado["testUsuYaBloqueado"];
                
               
                if($testUsuYaBloqueado["testUsuYaBloqueado"] == null){
                    
                     $sqlBloquearTotal = "insert into ".TBL_USUARIOS_BLOQUEADOS." (usuario_idUsuario,idUsuarioBloqueado,bloqueadoTotal,bloqueadoParcial)"
                     . " values (:usuBloquea,:usuBloqueado,:total,:parcial);";
                    $stmBloquearTotal = $conBloqueo->prepare($sqlBloquearTotal);
                    $stmBloquearTotal->bindValue(":usuBloquea", $usuBloquea, PDO::PARAM_INT);
                    $stmBloquearTotal->bindValue(":usuBloqueado", $idUsuBloquearTotal[0], PDO::PARAM_INT );
                    $stmBloquearTotal->bindValue(":total", "1", PDO::PARAM_INT );
                    $stmBloquearTotal->bindValue(":parcial", "0", PDO::PARAM_INT );
                    $test = $stmBloquearTotal->execute();
                    
                    
                    echo json_encode('adios'); 
                   
                }else{
                
                    echo json_encode('hola');
   
                }
                
                 Conne::disconnect($conBloqueo);
            } catch (Exception $exc) {
                echo $exc->getTraceAsString();
                echo $exc->getMessage();
                
            }


            break;

        default:
            break;
    }
    
    
    
        
    } catch (Exception $ex) {

    }