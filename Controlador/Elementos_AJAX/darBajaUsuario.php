<?php



  header('Content-Type: application/json');
 // header("Content-type: application/javascript"); 
  header('Cache-Control: no-cache, must-revalidate');
  header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
  header('Content-type: application/json; charset=utf-8');

require_once($_SERVER['DOCUMENT_ROOT'].'/Changes/Modelo/DataObj.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/Changes/Sistema/Constantes/ConstantesBbdd.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/Changes/Modelo/Post.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/Changes/Modelo/Usuarios.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/Changes/Sistema/Directorios.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/Changes/Sistema/Email/mandarEmails.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/Changes/Controlador/Validar/MisExcepciones.php');


 if(!isset($_SESSION)) 
    { 
        session_start(); 
    } 
    
global $conMenu;

 


try {
    global $conMenu;
    $conMenu= Conne::connect();
  
    
     // -------- párametro opción para determinar la select a realizar -------
    if (isset($_POST['opcion'])){ 
            $opc=$_POST['opcion'];
        }else{
            if (isset($_GET['opcion'])){
                $opc=$_GET['opcion'];
         }        
    }
    
    
    switch ($opc) {
    
    
        case 'Definitivamente':
          
            try{
            
                $excepciones = new MisExcepciones(CONST_ERROR_BBDD_DAR_BAJA_USUARIO_DEFINITIVAMENTE[1],CONST_ERROR_BBDD_DAR_BAJA_USUARIO_DEFINITIVAMENTE[0]); 
                $resultadoTotal = array("resultadoTotal" => true);
            
            global $conMenu;
                $idUsu = $_SESSION["userTMP"]->devuelveId();
                $nickEliTotal = $_SESSION["userTMP"]->getValue('nick');
                $x = $_SESSION["userTMP"]->deleteFrom('usuario');
            
              
                if($x){
                        $sqlEliminar = "Select idPost from ".TBL_POST.
                                " where idUsuarioPost = :idUsuario;";

                        $stm = $conMenu->prepare($sqlEliminar);
                        $stm->bindValue(":idUsuario",$idUsu , PDO::PARAM_INT);
                        $stm->execute();
                        $idPostEliminar = $stm->fetchAll();
                        //var_dump($idPostEliminar);
                        $totalPost = count($idPostEliminar);
                        //echo $totalPost;
                    //ELIMINAMOS SI LOS HAY LOS POSTS   
                        
                    if($totalPost > 0){
                        
                            $sqlElimiarPost = "Delete from ".TBL_POST. 
                                    " where idPost = :idPost;";

                            for($i = 0; $i < $totalPost; $i++){

                                $stmElimPost = $conMenu->prepare($sqlElimiarPost);
                                $stmElimPost->bindParam(":idPost", $idPostEliminar[$i][0], PDO::PARAM_INT );
                                $stmElimPost->execute();
                               // 

                            }    
                    $stmElimPost->closeCursor();
                    
                    }
                }

                       
                
                Conne::disconnect($conMenu);
            
                //ELIMINAMOS LOS DIRECTORIOS CREADOS AL REGISTRARSE
                if($idUsu != "" ){
                    
                    Directorios::eliminarDirectoriosSistema("../../photos/".$nickEliTotal,"eliminarDirectoriosBajaUsuario");
                    Directorios::eliminarDirectoriosSistema("../../datos_usuario/".$nickEliTotal,"eliminarDirectoriosBajaUsuario");
                    Directorios::eliminarDirectoriosSistema("../../Videos/".$nickEliTotal,"eliminarDirectoriosBajaUsuario");
                    
                        
                }
               
                
                //MANDAMOS EMAIL PARA INFORMAR USUARIO
                //SE HA DADO DE BAJA
                
           
               
                $objMandarEmails = new mandarEmails();
                $test = $objMandarEmails->mandarEmailBajaUsuario($_SESSION["userTMP"]);
                
              

                        unset($_SESSION["userTMP"]);     
                     
            echo json_encode($resultadoTotal);
            
            } catch (Exception $ex) {
                    Conne::disconnect($conMenu);
        
                    $excepciones->redirigirPorErrorSistema("darBajaDefinitivamente");
                    
            }
                 
                            
            break;
        
        case "parcialmente":
            
        try{
            
            $excepciones = new MisExcepciones(CONST_ERROR_BBDD_DAR_BAJA_USUARIO_PARCIAL[1],CONST_ERROR_BBDD_DAR_BAJA_USUARIO_PARCIAL[0]); 
            global $conMenu;
            $idUsu = $_SESSION["userTMP"]->devuelveId();
                
            //ELIMINAMOS LAS PALABRAS DE AVISO
            $sqlPalabrasEmail =  "update  usuario set activo = 0"
                    . " where idUsuario = :idUsuario;";
                           
            
            $stmEliPalabrasEmail = $conMenu->prepare($sqlPalabrasEmail);
            $stmEliPalabrasEmail->bindParam(":idUsuario", $idUsu, PDO::PARAM_INT );
            $test = $stmEliPalabrasEmail->execute();
            $resultado = array("resultado" => $test);
            echo json_encode($resultado);
            Conne::disconnect($conMenu);
            
        } catch (Exception $ex) {

            Conne::disconnect($conMenu);
            $excepciones->redirigirPorErrorSistema("Error al dar de baja parcial al usuario");
        }    
            
            
            break;
        
        
        default:
            break;
           
    }








    
} catch (Exception $exc) {
    echo $exc->getTraceAsString();
}