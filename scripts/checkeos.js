
jQuery(document).ready(function($) {
			
	//---------------------------- IMPUTS ------------------------------------	

		$('input').focus(function(){
					
					var $me = $(this);
		
					var id = $me.attr("id");
				        
					var nombre = $me.attr("name");
					
				if ((nombre=='url_elemento') || (nombre=='empresa')){
			$me.css({'background-color' : 'rgba(232,232,179,0.5)'});			
				}else{
			$me.css({'background-color' : 'rgba(232,232,179,0.5)'});	
				}
			});

			$('input').blur(function(){
				var $me = $(this);
			$me.css({'background-color' : 'rgba(224, 255, 255, 0.8)'});		
			});
			
		//---------------------------- TEXTAREA ------------------------------------	
			
			$('textarea').focus(function(){
				
					var $me = $(this);
		
					var id = $me.attr("id");
				        
					var nombre = $me.attr("name");
					
				if ((nombre=='url_elemento') || (nombre=='empresa')){
			    
				$me.css({'background-color' : 'rgba(0,0,255,0.3)'});	
			
				}else{

				$me.css({'background-color' : 'rgba(232,232,179,0.5)'});	
				
				}
			});

			$('textarea').blur(function(){
				var $me = $(this);
			$me.css({'background-color' : 'rgba(201, 241, 205, 0.5)'});		
			});			

		//---------------------------- OTROS ------------------------------------
});    
