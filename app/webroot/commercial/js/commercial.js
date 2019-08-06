jQuery.ads = {
    saveAds: function()
    {
        disableButton('createButton');
        disableButton('cancelButton');
        jQuery(".error-message").hide();
        jQuery(".alert-success").hide();

        //save data
        jQuery.post(mooConfig.url.base + "/commercial/save", jQuery("#formAds").serialize(), function(data){
            var json = $.parseJSON(data);
            if(json.result == 0)
            {
                jQuery(".error-message").show();
                jQuery(".error-message").html(json.message);
                enableButton('createButton');
                enableButton('cancelButton');
            }
            else
            {
                window.location = json.location;
            } 
        });
    },
    
    viewPlacements: function()
    {
        jQuery('#themeModal .modal-dialog').css('width', '1000px');
        jQuery('#themeModal').modal();
        jQuery.post(mooConfig.url.base + "/commercial_placement/load_placement_info", '', function(data){
            jQuery('#themeModal .modal-content').empty().append(data);
        });
    },
    
    viewPlacementDetail: function(item)
    {
        jQuery('#themeModal .modal-dialog').css('width', '260px');
        jQuery('#themeModal').modal();
        jQuery.post(mooConfig.url.base + "/commercial_placement/load_placement_info_detail", 'id=' + jQuery(item).data('id'), function(data){
            jQuery('#themeModal .modal-content').empty().append(data);
        });
    },
    
    loadPlacementDetail: function()
    {
        jQuery.post(mooConfig.url.base + "/commercial_placement/load_placement_detail", 'ads_placement_id=' + jQuery('#ads_placement_id').val(), function(data){
            var json = jQuery.parseJSON(data);
            if(json)
            {
                jQuery('#placement_detail').empty().append(json.info);
                jQuery('#required_size').empty().append(json.required_size);
                jQuery('.ads_placement_empty').hide();
                jQuery('.ads_placement').show();
                jQuery('#placementDetail').attr('data-id', json.placement.id).show();
                jQuery('#placement_period').val(json.placement.period);
                if(json.placement.placement_type == 'html' || json.placement.placement_type == 'feed')
                {
                    jQuery('.for_html').show();
                    jQuery('#ads_type').val('html');
                }
                else 
                {
                    jQuery('.for_html').hide();
                    jQuery('#ads_type').val('');
                }
                jQuery.ads.calcualteEndDate();
                jQuery.ads.initAdsUploader(json.placement.dimension_width, json.placement.dimension_height);
            }
            else 
            {
                jQuery('#placement_detail').empty().append('');
                jQuery('.ads_placement_empty').show();
                jQuery('.ads_placement').hide();
                jQuery('.for_html').hide();
                jQuery('#placementDetail').removeAttr('data-id').hide();
            }
        });
    },
    
    previewBanner: function()
    {
        if(jQuery('#ads_path').val() != '')
        {
            jQuery('#previewBannerHtml').hide();
            jQuery('#previewBannerImage').hide();
            if(jQuery('#ads_type').val() == 'html')
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
    },
    
    initAdsUploader: function(image_width, image_height)
    {
        var uploader = new qq.FineUploader({
            element: $('#product_image')[0],
            multiple: false,
            text: {
                    uploadButton: '<div class="upload-section"><i class="icon-camera"></i>' + mooPhrase.__('click_to_upload') + '</div>'
            },
            validation: {
//                    allowedExtensions: ['jpg', 'jpeg', 'gif', 'png'],
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
    },
    
    calcualteEndDate: function()
    {
        var date = jQuery('#start_date').val();
        var period = jQuery('#placement_period').val();
        if(date)
        {
            var d = new Date(date);
             d.setDate( d.getDate( ) + parseInt(period) );
            jQuery('#end_date').empty().append((d.getMonth( ) + 1 ) + '/' + d.getDate( ) + '/' + d.getFullYear( ));
        }
    },
    
    adsReport: function()
    {
        jQuery.post(mooConfig.url.base + "/commercial/load_report", jQuery('#formReport').serialize(), function(data){
            jQuery('#reportDetail').empty().append("<div class='report_data'>"+ data + "</div>");
        });
    },
    
    remainingCharacters: function(item, max_length, display_id){
        item = jQuery(item);
        var len = item.val().length;
        jQuery('#' + display_id).empty().append(max_length - len);
        if (len >= max_length) 
        {
            return false;
        } 
    },
    
    isJson: function(str) {
        try 
        {
            JSON.parse(str);
        } 
        catch (e) 
        {
            return false;
        }
        return true;
    }
}