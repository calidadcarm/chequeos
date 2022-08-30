<?php
/*
   ----------------------------------------------------------
   Plugin Checks 1.0.0
   GLPI 9.1.X
  
   Autor: Javier David Marín Zafrilla
   Fecha: Septiembre 2017
   ----------------------------------------------------------
 */
 
// Install process for plugin : need to return true if succeeded
function plugin_checks_install() {
   global $DB;

   
   //creamos la tabla si no existe
   if ((!$DB->TableExists("glpi_plugin_checks_checks")) OR (!$DB->TableExists("glpi_plugin_checks_log_email"))){
		$DB->runFile(GLPI_ROOT . '/plugins/checks/sql/install.sql');
   } 
   
   
  if (!$DB->FieldExists("glpi_plugin_checks_checks","remitente_id", false)) {
      $query = "ALTER TABLE `glpi_plugin_checks_checks`
                ADD `remitente_id` int NOT NULL default '0'";
      $DB->queryOrDie($query, "0.5 add field remitente_id");  

   }

  if (!$DB->FieldExists("glpi_plugin_checks_checks","remitente_name", false)) {
      $query = "ALTER TABLE `glpi_plugin_checks_checks`
                ADD `remitente_name` varchar(100) default NULL";
      $DB->queryOrDie($query, "0.5 add field remitente_name");  

   }   
   
  if (!$DB->FieldExists("glpi_plugin_checks_checks","remitente_email", false)) {
      $query = "ALTER TABLE `glpi_plugin_checks_checks`
                ADD `remitente_email` varchar(100) default NULL";
      $DB->queryOrDie($query, "0.5 add field remitente_email");  

   }    
   
   	  CronTask::unregister('PluginChecksCheck');
   
   
   Toolbox::logInFile("checks", "Plugin installation\n");
   
   
            	  CronTask::unregister('ChecksCheck');
   
   //comprobamos si esta creada la tarea automatica y si no la creamos.
   $cron = new CronTask; 
  if (!$cron->getFromDBbyName('PluginChecksCheck','cronChequeosCron'))
  {
	CronTask::Register('PluginChecksCheck', 'ChequeosCron', 3600, array('param' => 24, 'mode' => 2));
  }
   return true;
}


// Uninstall process for plugin : need to return true if succeeded
function plugin_checks_uninstall() {
   global $DB;
   
Toolbox::logInFile("checks", "Plugin uninstall\n");
   
   return true;
}


function plugin_checks_postinit() {
   global $CFG_GLPI;
   
   
   
   return true;
}
?>