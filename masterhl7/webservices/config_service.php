<?php
$dir_index = $_SERVER['SCRIPT_FILENAME'];
//Direccion del fichero de configuracion
$dir_aplication = substr($dir_index, 0, strrpos($dir_index, '/web/')) . '/apps/';
$config_file = $dir_aplication . 'comun/config_service.php';
include_once($config_file);
//Clases comunes de configuracion

set_include_path(get_include_path() . PATH_SEPARATOR . $config['include_path']);

