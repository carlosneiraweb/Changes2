<?php 



function mostrarError(){
    header('Location: mostrar_error.php');
}
function volverAnterior(){
    header('Location: index.php');
    die();
}
function volverPrincipio(){
    header('Location: index.php');
    die();
}
function abandonarSession(){
    header("Location: abandonar_sesion.php"); 
}
 
 require_once($_SERVER['DOCUMENT_ROOT'].'/Changes/Modelo/DataObj.php');
 require_once($_SERVER['DOCUMENT_ROOT'].'/Changes/Modelo/Usuarios.php');
 require_once($_SERVER['DOCUMENT_ROOT'].'/Changes/Controlador/Validar/ValidoForm.php');
 require_once($_SERVER['DOCUMENT_ROOT'].'/Changes/Controlador/Validar/ControlErroresSistemaEnArchivosUsuarios.php');
 require_once($_SERVER['DOCUMENT_ROOT'].'/Changes/Controlador/Validar/MisExcepciones.php');
 require_once($_SERVER['DOCUMENT_ROOT'].'/Changes/Sistema/Directorios.php');
 require_once($_SERVER['DOCUMENT_ROOT'].'/Changes/Sistema/Constantes/ConstantesBbdd.php');
 require_once($_SERVER['DOCUMENT_ROOT'].'/Changes/Sistema/Constantes/ConstantesErrores.php');
 require_once($_SERVER['DOCUMENT_ROOT'].'/Changes/Sistema/Email/mandarEmails.php');
   

 
 
if(!isset($_SESSION)){
    
 session_start();

}
 


?>
<!DOCTYPE html>

<html>
   <head>
       <meta charset="UTF-8">
       <title>Tú portal de intercambio</title>
	<meta name="description" content="Portal para intercambiar las cosas que ya no usas o utilizas por otras que necesitas o te gustan."/>
	<meta name="viewport" content="width=device-width, initial-scale=1"/>
	<link href="../img/fabicon.ico" rel="icon" type="image/x-icon">
	<link rel="stylesheet" href="../css/estilos.css"/>
        <script src="../Controlador/jquery-2.2.2.js" type="text/javascript"></script>
        <script src="../Controlador/Elementos_AJAX/CONEXION_AJAX.js"></script>
        <script src="../Controlador/Elementos_AJAX/principal.js"></script>
        <script src="./registrarse.js"></script>
        <script src="../Controlador/Validar/formulario_reg.js"></script>
        <script src="../Controlador/Validar/iconoObligatorio.js"></script>
       
       
        
        
    <!--Para navegadores viejos-->
        <!--[if lt IE 9]>
            <script
        src="http://html5shiv.googlecode.com/svn/trunk/html5.js">
        </script>
        <![endif]-->
    
   </head>
   <body id="cuerpo">
    
        <?php
        
       
        
        
        
    //Añadimos el div con la clase oculto
    // echo'<div id="ocultar" class="oculto"> </div>';  
       
        
        //Variable global de usuario
        global $userReg;
        $userReg = new Usuarios(array());
        //Variable para recuperar
        //el resultado de la validacion
        //y el posible mensaje de error
        global $resulTestReg;
        global $controlErrores;
        $controlErrores = new MisExcepciones(null,null);
        
      
        
    
        echo'<header>';
	echo'<figure id="logo" class="fade">';
		echo'<img src="../img/logo.png" alt="Logo del portal"/>';
		echo'<figcaption id="titulo">Cambia todo lo que ya no uses.</figcaption>';
	echo'</figure>';
        
            
	echo'<section id="cabecera">';
			echo'<h1>Te lo cambio</h1>';
			echo'<h3>Miles de personas compartiendo te están esperando.</h3>';
		        echo'<h3>Registrarte solo te llevara un minuto</h3>';
                
	echo'</section>';
     
    echo'</header>';
 
     
        // Si no se ha recivido el step
        // Se muestra por primera vez el formulario
        $paso = null;
            if(!isset($_POST['step'])){
                displayStep1(array());
            }
            
    //Variable donde cargaremos el paso
            //de cada una de las partes del formulario
           
    /*Mandamos a comprobar los campos del primer formulario*/
    if(isset($_POST['primeroReg']) and $_POST['primeroReg'] == "Siguiente"){
        $requiredFields = array('nick', 'password', 'email');
        if(isset($_POST['step']) and $_POST['step'] === "step1"){ $paso = 'step1';}
        processFormRegistro($requiredFields, $paso);
    } elseif(isset($_POST['primeroReg']) and $_POST['primeroReg'] == "Salir"){
            volverAnterior();
    } elseif(isset($_POST['segundoReg']) and $_POST['segundoReg'] == "Siguiente"){
        $requiredFields = array('nombre','telefono');
        if(isset($_POST['step']) and $_POST['step'] === "step2"){ $paso = 'step2';}
        processFormRegistro($requiredFields, $paso);
    } elseif(isset($_POST['segundoReg']) and $_POST['segundoReg'] == "Atras"){
        displayStep1(array());
    } elseif(isset($_POST['segundoReg']) and $_POST['segundoReg'] == "Salir"){
        volverAnterior();
    }elseif(isset($_POST['terceroReg']) and $_POST['terceroReg'] == "Siguiente"){
        $requiredFields = array('codPostal', 'ciudad');
        if(isset($_POST['step']) and $_POST['step'] === "step3"){ $paso = 'step3';}
        processFormRegistro($requiredFields, $paso);
    } elseif(isset($_POST['terceroReg']) and $_POST['terceroReg'] == "Atras"){
        displayStep2(array());
    } elseif(isset($_POST['terceroReg']) and $_POST['terceroReg'] == "Salir"){
        volverAnterior();
    }elseif(isset($_POST['cuartoReg']) and $_POST['cuartoReg'] == "Atras"){
        displayStep3(array());
    } elseif(isset($_POST['cuartoReg']) and $_POST['cuartoReg'] == "Siguiente"){
        $requiredFields = array();
        if(isset($_POST['step']) and $_POST['step'] === "step4"){ $paso = 'step4';}
        processFormRegistro($requiredFields, $paso);
    }elseif(isset($_POST['aceptaCondicionesReg']) and $_POST['aceptaCondicionesReg'] == "Acepto"){
        $requiredFields = array();
        processFormRegistro($requiredFields, 'step5');
    }elseif(isset($_POST['noAceptaCondicionesReg']) and $_POST['noAceptaCondicionesReg'] == "Salir"){
        //El usuario no acepta las condiciones
        //Eliminado todos los directorios creados
        
        $controlErrores->eliminarDirectoriosUsuario('registrar');
        volverPrincipio();
        
    }elseif(isset($_POST['registroConfirmado']) and $_POST['registroConfirmado'] == "Aceptar") {
        volverPrincipio();
    }

  
function displayStep1($missingFields){
    
  
    global $resulTestReg;
    
    echo'<section id="form_registro_1" class="inputsRegistro">';
                echo'<h4>Introduzca sus datos</h4>';
    echo'<form name="registro" action="registrarse.php" method="POST" id="registro_1" >';
        echo'<fieldset>';
                echo"<legend>Formulario de ";if(isset($_SESSION['actualizo']['nick']) ){echo "Actualizar Primer paso";}else{echo "Registro Primer Paso";}echo "</legend>";
        echo"<input type='hidden' name='step' value='step1'>";
       
    echo '<section class="contEtiquetas">';
    echo'<label '.ValidoForm::validateField("nick", $missingFields).' for="nick" class="labelFormulario">Introduce nombre de usuario:</label><span class="obligatorio"><img src="../img/obligado.png" alt="campo obligatorio" title="obligatorio"></span>';
    echo'<input type="text" name="nick" id="nick" autofocus placeholder="Tú nombre usuario maximo 25 caracteres" maxlength="25" value=';if(isset($_SESSION['usuario']['nick'])&& (!isset($_SESSION['actualizo']))){echo $_SESSION['usuario']['nick'];} if(isset($_SESSION['actualizo']['nick'])){echo $_SESSION['actualizo']['nick'];} echo ">";       
    echo'</section>';
    
    echo '<section class="contEtiquetas">';
    echo'<label '.ValidoForm::validateField("password", $missingFields). ' for="password">';if(isset($_SESSION['actualizo']['nick']) ){echo "Introduce tú password o cambialo";}else{echo "Introduce tú password";}echo '</label><span class="obligatorio"><img src="../img/obligado.png" alt="campo obligatorio" title="obligatorio"></span>';
    echo'<input type="password" name="password" placeholder="Solo puede tener letras y numeros" id="password"  maxlength="12" placeholder="Debe  minimo 6 y máximo 12" >';	
    echo'</section>';
    
    echo '<section class="contEtiquetas">';
    echo'<label '.ValidoForm::validateField("passReg2", $missingFields). ' for="passReg2">Repite el password</label><span class="obligatorio"><img src="../img/obligado.png" alt="campo obligatorio" title="obligatorio"></span>';
    echo'<input type="password" name="passReg2" id="passReg2" maxlength="12"  >';       
    echo'</section>';

    echo '<section class="contEtiquetas">';
    echo'<label '.ValidoForm::validateField("email", $missingFields).' for="email">Email:</label> <span class="obligatorio"><img src="../img/obligado.png" alt="campo obligatorio" title="obligatorio"></span>';
    echo'<input type="text" name="email" id="email" placeholder="info@developerji.com" maxlength="45" value=';if(isset($_SESSION['usuario']['email'])&& (!isset($_SESSION['actualizo']['nick']))){echo $_SESSION['usuario']['email'];} if(isset($_SESSION['actualizo']['correo'])){ echo $_SESSION['actualizo']['correo'];}echo ">";
    echo'</section>';
   
    
    echo '<section id="btns_registrar">';
                echo"<input type='submit' name='primeroReg' id='primeroSigReg'  value='Siguiente' >";
                echo"<input type='submit' name='primeroReg' id='primeroSalReg'  value='Salir' >";
    echo '</section>';
                    
            echo "</form>";
          
        echo'</fieldset>'; 
    //En caso de error 
        //se muestra en el formulario
        
    if($resulTestReg[0] != ""){
            echo $resulTestReg[0];
        }        
        
    echo'</section>';
 //fin displayStep1
}



function displayStep2($missingFields){
    
    global $resulTestReg;          
       
    echo'<section id="form_registro_2" class="inputsRegistro">';
                echo'<h4>Introduzca sus datos</h4>';
    echo'<form name="registro" action="registrarse.php" method="POST" id="registro_2">';
        echo'<fieldset>';
    echo"<legend>Formulario de ";if(isset($_SESSION['actualizo']['nick'])){echo "Actualizar Segundo paso";}else{echo "Registro Registro Paso";}echo "</legend>";
        	
    echo"<input type='hidden' name='step' value='step2'>";
    
    echo '<section class="contEtiquetas">';
    echo'<label '.ValidoForm::validateField("nombre", $missingFields). ' for="nombre">Nombre:</label> <span class="obligatorio"><img src="../img/obligado.png" alt="campo obligatorio" title="obligatorio"></span>';
    echo'<input type="text" name="nombre" id="nombre" autofocus  placeholder="Escribe tú nombre" maxlength= "25" value=';if(isset($_SESSION['usuario']['nombre'])&& (!isset($_SESSION['actualizo']))){echo $_SESSION['usuario']['nombre'];} if(isset($_SESSION['actualizo']['nombre'])){echo $_SESSION['actualizo']['nombre'];}echo ">";
    echo'</section>';
    
    echo '<section class="contEtiquetas">';
    echo'<label for="apellido_1">Primer Apellido:</label>';
    echo'<input type="text" name="apellido_1" id="apellido_1" placeholder="Escribe tú apellido"  maxlength= "25" value=';if(isset($_SESSION['usuario']['apellido_1'])&& (!isset($_SESSION['actualizo']))){echo $_SESSION['usuario']['apellido_1'];} if(isset($_SESSION['actualizo']['primerApellido'])){ echo $_SESSION['actualizo']['primerApellido'];}echo ">";
    echo'</section>';
    
    echo '<section class="contEtiquetas">';
    echo'<label for="apellido_2">Segundo Apellido:</label>';
    echo'<input type="text" name="apellido_2" id="apellido_2" placeholder="Escribe tú segundo apellido" maxlength= "25" value= ';if(isset($_SESSION['usuario']['apellido_2'])&& (!isset($_SESSION['actualizo']))){echo $_SESSION['usuario']['apellido_2'];} if(isset($_SESSION['actualizo']['segundoApellido'])){echo $_SESSION['actualizo']['segundoApellido'];}echo ">";        
    echo'</section>';
    
    echo '<section class="contEtiquetas">';
    echo'<label '.ValidoForm::validateField("telefono", $missingFields). ' for="telefono">Teléfono:</label><span class="obligatorio"><img src="../img/obligado.png" alt="campo obligatorio" title="obligatorio"></span>';
    echo'<input type="text" name="telefono" id="telefono" placeholder="Teléfono contacto" maxlength="9" value=';if(isset($_SESSION['usuario']['telefono'])&& (!isset($_SESSION['actualizo']))){ echo $_SESSION['usuario']['telefono'];} if(isset($_SESSION['actualizo']['tlf'])){echo $_SESSION['actualizo']['tlf'];}echo ">";
    echo'</section>'; 
    
    echo '<section class="contEtiquetas">';
    echo'<label for="genero">Selecciona tu sexo:</label>';
		echo'<select name="genero" id="genero">';			
		echo'</select>';
    echo'</section>';
	
                echo'<br>';        
    
    echo '<section id="btns_registrar">';
                        echo"<input type='submit' name='segundoReg' id='segundoSigReg'  value='Siguiente'>";
                        echo"<input type='submit' name='segundoReg' id='segundoAtrasReg' value='Atras' >";
                        echo"<input type='submit' name='segundoReg' id='segundoSalirReg' value='Salir' >";
    echo"</section>";
                    
            echo "</form>";
         //En caso de error 
        //se muestra en el formulario
         if($resulTestReg[0] != ""){
            echo $resulTestReg[0];
        }    
        echo'</fieldset>';  
        
    echo'</section>';
 //fin  displayStep2()   
}
 
function displayStep3($missingFields){
    global $resulTestReg;
        
            
    echo'<section id="form_registro_3" class="inputsRegistro">';
                echo'<h4>Introduzca sus datos</h4>';
    echo'<form name="registro" action="registrarse.php" method="POST" id="registro_3">';
        echo'<fieldset>';
    echo"<legend>Formulario de ";if(isset($_SESSION['actualizo']['nick'])){echo "Actualizar Tercer paso";}else{echo "Registro Tercer Paso";}echo "</legend>";
        	
    echo"<input type='hidden' name='step' value='step3'>";
    
    echo '<section class="contEtiquetas">';
    echo'<label for="calle">Nombre de la calle o vía:</label>';
    echo'<input type="text" name="calle" id="calle" placeholder="Escribe el nombre de la calle"  value= ';if(isset($_SESSION['usuario']['calle'])&& (!isset($_SESSION['actualizo']))){echo $_SESSION['usuario']['calle'];} if(isset($_SESSION['actualizo']['calle'])){echo $_SESSION['actualizo']['calle'];}echo ">";     
    echo'</section>';
    
    echo '<section class="contEtiquetas">';
    echo'<label for="numeroPortal">Número del portal:</label>';
    echo'<input type="text" name="numeroPortal" id="numeroPortal" placeholder="Escribe el número del portal" maxlength= "10" value= ';if(isset($_SESSION['usuario']['numeroPortal'])&& (!isset($_SESSION['actualizo']))){echo $_SESSION['usuario']['numeroPortal'];} if(isset($_SESSION['actualizo']['portal'])){echo $_SESSION['actualizo']['portal'];} echo ">";     
    echo'</section>';
    
    echo '<section class="contEtiquetas">';
    echo'<label for="ptr">Puerta:</label>';
    echo'<input type="text" name="ptr" id="ptr" placeholder="Escribe el número de la puerta"  maxlength= "10" value= ';if(isset($_SESSION['usuario']['ptr'])&& (!isset($_SESSION['actualizo']))){echo $_SESSION['usuario']['ptr'];}if(isset($_SESSION['actualizo']['puerta'])){echo $_SESSION['actualizo']['puerta'];} echo ">";     
    echo'</section>';
    
    echo '<section class="contEtiquetas">';
    echo'<label '.ValidoForm::validateField("ciudad", $missingFields).'for="ciudad">Ciudad:</label><span class="obligatorio"><img src="../img/obligado.png" alt="campo obligatorio" title="obligatorio"></span>';
    echo'<input type="text" name="ciudad" id="ciudad" placeholder="Nombre de tu Localidad" maxlength= "25" value= ';if(isset($_SESSION['usuario']['ciudad'])&& (!isset($_SESSION['actualizo']))){echo $_SESSION['usuario']['ciudad'];} if(isset($_SESSION['actualizo']['ciudad'])){echo $_SESSION['actualizo']['ciudad'];}echo ">";     
    echo '</section>';
    
    echo '<section class="contEtiquetas">';
    echo'<label '.ValidoForm::validateField("codPostal", $missingFields).'for="codPostal">Código Postal:</label><span class="obligatorio"><img src="../img/obligado.png" alt="campo obligatorio" title="obligatorio"></span>';
    echo'<input type="text" name="codPostal" id="codPostal" placeholder="Escribe el número del código postal"  maxlength="5" value= ';if(isset($_SESSION['usuario']['codPostal'])&& (!isset($_SESSION['actualizo']))){echo $_SESSION['usuario']['codPostal'];} if(isset($_SESSION['actualizo']['codigoPostal'])){echo $_SESSION['actualizo']['codigoPostal'];}echo ">";     
    echo'</section>';
    
    echo '<section class="contEtiquetas">';
    echo'<label for="provincia">Provincia:</label>';
 
	echo'<select name="provincia" id="provincia">';
           
               echo'</select>'; 
    echo'</section>';
    
    
                echo'<br>';
                echo'<br>';
    
    echo '<section class="contEtiquetas">';
    echo'<label for="pais">Pais:</label>'; 
    echo'<input type="text" name="pais" id="pais" placeholder="España"  maxlength= "25" value= '; if(isset($_SESSION['usuario']['pais'])&& (!isset($_SESSION['actualizo']))){echo $_SESSION['usuario']['pais'];} if(isset($_SESSION['actualizo']['pais'])){echo $_SESSION['actualizo']['pais'];} echo '>';		
    echo'</section>';
     
    echo '<section id="btns_registrar">';
                        echo"<input type='submit' name='terceroReg' id='terceroSigReg'  value='Siguiente'>";
                        echo"<input type='submit' name='terceroReg' id='terceroAtrReg' value='Atras' >";
                        echo"<input type='submit' name='terceroReg' id='terceroSalReg' value='Salir' >";
    echo"</section>";
                    
            echo "</form>";
          
        echo'</fieldset>';
     //En caso de error 
        //se muestra en el formulario   
     if($resulTestReg[0] != ""){
            echo $resulTestReg[0];
        }      
    echo'</section>';
 //fin  displayStep3()   
}

function displayStep4($missingFields){
    global $resulTestReg;
        
    echo'<section id="form_registro_4" class="inputsRegistro">';
                echo'<h4>Introduzca sus datos</h4>';
    echo'<form name="registro" action="registrarse.php" method="POST" id="registro_4" enctype="multipart/form-data">';
        echo'<fieldset>';
    echo"<legend> ";if(isset($_SESSION['actualizo']['nick'])){echo "Actualiza tú imagen si quieres";}else{echo "Personaliza tu perfil, sube una foto tuya si quieres";}echo "</legend>";
        	
    echo"<input type='hidden' name='step' value='step4'>";
    //Modificamos en php.ini y en el formulario el maximo tamaño del archivo
     
    echo'<input type="hidden" name="MAX_FILE_SIZE" value="50000" />';
    echo '<section class="contEtiquetas">';
    echo'<label for="photo">Solo fotos .jpg</label>';
            
            echo'<input type="file" name="photo" id="photo" value="" />';
    echo'</section>';
    
 if(isset($_SESSION['actualizo']['nick'])){          
 echo "<section id='mostrarFotoAntigua'>";
    echo "<figure id='fotoAntigua'>";
        //Utilizamos la variable de SESSION del Login para
        //Mostrar su antigua imagen
         echo '<img src='."../datos_usuario/".$_SESSION["userTMP"]->getValue('nick')."/".$_SESSION["userTMP"]->getValue('nick').".jpg".' alt="imagen del usuario antigua" title="Esta es tú antigua imagen."/>';
         echo "<figcaption>Tú antigua imagen.</figcaption>";
    echo "</figure>";
    echo"</section>";
 }
 
 echo'<section id="btns_registrar">';
 echo"<input type='submit' name='cuartoReg' id='cuartoSigReg'  value='Siguiente' accept='image/jpeg'>";   
 echo"<input type='submit' name='cuartoReg' id='cuartoAtrReg'  value='Atras'>"; 
 echo'</section>';
 
            echo "</form>";
         
        echo'</fieldset>';
    
    
    
     //En caso de error 
        //se muestra en el formulario    
    if($resulTestReg[0] != ""){
            echo $resulTestReg[0];
    }       
    echo'</section>';
    
  
//fin displayStep4    
}

function displayStep5(){
    
    echo '<script type="text/javascript">';
               echo "agregarFormularioCondiciones();";         
    echo '</script>';
       
//fin displayStep5    
}

function confirmarRegistro(){
    echo '<section id="confirmarRegistro">';
        echo '<h2>Has sido registrado correctamente</h2>';
        echo '<h3>Ahora podras logearte con tu usuario y contraseña</h3>';
            echo "<section id='form_registro_5' class='inputsREgistro'>";
                echo'<form name="registro" action="registrarse.php" method="POST" id="registro">';
                    echo '<section id="btns_registrar">';
                        echo"<input type='submit' name='registroConfirmado' id='registroConReg' value='Aceptar'>";
                    echo '</section>';
            echo "</form>";
    echo "</section>";
    
//Fin confirmar registro    
}

/**
     * Una vez validado todos los campos 
     * Instanciamos un objeto usuario y
     * hacemos la insercion.
     */
    function ingresarUsuario(){
        global $userReg;
        global $mensajeReg;
        global $excepciones;
        $repElimarPhotos = false;
        $repElimarDatosUsuario = false;
        $repEliminarVideos = false;
       
        
        $userReg = new Usuarios(array(
            "nombre" => $_SESSION['usuario']['nombre'],
            "apellido_1" => $_SESSION['usuario']['apellido_1'],
            "apellido_2" => $_SESSION['usuario']['apellido_2'],
            "calle" => $_SESSION['usuario']['calle'],
            "numeroPortal" => $_SESSION['usuario']['numeroPortal'],
            "ptr" => $_SESSION['usuario']['ptr'],
            "ciudad" => $_SESSION['usuario']['ciudad'],
            "codigoPostal" => $_SESSION['usuario']['codPostal'],
            "provincia" => $_SESSION['usuario']['provincia'],
            "telefono" => $_SESSION['usuario']['telefono'],
            "pais" => $_SESSION['usuario']['pais'],
            "genero" => $_SESSION['usuario']['genero'],
            "email" => $_SESSION['usuario']['email'],
            "nick" => $_SESSION['usuario']['nick'],
            "password" => $_SESSION['usuario']['password'],
            "admin" => 0
                ));
        
           
            if(!isset($_SESSION["userTMP"])){

               $test = $userReg->insert();
                $objMandarEmails = new mandarEmails();
               
                    //Si todo va bien le mandamos a la pagina para confirmar registro
                    //y le mandamos un email de bienvenida
                       confirmarRegistro();
                    //este metodo destruye el objeto $user
                       $objMandarEmails->mandarEmailWelcome($userReg);
                      
                       
                    //Destruimos el objeto user
                    //y la variable session
                        unset($userReg);
                        unset($_SESSION['usuario']);
                        unset($mensajeReg);
                
               
            
            }else{
                      
                $userReg->actualizoDatosUsuario();
                abandonarSession();
                  
            }
           
          
                
          
    //fin ingresarUsuario    
    }

function processFormRegistro($requiredFields, $st){
    
    //Array para almacenar los campos no rellenados y obligatorios
        global $missingFields;
        global $userReg;
        global $resulTestReg;
        $missingFields = array();   
        
        //Segun el paso vamos rellenando la variable de session  de usuario
       
        switch ($st){
            case "step1":                                                           
                $_SESSION['usuario']["nick"] = isset($_POST["nick"]) ? preg_replace("/[^\-\_a-zA-Z0-9ñÑ]/", "", $_POST["nick"]) : "";
                $_SESSION['usuario']["password"] = isset($_POST["password"]) ? preg_replace("/[^\-\_a-zA-Z0-9ñÑ]/", "", $_POST["password"]) : "";  
                $_SESSION['usuario']["email"] = isset($_POST["email"]) ? preg_replace("/[^\@\.\-\_a-zA-Z0-9ñÑ]/", "", $_POST["email"]) : "";
                    break;
            case "step2":
                $_SESSION['usuario']["nombre"] = isset($_POST["nombre"])  ? preg_replace("/[^\-\_a-zA-Z.,`'´ñÑ]/", "", $_POST["nombre"]) : "";
                $_SESSION['usuario']["apellido_1"] = isset($_POST["apellido_1"]) ? preg_replace("/[^\-\_a-zA-Z.,`'´ñÑ]/", "", $_POST["apellido_1"]) : "";
                $_SESSION['usuario']["apellido_2"] = isset($_POST["apellido_2"]) ? preg_replace("/[^\-\_a-zA-Z.,`'´ñÑ]/", "", $_POST["apellido_2"]) : "";
                $_SESSION['usuario']["telefono"] = isset($_POST["telefono"]) ?  $_POST["telefono"] : "";
                $_SESSION['usuario']["genero"] = isset($_POST["genero"]) ? $_POST['genero'] : "" ;
                    break;
            case "step3":
                $_SESSION['usuario']["calle"] = isset($_POST['calle']) ? preg_replace("/[^\-\_a-zA-Z0-9.,`'´ñÑ]/", "", $_POST["calle"]) : "";
                $_SESSION['usuario']["numeroPortal"] = isset($_POST['numeroPortal']) ? preg_replace("/[^\-\_a-zA-Z0-9.,`'´ñÑ]/", "", $_POST["numeroPortal"]) : "";
                $_SESSION['usuario']["ptr"] = isset($_POST['ptr']) ? preg_replace("/[^\-\_a-zA-Z0-9ñÑ]/", "", $_POST["ptr"]) : "";
                $_SESSION['usuario']["ciudad"] = isset($_POST['ciudad']) ? preg_replace("/[^\-\_a-zA-Z0-9.,`'´ñÑ]/", "", $_POST["ciudad"]) : "";
                $_SESSION['usuario']["codPostal"] = isset($_POST['codPostal']) ? preg_replace("/[^\-\_0-9]/", "", $_POST["codPostal"]) : "";
                $_SESSION['usuario']["provincia"] = isset($_POST['provincia']) ? $_POST['provincia'] : "";
                $_SESSION['usuario']["pais"] = isset($_POST['pais']) ? preg_replace("/[^\-\_a-z-Z0-9.,`'´ñÑ]/", "", $_POST["pais"]) : "";
                //cerramos escritura sobre variable de sesion
                //session_write_close();
                
                    break; 
            case "step4":
                
                //En este paso no hacemos nada
                //Aqui copiamos la imagen subida por el
                //usuario o sino sube una ponemos la de default
                    break;
        
             case "step5":
                
                //En este paso no hacemos nada
                    break;
        }
            
       
    foreach($requiredFields as $requiredField){
        if(!$_SESSION['usuario'][$requiredField]){
            $missingFields[] = $requiredField;
        }
    }
    
    
    
    //En cada uno de los pasos
    //Mandamos a validar al metodo validarCamposRegistro
    //del archivo ControlErroresSistemaEnArchivos 
    
    switch ($st){
   
        case 'step1':
            
            $resulTestReg = validarCamposRegistro($st, $userReg);
            
                //Si ha habido algun error volvemos a mostrar el paso del formulario
                //con los campos que ha rellenado el usuario
                //Si todo es correcto mostramos el siguiente paso
                if($missingFields || ($resulTestReg[1] == 0)){
                    displayStep1($missingFields);
                } else{
                    displayStep2(array());
                }
                break;
                
        case 'step2':
            $resulTestReg = validarCamposRegistro($st, $userReg);
            
                //Si ha habido algun error volvemos a mostrar el paso del formulario
                //  correcto y un mensaje con los campos correspondientes
                if($missingFields || ($resulTestReg[1] == 0)){
                    displayStep2($missingFields);
                } else{
                    displayStep3(array());
                }
                break;
     
     
        case 'step3':
            $resulTestReg = validarCamposRegistro($st, $userReg);
           
                //Si ha habido algun error volvemos a mostrar el paso del formulario
                //  correcto y un mensaje con los campos correspondientes
                if($missingFields || ($resulTestReg[1] == 0)){
                    displayStep3($missingFields);
                } else{
                    displayStep4(array());
                }
                break;
            
        case 'step4':
            
            if(!validarCamposRegistro($st, $userReg)){
                displayStep4($missingFields);
            }else{
                //Si el usuario se esta registrando se
                //le muestra el formulario de las condiciones
                if(!isset($_SESSION['actualizo'])){
                    displayStep5();
                }else{
                        
        
                    //Si esta actualizando sus datos
                    //Saltamos ese paso y directamente lo
                    //intentaremos actualizar
                     ingresarUsuario();
                }
                
            }
           
            break;
            
        case 'step5':
            //finalmente si todo ha ido bien mandamos a
                // ingresar el usuario. En caso de error lo
                //redirigimos a una página para hacerselo saber
                // y darle la oportunidad de intentarlo otra vez.                       
                ingresarUsuario();
    }
//fin processForm
}
   
    
    /*section contenedor*/
    echo'</section>';     
    
  
   echo'</body>';
echo'</html>';
