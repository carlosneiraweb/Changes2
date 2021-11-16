<?php

 if(!isset($_SESSION)) 
    { 
        session_start(); 
    } 
   

        




function moverImagen($nombreFoto, $nuevoDirectorio){
           echo "nombre foto".$nombreFoto."  nuevo directorio=>".$nuevoDirectorio."   "."opcion=>".$opc;
            
            
            try{
                
                if(!move_uploaded_file($nombreFoto, $nuevoDirectorio)){
                    //throw new MisExcepciones(CONST_ERROR_MOVER_IMAGEN[1],CONST_ERROR_MOVER_IMAGEN[0]);      
                    throw Exception("");
                    
                }
             //MisExcepciones $excepciones    

            } catch (Exception $ex) {
                /**/
                
                echo $ex->getMessage();

            }
}



function validarFoto(){
           
            $test = $_FILES['photoArticulo']['error'];
            echo 'file '.$_FILES["file"]['error'];
           
            var_dump($_FILES['photoArticulo']);
            if($test !== 4){
                $tipo = substr($_FILES['photoArticulo']["name"],-4);

                if($tipo !== '.jpg'){
                    $test = 10;
                }
            }
            echo 'test '.$test;
                switch ($test){
 
                    case 0:
                        $_SESSION['error'] = null;
                        //Todo ha ido bien
                            break;
                    case 1:
                        //Se ha sobrepasado el tamaño
                        //indicado en php.ini
                        $_SESSION['error'] =ERROR_TAMAÑO_FOTO;
                            break;
                    case 2:
                        //Se ha sobrepasado el tamaño
                        //indicado en el formulario
                        $_SESSION['error'] =ERROR_TAMAÑO_FOTO;
                            break;
                    case 3:
                        //El archivo ha subido parcialmente
                        $_SESSION['error'] = ERROR_INSERTAR_FOTO;
                            break;
                       
                    case 4:
                       //No se ha subido ningun archivo
                        $_SESSION['error'] = ERROR_FOTO_NO_ELIGIDA;
                            break;
                        
                   
                    case 10:
                         $_SESSION['error'] = ERROR_FORMATO_FOTO;
                            break;    
                    
                    
                    default:
                       //Otros errores 
                        $_SESSION['error'] = ERROR_FOTO_GENERAL;     
                }
            
               
                    return $test;
           // }   
        //fin validar foto    
        }

if(isset($_POST['segundoSubirPost']) and $_POST['segundoSubirPost'] == "Enviar" ){    
        //El usario  quiere subir una foto al post
        $requiredFields = array();
        validarFoto();
        $destino = '../photos/carlos/7/'.basename($_FILES['photoArticulo']['name']);//.$_SESSION['nuevoSubdirectorio'][0].'/'.$_SESSION['nuevoSubdirectorio'][1].'/'.basename($_FILES['photoArticulo']['name']);                   
       $foto = $_FILES['photoArticulo']['tmp_name'];
        moverImagen($foto, $destino);
}
 
    echo'<section id="form_post_2" class="fuenteFormulario">';
                echo'<h4>Puedes subir hasta 5 imagenes</h4>';
                
        //Seccion donde mostraemos las imagenes que
        //va subiendo el usuario
        echo '<section id="img_ingresadas">';
            //Vamos mostrando la cantidad de imagenes
            echo '<span id="contador">';
                echo $_SESSION['contador'].'<br>';
            echo '</span>';
                echo'<section id="cnt_img">';
            //Aqui el section creado con JS para las imagenes
                //Que el usuario va subiendo en cada nuevo post
                echo '</section>';
        echo '</section>';
      //
    echo'<form name="post" action="prueba.php"   method="POST" id="post" enctype="multipart/form-data">';
        echo'<fieldset>';
        	echo'<legend>Introduce alguna imagen.</legend>';
        echo"<input type='hidden' name='step' value='2'>"; 
        //Limitamos el valor máximo del archivo
        //echo'<input type="file" name="MAX_FILE_SIZE" value="80000" />';
        echo '<section class="contenedor">'; 
        echo'<label for="photoArticulo">Solo fotos .jpg</label>';
        echo '<br>';    
            echo'<input type="file" name="photoArticulo" id="photoArticulo" value="" />';        
        echo'</section>';
        
        
        
    echo '<section class="contenedor">'; 
    echo'<label  for="figcaption">Introduce una pequeña descripción, se verá junto a la imagen. </label>';
    echo'<input type="text" name="figcaption" id="figcaption" placeholder="Una pequeña descripción" maxlength="70" value="" >'; 
        echo'<label><span class="cnt">0</span></label>';
        echo '</section>';
        
   
    echo '<section id="btns_registrar">';
        
        
                        echo"<input type='submit' name='segundoSubirPost' id='atrasSubirPost'  value='Atras'>";
                    if($_SESSION['contador'] < 5){
                        echo"<input type='submit' name='segundoSubirPost' id='enviarSubirPost'  value='Enviar'>";
                    }    
                        echo"<input type='submit' name='segundoSubirPost' id='segundoSubirPost' value='Fin' >";
                    echo"</div>";       
    echo'</section>';
    
            echo "</form>";
