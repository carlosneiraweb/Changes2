
<?php
 

require_once($_SERVER['DOCUMENT_ROOT'].'/Changes/Modelo/DataObj.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/Changes/Modelo/Usuarios.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/Changes/Controlador/Validar/MisExcepciones.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/Changes/Sistema/Directorios.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/Changes/Controlador/Validar/ValidoForm.php');  
//require_once($_SERVER['DOCUMENT_ROOT'].'/Changes/Vista/registrarse.php');
/**
 * Controla los errores del sistema
 * al eliminar, crear o copiar
 * Directorios archivos, etc
 * al registrarse o actualizar un usuario
 * Extiende MixExcepciones
 * @author carlos
 */

if(!isset($_SESSION)){
    
 session_start();

}


//session_start();

  if(!isset($_SESSION["paginaError"])){
            $_SESSION["paginaError"] = "registrarse.php";
        }
 //Variable global para mostrar los errores de validacion
        
        global $mensajeReg;
        global $usuActualiza;
      
        //Si existe $_SESSION["userTMP"]
        //Es que el usuario se ha logeado
        
        if(isset($_SESSION["userTMP"])){          
            $usuActualiza = $_SESSION["userTMP"]->getValue('nick');
        }
       
/**
 * @param $dir <br/>
 * Array con la ruta donde crear los directorios <br/>
 * y la opcion por si hay algun error poder <br/>
 * tratarla adecuadamente <br/>
 */                              
 function crearDirectorios($dir){
             
            //Creamos los directorios del registro
        //echo 'crear Directorios '.$dir[1].' '.$dir[3];  
        Directorios::crearDirectorio($dir[0],$dir[3]);
        Directorios::crearDirectorio($dir[1],$dir[3]);
        Directorios::crearDirectorio($dir[2],$dir[3]);
        
        
   //fin crearDirectorios                                     
    }
   
    /**
     * Crea un array que contiene 
     * la ruta donde se crearan las 
     * carpetas donde se guardara la informacion
     */
    function crearRutasDirectorios(){
    
        global $usuActualiza;
        global $tmpNuevosDatos;
        global $tmpViejosDatos;
        
        //Este array contiene la ruta para crear los directorios 
        //que se necesitan en el sistema al registrarse
                                            
            $tmpNuevosDatos = array( "../photos/".$_SESSION['usuario']['nick'],
                              "../datos_usuario/".$_SESSION['usuario']['nick'],
                              "../Videos/".$_SESSION['usuario']['nick'],
                              "registrar"
                       );
        
        //Vamos a crear una copia de seguridad
        //de las carpetas antes de realizar cualquier cambio
        //en caso el usuario este actualizando sus datos
        if($usuActualiza != ""){
                                           
            $tmpViejosDatos = array(  "../photos/".$usuActualiza,
                                     "../datos_usuario/".$usuActualiza,
                                     "../Videos/".$usuActualiza,
                                     "actualizar"
                             );
        }
  
        
        
    //fin  crearRutasDirectorios()   
    }
 
/**
 * Este metodo comprueba en caso el usuario <br/>
 * cambie de correo electronico <br/>
 * que el introducido no se encuentre <br/>
 * ya en uso.Recordemos que <br/>
 * $_SESSION['actualizo']['correo'] se instancia cuando se accede <br/>
 * al formulario de actualizar para ir mostrando los datos <br/>
 * y <br/>
 * $_SESSION['usuario']['email'] son los datos que el usuario <br/>
 * va introduciendo en el formulario <br/>
 * @param @nombre usuario <br/>
 *  @tipo objeto usuario <br/>
 */
function comprobarNickNuevo($user){
   
    if($_SESSION['actualizo']['correo'] != $_SESSION['usuario']['email']){
                                
        if($user->getByEmailAddress($_SESSION['usuario']['email'])){
            return 1;
        }else{
            return 0;
        }
    }      
    
    //fin  comprobarNickNuevo   
    }
    
    
    
    /**
     * Metodo que crea los directorios </br>
     * de seguridad en la carpeta /Sistema/TMP_ACTUALIZAR </br>
     * antes de hacer ningun cambio </br>
     */


   
function crearDirectoriosTMP(){
    
    global $usuActualiza;
    global $tmpViejosDatos;
    
            Directorios::crearDirectorioPadreTMP("../Sistema/TMP_ACTUALIZAR/".$usuActualiza);                               /*******************/
            Directorios::copiarDirectorios($tmpViejosDatos[1],  "../Sistema/TMP_ACTUALIZAR/".$usuActualiza."/datos_usuario/","copiarDirectorios_a_TMP_actualizar");                          
            Directorios::copiarDirectorios($tmpViejosDatos[0],  "../Sistema/TMP_ACTUALIZAR/".$usuActualiza."/photos/", "copiarDirectorios_a_TMP_actualizar");
            Directorios::copiarDirectorios($tmpViejosDatos[2],  "../Sistema/TMP_ACTUALIZAR/".$usuActualiza."/Videos/", "copiarDirectorios_a_TMP_actualizar");
     
    
    
    //fin crearDirectoriosTMP()
}
    
    /**
     * Metodo que cambia el nombre de las carpetas </br>
     * creadas en el registro cuando el usuario </br>
     * al actualizar sus datos cambia el nick de usuario </br>
     * @global string $destino <br/>
     * Destino donde copiar o crear los directorios
     * @global type $foto <br/>
     * Foto que copiar o renombrar nombre
     * @global $tmpNuevosDatos
     * El nombre de los nuevos directorios
     * @global $tmpViejosDatos
     * El nombre de los directorios antes de cambiar el nick
     * @global $usuActualiza
     * Nick del usuario logueado
     * 
     */
    function actualizarCambiandoNickSubiendoFoto(){
        global $destino;
        global $foto;
        global $usuActualiza;
       
            crearDirectoriosTMP();
            
            Directorios::renombrarDirectoriosActualizar($_SESSION['usuario']['nick'], $usuActualiza);
            $destino = '../datos_usuario/'.$_SESSION['usuario']['nick'].'/'.basename($_FILES['photoArticulo']['name']);
               //Subimos la nueva foto
            Directorios::moverImagen($foto, $destino,"errorFotoActualizar");
                //Eliminamos la vieja foto 
            Directorios::eliminarImagen("../datos_usuario/".$_SESSION['usuario']['nick']."/".$_SESSION['actualizo']['nick'].".jpg", "errorFotoActualizar");
          //  $imagen = substr(basename($_FILES['photoArticulo']['tmp_name']),0,-4);
            
    Directorios::renombrarFotoActualiazar("../datos_usuario/".$_SESSION['usuario']['nick']."/".basename($_FILES['photoArticulo']['name']),"../datos_usuario/".$_SESSION['usuario']['nick']."/".$_SESSION['usuario']['nick'].".jpg","errorFotoActualizar"); 
  
            //fin actualizarCambiandoNickSubiendoFoto()    
    }

/**
 * Metodo cambia la foto
 * del usuario cuando este 
 * actualiza cualquier campo 
 * menos el nick 
 */    
function actualizarSinCambiarNickSubiendoFoto(){
 
    global $destino;
    global $foto;
   
    crearDirectoriosTMP();
    $destino = '../datos_usuario/'.$_SESSION['usuario']['nick'].'/'.basename($_FILES['photoArticulo']['name']);
               //Subimos la nueva foto
    Directorios::moverImagen($foto, $destino,"errorFotoActualizar");
                //Eliminamos la vieja foto 
    Directorios::eliminarImagen("../datos_usuario/".$_SESSION['usuario']['nick']."/".$_SESSION['actualizo']['nick'].".jpg", "errorFotoActualizar");
                       
    Directorios::renombrarFotoActualiazar("../datos_usuario/".$_SESSION['usuario']['nick']."/".basename($_FILES['photoArticulo']['name']),"../datos_usuario/".$_SESSION['usuario']['nick']."/".$_SESSION['usuario']['nick'].".jpg","errorFotoActualizar"); 
    
    
    //fin actualizarSinCambiarNickSubiendoFoto
}


/**
 *Este metodo se usa cuando el usuario
 * cambia algun dato como la direccion
 * cambiando o no de nick
 */
function actualizarCambios(){
   
    global $usuActualiza;
    global $destino;
    
    
        crearDirectoriosTMP();
        $destino = "../datos_usuario/".$_SESSION['usuario']['nick']."/$usuActualiza.jpg";
        Directorios::renombrarDirectoriosActualizar($_SESSION['usuario']['nick'], $usuActualiza);
        Directorios::renombrarFotoActualiazar("../datos_usuario/".$_SESSION['usuario']['nick']."/".$usuActualiza.".jpg", "../datos_usuario/".$_SESSION['usuario']['nick']."/".$_SESSION['usuario']['nick'].".jpg", "errorFotoActualizar");
       
        
//actualizarCambiandoNickPeroNoFoto  
}
  
/**
 * Este metodo crea los directorios 
 * y le asigna una foto default
 * a un usuario que se esta registrando pero
 * no sube foto
 */

function registrandoseSinSubirFoto(){
    
    global $tmpNuevosDatos;
    
    crearDirectorios($tmpNuevosDatos); 
     //Si no sube ninguna foto se le asigna la de default
   
    $destino = "../datos_usuario/".$_SESSION['usuario']['nick'].'/'.$_SESSION['usuario']['nick'].'.jpg';    
    Directorios::copiarFoto("../datos_usuario/desconocido.jpg", $destino, $tmpNuevosDatos[3]);
    
    
    
    //fin registrandoseSinSubirFoto()
}

/**
 * Este metodo crea los directorios </br>
 * necesarios cuando un usuario </br>
 * se registra y sube una foto </br>
 * para el perfil </br>
 */

function registrandoseSubiendoFoto(){
    
    global $tmpNuevosDatos;
    global $foto;
    global $destino;
           
    crearDirectorios($tmpNuevosDatos);
    Directorios::moverImagen($foto, $destino, $tmpNuevosDatos[3]);
    Directorios::renombrarFotoPerfil($destino, $_SESSION['usuario']['nick']);
    
    
    //fin registrandoseSubiendoFoto
}


  /**
     * Metodo que valida los datos introducidos por el usuario al registrarse. </br>
     * Valida los campos con los metodos static de ValidaForm </br>
     * Valida los datos de la bbdd con un objeto de la clase Usuarios </br>
     * Este metodo </br>
     * VALIDAMOS TANTO REGISTRO COMO ACTUALIZACION USUARIO  </br>
     * 
     * @param  user </br>
   * Objeto tipo usuario
     * @param name st </br>
   *    NUmero de paso que hay que validar </br>
     * @return boolean
     */
function validarCamposRegistro($st, $user){
        
        $testValidoReg = array(null, true);
        global $usuActualiza;
        global $tmpNuevosDatos;
        global $destino;
        global $foto;
        
        
        
        //Llama metodo mismo archivo
        crearRutasDirectorios();
        
        switch ($st){
           
            case "step1":


                
                //En caso de que exista el nombre de usuario o email
                //Los passwords se repitan o el email sea incorrecto
                    
           
                        //En caso se este registrando
                        if(!isset($_SESSION["userTMP"])){
                         
                            if($user->getUserName($_SESSION['usuario']['nick']) !== null){
                                                 
                               
                                $testValidoReg[0] = ERROR_NOMBRE_USUARIO_EXISTE;
                                $testValidoReg[1] =  false;
                                
                                
                            }
                            
                        }else{
                            
                            //En caso se este actualizando y cambie
                            //de nick se comprueba que el nuevo que ha elegido
                            //no este ya en uso. 
                            //$usuActualiza se instancia en el metodo 
                            //con la variable de sesion $_SESSION["userTMP"]
                            //cuando un usuario se logea

                            if($usuActualiza !== $_SESSION['usuario']['nick'] ){
                        
                                if($_SESSION["userTMP"]->getByUserName($_SESSION['usuario']['nick'])){
                                    
                                        $testValidoReg[0] = ERROR_NOMBRE_USUARIO_EXISTE;
                                        $testValidoReg[1] =  false;
                                        
                                }
                      
                            }
                        }
                    
                    if(!ValidoForm::validarPassword($_SESSION['usuario']['password'])){
                        $testValidoReg[0] =  ERROR_PASSWORD_INCORRECTO;
                        $testValidoReg[1] = false;
                           
                    }
                    if(ValidoForm::validarIgualdadPasswords($_SESSION['usuario']['password'], $_POST['passReg2'])){
                        $testValidoReg[0] =  ERROR_IGUALDAD_PASSWORD;
                        $testValidoReg[1] = false;
                           
                    }
                    if(!ValidoForm::validarEmail($_SESSION['usuario']['email'])){
                        $testValidoReg[0] = ERROR_EMAIL_INCORRECTO;
                        $testValidoReg[1] = false;
                    //           
                    }
                    
                    if($user->getByEmailAddress($_SESSION['usuario']['email'])){
                        
                        //Se comprueba que se esta registrando
                        //Si se esta actualizando no se hace esa comprobacion
                        if(!isset($_SESSION["userTMP"])){
                            
                            $testValidoReg[0] = ERROR_EMAIL_EXISTE;
                            $testValidoReg[1] = false;
                            
                        }elseif(isset($_SESSION["userTMP"])){ 
                            
                            //Mandamos comprobar que el nuevo correo
                            //no esta ya en uso si el usuario desea cambiarlo
                            if(comprobarNickNuevo($user)){
                                
                                $testValidoReg[0] = ERROR_EMAIL_EXISTE;
                                $testValidoReg[1] = false;  
                            }
                            
                        }
                         
                         
                    }
                
                if(
                    ValidoForm::campoVacio($_POST['nick']) == 0 ||
                    ValidoForm::campoVacio($_POST['password']) == 0 ||
                    ValidoForm::campoVacio($_POST['passReg2']) == 0 ||
                    ValidoForm::campoVacio($_POST['email']) == 0 
                ){ 
                    $testValidoReg[0] = ERROR;
                    $testValidoReg[1] =  false;
                }
                
                return $testValidoReg;     

                case 'step2':
                
                    if(!ValidoForm::validaTelefono($_SESSION['usuario']['telefono'])){
                        $testValidoReg[0] =  ERROR_TELEFONO_INCORRECTO;
                        $testValidoReg[1] = false;
                         
                    }
               
                    if(
                        ValidoForm::campoVacio($_POST['nombre']) == 0 ||
                        ValidoForm::campoVacio($_POST['telefono']) == 0 
                        ){ 
                        $testValidoReg[0] = ERROR;
                        $testValidoReg[1] =  false;
                    }
             
                return $testValidoReg;
                   
            case 'step3':
                    
                    if(!ValidoForm::validarCodPostal($_SESSION['usuario']['codPostal'])){
                        $testValidoReg[0] =  ERROR_CODIGO_POSTAL;
                        $testValidoReg[1] = false;
    
                    }
                    
                    if(
                        ValidoForm::campoVacio($_POST['ciudad']) == 0 ||
                        ValidoForm::campoVacio($_POST['codPostal']) == 0 
                        ){ 
                        $testValidoReg[0] = ERROR;
                        $testValidoReg[1] =  false;
                    }
             
                        
                return $testValidoReg;
                
            case 'step4':
            
            $testValidoFoto = '4';   
            //Si el usuario sube una foto para su perfil la validamos
            $test = 0;
                if(isset($_FILES['photoArticulo']) and $_FILES['photoArticulo']['name'] != ""){
                  
                    if (is_uploaded_file($_FILES['photoArticulo']['tmp_name'])) {
                    $testValidoFoto =  Directorios::validarFoto();  
                   
                        if( $testValidoFoto === 0){  
                        
                           
                            //Importante
                            //Recuperamos el nombre del archivo y ruta a la que mover la imagen
                                $destino = '../datos_usuario/'.$_SESSION['usuario']['nick'].'/'.basename($_FILES['photoArticulo']['name']);
                                $foto = $_FILES['photoArticulo']['tmp_name'];

                        //Comprobamos que el usuario se esta registrando
                        //Y no esta actualiando sus datos
                        //Hay que tener en cuenta que $_SESSION["userTMP"]
                        //Se crea cuando un usuario se ha logueado
        
                            if(!isset($_SESSION["userTMP"])){

                                registrandoseSubiendoFoto();
                                
                                
                            }                            
     
                                
                    //SE ESTA ACTUALIZANDO
                    if($usuActualiza != ""){
                        //En caso de que este actualizando
                         //Y cambie de nombre de usuario
                        //Hay que renombrar los directorios 
                        //que se crearon al registrarse y la foto de perfil
                        if($usuActualiza != $_SESSION['usuario']['nick']){

                            actualizarCambiandoNickSubiendoFoto();

                        //En caso que modifique algun dato
                        //pero no el nick
                        }else if($usuActualiza == $_SESSION['usuario']['nick']){
                           
                            actualizarSinCambiarNickSubiendoFoto();
                        }
                        
                    }
                        
                }else if ($testValidoFoto === '10') {  
                   
                        //Si hay algun error al validar la imagen 
                        //redirigimos a la pagina mostrarError
                            //y le indicamos el motivo del error
                        // Esto ultimo se hace en el switch del
                        //metodo que valida la subida en el directorio Directorios
                        $_SESSION['paginaError'] = 'registrarse.php';
                        mostrarErrorRegistro();      
                             
                        }
                    }
                    $test= 1;
                    //El usuario no sube foto
                }else if ($testValidoFoto === '4') {
                  
                    //Eliminamos la variable error
                    //Por que para registrarse no es obligatorio subir
                    //una imagen del usuario
                    //Esta variable se instancia en el metodo 
                    //validar fotos de la clase Directorios
                    if(isset($_SESSION['error'])){
                        unset($_SESSION['error']);
                        
                    }
                    
                    //En caso que actualice su nick o no lo haga
                    //y no sube foto 
                    if($usuActualiza != ""){
                             
                        if($usuActualiza != $_SESSION['usuario']['nick'] || $usuActualiza == $_SESSION['usuario']['nick']){
                            actualizarCambios();
                            $test = 1;
                        }
                        
                    }else{
                        
                    //En caso se este registrando
                    //y no sube una foto
                        registrandoseSinSubirFoto();
                        $test = 1;
                    
                    }
                        
                }       

                return $test;
   
                case 'step5':
                    //No hacemos ninguna validacion
                   
                
                    default :
                        //nada
                        
        
           
   
        }
  //fin validarCamposRegistro      
      }
 
      

