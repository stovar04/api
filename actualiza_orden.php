<?php
include('correo.php');
include('db.php');
	$resultados = array();
	$resultados["error"] = "1";
	$resultados["hora"] = date("F j, Y, g:i a"); 
	$resultados["generador"] = "Enviado desde Solo Plancho" ;
if(isset($_REQUEST['id_orden']) && !empty($_REQUEST['id_orden']))
{
      $usu = $_REQUEST['id_orden'];
      $orden=explode(" ",$usu);
      $usu=$orden[0];
      if(isset($_REQUEST['cedula'])){
		$ced = base64_decode(base64_decode($_REQUEST['cedula']));
      }else{
		$ced =0;
      }	

	if(isset($_REQUEST['latitud'])){
		$lat = $_REQUEST['latitud'];
		$long = $_REQUEST['longitud'];
		$gps="update clientes set latitud='$lat', longitud='$long' where latitud='0' and cedula='$ced'";
		$resup = mysql_query($gps);
	}
        
	$query="select o.status,id_orden,reg_id,cedula,fullname,movil,email from orden_servicios o,clientes where reg_id=id_cliente and id_orden='$usu' and cedula='$ced' limit 1";
	$res = mysql_query($query);
	$date=date("Y-m-d H:i:s");
	$cli="";
	if($res){
		$row=mysql_fetch_array($res);
		$cli=$row['reg_id'];
	}else{
		$row=array();
	}
if(isset($orden[1]))
      {
    if(count($row)>1){
	if($row['status']=='2'){
		$q="update orden_servicios set status='3' where id_orden='$usu'";
        	$result = mysql_query($q);
	
		if($result){
			$resultados["mensaje"] = "Orden # $usu Recibida por nuestro IKARO ";
			$resultados["error"] = "1";
			$up2="update usuario_ordenes set status='3',fecha_cumple='$date' where id_orden='$usu' and status='1'";
        		$resulta = mysql_query($up2);
			$query="select uo.id_orden,email,fullname from usuario_ordenes uo, usuarios u where u.id_usuario=uo.id_usuario and uo.id_orden='$usu' and uo.status='3' limit 1";
			$res = mysql_query($query);
				$deli=array();
			if($res){
				$deli=mysql_fetch_array($res);
			}else{
				$deli['email']=$row['email'];
			}
			
			
			$are=array(0=>strtolower(trim($row['email'])),1=>strtolower(trim($deli['email'])));
                        $mensaje="Estimada(o): ".$row['fullname']."\n\n\t\t La OS # $usu  ha sido exitosamente recibida por nuestro IKARO: ".$deli['fullname']." \n http://www.soloplancho.com\n"
                               . "Su cuenta email: ".strtolower(trim($row['email']));
			$arreglo=array('id_cliente'=>$cli,'titulo'=>"orden de servicio recibida por IKARO",'mensaje'=>$mensaje);
			enviar_curl("http://api.soloplancho.com/notifications/sendNotification.php", $arreglo);
                        enviar_mensaje($are, $mensaje, 'orden de servicio recibida por IKARO, SOLOPLANCHO.COM');
                        
			
		}else{
			$resultados["mensaje"] = "Error actualizando orden ($usu)";
			$resultados["error"] = "2";
		}
	}else
	if($row['status']=='9'){
	    $sql="select status,forma_pago from pago_ordenes where id_orden='$usu' limit 1";
            $resulp = mysql_query($sql);
	    $pago=mysql_fetch_array($resulp);
	    if($pago['status']=='2'){
		$q="update orden_servicios set status='10',fecha_entrega='$date' where id_orden='$usu'";
	        $result = mysql_query($q);
		if( $result){
			$up2="update usuario_ordenes set status='5',fecha_cumple='$date' where id_orden='$usu' and status='4'";
	        	$resulta = mysql_query($up2);
			$query="select uo.id_orden,email from usuario_ordenes uo, usuarios u where u.id_usuario=uo.id_usuario and uo.id_orden='$usu' and uo.status='5' limit 1";
			$res = mysql_query($query);
			$deli=array();
			$deli['email']=$row['email'];
			$deli=mysql_fetch_array($res);
			$resultados["mensaje"] = "Orden # $usu Entregada Al Cliente";
			$resultados["error"] = "1";
			$are=array(0=>strtolower(trim($row['email'])),1=>strtolower(trim($deli['email'])));
                        $mensaje="Estimada(o): ".$row['fullname']."\n\n\t\t La OS # $usu ha sido exitosamente entregada.\n​Un gusto atenderle.... \n HASTA SU PRÓXIMA ORDEN DE SERVICIO.\n Apreciamos retornar los ganchos\n http://www.soloplancho.com\n"."Su cuenta email: ".strtolower(trim($row['email']));
			$arreglo=array('id_cliente'=>$cli,'titulo'=>"ORDEN SERVICIO ENTREGADA",'mensaje'=>$mensaje);
			enviar_curl("http://api.soloplancho.com/notifications/sendNotification.php", $arreglo);
                        enviar_mensaje($are, $mensaje, 'ORDEN SERVICIO ENTREGADA AL CLIENTE, SOLOPLANCHO.COM');			
			
		}else{
			$resultados["mensaje"] = "Error actualizando orden ($usu)";
			$resultados["error"] = "2";
		}
	    }else{
			$resultados["mensaje"] = "Orden de servicio ($usu) no se a cancelado, verifique forma pago y actualice";
			$resultados["error"] = "2";
		}
	}
	else{
		$resultados["mensaje"] = "Orden no esta en satus Asignada Delivery o Enviada al Cliente";
		$resultados["status"] = $row["status"];
		$resultados["id_orden"] = $row["id_orden"];
		$resultados["nombre"] = " Cliente: ".$row["cedula"]." ".$row["fullname"]." <br> Teléfono Movil: ".$row["movil"];
		$resultados["error"] = "4";
	}
    }else{
	$resultados["mensaje"] = "Error Qr Cliente incorrecto verifique datos del cliente";
	$resultados["error"] = "3";
	}
  }/*else
   {
	$q="update balanzas set status='21' where codigo='$usu'";
        $result = mysql_query($q);
	$resultados["mensaje"] = "Kit # $usu Entregada Al Cliente";
	$resultados["error"] = "1";
	$are=array(0=>strtolower(trim($row['email'])));
                        $mensaje="Estimado(a): ".$row['fullname']."\n\n\t\t El Kit para realizar orden de servicio de planchado fue entregado en su domicilio \n "
                        ."Su cuenta email: ".strtolower(trim($row['email']));
			$arreglo=array('id_cliente'=>$cli,'titulo'=>"KIT ENTREGADO",'mensaje'=>$mensaje);
			enviar_curl("http://api.soloplancho.com/notifications/sendNotification.php", $arreglo);
                        enviar_mensaje($are, $mensaje, 'KIT ENTREGADO AL CLIENTE, SOLOPLANCHO.COM');

   }*/
}else{
	$resultados["mensaje"] = "Error actualizando orden faltan datos";
	$resultados["error"] = "3";
}
/*convierte los resultados a formato json*/
echo json_encode($resultados);
?>
