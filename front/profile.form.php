<?php
/*
   ----------------------------------------------------------
   Plugin Checks 1.0.0
   GLPI 9.1.X
  
   Autor: Javier David Marín Zafrilla
   Fecha: Septiembre 2017
   ----------------------------------------------------------
 */
 
define('GLPI_ROOT', '../../..');
include (GLPI_ROOT."/inc/includes.php");
include_once (GLPI_ROOT."/plugins/checks/inc/profile.class.php");

Session::checkRight("profile",CREATE);
$prof=new PluginChecksProfile();


//Save profile
if (isset ($_POST['UPDATE'])) {
	$prof->update($_POST);
	Html::back();
}

?>
