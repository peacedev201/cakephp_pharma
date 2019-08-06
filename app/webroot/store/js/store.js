/*
 * STORE
 */
(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery', 'mooFileUploader', 'mooBehavior', 'mooAlert', 'mooPhrase', 'mooAjax', 'mooUser', 'mooGlobal', 'mooButton', 'mooTooltip'], factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS-like
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals (root is window)
        root.store_store = factory();
    }
}(this, function ($, mooFileUploader, mooBehavior, mooAlert, mooPhrase, mooAjax, mooUser, mooGlobal, mooButton, mooTooltip) {
	function showLoading(wrapper){
        jQuery(wrapper).append('<div class="item_loading"></div>');
    }
    
    function hideLoading(wrapper){
        jQuery(wrapper + ' .item_loading').remove();
    }
	
    var initShortcut = function ()
    {
        if (jQuery('body.store-plugin').length == 0)
        {
            jQuery.post(mooConfig.url.base + "/store/popups/load_shortcut", '', function (data) {
                jQuery('body').append(data);
                if(!mooConfig.isApp)
                {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            });
        }
    }

    var loadCartBalloon = function ()
    {
        if (jQuery('#cart_balloon').length > 0) {
            jQuery.post(mooConfig.url.base + "/store/popups/load_cart_balloon", '', function (data) {
                jQuery('#cart_balloon').html(data);
            });
        }
    }

    var initGlobal = function ()
    {
        $('#shareFeedModal').on('show.bs.modal', function (e) {
            jQuery('#storeModal').modal('hide');
        })

        //quick view
        jQuery(document).on('click', '.quickview', function (e) {
            quickView(jQuery(this).data('link'));
        })

        //add to cart
        jQuery(document).on('click', '.add_to_cat', function (e) {
            addToCart(jQuery(this), jQuery(this).data('id'), jQuery(this).data('wrapper_id'), jQuery(this).data('quickview'));
        })

        //add to wishlist
        jQuery(document).on('click', '.add_to_wishlist', function (e) {
            addToWishlist(jQuery(this), jQuery(this).data('id'), jQuery(this).data('quickview'));
        })

        //like product
        jQuery(document).on('click', '.like_product', function (e) {
            likeProduct(jQuery(this), jQuery(this).data('id'));
        })

        //email friend
        jQuery(document).on('click', '.email_friend', function (e) {
            emailFriendDialog(jQuery(this).data('id'));
        })

        jQuery(document).on('click', '#emailFriendButton', function (e) {
            mooButton.disableButton('emailFriendButton');
            mooButton.disableButton('cancelEmailFriendButton');
            jQuery.post(mooConfig.url.base + "/stores/product_reports/email_friend", jQuery('#emailFriendForm').serialize(), function (data) {
                var json = jQuery.parseJSON(data);
                if (json.result == 1)
                {
                    jQuery('#storeModal').modal('hide');
                    mooAlert.alert(json.message);
                } else
                {
                    jQuery('#emailFriendMessage').empty().append(json.message).show();
                    mooButton.enableButton('emailFriendButton');
                    mooButton.enableButton('cancelEmailFriendButton');
                }
            });
        })

        //report
        jQuery(document).on('click', '.report_product', function (e) {
            reportDialog(jQuery(this).data('id'));
        })

        //ask seller
        jQuery(document).on('click', '.ask_seller', function (e) {
            askSellerDialog(jQuery(this).data('userid'));
        })

        //share
        jQuery(document).on('click', '.shareFeedBtn', function (e) {
            jQuery('#storeModal').modal('hide');
        })

        jQuery(document).on('click', '#reportButton', function (e) {
            mooButton.disableButton('reportButton');
            mooButton.disableButton('cancelReportButton');
            jQuery.post(mooConfig.url.base + "/stores/product_reports/report_product", jQuery('#reportForm').serialize(), function (data) {
                var json = jQuery.parseJSON(data);
                if (json.result == 1)
                {
                    jQuery('#storeModal').modal('hide');
                    mooAlert.alert(json.message);
                } else
                {
                    jQuery('#reportMessage').empty().append(json.message).show();
                    mooButton.enableButton('reportButton');
                    mooButton.enableButton('cancelReportButton');
                }
            });
        })

        //valid quantity
        jQuery(document).on('keyup', '.quantity_cart', function (e) {
            return validQuantity(e);
        })
        jQuery(document).on('change', '.quantity_cart', function (e) {
            return validQuantity(e);
        })

        if (jQuery('li#wishlist_active').length > 0)
        {
            jQuery('li#wishlist_active a').trigger('click');
        }
        if (jQuery('li#order_active').length > 0)
        {
            jQuery('li#order_active a').trigger('click');
        }

        //tooltip
        if(!mooConfig.isApp)
        {
            jQuery('.social-icons a').each(function () {
                genietip(jQuery(this), 'title');
            });
            jQuery('[data-toggle="tooltip"]').each(function () {
                jQuery(this).attr('data-toggle', '');
            });
        }

        //load app menu 
        jQuery(document).on('click', '#btn_app_categories', function (e) {
            jQuery('#storeModal').modal();
            jQuery.post(mooConfig.url.base + "/store/stores/load_category_dialog", '', function (data) {
                jQuery('#storeModal .modal-content').empty().append(data);
                //initCategoryEffect();
            });
        })
    }

    var genietip = function (element, content) {
        if (content == 'html') {
            var tipText = element.html();
        } else {
            var tipText = element.attr('title');
        }
        element.on('mouseover', function () {

            if (jQuery('.genietip').length == 0) {
                element.before('<span class="genietip">' + tipText + '</span>');
                var tipWidth = jQuery('.genietip').outerWidth();
                var tipPush = -(tipWidth / 2 - element.outerWidth() / 2);
                jQuery('.genietip').css('margin-left', tipPush);
            }
        });
        element.on('mouseleave', function () {
            jQuery('.genietip').remove();
        });
    }

    var addToCart = function (item, id, quantity_input_id, quickview, url)
    {
        jQuery(item).addClass('loading');
        var quantity = 1;
        if (typeof quantity_input_id != 'undefined' && jQuery('#' + quantity_input_id).length > 0)
        {
            quantity = jQuery('#' + quantity_input_id).val();
        }
        var extend = '';
        if (jQuery('#pro-attr-form').length > 0)
        {
            extend = '&' + jQuery('#pro-attr-form').serialize();
        }
        if (typeof url == 'undefined')
        {
            url = mooConfig.url.base;
        }
        if (typeof quickview != 'undefined' && quickview == 1)
        {
            extend += '&quickview=1';
        }
        jQuery.post(url + '/stores/carts/add_to_cart/', 'id=' + id + '&quantity=' + quantity + extend, function (data) {
            if (isJson(data))
            {
                data = jQuery.parseJSON(data);
                if (data.result == 0 || data.result == 'login')
                {
                    $.fn.SimpleModal({
                        btn_ok: mooPhrase.__('btn_ok'),
                        model: 'modal',
                        title: mooPhrase.__('warning'),
                        contents: data.message
                    }).showModal();
                    jQuery(item).removeClass('loading');
                } else if (typeof quickview != 'undefined' && quickview == 1)
                {
                    jQuery('#quickview-message').empty().append(data.message).show();
                    setTimeout(function () {
                        $('#quickview-message').fadeOut('fast');
                    }, 4000);
                    jQuery(item).removeClass('loading');
                    if (jQuery(item).data('type') != mooPhrase.__('STORE_PRODUCT_TYPE_REGULAR'))
                    {
                        jQuery('.cart_product_' + id).html(mooPhrase.__('text_added_to_cart')).removeClass('add_to_cat').addClass('active');
                    }
                }
            } else
            {
                jQuery('#storeModal').modal();
                jQuery('#storeModal .modal-content').empty().append(data);
                jQuery(item).removeClass('loading');
                if (jQuery(item).data('type') != mooPhrase.__('STORE_PRODUCT_TYPE_REGULAR'))
                {
                    jQuery('.cart_product_' + id).html(mooPhrase.__('text_added_to_cart')).removeClass('add_to_cat').addClass('active');
                }
            }

            //load cart balloon
            loadCartBalloon();
        });
    }

    var addToWishlist = function (item, product_id, quickview)
    {
        var action = jQuery(item).data('action');
        jQuery.post(mooConfig.url.base + '/stores/wishlists/add_to_wishlist/', 'product_id=' + product_id + '&action=' + action, function (data) {
            data = jQuery.parseJSON(data);
            if (data.result == 0)
            {
                if (data.redirect)
                {
                    window.location = data.redirect;
                } else
                {
                    if (typeof quickview != 'undefined' && quickview == 1)
                    {
                        jQuery('#quickview-message').empty().append(data.message).show();
                        setTimeout(function () {
                            $('#quickview-message').fadeOut('fast');
                        }, 4000);
                    } else
                    {
                        jQuery('#simpleModal').remove();
                        $.fn.SimpleModal({
                            btn_ok: mooPhrase.__('btn_ok'),
                            model: 'modal',
                            title: mooPhrase.__('warning'),
                            contents: data.message
                        }).showModal();
                    }
                }
            } else
            {
                var action = jQuery(item).data('action');
                var new_item = jQuery(item).clone();
                jQuery(item).parent().find('.tooltip').remove();
                if (action == 1)
                {
                    new_item.addClass('active');
                    new_item.attr('data-action', 0);
                    new_item.attr('data-original-title', mooPhrase.__('text_remove_from_wishlist'));
                } else
                {
                    new_item.removeClass('active');
                    new_item.attr('data-action', 1);
                    new_item.attr('data-original-title', mooPhrase.__('text_add_to_wishlist'));
                }
                if (jQuery('.product_wishlist_' + product_id).length > 0)
                {
                    jQuery('.product_wishlist_' + product_id).after(new_item).remove();
                } else
                {
                    jQuery(item).after(new_item).remove();
                }
                if(!mooConfig.isApp)
                {
                    $('[data-toggle="tooltip"]').tooltip();
                }

                if (typeof quickview != 'undefined' && quickview == 1)
                {
                    jQuery('#quickview-message').empty().append(data.message).show();
                    setTimeout(function () {
                        $('#quickview-message').fadeOut('fast');
                    }, 4000);
                } else
                {
                    mooAlert.alert(data.message);
                }

            }
        });
    }

    var likeProduct = function (item, id)
    {
        likeActivity('Store_Store_Product', id, 1);
        jQuery(document).ajaxComplete(function (event, xhr, settings) {
            if (typeof (mooViewer) != 'undefined')
            {
                var action = jQuery(item).data('action');
                var new_item = jQuery(item).clone();
                jQuery('.tooltip').remove();
                if (action == 1)
                {
                    new_item.addClass('active');
                    new_item.attr('data-action', 0);
                    new_item.attr('data-original-title', mooPhrase.__('text_you_liked_this_product'));
                } else
                {
                    new_item.removeClass('active');
                    new_item.attr('data-action', 1);
                    new_item.attr('data-original-title', mooPhrase.__('text_like_this_product'));
                }

                if (jQuery('.product_like_' + id).length > 0)
                {
                    jQuery('.product_like_' + id).after(new_item).remove();
                } else
                {
                    jQuery(item).after(new_item).remove();
                }
                if(!mooConfig.isApp)
                {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            } else
            {
                jQuery('#storeModal').modal('hide');
            }
            jQuery(document).off('ajaxComplete')
        })
    }

    var likeActivity = function (item_type, id, thumb_up) {

        var type;

        if (item_type == 'photo_comment') {
            type = 'comment';
        } else {
            type = item_type;
        }

        $.post(mooConfig.url.base + '/likes/ajax_add/' + type + '/' + id + '/' + thumb_up, {noCache: 1}, function (data) {
            try
            {
                var res = $.parseJSON(data);
                $('#' + item_type + '_like_' + id).html(parseInt(res.like_count));
                $('#' + item_type + '_dislike_' + id).html(parseInt(res.dislike_count));
                if (item_type == 'comment') {
                    $('#photo_comment' + '_like_' + id).html(parseInt(res.like_count));
                    $('#photo_comment' + '_dislike_' + id).html(parseInt(res.dislike_count));
                }

                if (thumb_up)
                {
                    $('#' + item_type + '_l_' + id).toggleClass('active');
                    $('#' + item_type + '_d_' + id).removeClass('active');
                    if (item_type == 'comment') {
                        $('#photo_comment' + '_l_' + id).toggleClass('active');
                        $('#photo_comment' + '_d_' + id).removeClass('active');
                    }
                } else
                {
                    $('#' + item_type + '_d_' + id).toggleClass('active');
                    $('#' + item_type + '_l_' + id).removeClass('active');
                    if (item_type == 'comment') {
                        $('#photo_comment' + '_d_' + id).toggleClass('active');
                        $('#photo_comment' + '_l_' + id).removeClass('active');
                    }
                }
            } catch (err)
            {
                mooUser.validateUser();
            }
        });
    }

    var quickView = function (href)
    {
        jQuery('#storeModal').modal();
        jQuery.post(href, '', function (data) {
            jQuery('#storeModal .modal-content').empty().append(data);
            initReviewStar();
            if(!mooConfig.isApp)
            {
                $('[data-toggle="tooltip"]').tooltip();
            }
        });
    }

    var emailFriendDialog = function (product_id)
    {
        jQuery('#storeModal').modal({
            'backdrop': 'static'
        });
        //save data
        jQuery.post(mooConfig.url.base + "/stores/product_reports/email_friend_dialog", 'product_id=' + product_id, function (data) {
            if (isJson(data))
            {
                data = jQuery.parseJSON(data);
                if (data.result == 0)
                {
                    $.fn.SimpleModal({
                        btn_ok: mooPhrase.__('btn_ok'),
                        model: 'modal',
                        title: mooPhrase.__('warning'),
                        contents: data.message
                    }).showModal();
                    jQuery('#storeModal').modal('hide');
                }
            } else
            {
                jQuery('#storeModal .modal-content').empty().append(data);
            }
        });
    }

    var reportDialog = function (product_id)
    {
        jQuery('#storeModal').modal();
        //save data
        jQuery.post(mooConfig.url.base + "/stores/product_reports/report_dialog", 'product_id=' + product_id, function (data) {
            if (isJson(data))
            {
                data = jQuery.parseJSON(data);
                if (data.result == 0)
                {
                    $.fn.SimpleModal({
                        btn_ok: mooPhrase.__('btn_ok'),
                        model: 'modal',
                        title: mooPhrase.__('warning'),
                        contents: data.message
                    }).showModal();
                    jQuery('#storeModal').modal('hide');
                }
            } else
            {
                jQuery('#storeModal .modal-content').empty().append(data);
            }
        });
    }

    function askSellerDialog(user_id)
    {
        jQuery('#storeModal').modal({
            backdrop: 'static'
        });
        //save data
        jQuery.post(mooConfig.url.base + "/conversations/ajax_send/" + user_id, '', function (data) {
            jQuery('#storeModal .modal-content').empty().append(data);
			mooGlobal.initConversationSendBtn();
        });

        jQuery(document).ajaxComplete(function (event, xhr, settings) {
            if (settings.url === mooConfig.url.base + "/conversations/ajax_doSend")
            {
                var json = jQuery.parseJSON(xhr.responseText);
                if (json.result == 1)
                {
                    jQuery('#storeModal .modal-content').empty();
                    jQuery('#storeModal').modal('hide');
                }
            }
        });
    }

    var validQuantity = function (e)
    {
        var keypressed = null;
        if (window.event) {
            keypressed = window.event.keyCode; //IE
        } else {
            keypressed = e.which; //NON-IE, Standard
        }
        if (keypressed < 48 || keypressed > 57) {
            if (keypressed == 8 || keypressed == 127 || keypressed == 0) {
                return;
            }
            return false;
        }
    }

    var initQuickview = function ()
    {
        (function ($) {
            // This is the connector function.
            // It connects one item from the navigation carousel to one item from the
            // stage carousel.
            // The default behaviour is, to connect items with the same index from both
            // carousels. This might _not_ work with circular carousels!
            var connector = function (itemNavigation, carouselStage) {
                return carouselStage.jcarousel('items').eq(itemNavigation.index());
            };

            $(function () {
                // Setup the carousels. Adjust the options for both carousels here.
                var carouselStage = $('.carousel-stage').jcarousel();
                var carouselNavigation = $('.carousel-navigation').jcarousel();

                // We loop through the items of the navigation carousel and set it up
                // as a control for an item from the stage carousel.
                carouselNavigation.jcarousel('items').each(function () {
                    var item = $(this);

                    // This is where we actually connect to items.
                    var target = connector(item, carouselStage);

                    item
                            .on('jcarouselcontrol:active', function () {
                                carouselNavigation.jcarousel('scrollIntoView', this);
                                item.addClass('active');
                            })
                            .on('jcarouselcontrol:inactive', function () {
                                item.removeClass('active');
                            })
                            .jcarouselControl({
                                target: target,
                                carousel: carouselStage
                            });
                });

                // Setup controls for the stage carousel
                $('.prev-stage')
                        .on('jcarouselcontrol:inactive', function () {
                            $(this).addClass('inactive');
                        })
                        .on('jcarouselcontrol:active', function () {
                            $(this).removeClass('inactive');
                        })
                        .jcarouselControl({
                            target: '-=1'
                        });

                $('.next-stage')
                        .on('jcarouselcontrol:inactive', function () {
                            $(this).addClass('inactive');
                        })
                        .on('jcarouselcontrol:active', function () {
                            $(this).removeClass('inactive');
                        })
                        .jcarouselControl({
                            target: '+=1'
                        });

                // Setup controls for the navigation carousel
                $('.prev-navigation')
                        .on('jcarouselcontrol:inactive', function () {
                            $(this).addClass('inactive');
                        })
                        .on('jcarouselcontrol:active', function () {
                            $(this).removeClass('inactive');
                        })
                        .jcarouselControl({
                            target: '-=1'
                        });

                $('.next-navigation')
                        .on('jcarouselcontrol:inactive', function () {
                            $(this).addClass('inactive');
                        })
                        .on('jcarouselcontrol:active', function () {
                            $(this).removeClass('inactive');
                        })
                        .jcarouselControl({
                            target: '+=1'
                        });
            });
        })(jQuery);

        //change height
        jQuery(window).bind('load resize', function () {
            var imgHeight = jQuery('.thumbnails_carousel img').height();
            jQuery('.thumbnails_carousel').css('max-height', imgHeight);
            jQuery('.caroufredsel_wrapper').css('max-height', imgHeight);
            jQuery('.thumbnails_carousel_list').css('max-height', imgHeight);
        });
    }

    var initStoreList = function () {
        //load store
        loadStoreList();
        jQuery(document).on('click', 'ul.page-numbers li a', function (e) {
            loadStoreList(jQuery(this).attr('href'));
            return false;
        })

        //search store
        jQuery(document).on('click', '#btn_search_store', function (e) {
            e.preventDefault();
            jQuery('.form_search_store').addClass('hidden_search_form');
            jQuery(this).closest('.form_search_store').removeClass('hidden_search_form');
            loadStoreList();
        })
    }

    var initProductList = function (business_id)
    {
        //load product
        loadProductList('', business_id);
        jQuery(document).on('click', 'ul.page-numbers li a', function (e) {
            loadProductList(jQuery(this).attr('href'), business_id);
            return false;
        })

        //product view mode (grid - list)
        jQuery('.view-mode').each(function () {
            jQuery(this).find('.grid').on("click", function (event) {
                event.preventDefault();
                jQuery('#archive-product .view-mode').find('.grid').addClass('active');
                jQuery('#archive-product .view-mode').find('.list').removeClass('active');
                jQuery('#archive-product .shop-products').removeClass('list-view');
                jQuery('#archive-product .shop-products').addClass('grid-view');
                jQuery('#archive-product .list-col4').removeClass('col-xs-12 col-sm-4');
                jQuery('#archive-product .list-col8').removeClass('col-xs-12 col-sm-8');
            });
            jQuery(this).find('.list').on("click", function (event) {
                event.preventDefault();
                jQuery('#archive-product .view-mode').find('.list').addClass('active');
                jQuery('#archive-product .view-mode').find('.grid').removeClass('active');
                jQuery('#archive-product .shop-products').addClass('list-view');
                jQuery('#archive-product .shop-products').removeClass('grid-view');
                jQuery('#archive-product .list-col4').addClass('col-xs-12 col-sm-4');
                jQuery('#archive-product .list-col8').addClass('col-xs-12 col-sm-8');
            });
        });

        jQuery(window).resize(function () {
            changeToListView();
        })
    }

    var initSuggestSearchCategory = function ()
    {
        jQuery('.form_search_product .suggest_category').each(function () {
            var item = jQuery(this);
            var item_parent = item.closest('.form_search_product');
            jQuery(this).autocomplete({
                source: function (request, response) {
                    jQuery.post(mooConfig.url.base + '/stores/store_categories/suggest_category/', 'keyword=' + item.val(), function (data) {
                        if (data != 'null')
                        {
                            response(jQuery.parseJSON(data));
                        } else
                        {
                            item_parent.find('#search_category_id').val('');
                        }
                    });
                },
                minLength: 2,
                messages: {
                    noResults: '',
                    results: function () {}
                },
                select: function (event, ui) {
                    item.val(ui.item.label);
                    item_parent.find('#search_category_id').val(ui.item.value);
                    return false;
                },
            }).data("ui-autocomplete")._renderItem = function (ul, item) {
                var term = this.term;
                //var regex = new RegExp("\\S*" + $.ui.autocomplete.escapeRegex(term) + "\\S*", "gi");
                var regex = new RegExp($.ui.autocomplete.escapeRegex(term), "gi");
                var label = item.label.replace(regex, '<b>$&</b>');
                return $('<li id="cat_suggest_' + item.value + '"></li>').append(label).appendTo(ul);
            };
            ;
        });
    }

    var initCreateStore = function ()
    {
        //create store
        jQuery(document).on('click', '#createStoreButton', function (e) {
            mooButton.disableButton('createStoreButton');
            mooButton.disableButton('cancelStoreButton');
            jQuery(".error-message").hide();
            jQuery(".alert-success").hide();

            //set data for editor
            for (i = 0; i < tinyMCE.editors.length; i++)
            {
                var content = tinyMCE.editors[i].getContent();
                jQuery('#' + tinyMCE.editors[i].id).val(content);
            }

            //save data
            jQuery.post(mooConfig.url.base + "/stores/save", jQuery("#createForm").serialize(), function (data) {
                var json = $.parseJSON(data);
                if (json.result == 0)
                {
                    jQuery(".error-message").html(json.message).show();
                    mooButton.enableButton('createStoreButton');
                    mooButton.enableButton('cancelStoreButton');
                } else
                {
                    if(mooConfig.isApp)
                    {
                        window.mobileAction.backAndRefesh();
                    }
                    else
                    {
                        window.location = json.redirect;
                    }
                }
            });
        })

        //select payment
        selectPayment();
        jQuery(document).on('click', '.select_payment', function (e) {
            selectPayment();
        })

        //uploader
        var uploader = new mooFileUploader.fineUploader({
            element: $('#store_image')[0],
            autoUpload: true,
            text: {
                uploadButton: '<div class="upload-section"><i class="material-icons">photo_camera</i>' + mooPhrase.__('drag_or_click_here_to_upload_photo') + '</div>'
            },
            validation: {
                allowedExtensions: mooConfig.photoExt,
                sizeLimit: mooConfig.sizeLimit
            },
            request: {
                endpoint: mooConfig.url.base + "/stores/upload_image"
            },
            callbacks: {
                onError: mooGlobal.errorHandler,
                onComplete: function (id, fileName, response) {
                    jQuery("#store_image_value").val(response.filename);
                    jQuery("#store_image_preview img").attr("src", response.url).show();
                }
            }
        });
        
        //check business page
        jQuery(document).on('change', '#business_id', function(){
            var business_id = jQuery(this).val();
            var store_id = jQuery('#store_id').val();
            jQuery.post(mooConfig.url.base + "/stores/check_link_business_page/" + business_id, 'store_id=' + store_id, function(data){
                if(isJson(data))
                {
                    data = jQuery.parseJSON(data);
                    mooAlert.alert(data.message);
                    jQuery('#business_id').val('');
                }
            });
        });
    }

    function selectPayment()
    {
        var is_online = 0;
        jQuery('.select_payment').each(function () {
            if (jQuery(this).is(':checked') && jQuery(this).data('is_online') == 1)
            {
                is_online = 1;
            }
        })

        if (is_online == 1)
        {
            jQuery('.online_info').find('input').removeAttr('disabled');
            jQuery('.online_info').show();
        } else
        {
            jQuery('.online_info').find('input').attr('disabled', 'disabled');
            jQuery('.online_info').hide();
        }
    }

    var loadStoreList = function (link)
    {
        showLoading("#store-list");
        if (typeof link == 'undefined')
        {
            link = mooConfig.url.base + "/stores/load_store_list/";
        }

        var search_store_params = jQuery('.form_search_store:not(.hidden_search_form)').serialize();
        $.post(link, search_store_params, function (data) {
            hideLoading("#store-list");
            if (isJson(data))
            {
                data = jQuery.parseJSON(data);
                mooAlert.alert(data.message);
            } 
            else
            {
                jQuery('#store-list').empty().append(data);
                jQuery('#ul.page-numbers li a').attr('href', '#');
                jQuery('#top-paginator-counter').empty().append(jQuery('#bottom-paginator-counter').text());
                jQuery('#top-paginator').empty().append(jQuery('#bottom-paginator').clone().removeAttr('id'));
            }
        });
    }

    var loadProductList = function (link, business_id)
    {
        showLoading("#product-list");
        if (typeof link == 'undefined' || link == '')
        {
            link = mooConfig.url.base + "/stores/products/load_product_list/";
        }

        var search_product_params = jQuery('.form_search_product:not(.hidden_search_form)').serialize();
        var store_id = jQuery('#store_id').val();
        var view = 'grid';
        if (jQuery('.view-mode .list').hasClass('active'))
        {
            view = 'list';
        }
        
        if (typeof business_id != 'undefined' && parseInt(business_id) > 0)
        {
            search_product_params += '&business_id=' + business_id;
        }
        
        if(mooConfig.isApp)
        {
            search_product_params += '&app_no_tab=1';
        }

        $.get(link, search_product_params + '&view=' + view + '&store_id=' + store_id, function (data) {
            hideLoading("#product-list");
            if (isJson(data))
            {
                data = jQuery.parseJSON(data);
                mooAlert.alert(data.message);
            } 
            else
            {
                jQuery('#product-list').empty().append(data);
                jQuery('#ul.page-numbers li a').attr('href', '#');
                if(!mooConfig.isApp)
                {
                    $('[data-toggle="tooltip"]').tooltip();
                }
                jQuery('#top-paginator-counter').empty().append(jQuery('#bottom-paginator-counter').text());
                jQuery('#top-paginator').empty().append(jQuery('#bottom-paginator').clone().removeAttr('id'));
            }
            if (jQuery('.closeButton button').length > 0)
            {
                jQuery('.closeButton button').trigger('click');
            }
            changeToListView();

            //in case modal
            jQuery('#storeModal').modal('hide');
            initReviewStar();
            
            if(!mooConfig.isApp)
            {
                jQuery('.list_option_button').dropdown();
            }
        });
        
        if(mooConfig.isApp)
        {
            jQuery('#storeModal').on('click', '.btn_view_cart', function(){
                jQuery('#storeModal').modal('hide');
            })
        }
    }

    var initCategoryEffect = function ()
    {
        var accordionsMenu = $('.cd-accordion-menu');

        if (accordionsMenu.length > 0) {
            accordionsMenu.each(function () {
                var accordion = $(this);
                //detect change in the input[type="checkbox"] value
                accordion.on('change', 'input[type="checkbox"]', function () {
                    var checkbox = $(this);
                    if (checkbox.prop('checked'))
                    {
                        checkbox.siblings('label').find('.cat-icon-add').hide();
                        checkbox.siblings('label').find('.cat-icon-remove').show();
                        checkbox.siblings('ul').attr('style', 'display:none;').slideDown(300);
                    } else
                    {
                        checkbox.siblings('label').find('.cat-icon-add').show();
                        checkbox.siblings('label').find('.cat-icon-remove').hide();
                        checkbox.siblings('ul').attr('style', 'display:block;').slideUp(300);
                    }
                });
                accordion.find('input[type="checkbox"]:checked').each(function () {
                    $(this).parents('li').each(function () {
                        $("#" + $(this).data('group')).attr('checked', 'checked');
                    })
                })
                accordion.find('input[type="checkbox"]').each(function () {
                    var checkbox = $(this);
                    if (checkbox.prop('checked'))
                    {
                        checkbox.siblings('label').find('.cat-icon-add').hide();
                        checkbox.siblings('label').find('.cat-icon-remove').show();
                        checkbox.siblings('ul').attr('style', 'display:none;').slideDown(300);
                    }
                });
            });
        }
    }

    var initReviewStar = function () {
        var input = jQuery('input.rating'), count = Object.keys(input).length;
        if (count > 0) {
            input.rating('refresh', {
                disabled: true,
                showClear: false,
                showCaption: false,
                max: 5,
                size: 'xs'
            });
        }
    }

    var changeToListView = function ()
    {
        if (jQuery(window).width() <= 992)
        {
            jQuery('.view-mode .list').trigger('click');
        }
    }

    var isJson = function (str) {
        try
        {
            JSON.parse(str);
        } catch (e)
        {
            return false;
        }
        return true;
    }

    var mooConfirmBox = function (msg, callback)
    {
        setTimeout(function () {
            // Set title
            $($('#portlet-config  .modal-header .modal-title')[0]).html(mooPhrase.__('please_confirm'));
            // Set content
            $($('#portlet-config  .modal-body')[0]).html(msg);
            // OK callback, remove all events bound to this element
            $('#portlet-config  .modal-footer .ok').off("click").click(function () {
                callback();
                $('#portlet-config').modal('hide');
            });
            $('#portlet-config').modal('show');
        }, 1);
    }

    var initProductDetail = function (allow_comment)
    {
        jQuery('#postComment').autogrow();
        jQuery('#postComment').css('height', '60px');
        if (allow_comment == 0)
        {
            jQuery('#commentForm').remove();
            jQuery('#comments li .comment-option').remove();
        }
        initReviewStar();

        //change height
        jQuery(window).bind('load resize', function () {
            var imgHeight = jQuery('.thumbnails_carousel img').height();
            jQuery('.thumbnails_carousel').css('max-height', imgHeight);
            jQuery('.caroufredsel_wrapper').css('max-height', imgHeight);
            jQuery('.thumbnails_carousel_list').css('max-height', imgHeight);
        });

        //change attribute price
        jQuery(document).on('change', '.product_attribute', function () {
            jQuery.post(mooConfig.url.base + '/stores/attributes/load_price/', jQuery('#pro-attr-form').serialize(), function (data) {
                jQuery('#total_price').html(data);
            });
        })

        //buy featured product
        jQuery(document).on('click', '.buy_featured_product', function () {
            var item = jQuery(this);
            mooConfirmBox(jQuery('#buy_feature_product_confirm').html(), function () {
                window.location = mooConfig.url.base + "/stores/manager/store_packages/buy_featured_product/" + item.data('id');
            })
        })

        jQuery('#storeModal').on('hidden.bs.modal', function () {
            jQuery('#storeModal .modal-body').empty();
        })

        //zoom
        initProductZoom();

        //product review
        initReviews()
    }

    var loadProductDescription = function (product_id, review_id)
    {
        //tab
        var default_tab = jQuery('#default_tab_value').val();
        jQuery("#tabs").tabs({
            active: default_tab
        });

        jQuery.post(mooConfig.url.base + '/stores/store_reviews/load_product_reviews/' + product_id + '/' + review_id, '', function (data) {
            jQuery('#review_content').empty().append(data);
            initReviewStar();
            initReviewPhotoPopup();
            mooTooltip.init();
            mooBehavior.initMoreResults();
        });
    }

    function initProductZoom()
    {
        jQuery(document).ready(function (i) {
            $('#carousel').flexslider({
                animation: "slide",
                controlNav: false,
                animationLoop: false,
                slideshow: false,
                itemWidth: 73,
                itemMargin: 5,

            });
            $('.cloud-zoom, .cloud-zoom-gallery').CloudZoom();

            //zoom image
            jQuery('.zoom_in_marker').on("click", function () {
                jQuery.fancybox({
                    href: jQuery('.cloud-zoom').attr('href'),
                    openEffect: 'elastic',
                    closeEffect: 'elastic'
                });
            });
        });
    }

    var initRelatedProducts = function ()
    {
        jQuery('.related .shop-products').slick({
            infinite: false,
            slidesToShow: 3,
            slidesToScroll: 1,
            speed: 1000,
            easing: 'linear',
            autoplaySpeed: 3000,
            responsive: [{
                    breakpoint: 1200,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 1
                    }
                }, {
                    breakpoint: 960,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 1
                    }
                }, {
                    breakpoint: 760,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1
                    }
                }, {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }]
        });
    }

    var printOrder = function ()
    {
        jQuery(document).on('click', '.print_order', function () {
            window.open(mooConfig.url.base + "/stores/orders/print_order/" + jQuery(this).data('id'), '_blank', 'location=yes');
        })
    }

    var doRating = function (product_id, rating)
    {
        jQuery.post(mooConfig.url.base + '/stores/products/add_rating/', 'product_id=' + product_id + '&rating=' + rating, function (data) {
            data = jQuery.parseJSON(data);
            if (data.result == 0)
            {
                mooAlert.alert(data.message);
            } else
            {
                mooAlert.alert(data.message);
                jQuery('#my_rating').val(rating);
                $('#my_rating').rating('refresh', {
                    disabled: true,
                    showClear: false,
                    showCaption: false,
                    stars: 5
                });
                //jQuery('#my_rating').attr('readonly', 'readonly');
                //jQuery.store.initReviewStar();
            }
        });
    }

    ////////////////////////////////////////reviews////////////////////////////////////////
    var initReviews = function ()
    {
        initReviewStar();

        //review dialog
        jQuery(document).on('click', '.btn_write_review', function () {
            reviewDialog(jQuery(this).data('product_id'), '', '', jQuery(this).data('view_detail'), jQuery(this).data('my_review'));
        })

        //edit review dialog
        jQuery(document).on('click', '.edit_review', function () {
            reviewDialog(jQuery(this).data('product_id'), jQuery(this).data('id'), jQuery(this).data('parent_id'), jQuery(this).data('view_detail'), jQuery(this).data('my_review'));
        })

        //delete review
        jQuery(document).on('click', '.delete_review', function () {
            deleteReview(jQuery(this).data('review_id'), jQuery(this).data('parent_id'));
        })

        //save review
        doReview();

        //report review
        jQuery(document).on('click', '#reportReviewButton', function () {
            mooButton.disableButton('reportReviewButton');
            mooButton.disableButton('cancelReportReviewButton');
            jQuery.post(mooConfig.url.base + '/stores/store_reviews/do_report/', jQuery('#reportReviewForm').serialize(), function (data) {
                var json = jQuery.parseJSON(data);
                mooButton.enableButton('reportReviewButton');
                mooButton.enableButton('cancelReportReviewButton');
                if (json.result == 0)
                {
                    jQuery('#reportReviewMessage').html(json.message).show();
                } else
                {
                    jQuery('#storeModal').modal('hide');
                    mooAlert.alert(json.message);
                }
            })
        })

        //review_useful
        jQuery(document).on('click', '.review_useful', function () {
            var item = jQuery(this);
            var review_id = item.data('review_id');
            jQuery.post(mooConfig.url.base + '/stores/store_reviews/useful/' + review_id, '', function (data) {
                var json = jQuery.parseJSON(data);
                if (json.result == 0)
                {
                    mooAlert.alert(json.message);
                } else
                {
                    jQuery('#useful_count_' + review_id).empty().append(json.total);
                    if (item.hasClass('set_useful'))
                    {
                        item.removeClass('set_useful');
                    } else
                    {
                        item.addClass('set_useful');
                    }
                }
            })
        })

        //delete review
        jQuery(document).on('click', '.delete_review', function () {
            deleteReview(jQuery(this).data('review_id'), jQuery(this).data('parent_id'));
        })

        //reply dialog
        jQuery(document).on('click', '.btn_reply', function () {
            reviewDialog(jQuery(this).data('product_id'), '', jQuery(this).data('id'));
        })
        
        //report dialog
        jQuery(document).on('click', '.store_review_report', function () {
            jQuery.get(mooConfig.url.base + '/stores/store_reviews/report/' + jQuery(this).data('id'), function (data) {
                jQuery('#storeModal .modal-content').empty().append(data);
                jQuery('#storeModal').modal({
                    backdrop: 'static'
                });
            })
        })
    }

    var initReviewPhotoPopup = function ()
    {
        if ($('.bus_review_photo a').length) {
            $('.bus_review_photo a').magnificPopup({
                type: 'image',
                gallery: {enabled: false},
                zoom: {
                    enabled: true,
                    opener: function (openerElement) {
                        return openerElement;
                    }
                }
            });
        }
    }

    var reviewDialog = function (product_id, review_id, parent_id, view_detail, my_review)
    {
        if (typeof review_id == 'undefined' || review_id == 0)
        {
            review_id = '';
        }
        if (typeof view_detail == 'undefined')
        {
            view_detail = '';
        }
        if (typeof my_review == 'undefined')
        {
            my_review = '';
        }
        jQuery.post(mooConfig.url.base + '/stores/store_reviews/review_dialog/', 'product_id=' + product_id + '&review_id=' + review_id + '&parent_id=' + parent_id + '&view_detail=' + view_detail + '&my_review=' + my_review, function (data) {
            jQuery('#storeModal .modal-content').empty().append(data);
            jQuery('#storeModal').modal({
                backdrop: 'static'
            });
            initReviewPhotoUploader();

            //init star
            var input = jQuery('input.rating'), count = Object.keys(input).length;
            if (count > 0) {
                input.rating('refresh', {
                    showClear: false,
                    showCaption: false,
                    max: 5,
                    size: 'md',
                    step: 1
                });
            }

            //review emotion
            jQuery('#product_user_review').on('rating.change', function (event, value, caption, target) {
                jQuery('#reviewForm .review_star #rating').val(value)
                jQuery('#default_emotion').val(jQuery(caption).text());
                jQuery('#emotion').val(jQuery(caption).text());

            });
        });
    }

    function deleteReview(review_id, parent_id, redirect)
    {
        mooConfirmBox(mooPhrase.__('product_text_confirm_remove_review'), function () {
            if (typeof review_id == 'undefined' || review_id == 0)
            {
                review_id = '';
            }
            jQuery.post(mooConfig.url.base + '/stores/store_reviews/delete_review/' + review_id, '', function (data) {
                var json = jQuery.parseJSON(data);
                if (json.result == 0)
                {
                    mooAlert.alert(json.message);
                } else
                {
                    if (typeof redirect != 'undefined' && redirect == 1 && json.redirect)
                    {
                        window.location = json.redirect;
                    } else
                    {
                        jQuery('#itemreview_' + review_id).remove();
                        jQuery('#itemreply_' + parent_id + '_' + review_id).remove();
                        jQuery('.btnReview').show();
                        jQuery('#btnReply_' + parent_id).show();
                        jQuery('#messageReviewed').hide();
                        if (parent_id == 0)
                        {
                            //update counter on tab
                            updateTabCounter('review_count', '-');
                        }
                        mooAlert.alert(json.message);
                        if (jQuery('#my_review_content').length > 0 && jQuery('#my_review_content li').length == 0 && jQuery('#my_review_content .view-more').length == 0)
                        {
                            jQuery('#my_review_content').before(mooPhrase.__('product_text_no_reviews')).remove();
                        }
                    }
                }
            });
        })
    }

    function doReview()
    {
        //remove_review_image
        jQuery(document).on('click', '.remove_review_image', function () {
            var image_id = jQuery(this).data('image_id');
            jQuery('#attach' + image_id).remove();
            var ids = jQuery('#photo_review_delete_id').val();
            if (ids != '')
            {
                ids = ids.split(',');
            } else
            {
                ids = [];
            }
            if (jQuery.inArray(image_id, ids) == -1)
            {
                ids.push(image_id)
            }
            jQuery('#photo_review_delete_id').val(ids.join());
        })

        jQuery(document).on('click', '#reviewButton', function () {
            mooButton.disableButton('reviewButton');
            mooButton.disableButton('cancelReviewButton');
            var product_id = jQuery('#reviewForm').find('#store_product_id').val();
            var review_id = jQuery('#reviewForm').find('#review_id').val();
            var parent_id = jQuery('#reviewForm').find('#parent_id').val();
            var review_item = jQuery('#itemreview_' + review_id);
            var reply_item = jQuery('#itemreply_' + parent_id + '_' + review_id);
            jQuery.post(mooConfig.url.base + '/stores/store_reviews/do_review/', jQuery('#reviewForm').serialize(), function (data) {
                if (isJson(data))
                {
                    var json = jQuery.parseJSON(data);
                    if (json.location)
                    {
                        window.location = json.location;
                    } else
                    {
                        jQuery('#reviewMessage').empty().append(json.message).show();
                        mooButton.enableButton('reviewButton');
                        mooButton.enableButton('cancelReviewButton');
                    }
                } else
                {
                    if (parent_id > 0)
                    {
                        if (reply_item.length > 0)
                        {
                            reply_item.after(data).remove();
                        } else
                        {
                            jQuery('#reply_content' + parent_id).prepend(data);
                        }
                    } else
                    {
                        if (review_item.length > 0)
                        {
                            review_item.after(data).remove();
                        } else
                        {
                            jQuery('#review_content').prepend(data);
                            jQuery('#my_review_content').prepend(data);
                        }
                    }

                    jQuery('#storeModal').modal('hide');
                    jQuery('.btnReview').hide();
                    jQuery('.no_results').remove();
                    initReviewPhotoPopup();
                    mooTooltip.init();
                    loadProductTotalReview(product_id);
                    if (parent_id == 0)
                    {
                        jQuery('#messageReviewed').show();
                        initReviewStar();
                    } else
                    {
                        jQuery('#btnReply_' + parent_id).hide();
                    }

                    //update counter on tab
                    if (review_id == '' && parent_id == 0)
                    {
                        updateTabCounter('review_count', '+');
                    }
                }
            });
        })
    }

    function loadProductTotalReview(product_id)
    {
        jQuery.post(mooConfig.url.base + '/stores/store_reviews/total_product_review/' + product_id, function (data) {
            jQuery('.comment-form-rating').html(data);
            initReviewStar();
        })
    }

    var initReviewPhotoUploader = function (type, target_id)
    {
        //init uploader
        if ($('#product_uploader').length > 0) {
            var uploader = new mooFileUploader.fineUploader({
                element: $('#product_uploader')[0],
                autoUpload: false,
                text: {
                    uploadButton: '<div class="upload-section"><i class="material-icons">photo_camera</i>' + mooPhrase.__('drag_or_click_here_to_upload_photo') + '</div>'
                },
                validation: {
                    allowedExtensions: ['jpg', 'jpeg', 'gif', 'png'],
                },
                request: {
                    endpoint: mooConfig.url.base + "/photo/photo_upload/album/" + type + "/" + target_id
                },
                callbacks: {
                    onError: mooGlobal.errorHandler,
                    onComplete: function (id, fileName, response) {
                        if (response.filename)
                        {
                            if ($('#attachments').length > 0)
                            {
                                var attachs = $('#attachments').val();
                                if (attachs == '')
                                {
                                    $('#attachments').val(response.photo);
                                } else
                                {
                                    $('#attachments').val(attachs + ',' + response.photo);
                                }
                            }
                            var item = jQuery('#photo_content').html();
                            item = jQuery(item);
                            var photo = mooConfig.url.base + '/uploads/tmp/' + response.filename;
                            item.find('.albums_photo_edit').attr('style', 'background-image:url(' + photo + ')');
                            item.find('.photo_filename').val(response.photo);
                            jQuery('#wrap_photo_content').append(item);
                        } else if (response.result == 0)
                        {
                            mooAlert.alert(response.message);
                        }
                    }
                }
            });

            $('#triggerUpload').click(function () {
                uploader.uploadStoredFiles();
            });
        }

        jQuery(document).on('click', '.photo_edit_checkbox', function () {
            var id = jQuery(this).data('id');
            var photos = jQuery('#photo_delete_id').val();
            if (photos != '')
            {
                photos = photos.split(',');
            } else
            {
                photos = [];
            }
            if (jQuery.inArray(id, photos) == -1)
            {
                photos.push(id);
            }
            jQuery('#photo_delete_id').val(photos.join());
            jQuery(this).closest('li').remove();
        })
    }

    var updateTabCounter = function (id, char)
    {
        var count = parseInt(jQuery('#' + id).html());
        if (char == '-')
        {
            var result = count - 1;
        } else
        {
            var result = count + 1;
        }
        jQuery('#' + id).html(result);
    }

    ////////////////////////////////////////cart////////////////////////////////////////
    var initCart = function (load_cart)
    {
        //load cart by store
        if (load_cart == 1)
        {
            loadCartByStore();
        }
        jQuery(document).on('change', '#store_id', function () {
            loadCartByStore();
        })

        jQuery(document).on('change', '.product-quantity .qty', function () {
            calculateCartPrice();
        })

        //remove product
        jQuery(document).on('click', '.remove_cart_product', function () {
            var store_id = jQuery(this).data('store_id');
            var cart_id = jQuery(this).data('cart_id');
            mooConfirmBox(mooPhrase.__('are_you_sure_you_want_to_delete'), function () {
                jQuery.post(mooConfig.url.base + '/stores/carts/remove_cart_item/', 'store_id=' + store_id + '&id=' + cart_id, function (data) {
                    data = jQuery.parseJSON(data);
                    if (data.success == 0)
                    {
                        mooAlert.alert(data.msg);
                    } else
                    {
                        jQuery('#' + cart_id).remove();
                        jQuery('.store_area').each(function () {
                            if (jQuery(this).find('.cart_item').length == 0)
                            {
                                jQuery(this).remove();
                            }
                        })
                        if (jQuery('#cart_content').find('.store_area').length == 0)
                        {
                            jQuery('#table-cart #cart_content').hide();
                            jQuery('#table-cart .data-empty').show();
                        }
                        calculateCartPrice();
                    }
                });
            });
        })

        //clear cart
        jQuery(document).on('click', '#clear_all_cart', function () {
            var store_id = jQuery(this).data('store_id');
            mooConfirmBox(mooPhrase.__('text_confirm_clear_products'), function () {
                jQuery.post(mooConfig.url.base + '/stores/carts/clear_cart/', 'store_id=' + store_id, function (data) {
                    data = jQuery.parseJSON(data);
                    if (data.success == 0)
                    {
                        mooAlert.alert(data.message);
                    } else
                    {
                        jQuery('#table-cart #cart_content').remove();
                        jQuery('.cart-actions').remove();
                        jQuery('#wrapper_cart_stores').remove();
                        jQuery('#table-cart .data-empty').show();

                        //load cart balloon
                        loadCartBalloon();
                    }
                });
            })
        })

        jQuery(document).on('click', '#update_cart', function () {
            jQuery.post(mooConfig.url.base + '/stores/carts/update_cart/', jQuery('#cartForm').serialize(), function (data) {
                data = jQuery.parseJSON(data);
                if (data.success == 0)
                {
                    mooAlert.alert(data.message);
                } else
                {
                    mooAlert.alert(data.message);
                }

                //load cart balloon
                loadCartBalloon();
            });
        })
    }

    var loadCartByStore = function ()
    {
        var store_id = jQuery('#store_id').length > 0 ? jQuery('#store_id').val() : "";
        jQuery.post(mooConfig.url.base + '/stores/carts/load_cart_by_store/', 'store_id=' + store_id + '&warning_store=' + jQuery('#warning_store').val(), function (data) {
            if (data != '')
            {
                jQuery('#cartForm .data-empty').hide();
                jQuery('#cart_content').empty().append(data).show();
                //jQuery('#store-name').empty().append(jQuery('#store_id option:selected').text());
                currency_position = jQuery('#cart_currency_position').val();
                currency_symbol = jQuery('#cart_currency_symbol').val();
                calculateCartPrice();
            } else
            {
                jQuery('#cart_content').empty().hide();
                jQuery('#cartForm .data-empty').show();
            }
        });
    }

    var calculateCartPrice = function ()
    {
        var subtotal = total = 0;
        jQuery('.product-price .amount').each(function () {
            var id = jQuery(this).data('id');
            var price = jQuery(this).data('price');
            var quantity = jQuery('#quantity' + id).val();
            var subtotal_item = (parseFloat(price) * parseInt(quantity)).toFixed(2);
            subtotal += parseFloat(subtotal_item);
            subtotal_item = formatMoney(subtotal_item);
            jQuery('#subtotal' + id).empty().append(subtotal_item);
        })
        subtotal = subtotal.toFixed(2);
        subtotal = formatMoney(subtotal);
        jQuery('#subtotal').empty().append(subtotal);
        jQuery('#total').empty().append(subtotal);
    }

    var formatMoney = function (amount)
    {
        amount = parseFloat(amount);
        amount = amount.toFixed(2);
        switch (currency_position)
        {
            case 'left':
                money = currency_symbol + amount;
                break;
            case 'right':
                money = amount + currency_symbol;
                break;
            default :
                money = currency_symbol + amount;
        }
        return formatCredit(amount, money);
    }

    function formatCredit(amount, normal_money)
    {
        if (jQuery('#allow_credit').val() == 1)
        {
            var credit = amount;
            switch (jQuery('#setting_show_money_type').val())
            {
                case '3':
                    credit = exchangeToCredit(credit);
                    return normal_money + '<br/>' + credit + ' ' + mooPhrase.__('text_credits');
                case '1':
                    return normal_money;
                case '2':
                    credit = exchangeToCredit(credit);
                    return credit + ' ' + mooPhrase.__('text_credits');
            }
        }
        return normal_money;
    }

    function exchangeToCredit(amount)
    {
        return amount * mooPhrase.__('setting_credit_currency_exchange');
    }

    ////////////////////////////////////////checkout////////////////////////////////////////
    var initCheckout = function ()
    {
        currency_position = jQuery('#cart_currency_position').val();
        currency_symbol = jQuery('#cart_currency_symbol').val();

        //show shipping info
        jQuery(document).on('click', '#ship-to-different-address-checkbox', function () {
            if (jQuery(this).is(':checked'))
            {
                jQuery('.shipping_address').show();
                jQuery('#shipping_country_id').trigger("change");
            } else
            {
                jQuery('.shipping_address').hide();
                jQuery('#billing_country_id').trigger("change");
            }
        })

        //do checkout
        jQuery(document).on('click', '#place_order', function () {
            var item = jQuery(this);
            item.addClass('loading').prop('disabled', true);
            item.after('<i class="fa fa-spinner fa-spin"></i>');
            jQuery.post(mooConfig.url.base + '/stores/orders/do_checkout/', jQuery('#checkoutForm').serialize(), function (data) {
                data = jQuery.parseJSON(data);
                if (data.result == 0)
                {
                    jQuery('.store_plugin-error').empty().append(data.message).show();
                    jQuery('html,body').animate({
                        scrollTop: jQuery('.store_plugin-error').offset().top - 50
                    });
                    item.removeClass('loading').removeAttr('disabled');
                    item.parent().find('.fa-spinner').remove();
                } else
                {
                    if(mooConfig.isApp)
                    {
                        window.location = data.url + "?app_no_tab=1";
                    }
                    else
                    {
                        window.location = data.url;
                    }
                }
            });
        })
        
        if(mooConfig.isApp)
        {
            //window.mobileAction.backAndRefesh();
        }
    }

    var initOrderShipping = function () {
        //shipping
        jQuery(document).on('change', '#billing_country_id, #shipping_country_id', function () {
            var country_id = jQuery('#billing_country_id').val();
            if (jQuery('#ship-to-different-address-checkbox').is(':checked'))
            {
                country_id = jQuery('#shipping_country_id').val();
            }
            jQuery.post(mooConfig.url.base + '/stores/shippings/load_order_shippings/', 'country_id=' + country_id, function (data) {
                var data = jQuery.parseJSON(data);
                for (var store_id in data) {
                    resetOrderTotal(store_id);
                    jQuery('#shipping_' + store_id).empty();
                    var shippings = data[store_id];
                    if (shippings.length > 0) {
                        for (var key in shippings) {
                            var shipping = shippings[key];
                            var template = jQuery(jQuery("#shippingTemplate").html());
                            shipping.price = calculateShippingPrice(store_id, shipping.key_name, shipping.price, shipping.weight);
                            template.find('.shipping_name input').attr('id', 'shipping_method_' + shipping.id).attr('data-price', shipping.price).attr('data-store', store_id).attr('name', 'store_shipping_id[' + store_id + ']').val(shipping.id);
                            template.find('.shipping_name label').attr('for', 'shipping_method_' + shipping.id).html(shipping.name);
                            template.find('.shipping_price').html(formatMoney(shipping.price));
                            jQuery('#shipping_' + store_id).append(template);
                        }
                    }
                    jQuery('#shipping_' + store_id + ' .shipping_method:first').attr('checked', 'checked');
                }
                calculateOrderTotalByShipping();
            })
        })

        //select shipping
        jQuery(document).on('change', '.shipping_method', function () {
            calculateOrderTotalByShipping();
        })
    }

    function calculateShippingPrice(store_id, key_name, price, weight)
    {
        price = parseFloat(price);
        weight = parseFloat(weight);
        if (key_name == mooPhrase.__('STORE_SHIPPING_PER_ITEM'))
        {
            var total_quantity = 0;
            jQuery('.store_product_' + store_id).each(function () {
                total_quantity += jQuery(this).data('quantity');
            })
            price = price * total_quantity;
        }
        if (key_name == mooPhrase.__('STORE_SHIPPING_WEIGHT'))
        {
            var total_weight = 0;
            jQuery('.store_product_' + store_id).each(function () {
                total_weight += parseFloat(jQuery(this).data('weight')) * parseInt(jQuery(this).data('quantity'));
            })
            if (weight == 0 || total_weight == 0/* || weight > 0 && total_weight < weight*/)
            {
                price = 0;
            }
        }
        return price;
    }

    function calculateOrderTotalByShipping() {
        var total = parseFloat(jQuery('#total_amount_value').val());
        jQuery('td[id*=shipping_fee_]').html(formatMoney(0));
        jQuery('.shipping_method:checked').each(function () {
            var store_id = jQuery(this).data('store');
            var price = parseFloat(jQuery(this).data('price'));
            var sub_total = parseFloat(jQuery('#sub_total_value_' + store_id).val());
            sub_total = sub_total + price;
            total += price;
            jQuery('#shipping_fee_' + store_id).html(formatMoney(price));
            //jQuery('#sub_total_' + store_id).html(formatMoney(sub_total));
        })
        jQuery('#total_amount').html(formatMoney(total));
    }

    function resetOrderTotal(store_id) {
        var total = parseFloat(jQuery('#total_amount_value').val());
        var sub_total = parseFloat(jQuery('#sub_total_value_' + store_id).val());
        jQuery('#sub_total_' + store_id).html(formatMoney(sub_total));
        jQuery('#total_amount').html(formatMoney(total));
    }

    var initProfile = function (tab)
    {
        jQuery('#browse ul li').removeClass('current');
        jQuery('#browse ul li#tab-' + tab).addClass('current');
        var item = jQuery('#browse ul li#tab-' + tab + ' a');
        jQuery.post(item.data('url'), '', function (data) {
            jQuery('.profilePage').html(data);
        })
    }

    var initProfileWishlist = function ()
    {
        //wishlist
        loadWishlist();
        /* jQuery(document).on('click', '.remove_from_wishlist', function() {
         var product_id = jQuery(this).data('id');
         jQuery.post(mooConfig.url.base + '/stores/wishlists/remove_form_wishlist/', 'product_id=' + product_id, function(data){
         data = jQuery.parseJSON(data);
         if(data.result == 0)
         {
         mooAlert.alert(data.message);
         }
         else 
         {
         jQuery('#product-wishlist-' + product_id).remove();
         var total = parseInt(jQuery('#badge_counter_wishlist').text());
         jQuery('#badge_counter_wishlist').empty().append(total - 1);
         if(jQuery('#table-wishlist tbody.data-exist tr').length == 0)
         {
         jQuery('#table-wishlist tbody.data-exist').remove();
         jQuery('#table-wishlist tbody.data-empty').show();
         }
         }
         });
         })*/

        //remove_from_wishlist
        jQuery(document).on('click', '.remove_product_wishlist', function () {
            var product_id = jQuery(this).data('id');
            console.log(product_id);
            mooConfirmBox(mooPhrase.__('text_confirm_remove_from_wishlist'), function () {
                jQuery.post(mooConfig.url.base + '/stores/wishlists/remove_form_wishlist/', 'product_id=' + product_id, function (data) {
                    data = jQuery.parseJSON(data);
                    if (data.result == 0)
                    {
                        mooAlert.alert(data.message);
                    } else
                    {
                        jQuery('#product-wishlist-' + product_id).remove();
                        var total = parseInt(jQuery('#badge_counter_wishlist').text());
                        jQuery('#badge_counter_wishlist').empty().append(total - 1);
                        if (jQuery('#table-wishlist tbody.data-exist tr').length == 0)
                        {
                            jQuery('#table-wishlist tbody.data-exist').remove();
                            jQuery('#table-wishlist tbody.data-empty').show();
                        }
                    }
                });
            });
        })

        //load paging
        jQuery(document).on('click', '.wishlist_paging a', function () {
            loadWishlist(jQuery(this).attr('href'));
            return false;
        })
    }

    var initProfileMyFiles = function ()
    {
        //load my files
        loadMyFiles();

        //load paging
        jQuery(document).on('click', '.wishlist_paging a', function () {
            loadMyFiles(jQuery(this).attr('href'));
            return false;
        })
    }

    var initProfileOrders = function ()
    {
        //orders
        loadMyOrders();

        //view order detail
        jQuery(document).on('click', '.view_order_detail', function () {
            var order_id = jQuery(this).data('id');
            jQuery.post(mooConfig.url.base + "/stores/orders/order_detail/" + order_id, '', function (data) {
                jQuery('#storeModal .modal-content').empty().append(data);
                jQuery('#storeModal').modal();
            });
        })

        //load paging
        jQuery(document).on('click', '.order_paging a', function () {
            loadMyOrders(jQuery(this).attr('href'));
            return false;
        })
    }

    var loadWishlist = function (link)
    {
        if (typeof link == 'undefined')
        {
            link = mooConfig.url.base + '/stores/wishlists/load_wishlist/';
        }

        jQuery.post(link, '', function (data) {
            jQuery('#table-wishlist .data-exist').empty().append(data);
        });
    }

    var loadMyFiles = function (link)
    {
        if (typeof link == 'undefined')
        {
            link = mooConfig.url.base + '/stores/products/load_my_files/';
        }

        jQuery.post(link, '', function (data) {
            jQuery('#table-wishlist .data-exist').empty().append(data);
        });
    }

    var loadMyOrders = function (link)
    {
        if (typeof link == 'undefined')
        {
            link = mooConfig.url.base + '/stores/orders/my_order_list/';
        }

        jQuery.post(link, '', function (data) {
            jQuery('#table_order .data-exist').empty().append(data);
        });
    }
    
    var initCreateSeller = function()
    {
        //check exist business
        jQuery(document).on('click', '#btn_create_seller', function() {
            jQuery.post(mooConfig.url.base + '/stores/check_exist_business_page', function(data){
                if(isJson(data))
                {
                    data = jQuery.parseJSON(data);
                    if(data.result == 0)
                    {
                        window.location = mooConfig.url.base + data.redirect;
                    }
                    else 
                    {
                        $.fn.SimpleModal({
                            btn_ok: mooPhrase.__('open_business_page'),
                            btn_cancel: mooPhrase.__('cancel'),
                            model: 'content',
                            title: mooPhrase.__('warning'),
                            contents: data.text
                        }).addButton(mooPhrase.__('open_business_page'), "button button-default", function(e){								
                            window.location = mooConfig.url.base + data.redirect;
                        }).addButton(mooPhrase.__('cancel'), "button button-default").showModal();
                    }
                }
            });
        });
    }
    
    var initSearch = function()
    {
        //suggest search category
        initSuggestSearchCategory();
        
        //search product
        jQuery(document).on('click', '#btn_search_product', function (e) {
            jQuery('.form_search_product').addClass('hidden_search_form');
            jQuery(this).closest('.form_search_product').removeClass('hidden_search_form');
            loadProductList('');
        })

        jQuery(".form_search_product").submit(function (e) {
            e.preventDefault();
        });
    }
    
    var parseAjaxLink = function()
    {
        jQuery(document).ajaxSend(function( event, jqxhr, settings ) {
            if(mooConfig.isApp)
            {
                settings.url = mooGlobal.appBindTokenLanguage(settings.url);
            }
        });
    }
    
    var initShowMoreContent = function(){
        $('.truncate').each(function () {
            if (parseInt($(this).css('height')) >= 145){
                var element = $('<a href="javascript:void(0)" class="show-more">' + $(this).data('more-text') + '</a>');
                $(this).after(element);
                element.click(function(e){
                    showMore(this);
                });
            }
        });
    }
    
    function showMore(obj){
        
        $(obj).prev().css('max-height', 'none');
        var element = $('<a href="javascript:void(0)" class="show-more">' + $(obj).prev().data('less-text') + '</a>');
        $(obj).replaceWith(element);
        element.click(function(e){
            showLess(this);
        });
    }

    function showLess(obj){
        
        $(obj).prev().css('max-height', '');
        var element = $('<a href="javascript:void(0)" class="show-more">' + $(obj).prev().data('more-text') + '</a>');
        $(obj).replaceWith(element);
        element.click(function(e){
            showMore(this);
        });
    }

    return{
        initShortcut: function ()
        {
            initShortcut();
        },
        loadCartBalloon: function ()
        {
            loadCartBalloon();
        },
        initGlobal: function () {
            initGlobal();
        },
        initStoreList: function () {
            initStoreList();
        },
        initProductList: function (business_id) {
            initProductList(business_id);
        },
        initCreateStore: function () {
            initCreateStore();
        },
        initQuickview: function ()
        {
            initQuickview();
        },
        initCategoryEffect: function ()
        {
            initCategoryEffect();
        },
        initReviewStar: function ()
        {
            initReviewStar();
        },
        initProductDetail: function (allow_comment)
        {
            initProductDetail(allow_comment);
        },
        initCart: function (load_cart)
        {
            initCart(load_cart);
        },
        initCheckout: function ()
        {
            initCheckout();
        },
        initProfileWishlist: function ()
        {
            initProfileWishlist();
        },
        initProfileOrders: function ()
        {
            initProfileOrders();
        },
        initProfile: function (tab)
        {
            initProfile(tab);
        },
        initOrderShipping: function () {
            initOrderShipping();
        },
        printOrder: function () {
            printOrder();
        },
        initProfileMyFiles: function ()
        {
            initProfileMyFiles();
        },
        loadProductDescription: function (product_id, review_id)
        {
            loadProductDescription(product_id, review_id)
        },
        initReviewPhotoPopup: function ()
        {
            initReviewPhotoPopup();
        },
        initRelatedProducts: function ()
        {
            initRelatedProducts();
        },
        initCreateSeller: function()
        {
            initCreateSeller();
        },
        initSearch: function()
        {
            initSearch();
        },
        parseAjaxLink: function(){
            parseAjaxLink();
        },
        initShowMoreContent: initShowMoreContent,
    };
}));
