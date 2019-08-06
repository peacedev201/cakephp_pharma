jQuery.admin = {
	leaveNote: function(id){
		 jQuery('#usernotesModal').modal();
		jQuery.ajax({
			data:{'target_id':id},
			method:'GET',
			url:mooConfig.url.base+'/usernotes/usernotess/ajax_leave_note',
			success: function(data){
			// success
			jQuery('#usernotesModal .modal-content').empty().append(data);

			}
			
		});
	},
	deleteNote: function(id){
		if(id == 0 || id == ''){
			return;
		}
		jQuery.ajax({
			dataType:'JSON',
			data:{'note_id':id},
			method:'GET',
			url:mooConfig.url.base+'/usernotes/usernotess/delete_note',
			success: function(data){
				data = jQuery.praseJSON(data);
				if(data.result == 1){
					// success
				}else{
					// fail
				}
			}
		});
	},
        
        saveAdminNote: function(is_admin){
            var content = jQuery('#usernotesContent').val();
            var target_id = jQuery('#target_id').val();
            var note_id = jQuery('#note_id').val();
            jQuery.ajax({
                dataType: 'JSON',
                data: {'content':content,'target_id':target_id,'id':note_id},
                method: 'POST',
                url: mooConfig.url.base+'/admin/usernotes/usernotess/save_note',
                success: function(data){
                    if(data.result == 0){
                        jQuery('#unote-error-message').show();
                        jQuery('#unote-error-message').html(data.message);
                        return false;
                    }
                    location.reload();
                }
            });
        }
};