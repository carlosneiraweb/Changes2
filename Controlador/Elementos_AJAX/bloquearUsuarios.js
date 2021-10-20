

function bloquear(nickBloquear,opc){
    
   
    
   $.ajax({
                    data: { 
                        
                        nickBloquear : nickBloquear,
                        opcion : opc 
                            
                           },
                    type: "POST",
                    dataType: 'JSON',   
                    url: "../Controlador/Elementos_AJAX/bloquearUsuarios.php"
                }).done(function( data) {
                    var test = data;
                    alert(test);
                    
                    
                    
                    
                    if(test > 0){
                        
                    $('#bloquearUsuarios').empty();
                    $('#bloquearUsuarios').append($('<h5>',{
                        text : 'Parece ser que ya tenias bloqueado a este usuario',
                        class : 'bloqueoTotal'
                    })).append($('<section>',{
                        id: 'botonRecargar'
                    }).append($('<input>',{
                        type : 'button',
                        id : 'bRecargar',
                        value : 'Aceptar'
                    }))).on('click','#bRecargar',function(){
                    
                        $('#menuUsuario').empty();
                        mostrarMenu();
                        
                    });
                        
                    }else{
                        
                    }
                   


            
            
            });
//                    
   
                    };
    



