(function(root, factory){
    if (typeof define === 'function' && define.amd) {
        // AMD
    	define(['jquery', 'mooPhrase','mooFileUploader','mooButton'], factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS-like
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals (root is window)
        root.mooAds = factory();
    }
}(this, function ($, mooPhrase,mooFileUploader,mooButton){
    var initAds = function(){
        $('#ads_placement_id').change(function(){
           $.post(mooConfig.url.base + "/commercial_placement/load_placement_detail", 'ads_placement_id=' + $('#ads_placement_id').val(), function(data){
            var json = $.parseJSON(data);
            if(json)
            {
                $('#placement_detail').empty().append(json.info);
                $('#required_size').empty().append(json.required_size);
                $('.ads_placement_empty').hide();
                $('.ads_placement').show();
                $('#placementDetail').attr('data-id', json.placement.id).show();
                $('#placement_period').val(json.placement.period);
                if(json.placement.placement_type == 'html' || json.placement.placement_type == 'feed')
                {
                    $('.for_html').show();
                    $('#ads_type').val('html');
                }
                else 
                {
                    $('.for_html').hide();
                    $('#ads_type').val('');
                }
                initDatetimePicker();
                initAdsUploader(json.placement.dimension_width, json.placement.dimension_height);
            }
            else 
            {
                $('#placement_detail').empty().append('');
                $('.ads_placement_empty').show();
                $('.ads_placement').hide();
                $('.for_html').hide();
                $('#placementDetail').removeAttr('data-id').hide();
            }
        });
        });
        // init view all placement
        $('.view_all').on("click",function(){
            viewAllPlacements();
        });
        
        $('#placementDetail').on('click',function(){
            viewPlacement();
        });
        $('#previewBanner').on('click',function(){
            previewBanner();
        });
        $("#createButton").on('click',function(){
            saveAds();
        });
        

    };
    var viewAllPlacements = function(){
            jQuery('#adsModal .modal-dialog').css('width', '1000px');
            jQuery('#adsModal').modal();
            jQuery.post(mooConfig.url.base + "/commercial_placement/load_placement_info", '', function(data){
                jQuery('#adsModal .modal-content').empty().append(data);
            });  
    };
    var viewPlacement = function(item){
        jQuery('#adsModal .modal-dialog').css('width', '260px');
        jQuery('#adsModal').modal();
        jQuery.post(mooConfig.url.base + "/commercial_placement/load_placement_info_detail", 'id=' + $('#placementDetail').attr('data-id'), function(data){
            jQuery('#adsModal .modal-content').empty().append(data);
        });  
    };
    var initDatetimePicker = function(){
        jQuery('#start_date').datepicker({
            changeMonth: true,
            onSelect: function() {
                calcualteEndDate();
            }
        });
    };
    var calcualteEndDate = function(){
        var date = jQuery('#start_date').val();
        var period = jQuery('#placement_period').val();
        if(date)
        {
            var d = new Date(date);
             d.setDate( d.getDate( ) + parseInt(period) );
            jQuery('#end_date').empty().append((d.getMonth( ) + 1 ) + '/' + d.getDate( ) + '/' + d.getFullYear( ));
        }
    };
    var previewBanner = function(){
        if(jQuery('#ads_path').val() != '')
        {
            jQuery('#previewBannerHtml').hide();
            jQuery('#previewBannerImage').hide();
            if(jQuery('#ads_type').val() == 'html' || jQuery('#ads_type').val() == 'feed')
            {
                jQuery('#previewBannerHtml img').attr('src', jQuery('#ads_path').val());
                /*if(jQuery('#link').val() != '')
                {
                    jQuery('#previewBannerHtml a').attr('href', jQuery('#link').val());
                }*/
                jQuery('#previewBannerHtml a.banner_title').empty().append(jQuery('#ads_title').val());
                jQuery('#previewBannerHtml .banner_description').empty().append(jQuery('#ads_description').val());
                jQuery('#previewBannerHtml').show();
            }
            else 
            {
                jQuery('#previewBannerImage img').attr('src', jQuery('#ads_path').val());
                /*if(jQuery('#link').val() != '')
                {
                    jQuery('#previewBannerImage a').attr('href', jQuery('#link').val());
                }*/
                jQuery('#previewBannerImage').show();
            }
        }
    };
    var initAdsUploader = function(image_width, image_height){
        var uploader = new mooFileUploader.fineUploader({
            element: jQuery('#product_image')[0],
            multiple: false,
            text: {
                uploadButton: '<div class="upload-section"><i class="material-icons">photo_camera</i>' + mooPhrase.__('click_to_upload') + '</div>'
            },
            validation: {
                //allowedExtensions: ['jpg', 'jpeg', 'gif', 'png'],
                allowedExtensions: mooConfig.photoExt,
                sizeLimit: mooConfig.sizeLimit

            },
            request: {
                endpoint: mooConfig.url.base + "/commercial/upload_banner/" + image_width + "/" + image_height
            },
            callbacks: {
                    onError: function(event, id, fileName, reason) {
                if ($('.qq-upload-list .errorUploadMsg').length > 0){
                    $('.qq-upload-list .errorUploadMsg').html(mooPhrase.__('can_not_upload_file_more_than'));
                }
                else 
                {
                    $('.qq-upload-list').prepend('<div class="errorUploadMsg">' + mooPhrase.__('can_not_upload_file_more_than') + '</div>');
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
                        //jQuery('#product_image_preview').empty().append('<img src="' + response.path + '" />');
                        jQuery('#ads_image').val(response.filename);
                        jQuery('#ads_path').val(response.path);
                    }
                }
                    }
            }
        });
    };
    
    var saveAds = function(){
        mooButton.disableButton('createButton');
        mooButton.disableButton('cancelButton');
        jQuery(".error-message").hide();
        jQuery(".alert-success").hide();

        //save data
        jQuery.post(mooConfig.url.base + "/commercial/save", jQuery("#formAds").serialize(), function(data){
            var json = $.parseJSON(data);
            if(json.result == 0)
            {
                jQuery(".error-message").show();
                jQuery(".error-message").html(json.message);
                mooButton.enableButton('createButton');
                mooButton.enableButton('cancelButton');
            }
            else
            {
                window.location = json.location;
            } 
        });
    };
    var initReport = function(){
        jQuery("#getReport").on("click",function(){
             jQuery.post(mooConfig.url.base + "/commercial/load_report", jQuery('#formReport').serialize(), function(data){
            jQuery('#reportDetail').empty().append("<div class='report_data'>"+ data + "</div>");
            });
        });
        //
        initReportDatePicker();  
    };
    
    var initReportDatePicker = function(){
        jQuery('#from_date').datepicker({
            changeMonth: true,
            onClose: function( selectedDate ) {
            jQuery("#to_date").datepicker("option", "minDate", selectedDate);
        }
        });
        
        jQuery('#to_date').datepicker({
            changeMonth: true,
            onClose: function( selectedDate ) {
            jQuery("#from_date").datepicker("option", "maxDate", selectedDate);
        }
        });
        
    };
    
    var initLoadAdsInWidget = function(key,num_load_start,listCampaignsId,time_interval){
        
    };
    var loadPlacementDetail = function(){
      $('#ads_placement_id').trigger('change');  
    };
    var initLimitChracter = function(){
        $('#ads_title').on('keyup',function(){
            remainingCharacters(this,25, "ads_title_remaining");
        });
        
         $('#ads_description').on('keyup',function(){
            remainingCharacters(this,80, "ads_description_remaining");
        });
    };
    
    var  remainingCharacters = function(item, max_length, display_id){
        item = jQuery(item);
        var len = item.val().length;
        jQuery('#' + display_id).empty().append(max_length - len);
        if (len >= max_length) 
        {
            return false;
        } 
    };
    
    return {
        initAds: function(){
            initAds();
            initLimitChracter();
        },
        initLoadAdsInWidget: function(key,num_load_start,listCampaignsId,time_interval){
            initLoadAdsInWidget(key,num_load_start,listCampaignsId,time_interval);
        },
        loadPlacementDetail: function(){
            loadPlacementDetail();
        },
        initReport: function(){
            initReport();
        }
    };
}));


