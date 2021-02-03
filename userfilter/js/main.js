(function ($) {
    $(document).ready(function(){
        
        jQuery('.user_filter_container select').on('change', function(e){
            //var optionSelected = jQuery("option:selected", this);
            var valueSelected = this.value;
    		var json = '[{"role": "'+valueSelected+'"}]';
    		jQuery.ajax({
    			type: 'POST',
    			dataType: 'json',
    			url: user_filter_object.ajaxurl,
    			data: { 
    				'action': 'ajaxfilterrole', //calls wp_ajax_nopriv_ajaxlogin				
    				'json': json,
    				'nonce' : user_filter_object.nonce
    			},
    			success: function(data){
    				jQuery('.user_table_container table tbody').html('');
    				var result = JSON.parse(data.json);
                    for (var i = 0; i < result.length; i++) {
                        jQuery('.user_table_container table tbody').append('<tr><td>'+result[i].login+'</td><td>'+result[i].email+'</td><td>'+result[i].role+'</td></tr>');
                    }
    			}
    		});
    		e.preventDefault();	
    	});
    	
        jQuery('.user_table_container table .fa-arrow-up').on('click', function(e){
            var orderby = jQuery(this).parent().attr('data-field-type');
            var role = jQuery('.user_filter_container select option:selected').val();
    		var json = '[{"role": "'+role+'", "orderby": "'+orderby+'", "order": "ASC"}]';
    		console.log(json);
    		jQuery.ajax({
    			type: 'POST',
    			dataType: 'json',
    			url: user_filter_object.ajaxurl,
    			data: { 
    				'action': 'ajaxorderby', //calls wp_ajax_nopriv_ajaxlogin				
    				'json': json,
    				'nonce' : user_filter_object.nonce
    			},
    			success: function(data){
    				console.log(data.json);	
    				jQuery('.user_table_container table tbody').html('');
    				var result = JSON.parse(data.json);
    				console.log(result.length);
                    for (var i = 0; i < result.length; i++) {
                        jQuery('.user_table_container table tbody').append('<tr><td>'+result[i].login+'</td><td>'+result[i].email+'</td><td>'+result[i].role+'</td></tr>');
                    }
    			}
    		});
    		e.preventDefault();	
    	});
    	
        jQuery('.user_table_container table .fa-arrow-down').on('click', function(e){
            var orderby = jQuery(this).parent().attr('data-field-type');
            var role = jQuery('.user_filter_container select option:selected').val();
    		var json = '[{"role": "'+role+'", "orderby": "'+orderby+'", "order": "DESC"}]';
    		console.log(json);
    		jQuery.ajax({
    			type: 'POST',
    			dataType: 'json',
    			url: user_filter_object.ajaxurl,
    			data: { 
    				'action': 'ajaxorderby', //calls wp_ajax_nopriv_ajaxlogin				
    				'json': json,
    				'nonce' : user_filter_object.nonce
    			},
    			success: function(data){
    				console.log(data.json);	
    				jQuery('.user_table_container table tbody').html('');
    				var result = JSON.parse(data.json);
    				console.log(result.length);
                    for (var i = 0; i < result.length; i++) {
                        jQuery('.user_table_container table tbody').append('<tr><td>'+result[i].login+'</td><td>'+result[i].email+'</td><td>'+result[i].role+'</td></tr>');
                    }
    			}
    		});
    		e.preventDefault();	
    	});
    });
})(jQuery);