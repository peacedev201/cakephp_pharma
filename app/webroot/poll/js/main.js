(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
    	define(['jquery', 'mooFileUploader', 'mooBehavior', 'mooAlert', 'mooPhrase', 'mooAjax', 'mooUser', 'mooGlobal', 'mooTooltip'], factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS-like
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals (root is window)
        root.mooPoll = factory();
    }
}(this, function ($, mooFileUploader, mooBehavior, mooAlert, mooPhrase, mooAjax, mooUser, mooGlobal, mooTooltip) {
	var initCreatePoll = function(settings)
	{
		 $('#poll_button').click(function(){
	    	button = $(this);
	        button.addClass('disabled');
	        
	        mooAjax.post({
		        url :  mooConfig.url.base + '/polls/save',
		        data: jQuery("#createForm").serialize()
		    }, function(data){
		        var json = $.parseJSON(data);
		
		        if ( json.result == 1 )
				{
		        	if (!mooConfig.isApp)
		        	{
		        		if (settings.url != '')
		        			window.location = settings.url;
		        		else
		        			window.location = mooConfig.url.base + '/polls';
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
	    
	    var uploader = new mooFileUploader.fineUploader({
	        element: $('#poll_thumnail')[0],
	        multiple: false,
	        text: {
	            uploadButton: text_upload_button
	        },
	        validation: {
	            allowedExtensions: ['jpg', 'jpeg', 'gif', 'png'],
	            sizeLimit: mooConfig.sizeLimit
	        },
	        request: {
	            endpoint: mooConfig.url.base + "/polls/upload_avatar"
	        },
	        callbacks: {
	        	onError: mooGlobal.errorHandler,
	            onComplete: function(id, fileName, response) {
	                $('#poll_thumnail_preview > img').attr('src', response.thumb);
	                $('#poll_thumnail_preview > img').show();
	                $('#thumbnail').val(response.file);
	            }
	        }
	    });
	    
	    iMinAnswers = settings.min_answer;
	    $(".sortable").sortable({placeholder: "placeholder", axis: "y"});
	    
	    $('body').on('click','.poll_append_answer',function(e){
	    	appendAnswer(this);
	    });
	    
	    $('body').on('click','.poll_remove_answer',function(e){
	    	removeAnswer(this);
	    });
	    
	    initDeletePoll();
	}
	
	var initDeletePoll = function()
	{
		$('.deletePoll').unbind('click');
		$('.deletePoll').click(function()
		{
			 var data = $(this).data();
			 if (!mooConfig.isApp)
		     {
				 var deleteUrl = mooConfig.url.base + '/polls/delete/' + data.id;
		     }
			 else
			 {
				 var deleteUrl = mooConfig.url.base + '/polls/delete/' + data.id + '?app_no_tab=1';
			 }
	         mooAlert.confirm(mooPhrase.__('delete_poll_confirm'), deleteUrl);
		});
	}
	
	var iMinAnswers = 0;

	var appendAnswer = function(sId)
	{
		iCnt = 0;
		$('.js_answers').each(function()
		{
			if ($(this).parents('.placeholder:visible').length)
			iCnt++;
		});		
		
		//iCnt++;
		var oCloned = $('#createForm .placeholder:first').clone();
		oCloned.find('.js_answers').val('Answer ' + iCnt + '...');
		oCloned.find('.js_answers').addClass('default_value');
		oCloned.find('.hdnAnswerId').remove();
		
		//debug(oCloned.find('.js_answers').val());

		var sInput = '<input type="text" class="js_answers" size="30" value="" name="data[answers][][text]"/>';
		oCloned.find('.class_answer').html(sInput);
		oCloned.find('.js_answers').attr('name', 'data[answers][][text]');
		oCloned.find('.js_answers').attr('value', '');
		var oFirst = oCloned.clone();
		//debug(iCnt+'__' + oFirst.find('.js_answers').val());
		
		var firstAnswer = oFirst.html();

		$(sId).parents('.js_prev_block').parents('.placeholder').after('<div class="placeholder">' + firstAnswer + '</div><div class="js_next_block"></div>')
		return false;
	}

	var removeAnswer = function(sId)
	{
		iCnt = 0;
			
		$('.js_answers').each(function()
		{
			iCnt++;
		});
			
		if (iCnt == iMinAnswers)
		{
			alert(mooPhrase.__('min_answer'));
			return false;
		}
		
		$(sId).parents('.placeholder').remove();
			
		return false;
	}
	
	var process = false;
	
	var changeDisablePoll = function(id,type)
	{
		if (type == 1)
			$('.poll_'+id).find('input').attr('disabled',true);
		else
			$('.poll_'+id).find('input').removeAttr('disabled');
	}
	
	var submit = function(element,poll_id,item_id)
	{
		if (process)
			return;
		
		if (mooUser.validateUser())
		{
			process = true;
			changeDisablePoll(poll_id,1);
			is_activity = $(element).parents('.poll_content').hasClass('is_activity');
			if (is_activity)
				is_activity = 1;
			else
				is_activity = 0;
			
			$.ajax({ 
			    type: 'POST', 
			    url: mooConfig.url.base + '/polls/ajax_save_answer', 
			    data: { 
			    		'poll_id': poll_id,
			    		'item_id': item_id,
			    		'is_activity' : is_activity
			    	  }, 
			    success: function (data) { 
			    	$('.poll_'+poll_id).html(data);
			    	mooTooltip.init();
			    	process = false;
			    }
			});
		}
		else
		{
			$(element).prop('checked', false);
		}
		
	}
	
	var addMore = function(element,poll_id)
	{
		if (process)
			return;
		
		if ($(element).prop("tagName") == 'BUTTON')
		{
			element = $(element).parent().parent().find('.poll_add_more');
		}
		else
		{
			element = $(element);
		}
			
		if ($.trim(element.val()) != '')
		{
			changeDisablePoll(poll_id,1);
			process = true;	
			$.ajax({ 
			    type: 'POST', 
			    url: mooConfig.url.base + '/polls/ajax_add_answer', 
			    data: { 
			    		'poll_id': poll_id,
			    		'text' : $.trim(element.val())
			    	  }, 
			    success: function (data) { 
			    	$('.poll_'+poll_id).html(data);
			    	mooTooltip.init();
			    	process = false;
			    }
			});
		}
	}
	
	var isMobile = function() 
	{
		var check = false;
		(function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4)))check = true})(navigator.userAgent||navigator.vendor||window.opera);
		return check;
	}
	var checkInitPollGlobal = false;
	var initPollGlobal = function()
	{
		if (checkInitPollGlobal)
			return;
		
		checkInitPollGlobal = true;
		
		if (!isMobile())
		{
			$('body').on('keypress','.poll_add_more',function(e){
				var data = $(this).data();
				var key = e.which;
				if(key == 13)
					addMore(this,data.pollId);
				
			});
		}
		else
		{
			$('body').on('click','.poll_button_add_more',function(e){
				var data = $(this).data();
				addMore(this,data.pollId);
			});
		}
		
		$('body').on('click','.poll_answer',function(e){
			var data = $(this).data();
			submit(this,data.pollId,data.itemId);
		});
		
		
	}
	var page_user_vote = 1;
	var getMoreUserVote = function(id)
	{
		page_user_vote++;
		$('.poll_user_more').hide();
		$.ajax({ 
		    type: 'POST', 
		    url: mooConfig.url.base + '/polls/ajax_show_user_answer/'+id, 
		    data: { 
		    		'page': page_user_vote
		    	  }, 
		    success: function (data) { 
		    	$('#poll_list_user_answer').html($('#poll_list_user_answer').html()+data);
		    }
		});
	}
	var setPageUserVote = function(page)
	{
		page_user_vote = page;
	}
	
	var initOnListing = function()
	{	
		mooBehavior.initMoreResults();
		
		initDeletePoll();		
	}
	
	var initOnAnswer = function()
	{
		initPollGlobal();
	}
	
	var initViewPoll = function()
	{		
		initDeletePoll();
	}
	
	var initOnListingUserAnswer = function()
	{
		$('#poll_user_more').click(function(){
			getMoreUserVote($(this).data('id'));
		});
		
		setPageUserVote(1);
	}

	return{
		initCreatePoll: function(settings){
			initCreatePoll(settings);
        },
        initOnAnswer : function()
        {
        	initOnAnswer();
        },
        initPollGlobal : function()
        {
        	initPollGlobal();
        },
        initOnListing : function()
        {
        	initOnListing();
        },
        initViewPoll : function()
        {
        	initViewPoll();
        },
        initOnListingUserAnswer : function()
        {
        	initOnListingUserAnswer();
        }
    }
}));