jQuery.admin = {
		typeChange : function()
		{
			current = $('#billing_cycle_type');
			tmp = $('#duration_type');
			tmp.val(current.val());
			
			tmp.find('option').each(function(){
				$(this).removeAttr('disabled');
				if ($(this).attr('value') != current.val() && $(this).attr('value') != 5)
				{
					$(this).attr('disabled','disabled');
				}
			});
		},
        changeBusinessStatus: function (item, id, default_select)
        {
            if(jQuery(item).val() == 'rejected')
            {
                jQuery.post(mooConfig.url.base + '/admin/business/business/reject_dialog/' + id, function(data){
                    jQuery('#businessModal .modal-content').empty().append(data);
                    jQuery('#businessModal').modal({
                        backdrop: 'static'
                    });
                })
                
                jQuery('#businessModal').on('hidden.bs.modal', function () {
                    jQuery(item).val(default_select);
                })
            }
            else
            {
                mooConfirm(mooPhrase.__('tconfirm'), mooConfig.url.base + '/admin/business/business/status/' + jQuery(item).val() + '/' + id)
                jQuery('#portlet-config').on('hidden.bs.modal', function () {
                    jQuery(item).val(default_select);
                })
            }
        },
        doRejectbusines: function()
        {
            //reject business
            disableButton('rejectButton');
            disableButton('cancelRejectButton');
            jQuery.post(mooConfig.url.base + '/admin/business/business/reject_business/', jQuery('#rejectForm').serialize(), function(data){
                var json = jQuery.parseJSON(data);
                if(json.result == 0)
                {
                    jQuery("#rejectMessage").show();
                    jQuery("#rejectMessage").html(json.message);
                    enableButton('rejectButton');
                    enableButton('cancelRejectButton');
                }
                else
                {
                    window.location = mooConfig.url.base + '/admin/business/business/?status=rejected';
                } 
            });
        },
        initOptions: function () {
			if($('.datepicker').length > 0) {
				$('.datepicker').pickadate({
					monthsFull: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
					monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
					weekdaysFull: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
					weekdaysShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
					today:"Today",
					clear:"Clear",
					close: "Close",
					format: 'yyyy-mm-dd'
				});
			}
            $(document).on('hidden.bs.modal', function (e) {
                $(e.target).removeData('bs.modal');
            });
            $('.js_drop_down_link').click(function (){
                eleOffset = $(this).offset();
                $('#js_drop_down_cache_menu').remove();
                $('body').prepend('<div id="js_drop_down_cache_menu" style="position:absolute; left:' + eleOffset.left + 'px; top:' + (eleOffset.top + 15) + 'px; z-index:9999;"><div class="link_menu" style="display:block;">' + $(this).parent().find('.link_menu:first').html() + '</div></div>');
                $('#js_drop_down_cache_menu .link_menu').hover(function ()
                {

                },
                function ()
                {
                $('#js_drop_down_cache_menu').remove();
                });
                return false;
            });
        },
        initCreateItem: function (url) {
            $('#createButton').click(function () {
            disableButton('createButton');
                    $.post(url, $("#createForm").serialize(), function (data) {
                    enableButton('createButton');
                            var json = $.parseJSON(data);
                            if (json.result == 1)
                            location.reload();
                            else
                    {
                    $(".error-message").show();
                            $(".error-message").html(json.message);
                    }
                    });
                    return false;
            });
        },
        initTranslate: function(url) {
            $('#tCreateButton').click(function() {
            disableButton('tCreateButton');
                    $.post(url, $("#tCreateForm").serialize(), function(data) {
                    enableButton('tCreateButton');
                            var json = $.parseJSON(data);
                            if (json.result == 1)
                            location.reload();
                            else
                    {
                    $(".error-message").show();
                            $(".error-message").html('<strong>Error!</strong>' + json.message);
                    }
                    });
                    return false;
            });
        },
        initPackageScript : function() {
        	var self = this;
            $("#recurring_type #price").prop('disabled', true);
            $('#type').change(function(){
                if ($('#type').val() == '1') {
                    $("#recurring_type").hide();
                    $("#recurring_type #price").prop('disabled', true);
                    $("#onetime_type #price").prop('disabled', false);
                    $("#recurring_type #billing_cycle").prop('disabled', true);
                    $("#onetime_type #billing_cycle_type").prop('disabled', true);
                    $("#onetime_type").show();
                    
                    tmp = $('#duration_type');
        			
        			tmp.find('option').each(function(){
        				$(this).removeAttr('disabled');
        			});
                } else{
                    $("#recurring_type").show();
                    $("#recurring_type #price").prop('disabled', false);
                    $("#onetime_type #price").prop('disabled', true);
                    $("#recurring_type #billing_cycle").prop('disabled', false);
                    $("#onetime_type #billing_cycle_type").prop('disabled', false);
                    $("#onetime_type").hide();

                    self.typeChange();
                }
            });

            $('#trial_package').change(function(){
                if($("#trial_package").is(':checked')) {
                    $('#trial_package_select').show();
                    $('.sl_form_group').hide();
                    $('.sl_form_group_hide').hide();
                    $("#type option[value='2']").remove();
                }else{
                    $('#trial').val(0);
                    $('#trial_package_select').hide();
                    $('.sl_form_group').show();
                    $('.sl_form_group_hide').show();
                    $("#type").append('<option value="2">Recurring</option>');
                    $('#package_select').val('');
                    $('.sl_form_group :checkbox').each(function(indx, item){
                        $(item).prop("checked",false);
                    });
                    $(':input','#createForm')
                     .not(':button, :submit, :reset, :hidden, :checbox')
                     .val('')
                     .removeAttr('selected');
                     $('#type').val(1);
                     $(':input','#createForm')
                     .not(':button, :submit, :reset, :hidden').removeAttr('checked');
                }
            });
            $('#package_select').change(function(){
                $('.sl_form_group :checkbox').each(function(indx, item){
                    $(item).prop("checked",false);
                });
                var package_id = parseInt($(this).val()) ;
                if( package_id > 0) {
                    jQuery.post(mooConfig.url.base + '/admin/business/business_packages/get_package/', 'package_id=' + package_id, function(data){
                        if(data != 'null') {
                            var jdata = jQuery.parseJSON(data);
                            $('#trial').val(jdata.id);
                            $('#name').val('Trial for ' + jdata.name);
                            $("#onetime_type #price").val(jdata.price);
                            $('#duration').val(jdata.duration);
                            $('#duration_type').val(jdata.duration_type);
                            $('#expiration_reminder').val(jdata.expiration_reminder);
                            $('#expiration_reminder_type').val(jdata.expiration_reminder_type);
                            $('#cat_number').val(jdata.cat_number);
                            $('#photo_number').val(jdata.photo_number);
                            $('#free_schedule_blog').val(jdata.free_schedule_blog);
                            $('#free_schedule_poll').val(jdata.free_schedule_poll);
                            $('#free_schedule_event').val(jdata.free_schedule_event);
                            $('#free_schedule_job').val(jdata.free_schedule_job);
                            var options = jQuery.parseJSON(jdata.options);
                            $.each( options, function(key, value ) {
                                console.log(value);
                                console.log($(":checkbox[value="+value+"]"));
                              $(":checkbox[value="+value+"]").prop("checked",true);
                            });  
                            $('#type').val(1);
                            $('.sl_form_group').show();
                        } else {
                            alert('Can not find package');
                        }
                    });

                }else{
                    $('#trial').val(0);
                    $('.sl_form_group').hide();
                }
            });
            $('#billing_cycle_type').change(function(){
            	self.typeChange();
            });

            if ($('#type').val() == 2)
            {
            	self.typeChange();
            }

        },
        saveOrder: function (url) {
            var list = {};
            $('input[name="data[ordering]"]').each(function(index, value){
                list[$(value).data('id')] = $(value).val();
            });
            jQuery.post(url, {order:list}, function(data){
                window.location = data;
            });
        },
        initFeatureCategory: function(id, url) {
            jQuery('#'+id).autocomplete({
                source: function (request, response) {
                    jQuery.post(mooConfig.url.base + '/admin/business/business_categories/suggest_category/', 'keyword=' + jQuery('#'+id).val(), function(data){
                        if(data != 'null')
                        {
                            response(jQuery.parseJSON(data));
                        }
                        else
                        {
                            jQuery('#feature_id').val('');
                        }
                    });
                },
                change: function(event, ui) {
                },
                minLength: 2,
                select: function (event, ui) {
                    jQuery('#'+id).val(ui.item.label);
                    jQuery('#feature_id').val(ui.item.value);
                    return false;
                }
            });
             $('#featureButton').click(function () {
                disableButton('featureButton');
                $.post(url, $("#featureForm").serialize(), function (data) {
                enableButton('featureButton');
                    var json = $.parseJSON(data);
                    if (json.result == 1)
                        location.reload();
                    else
                    {
                        $(".error-message").show();
                        $(".error-message").html(json.message);
                    }
                });
                return false;
            });
        },
        mooAlert: function(msg){
            $.fn.SimpleModal({btn_ok: 'OK', title: 'Message', hideFooter: false, closeButton: false, model: 'alert', contents: msg}).showModal();
        },
        
        setBusinessFeatured: function(link)
        {
            jQuery.post(link, jQuery('#featuredForm').serialize(), function(data){
                var json = jQuery.parseJSON(data);
                if(json.result == 0)
                {
                    jQuery('#featuredMessage').empty().append(json.message).show();
                }
                else
                {
                    location.reload();
                }
            });
        },
        initIconUploader: function()
        {
            var uploader = new mooFileUploader.fineUploader({
                element: $('#select-0')[0],
                multiple: false,
                text: {
                        uploadButton: '<div class="upload-section"><i class="material-icons">photo_camera</i>' + mooPhrase.__('tdesc') + '</div>'
                },
                validation: {
                        allowedExtensions: ['jpg', 'jpeg', 'gif', 'png'],

                },
                request: {
                        endpoint: mooConfig.url.base + "/admin/business/business_categories/upload_icon/"
                },
                callbacks: {
                        onError: function(event, id, fileName, reason) {
                    if ($('.qq-upload-list .errorUploadMsg').length > 0){
                        $('.qq-upload-list .errorUploadMsg').html(mooPhrase.__('tmaxsize'));
                    }
                    else 
                    {
                        $('.qq-upload-list').prepend('<div class="errorUploadMsg">' + mooPhrase.__('tmaxsize') + '</div>');
                    }
                    $('.qq-upload-fail').remove();
                },
                onComplete: function(id, fileName, response) {
                    if(response.success == 0)
                    {
                        jQuery('.qq-upload-fail:last .qq-upload-status-text').empty().append(response.message);

                    }
                    else 
                    {
                        if(response.filename)
                        {
                            jQuery('#uploader_value').val(response.filename);
                            jQuery('#uploader_image').attr('src', response.path).show();
                        }
                    }
                }}
            });
        },
}