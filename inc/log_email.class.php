<?php
/*
   ----------------------------------------------------------
   Plugin Checks 1.0.0
   GLPI 9.1.X
  
   Autor: Elena Martínez Ballesta.
   Fecha: Septiembre 2017
   ----------------------------------------------------------
 */
 
if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access this file directly");
}

class PluginChecksLog_Email extends CommonDBTM {
	
   /**
    * We activate the history.
    *
    * @var boolean
    */	
	
	public $dohistory = TRUE;

   /**
    * The right name for this class
    *
    * @var string
    */
   static $rightname = 'plugin_checks';


   static function getTypeName($nb=0) {
      return __('Notification queue');
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
	'id' => '100',
	'table' => $this->getTable(),
	'field' => 'recipient',
	'name' => __('Recipient email'),
	'datatype' => 'itemlink',
	];

	$tab[] = [
	'id' => '102',
	'table' => $this->getTable(),
	'field' => 'create_time',
	'name' => __('Creation date'),
	'datatype' => 'datetime',
	];
	
	$tab[] = [
	'id' => '103',
	'table' => $this->getTable(),
	'field' => 'sent_time',
	'name' => __('Send date'),
	'datatype' => 'datetime',
	];	
	 
	$tab[] = [
	'id' => '104',
	'table' => $this->getTable(),
	'field' => 'sender',
	'name' => __('Sender email'),
	'datatype' => 'itemlink',
	];
	
/*      $tab[18]['table']    = $this->getTable();
      $tab[18]['field']    = 'name_complete';
      $tab[18]['name']     = __('Chequeo');
      $tab[18]['datatype'] = 'itemlink';	  	
	  
      $tab[19]['table']    = $this->getTable();
      $tab[19]['field']    = 'items_id';
      $tab[19]['name']     = __('ID CHEQUEO');
      $tab[19]['datatype'] = 'itemlink';	*/	
	
	return $tab;

	}   
   

static function add_log($input) {
	
	if ((isset($input["itemtype"])) and ($input["itemtype"]=="PluginChecksCheck")) {
		
	//echo var_dump(self::prepareForAdd($input));
		
		//   $emilio = new PluginChecksLog_Email();
		 
    
          //  $emilio->add(self::prepareForAdd($input));
			
		//	exit();
	} 
	
   }

static function update_log($input) {
	
	
		if ((isset($input["itemtype"])) and ($input["itemtype"]=="PluginChecksCheck")) {
	
//	echo var_dump(self::prepareForAdd($input));
	
	     $emilio = new PluginChecksLog_Email();
    
           // $emilio->update(self::prepareForAdd($input));
			
	//		exit();
         }
		 
}


   /**
    * Print the queued mail form
    *
    * @param $ID        integer ID of the item
    * @param $options   array
    *
    * @return true if displayed  false if item not found or not right to display
   **/
   function showForm($ID, $options=array()) {
      global $CFG_GLPI;

      if (!Session::haveRight("queuednotification", READ)) {
        return false;
      }

     $this->check($ID, READ);
      $options['canedit'] = false;

      $this->showFormHeader($options);
      echo "<tr class='tab_bg_1'>";
      echo "<td>".__('Type')."</td>";

      echo "<td>";
      if (!($item = getItemForItemtype($this->fields['itemtype']))) {
         echo NOT_AVAILABLE;
         echo "</td>";
         echo "<td>"._n('Item', 'Items', 1)."</td>";
         echo "<td>";
         echo NOT_AVAILABLE;
      } else if ($item instanceof CommonDBTM) {
         echo $item->getType();
         $item->getFromDB($this->fields['items_id']);
         echo "</td>";
         echo "<td>"._n('Item', 'Items', 1)."</td>";
         echo "<td>";
         echo $item->getLink();
      } else {
         echo get_class($item);
         echo "</td><td></td>";
      }
      echo "</tr>";

      echo "<tr class='tab_bg_1'>";
      echo "<td>"._n('Notification template', 'Notification templates', 1)."</td>";
      echo "<td>";
      echo Dropdown::getDropdownName('glpi_notificationtemplates',
                                     $this->fields['notificationtemplates_id']);
      echo "</td>";
      echo "<td>&nbsp;</td>";
      echo "<td>&nbsp;</td>";
      echo "</tr>";

      echo "<tr class='tab_bg_1'>";
      echo "<td>".__('Creation date')."</td>";
      echo "<td>";
      echo Html::convDateTime($this->fields['create_time']);
      echo "</td><td>".__('Expected send date')."</td>";
      echo "<td>".Html::convDateTime($this->fields['send_time'])."</td>";
      echo "</tr>";

      echo "<tr class='tab_bg_1'>";
      echo "<td>".__('Send date')."</td>";
      echo "<td>".Html::convDateTime($this->fields['sent_time'])."</td>";
      echo "<td>".__('Number of tries of sent')."</td>";
      echo "<td>".$this->fields['sent_try']."</td>";
      echo "</tr>";

      echo "<tr><th colspan='4'>".__('Email')."</th></tr>";
      echo "<tr class='tab_bg_1'>";
      echo "<td>".__('Sender email')."</td>";
      echo "<td>".$this->fields['sender']."</td>";
      echo "<td>".__('Sender name')."</td>";
      echo "<td>".$this->fields['sendername']."</td>";
      echo "</tr>";

      echo "<tr class='tab_bg_1'>";
      echo "<td>".__('Recipient email')."</td>";
      echo "<td>".$this->fields['recipient']."</td>";
      echo "<td>".__('Recipient name')."</td>";
      echo "<td>".$this->fields['recipientname']."</td>";
      echo "</tr>";

      echo "<tr class='tab_bg_1'>";
      echo "<td>".__('Reply-to email')."</td>";
      echo "<td>".$this->fields['replyto']."</td>";
      echo "<td>".__('Reply-to name')."</td>";
      echo "<td>".$this->fields['replytoname']."</td>";
      echo "</tr>";

      echo "<tr class='tab_bg_1'>";
      echo "<td>".__('Message ID')."</td>";
      echo "<td>".$this->fields['messageid']."</td>";
      echo "<td>".__('Additional headers')."</td>";
      echo "<td>".QueuedNotification::getSpecificValueToDisplay('headers', $this->fields)."</td>";
      echo "</tr>";

      echo "<tr class='tab_bg_1'>";
      echo "<td>".__('Subject')."</td>";
      echo "<td colspan=3>".$this->fields['name_complete']."</td>";
      echo "</tr>";

      echo "<tr><th colspan='2'>".__('Email HTML body')."</th>";
      echo "<th colspan='2'>".__('Email text body')."</th>";
      echo "</tr>";

      echo "<tr class='tab_bg_1 top' >";
      echo "<td colspan='6' class='queuemail_preview'>".$this->fields['body_html']."</td>";
      echo "</tr>";

   /*   echo "<tr class='tab_bg_1 top' >";
      echo "<td colspan='2' class='queuemail_preview'>".self::cleanHtml($this->fields['body_html'])."</td>";
      echo "<td colspan='2'>".nl2br($this->fields['body_text'], false)."</td>";
      echo "</tr>";*/

      $this->showFormButtons($options);	        

      return true;

   }



static function showForCheck() {

Search::show('PluginChecksLog_Email');

}


static function reemplazar($cadena, $busco, $reemplazo){
	
	$buscar=explode("*",$busco);

	$reemplazar=explode("*",$reemplazo);

	$formulario=str_ireplace($buscar,$reemplazar,$cadena);
	
	return $formulario;  

 }

		 
static function prepareForAdd($input) {
      global $DB;

	   if (isset($input['body_html'])) {
	  
	 $input['body_text'] = self::reemplazar($input['body_text'], "'", "\'");
	 $input['body_text'] ="";
	  
	   }
	   
	  
      if (!isset($input['create_time']) || empty($input['create_time'])) {
         $input['create_time'] = $_SESSION["glpi_currenttime"];
      }
      if (!isset($input['send_time']) || empty($input['send_time'])) {
         $toadd = 0;
         if (isset($input['entities_id'])) {
            $toadd = Entity::getUsedConfig('delay_send_emails', $input['entities_id']);
         }
         if ($toadd > 0) {
            $input['send_time'] = date("Y-m-d H:i:s",
                                       strtotime($_SESSION["glpi_currenttime"])
                                                      +$toadd*MINUTE_TIMESTAMP);
         } else {
            $input['send_time'] = $_SESSION["glpi_currenttime"];
         }
      }
      $input['sent_try'] = 0;
      if (isset($input['headers']) && is_array($input['headers']) && count($input['headers'])) {
         $input["headers"] = exportArrayToDB($input['headers']);
      } else {
        $input['headers'] = '';
      }

      if (isset($input['documents']) && is_array($input['documents']) && count($input['documents'])) {
         $input["documents"] = exportArrayToDB($input['documents']);
      } else {
        $input['documents'] = '';
      }

      // Force items_id to integer
      if (!isset($input['items_id']) || empty($input['items_id'])) {
         $input['items_id'] = 0;
      }

      // Drop existing mails in queue for the same event and item  and recipient
      if (isset($input['itemtype']) && !empty($input['itemtype'])
          && isset($input['entities_id']) && ($input['entities_id'] >= 0)
          && isset($input['items_id']) && ($input['items_id'] >= 0)
          && isset($input['notificationtemplates_id']) && !empty($input['notificationtemplates_id'])
          && isset($input['recipient'])) {
         $query = "NOT `is_deleted`
                   AND `itemtype` = '".$input['itemtype']."'
                   AND `items_id` = '".$input['items_id']."'
                   AND `entities_id` = '".$input['entities_id']."'
                   AND `notificationtemplates_id` = '".$input['notificationtemplates_id']."'
                   AND `recipient` = '".$input['recipient']."'";
         foreach ($DB->request($this->getTable(),$query) as $data) {
            $this->delete(array('id' => $data['id']),1);
         }
      }

      return $input;
   }
   
  /**
    * @since version 0.85
    *
    * @param $string
    **/
   static function cleanHtml($string) {

      $begin_strip     = -1;
      $end_strip       = -1;
      $begin_match     = "/<body>/";
      $end_match       = "/<\/body>/";
      $content         = explode("\n", $string);
      $newstring       = '';
      foreach ($content as $ID => $val) {
         // Get last tag for end
         if ($begin_strip >= 0) {
            if (preg_match($end_match,$val)) {
               $end_strip = $ID;
               continue;
            }
         }
         if (($begin_strip >= 0) && ($end_strip < 0)) {
            $newstring .= $val;
         }
         // Get first tag for begin
         if ($begin_strip < 0) {
            if (preg_match($begin_match,$val)) {
               $begin_strip = $ID;
            }
         }
      }
      return nl2br($newstring,false);
      return preg_replace($patterns, $replacements, $string);
   }
   
   }   

