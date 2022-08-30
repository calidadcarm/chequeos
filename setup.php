<?php
/*
   ----------------------------------------------------------
   Plugin Checks 1.0.0
   GLPI 9.1.X
  
   Autor: Javier David Marín Zafrilla
   Fecha: Septiembre 2017
   ----------------------------------------------------------
 */


// Init the hooks of the plugins -Needed
function plugin_init_checks() {
   global $PLUGIN_HOOKS,$CFG_GLPI;
   
   // CSRF compliance : All actions must be done via POST and forms closed by Html::closeForm();
     $PLUGIN_HOOKS['csrf_compliant']['checks'] = true;
   //$PLUGIN_HOOKS['add_css']['checks']="css/checkeos.css"; // Añade al head de la pagina el css.
     

   $plugin = new Plugin();
   
   if ($plugin->isInstalled("checks") 
      && $plugin->isActivated("checks") 
         && isset($_SESSION['glpiactiveprofile'])) {
	  // Registro de clases
		Plugin::registerClass('PluginChecksProfile', array('addtabon' => array('Profile'))); 
		Plugin::registerClass('PluginChecksLog', array('addtabon' => array('email'))); 
		Plugin::registerClass('PluginChecksCheck'); 	// Chequeos

	    $PLUGIN_HOOKS['change_profile']['checks'] = array('PluginChecksProfile', 'initProfile');		
		if (Session::haveRight("plugin_checks", CREATE)) {
			$PLUGIN_HOOKS['menu_toadd']['checks'] = array('config' => 'PluginChecksCheck');
			$PLUGIN_HOOKS['config_page']['checks'] = 'front/check.php';
		}
		
		// Add specific JavaScript
	//	$PLUGIN_HOOKS['add_javascript']['checks'][] = 'scripts/checkeos.js';
		
		//Menu management
		$PLUGIN_HOOKS['submenu_entry']['checks']['options']['check']['title']
                                                   = PluginChecksCheck::getTypeName();
		$PLUGIN_HOOKS['submenu_entry']['checks']['options']['check']['page'] = '/plugins/checks/front/check.php';
		$PLUGIN_HOOKS['submenu_entry']['checks']['options']['check']['links']['search'] = '/plugins/checks/front/check.php';

		if (Session::haveRight("plugin_checks",CREATE)) {
                   $PLUGIN_HOOKS['submenu_entry']['checks']['options']['check']['links']['add'] = '/plugins/checks/front/check.form.php';
		}
		$PLUGIN_HOOKS['cron']['checks'] = 1800;	
		$PLUGIN_HOOKS['admin_menu_entry']['checks'] = true;
		
		
   }
	
   return $PLUGIN_HOOKS;
}


// Get the name and the version of the plugin
function plugin_version_checks() {

   return array('name'          => _n('Chequeos' , 'Chequeos' ,2, 'checks'),
                'version'        => '1.2.2',
                'license'        => 'AGPL3',
                'author'         => '<a href="https://www.carm.es" target="_blank" >CARM</a>',
                'homepage'       => 'http://www.carm.es',
                'minGlpiVersion' => '9.1');
}


// Optional : check prerequisites before install : may print errors or add to message after redirect
function plugin_checks_check_prerequisites() {

   /*if (version_compare(GLPI_VERSION,'9.1','lt')) {
     echo "This plugin requires GLPI >= 9.1";
      return false;
   }*/
   return true;
}


// Check configuration process for plugin : need to return true if succeeded
// Can display a message only if failure and $verbose is true
function plugin_checks_check_config($verbose=false) {
   if (true) {
      // Always true ...
      return true;
   }

   if ($verbose) {
      _e('Installed / not configured', 'checks');
   }
   return false;
}
?>
