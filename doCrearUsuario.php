<?php 
 include "db.php"; 
 if(isset($_GET['email'])) 
 { 
     $celular=mysql_escape_string($_GET['movil']);
     $email=mysql_escape_string($_GET['email']);
     $cedula=mysql_escape_string($_GET['cedula']);
     $nombre=mysql_escape_string($_GET['fullname']);
     $sexo=mysql_escape_string($_GET['sexo']);
     $telefono=mysql_escape_string($_GET['telefono']);

     $clave=mysql_escape_string($_GET['password']);
    if(isset($_GET['localidad'])){
         $ciudad=mysql_escape_string($_GET['ciudad']);
         $localidad=mysql_escape_string($_GET['localidad']);
         $av=mysql_escape_string($_GET['calle_av']);
         $edificio=mysql_escape_string($_GET['edificio']);
         $numero=mysql_escape_string($_GET['numero']);
        
      }$date=date("Y-m-d");
     //Creamos nuestra consulta sql
     $query="insert into clientes (movil, cedula, fullname, sexo, email, telefono, password, reg_date) value ('$celular', '$cedula', '$nombre','$sexo','$email','$telefono','$clave','$date')";
  
    if(mysql_query($query)){
         //Si todo salio bien imprimimos este mensaje
          $ultimo_id = mysql_insert_id();
          $query2="insert into direccion_cliente (ciudad, localidad, calle_av, edificio, numero, id_cliente) value ('$ciudad', '$localidad', '$av','$edificio','$numero','$ultimo_id')";
        if(mysql_query($query2)) 
        echo "ok"; 
        else 
            echo "error";
    }else 
        echo "error"; 
 }else 
  echo "error"; 
 ?>