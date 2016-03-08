<?php
include('db.php');
/* Define los valores que seran evaluados, en este ejemplo son valores estaticos,
en la verdadera aplicacion son dinamicos a partir de una base de datos */

/* Extrae los valores enviados desde la aplicacion movil */
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }

    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

        exit(0);
    }


if(!isset($_REQUEST['username']) || !isset($_REQUEST['password']))
{
	$resultados["mensaje"] = "Campos Vacios";
	$resultados["validacion"] = "error";
}elseif(empty($_REQUEST['username']) || !isset($_REQUEST['password']))
{
	$resultados["mensaje"] = "Campos Vacios";
	$resultados["validacion"] = "error";
}else{
$usuarioEnviado = $_REQUEST['username'];
$passwordEnviado = $_REQUEST['password'];
if(isset($_REQUEST['username']))
$resultados = array();
 $q="select email, password from clientes where email='$usuarioEnviado' and password='$passwordEnviado' limit 1";
 $result = mysql_query($q);
	$row = mysql_fetch_array($result, MYSQL_ASSOC);
$usuarioValido = $row['email'];
$passwordValido = $row['password'];

/* verifica que el usuario y password concuerden correctamente */
if(  $usuarioEnviado == $usuarioValido  && $passwordEnviado == $passwordValido ){
	/*esta informacion se envia solo si la validacion es correcta */
	/*$resultados["fullname"] = $row['fullname'];
	$resultados["reg_id"] = $row['reg_id'];
	$resultados["cedula"] = $row['cedula'];*/
	$resultados["mensaje"] = "Usuario Correcto";
	$resultados["validacion"] = "ok";
    
}else{
	/*esta informacion se envia si la validacion falla */
	$resultados["mensaje"] = "Usuario y password incorrectos";
	$resultados["validacion"] = "error";
}

}

/*convierte los resultados a formato json*/
$resultadosJson = json_encode($resultados);
echo $_GET['jsoncallback'] . '' . $resultadosJson . '';
?>
