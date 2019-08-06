(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
    	define(['jquery','mooPhrase', 'mooAlert'], factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS-like
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals (root is window)
        root.mooSmsVerify = factory();
    }
}(this, function ($, mooPhrase, mooAlert) {
	var step = 0;
	var init = function()
	{
		$('body').on('validateUser', function(e, data){
		    // data.status = false;
		    // data.message = mooPhrase.__('sms_verify_error');
		});
	};

	var initSend = function()
	{
		step = 0;
		$('#smsVerifyButton').click(function(){
			$('#sms_error').hide();
			if (!step)
			{
				if ($('#sms_phone_number').val().trim() == '')
					return;

				$.ajax({
				    type: 'POST',
				    dataType : 'json',
				    url: mooConfig.url.base + '/sms_verifys/send',
				    data: {
				    	'phone':$('#sms_phone_number').val(),
				    	'g-recaptcha-response' : $('#g-recaptcha-response').val()
				    },
				    success: function (result) {
				    	if (result.status)
	                    {
	                    	$('#sms_phone_number').hide();
	                    	$('#sms_wrap_phone_number').hide();
	                    	$('#sms_phone_code').show();
	                    	$('#sms_wrap_phone_code').show();
	                    	step = 1;
	                    	$('#recaptcha_content').hide();
	                    }
	                    else
	                	{
	                    	$('#sms_error').show();
	                    	$('#sms_error').html(result.message);

	                    	if (typeof grecaptcha == "object")
	                    	{
	                        	grecaptcha.reset();
	                    	}
	                	}
				    }
				});
			}
			else
			{
				if ($('#sms_phone_code').val().trim() == '')
					return;

				$.ajax({
				    type: 'POST',
				    dataType : 'json',
				    url: mooConfig.url.base + '/sms_verifys/check',
				    data: {
				    	'code':$('#sms_phone_code').val()
				    },
				    success: function (result) {
				    	if (result.status)
	                    {
				    		if (mooConfig.isApp)
				    		{
				    			$('#sms_content').hide();
				    			$('#sms_content_message').show();
				    		}
				    		else
				    		{
				    			location.reload();
				    		}
	                    }
	                    else
	                	{
	                    	$('#sms_error').show();
	                    	$('#sms_error').html(result.message);
	                	}
				    }
				});
			}
		});
	};

    var initCheck = function()
    {
        $('#checkButton').unbind('click');
    	$('#checkButton').click(function () {
            if ($('#sms_phone_code').val().trim() == '')
                return;

            $.ajax({
                type: 'POST',
                dataType : 'json',
                url: mooConfig.url.base + '/sms_verifys/check',
                data: {
                    'code':$('#sms_phone_code').val()
                },
                success: function (result) {
                    if (result.status)
                    {
                        if (mooConfig.isApp)
                        {
                            $('#sms_content').hide();
                            $('#sms_content_message').show();
                        }
                        else
                        {
                            $('#themeModal').modal('hide');
                            mooAlert.alert(mooPhrase.__('you_has_been_verified_with_sms'));
                        }
                    }
                    else
                    {
                        $('#sms_error').show();
                        $('#sms_error').html(result.message);
                    }
                }
            });
        });
    };

	return{
		init : init,
		initSend : initSend,
        initCheck : initCheck
    }
}));