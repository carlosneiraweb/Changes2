<?php


require_once($_SERVER['DOCUMENT_ROOT'].'/Changes/Modelo/DataObj.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/Changes/Modelo/Usuarios.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/Changes/Controlador/Validar/MisExcepcionesPost.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/Changes/Sistema/Directorios.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/Changes/Controlador/Validar/ValidoForm.php');  


/**
 * Description of ControlErroresSistemaEnArchivos
 * Es la encargada de controlar y tratar
 * los posibles errores que haya al trabajar
 * con archivos y directorios 
 * al subir, actualizar o borrar un Post
 * @author carlos
 */
 
/**
 *  Este metodo Crea un subdirectorio para almacenar las imagenes 
 *   IMPORTANTE CONOCER EL CONTENIDO DE 'nuevoSubdirectorio' 
 *   Es la usada para mover, copiar, eliminar he ingresar en la bbdd
 *   Su contenido es del tipo nombreUsuario/totalSubdirectorios
 *               
 *  Agregamos una foto demo por 
 *  si el usuario no quiere subir ninguna imagen
 *   Esto solo se hace la primera vez y se evita crearlo otra vez si el usuario 
*    vuelve atras en el formulario comprobando que $_SESSION['atras'] no existe
 */

function crearSubdirectorio(){
   
   /**/
   $nickUsu = $_SESSION['userTMP']->getValue('nick');
   //[0] nombre usuario
   $_SESSION['nuevoSubdirectorio'][0] = $nickUsu;
   //[1] numero subdirectorio ejemplo "1"
   $_SESSION['nuevoSubdirectorio'][1] = Directorios::crearSubdirectorio('../photos/'.$nickUsu,"crearSubdirectorio");
   
   Directorios::copiarFoto("../photos/demo.jpg","../photos/".$_SESSION['nuevoSubdirectorio'][0].'/'.$_SESSION['nuevoSubdirectorio'][1]."/demo.jpg", "copiarDemoSubirPost");    
    
//fin crearSubdirectorio    
}








     /**
     * Metodo que valida los datos introducidos por el usuario.
     * Valida los campos con los metodos static de ValidaForm
     * 
     * @global type $mensaje
     * @global Post $articulo
     * @param type $st
     * @return boolean
     */
   
function validarCamposSubirPost($st){
 
    switch ($st){
         
        case("step1"):
            
            
                if(($_SESSION['contador'] == 0) and (!isset($_SESSION['atras'])) ){
                   //Mandamos crear el subdirectorio
                    //para almacenar el post
                    crearSubdirectorio();
                }
                
                break;
               
        case('step2'):
        
            $testSubirArchivo = Directorios::validarFoto('photoArticulo');
 
        //Comprobamos que nos devuelve la constante 0 que significa que se 
        //ha subido correctamente o que no nos devuelve la constante 4
        //que signfica que no se ha elegido un archivo
            if($testSubirArchivo === 0){
         
            
                //Si la foto es correcta entonces eliminamos la imagen default 
                    //que subimos solo ocurre la primera vez
                    if(isset($_SESSION['contador']) && $_SESSION['contador'] == "0"){
                        Directorios::eliminarImagen('../photos/'.$_SESSION['nuevoSubdirectorio'][0].'/'.$_SESSION['nuevoSubdirectorio'][1]."/demo.jpg", "eliminarImgDemoSubirPost");
                    }
                    
                
                //Movemos la imagen que ha subido el usuario
                //Al directorio correcto
                
           
                $destino = '../photos/'.$_SESSION['nuevoSubdirectorio'][0].'/'.$_SESSION['nuevoSubdirectorio'][1].'/'.basename($_FILES['photoArticulo']['name']);                   
                $foto = $_FILES['photoArticulo']['tmp_name'];
                        
                Directorios::moverImagen($foto, $destino, "subirImagenPost");
                
               
                //Comprobamos subiendo imagenes el usuario no ha eliminado ninguna
                //Si lo ha hecho le asignamos en el directorio photos/subdirectorio 
                //Ese nombre
                    
                if(isset($_SESSION['imgTMP']) and $_SESSION['imgTMP']['imagenesBorradas'][0] != null){
                                
                    $_SESSION['idImagen'] = Directorios::renombrarFotoSubirPost($destino, 0); 
                                    
                        
                  //Aqui vamos subiendo las fotos al post mientras el usuario no 
                            //hubiera eliminado ninguna mientras subia las fotos                    
                    }else if (!isset($_SESSION['imgTMP'])){   
                                
                        $_SESSION['idImagen'] = Directorios::renombrarFotoSubirPost($destino, 1);
                                    
                    }   
           
            
        }else if($testSubirArchivo === 4 || $testSubirArchivo === 10){
            //Si hay algun error al validar la imagen 
            //redirigimos a la pagina mostrarError
            //y le indicamos el motivo del error
            // Esto ultimo se hace en el switch del
            //metodo que valida la subida en el directorio Directorios
                $_SESSION['paginaError'] = 'subir_posts.php';
                //png bandera para que al recargar
                //no se ingrese la img otra vez
                $_SESSION['png'] = 'png';
                //Para que actualice el post
                //Ya que nos devuelve al primer paso del formulario
                $_SESSION['atras'] = 'atras';
                mostrarError();      
               
                
        }
    //switch        
    } 
    
        
//fin de validarCamposSubirPost   
 }
    
    
   
