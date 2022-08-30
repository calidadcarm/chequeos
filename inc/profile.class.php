<?php
/*
   ----------------------------------------------------------
   Plugin Checks 1.0.0
   GLPI 9.1.X
  
   Autor: Javier David Marín Zafrilla.
   Fecha: Septiembre 2017
   ----------------------------------------------------------
 */

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

class PluginChecksProfile extends Profile {

   static $rightname = "profile";

   function getTabNameForItem(CommonGLPI $item, $withtemplate=0) {

      if ($item->getType()=='Profile') {
            return PluginChecksCheck::getTypeName(2);
      }
      return '';
   }


   static function DisplayTabContentForItem(CommonGLPI $item, $tabnum=1, $withtemplate=0) {
      global $CFG_GLPI;

      if ($item->getType()=='Profile') {
         $ID = $item->getID();
         $prof = new self();

         self::addDefaultProfileInfos($ID, 
                                    array('plugin_checks' => 0));
         $prof->showForm($ID);
      }
      return true;
   }
   
   static function createFirstAccess($ID) {
      //85
      self::addDefaultProfileInfos($ID,
                                    array('plugin_checks' => 127), true);
   }
   
    /**
    * @param $profile
   **/
  static function addDefaultProfileInfos($profiles_id, $rights, $drop_existing = false) {
      global $DB;
      
      $profileRight = new ProfileRight();
      foreach ($rights as $right => $value) {
		  
		  $criteria = [
"profiles_id" => $profiles_id,
"name" => $right,
];
		  
         if (countElementsInTable('glpi_profilerights',
                                   $criteria) && $drop_existing) {
            $profileRight->deleteByCriteria($criteria);
         }
         if (!countElementsInTable('glpi_profilerights',
                                   $criteria)) {
            $myright['profiles_id'] = $profiles_id;
            $myright['name']        = $right;
            $myright['rights']      = $value;
            $profileRight->add($myright);

            //Add right to the current session
            $_SESSION['glpiactiveprofile'][$right] = $value;
         }
      }
   }


   /**
    * Show profile form
    *
    * @param $items_id integer id of the profile
    * @param $target value url of target
    *
    * @return nothing
    **/
   function showForm($profiles_id=0, $openform=TRUE, $closeform=TRUE) {

      echo "<div class='firstbloc'>";
      if (($canedit = Session::haveRightsOr(self::$rightname, array(CREATE)))
          && $openform) {
         $profile = new Profile();
         echo "<form method='post' action='".$profile->getFormURL()."'>";
      }

      $profile = new Profile();
      $profile->getFromDB($profiles_id);
      if ($profile->getField('interface') == 'central') {
         $rights = $this->getAllRights();	
         $profile->displayRightsChoiceMatrix($rights, array('canedit'       => $canedit,
                                                         'default_class' => 'tab_bg_2',
                                                         'title'         => __('General')));
      }
       
      if ($canedit
          && $closeform) {
         echo "<div class='center'>";
         echo Html::hidden('id', array('value' => $profiles_id));
         echo Html::submit(_sx('button', 'Save'), array('name' => 'update'));
         echo "</div>\n";
         Html::closeForm();
      }
      echo "</div>";
   }

   static function getAllRights($all = false) {
      $rights = array(
          array('rights'    => array(ALLSTANDARDRIGHT  => 'Habilitar'),
              'itemtype'  => 'PluginChecksCheck',
                'label'     => _n('Chequeos', 'Chequeos', 2, 'Chequeos'),
                'field'     => 'plugin_checks'
          ),
      );
      
      return $rights;
   }
 
   
   
   /**
    * Init profiles
    *
    **/
    
   static function translateARight($old_right) {
      switch ($old_right) {
         case '': 
            return 0;
         case 'r' :
            return READ;
         case 'w':
            return ALLSTANDARDRIGHT + READNOTE + UPDATENOTE;
         case '0':
         case '1':
            return $old_right;
            
         default :
            return 0;
      }
   }
      
   /**
   * Initialize profiles, and migrate it necessary
   */
   static function initProfile() {
      global $DB;
      $profile = new self();

      //Add new rights in glpi_profilerights table
      foreach ($profile->getAllRights(true) as $data) {
		  
		  $criteria = [
"name" => $data['field'],
];		  
		  
         if (countElementsInTable("glpi_profilerights", 
                                  $criteria) == 0) {
            ProfileRight::addProfileRights(array($data['field']));
         }
      }
      foreach ($DB->request("SELECT *
                           FROM `glpi_profilerights` 
                           WHERE `profiles_id`='".$_SESSION['glpiactiveprofile']['id']."' 
                              AND `name` LIKE '%plugin_checks%'") as $prof) {
         $_SESSION['glpiactiveprofile'][$prof['name']] = $prof['rights']; 
      }
   }

   
  static function removeRightsFromSession() {
      foreach (self::getAllRights(true) as $right) {
         if (isset($_SESSION['glpiactiveprofile'][$right['field']])) {
            unset($_SESSION['glpiactiveprofile'][$right['field']]);
         }
      }
   }   
}
?>