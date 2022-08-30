<?php
/*
 * 7/05/2013 - Estilos propios del plugin Chequeos
 -------------------------------------------------------------------------
 Javier David Marín Zafrilla
 -------------------------------------------------------------------------

 LICENSE

 This file is part of GLPI.

 GLPI is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 GLPI is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with GLPI. If not, see <http://www.gnu.org/licenses/>.
 --------------------------------------------------------------------------
*/
/* ################--------------- GLPI CSS style   ---------------#################### */


/* Estilo campo url_elemento del fromulario  */
echo '
<style>

 #ui-tabs-1 {
	background-color:#F2F2F2; 
}	


#mainformtable tbody tr th, .tab_bg_2, .responsive_hidden { background-color: background-color:#d2e3f7; }	
	


input[type=text], input[type=password], input[type=number] {
   background-color:#F2F2F2;  
   color: black;
   border: 1px solid #D3D3D3;
   font-size: 13px;
   border-radius: 3px;
   padding_left: 7px;
   width: 98%; 
   height:25px;
}

textarea {    
 background-color:#F2F2F2;  
   color: black;
   border: 1px solid #D3D3D3;
   font-size: 11px;
   border-radius: 3px;
   padding: 7px;
   width: 97.5%; 
	
}	

input[name=_begin_date], input[name=_end_date]  {    
width: 90%; 
}	

</style>';
?>