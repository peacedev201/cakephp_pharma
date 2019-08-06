(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
    	define(['jquery', 'mooFileUploader', 'mooBehavior', 'mooAlert', 'mooPhrase', 'mooAjax', 'mooGlobal', 'tinyMCE'], factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS-like
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals (root is window)
        root.mooDocument = factory();
    }
}(this, function ($, mooFileUploader, mooBehavior, mooAlert, mooPhrase, mooAjax, mooGlobal, tinyMCE) {
	var initCreateDocument = function(url,params)
	{		
		if (url == '')
		{
		    var uploader = new mooFileUploader.fineUploader({
		        element: $('#document_file_upload')[0],
				multiple: false,
		        text: {
		        	uploadButton: '<div class="upload-section"><i class="material-icons">photo_camera</i>'+mooPhrase.__('drag_file')+'</div>'
		        },
		        validation: {
		            allowedExtensions:  params.split(','),	
		            sizeLimit: mooConfig.sizeLimit
		        },
		        request: {
		            endpoint: mooConfig.url.base + "/document/documents/upload"
		        },
		        callbacks: {
		        	onError: mooGlobal.errorHandler,
		            onComplete: function(id, fileName, response) {
						if (jQuery.isEmptyObject(response))
						{
							return;
						}
						file = jQuery(this.getItemByFileId(id));					
						element_delete = $('<a href="javascript:void(0);">'+mooPhrase.__('delete')+'<a/>');
						file.find('.qq-upload-status-text').append(element_delete);
						element_delete.click(function(){												
							mooAjax.post({
								url : mooConfig.url.base + '/documents/delete_file/'+$('#document_file').val()
							}, function(data){
								
							});
							$('#document_file').val('');
							$('#original_filename').val('');
							$('#document_file_upload .qq-upload-button').show();
							file.remove();
						});
		                $('#document_file').val(response.document_file);
		                $('#original_filename').val(response.original_filename);
						$('#document_file_upload .qq-upload-button').hide();
		            }
		        }
		    });
		}
		
		$('#document_button').click(function(){
	    	button = $(this);
	        button.addClass('disabled');
	        if (tinyMCE.activeEditor !== null) {
	            $('#description').val(tinyMCE.activeEditor.getContent());
	        }
	        mooAjax.post({
		        url : mooConfig.url.base + '/documents/save',
		        data: jQuery("#createForm").serialize()
		    }, function(data){
		        var json = $.parseJSON(data);
		
		        if ( json.result == 1 )
		        {
		        	if (!mooConfig.isApp)
		        	{
			        	if (url != '')
			        		window.location = url;
			        	else
			        		window.location = mooConfig.url.base + '/documents';
		        	}
		        	else
	        		{
		        		window.location = json.href + '?app_no_tab=1';
	        		}
		        }
		        else
		        {
		            button.removeClass('disabled');
		            $(".error-message").show();
		            $(".error-message").html(json.message);
		        }
		    });
	        return false;
	    });
		
	    if (mooPhrase.__('drag_photo') != '')
	    	text_upload_button = '<div class="upload-section"><i class="material-icons">photo_camera</i>'+ mooPhrase.__('drag_photo') +'</div>';
	    else
	    	text_upload_button = '<div class="upload-section"></div>';
	    
	    var uploader1 = new mooFileUploader.fineUploader({
	        element: $('#document_thumnail')[0],
	        multiple: false,
	        text: {
	            uploadButton: text_upload_button
	        },
	        validation: {
	            allowedExtensions: ['jpg', 'jpeg', 'gif', 'png'],
	            sizeLimit: mooConfig.sizeLimit
	        },
	        request: {
	            endpoint: mooConfig.url.base + "/documents/upload_avatar"
	        },
	        callbacks: {
	        	onError: mooGlobal.errorHandler,
	            onComplete: function(id, fileName, response) {
	                $('#document_thumnail_preview > img').attr('src', response.thumb);
	                $('#document_thumnail_preview > img').show();
	                $('#thumbnail').val(response.file);
	            }
	        }
	    });
		
		initDeleteDocument();
	}
	
	var initDeleteDocument = function()
	{
		$('.deleteDocument').unbind('click');
		$('.deleteDocument').click(function()
		{
			 var data = $(this).data();
	         if (!mooConfig.isApp)
		     {
				 var deleteUrl = mooConfig.url.base + '/documents/delete/' + data.id;
		     }
			 else
			 {
				 var deleteUrl = mooConfig.url.base + '/documents/delete/' + data.id + '?app_no_tab=1';
			 }
	         mooAlert.confirm(mooPhrase.__('delete_document_confirm'), deleteUrl);
		});
	}
	
	var initViewDocument = function()
	{
		$( window ).ready(function() {						
			var height = window.innerHeight - 50;
			if (window.innerHeight > 650)
				height = 650;
			if (window.innerWidth < 768)
				height = 300;
									
			$('#iframe_preview').attr('height',height);
		});		
		
		initDeleteDocument();
	}
	
	var initOnListing = function()
	{
		mooBehavior.initMoreResults();
		
		initDeleteDocument();
	}
	return{
		initCreateDocument: function(url,params){
			initCreateDocument(url,params);
        },
        initViewDocument : function()
        {
        	initViewDocument();
        },
        initOnListing : function()
        {
        	initOnListing();
        }
    }
}));