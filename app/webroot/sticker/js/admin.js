$.admin = {
    showLoading : function(wrapper)
    {
        var html = '<div class="loading_indicator"></div>';
        $(wrapper).append(html);
    },
    
    hideLoading: function(wrapper)
    {
        $(wrapper).find('.loading_indicator').remove();
    },
    
    action: function(id, task) 
    {
        $('input.multi_cb').removeAttr('checked');
        $('#cb' + id).trigger('click');
        if(task == 'delete')
        {
            if(confirm(mooPhrase.__('sticker_are_you_sure_you_want_to_delete')))
            {
                this.doSubmit(task);
            }
        }
        else if(task == 'create')
        {
            window.location = jQuery('#adminForm').attr('action') + task + '/' + id;
        }
        else
        {
            this.doSubmit(task);
        }
    },
    
    activeAll: function(task)
    {
        if(jQuery('input.multi_cb:checked').length < 1)
        {
            mooAlert(mooPhrase.__('sticker_you_must_select_at_least_an_item'));
        }
        else 
        {
            this.doSubmit(task);
        }
    },
    
    deleteAll: function(task)
    {
        if(jQuery('input.multi_cb:checked').length < 1)
        {
            mooAlert(mooPhrase.__('sticker_you_must_select_at_least_an_item'));
        }
        else if(confirm(mooPhrase.__('sticker_are_you_sure_you_want_to_delete'))) 
        {
            this.doSubmit(task);
        }
    },
    
    saveAll: function(task)
    {
        jQuery('.multi_cb').trigger('click');
        this.doSubmit(task);
    },

    doSubmit: function(task)
    {
        var action = jQuery('#adminForm').attr('action');
        jQuery('#adminForm').attr('action', action + task);
        jQuery('#adminForm').submit();
    },
    
    toggleCheckboxes: function (obj)
    {
        if (jQuery(obj).is(':checked'))
        {
            jQuery('.multi_cb').attr('checked', 'checked');
            jQuery('.multi_cb').parent('span').addClass('checked');
        }
        else
        {
            jQuery('.multi_cb').attr('checked', false);
            jQuery('.multi_cb').parent('span').removeClass('checked');
        }
    },
    
    initColorPicker(wrapper, wrapper_preview)
    {
        wrapper = $(wrapper);
        wrapper_preview = $(wrapper_preview);
        wrapper.ColorPicker({
            onSubmit: function (hsb, hex, rgb, el) {
                $(el).val(hex);
                $(el).ColorPickerHide();
                wrapper_preview.attr('style', 'background:#' + hex);
            },
            onBeforeShow: function () {
                $(this).ColorPickerSetColor(this.value);
                wrapper_preview.attr('style', 'background:#' + this.value);
            }
        })
        .bind('keyup', function () {
            $(this).ColorPickerSetColor(this.value);
            wrapper_preview.attr('style', 'background:#' + this.value);
        });
    },
    
    initTab(wrapper)
    {
        wrapper = $(wrapper);
        wrapper.tabs();
    },
    
    initUploader: function(wrapper, wrapper_value, wrapper_preview, extensions, link)
    {
        wrapper = $(wrapper);
        wrapper_value = $(wrapper_value);
        wrapper_preview = $(wrapper_preview);
        extensions = typeof extensions != 'undefined' ? extensions.split(',') : ['jpg','jpeg','png'];
        var uploader = new mooFileUploader.fineUploader({
            element: wrapper[0],
            multiple: false,
            text: {
                uploadButton: '<div class="upload-section"><i class="material-icons">camera_enhance</i></i>' + mooPhrase.__('sticker_click_here_to_upload') + '</div>',
                dropProcessing: ''
            },
            validation: {
                allowedExtensions : extensions,
                sizeLimit : mooConfig.sizeLimit
            },
            request: {
                endpoint: link
            },
            callbacks: {
                onComplete: function(id, fileName, response) {
                    if(response.result == 0)
                    {
                        mooAlert(response.message);
                    }
                    else 
                    {
                        wrapper_value.val(response.filename);
                        wrapper_preview.find('img').attr('src', response.url).show();
                    }
                }
            }
        });
    },
    
    initUpDown: function(wrapper_id)
    {
        jQuery(wrapper_id + ' tbody tr').find('.btn-up').show();
        jQuery(wrapper_id + ' tbody tr').find('.btn-down').show();
        jQuery(wrapper_id + ' tbody tr:first-child').find('.btn-up').hide();
        jQuery(wrapper_id + ' tbody tr:last-child').find('.btn-down').hide();
    },
    
    ///////////////////////////////////////category///////////////////////////////////////
    initCreateCategory: function()
    {
        //show color picker
        this.initColorPicker('#background_color', '#background_color_preview');
        
        //init uploader
        this.initUploader('#category_icon', '#icon', '#category_icon_preview', mooPhrase.__('STICKER_IMAGE_EXTENSION'), mooConfig.url.base + "/admin/sticker/categories/upload_icon/");
        
        //show language tab
        this.initTab('#lang-tabs');
        
        //save
        $(document).on('click', '#btnSave', function (e) {
            $.admin.saveCategory();
        })

        $(document).on('click', '#btnApply', function (e) {
            $.admin.saveCategory(1);
        })
    },
    
    saveCategory(apply)
    {
        jQuery(document).on("#createForm", "submit", function(e){
            e.preventDefault();
        })
        
        disableButton('btnSave');
        disableButton('btnApply');
        disableButton('btnCancel');
        $("#errorMessage").hide();
        if(apply == 1)
        {
            jQuery('#save_type').val(1);
        }

        //save data
        $.post(mooConfig.url.base + "/admin/sticker/categories/save", $("#createForm").serialize(), function(data){
            var json = $.parseJSON(data);
            if(json.result == 0)
            {
                $("#errorMessage").html(json.message).show();
                enableButton('btnSave');
                enableButton('btnApply');
                enableButton('btnCancel');
            }
            else
            {
                window.location = json.location;
            } 
        });
    },
    
    ///////////////////////////////////////sticker///////////////////////////////////////
    initCreateSticker: function()
    {
        //init uploader
        var uploader = new mooFileUploader.fineUploader({
            element: jQuery('#sticker_image')[0],
            multiple: true,
            text: {
                uploadButton: '<div class="upload-section"><i class="material-icons">camera_enhance</i></i>' + mooPhrase.__('sticker_click_here_to_upload') + '</div>'
            },
            validation: {
                allowedExtensions :mooPhrase.__('STICKER_IMAGE_EXTENSION').split(','),
                sizeLimit : mooConfig.sizeLimit
            },
            request: {
                endpoint: mooConfig.url.base + "/admin/sticker/stickers/upload_image/"
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
                            var item = jQuery(jQuery("#imageTemplate").html());
                            item.find('img').attr('src', response.url);
                            item.find('.image_filename').val(response.filename);
                            item.find('.image_url').val(response.url);
                            item.find('.image_width').val(response.width);
                            item.find('.image_height').val(response.height);
                            jQuery("#tbImage tbody").append(item);
                        }
                        $.admin.initUpDown('#tbImage');
                        $.admin.initStickerAnimation();
                    }
                }
            }
        });
        
        //init uploader
        this.initUploader('#sticker_icon', '#icon', '#sticker_icon_preview', mooPhrase.__('STICKER_IMAGE_EXTENSION'), mooConfig.url.base + "/admin/sticker/stickers/upload_icon/");
        
        $(document).on('click', '#tbImage .btn-up', function(){
            var prev = jQuery(this).closest('tr').prev();
            jQuery(this).closest('tr').insertBefore(prev);
            $.admin.initUpDown('#tbImage');
        })

        $(document).on('click', '#tbImage .btn-down', function(){
            var next = jQuery(this).closest('tr').next();
            jQuery(this).closest('tr').insertAfter(next);
            $.admin.initUpDown('#tbImage');
        })
        
        //delete image 
        $(document).on('click', '.delete_image', function (e) {
            $(this).closest('tr').remove();
        })
        
        //show for edit
        this.addStickerImageItem();
        
        //show language tab
        this.initTab('#lang-tabs');
        
        //save
        $(document).on('click', '#btnSave', function (e) {
            $.admin.saveSticker();
        })

        $(document).on('click', '#btnApply', function (e) {
            $.admin.saveSticker(1);
        })
        
        //dynamic update block and quantity for image preview
        $(document).on('change', '.image_block', function (e) {
            var item = $(this);
            item.closest('tr').find('.sticker_preview').attr('data-block', item.val());
        })
        
        $(document).on('change', '.image_quantity', function (e) {
            var item = $(this);
            item.closest('tr').find('.sticker_preview').attr('data-quantity', item.val());
        })
        
        //animation
        var stickerInterval;
        $(document).on('mouseenter', '.sticker_animation', function (e) {
            var item = $(this);
            if(parseInt(item.attr('data-block')) == 1 && parseInt(item.attr('data-quantity')) == 1){
                return;
            }
            var interval = parseInt($('#animation_interval').val()) > 0 ? parseInt($('#animation_interval').val()) : parseInt(mooPhrase.__('sticker_animation_interval'));
            var backgroundPos = item.css('backgroundPosition').split(" "); 
            var backgroundSize = item.css('backgroundSize').split(" ");
            var xPos = parseFloat(backgroundPos[0]),
                yPos = parseFloat(backgroundPos[1]);
            var sizeW = parseFloat(backgroundSize[0]), 
                sizeH = parseFloat(backgroundSize[1]);
            var offset = ((sizeW / parseInt(item.attr('data-block'))) * 100).toFixed(1);
            offset = Math.ceil(offset) / 100;
            var max_quantity = parseInt(item.attr('data-quantity')) - 1;
            var quantity = 0;
            stickerInterval = setInterval(function() {
                quantity += 1;
                xPos -= offset;
                if(xPos <= -sizeW){
                    xPos = 0;
                    yPos -= offset;
                }
                if(yPos < -sizeH || quantity > max_quantity){
                    quantity = 0;
                    xPos = 0;
                    yPos = 0;
                }
                item.css('backgroundPosition', xPos + 'px ' + yPos + 'px');
            }, interval);
        })
        
        $(document).on('mouseout', '.sticker_animation', function (e) {
            var item = $(this);
            if(parseInt(item.attr('data-block')) == 1 && parseInt(item.attr('data-quantity')) == 1){
                return;
            }
            clearInterval(stickerInterval);
            item.css('backgroundPosition', '0px 0px');
        })
    },
    
    addStickerImageItem()
    {
        var data = jQuery("#imageDataTemplate").html();
        data = JSON.parse(data);
        if(data.length > 0)
        {
            for(var i = 0; i < data.length; i++)
            {
                var item = data[i].StickerImage;
                var template = jQuery(jQuery("#imageTemplate").html());
                template.find('.image_id').val(item.id);
                template.find('.image_filename').val(item.filename);
                template.find('.image_url').val(item.url);
                template.find('.image_width').val(item.width);
                template.find('.image_height').val(item.height);
                template.find('.image_category_id').val(item.sticker_category_id);
                template.find('.image_block').val(item.block);
                template.find('.image_quantity').val(item.quantity);
                if(item.enabled)
                {
                    template.find('.image_enable').attr('checked', 'checked');
                }
                else
                {
                    template.find('.image_enable').removeAttr('checked');
                }
                jQuery("#tbImage tbody").append(template);
            }
            $.admin.initUpDown('#tbImage');
            $.admin.initStickerAnimation();
        }
    },
    
    initStickerAnimation()
    {
        jQuery("#tbImage tbody tr").each(function(){
            var item = $(this);
            var image_url = item.find('.image_url').val();
            var image_width = item.find('.image_width').val();
            var image_height = item.find('.image_height').val();
            var image_block = item.find('.image_block').val();
            var image_quantity = item.find('.image_quantity').val();
            var animation_style = "background-image: url('" + image_url + "'); background-size: " + image_width + "px " + image_height + "px; cursor: pointer; height: 80px; width: 80px; background-position: 0 0; image-rendering: -webkit-optimize-contrast;background-repeat: no-repeat;";
            item.find('.sticker_preview').attr('style', animation_style).attr('data-block', image_block);
            item.find('.sticker_preview').attr('style', animation_style).attr('data-quantity', image_quantity);
        });
    },
    
    saveSticker(apply)
    {
        jQuery(document).on("#createForm", "submit", function(e){
            e.preventDefault();
        })
        
        disableButton('btnSave');
        disableButton('btnApply');
        disableButton('btnCancel');
        $("#errorMessage").hide();
        if(apply == 1)
        {
            jQuery('#save_type').val(1);
        }

        //set enable image
        var count = -1;
        jQuery('#tbImage tbody .image_enable').each(function(){
            count++;
            if(jQuery(this).is(':checked'))
            {
                jQuery(this).val(count);
            }
        })

        //save data
        $.post(mooConfig.url.base + "/admin/sticker/stickers/save", $("#createForm").serialize(), function(data){
            var json = $.parseJSON(data);
            if(json.result == 0)
            {
                $("#errorMessage").html(json.message).show();
                enableButton('btnSave');
                enableButton('btnApply');
                enableButton('btnCancel');
            }
            else
            {
                window.location = json.location;
            } 
        });
    },
};