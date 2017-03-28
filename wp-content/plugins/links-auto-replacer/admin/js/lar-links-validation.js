(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-specific JavaScript source
	 * should reside in this file.
	 *
	 * Note that this assume you're going to use jQuery, so it prepares
	 * the $ function reference to be used within the scope of this
	 * function.
	 *
	 * From here, you're able to define handlers for when the DOM is
	 * ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * Or when the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and so on.
	 *
	 * Remember that ideally, we should not attach any more than a single DOM-ready or window-load handler
	 * for any particular page. Though other scripts in WordPress core, other plugins, and other themes may
	 * be doing this, we should try to minimize doing that in our own work.
	 */


		jQuery(document).ready(function(){
			 var validated = false;
			
				 jQuery('#post').submit(function() {
				 				if(validated== true) return true;
			                    var form_data = jQuery('#post').serializeArray();
			                    form_data = jQuery.param(form_data);
			                    var data = {
			                        action: 'my_pre_submit_validation',
			                        security: validation_nonce,
			                        form_data: form_data
			                    };
			                    jQuery.post(ajaxurl, data, function(response) {
			                        if (response == 1) {

			                        	validated = true;
			                        	jQuery('#publish').trigger('click');
			                            jQuery('#post-body-content').html('');

			                            jQuery('#ajax-loading').show();
	                            		jQuery('#publish').removeClass('button-primary-disabled');
			                           
			                            return true;
			                        }else{
			                        	validated = false;
			                        	jQuery('#message').hide();
			                            jQuery('#post-body-content').html('<div id="message" class="error"><p>'+response+'</p></div>');
			                            jQuery('#ajax-loading').hide();
	                            		jQuery('#publish').removeClass('button-primary-disabled');
			                            return false;
			                        }
			                    });
			                    return false;
			        });
			


					jQuery('.form-table input[type=text]').on('keydown',function(e){
						
						if(e.which === 13){
							jQuery('#publish').trigger('click');
							return false;
						}
						
					});
					
					

						if(jQuery('#'+plugin_prefix+'shrink').is(':checked')){

					        jQuery('#'+plugin_prefix+'slug').removeAttr('disabled');
					        jQuery("#lar_slug_example").html(home_url + '/go/' + jQuery('#'+plugin_prefix+'slug').val());

				        

						}else{
					        jQuery('#'+plugin_prefix+'slug').attr('disabled','disabled');
					        jQuery("#lar_slug_example").html('');

						}

					

					jQuery('#'+plugin_prefix+'shrink').click(function(){
        				
				        var attr = jQuery('#'+plugin_prefix+'slug').attr('disabled');

				        if (typeof attr !== typeof undefined && attr !== false) {
				              jQuery('#'+plugin_prefix+'slug').removeAttr('disabled');
				              //jQuery('#'+plugin_prefix+'slug').val(last_link_id); 
				              jQuery("#"+plugin_prefix+'slug').trigger('change');
				              
				        }else{
				              
				              jQuery('#'+plugin_prefix+'slug').attr('disabled','disabled');
				              //jQuery('#'+plugin_prefix+'slug').val('');
				              jQuery("#lar_slug_example").html('');
				        }
				        
				    });
					
				      jQuery('#'+plugin_prefix+'slug').bind('change paste keyup', function(){
				          if(jQuery(this).val()!=''){
				            jQuery("#lar_slug_example").html(home_url + '/go/' + jQuery(this).val());
				          	last_link_id = jQuery(this).val();
				          }else{
				            jQuery("#lar_slug_example").html('');
				          }
				    });


				  
				     
				      

		        });
			 
		

})( jQuery );


