<script>
// === VALIDATORS LIST ===
jQuery(document).ready(function($) {
			
	//---------------------------- IMPUTS ------------------------------------	

		$('input').focus(function(){
					
					var $me = $(this);
		
					var id = $me.attr("id");
				        
					var nombre = $me.attr("name");
					
				if ((nombre=='url_elemento') || (nombre=='empresa')){
			$me.css({'background-color' : 'rgba(250, 219, 216, 0.8)'});			
				}else{
			$me.css({'background-color' : 'rgba(250, 219, 216, 0.8)'});	
				}
			});

			$('input').blur(function(){
				var $me = $(this);
			$me.css({'background-color' : '#F2F2F2'});		
			});
			
		//---------------------------- TEXTAREA ------------------------------------	
			
			$('textarea').focus(function(){
				
					var $me = $(this);
		
					var id = $me.attr("id");
				        
					var nombre = $me.attr("name");
					
				if ((nombre=='url_elemento') || (nombre=='empresa')){
			    
				$me.css({'background-color' : 'rgba(250, 219, 216, 0.8)'});	
			
				}else{

				$me.css({'background-color' : 'rgba(250, 219, 216, 0.8)'});	
				
				}
			});

			$('textarea').blur(function(){
				var $me = $(this);
			$me.css({'background-color' : '#F2F2F2'});		
			});			

		//---------------------------- OTROS ------------------------------------
});    
</script>