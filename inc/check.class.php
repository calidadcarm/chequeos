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
   die("Sorry. You can't access this file directly");
}

class PluginChecksCheck extends CommonDropdown {

   // From CommonDBTM
   public $dohistory              = true;

   // From CommonDropdown
   public $first_level_menu       = "admin";
   public $second_level_menu      = "plugin_checks";
   static $rightname              = 'plugin_checks';
   public $can_be_translated  = false;
   public $display_dropdowntitle  = false;

const CONFIG_PARENT   = - 2;

   static function getTypeName($nb=0) {
      return __('Chequeos');
   }


   static function displayTabContentForItem(CommonGLPI $item, $tabnum=1, $withtemplate=0) {

      switch ($item->getType()) {
         case 'PluginChecksCheck' :
            switch ($tabnum) {
               case 1 :
                  $item->showInfos();
                  return true;
               case 2 :
                  $item->showEmails();
                  return true;				  
				  
            }
            break;
      }
      return false;
   }

 
   function getTabNameForItem(CommonGLPI $item, $withtemplate=0) {

      if (Session::haveRight('plugin_checks', CREATE)) {
         switch ($item->getType()) {
            case 'PluginChecksCheck' :
               $ong[1] = _n('Information', 'Information', Session::getPluralNumber());
			   $ong[2] = _n('Email', 'Email', Session::getPluralNumber());
               return $ong;
         }
      }
      return '';
   }

   


   function defineTabs($options=array()) {

      $ong = array();
      $this->addDefaultFormTab($ong);
      $this->addStandardTab(__CLASS__, $ong, $options);
      $this->addStandardTab('Log', $ong, $options);

      return $ong;
   }


   function prepareInputForAdd($input) {

      $input['next_creation_date'] = $this->chequeoNextCreationDate($input['begin_date'],
                                                                    $input['end_date'],
                                                                    $input['periodicity'],
                                                                    $input['create_before'],
                                                                    $input['calendars_id']);
      return $input;
   }


   function prepareInputForUpdate($input) {

      if (isset($input['begin_date'])
          && isset($input['periodicity'])
          && isset($input['create_before'])) {

         $input['next_creation_date'] = $this->chequeoNextCreationDate($input['begin_date'],
                                                                       $input['end_date'],
                                                                       $input['periodicity'],
                                                                       $input['create_before'],
                                                                       $input['calendars_id']);
      }
      return $input;
   }


   /**
    * Return Additional Fileds for this type
   **/
   function getAdditionalFields() {

      return array(array('name'  => 'is_active',
                         'label' => __('Active'),
                         'type'  => 'bool',
                         'list'  => false),
                   array('name'  => 'begin_date',
                         'label' => __('Start date'),
                         'type'  => 'datetime',
                         'list'  => false),
                   array('name'  => 'end_date',
                         'label' => __('End date'),
                         'type'  => 'datetime',
                         'list'  => false),
                   array('name'  => 'periodicity',
                         'label' => __('Periodicity'),
                         'type'  => 'specific_timestamp',
                         'min'   => DAY_TIMESTAMP,
                         'step'  => DAY_TIMESTAMP,
                         'max'   => 2*MONTH_TIMESTAMP),
                   array('name'  => 'create_before',
                         'label' => __('Preliminary creation'),
                         'type'  => 'timestamp',
                         'max'   => 7*DAY_TIMESTAMP,
                         'step'  => HOUR_TIMESTAMP),
                   array('name'  => 'calendars_id',
                         'label' => _n('Calendar', 'Calendars', 1),
                         'type'  => 'dropdownValue',
                         'list'  => true),
                   array('name'  => 'consulta',
                         'label' => 'Consulta',
                         'type'  => 'textarea',
                         'list'  => true),
                    array('name'  => 'asunto',
                         'label' => 'Asunto',
                         'type'  => 'text',
                         'list'  => true),
                   array('name'  => 'cabecera',
                         'label' => 'Cabecera',
                         'type'  => 'textarea',
                         'list'  => true),
                    array('name'  => 'pie',
                         'label' => 'Pie',
                         'type'  => 'textarea',
                         'list'  => true),
                     array('name'  => 'url_elemento',
                         'label' => 'URL Elemento',
                         'type'  => 'text',
                         'list'  => true),
                  );
                  
   }


   
      function dropdownFrequency($name, $value=0) {

      $tab = array();

     // $tab[MINUTE_TIMESTAMP] = sprintf(_n('%d minute','%d minutes',1),1);

      // Minutes
      for ($i=5 ; $i<60 ; $i+=5) {
         $tab[$i*MINUTE_TIMESTAMP] = sprintf(_n('%d minute','%d minutes',$i), $i);
      }

      // Heures
      for ($i=1 ; $i<24 ; $i++) {
         $tab[$i*HOUR_TIMESTAMP] = sprintf(_n('%d hour','%d hours',$i), $i);
      }

      // Jours
    //  $tab[DAY_TIMESTAMP] = __('Each day');
      for ($i=1 ; $i<31 ; $i++) {
         $tab[$i*DAY_TIMESTAMP] = sprintf(_n('%d day','%d days',$i),$i);
      }

      for ($i=1 ; $i<12 ; $i++) {
         $tab[$i*MONTH_TIMESTAMP] = sprintf(_n('%d month','%d months',$i),$i);
      }	  

        for ($i=1 ; $i<5 ; $i++) {
         $tab[$i*(DAY_TIMESTAMP*365)] = sprintf(_n('%d year', '%d years', $i), $i);
      }
	  
 //     $tab[WEEK_TIMESTAMP]  = __('Each week');
 //     $tab[MONTH_TIMESTAMP] = __('Each month');

      Dropdown::showFromArray($name, $tab, array('value' => $value));
   }
   
   public function showForm ($ID, $options=array()) {
	  
	  global $CFG_GLPI, $DB;
  
	  $this->initForm($ID, $options);
      $this->showFormHeader($options);
	  
	 //Nombre del Chequeo
	  echo "<tr class='tab_bg_1'>";
      echo "<th>".__('Name')."</th>";
	  echo "<td colspan='3'>";	  
	  echo "<input type='text' style='width:98%' maxlength=250 name='name' ".
                " value=\"".Html::cleanInputText($this->fields["name"])."\">";
	  echo "</td>";	  
      echo "</tr>";	  
	  
	  // Chequeo ACTIVO
	  echo "<tr class='tab_bg_1'>";
      echo "<th>".__('Active')."</th>";
	  echo "<td colspan='1'>";	  
	  Dropdown::showYesNo("is_active",$this->fields["is_active"]);
	  echo "</td>";	 
      echo "<th>".__('Periodicity')."</th>";
	  echo "<td colspan='1'>";	  
	  $this->dropdownFrequency('periodicity', $this->fields["periodicity"]);
	  echo "</td>";	 
      echo "</tr>";	  
	 
	  echo "<tr class='tab_bg_1'>";
  	  echo "<th colspan='1'>".__('Preliminary creation')."</th>";
	  echo "<td colspan='1'>";
	  Dropdown::showTimeStamp("create_before",   array('name'  => 'create_before',
                         'label' => __('Preliminary creation'),
						 'value' => $this->fields["create_before"],
                         'type'  => 'timestamp',
                         'max'   => 7*DAY_TIMESTAMP,
                         'step'  => MINUTE_TIMESTAMP*5));  
						 
      echo "</td>";
	  
	  
	  		$entity = new Entity();
			if (!$entity->getFromDB($ID)) {
				$entity->getEmpty();
			}
	  echo "<th>"._n('Calendar', 'Calendars', 1)."</th>";
	  echo "<td colspan='1'>";
	  $options = array('value'      => $this->fields["calendars_id"]);
	  Dropdown::show('Calendar', $options);

				if ($entity->fields["calendars_id"] == self::CONFIG_PARENT) {
					echo "<font class='green'>&nbsp;&nbsp;";
					$calendar = new Calendar();
					$cid = Entity::getUsedConfig('calendars_id', $ID, '', 0);
					echo "</font>";
				}
				
	  echo "</td>";
	  echo "</tr>";
	  
	  //fila para las fechas inicial y final del chequeo
	  echo "<tr class='tab_bg_1'>";
	  echo "<th>".__('Start date')."</th>";
	  echo "<td colspan='1'>";

	  // Chequeo FECHA Inicial
	  $canupdate = 1;	  
	  
         Html::showDateTimeField("begin_date", ['value'      => $this->fields["begin_date"],
                                          'timestep'   => 1,
                                          'maybeempty' => true]);	  
	  
	  //Html::showDatetimeFormItem("begin_date",$this->fields["begin_date"], 1, true, $canupdate);	  
	  echo "</td>";
	  echo "<th>".__('End date')."</th>";
	  echo "<td colspan='1'>";

         Html::showDateTimeField("end_date", ['value'      => $this->fields["end_date"],
                                          'timestep'   => 1,
                                          'maybeempty' => true]);		 
	 
	 // Html::showDatetimeFormItem("end_date",$this->fields["end_date"], 1, true, $canupdate);
	  echo "</td>";
	  echo "</tr>";
	  
	  //fila para los datos del remitente
	  echo "<tr class='tab_bg_1'>";
	  echo "<th>".__('Remitente')."</th>";
	  echo "<td colspan='1'>";
	  echo "<input type='text' style='width:95%' maxlength=100 name='remitente_name' ".
                " value=\"".Html::cleanInputText($this->fields["remitente_name"])."\">";
	  echo "</td>";	  
	  echo "<th>".__('Dirección Remitente')."</th>";
	  echo "<td colspan='1'>";
	  echo "<input type='text' style='width:95%' maxlength=100 name='remitente_email' ".
                " value=\"".Html::cleanInputText($this->fields["remitente_email"])."\">";
	  
	  echo "</td>";	
	  echo "</tr>";	
	  
	 //Fila para el asunto email del Chequeo
      echo "<tr class='tab_bg_1'>";
			echo "<th>".__('Asunto')."</th>";
			echo "<td colspan='3'>";
				echo "<input type='text' style='width:98%' maxlength=250 name='asunto' ".
					" value=\"".Html::cleanInputText($this->fields["asunto"])."\">";				
			echo "</td>";
      echo "</tr>";	  	

	  //Fila para la cabecera email del Chequeo
	  echo "<tr class='tab_bg_1'>";
	  echo "<th>".__('Cabecera')."</th>";
	  echo "<td colspan='4'>";
	  echo "<textarea name='cabecera' cols='90' rows='6' style='width:98%'>".
            $this->fields["cabecera"]."</textarea>";
      echo "</td></tr>";	  
  
  	 //URL ENLACES DEL email del Chequeo
      echo "<tr class='tab_bg_1'>";
			echo "<th>".__('URL Elemento')."</th>";
			echo "<td colspan='3'>";
				echo "<input type='text' style='width:98%' maxlength=250 name='url_elemento' ".
					" value=\"".Html::cleanInputText($this->fields["url_elemento"])."\">";				
				
			echo "</td>";
      echo "</tr>";	 
	  
	  //Fila para la consulta sql
	  echo "<tr class='tab_bg_1'>";
	  echo "<th>".__('Consulta')."</th>";
	  echo "<td colspan='4'>";
	  echo "<textarea name='consulta' cols='90' rows='9' style='width:98%'>".
            $this->fields["consulta"]."</textarea>";
      echo "</td></tr>";		

	  //Fila para el pie del email
	  echo "<tr class='tab_bg_1'>";
	  echo "<th>".__('Pie')."</th>";
	  echo "<td colspan='4'>";
	  echo "<textarea name='pie' cols='90' rows='6' style='width:98%'>".
            $this->fields["pie"]."</textarea>";
      echo "</td></tr>";		  
 
	  //Fila para los comentarios del chequeo
	  echo "<tr class='tab_bg_1'>";
	  echo "<th>".__('Comentarios')."</th>";
	  echo "<td colspan='4'>";
	  echo "<textarea name='comment' cols='90' rows='6' style='width:98%'>".
            $this->fields["comment"]."</textarea>";
      echo "</td></tr>";	 
	  
  
	// Ultima modificación
	echo "<tr>";
	  echo "<td class='center' colspan='4'>";
      printf(__('Next task to run: %s'), Html::convDateTime($this->fields["next_creation_date"]));
      echo "</td>";
	echo "</tr>";

	echo "<tr>";
	  echo "<td class='center' colspan='4'>";
     $this->showFormButtons($options); 
      echo "</td>";
	echo "</tr>";
	
	    
      return true;
   }

   /**
    * @since version 0.83.1
    *
    * @see CommonDropdown::displaySpecificTypeField()
   **/
   function displaySpecificTypeField($ID, $field=array()) {

      switch ($field['name']) {
         case 'periodicity' :
            $possible_values = array();
            for ($i=1 ; $i<24 ; $i++) {
               $possible_values[$i*HOUR_TIMESTAMP] = sprintf(_n('%d hour','%d hours',$i), $i);
            }
            for ($i=1 ; $i<=30 ; $i++) {
               $possible_values[$i*DAY_TIMESTAMP] = sprintf(_n('%d day','%d days',$i), $i);
            }

            for ($i=1 ; $i<12 ; $i++) {
               $possible_values[$i.'MONTH'] = sprintf(_n('%d month','%d months',$i), $i);
            }

            for ($i=1 ; $i<5 ; $i++) {
               $possible_values[$i.'YEAR'] = sprintf(_n('%d year','%d years',$i), $i);
            }

            Dropdown::showFromArray($field['name'], $possible_values,
                                    array('value' => $this->fields[$field['name']]));
            break;
      }
   }

   /**
    * @since version 0.84
    *
    * @param $field
    * @param $values
    * @param $options   array
   **/
   static function getSpecificValueToDisplay($field, $values, array $options=array()) {

      if (!is_array($values)) {
         $values = array($field => $values);
      }

      switch ($field) {
         case 'periodicity' :
            if (preg_match('/([0-9]+)MONTH/',$values[$field], $matches)) {
               return sprintf(_n('%d month','%d months',$matches[1]), $matches[1]);
            }
            if (preg_match('/([0-9]+)YEAR/',$values[$field], $matches)) {
               return sprintf(_n('%d year','%d years',$matches[1]), $matches[1]);
            }
            return Html::timestampToString($values[$field], false);
         break;
      }
      return parent::getSpecificValueToDisplay($field, $values, $options);
   }


   /**
    * Get search function for the class
    *
    * @return array of search option
   **/
   
      function rawSearchOptions() {
      $tab = [];    
	  
	  $tab = array_merge($tab, parent::rawSearchOptions());
	  
      $tab[] = [
         'id'                 => '100',
         'table'              => $this->getTable(),
         'field'              => 'is_active',
         'name'               =>  __('Active'),
         'datatype'           => 'bool',
      ];
	  
      $tab[] = [
         'id'                 => '101',
         'table'              => $this->getTable(),
         'field'              => 'begin_date',
         'name'               =>  __('Start date'),
         'datatype'           => 'datetime',
      ];	  
	  
      $tab[] = [
         'id'                 => '102',
         'table'              => $this->getTable(),
         'field'              => 'end_date',
         'name'               =>  __('End date'),
         'datatype'           => 'datetime',
      ];
	  
      $tab[] = [
         'id'                 => '103',
         'table'              => $this->getTable(),
         'field'              => 'periodicity',
         'name'               =>  __('Periodicity'),
         'datatype'           => 'specific',
      ];

      $tab[] = [
         'id'                 => '104',
         'table'              => $this->getTable(),
         'field'              => 'create_before',
         'name'               =>  __('Preliminary creation'),
         'datatype'           => 'timestamp',
      ];

      $tab[] = [
         'id'                 => '105',
         'table'              => 'glpi_calendars',
         'field'              => 'name',
         'name'               => _n('Calendar', 'Calendars', 1),
         'datatype'           => 'text',
      ];	  

      $tab[] = [
         'id'                 => '106',
         'table'              => $this->getTable(),
         'field'              => 'remitente_name',
         'name'               =>  __('Nombre Remitente'),
         'datatype'           => 'text',
      ];

      $tab[] = [
         'id'                 => '107',
         'table'              => $this->getTable(),
         'field'              => 'remitente_email',
         'name'               =>  __('Dirección Remitente'),
         'datatype'           => 'text',
      ];
	
      $tab[] = [
         'id'                 => '108',
         'table'              => $this->getTable(),
         'field'              => 'asunto',
         'name'               =>  __('Asunto'),
         'datatype'           => 'text',
      ];
	  
      $tab[] = [
         'id'                 => '109',
         'table'              => $this->getTable(),
         'field'              => 'cabecera',
         'name'               =>  __('Cabecera'),
         'datatype'           => 'textarea',
      ];
 
      $tab[] = [
         'id'                 => '110',
         'table'              => $this->getTable(),
         'field'              => 'url_elemento',
         'name'               =>  __('URL Elemento'),
         'datatype'           => 'text',
      ];	  	
		  
      $tab[] = [
         'id'                 => '111',
         'table'              => $this->getTable(),
         'field'              => 'consulta',
         'name'               =>  __('Consulta'),
         'datatype'           => 'textarea',
      ];			  
	
	  
      $tab[] = [
         'id'                 => '112',
         'table'              => $this->getTable(),
         'field'              => 'pie',
         'name'               =>  __('Pie'),
         'datatype'           => 'textarea',
      ];	
	  
      $tab[] = [
         'id'                 => '113',
         'table'              => $this->getTable(),
         'field'              => 'comment',
         'name'               =>  __('Comentarios'),
         'datatype'           => 'textarea',
      ];
  
  
      return $tab;
   }
   

   /**
    * Show next creation date
    *
    * @return nothing only display
   **/
   function showInfos() {

      if (!is_null($this->fields['next_creation_date'])) {
         echo "<div class='center b big'><p><br><br><font color='#722190;'>";
         //TRANS: %s is the date of next creation
         echo sprintf(__('Next creation on %s'),
                      Html::convDateTime($this->fields['next_creation_date']));
         echo "</font><br><br><br></p></div>";
      }
   }

 
   /**
    * Show Emails that the plugin has sent
    *
    * @return nothing only display
   **/
   function showEmails() {

  // session_start();
   
   $_SESSION["check_id"]=$this->fields['id'];
   $_SESSION["check_name"]=$this->fields['name'];
   
    $log = new PluginChecksLog();
   
   $log:: showForCheck();
   
   
   
    /*  if (!is_null($this->fields['next_creation_date'])) {
         echo "<div class='center b big'><p><br><br><font color='#722190;'>";
         //TRANS: %s is the date of next creation
      /*   echo sprintf(__('Next creation on %s'),
                      Html::convDateTime($this->fields['next_creation_date']));*/
					  
			/*		  var_dump($this->fields);
         echo "</font><br><br><br></p></div>";
      }*/
   }  
   

   /**
    * Compute next creation date of a ticket
    *
    * New parameter in  version 0.84 : $calendars_id
    *
    * @param $begin_date      datetime    Begin date of the recurrent ticket
    * @param $end_date        datetime    End date of the recurrent ticket
    * @param $periodicity     timestamp   Periodicity of creation
    * @param $create_before   timestamp   Create before specific timestamp
    * @param $calendars_id    integer     ID of the calendar to used
    *
    * @return datetime next creation date
   **/
   function chequeoNextCreationDate($begin_date, $end_date, $periodicity, $create_before,
                                    $calendars_id) {

      if (empty($begin_date) || ($begin_date == 'NULL')) {
         return 'NULL';
      }
      if (!empty($end_date) && ($end_date <> 'NULL')) {
         if (strtotime($end_date) < time()) {
            return 'NULL';
         }
      }
      $check = true;
      if (preg_match('/([0-9]+)MONTH/',$periodicity)
          || preg_match('/([0-9]+)YEAR/',$periodicity)) {
         $check = false;
      }

      if ($check
          && ($create_before > $periodicity)) {
         Session::addMessageAfterRedirect(__('Invalid frequency. It must be greater than the preliminary creation.'),
                                          false, ERROR);
         return 'NULL';
      }

      if ($periodicity <> 0) {
         // Standard time computation
         $timestart  = strtotime($begin_date) - $create_before;
         $now        = time();
         if ($now > $timestart) {
            $value = $periodicity;
            $step  = "second";
            if (preg_match('/([0-9]+)MONTH/',$periodicity, $matches)) {
               $value = $matches[1];
               $step  = 'MONTH';
            } else if (preg_match('/([0-9]+)YEAR/',$periodicity, $matches)) {
               $value = $matches[1];
               $step  = 'YEAR';
            } else {
               if (($value%DAY_TIMESTAMP)==0) {
                  $value = $value/DAY_TIMESTAMP;
                  $step  = "DAY";
               } else {
				   if (($value%HOUR_TIMESTAMP)==0) {
                  $value = $value/HOUR_TIMESTAMP;
                  $step  = "HOUR";
               } else {
			      $value = $value/MINUTE_TIMESTAMP;
                  $step  = "MINUTE";
			   
			   } }
			   
			    

            }

            while ($timestart < $now) {
               $timestart = strtotime("+ $value $step",$timestart);
            }
         }
         // Time start over end date
         if (!empty($end_date) && ($end_date <> 'NULL')) {
            if ($timestart > strtotime($end_date)) {
               return 'NULL';
            }
         }

         $calendar = new Calendar();
         if ($calendars_id
             && $calendar->getFromDB($calendars_id)) {
            $durations = $calendar->getDurationsCache();
            if (array_sum($durations) > 0) { // working days exists
               while (!$calendar->isAWorkingDay($timestart)) {
                  $timestart = strtotime("+ 1 day",$timestart);
               }
            }
         }
         
         return date("Y-m-d H:i:s", $timestart);
      }

      return 'NULL';
   }


   /**
    * Give cron information
    *
    * @param $name : task's name
    *
    * @return arrray of information
   **/
   static function cronInfo($name) {

      switch ($name) {
         case 'PluginChecksCheck' :
            return array('description' => self::getTypeName(Session::getPluralNumber()));
      }
      return array();
   }


   /**
    * Cron for ticket's automatic close
    *
    * @param $task : crontask object
    *
    * @return integer (0 : nothing done - 1 : done)
   **/
   
   static function cronChequeosCron($task) {
      global $DB;

      $tot = 0;
						   
				$query =  "SELECT `glpi_users`.name as usuario, `glpi_users`.firstname, `glpi_users`.realname, `glpi_useremails`.email, `glpi_plugin_checks_checks`.*
                FROM `glpi_plugin_checks_checks` left join `glpi_users` on `glpi_plugin_checks_checks`.remitente_id=`glpi_users`.id left join `glpi_useremails` on `glpi_plugin_checks_checks`.remitente_id=`glpi_useremails`.users_id and `glpi_useremails`.is_default=1
                WHERE `glpi_plugin_checks_checks`.`next_creation_date` < NOW()
                      AND `glpi_plugin_checks_checks`.`is_active` = 1
                      AND (`glpi_plugin_checks_checks`.`end_date` IS NULL
                           OR `glpi_plugin_checks_checks`.`end_date` > NOW())";
      
	 // escribe en log la consulta realizada.
	 // Toolbox::logInFile("consultas_checks",  "\r\n\r\n consulta \r\n\r\n" . $query . "\r\n\r\n");
	  
      $consulta=$DB->request($query);
     
      foreach ($consulta as $data) {
         if (self::generateChek($data)) {
            $tot++;
         } else {
            //TRANS: %s is a name
            $task->log(sprintf('Fallo al crear el checkeo',
                               $data['name']));
         }
      }


      $task->setVolume($tot);
			  //[INICIO] JMZ18G quitar is_deleted para que no aparezca en la papelera los correos enviados.
     $query = "INSERT INTO glpi_plugin_checks_logs
SELECT  `glpi_queuednotifications`.`id`,
		`glpi_queuednotifications`.`itemtype`,
		`glpi_queuednotifications`.`items_id`,
		`glpi_queuednotifications`.`notificationtemplates_id`,
		`glpi_queuednotifications`.`entities_id`,
		`glpi_queuednotifications`.`is_deleted`,
		`glpi_queuednotifications`.`sent_try`,
		`glpi_queuednotifications`.`create_time`,
		`glpi_queuednotifications`.`send_time`,
		`glpi_queuednotifications`.`sent_time`,
		 concat('Número de envío: (', id, ')') as `name`,
		`glpi_queuednotifications`.`name` as `name_complete`, 
		`glpi_queuednotifications`.`sender`,
		`glpi_queuednotifications`.`sendername`,
		`glpi_queuednotifications`.`recipient`,
		`glpi_queuednotifications`.`recipientname`,
		`glpi_queuednotifications`.`replyto`,
		`glpi_queuednotifications`.`replytoname`,
		`glpi_queuednotifications`.`headers`,
		`glpi_queuednotifications`.`body_html`,
		`glpi_queuednotifications`.`body_text`,
		`glpi_queuednotifications`.`messageid`,
		`glpi_queuednotifications`.`documents`
	FROM glpi_queuednotifications
WHERE glpi_queuednotifications.itemtype='PluginChecksCheck' and id not in (select id from glpi_plugin_checks_logs)";


 

      if ($result=$DB->query($query)) {
       //  echo "Alta hecha";
      }

//[INICIO] JMZ18G quitar is_deleted para que no aparezca en la papelera los correos enviados.	  
/*$query = "update glpi_plugin_checks_logs as a
inner join glpi_queuednotifications b on a.id=b.id and a.sent_time is null and b.sent_time is not null
set a.sent_time=b.sent_time, a.is_deleted=1";*/
//[FIN]
	  
$query = "update glpi_plugin_checks_logs as a
inner join glpi_queuednotifications b on a.id=b.id and a.sent_time is null and b.sent_time is not null
set a.sent_time=b.sent_time";
	  
      if ($result=$DB->query($query)) {
         //echo "Actualizacion hecha";
      }

//[INICIO] JMZ18G quitar is_deleted para que no aparezca en la papelera los correos enviados.	  
/*$query = "update glpi_plugin_checks_logs set is_deleted=0";
	  
      if ($result=$DB->query($query)) {
         //echo "Actualizacion hecha";
      }	  */
//[FIN]	  
      return ($tot > 0);
   }


   /**
    * Create a ticket based on ticket recurrent infos
    *
    * @param $data array data of a entry of glpi_plugin_checks_check
    *
    * @return boolean
    * 
    * 
   **/
   
   static function generateChek($data){
        global $DB;
        
      $consulta=str_replace("&gt;",">",$data['consulta']);
      $consulta=str_replace("&lt;","<",$consulta);    

      $prefijo='select * from ( ';
      $sufijo=' ) tabla group by 3';
      $sufijo_filtrado=') tabla where email_elemento=\'';
      
      
      $consulta_agrupada=$prefijo.$consulta.$sufijo;
      $consulta_filtrado=$prefijo.$consulta.$sufijo_filtrado;
	 
     
if (!$request=$DB->query($consulta_agrupada)) {
	Toolbox::logInFile("consultas_checks",  "\r\n\r\nConsulta con errores en el check: ".$data['name']."  \r\n\r\n"); 	 
}

      foreach ($request as $datos){
          
          $tabla='<tr>
                  <td style="padding: 0px;">
					<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; width: 100%;">
                    <tbody tyle="font-family:Helvetica;">';
                   
          
          $columna="";
		  
		  // escribe en log las consultas realizadas.
		 
           $fecha_completa=date("Y-m-d H:i:s");

		if (!$result2 = $DB->query($consulta_filtrado.$datos['email_elemento'].'\'')) {
		 //Toolbox::logInFile("consultas_checks",  "\r\n\r\nconsulta_agrupada \r\n\r\n" . $consulta_agrupada . "\r\n\r\n consulta_filtrado \r\n\r\n" . $consulta_filtrado.$datos['email'].'\''."\r\n\r\n");
		 Toolbox::logInFile("consultas_checks",  "\r\n\r\nConsulta con errores en el check: ".$data['name']."  \r\n\r\n"); 
		   	
		}  
		 $indice=0;
           foreach ($result2 as $detalle){
			   
               $url=$data['url_elemento'].$detalle['id_elemento'];
             
               $columna=$columna.
			   '<tr style="COLOR: #000000; BACKGROUND-COLOR: #ffffff; TEXT-ALIGN: left;"> 
                      <td style="padding-top: 5px; padding-bottom: 5px; line-height: 20px; padding-right: 15px;"><a style="COLOR: #347BB7; font-size: 15px; TEXT-DECORATION: none;" href="'.$url.'"><b>'.Html::resume_text($detalle['detalle_elemento'],80).'</b></a></td>
                     </tr>
                   <!--   <tr>
                     <td style="color: #666666; line-height: 24px; padding-right: 15px; font-size: 15px; BACKGROUND-COLOR: #ffffff;">Email - <span style="color: #515050; font-size: 15px;"><b>'.Html::resume_text($detalle['email_elemento'],80).'</b></span></td>
                     </tr>
                     <tr style="COLOR: #000000; BACKGROUND-COLOR: #ffffff; TEXT-ALIGN: left;">
                      <td style="padding-bottom: 10px; font-size: 13px; color: #666666; line-height: 24px; padding-right: 15px;">java, hibernate, spring, &#160;|&#160;Jorn. completa&#160;&#160;|&#160;Indefinido</td>
                     </tr> -->';
			$indice++;                            
           }
           $tabla=$tabla.$columna;
           $tabla=$tabla.'</tbody>
                   </table></td>
                  <td style="padding: 0px; width: 20px; BACKGROUND-COLOR: #ffffff;"><img src="" data-original-src=""></td>
                 </tr>';
           
     
     $cabecera='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
                        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                         <title>[TILENA #0073750] </title>
                         <style type="text/css">
                           @page WordSection1{

size:695.3pt 841.9pt;
margin:70.85pt 3.0cm 70.85pt 3.0cm;
mso-header-margin:36.0pt;
mso-footer-margin:36.0pt;
mso-paper-source:0;

}

div.WordSection1{

page:WordSection1;

}
                         </style>
                        </head>
                        <body style="background: #d6dde5;">
						<div class="WordSection1">
<div align="center">
<div style="text-align: center;"><br>
<table class="tab_cadre" style="width: 700px;" align="center">
<tbody>
<tr class="tab_bg_1">
<td style="text-align: left;">

  <div style="background-color: #d6dde5;">  
     <div style="margin: 0px auto; max-width: 600px; background: #347bb7;"> 
    <table cellpadding="0" cellspacing="0" style="border-collapse: collapse; font-size: 0px; width: 100%; background: #347bb7;" align="center" border="0"> 
     <tbody> 
      <tr> 
       <td style="text-align: center; vertical-align: top; direction: ltr; font-size: 0px; padding: 20px 0px; padding-bottom: 0px; padding-top: 0px;">  
        <div class="mj-column-per-100 outlook-group-fix" style="vertical-align: top; display: inline-block; direction: ltr; font-size: 13px; text-align: left; width: 100%;"> 
         <table cellpadding="0" cellspacing="0" style="border-collapse: collapse; vertical-align: top;" width="100%" border="0"> 
          <tbody> 
           <tr> 
            <td style="word-wrap: break-word; font-size: 0px; padding: 10px 25px; padding-top: 10px; padding-bottom: 15px; padding-right: 25px; padding-left: 25px;" align="center"> 
             <div class="" style="cursor: auto; color: #ffffff; font-family: Verdana, Helvetica, Arial; font-size: 15px; line-height: 25px; text-align: center;">
              '.$data['name'].'
             </div> 
			 </td> 
           </tr> 
          </tbody> 
         </table> 
        </div>  
		</td> 
      </tr> 
     </tbody> 
    </table> 
   </div> 
  
     <div style="margin: 0px auto; max-width: 600px; background: #347bb7;"> 
    <table cellpadding="0" cellspacing="0" style="border-collapse: collapse; font-size: 0px; width: 100%; background: #ffffff;" align="center" border="0"> 
     <tbody> 
      <tr> 
       <td style="text-align: center; vertical-align: top; direction: ltr; font-size: 0px; padding: 0px 0px; padding-bottom: 0px; padding-top: 0px;">  
        <div class="mj-column-per-100 outlook-group-fix" style="vertical-align: top; display: inline-block; direction: ltr; font-size: 16px; text-align: left; width: 100%;"> 
         <table cellpadding="0" cellspacing="0" style="border-collapse: collapse; vertical-align: top;" width="100%" border="0"> 
          <tbody> 
           <tr> 
            <td style="word-wrap: break-word; font-size: 0px; padding: 10px 25px; padding-top: 10px; padding-bottom: 10px; padding-right: 25px; padding-left: 25px;" align="center"> 
             <div class="" style="cursor: auto; color: #444444; font-family: Helvetica, Arial; font-size: 15px; line-height: 25px; text-align: justify;"> 
              <b> <font color="#709e4b">'.$data['cabecera'].'</font></b><br>';
			 
			  if (!empty($data['comment'])) { 
			
			//[INICIO] JMZ18G quitar is_deleted para que no aparezca en la papelera los correos enviados.
			//  $clase_comentario='style=" padding-bottom: 10px; "';  
			//  $cabecera=$cabecera.'"<span style="font-size:9.0pt; font-style: oblique; padding-top: 10px; padding-bottom: 10px; line-height: 25px; padding-right: 15px; padding-left: 15px;">'.$data['comment'].'</span>"<br>'; 
			//[FIN]
			
			  $clase_comentario='style="padding-top: 10px; padding-bottom: 10px; "'; 
			
			  } else { 
			  
			  $clase_comentario='style="padding-top: 10px; padding-bottom: 10px; "'; 
			  
			  }
			   
              $cabecera=$cabecera.'<!--<span '.$clase_comentario.'><b>Chequeos encontrados: <font color="#5aa420">'.$indice.'</font></b></span>
			  <br>
            
              <br>-->
              <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; border-bottom: 6px solid #ebeff5;">
               <tbody>
                <tr>
                 <td style="padding: 0px; border-bottom: 2px solid #CECEF6;" colspan="2"></td>
                </tr>
               </tbody>
              </table>             
               <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;">
                <tbody>';

	$pie='<tr>
                  <td style="padding: 0px; border-bottom: 2px solid #CECEF6;" colspan="2"></td>
                 </tr>
                </tbody>
               </table>
              <br>
              <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;">
			  <tbody>
                <tr>
                 <td valign="top" style="padding: 0px;"> <div class="" style="cursor: auto; color: #444444; font-family: Helvetica, Arial; font-size: 15px; line-height: 25px; text-align: justify;">   <b> <font color="#709e4b">'.$data['pie'].'</font></b></div></td>
			     </tr>
                </tbody>
			  </table>
              <table cellpadding="0" cellspacing="0" style="border-collapse: collapse; margin-top: 5px; border-collapse: separate;" align="center" border="0"> 
               <tbody> 
                <tr> 
                 <td style="padding: 0px; border: none; color: #FFFFFF; cursor: auto;" align="center" valign="middle">
				  
				 <!--<a href="#" style="text-decoration: none; line-height: 100%; border-radius: 3px; background: #66ac51; color: #FFFFFF; font-size: 15px; font-weight: normal; text-transform: none; margin: 0px; padding: 10px 25px;">DETALLES DEL TICKET</a></td> -->
                </tr> 
               </tbody> 
              </table>
              <br>
             
            <!--    <table style="width: 100%; border: 2px solid #EBEFF5; font-size: 15px;">
                <tbody>
                 <tr>
                  <td align="center" valign="top" style="padding: 10px 20px;">Su Ticket ha sido registrado en 
              <a href="https://www.carm.es" style="color: #347bb7; text-decoration: underline;">CARM</a> .<br>
			<br> Fecha ejecución del SCRIPT:  <b> <font color="#709e4b">'.$fecha_completa.'</font></b></td>
                 </tr>
                </tbody>
               </table><br>-->
             </div><a name="conf_alerta"> </a> </td> 
           </tr> 
          </tbody> 
         </table> 
        </div>  </td> 
      </tr> 
     </tbody> 
    </table> 
   </div> 
   
   
   <div style="margin: 0px auto; max-width: 600px; background: #347bb7;"> 
    <table cellpadding="0" cellspacing="0" style="border-collapse: collapse; font-size: 0px; width: 100%; background: #347bb7;" align="center" border="0"> 
     <tbody> 
      <tr> 
       <td style="text-align: center; vertical-align: top; direction: ltr; font-size: 0px; padding: 20px 0px; padding-bottom: 0px; padding-top: 0px;">  
        <div class="mj-column-per-100 outlook-group-fix" style="vertical-align: top; display: inline-block; direction: ltr; font-size: 13px; text-align: left; width: 100%;"> 
         <table cellpadding="0" cellspacing="0" style="border-collapse: collapse; vertical-align: top;" width="100%" border="0"> 
          <tbody> 
           <tr> 
            <td style="word-wrap: break-word; font-size: 0px; padding: 10px 25px; padding-top: 10px; padding-bottom: 10px; padding-right: 25px; padding-left: 25px;" align="center"> 
             <div class="" style="cursor: auto; color: #ffffff; font-family: Verdana, Helvetica, Arial; font-size: 14px; line-height: 25px; text-align: center;">
              
			  <span style="font-size:9.0pt; font-style: oblique;">
			<!-- Este correo es informativo, por favor no responda a esta dirección de<br /> correo, ya que no se encuentra habilitada para recibir mensajes. -->
              </span>
              <br>
             </div> </td> 
           </tr> 
          </tbody> 
         </table> 
        </div>  </td> 
      </tr> 
     </tbody> 
    </table> 
   </div> 
</td>
</tr>
</tbody>
</table>

</div>
<div style="text-align: center;">&nbsp;</div>
<p>&nbsp;</p>
<div style="text-align: center;"></div><!--<br><br>-- 
<br>********  CARM  *********<br>
Departamento de Soporte a Clientes<br>Generado automáticamente por TILENA 0.90.3<br><br>-->
</div></div>
</body></html>'; 




     // $body_html=$data['cabecera']."<br>".$tabla."<br>".$data['pie'];
         //$correo['body_html']=$data['cabecera']."<br>".$tabla."<br><br><br> Fecha ejecución del SCRIPT: ".$fecha_completa."<br><br><br><br>".$data['pie'];
         //  $correo['body_html']=$cabecera.'<p class="MsoNormal"> <font color="blue"><strong>'.$data['cabecera'].'</strong></font>.</p><br><p class="MsoNormal">'.$tabla.'</p><br><br><p class="MsoNormal"> Fecha ejecución del SCRIPT: ".$fecha_completa."</p><br><br><br>'.$data['pie'].$pie;
		 //  $correo['body_html']=$plantilla;
			 $correo['body_html']=$cabecera.$tabla.$pie;		  
		     $correo['recipient']=$datos['email_elemento'];
		
             $correo['asunto']=$data['asunto'];
		     if (isset($datos['respuesta_elemento'])){
			 $correo['replyto']=$datos['respuesta_elemento'];
			 }
		    
			//(número de filas que se recogen en el detalle del correo)
			$correo["registros"]=$indice;
			$correo["entities_id"]=$data["entities_id"];
			$correo["items_id"] = $data['id'];
			
			
			if (!empty($data["remitente_email"])) {
				
				 $correo['sender']= $data["remitente_email"];
				 $correo['sendername']= $data["remitente_name"];
				
			} else {
			
			
			  $correo['sender']= $data["email"];
			  
			  if ((isset($data["firstname"])) and (!empty($data["firstname"]))) {
			  $nombre=explode($data["firstname"],$data["realname"]);
			
			  if (count($nombre)>1){
			
			$remitente=$data["realname"];
				
			  } else {

			$remitente=$data["firstname"]." ".$data["realname"];
			 
			  }			  
			  
			  } else {
			   
			  $remitente=$data["usuario"]; 
			  
			  } 			
			   
		 //  echo $remitente."<br>"; 
			  
			  $correo['sendername']= $remitente;	

			   }
			
		
			  
			  $correo['id_chequeo'] =  $data['id'];
					
           $correo=self::sendCheck($correo);   
		   
			   
           
      }


      $tr = new self();
      if ($tr->getFromDB($data['id'])) {
         $input                       = array();
         $input['id']                 = $data['id'];
         $input['next_creation_date'] = $tr->chequeoNextCreationDate($data['begin_date'],
                                                                     $data['end_date'],
                                                                     $data['periodicity'],
                                                                     $data['create_before'],
                                                                     $data['calendars_id']);
         $tr->update($input);
         return 1;
      }
       
   }
   
   
   
   
   static function sendCheck($options){
     global $CFG_GLPI;
			
      $data = array();
      $data['itemtype']                             = 'PluginChecksCheck';
      $data['items_id']                             = $options["items_id"];
      $data['notificationtemplates_id']             = 0;
      $data['entities_id']                          = $options["entities_id"];
      $data["headers"]['Auto-Submitted']            = "auto-generated";
      $data["headers"]['X-Auto-Response-Suppress']  = "OOF, DR, NDR, RN, NRN";

      $data['name'] = $options['asunto'];        
      $data['body_text'] = $options['body_html']; 
      $data['body_html'] = $options['body_html'];
  

      $data['recipient']                            = $options['recipient'];
      $data['recipientname']                        = $options['recipient'];
	  
	  if (!empty($options['sender'])){
	  $data['sender']                               = $options["sender"];	 
      $data['sendername']                           = $options["sendername"]; 	  
	  } else {	  
	  $data['sender']                               = $CFG_GLPI["admin_email"];
      $data['sendername']                           = $CFG_GLPI["admin_email_name"]; 
	  }
	  
  if (isset($options['replyto'])) {
         $data['replyto']       = $options['replyto'];
      }
	  
	  if (isset($options['replytoname'])) {
		   $data['replytoname']   = $options['replytoname'];
	  } else {
		   if (isset($options['replyto'])) {
		   $data['replytoname']   = $options['replyto'];  
		   }
	  }
	  
	  $data['messageid'] = "GLPI-".$options["id_chequeo"].".".time().".".rand(). "@".php_uname('n');

 //Escribe en el log el cuerpo del correo que enviamos
 //  Toolbox::logInFile("consultas_checks",  "\r\n\r\ correo \r\n\r\n" . $options['body_html'] . "\r\n\r\n  \r\n\r\n");

	 $data['mode'] = 'mailing'; //[CRI] JMZ18G EL PARÁMETRO MODE ES NECESARIO CON EL VALOR mailing PARA QUE EL CORREO SE ENVÍE
	 
	 $mailqueue = new QueuedNotification();

      if (!$id_cola=$mailqueue->add(Toolbox::addslashes_deep($data))) {
         $senderror = true;
         Session::addMessageAfterRedirect(__('Error inserting email to queue'), true);
      } else {
         //TRANS to be written in logs %1$s is the to email / %2$s is the subject of the mail
         Toolbox::logInFile("mail_CHEQUEOS",
                            sprintf(__('%1$s: %2$s'),
                                    sprintf(__('An email to %s was added to queue'),
                                            $options['recipient']),
                                    "\n\r ".$options['asunto']."\n\r (Detalle del correo: ".$options['registros']." FILAS) \n\r"));								
      }
	 
       
   }
   
   static function createTicket($data) {

      $result = false;
      $tt     = new TicketTemplate();

      // Create ticket based on ticket template and entity information of ticketrecurrent
      if ($tt->getFromDB($data['notificationtemplates_id'])) {
         // Get default values for ticket
         $input = Ticket::getDefaultValues($data['entities_id']);
         // Apply tickettemplates predefined values
         $ttp        = new TicketTemplatePredefinedField();
         $predefined = $ttp->getPredefinedFields($data['notificationtemplates_id'], true);

         if (count($predefined)) {
            foreach ($predefined as $predeffield => $predefvalue) {
               $input[$predeffield] = $predefvalue;
            }
         }
         // Set date to creation date
         $createtime    = strtotime($data['next_creation_date'])+$data['create_before'];
         $input['date'] = date('Y-m-d H:i:s', $createtime);
         if (isset($predefined['date'])) {
            $input['date'] = Html::computeGenericDateTimeSearch($predefined['date'], false,
                                                                $createtime);
         }
         // Compute due_date if predefined based on create date
         if (isset($predefined['due_date'])) {
            $input['due_date'] = Html::computeGenericDateTimeSearch($predefined['due_date'], false,
                                                                    $createtime);
         }
         // Set entity
         $input['entities_id'] = $data['entities_id'];
         $input['_auto_import'] = true;

         $ticket = new Ticket();
         $input  = Toolbox::addslashes_deep($input);
         if ($tid = $ticket->add($input)) {
            $msg = sprintf(__('Ticket %d successfully created'), $tid);
            $result = true;
         } else {
            $msg = __('Ticket creation failed (check mandatory fields)');
         }
      } else {
         $msg = __('Ticket creation failed (no template)');
      }
      $changes[0] = 0;
      $changes[1] = '';
      $changes[2] = addslashes($msg);
      Log::history($data['id'], __CLASS__, $changes, '', Log::HISTORY_LOG_SIMPLE_MESSAGE);

      
      $tr = new self();
      if ($tr->getFromDB($data['id'])) {
         $input                       = array();
         $input['id']                 = $data['id'];
         $input['next_creation_date'] = $tr->computeNextCreationDate($data['begin_date'],
                                                                     $data['end_date'],
                                                                     $data['periodicity'],
                                                                     $data['create_before'],
                                                                     $data['calendars_id']);
         $tr->update($input);
      }

      return $result;
   }
static function getMenuContent() {
      global $CFG_GLPI;

      $menu                                           = array();
      $menu['title']                                  = self::getMenuName();
      $menu['page']                                   = "/plugins/checks/front/check.php";
	  
	  if (Session::haveRight('plugin_checks', CREATE)){
			$menu['links']['add']      = '/plugins/checks/front/check.form.php';
			$menu['links']['search']     = '/plugins/checks/front/check.php';		  
	  }
	
      return $menu;
   }

}