
/*
 * manager
 */
(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
    	define(['jquery', 'mooFileUploader', 'mooBehavior', 'mooAlert', 'mooPhrase', 'mooAjax', 'mooUser', 'mooGlobal', 'mooButton'], factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS-like
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals (root is window)
        root.store_manager = factory();
    }
}(this, function ($, mooFileUploader, mooBehavior, mooAlert, mooPhrase, mooAjax, mooUser, mooGlobal, mooButton) {
    var initHelp = function()
    {
        if(typeof helps != 'undefined')
        {
            helps = jQuery.parseJSON(helps);
            jQuery('.help-tip').each(function(){
                var tip = jQuery(this).data('tip').toString();
                if(helps[tip])
                {
                    jQuery(this).empty().append('<p>' + helps[tip] + '</p>');
                }
            })
        }
    }
    
    var initManagerMenu = function()
    {
        jQuery(document).on('click', '#load_manager_menu', function(){
            jQuery.post(mooConfig.url.base + "/stores/manager/load_manager_menu", '', function(data){
                jQuery('#storeModal .modal-content').empty().append(data); 
                jQuery('#storeModal').modal();
                $('#side-menu-dialog').metisMenu();
            });
        })
        
        if(jQuery('#side-menu').length > 0)
        {
            jQuery('#side-menu').metisMenu();
        }
        
        jQuery(document).on('click', '#delete_seller', function(){
            mooConfirmBox(mooPhrase.__('text_confirm_delete_seller'), function(){
                var link = mooConfig.url.base + "/stores/manager/delete";
                if(mooConfig.isApp)
                {
                    link += "?app_no_tab=1";
                }
                window.location = link;
            });
        })
        
        //buy featured product
        jQuery(document).on('click', '.buy_featured_product', function(){
            var item = jQuery(this);
            mooConfirmBox(jQuery('#buy_feature_product_confirm').html(), function(){
                var link = mooConfig.url.base + "/stores/manager/store_packages/buy_featured_product/" + item.data('id');
                if(mooConfig.isApp)
                {
                    link += "?app_no_tab=1";
                }
                jQuery.post(link, jQuery('#adminForm').serialize(), function (data) {
                    if(!isJson(data))
                    {
                        location.reload();
                    }
                    else
                    {
                        var json = jQuery.parseJSON(data);
                        if(json.result == 0)
                        {
                            mooAlert.alert(json.message);
                        }
                        else
                        {
                            var redirect = mooConfig.url.base + json.redirect;
                            if(mooConfig.isApp)
                            {
                                redirect += "?app_no_tab=1";
                            }
                            window.location = redirect;
                        }
                    }
                });
            })
        })
        
        //buy featured store
        jQuery(document).on('click', '.buy_featured_store', function(){
            mooConfirmBox(jQuery('#buy_feature_store_confirm').html(), function(){
                var link = mooConfig.url.base + "/stores/manager/store_packages/buy_featured_store/";
                if(mooConfig.isApp)
                {
                    link += "?app_no_tab=1";
                }
                jQuery.post(link, jQuery('#adminForm').serialize(), function (data) {
                    if(!isJson(data))
                    {
                        location.reload();
                    }
                    else
                    {
                        var json = jQuery.parseJSON(data);
                        if(json.result == 0)
                        {
                            mooAlert.alert(json.message);
                        }
                        else
                        {
                            var redirect = mooConfig.url.base + json.redirect;
                            if(mooConfig.isApp)
                            {
                                redirect += "?app_no_tab=1";
                            }
                            window.location = redirect;
                        }
                    }
                });
            })
        })
    }
    
    /////////////////////////////////////////////manage list/////////////////////////////////////////////
    var initManage = function()
    {
        //check all
        jQuery(document).on('click', '.group_checkbox', function(){
            if (jQuery(this).is(':checked'))
            {
                jQuery('.multi_cb').trigger('click');
            }
            else
            {
                jQuery('.multi_cb').removeAttr('checked');
            }
        })
        
        //delete all
        jQuery(document).on('click', '#delete_all', function(){
            if(jQuery('input.multi_cb:checked').length < 1)
            {
                mooAlert.alert(mooPhrase.__('you_must_select_at_least_an_item'));
            }
            else
            {
                mooConfirmBox(mooPhrase.__('are_you_sure_you_want_to_delete'), function(){doSubmit('delete')});
            }
            
        })
        
        //enable all
        jQuery(document).on('click', '#enable_all', function(){
            if(jQuery('input.multi_cb:checked').length < 1)
            {
                mooAlert.alert(mooPhrase.__('you_must_select_at_least_an_item'));
            }
            else 
            {
                doSubmit('enable');
            }
        })
        
        //disable all
        jQuery(document).on('click', '#disable_all', function(){
            if(jQuery('input.multi_cb:checked').length < 1)
            {
                mooAlert.alert(mooPhrase.__('you_must_select_at_least_an_item'));
            }
            else 
            {
                doSubmit('disable');
            }
        })
        
        //ordering all
        jQuery(document).on('click', '#ordering_all', function(){
            if(jQuery('input.multi_cb:checked').length < 1)
            {
                mooAlert.alert(mooPhrase.__('you_must_select_at_least_an_item'));
            }
            else 
            {
                doSubmit('ordering');
            }
        })
        
        //delete
        jQuery(document).on('click', '.action_delete', function(){
            action(jQuery(this).data('id'), 'delete')
        })
        
        //enable
        jQuery(document).on('click', '.action_enable', function(){
            action(jQuery(this).data('id'), 'enable')
        })
        
        //disable
        jQuery(document).on('click', '.action_disable', function(){
            action(jQuery(this).data('id'), 'disable')
        })
        
        if(mooConfig.isApp)
        {
            jQuery(document).on('submit', '#searchForm', function(){
                $('<input />').attr('type', 'hidden')
                .attr('name', "app_no_tab")
                .attr('value', 1)
                .appendTo('#searchForm');
                return true;
            })
        }
    }
    
    var action = function(id, task) 
    {
        $('input.multi_cb').removeAttr('checked');
        $('#cb' + id).trigger('click');
        if(task == 'delete')
        {
            mooConfirmBox(mooPhrase.__('are_you_sure_you_want_to_delete'), function(){doSubmit(task)});
        }
        else if(task == 'create')
        {
            window.location = jQuery('#adminForm').attr('action') + task + '/' + id;
        }
        else
        {
            doSubmit(task);
        }
    }
    
    var doSubmit = function(task)
    {
        var action = jQuery('#adminForm').attr('action');
        var link = action + task; 
        if(mooConfig.isApp)
        {
            link = action + task + '?app_no_tab=1';
        }
        //jQuery('#adminForm').attr('action', link);
        //jQuery('#adminForm').submit();
        jQuery.post(link, jQuery('#adminForm').serialize(), function () {
            location.reload();
        });
    }
    
    var mooConfirmBox = function( msg, callback )
    {
        // Set title
        $($('#portlet-config  .modal-header .modal-title')[0]).html(mooPhrase.__('please_confirm'));
        // Set content
        $($('#portlet-config  .modal-body')[0]).html(msg);
        // OK callback, remove all events bound to this element
        $('#portlet-config  .modal-footer .ok').off("click").click(function(){
            callback();
            $('#portlet-config').modal('hide');
        });
        $('#portlet-config').modal('show');

    }
 
    /////////////////////////////////////////////create product/////////////////////////////////////////////
    var initCreateProduct = function()
    {
        // uploader
        var uploader = new mooFileUploader.fineUploader({
            element: jQuery('#product_image')[0],
            multiple: true,
            text: {
                uploadButton: '<div class="upload-section"><i class="material-icons">camera_enhance</i></i>' + mooPhrase.__('drag_or_click_here_to_upload_photo') + '</div>'
            },
            validation: {
                allowedExtensions : mooConfig.photoExt,
                sizeLimit : mooConfig.sizeLimit
            },
            request: {
                endpoint: mooConfig.url.base + "/stores/product_upload/images/"
            },
            callbacks: {
                onComplete: function(id, fileName, response) {
                    if(response.success == 0)
                    {
                        jQuery('.qq-upload-fail:last .qq-upload-status-text').empty().append(response.message);

                    }
                    else 
                    {
                        if(response.filename != '')
                        {
                            var item = jQuery(jQuery("#imgItemTemplate").html());
                            item.find('img').attr('src', response.thumb);
                            item.find('.image_filename').val(response.filename);
                            item.find('.image_path').val(response.path);
                            jQuery("#tbImage tbody").append(item);
                        }
                        initUpDown('tbImage');
                    }
                }
            }
        });
        
        //datetime picker
        initDatetimePicker('promotion_start', 'promotion_end');
        
        //promotions
        showProductPromotion();
        jQuery(document).on('click', '#allow_promotion', function(){
            showProductPromotion();
        })
        
        //product image ordering
        initUpDown('tbImage');
        jQuery(document).on('click', '#tbImage .btn-up', function(){
            var prev = jQuery(this).closest('tr').prev();
            jQuery(this).closest('tr').insertBefore(prev);
            initUpDown('tbImage');
        })

        jQuery(document).on('click', '#tbImage .btn-down', function(){
            var next = jQuery(this).closest('tr').next();
            jQuery(this).closest('tr').insertAfter(next);
            initUpDown('tbImage');
        })
        
        //product video ordering
        initUpDown('tbVideo');
        jQuery(document).on('click', '#tbVideo .btn-up', function(){
            var prev = jQuery(this).closest('tr').prev();
            jQuery(this).closest('tr').insertBefore(prev);
            initUpDown('tbVideo');
        })

        jQuery(document).on('click', '#tbVideo .btn-down', function(){
            var next = jQuery(this).closest('tr').next();
            jQuery(this).closest('tr').insertAfter(next);
            initUpDown('tbVideo');
        })
        
        //load product video
        loadProductVideo();
        
        //show attr dialog
        jQuery(document).on('click', '#btnShowAttrDlg', function(){
            showAttributeDialog(1, '#attribute_buy')
        })
        
        //add atribute
        jQuery(document).on('click', '#btnAddAttr', function(){
            addAttribute();
        })
        
        //remove attribute
        jQuery(document).on('click', '.remove_attribute', function(){
            jQuery(this).closest('.form-group').remove();
        })
        
        //remove remove_added_attribute
        jQuery(document).on('click', '.remove_added_attribute', function(){
            var attribute_id = jQuery(this).data('id');
            jQuery(this).closest('.form-group').remove();
            var json = jQuery.parseJSON(jQuery('#attributeData').html());
            for(var i in json)
            {
                if(json[i].attribute_id == attribute_id) 
                {
                    json.splice(i, 1);
                }
            }
            jQuery('#attributeData').html(JSON.stringify(json));
        })
        
        //save product
        jQuery(document).on('click', '#btnSave', function(){
            saveProduct();
        })
        
        jQuery(document).on('click', '#btnApply', function(){
            saveProduct(1);
        })
        
        //delete product image
        jQuery(document).on('click', '.delete_image', function(){
            deleteProductImage(jQuery(this));
        })
        
        //delete product video
        jQuery(document).on('click', '.delete_video', function(){
            deleteProductVideo(jQuery(this));
        })
        
        //select product type
        jQuery(document).on('change', '#product_type', function(){
            switch(jQuery(this).val())
            {
                case mooPhrase.__("STORE_PRODUCT_TYPE_DIGITAL"):
                    jQuery('.for_digital_product').show();
                    jQuery('.for_link_product').hide();
                    break;
                case mooPhrase.__("STORE_PRODUCT_TYPE_LINK"):
                    jQuery('.for_digital_product').hide();
                    jQuery('.for_link_product').show();
                    break;
                default:
                    jQuery('.for_digital_product').hide();
                    jQuery('.for_link_product').hide();
            }
        })
        
        //uploader digital product
         var uploader = new mooFileUploader.fineUploader({
            element: jQuery('#product_digital_file')[0],
            autoUpload: true,
            text: {
                uploadButton: '<div class="upload-section"><i class="material-icons">attachment</i>' + mooPhrase.__('drag_or_click_here_to_upload_file') + '</div>'
            },
            validation: {
                allowedExtensions : mooPhrase.__('store_allow_digital_file_extensions').split(','),
                sizeLimit : mooConfig.sizeLimit
            },
            request: {
                endpoint: mooConfig.url.base + "/stores/product_upload/files"
            },
            callbacks: {
                onError: mooGlobal.errorHandler,
                onComplete: function (id, fileName, response) {
                    jQuery("#digital_file").val(response.filename);
                    jQuery("#product_digital_file_preview").html(response.filename).show();
                }
            }
        });
        initVideoShortList();      
        
        
        jQuery('#storeModal').on('hidden.bs.modal', function () {
            jQuery('#storeModal .modal-body').empty();
        })
    }
    
    function initVideoShortList()
    {
        //show select video dialog
        jQuery(document).on('click', '#btn_select_video', function(e){
            jQuery('#storeModal').modal('show');
            loadVideoShortList();
        })
        
        //load video short list paging
        jQuery(document).on('click', '#video-short-list-paging li a', function(e){
            e.stopPropagation();
            e.preventDefault();
            loadVideoShortList(jQuery(this).attr('href'));
        })
        
        //search video short list
        jQuery(document).on('click', '#search_video_short_list', function(e){
            if(jQuery('#videoShortListForm #keyword').val() != '')
            {
                loadVideoShortList();
            }
        })
        
        //select video
        jQuery(document).on('click', '.select_video', function(e){
            var item = jQuery(this);
            var video = jQuery(jQuery("#videoItemTemplate").html());
            video.find('.video_image').attr('src', item.data('image'));
            video.find('.video_name').html(item.data('title'));
            video.find('.video_id').val(item.data('id'));
            jQuery("#tbVideo tbody").append(video);
            initUpDown('tbVideo');
            jQuery('#storeModal').modal('hide');
        })
    }
    
    var initSelectAttributeDlg = function()
    {
        //select cat group
        jQuery(document).on('click', '.attribute_parent', function(){
            var id = jQuery(this).data('id');
            jQuery('.attribute_group' + id).removeAttr('checked', 'checked');
            if(jQuery('#attribute_parent' + id).is(':checked'))
            {
                jQuery('.attribute_group' + id).trigger('click');
            }
        })
        
        //select parent
        jQuery(document).on('click', '.attribute_group', function(){
            var id = jQuery(this).data('id');
            if(jQuery('.attribute_group' + id + ':checked').length > 0)
            {
                jQuery('#attribute_parent' + id).prop('checked', true);
            }
            else
            {
                jQuery('#attribute_parent' + id).removeAttr('checked');
            }
        })
        
        //save
        jQuery(document).on('click', '#btnAttrSave', function(){
            var buy = jQuery(this).data('buy');
            jQuery('.attribute_parent').each(function(){
                if(jQuery('.attribute_group' + jQuery(this).val() + ':checked').length > 0)
                {
                    jQuery(this).prop('checked', true);
                }
            })
            $.post(mooConfig.url.base + "/stores/manager/attributes/show_select_list", $("#formSelect").serialize(), function(data){
                if(isJson(data))
                {
                    var json = jQuery.parseJSON(data);
                    jQuery('#attributeMessage').html(json.message).show();
                }
                else
                {
                    if(buy == 1)
                    {
                        jQuery('#attributeToBuy').empty().append(data);
                    }
                    else
                    {
                        jQuery('#attributeList').empty().append(data);
                    }
                    jQuery('#storeModal').modal('hide');
                }
            });
        })
    }
    
    function addAttribute(attribute_data)
    {
        if(typeof attribute_data != 'undefined' && attribute_data != '')
        {
            attribute_data = jQuery.parseJSON(attribute_data);
            for(var i in attribute_data)
            {
                var data = attribute_data[i];
                var new_item = jQuery(jQuery('#attributeDataTemplate').html());
                new_item.find('.attribute_id').val(data.attribute_id);
                new_item.find('.plus').val(data.plus);
                new_item.find('.attribute_price').val(data.attribute_price);
                jQuery('#attributes_content').append(new_item);
            }
        }
        else
        {
            var new_item = jQuery(jQuery('#attributeDataTemplate').html());
            jQuery('#attributes_content').append(new_item);
        }
    }
    
    var initDatetimePicker = function(start_id, end_id)
    {
        jQuery("#" + start_id).datepicker({
            changeMonth: true,
            onClose: function( selectedDate ) {
                jQuery("#" + end_id).datepicker("option", "minDate", selectedDate);
            }
        });
        jQuery("#" + end_id).datepicker({
            changeMonth: true,
            onClose: function( selectedDate ) {
                jQuery("#" + start_id).datepicker("option", "maxDate", selectedDate);
            }
        });
    }
    
    var initUpDown = function(wrapper_id)
    {
        jQuery('#' + wrapper_id + ' tbody tr').find('.btn-up').show();
        jQuery('#' + wrapper_id + ' tbody tr').find('.btn-down').show();
        jQuery('#' + wrapper_id + ' tbody tr:first-child').find('.btn-up').hide();
        jQuery('#' + wrapper_id + ' tbody tr:last-child').find('.btn-down').hide();
    }
    
    var loadProductAttributes = function(product_id, buy)
    {
        $.post(mooConfig.url.base + "/stores/manager/attributes/show_select_list", 'product_id=' + product_id + '&buy=' + buy, function(data){
            if(buy == 1)
            {
                jQuery('#attributeToBuy').empty().append(data);
            }
            else 
            {
                jQuery('#attributeList').empty().append(data);
            }
        });
    }
    
    var showAttributeDialog = function(buy, input_ids)
    {
        input_ids = jQuery(input_ids);
        if(input_ids.length > 0)
        {
            input_ids = input_ids.val();
        }
        jQuery('#storeModal').modal({
            backdrop: "static"
        });
        $.post(mooConfig.url.base + "/stores/manager/attributes/select_list/" + buy, 'ids=' + input_ids, function(data){
            jQuery('#storeModal .modal-content').empty().append(data);
            
            //add attribute
            if(jQuery('#attributeData').text().trim() != '')
            {
                addAttribute(jQuery('#attributeData').text().trim());
            }
        });
    }
    
    var saveProduct = function(apply)
    {
        mooButton.disableButton('btnSave');
        mooButton.disableButton('btnApply');
        mooButton.disableButton('btnCancel');
        $(".error-message").hide();
        $(".alert-success").hide();

        //set data for editor
        for (i=0; i < tinyMCE.editors.length; i++)
        {
            var content = tinyMCE.editors[i].getContent();
            jQuery('#' + tinyMCE.editors[i].id).val(content);
        }
        if(apply == 1)
        {
            jQuery('#save_type').val(1);
        }

        //set main image
        var count = -1;
        jQuery('#tbImage tbody .is_main').each(function(){
            count++;
            if(jQuery(this).is(':checked'))
            {
                jQuery(this).val(count);
            }
        })

        //set enable image
        var count = -1;
        jQuery('#tbImage tbody .enable_image').each(function(){
            count++;
            if(jQuery(this).is(':checked'))
            {
                jQuery(this).val(count);
            }
        })
        
        //set enable video
        var count = -1;
        jQuery('#tbVideo tbody .enable_video').each(function(){
            count++;
            if(jQuery(this).is(':checked'))
            {
                jQuery(this).val(count);
            }
        })

        //save data
        $.post(mooConfig.url.base + "/stores/manager/products/save", $("#formProduct").serialize(), function(data){
            var json = $.parseJSON(data);
            if(json.result == 0)
            {
                $(".error-message").show();
                $(".error-message").html(json.message);
                mooButton.enableButton('btnSave');
                mooButton.enableButton('btnApply');
                mooButton.enableButton('btnCancel');
            }
            else
            {
                if(mooConfig.isApp)
                {
                    if(apply == 1)
                    {
                        window.location = json.location + "?app_no_tab=1";
                    }
                    else
                    {
                        window.mobileAction.backAndRefesh();
                    }
                }
                else
                {
                    window.location = json.location;
                }
            } 
        });
    }
    
    var showProductPromotion = function()
    {
        if(jQuery('#allow_promotion').is(":checked"))
        {
            jQuery('.promotion-group').show();
        }
        else 
        {
            jQuery('.promotion-group').hide();
        }
    }
    
    var deleteProductImage = function(item)
    {
        jQuery(item).closest('tr').remove();
        initUpDown('tbImage');
    }
    
    var deleteProductVideo = function(item)
    {
        jQuery(item).closest('tr').remove();
        initUpDown('tbVideo');
    }
    
    var initUploadVideo = function()
    {
        $('#fetchButton').unbind('click');
        $('#fetchButton').click(function () {
            $('#fetchButton').spin('small');
            $("#videoForm .error-message").hide();

            mooButton.disableButton('fetchButton');

            mooAjax.post({
                url: mooConfig.url.base + "/stores/manager/products/video_validate",
                data: $("#createForm").serialize()
            }, function (data) {
                mooButton.enableButton('fetchButton');

                if (data) {
                    $("#fetchForm .error-message").html(JSON.parse(data).error);
                    $("#fetchForm .error-message").show();
                    $('#fetchButton').spin(false);
                } else {
                    mooAjax.post({
                        url: mooConfig.url.base + "/stores/manager/products/video_fetch",
                        data: $("#createForm").serialize()
                    }, function (data) {
                        mooButton.enableButton('fetchButton');

                        $("#fetchForm").slideUp();
                        $("#videoForm").html(data);
                    });
                }
            });
            return false;
        });
    }
    
    var initAfterFetchVideo = function()
    {
        $('#saveBtn').unbind('click');
        $('#saveBtn').click(function () {
            $(this).addClass('disabled');
            mooButton.disableButton('saveBtn');
            mooAjax.post({
                url : mooConfig.url.base + "/stores/manager/products/save_video/",
                data: $("#createForm").serialize()
            }, function(data){
                var json = $.parseJSON(data);

                if ( json.result == 1 ){
                    var item = jQuery(jQuery("#videoItemTemplate").html());
                    item.find('.video_image').attr('src', json.video.image_url);
                    item.find('.video_name').html(json.video.title);
                    item.find('.video_id').val(json.video.id);
                    jQuery("#tbVideo tbody").append(item);
                    initUpDown('tbVideo');
                    jQuery('#storeModal').modal('hide');
                }
                else{
                    mooButton.enableButton('saveBtn');
                    $("#videoForm .error-message").show();
                    $("#videoForm .error-message").html(json.message);
                    if ($('.spinner').length > 0){
                        $('.spinner').remove();
                    }
                }
            });
        });
    }
    
    function loadProductVideo()
    {
        var data = jQuery("#videoData").html().trim();
        data = jQuery.parseJSON(data);
        if(data.length > 0)
        {
            for(var i in data)
            {
                var video = data[i]['StoreProductVideo'];
                var item = jQuery(jQuery("#videoItemTemplate").html());
                item.find('.video_image').attr('src', video.image_url);
                item.find('.video_name').html(video.title);
                item.find('.product_video_id').val(video.id);
                item.find('.video_id').val(video.video_id);
                if(video.enable == 0)
                {
                    item.find('.enable_video').removeAttr('checked');
                }
                jQuery("#tbVideo tbody").append(item);
            }
            initUpDown('tbVideo');
        }
    }
    
    var loadVideoShortList = function(link)
    {
        if(typeof link == 'undefined')
        {
            link = mooConfig.url.base + "/stores/manager/products/select_video";
        }
        
        //except selected video
        var except_id = [];
        if(jQuery('#tbVideo .video_id').length > 0)
        {
            jQuery('#tbVideo .video_id').each(function(){
                except_id.push(jQuery(this).val());
            })
        }
        $.post(link, $("#videoShortListForm").serialize() + '&data[except_id]=' + except_id.join(), function(data){
           jQuery('#storeModal .modal-content').empty().append(data);
        });

    }
    
    /////////////////////////////////////////////manage order/////////////////////////////////////////////
    var initManageOrder = function()
    {
        //datetime picker
        initDatetimePicker('datetime_start', 'datetime_end');
        
        //change status
        jQuery(document).on('change', '.change_order_status', function(){
            var status = jQuery(this).val();
            jQuery.post(mooConfig.url.base + "/stores/manager/orders/change_order_status", 'order_id=' + jQuery(this).data('id') + '&status=' + status, function(data){
                var json = $.parseJSON(data);
                if(json.result == 0)
                {
                    jQuery(item).val(status);
                    mooAlert.alert(json.message);
                }
                else
                {
                    mooAlert.alert(json.message);
                } 
            });
        })
        
        //view detail
        jQuery(document).on('click', '.view_order_detail', function(){
            jQuery.post(mooConfig.url.base + "/stores/manager/orders/order_detail/" + jQuery(this).data('id'), '', function(data){
                jQuery('#storeModal .modal-content').empty().append(data); 
                jQuery('#storeModal').modal();
            });
        })
        
        //print order
        printOrder();
    }
    
    function printOrder()
    {
        jQuery(document).on('click', '.print_order', function(){
            window.open(mooConfig.url.base + "/stores/orders/print_order/" + jQuery(this).data('id'), '_blank', 'location=yes');
        })
    }
    
    /////////////////////////////////////////////create attribute/////////////////////////////////////////////
    initCreateAttribute = function()
    {
        //save attribute
        jQuery(document).on('click', '#btnSave', function(){
            saveAttribute();
        })
        
        jQuery(document).on('click', '#btnApply', function(){
            saveAttribute(1);
        })
    }
    
    var saveAttribute = function(apply)
    {
        mooButton.disableButton('btnSave');
        mooButton.disableButton('btnApply');
        mooButton.disableButton('btnCancel');
        $(".error-message").hide();
        $(".alert-success").hide();
        if(apply == 1)
        {
            jQuery('#save_type').val(1);
        }

        //save data
        $.post(mooConfig.url.base + "/stores/manager/attributes/save", $("#formAttribute").serialize(), function(data){
            var json = $.parseJSON(data);
            if(json.result == 0)
            {
                $(".error-message").show();
                $(".error-message").html(json.message);
                mooButton.enableButton('btnSave');
                mooButton.enableButton('btnApply');
                mooButton.enableButton('btnCancel');
            }
            else
            {
                if(mooConfig.isApp)
                {
                    if(apply == 1)
                    {
                        window.location = json.location + "?app_no_tab=1";
                    }
                    else
                    {
                        window.mobileAction.backAndRefesh();
                    }
                }
                else
                {
                    window.location = json.location;
                }
            } 
        });
    }
    
    /////////////////////////////////////////////create producer/////////////////////////////////////////////
    initCreateProducer = function()
    {
        //save attribute
        jQuery(document).on('click', '#btnSave', function(){
            saveProducer();
        })
        
        jQuery(document).on('click', '#btnApply', function(){
            saveProducer(1);
        })
    }
    
    var saveProducer = function(apply)
    {
        mooButton.disableButton('btnSave');
        mooButton.disableButton('btnApply');
        mooButton.disableButton('btnCancel');
        $(".error-message").hide();
        $(".alert-success").hide();
        if(apply == 1)
        {
            jQuery('#save_type').val(1);
        }

        //save data
        $.post(mooConfig.url.base + "/stores/manager/producers/save", $("#createForm").serialize(), function(data){
            var json = $.parseJSON(data);
            if(json.result == 0)
            {
                $(".error-message").show();
                $(".error-message").html(json.message);
                mooButton.enableButton('btnSave');
                mooButton.enableButton('btnApply');
                mooButton.enableButton('btnCancel');
            }
            else
            {
                if(mooConfig.isApp)
                {
                    if(apply == 1)
                    {
                        window.location = json.location + "?app_no_tab=1";
                    }
                    else
                    {
                        window.mobileAction.backAndRefesh();
                    }
                }
                else
                {
                    window.location = json.location;
                }
            } 
        });
    }
    
    /////////////////////////////////////////////create shipping/////////////////////////////////////////////
    initCreateShipping = function()
    {
        //save attribute
        jQuery(document).on('click', '#btnSave', function(){
            saveShipping();
        })
        
        jQuery(document).on('click', '#btnApply', function(){
            saveShipping(1);
        })
        
        //add zone
        if(jQuery('#zoneData').text().trim() != '')
        {
            addShippingZone(jQuery('#zoneData').text().trim());
        }
        jQuery(document).on('click', '#btnAddZone', function(){
            addShippingZone();
        })
        
        //remove zone
        jQuery(document).on('click', '.remove_zone', function(){
            jQuery(this).closest('.div-detail-row').remove();
            parseSelectedList('zoneDataTemplate', 'btnAddZone', 'zone_content', 'store_shipping_zone_id');
        })
        
        //list change
        jQuery(document).on('change', '#zone_content .store_shipping_zone_id', function(){
            parseSelectedList('zoneDataTemplate', 'btnAddZone', 'zone_content', 'store_shipping_zone_id');
        })
    }
    
    function addShippingZone(zoneData)
    {
        if(typeof zoneData != 'undefined' && zoneData != '')
        {
            zoneData = jQuery.parseJSON(zoneData);
            for(var i in zoneData)
            {
                var data = zoneData[i]['StoreShipping'];
                var new_item = jQuery(jQuery('#zoneDataTemplate').html());
                new_item.find('.store_shipping_zone_id').val(data.store_shipping_zone_id);
                new_item.find('.price').val(data.price);
                new_item.find('.weight').val(data.weight);
                if(data.enable == 1)
                {
                    new_item.find('.enable_zone').attr('checked', 'checked');
                }
                jQuery('#zone_content').append(new_item);
            }
        }
        else
        {
            var new_item = jQuery(jQuery('#zoneDataTemplate').html());
            parseAddNewList(new_item, 'zone_content', 'store_shipping_zone_id');
            jQuery('#zone_content').append(new_item);
        }
        parseSelectedList('zoneDataTemplate', 'btnAddZone', 'zone_content', 'store_shipping_zone_id');
    }
    
    function parseSelectedList(template, button, content, list)
    {
        var selected_items = [];
        jQuery('#' + content + ' .' + list).each(function(){
            selected_items.push(parseInt(jQuery(this).val()));
        })
        jQuery('#' + content + ' .' + list).each(function(){
            var item = jQuery(this);
            var select_item = parseInt(item.val());
            item.find('option').each(function(){
                var option_value = parseInt(jQuery(this).val());
                jQuery(this).show();
                if(selected_items.indexOf(option_value) > -1 && select_item != option_value)
                {
                    jQuery(this).hide();
                }
            })
        })
        
        //check total list
        var new_item = jQuery(jQuery('#' + template).html());
        if(new_item.find('.' + list + ' option').length == jQuery('#' + content + ' .' + list).length)
        {
            jQuery('#' + button).attr('disabled', 'disabled');
        }
        else
        {
            jQuery('#btnAddZone').removeAttr('disabled');
        }
    }
    
    function parseAddNewList(new_item, content, list)
    {
        var item = new_item.find('.' + list);
        var selected_items = [];
        jQuery('#' + content + ' .' + list).each(function(){
            selected_items.push(parseInt(jQuery(this).val()));
        })
        var first = 0;
        item.find('option').each(function(){
            var option_value = parseInt(jQuery(this).val());
            if(selected_items.indexOf(option_value) > -1)
            {
                jQuery(this).hide();
            }
            else if(first == 0)
            {
                first = 1;
                jQuery(this).attr("selected", "selected");
            }
        })
    }
    
    var saveShipping = function(apply)
    {
        mooButton.disableButton('btnSave');
        mooButton.disableButton('btnApply');
        mooButton.disableButton('btnCancel');
        $(".error-message").hide();
        $(".alert-success").hide();
        if(apply == 1)
        {
            jQuery('#save_type').val(1);
        }
        
        //parse enable
        jQuery('#zone_content .enable_zone').each(function(){
            if(jQuery(this).is(':checked'))
            {
                jQuery(this).parent().find('input[type=hidden]').attr('disabled', 'disabled');
            }
            else
            {
                jQuery(this).attr('disabled', 'disabled');
            }
        })

        //save data
        $.post(mooConfig.url.base + "/stores/manager/shippings/save", $("#createForm").serialize(), function(data){
            var json = $.parseJSON(data);
            if(json.result == 0)
            {
                $(".error-message").show();
                $(".error-message").html(json.message);
                mooButton.enableButton('btnSave');
                mooButton.enableButton('btnApply');
                mooButton.enableButton('btnCancel');
                jQuery('#zone_content .enable_zone').each(function(){
                    jQuery(this).parent().find('input[type=hidden]').removeAttr('disabled');
                    jQuery(this).removeAttr('disabled');
                })
            }
            else
            {
                if(mooConfig.isApp)
                {
                    if(apply == 1)
                    {
                        window.location = json.location + "?app_no_tab=1";
                    }
                    else
                    {
                        window.mobileAction.backAndRefesh();
                    }
                }
                else
                {
                    window.location = json.location;
                }
            } 
        });
    }
    
    /////////////////////////////////////////////create shipping zone/////////////////////////////////////////////
    initCreateShippingZone = function()
    {
        //save attribute
        jQuery(document).on('click', '#btnSave', function(){
            saveShippingZone();
        })
        
        jQuery(document).on('click', '#btnApply', function(){
            saveShippingZone(1);
        })
        
        //add location
        if(jQuery('#locationData').text().trim() != '')
        {
            addShippingZoneLocation(jQuery('#locationData').text().trim());
        }
        jQuery(document).on('click', '#btnAddLocation', function(){
            addShippingZoneLocation();
        })
        
        //remove location
        jQuery(document).on('click', '.remove_location', function(){
            jQuery(this).closest('.div-detail-row').remove();
            parseSelectedList('locationDataTemplate', 'btnAddLocation', 'location_content', 'country_id');
        })
        
        //list change
        jQuery(document).on('change', '#location_content .country_id', function(){
            parseSelectedList('locationDataTemplate', 'btnAddLocation', 'location_content', 'country_id');
        })
    }
    
    function addShippingZoneLocation(locationData)
    {
        if(typeof locationData != 'undefined' && locationData != '')
        {
            locationData = jQuery.parseJSON(locationData);
            for(var i in locationData)
            {
                var data = locationData[i];
                var new_item = jQuery(jQuery('#locationDataTemplate').html());
                new_item.find('.country_id').val(data.country_id);
                if(data.enable == 1)
                {
                    new_item.find('.enable_location').attr('checked', 'checked');
                }
                jQuery('#location_content').append(new_item);
            }
        }
        else
        {
            var new_item = jQuery(jQuery('#locationDataTemplate').html());
            parseAddNewList(new_item, 'location_content', 'country_id');
            jQuery('#location_content').append(new_item);
        }
        parseSelectedList('locationDataTemplate', 'btnAddLocation', 'location_content', 'country_id');
    }
    
    var saveShippingZone = function(apply)
    {
        mooButton.disableButton('btnSave');
        mooButton.disableButton('btnApply');
        mooButton.disableButton('btnCancel');
        $(".error-message").hide();
        $(".alert-success").hide();
        if(apply == 1)
        {
            jQuery('#save_type').val(1);
        }
        
        //parse enable
        jQuery('#location_content .enable_location').each(function(){
            if(jQuery(this).is(':checked'))
            {
                jQuery(this).parent().find('input[type=hidden]').attr('disabled', 'disabled');
            }
            else
            {
                jQuery(this).attr('disabled', 'disabled');
            }
        })

        //save data
        $.post(mooConfig.url.base + "/stores/manager/shipping_zones/save", $("#createForm").serialize(), function(data){
            var json = $.parseJSON(data);
            if(json.result == 0)
            {
                $(".error-message").show();
                $(".error-message").html(json.message);
                mooButton.enableButton('btnSave');
                mooButton.enableButton('btnApply');
                mooButton.enableButton('btnCancel');
                jQuery('#location_content .enable_zone').each(function(){
                    jQuery(this).parent().find('input[type=hidden]').removeAttr('disabled');
                    jQuery(this).removeAttr('disabled');
                })
            }
            else
            {
                if(mooConfig.isApp)
                {
                    if(apply == 1)
                    {
                        window.location = json.location + "?app_no_tab=1";
                    }
                    else
                    {
                        window.mobileAction.backAndRefesh();
                    }
                }
                else
                {
                    window.location = json.location;
                }
            } 
        });
    }
    
    /////////////////////////////////////////////create order/////////////////////////////////////////////
    var edit_product_id = '';
    initCreateOrder = function()
    {
        //save attribute
        jQuery(document).on('click', '#btnSave', function(){
            saveOrder();
        })
        
        jQuery(document).on('click', '#btnApply', function(){
            saveOrder(1);
        })
        
        //copy info
        jQuery(document).on('click', '.copy_order_btn', function(){
            var form = jQuery('#createForm');
            form.find('#shipping_email').val(form.find('#billing_email').val());
            form.find('#shipping_first_name').val(form.find('#billing_first_name').val());
            form.find('#shipping_last_name').val(form.find('#billing_last_name').val());
            form.find('#shipping_company').val(form.find('#billing_company').val());
            form.find('#shipping_phone').val(form.find('#billing_phone').val());
            form.find('#shipping_address').val(form.find('#billing_address').val());
            form.find('#shipping_country_id').val(form.find('#billing_country_id').val());
            form.find('#shipping_city').val(form.find('#billing_city').val());
            form.find('#shipping_postcode').val(form.find('#billing_postcode').val());
            initOrderShipping();
        })
        
        //valid quantity
        jQuery('#order-detail-form #quantity').on( "keydown", function(e) {
            var keypressed = null;
            if (window.event) {
                keypressed = window.event.keyCode; //IE
            }
            else {

                keypressed = e.which; //NON-IE, Standard
            }
            if (keypressed < 48 || keypressed > 57) {
                if (keypressed == 8 || keypressed == 127 || keypressed == 0) { return; }
                return false;
            }
        })
        
        //load product short list paging
        jQuery(document).on('click', '#product-short-list-paging li a', function(e){
            e.stopPropagation();
            e.preventDefault();
            loadProductShortList(jQuery(this).attr('href'));
        })
        
        //change payment
        changePayment();
        jQuery(document).on('change', '#payment', function(e){
            changePayment();
        })
        
        //show add product dialog
        jQuery(document).on('click', '.order_detail_dialog', function(e){
            orderDetailDialog(jQuery(this).data('id'));
        })
        
        //clear order detail
        jQuery(document).on('click', '.clear_order_detail', function(e){
            clearOrderDetail(jQuery(this).data('id'));
        })
        
        //calcualte price
        jQuery(document).on('change', '#order-detail-form #quantity', function(e){
            calculatePrice();
        })
        
        //addOrderDetail
        jQuery(document).on('click', '#add_order_detail', function(e){
            var form =jQuery('#order-detail-form');
            var product_id = form.find('#product_id').val();
            var product_code = form.find('#product_code').val();
            var product_name = form.find('#product_name').val();
            var price = form.find('#price').val();
            var quantity = form.find('#quantity').val();
            var amount = form.find('#amount').val();
            var weight = form.find('#weight').val();
            if(quantity < 1)
            {
                jQuery('#order_detail_modal').modal('hide');
                mooAlert.alert(mooPhrase.__("text_invalid_quantity"));
                jQuery(document).on("click", "#simple-modal .button-action.primary", function(){
                    jQuery('#order_detail_modal').modal('show');
                })
            }
            else if(product_id > 0)
            {
                jQuery('#order_detail_modal').modal('hide');
                loadDetailHtml(product_id, product_code, product_name, price, quantity, amount, weight);
                addToExceptProductList(product_id);
                removeFromExceptProductList(edit_product_id);
            }
            else 
            {
                jQuery('#order_detail_modal').modal('hide');
                mooAlert.alert(mooPhrase.__("text_please_select_product"));
                jQuery(document).on("click", "#simple-modal .button-action.primary", function(){
                    jQuery('#order_detail_modal').modal('show');
                })
            }
        })
        
        //load product short list
        jQuery(document).on('click', '.load_product_short_list', function(e){
            loadProductShortList();
        })
        
        //select product
        jQuery(document).on('click', '.select_product', function(e){
            var data = $(this).find('.product_list').data();
            loadOrderDetailFormData(data['id'], data['productCode'], data['name'], data['price']);
            jQuery('#order_detail_modal').modal('show');
            jQuery('#product_short_list').modal('hide');
        })
        
        //load product detail
        var products = jQuery('#data-products').html().trim();
        if(products != '')
        {
            products = jQuery.parseJSON(products);
            for(var i in products)
            {
                var item = products[i];
                loadDetailHtml(item.product_id, item.product_code, item.product_name, item.price, item.quantity, item.amount, item.weight)
            }
        }
        
        //shipping
        if(jQuery('#select_store_shipping_id').length > 0)
        {
            initOrderShipping();
            jQuery(document).on('change', '#shipping_country_id', function() {
                initOrderShipping();
            })
        }
    }
    
    function initOrderShipping(){
        jQuery.post(mooConfig.url.base + '/stores/manager/shippings/load_order_shippings/', 'country_id=' + jQuery('#shipping_country_id').val() + '&select_id=' + jQuery('#select_store_shipping_id').val(), function(data){
            jQuery('#order_shipping').html(data);
        })
    }
    
    function calculateShippingPrice()
    {
        jQuery('.shipping_price').each(function(){
            var price = parseFloat(jQuery(this).data('price'));
            var weight = parseFloat(jQuery(this).data('weight'));
            var key_name = jQuery(this).data('key');
            if(key_name == mooPhrase.__('STORE_SHIPPING_PER_ITEM'))
            {
                var total_quantity = 0;
                jQuery('.div-detail-row').each(function(){
                    total_quantity += parseInt(jQuery(this).find('.quantity').data('value'));
                })
                price = price * total_quantity;
            }
            if(key_name == mooPhrase.__('STORE_SHIPPING_WEIGHT'))
            {
                var total_weight = 0;
                jQuery('.div-detail-row').each(function(){
                    total_weight += parseInt(jQuery(this).find('.quantity').data('weight')) * parseInt(jQuery(this).find('.quantity').data('value'));
                })
                if(weight == 0 || weight > 0 && total_weight < weight)
                {
                    price = 0;
                }
            }
            jQuery(this).html(formatMoney(price));
        });
    }
    
    var formatMoney = function(money)
    {
        switch(currency_position)
        {
            case 'left':
                money = currency_symbol + money;
                break;
            case 'right':
                money = money + currency_symbol;
                break;
            default :
                money = currency_symbol + money;
        }
        return money;
    }
    
    var loadProductShortList = function(link)
    {
        jQuery('#order_detail_modal').modal('hide');
        jQuery('#product_short_list').modal('show');
        if(typeof link == 'undefined')
        {
            link = mooConfig.url.base + "/stores/manager/products/product_short_list";
        }
        $.post(link, $("#productShortListForm").serialize() + '&except_id=' + jQuery('#except_product_ids').val(), function(data){
           jQuery('#product_short_list .modal-content').empty().append(data);
        });

    }
    
    var changePayment = function()
    {
        if(jQuery('#payment option:selected').data('online') == 1)
        {
            jQuery('#payment_transaction_id').show();
        }
        else
        {
            jQuery('#payment_transaction_id').hide();
        }
    }
    
    var orderDetailDialog = function(product_id)
    {
        if(typeof product_id == 'undefined')
        {
            edit_product_id = '';
            loadOrderDetailFormData('', '', '', '');
        }
        else 
        {
            edit_product_id = product_id;
            var product = jQuery('#product' + product_id);
            var product_code = product.find('.product_code').data('value');
            var product_name = product.find('.product_name').data('value');
            var price = product.find('.price').data('value');
            var quantity = product.find('.quantity').data('value');
            loadOrderDetailFormData(product_id, product_code, product_name, price, quantity);
        }
        jQuery('#order_detail_modal').modal({
            backdrop: 'static'
        });
    }
    
    var loadOrderDetailFormData = function(product_id, product_code, product_name, price, quantity)
    {
        if(typeof quantity == 'undefined')
        {
            quantity = 1;
        }
        var form =jQuery('#order-detail-form');
        form.find('#product_id').val(product_id);
        form.find('#product_code').val(product_code);
        form.find('#product_name').val(product_name);
        form.find('#price').val(price);
        form.find('#quantity').val(quantity);
        calculatePrice();
    }
    
    var calculatePrice = function()
    {
        var form =jQuery('#order-detail-form');
        var price = form.find('#price').val();
        var quantity = form.find('#quantity').val();
        var amount = parseFloat(price) * parseInt(quantity);
        amount = isNaN(amount) ? 0 : amount;
        form.find('#amount').val(amount.toFixed(2));
    }
    
    var loadDetailHtml = function(product_id, product_code, product_name, price, quantity, amount, weight)
    {
        var item = jQuery(jQuery('#data-template').html());
        item.attr('id', item.data('value') + product_id);
        item.find('.product_code').attr('data-value', product_code).empty().append(product_code);
        item.find('.product_name').attr('data-value', product_name).empty().append(product_name);
        item.find('.price').attr('data-value', price).empty().append(price);
        item.find('.quantity').attr('data-value', quantity).empty().append(quantity);
        item.find('.amount').attr('data-value', amount).empty().append(amount);
        item.find('.quantity_value').val(quantity).attr('name', 'data[quantity][' + product_id + ']');
        item.find('.weight').val(weight);
        item.find('.product_id_value').val(product_id);
        item.find('.edit').attr('data-id', product_id);
        item.find('.delete').attr('data-id', product_id);
        if(edit_product_id > 0)
        {
            jQuery('#table-order-details #product' + edit_product_id).after(item).remove();
        }
        else
        {
            item = jQuery('#table-order-details').append(item);
        }
    }
    
    var clearOrderDetail = function(product_id)
    {
        mooConfirmBox(mooPhrase.__('are_you_sure_you_want_to_delete'), function(){
            jQuery('#product' + product_id).remove();
            removeFromExceptProductList(product_id);
        });
    }
    
    var addToExceptProductList = function(product_id)
    {
        var list = [];
        if(jQuery('#except_product_ids').val() != '')
        {
            list = jQuery('#except_product_ids').val().split(',');
        }
        list.push(product_id);
        jQuery('#except_product_ids').val(list.join(','));
    }

    var removeFromExceptProductList = function(product_id)
    {

        var list = jQuery('#except_product_ids').val().split(',');
        var index = list.indexOf(product_id.toString());
        if (index > -1) 
        {
            list.splice(index, 1);
        }
        jQuery('#except_product_ids').val(list.join(','));
    }

    var saveOrder = function(apply)
    {
        mooButton.disableButton('btnSave');
        mooButton.disableButton('btnApply');
        mooButton.disableButton('btnCancel');
        $(".error-message").hide();
        $(".alert-success").hide();
        if(apply == 1)
        {
            jQuery('#save_type').val(1);
        }

        //save data
        $.post(mooConfig.url.base + "/stores/manager/orders/save", $("#createForm").serialize(), function(data){
            var json = $.parseJSON(data);
            if(json.result == 0)
            {
                $(".error-message").show();
                $(".error-message").html(json.message);
                mooButton.enableButton('btnSave');
                mooButton.enableButton('btnApply');
                mooButton.enableButton('btnCancel');
            }
            else
            {
                if(mooConfig.isApp)
                {
                    if(apply == 1)
                    {
                        window.location = json.location + "?app_no_tab=1";
                    }
                    else
                    {
                        window.mobileAction.backAndRefesh();
                    }
                }
                else
                {
                    window.location = json.location;
                }
            }
        });
    }
    
    function isJson(str){
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
    
    return{
        initHelp: function(){
            initHelp();
        },
        initManagerMenu: function()
        {
            initManagerMenu();
        },
        initManage: function()
        {
            initManage();
        },
        initCreateAttribute :function(){
            initCreateAttribute();
        },
        initCreateProducer: function()
        {
            initCreateProducer();
        },
        initCreateProduct: function()
        {
            initCreateProduct();
        },
        initCreateOrder: function()
        {
            initCreateOrder();
        },
        initManageOrder : function()
        {
            initManageOrder();
        },
        loadDetailHtml: function(product_id, product_code, product_name, price, quantity, amount, weight)
        {
            loadDetailHtml(product_id, product_code, product_name, price, quantity, amount, weight);
        },
        /*initDatetimePicker : function()
        {
            initDatetimePicker();
        },*/
        loadProductAttributes : function(product_id, buy)
        {
        	loadProductAttributes(product_id, buy);
        },
        /*showAttributeDialog : function(buy, input_ids)
        {
        	showAttributeDialog(buy, input_ids);
        },
        saveProduct : function(apply)
        {
        	saveProduct(apply);
        },
        deleteProductImage : function(item)
        {
        	deleteProductImage(item);
        },*/
        initSelectAttributeDlg : function()
        {
            initSelectAttributeDlg();
        },
        initCreateShipping: function()
        {
            initCreateShipping();
        },
        initCreateShippingZone: function()
        {
            initCreateShippingZone();
        },
        calculateShippingPrice: function(){
            calculateShippingPrice();
        },
        initUploadVideo: function(){
            initUploadVideo();
        },
        initAfterFetchVideo: function(){
            initAfterFetchVideo();
        }
    }
}));