(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
    	define(['jquery', 'mooFileUploader', 'mooBehavior', 'mooAlert', 'mooPhrase', 'mooAjax', 'mooGlobal', 'tinyMCE', 'mooTooltip'], factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS-like
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals (root is window)
        root.mooQuestion = factory();
    }
}(this, function ($, mooFileUploader, mooBehavior, mooAlert, mooPhrase, mooAjax, mooGlobal, tinyMCE, mooTooltip) {
	var uploader;    
	var initCreateQuestion = function(url)
	{
		$('#tags').tokenize({
		    datas: mooConfig.url.base+'/question/question_suggest/tag'
		});
		
		if ($('#attachments_upload').length > 0)
		{
			extension = $('#attachments_upload').data('extension').split(",");		
			
			uploader = new mooFileUploader.fineUploader({
		        element: $('#attachments_upload')[0],
		        text: {
		            uploadButton: '<div class="upload-section"><i class="material-icons">insert_drive_file</i>'+mooPhrase.__('upload_button_text')+'</div>'
		        },
		        validation: {
		            allowedExtensions: extension,
		            sizeLimit: mooConfig.sizeLimit
		        },
		        request: {
		            endpoint: mooConfig.url.base + '/question/question_upload/attachments/Question'
		        },
		        callbacks: {
		        	onError: mooGlobal.errorHandler,
		            onComplete: function(id, fileName, response) {
		                var attachs = $('#attachments').val();
		              
		                if (response.attachment_id){
		                    tinyMCE.activeEditor.insertContent('<p><a href="'+mooConfig.url.base+'/question/question_attachments/download/' + response.attachment_id + '" class="attached-file">' + response.original_filename + '</a></p><br>');
		                    if ( attachs == '' ){
		                        $('#attachments').val( response.attachment_id );
		                    }
		                    else{
		                        $('#attachments').val(attachs + ',' + response.attachment_id);
		                    }
		                }else if(id || response.thumb){
		                	$('#photo_ids').val($('#photo_ids').val() + ',' + response.photo_id);
		                    tinyMCE.activeEditor.insertContent('<p align="center"><a href="' + response.large + '" class="attached-image"><img src="' + response.thumb + '"></a></p><br>');
		                }
		            }
		        }
		    });
		}
		
		$('.attach_remove').click(function(){
			var obj = $(this);
			obj.parent().fadeOut();
			var arr = $('#attachments').val().split(',');
			var pos = arr.indexOf(obj.attr('data-id'));
			arr.splice(pos, 1);
			$('#attachments').val(arr.join(','));	
			
			return false;
		});
		if (mooPhrase.__('upload_button_text_photo') != '')
	    	text_upload_button = '<div class="upload-section"><i class="material-icons">photo_camera</i>'+ mooPhrase.__('upload_button_text_photo') +'</div>';
	    else
	    	text_upload_button = '<div class="upload-section"></div>';
	    var uploader = new mooFileUploader.fineUploader({
	        element: $('#question_thumnail')[0],
	        multiple: false,
	        text: {
	            uploadButton: text_upload_button
	        },
	        validation: {
	            allowedExtensions: ['jpg', 'jpeg', 'gif', 'png'],
	            sizeLimit: mooConfig.sizeLimit
	        },
	        request: {
	            endpoint: mooConfig.url.base + "/questions/upload_avatar"
	        },
	        callbacks: {
	        	onError: mooGlobal.errorHandler,
	            onComplete: function(id, fileName, response) {
	                $('#question_thumnail_preview > img').attr('src', response.thumb);
	                $('#question_thumnail_preview > img').show();
	                $('#thumbnail').val(response.file);
	            }
	        }
	    });
		
		$('#question_button').click(function(){
	    	button = $(this);
	        button.addClass('disabled');
	        if(tinyMCE.activeEditor !== null){
	            $('#description').val(tinyMCE.activeEditor.getContent());
	        }
	        mooAjax.post({
		        url : mooConfig.url.base + '/questions/save',
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
			        		window.location = mooConfig.url.base + '/questions';
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
		
		initDeleteQuestion();
	}
	
	var initDeleteQuestion = function()
	{
		$('.deleteQuestion').unbind('click');
		$('.deleteQuestion').click(function()
		{
			 var data = $(this).data();
			 if (!mooConfig.isApp)
		     {
				 var deleteUrl = mooConfig.url.base + '/questions/delete/' + data.id;
		     }
			 else
			 {
				 var deleteUrl = mooConfig.url.base + '/questions/delete/' + data.id + '?app_no_tab=1';
			 }
	         mooAlert.confirm(mooPhrase.__('delete_question_confirm'), deleteUrl);
		});
	}
	
	var initBrowseQuestion = function(type)
	{
		$('#category').change(function(e){
			$('#question_form_search').submit();
		});
		
		/*$('#keyword').keyup(function(event) {
            if (event.keyCode == '13') {
            	$('#question_form_search').submit();
            }
		});*/
	}
	
	var initListingQuestion = function()
	{
		initDeleteQuestion();
		
		mooBehavior.initMoreResults();
	}
	
	var vote = function(element,type,id,up)
	{
		element = $(element);
		$.ajax({ 
		    type: 'POST',
		    dataType : 'json',
		    url: mooConfig.url.base + '/questions/ajax_vote', 
		    data: { 
		    		'type': type,
		    		'id': id,
		    		'up' : up
		    	  }, 
		    success: function (data) {
		    	if (data.status)
	    		{		
		    		block = element.parents('.vote-block');
		    		if (!element.hasClass('active'))
		    		{			    		
			    		block.find('.img-circle').not('.mark_answer').removeClass('active');
			    		element.addClass('active');
		    		}
		    		else
	    			{
		    			element.removeClass('active')
	    			}
		    		
		    		block.find('.vote-count').html(data.vote_count);
	    		}
		    }
		});
	}
	
	var showComment = function(element)
	{
		element = $(element);
		element.hide();
		element.parent().find('.comments-form_content').show();
		element.parent().find('textarea').autogrow();
	}
	
	var initViewQuestion = function()
	{
		$('[data-toggle="popover"]').popover();
		
		if ($('#attachments_upload').length > 0)
		{
			extension = $('#attachments_upload').data('extension').split(",");		
			
			uploader = new mooFileUploader.fineUploader({
		        element: $('#attachments_upload')[0],
		        text: {
		            uploadButton: '<div class="upload-section"><i class="material-icons">insert_drive_file</i>'+mooPhrase.__('upload_button_text')+'</div>'
		        },
		        validation: {
		            allowedExtensions: extension,
		            sizeLimit: mooConfig.sizeLimit
		        },
		        request: {
		            endpoint: mooConfig.url.base + '/question/question_upload/attachments/Answer'
		        },
		        callbacks: {
		        	onError: mooGlobal.errorHandler,
		            onComplete: function(id, fileName, response) {
		                var attachs = $('#attachments').val();
		              
		                if (response.attachment_id){
		                	tinyMCE.get('content').insertContent('<p><a href="'+mooConfig.url.base+'/question/question_attachments/download/' + response.attachment_id + '" class="attached-file">' + response.original_filename + '</a></p><br>');
		                    if ( attachs == '' ){
		                        $('#attachments').val( response.attachment_id );
		                    }
		                    else{
		                        $('#attachments').val(attachs + ',' + response.attachment_id);
		                    }
		                }else if(id || response.thumb){
		                	$('#photo_ids').val($('#photo_ids').val() + ',' + response.photo_id);
		                	tinyMCE.get('content').insertContent('<p align="center"><a href="' + response.large + '" class="attached-image"><img src="' + response.thumb + '"></a></p><br>');
		                }
		            }
		        }
		    });
		}
		
		$('#form_reply').submit(function( event ) {
			if (tinyMCE.get('content') != null)
			{
				if (tinyMCE.get('content').getContent({format: 'text'}).trim() != '')
				{
					return true;
				}
				if (tinyMCE.get('content').getContent().search('img') != -1)
				{
					return true;
				}
			}
			else
			{
				if ($('#content').val().trim() != '')
				{
					return true;
				}
			}
			
			event.preventDefault();
		});
		
		initDeleteQuestion();
		
		$('.question_vote_up').click(function(){
			vote(this,$(this).data('type'),$(this).data('id'),1);
		});
		
		$('.question_vote_down').click(function(){
			vote(this,$(this).data('type'),$(this).data('id'),0);
		});
		
		$('.question_add_favorite').click(function(){
			addFavorite($(this).data('id'));
		});
		
		$('.question_mark_best_answer').click(function(){
			markBestAnswer(this,$(this).data('id'));
		});
		
		$('.question_edit_answer').click(function(){
			editAnswer($(this).data('id'));
		});
		
		$('.question_delete_answer').click(function(){
			deleteAnswer($(this).data('id'));
		});
		
		$('.question_more_comment').click(function(){
			showMoreComment(this,$(this).data('type'),$(this).data('id'));
		});
		
		$('.question_comment_action').click(function(){
			comment(this,$(this).data('type'),$(this).data('id'));
		});
		
		$('.question_show_comment').click(function(){
			showComment(this);
		});
		
		$('body').on('click','.question_edit_comment',function(e){
			editComment($(this).data('id'),this);
		});
		
		$('body').on('click','.question_delete_comment',function(e){
			deleteComment($(this).data('id'));
		});
		
		$('body').on('click','.question_cancel_edit_comment',function(e){
			cancelEditComment(this,$(this).data('id'));
		});
		
		$('body').on('click','.question_save_edit_comment',function(e){
			saveEditComment(this,$(this).data('id'));
		});
	}
	
	var comment = function(element,type,id)
	{
		element = $(element);
		text = element.parent().parent().find('textarea');
		if ($.trim(text.val()) != '')
		{
			element.addClass('disabled');
			$.ajax({ 
			    type: 'POST',
			    url: mooConfig.url.base + '/questions/ajax_comment', 
			    data: { 
			    		'type': type,
			    		'id': id,
			    		'message' : $.trim(text.val())
			    	  }, 
			    success: function (html) {
			    	element.removeClass('disabled');
			    	text.val('');
			    	element.parents('.comments-wrapper').find('.comment_list').append($(html));
			    	
			    	mooTooltip.init();
			    }
			});
		}
	}
	
	var editComment = function(id, element)
	{
		$(element).parent().hide();
		$(element).parents('.comment-content').find('.cm-wrap').hide();
		$('#comment_edit_'+id).show();
		$('#comment_edit_'+id).find('textarea').autogrow();
		$('#comment_edit_'+id).find('textarea').val($.trim($(element).parents('.comment-content').find('.cm-wrap').html()));
	}
	
	var cancelEditComment =function (element,id)
	{
		$('#comment_edit_'+id).hide();
		$('#comment_edit_'+id).find('textarea').val('');
		$(element).parents('.comment-content').find('.cm-wrap').show();
		$(element).parents('.comment-content').find('.comment-edit').show();
	}
	
	var deleteComment = function(id)
	{
		$.fn.SimpleModal({
			btn_ok : mooPhrase.__('btn_ok'),
            btn_cancel: mooPhrase.__('btn_cancel'),
            callback: function(){
                $.post(mooConfig.url.base+'/questions/ajax_remove_comment', {id: id}, function() {
                    $('#comment_'+id).fadeOut('normal', function() {
                        $('#comment_'+id).remove();
                    });
                });
            },
            title: mooPhrase.__('confirm_title'),
            contents: mooPhrase.__('please_confirm_remove_this_comment'),
            model: 'confirm', hideFooter: false, closeButton: false
        }).showModal();
	}
	
	var showMoreComment = function (element,type,id)
	{
		$(element).html('&nbsp');
		$(element).parent().spin('small');
		
		$.ajax({ 
		    type: 'POST',
		    url: mooConfig.url.base + '/questions/ajax_load_comment', 
		    data: { 
		    		'type': type,
		    		'id': id
		    	  }, 
		    success: function (html) {
		    	$(element).parent().hide();
		    	$(element).parents('.comments-wrapper').find('.comments-form').show();
		    	$(html).children().each(function(e){
		    		$(element).parents('.comments-wrapper').find('.comment_list').append($(this));
		    	});	 
		    	
		    	mooTooltip.init();
		    }
		});
	}
	
	var saveEditComment = function(element,id)
	{
		element = $(element);
		text = element.parent().parent().find('textarea');
		if ($.trim(text.val()) != '')
		{
			element.addClass('disabled');
			$.ajax({ 
			    type: 'POST',
			    dataType  : 'json',
			    url: mooConfig.url.base + '/questions/ajax_edit_comment', 
			    data: { 
			    		'id': id,
			    		'message' : $.trim(text.val())
			    	  }, 
			    success: function (object) {
			    	$('#comment_edit_' + id).hide();			    	
			    	element.removeClass('disabled');
			    	element.parents('.comment-content').find('.cm-wrap').html($.trim(text.val()));
			    	element.parents('.comment-content').find('.cm-wrap').show();
			    	$(element).parents('.comment-content').find('.comment-edit').show();
			    	text.val('');
			    	
			    	if (object.status)
			    	{
			    		$('#comment_history_' + id).show();
			    	}
			    }
			});
		}
	}
	
	var markBestAnswer = function(element,id)
	{
		$.ajax({ 
		    type: 'POST',
		    dataType  : 'json',
		    url: mooConfig.url.base + '/questions/ajax_mark_best_answer', 
		    data: { 
		    		'id': id		    		
		    	  }, 
		    success: function (object) {
		    	if (object.status)
	    		{
		    		$('.mark_answer').removeClass('active');
		    		if (object.active)
	    			{
		    			$(element).addClass('active');
	    			}
	    		}
		    }
		});
	}
	var deleteAnswer = function(id)
	{
		$.fn.SimpleModal({
			btn_ok : mooPhrase.__('btn_ok'),
            btn_cancel: mooPhrase.__('btn_cancel'),
            callback: function(){
                $.ajax({ 
					type: 'POST',
					dataType  : 'json',
					url: mooConfig.url.base + '/questions/ajax_delete_answer', 
					data: { 
							'id': id		    		
						  }, 
					success: function (object) {
						$('#portlet-config').modal('hide');
						if (object.status)
						{
							$('#answer_'+id).remove();
							$('#count_answer').html(parseInt($('#count_answer').html()) - 1);
						}
					}
				});
            },
            title: mooPhrase.__('confirm_title'),
            contents: mooPhrase.__('confirm_delete_answer'),
            model: 'confirm', hideFooter: false, closeButton: false
        }).showModal();
	}
	
	var editAnswer = function(id)
	{
		$('#edit_answer').modal('show');
		$('#edit_answer_button').removeClass('disabled');
		
		if (tinyMCE.get('content_edit_answer') != null)
		{
			tinyMCE.get('content_edit_answer').setContent($('#content_answer_'+id).html());
		}
		else
		{
			$('#content_edit_answer').val($('#content_answer_'+id).html());
		}
		
		$('#attachments_edit').val('');
		
		
		if ($('#edit_attachments_upload').length > 0)
		{
			extension = $('#edit_attachments_upload').data('extension').split(",");		
			
			uploader = new mooFileUploader.fineUploader({
		        element: $('#edit_attachments_upload')[0],
		        text: {
		            uploadButton: '<div class="upload-section"><i class="material-icons">insert_drive_file</i>'+mooPhrase.__('upload_button_text')+'</div>'
		        },
		        validation: {
		            allowedExtensions: extension,
		            sizeLimit: mooConfig.sizeLimit
		        },
		        request: {
		            endpoint: mooConfig.url.base + '/question/question_upload/attachments/Answer'
		        },
		        callbacks: {
		            onError: mooGlobal.errorHandler,
		            onComplete: function(id, fileName, response) {
		                var attachs = $('#attachments_edit').val();
		              
		                if (response.attachment_id){
		                	tinyMCE.get('content_edit_answer').insertContent('<p><a href="'+mooConfig.url.base+'/question/question_attachments/download/' + response.attachment_id + '" class="attached-file">' + response.original_filename + '</a></p><br>');
		                    if ( attachs == '' ){
		                        $('#attachments_edit').val( response.attachment_id );
		                    }
		                    else{
		                        $('#attachments_edit').val(attachs + ',' + response.attachment_id);
		                    }
		                }else if(id || response.thumb){
		                	$('#edits_photo_ids').val($('#edits_photo_ids').val() + ',' + response.photo_id);
		                	tinyMCE.get('content_edit_answer').insertContent('<p align="center"><a href="' + response.large + '" class="attached-image"><img src="' + response.thumb + '"></a></p><br>');
		                }
		            }
		        }
		    });
		}
		
		$('#edit_answer_button').unbind("click");
		$('#edit_answer_button').click(function(e){
			if (tinyMCE.get('content_edit_answer') != null)
			{
				if (tinyMCE.get('content_edit_answer').getContent({format: 'text'}).trim() == '' && tinyMCE.get('content_edit_answer').getContent().search('img') == -1)
				{
					return;
				}				
			}
			else
			{
				if ($('#content_edit_answer').val().trim() == '')
				{
					return;
				}
			}
			
			$('#edit_answer_button').addClass('disabled');
			var message = '';
			if (tinyMCE.get('content_edit_answer') != null)
			{
				message = tinyMCE.get('content_edit_answer').getContent();
			}
			else
			{
				message = $('#content_edit_answer');
			}
			
			$.ajax({ 
			    type: 'POST',
			    dataType  : 'json',
			    url: mooConfig.url.base + '/questions/ajax_edit_answer', 
			    data: { 
			    		'id': id,
			    		'content': message,
			    		'attachments' : $('#attachments_edit').val(),
			    		'photo_ids' : $('#edits_photo_ids').val(),
			    	  }, 
			    success: function (object) {
			    	$('#edit_answer').modal('hide');
			    	if (object.status)
		    		{
			    		$('#history_answer_'+id).show();
			    		$('#content_answer_'+id).html(message);
		    		}
			    }
			});
		});
	}
	
	var addFavorite = function(id)
	{
		$.ajax({ 
		    type: 'POST',
		    dataType  : 'json',
		    url: mooConfig.url.base + '/questions/ajax_favorite', 
		    data: { 
		    		'id': id,		    		
		    	  }, 
		    success: function (object) {
		    	if (object.status)
	    		{
		    		$('.favorite_t').removeClass('star_active');
		    		$('#favorite_count').html(object.count);
		    		if (object.favorite)
		    		{
		    			$('.favorite_t').addClass('star_active');		    			
		    		}
	    		}
		    }
		});
	}
	
	var editAnswerApp = function(id)
	{			
		$('#attachments_edit').val('');
		
		
		if ($('#edit_attachments_upload').length > 0)
		{
			extension = $('#edit_attachments_upload').data('extension').split(",");		
			
			uploader = new mooFileUploader.fineUploader({
		        element: $('#edit_attachments_upload')[0],
		        text: {
		            uploadButton: '<div class="upload-section"><i class="material-icons">insert_drive_file</i>'+mooPhrase.__('upload_button_text')+'</div>'
		        },
		        validation: {
		            allowedExtensions: extension,
		            sizeLimit: mooConfig.sizeLimit
		        },
		        request: {
		            endpoint: mooConfig.url.base + '/question/question_upload/attachments/Answer'
		        },
		        callbacks: {
		            onError: mooGlobal.errorHandler,
		            onComplete: function(id, fileName, response) {
		                var attachs = $('#attachments_edit').val();
		              
		                if (response.attachment_id){
		                	tinyMCE.get('content_edit_answer').insertContent('<p><a href="'+mooConfig.url.base+'/question/question_attachments/download/' + response.attachment_id + '" class="attached-file">' + response.original_filename + '</a></p><br>');
		                    if ( attachs == '' ){
		                        $('#attachments_edit').val( response.attachment_id );
		                    }
		                    else{
		                        $('#attachments_edit').val(attachs + ',' + response.attachment_id);
		                    }
		                }else if(id || response.thumb){
		                	$('#edits_photo_ids').val($('#edits_photo_ids').val() + ',' + response.photo_id);
		                	tinyMCE.get('content_edit_answer').insertContent('<p align="center"><a href="' + response.large + '" class="attached-image"><img src="' + response.thumb + '"></a></p><br>');
		                }
		            }
		        }
		    });
		}
		
		$('#edit_answer_button').unbind("click");
		$('#edit_answer_button').click(function(e){
			if (tinyMCE.get('content_edit_answer') != null)
			{
				if (tinyMCE.get('content_edit_answer').getContent({format: 'text'}).trim() == '' && tinyMCE.get('content_edit_answer').getContent().search('img') == -1)
				{
					return;
				}				
			}
			else
			{
				if ($('#content_edit_answer').val().trim() == '')
				{
					return;
				}
			}
			$('#edit_answer_button').addClass('disabled');
			var message = '';
			if (tinyMCE.get('content_edit_answer') != null)
			{
				message = tinyMCE.get('content_edit_answer').getContent();
			}
			else
			{
				message = $('#content_edit_answer');
			}
			
			$.ajax({ 
			    type: 'POST',
			    dataType  : 'json',
			    url: mooConfig.url.base + '/questions/ajax_edit_answer', 
			    data: { 
			    		'id': id,
			    		'content': message,
			    		'attachments' : $('#attachments_edit').val(),
			    		'photo_ids' : $('#edits_photo_ids').val(),
			    	  }, 
			    success: function (object) {
			    	$('#edit_answer').modal('hide');
			    	if (object.status)
		    		{
			    		window.mobileAction.backAndRefesh({});
		    		}
			    }
			});
		});
	}
	
	return{
        initCreateQuestion: function(url){
        	initCreateQuestion(url);
        },
        initBrowseQuestion: function(type){
        	initBrowseQuestion(type);
        },
        initViewQuestion: function(){
        	initViewQuestion();
        },
        initListingQuestion: function(){
        	initListingQuestion();
        },
        editComment: function(id,element)
        {
        	editComment(id,element);
        },
        cancelEditComment :function(element,id)
        {
        	cancelEditComment(element,id);
        },
        deleteComment : function(id)
        {
        	deleteComment(id);
        },
        showMoreComment : function (element,type,id)
        {
        	showMoreComment(element,type,id);
        },
        saveEditComment : function (element,id)
        {
        	saveEditComment(element,id);
        },
        editAnswerApp: editAnswerApp
    }
}));