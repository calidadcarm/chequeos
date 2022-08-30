<?php
/*
   ----------------------------------------------------------
   Plugin Checks 1.0.0
   GLPI 9.1.X
  
   Autor: Javier David Marín Zafrilla
   Fecha: Septiembre 2017
   ----------------------------------------------------------
 */

include ("../../../inc/includes.php");
	  
Html::header(__('Checks', 'checks'),
      $_SERVER['PHP_SELF'],
      "config",
      "PluginChecksCheck",
      "plugin_checks"
);
$dropdown = new PluginChecksCheck();

include (GLPI_ROOT . "/front/dropdown.common.form.php");

?>