(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery', 'mooFileUploader', 'mooBehavior', 'mooPhrase', 'mooAjax', 'mooGlobal', 'mooTooltip', 'slimScroll'], factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS-like
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals (root is window)
        root.mooGifPostFeed = factory();
    }
}(this, function ($, mooFileUploader, mooBehavior, mooPhrase, mooAjax, mooGlobal, mooTooltip) {
    var _plugin3rd_name = 'GifComment';
    var flagScroll = true;
    var array_delete_links = {};
    var autoCloseGifFormWhenOutsideClick = function () {
        $(document).mouseup(function(e) 
        {
            var container = $(".gif_form_content_hoder");
            // if the target of the click isn't the container nor a descendant of the container
            if (!container.is(e.target) && container.has(e.target).length === 0) 
            {
                $('.gif_form_content_hoder').removeClass('show');
            }
        });
    }
    var initLoadGif = function ()
    {
        $('#gif_icon_form').unbind("click");
        $('#gif_icon_form').click(function () {
            if(($(this).attr('data-popup')) == '1'){
                if ($(this).next().hasClass('show')) {
                    $('.gif_form_content_hoder').removeClass('show');
                } else {
                    $('.gif_form_content_hoder').removeClass('show');
                    $(this).next().addClass('show');
                }
                $('.gif_form > input').val('');
                loadGifContentForm();
                closeGifWhenShareFeed();
                autoCloseGifFormWhenOutsideClick();
            }else{
                message = mooPhrase.__('information');
                msg = mooPhrase.__("please_contact_any_sales_reps_visiting_you")+". <br><br>"+mooPhrase.__("gif_msg_part2")+".<br>("+mooPhrase.__("gif_msg_part3")+").<br>"+mooPhrase.__("gif_msg_part4")+". <br><br><a href='/faqs/view/20/how-to-get-free-item-from-sales-member'>"+mooPhrase.__("gif_msg_part5")+"</a>, "+mooPhrase.__("or")+", <a href='javascript:void(0);'>"+mooPhrase.__("gif_msg_part6")+"</a>";
                // text = 'close';
                // Set title
                $($('#portlet-config  .modal-header .modal-title')[0]).html(message);
                // Set content
                $($('#portlet-config  .modal-body')[0]).html(msg);
                // OK callback, remove all events bound to this element
                $('#portlet-config  .modal-footer .ok').off("click").click(function(){
                    callback();
                    $('#portlet-config').modal('hide');
                });
                $('#portlet-config .blue.ok').css('display','none');
                $('#portlet-config').modal('show');
            }
        });

        $('body').on('enablePluginsCallback', function(e, data){
            var _plugins = data.plugins;
            if(_plugins.indexOf(_plugin3rd_name) > -1){
                enablePlugin();
            }
        });
        
        $('body').on('disablePluginsCallback', function(e, data){
            var _plugins = data.plugins;
            if(_plugins.indexOf(_plugin3rd_name) > -1){
                disabePlugin();
            }
        });
        
        $('body').on('afterPostWallCallbackSuccess', function () {
            $('body').trigger('stopFeedPlugin3drCallback', [{plugin_name: _plugin3rd_name}]);
        });
    }
    var loadGifContentForm = function ()
    {
        mooAjax.get({
            url: 'https://api.tenor.com/v1/trending?key=CQ30Q3LPLUGM&limit=10',
        }, function (data) {
            var tmp = [];
            $.each(data.results, function (index, element) {
                tmp.push('<p class="gif_image_wraaper"><img class="gif_image_form" id="gif_img_' + element.id + ' " src=" ' + element.media[0].mediumgif.url + ' " ></p>');
            });
            tmp.push('<p class="gif_load_more"  data-pos="' + data.next + '"  id="gif_load_more_form"><a href="javascript:void(0)"><span class="gif_loading" style="background-image:url(' + mooConfig.url.base + '/img/indicator.gif);background-size:inherit;background-repeat:no-repeat"></span></a></p>');
            $('#gif_content_form_feed').html(tmp);
            initUploadGifFormFeed();
            searchGif();
            $('.initSlimScroll').slimScroll({height: '300px'});
            initLoadMoreGifForForm();
        });

    }
    var initUploadGifFormFeed = function ()
    {
        $('.gif_image_form').unbind('click');
        $('.gif_image_form').click(function () {
            var ele = $(this).attr('src');
            var element = $('<span img-file="" id="attach_gif_form' + '" style="background-image:url(' + mooConfig.url.base + '/img/indicator.gif);background-size:inherit;background-repeat:no-repeat"></span>');
            if ($('#attach_gif_form').length > 0) {
                var tmp = $('#attach_gif_form').attr('img-file');
                $('#attach_gif_form').remove();
                $('#wall_photo').val($('#wall_photo').val().replace(tmp, ''));
            }
            element.insertBefore('.addMoreImage');
            $('#wall_photo_preview').show();
            $('#addMoreImage').show();

            $('body').trigger('startFeedPlugin3drCallback',[{plugin_name: _plugin3rd_name}]);

            mooAjax.post({
                url: mooConfig.url.base + '/gif_comments/upload_gif',
                data: {
                    gif_link: ele
                }
            }, function (data) {
                var json = $.parseJSON(data);
                if (json.file)
                {
                    if ($('#attach_gif_form').length > 0) {
                        var tmp = $('#attach_gif_form').attr('img-file');
                        $('#wall_photo').val($('#wall_photo').val().replace(tmp, ''));
                    }

                    $('[data-toggle="tooltip"]').tooltip('hide');
                    var element = $('#attach_gif_form');
                    element.attr('style', 'background-image:url(' + json.thumb + ')');
                    element.attr('img-file', json.file + ',');
                    var deleteItem = $('<a href="javascript:void(0)"><i class="material-icons thumb-review-delete">clear</i></a>');
                    element.append(deleteItem);

                    element.find('.thumb-review-delete').unbind('click');
                    element.find('.thumb-review-delete').click(function () {
                        element.remove();
                        $('#wall_photo').val($('#wall_photo').val().replace(json.file + ',', ''));
                        $('body').trigger('afterDeleteWallPhotoCallback',null);
                        $('body').trigger('stopFeedPlugin3drCallback', [{plugin_name: _plugin3rd_name}]);
                    });
                    var wall_photo = $('#wall_photo').val();

                    var result = $('#wall_photo').val().split(',');
                    for (var i = 0; i < result.length; i++) {
                        if (json.file == result[i])
                            json.file = '';
                    }
                    if (json.file != '')
                        $('#wall_photo').val(wall_photo + json.file + ',');

                    destroyPreviewlink();
                } else
                {
                    $(".error-message").show();
                    $(".error-message").html(json.message);
                }
                $('body').trigger('afterUploadWallPhotoCallback',null);
            });
        });
    }

    var searchGif = function ()
    {
        var minlength = 2;
        var searchRequest = null;
        $("#gif_auto_search_form").keyup(function () {
            var value = $(this).val();
            if (value.length >= minlength) {
                searchRequest = $.ajax({
                    type: "GET",
                    url: "https://api.tenor.com/v1/search?q=" + value + "&key=CQ30Q3LPLUGM&limit=10",
                    dataType: "json",
                    success: function (data) {
                        var tmp = [];
                        $.each(data.results, function (index, element) {
                            tmp.push('<p class="gif_image_wraaper"><img class="gif_image_form" id=" gif_img_' + element.id + ' " src=" ' + element.media[0].tinygif.url + ' " ></p>');
                        });
                        tmp.push('<p class="gif_load_more"  data-search="' + value + '"  data-pos="' + data.next + '"  id="gif_load_more_form"><a href="javascript:void(0)"><span class="gif_loading" style="background-image:url(' + mooConfig.url.base + '/img/indicator.gif);background-size:inherit;background-repeat:no-repeat"></span></a></p>');
                        $('#gif_content_form_feed').html(tmp);
                        initUploadGifFormFeed();
                        $('.initSlimScroll').slimScroll({height: '300px'});
                        initLoadMoreGifForForm();
                    }
                });
            }
        });
    }

    var initLoadMoreGifForForm = function () {
        var $content = $('#gif_content_form_feed');
        if ($content.length > 0) {
            $content.scroll(function () {
                var $loadMore = $('#gif_load_more_form');
                var top = $content.offset().top;
                var loadmore = $loadMore.offset().top;
                var space = loadmore - top;
                if (space < 300 && flagScroll) {
                    doClickLoadMoreForm();
                    $loadMore.find('a').trigger("click");
                    flagScroll = false;
                }
            });
        }
    }

    var doClickLoadMoreForm = function () {
        $('.gif_load_more a').unbind("click");
        $('.gif_load_more a').click(function () {
            var data = $('.gif_load_more').data();
            var pos = data.pos;
            if (data.search)
                var value = data.search;
            doLoadMoreForm(pos, value);
        });
    }

    var doLoadMoreForm = function (pos, value) {
        var searchRequest = null;
        var $content = $('#gif_content_form_feed');
        var url = '';
        if (value != undefined) {
            url = "https://api.tenor.com/v1/search?q=" + value + "&key=CQ30Q3LPLUGM&limit=10&pos=" + pos + "";
        } else
            url = 'https://api.tenor.com/v1/trending?key=CQ30Q3LPLUGM&limit=10&pos=' + pos + '';

        searchRequest = $.ajax({
            type: "GET",
            url: url,
            dataType: "json",
            success: function (data) {
                flagScroll = true;
                $('.gif_load_more:first').remove();
                var tmp = [];
                $.each(data.results, function (index, element) {
                    tmp.push('<p class="gif_image_wraaper"><img class="gif_image_form" id=" gif_img_' + element.id + ' " src=" ' + element.media[0].tinygif.url + ' " ></p>');
                });
                $content.find('.gif_loading').remove();

                $content.append(tmp);
                if (value != undefined) {
                    $content.append('<p class="gif_load_more" data-search="' + value + '"  data-pos="' + data.next + '"  id="gif_load_more_form"><a href="javascript:void(0)"><span class="gif_loading" style="background-image:url(' + mooConfig.url.base + '/img/indicator.gif);background-size:inherit;background-repeat:no-repeat"></span></a></p>');
                } else {
                    $content.append('<p class="gif_load_more" data-pos="' + data.next + '"  id="gif_load_more_form"><a href="javascript:void(0)"><span class="gif_loading" style="background-image:url(' + mooConfig.url.base + '/img/indicator.gif);background-size:inherit;background-repeat:no-repeat"></span></a></p>');
                }

                $('.initSlimScroll').slimScroll({height: '300px'});
                initUploadGifFormFeed();
                doClickLoadMoreForm();
            },
            error: function (response) {
                $('.gif_load_more').remove();
            }

        });
    }
    var resetVideoUpload = function () {
        $('#video_pc_feed_preview').hide();
        $('#title').val('');
        $('#description').val('');
        $('#video_destination').val('');
    }
    var destroyPreviewlink = function ()
    {
        if ( ($('#userShareLink').length > 0) && ($('#userShareLink').val().trim() != '') )
        {
            array_delete_links[$('#userShareLink').val().trim()] = '1';
        }
        if ( ($('#userShareVideo').length > 0) && ($('#userShareVideo').val().trim() != '') )
        {
            array_delete_links[$('#userShareVideo').val().trim()] = '1';
        }

        $('#preview_link').remove();
        $('#userShareLink').val('');
        $('#userShareVideo').val('');
        $('#shareImage').val('1');
    }
    var closeGifWhenShareFeed = function () {
        $('#status_btn').unbind("mouseup");
        $("#status_btn").mouseup(function () {
            $('body').trigger('afterCloseGifWhenShareFeed',null);
            $('.gif_form_content_hoder').removeClass('show');
            $('.gif_form > input').val('');
            /*if ($('.wall-status-background').length > 0) {
                if (!$('#wall_photo').val()) {
                    $('.wall-status-background').removeClass('status-thumb-disable');
                    $("#postBgThumb").show();
                    $("#postBackgroundBox").show();
                }
            }*/
        });
    }
    
    var disabePlugin = function () {
        jQuery('.gif_form_feed').hide();
        hideUploadPhoto();
    }
    
    var enablePlugin = function () {
        jQuery('.gif_form_feed').show();
        showUploadPhoto();
    }
    
    function showUploadPhoto()
    {
        jQuery('#select-2').show();
        jQuery('#wall_photo_preview').hide();
        jQuery('#wall_photo').val('');
    }
    
    function hideUploadPhoto()
    {
        jQuery('#select-2').hide();
        jQuery('#wall_photo_preview').hide();
        jQuery('#wall_photo_preview').find('span').each(function(){
            if(jQuery(this).attr('id') != 'addMoreImage')
            {
                jQuery(this).remove();
            }
        })
        jQuery('#wall_photo_preview').find('#addMoreImage').hide();
    }

    return{
        initLoadGif: function () {
            initLoadGif();
        },
        doClickLoadMoreForm: function () {
            doClickLoadMoreForm();
        },
        autoCloseGifFormWhenOutsideClick: function () {
            autoCloseGifFormWhenOutsideClick();
        },
    }
}));