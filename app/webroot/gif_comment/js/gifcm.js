(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery', 'mooFileUploader', 'mooBehavior', 'mooPhrase', 'mooAjax', 'mooGlobal', 'mooTooltip', 'slimScroll'], factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS-like
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals (root is window)
        root.mooGifComment = factory();
    }
}(this, function ($, mooFileUploader, mooBehavior, mooPhrase, mooAjax, mooGlobal, mooTooltip) {

    var flagScroll = true;
    var autoCloseWhenOutsideClick = function () {
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
        $('.gif-icon').unbind("click");
        $('.gif-icon').click(function () {
            var data = $(this).data();
            var type = data.type;
            var activity_id = data.id;

            //$('#gif_icon_'+type+'_'+activity_id+'').click(function () {
            if ($(this).next().hasClass('show')) {
                $('.gif_form_content_hoder').removeClass('show');
            } else {
                $('.gif_form_content_hoder').removeClass('show');
                $(this).next().addClass('show');
            }
            $('.gif_form > input').val('');
            loadGifContent(type, activity_id);
            closeGifWhenShareFeed();
            autoCloseWhenOutsideClick();
        });

    }
    var loadGifContent = function (type, activity_id)
    {
        mooAjax.get({
            url: 'https://api.tenor.com/v1/trending?key=CQ30Q3LPLUGM&limit=10',
        }, function (data) {
            var tmp = [];
            $.each(data.results, function (index, element) {
                tmp.push('<p class="gif_image_wraaper"><img class="gif_image" id="gif_img_' + element.id + ' " src=" ' + element.media[0].tinygif.url + ' " ></p>');
            });
            tmp.push('<p class="gif_load_more" data-id="' + activity_id + '"  data-pos="' + data.next + '"  data-type="' + type + '" id="gif_load_more_' + type + '_' + activity_id + '"><a href="javascript:void(0)"><span class="gif_loading" style="background-image:url(' + mooConfig.url.base + '/img/indicator.gif);background-size:inherit;background-repeat:no-repeat"></span></a></p>');
            $('#gif_content_' + type + '_' + activity_id + '').html(tmp);
            initUploadGif(type, activity_id);
            searchGif(type, activity_id);
            $('.initSlimScroll').slimScroll({height: '300px'});
            initLoadMoreGif(type, activity_id);
        });

    }
    var initUploadGif = function (type, id)
    {
        $('.gif_image').unbind('click');
        $('.gif_image').click(function () {
            var ele = $(this).attr('src');
            var element = $('<span id="attach_' + type + '_' + id + '_gif' + '" style="background-image:url(' + mooConfig.url.base + '/img/indicator.gif);background-size:inherit;background-repeat:no-repeat"></span>');

            var preview_img = getPreviewId(type, id);
            var button_attach = getButtonId(type, id);
            var img_hidden_link = getHiddenLink(type, id);
            $(preview_img).html(element);
            $(button_attach).hide();

            mooAjax.post({
                url: mooConfig.url.base + '/gif_comments/upload_gif',
                data: {
                    gif_link: ele
                }
            }, function (data) {
                var json = $.parseJSON(data);
                if (json.file)
                {
                    var element = $('<span id="attach_' + type + '_' + id + '_gif' + '" style="background-image:url(' + json.thumb + ');background-size:inherit;background-repeat:no-repeat"></span>');
                    $(preview_img).html(element);
                    $(button_attach).hide();
                    var deleteItem = $('<a href="javascript:void(0);"><i class="material-icons thumb-review-delete">clear</i></a>');
                    element.append(deleteItem);

                    element.find('.thumb-review-delete').unbind('click');
                    element.find('.thumb-review-delete').click(function () {
                        element.remove();
                        $(button_attach).show();
                        $(img_hidden_link).val('');
                    });
                    $(img_hidden_link).val(json.file);
                } else
                {
                    $(".error-message").show();
                    $(".error-message").html(json.message);
                }
            });
        });
    }

    var searchGif = function (type, id)
    {
        var minlength = 2;
        var searchRequest = null;
        $("#gif_auto_search_" + type + '_' + id + "").keyup(function () {
            var value = $(this).val();
            if (value.length >= minlength) {
                //if (searchRequest != null) searchRequest.abort();
                searchRequest = $.ajax({
                    type: "GET",
                    url: "https://api.tenor.com/v1/search?q=" + value + "&key=CQ30Q3LPLUGM&limit=10",
                    dataType: "json",
                    success: function (data) {
                        var tmp = [];
                        $.each(data.results, function (index, element) {
                            tmp.push('<p class="gif_image_wraaper"><img class="gif_image" id=" gif_img_' + element.id + ' " src=" ' + element.media[0].tinygif.url + ' " ></p>');
                        });
                        tmp.push('<p class="gif_load_more" data-id="' + id + '" data-search="' + value + '"  data-pos="' + data.next + '"  data-type="' + type + '" id="gif_load_more_' + type + '_' + id + '"><a href="javascript:void(0)"><span class="gif_loading" style="background-image:url(' + mooConfig.url.base + '/img/indicator.gif);background-size:inherit;background-repeat:no-repeat"></span></a></p>');
                        $('#gif_content_' + type + '_' + id + '').html(tmp);
                        initUploadGif(type, id);
                        $('.initSlimScroll').slimScroll({height: '300px'});
                        initLoadMoreGif(type, id);
                    }
                });
            }
        });
    }

    var initLoadMoreGif = function (type, id) {
        var $content = $('#gif_content_' + type + '_' + id + '');
        if ($content.length > 0) {
            $content.scroll(function () {
                var $loadMore = $('#gif_load_more_' + type + '_' + id + '');
                var top = $content.offset().top;
                var loadmore = $loadMore.offset().top;
                var space = loadmore - top;
                if (space < 300 && flagScroll) {
                    doClickLoadMore();
                    $loadMore.find('a').trigger("click");
                    flagScroll = false;
                }
            });
        }
    }

    var doClickLoadMore = function () {
        $('.gif_load_more a').unbind("click");
        $('.gif_load_more a').click(function () {
            var data = $(this).parent().data();
            var type = data.type;
            var id = data.id;
            var pos = data.pos;
            if (data.search)
                var value = data.search;
            doLoadMore(type, id, pos, value);
        });
    }

    var doLoadMore = function (type, id, pos, value) {
        var searchRequest = null;
        var $content = $('#gif_content_' + type + '_' + id + '');
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
                    tmp.push('<p class="gif_image_wraaper"><img class="gif_image" id=" gif_img_' + element.id + ' " src=" ' + element.media[0].tinygif.url + ' " ></p>');
                });
                $content.find('.gif_loading').remove();

                $content.append(tmp);
                if (value != undefined) {
                    $content.append('<p class="gif_load_more" data-search="' + value + '" data-id="' + id + '" data-pos="' + data.next + '"  data-type="' + type + '" id="gif_load_more_' + type + '_' + id + '"><a href="javascript:void(0)"><span class="gif_loading" style="background-image:url(' + mooConfig.url.base + '/img/indicator.gif);background-size:inherit;background-repeat:no-repeat"></span></a></p>');
                } else {
                    $content.append('<p class="gif_load_more" data-id="' + id + '" data-pos="' + data.next + '"  data-type="' + type + '" id="gif_load_more_' + type + '_' + id + '"><a href="javascript:void(0)"><span class="gif_loading" style="background-image:url(' + mooConfig.url.base + '/img/indicator.gif);background-size:inherit;background-repeat:no-repeat"></span></a></p>');
                }

                $('.initSlimScroll').slimScroll({height: '300px'});
                initUploadGif(type, id);
                doClickLoadMore();
            },
            error: function (response) {
                $('.gif_load_more').remove();
            }

        });
    }


    var getPreviewId = function (type, id) {
        var previewId = '';
        switch (type) {
            case'commentForm':
                previewId = '#comment_preview_image_' + id;
                break;
            case'activity_comment_edit':
                previewId = '#activity_comment_preview_attach_' + id;
                break;
            case'item_comment_edit':
                previewId = '#item_comment_preview_attach_' + id;
                break;
        }
        return previewId;

    }
    var getButtonId = function (type, id) {
        var previewId = '';
        switch (type) {
            case'commentForm':
                previewId = '#comment_button_attach_' + id;
                break;
            case'activity_comment_edit':
                previewId = '#activity_comment_attach_' + id;
                break;
            case'item_comment_edit':
                previewId = '#item_comment_attach_' + id;
                break;
        }
        return previewId;
    }
    var getHiddenLink = function (type, id) {
        var previewId = '';
        switch (type) {
            case'commentForm':
                previewId = '#comment_image_' + id;
                break;
            case'activity_comment_edit':
                previewId = '#activity_comment_attach_id_' + id;
                break;
            case'item_comment_edit':
                previewId = '#item_comment_attach_id_' + id;
                break;
        }
        return previewId;
    }
    var closeGifWhenShareFeed = function () {
        $('.viewer-submit-comment').unbind("click");
        $('.viewer-submit-comment').click(function () {
            $('.gif_form_content_hoder').removeClass('show');
            $('.gif_form > input').val('');
        });
        $('.commentButton > .btn').click(function () {
            $('.gif_form_content_hoder').removeClass('show');
            $('.gif_form > input').val('');
        });
        $('.commentButton > .mdl-button').click(function () {
            $('.gif_form_content_hoder').removeClass('show');
            $('.gif_form > input').val('');
        });
    }


    return{
        initLoadGif: function () {
            initLoadGif();
        },
        autoCloseWhenOutsideClick: function () {
            autoCloseWhenOutsideClick();
        },
        doClickLoadMore: function () {
            doClickLoadMore();
        },
        closeGifWhenShareFeed: function () {
            closeGifWhenShareFeed();
        },
    }
}));