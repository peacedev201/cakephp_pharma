(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
    	define(['jquery', 'mooBusinessFlexslider', 'mooFileUploader', 'mooBehavior', 'mooAlert', 'mooPhrase', 'mooAjax', 'mooUser', 'mooGlobal', 'mooButton', 'mooSlimscroll', 'mooActivities', 'mooShare','mooResponsive', 'business_star_rating', 'mooTooltip', 'Jcrop'], factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS-like
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals (root is window)
        root.mooBusiness = factory();
    }
}(this, function ($, mooBusinessFlexslider, mooFileUploader, mooBehavior, mooAlert, mooPhrase, mooAjax, mooUser, mooGlobal, mooButton, mooSlimscroll, mooActivities, mooShare, mooResponsive, business_star_rating, mooTooltip) {
    var initActivity = function()
    {
        initReviewStar();
        
        //show hide map
        jQuery(document).on('click', '.btn_show_map', function(){
            var item = jQuery(this).data('item');
            if(!jQuery(item).is(':visible'))
            {
                if(jQuery(item).attr('src') == '')
                {
                    jQuery(item).attr('src', jQuery(this).data('link'));
                }
                jQuery(item).show();
                jQuery(this).find('span').html(mooPhrase.__('business_text_hide_map'));
            }
            else
            {
                jQuery(item).hide();
                jQuery(this).find('span').html(mooPhrase.__('business_text_show_map'));
            }
            if(item == '#map_detail')
            {
                jQuery("html, body").animate({ scrollTop:  $("#wrap_map").offset().top }, "fast");
            }
        })
    }
    
    var mooConfirmBox = function( msg, callback )
    {
        setTimeout(function () {
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
        }, 1);
    }
    
    var isJson = function(str) {
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
    
    var moreResults = function(url, div, obj){
        $(obj).spin('small');
        $(obj).parent().css('display', 'none');
        var postData = {};
        
        if(typeof(window.searchParams) === 'undefined'){
            window.searchParams = '';
        }
        $.post(mooConfig.url.base + url,window.searchParams ,function(data){
            $(obj).spin(false);
            $('#' + div).find('.view-more:first').remove();
            $('#' + div).children('.clear:first').remove();
            $('#' + div).find('.loading:first').remove();
            flagScroll = true;
            if ( div == 'comments' || div == 'theaterComments' ){
                $("#" + div).append(data);
                
                // move load more to end of comment list
                $('#'+div+' .view-more').insertAfter('#'+div+' li[id^="itemcomment_"]:last');
            }
            else{
                $("#" + div).append(data);
                
            }

            mooOverlay.registerOverlay();
            registerImageComment();
            $(".tip").tipsy({ html: true, gravity: 's' });
            mooResponsive.initFeedImage();
            // bind load more button
            initMoreResults();
        });
    };
    
    var initUserDetail = function(user_id, activity_id)
    {
        loadBusinessActivities('user', user_id, activity_id);
    }
    
    var initActivityForm = function()
    {
        jQuery(document).on('click', '#btn_activity_schedule', function(){
            if(!jQuery('#schedule_form').is(':visible'))
            {
                jQuery('#schedule_form').show();
                jQuery("#schedule_value" ).datepicker();
                jQuery('#schedule_form').find('input, select').removeAttr('disabled');
            }
            else
            {
                jQuery('#schedule_form').hide();
                jQuery('#schedule_form').find('input, select').attr('disabled', 'disabled');
            }
            
        });
    }
    
    var initCheckIn = function(){
        //check in dialog
        jQuery(document).on('click', '.btn_checkin', function(){
            var id = jQuery(this).data('id');
            jQuery.post(mooConfig.url.base + "/businesses/checkin_dialog/" + id, function(data){
                if(isJson(data))
                {
                    var json = jQuery.parseJSON(data);
                    if(json.require_login)
                    {
                        mooAlert.alert(json.message);
                    }
                    else
                    {
                        mooAlert.alert(json.message);
                    }
                }
                else 
                {
                    jQuery('#businessModal .modal-content').empty().append(data);
                    jQuery('#businessModal').modal({
                        backdrop : 'static'
                    });
                }
            })
        })
        
        jQuery(document).on('click', '#checkInButton', function(){
            jQuery.post(mooConfig.url.base + "/business_checkin/do_checkin/", jQuery('#checkInForm').serialize(), function(data){
                var json = jQuery.parseJSON(data);
                if(json.result == 0)
                {
                    jQuery('#checkInMessage').html(json.message).show();
                }
                else
                {
                    if(mooConfig.isApp)
                    {
                        window.location = json.location + "?app_no_tab=1";
                    }
                    else
                    {
                        window.location = json.location;
                    }
                }
            })
        })
    }
    
    function scrollCat()
    {
        if($('.wrap_business_cat').length > 0)
        {
            var widthBound = $('.wrap_business_cat').width();
            $('#business_cat').each(function(){  
                var liItems = $(this);
                var Sum = 0;
                if(liItems.children('li').length >= 1){
                 $(this).children('li').each(function(i, e){
                        Sum += $(e).outerWidth(true);
                 });
                $(this).width(Sum+1);
                } 
            });
            var totalWidth =  $('#business_cat').width();
            //var newScroll = parseFloat($('#business_cat').css('marginLeft').replace(/[^-\d\.]/g, ''));
            var left_position = 0;
            $('.next_cat').click(function(){

                if(-(left_position - widthBound) < totalWidth){
                    left_position-=widthBound;
                    $('#business_cat').animate({
                        marginLeft: left_position
                      }, 'fast');
                }
            });

            $('.prev_cat').click(function(){            
                if((left_position + widthBound) <= 0){
                    left_position += widthBound;
                    $('#business_cat').animate({
                        marginLeft: left_position
                      }, 'fast');
                }
            });
        }
    }
    
    var initReviewStar = function() {
        var input = jQuery('input.rating'), count = Object.keys(input).length;
        if (count > 0) {
            input.rating('refresh', {
                disabled: true, 
                showClear: false, 
                showCaption: false,
                max: 5,
                size:'xs'
            });
        }
    }
    
    var initSuggestGlobalLocation = function()
    {
        jQuery('.global_search_location').each(function(){
            var item = jQuery(this);
            item.autocomplete({
                source: function (request, response) {
                    jQuery.post(mooConfig.url.base + '/businesses/suggest_global_location/', 'keyword=' + item.val(), function(data){
                        if(data != 'null')
                        {
                            response(jQuery.parseJSON(data));
                        }
                        else
                        {
                            jQuery('#global_search_location_id').val('');
                        }
                    });
                },
                minLength: 3,
                select: function (event, ui) {
                    item.val(ui.item.label);
                    //jQuery('#global_search_location_id').val(ui.item.value);
                    return false;
                },
                focus: function( event, ui ) {
                    item.val( ui.item.label );
                    jQuery('.ui-autocomplete li').removeClass('selected');
                    jQuery('#location_suggest_' + ui.item.value).addClass('selected');
                    return false;  
                },  
            }).data("ui-autocomplete")._renderItem = function(ul, item) {
                var term = this.term;
                //var regex = new RegExp("\\S*" + $.ui.autocomplete.escapeRegex(term) + "\\S*", "gi");
                var regex = new RegExp($.ui.autocomplete.escapeRegex(term), "gi");
                var label = item.label.replace(regex, '<b>$&</b>');
                return $('<li id="location_suggest_' + item.value + '"></li>').append(label).appendTo(ul);
            };
        })
        jQuery('.global_search_location').keypress(function (e) {
            if(e.which == 13)  // the enter key code
            {
                jQuery(this).closest('form').find('#btn_global_search').trigger('click');
                return false;  
            }
        });
    }
    
    var initSuggestGlobalCategory = function()
    {
        jQuery('.global_search_category').each(function(){
            var item = jQuery(this);
            var arrow = item.parent().find('.drop-history');
            var history = [];
            if(typeof jQuery(this).data('history') != 'undefined')
            {
                history = jQuery(this).data('history');
            }
            item.mousedown(function() {
                if(this.value == '')
                {
                    item.autocomplete({
                        source: history,
                        minLength: 0,
                        focus: function( event, ui ) {
                            item.val( ui.item.label );
                            return false;  
                        }
                    }).data("ui-autocomplete")._renderItem = function(ul, item) {
                        var html = '';
                        var item_id = '';
                        if(item.image)
                        {
                            html = 
                                '<a href="' + item.link + '">\n\
                                    <div class="user_avatar_small attached-image">' + item.image + '</div>\n\
                                    <div class="suggest_name">' + item.label + '</div>\n\
                                    <div class="suggest_more_info">' + item.address + '</div>\n\
                                </a>';
                            item_id = 'business_suggest_' + item.value;
                        }
                        else
                        {
                            var term = this.term;
                            //var regex = new RegExp("\\S*" + $.ui.autocomplete.escapeRegex(term) + "\\S*", "gi");
                            var regex = new RegExp($.ui.autocomplete.escapeRegex(term), "gi");
                            html = item.label.replace(regex, '<b>$&</b>');
                            item_id = 'cat_suggest_' + item.value;
                        }
                        return $('<li id="' + item_id + '"></li>').data("item.autocomplete", item).append(html).appendTo(ul);
                    };
                    item.autocomplete("search");
                }
            })
            arrow.click(function(){
                if(!jQuery('.ui-autocomplete').is(':visible'))
                {
                    item.trigger('mousedown');
                }
                else
                {
                    jQuery('.ui-autocomplete').hide();
                }
            })
            item.keyup(function() {
                if(this.value != '')
                {
                    item.autocomplete({
                        source: function (request, response) {
                            jQuery.post(mooConfig.url.base + '/businesses/suggest_global_category/', 'keyword=' + item.val(), function(data){
                                response(jQuery.parseJSON(data));
                            });
                        },
                        minLength: 3,
                        select: function (event, ui) {
                            item.val(ui.item.label);
                            if(ui.item.link)
                            {
                                window.location = ui.item.link;
                            }
                            return false;
                        },
                        focus: function( event, ui ) {
                            item.val( ui.item.label );
                            jQuery('.ui-autocomplete li').removeClass('selected');
                            if(ui.item.image)
                            {
                                jQuery('#business_suggest_' + ui.item.value).addClass('selected');
                            }
                            else
                            {
                                jQuery('#cat_suggest_' + ui.item.value).addClass('selected');
                            }
                            return false;  
                        } 
                    }).data("ui-autocomplete")._renderItem = function(ul, item) {
                        var html = '';
                        var item_id = '';
                        if(item.image)
                        {
                            html = 
                                '<a href="javascript:void(0)" onclick="window.location = ' + item.link + '">\n\
                                    <div class="user_avatar_small attached-image">' + item.image + '</div>\n\
                                    <div class="suggest_name">' + item.label + '</div>\n\
                                    <div class="suggest_more_info">' + item.address + '</div>\n\
                                </a>';
                            item_id = 'business_suggest_' + item.value;
                        }
                        else
                        {
                            var term = this.term;
                            //var regex = new RegExp("\\S*" + $.ui.autocomplete.escapeRegex(term) + "\\S*", "gi");
                            var regex = new RegExp($.ui.autocomplete.escapeRegex(term), "gi");
                            html = item.label.replace(regex, '<b>$&</b>');
                            item_id = 'cat_suggest_' + item.value;
                        }
                        return $('<li id="' + item_id + '"></li>').data("item.autocomplete", item).append(html).appendTo(ul);
                    };
                }
            })
        })
        
        jQuery('.global_search_category').keypress(function (e) {
            if(e.which == 13)  // the enter key code
            {
                var form = jQuery(this).closest('form');
                if(form.find('#btn_global_search').length > 0)
                {
                    form.find('#btn_global_search').trigger('click');
                }
                if(form.find('#btn_global_search_landing').length > 0)
                {
                    form.find('#btn_global_search_landing').trigger('click');
                }
                return false;  
            }
        });
    }
    
    ///////////////////////////////////////////search page///////////////////////////////////////////
    var markers = []
    var map = ''
    function initShowSearchMap(){
        jQuery(document).on('click', '#hide_search_map', function(){
            if(jQuery('#map_canvas').is(':visible'))
            {
                jQuery('#map_canvas').hide();
                jQuery(this).find('span').html(mooPhrase.__('business_text_show_map'));
            }
            else
            {
                if(jQuery('#map_canvas').html() == '')
                {
                    initSearchMap(jQuery('#locationDataTemplate').html());
                }
                jQuery('#map_canvas').show();
                jQuery(this).find('span').html(mooPhrase.__('business_text_hide_map'));
            }
        })
    }
    
    var initSearchPage = function()
    {
        initReviewStar();
        
        //hide search map
        initShowSearchMap();
        
        //search filter
        var app_no_tab = "";
        if(mooConfig.isApp)
        {
            app_no_tab = "&app_no_tab=1";
        }
        jQuery(document).on('change', '#search_filter', function(){
            var link = jQuery(this).data('current_link');
            var current_sort = jQuery(this).data('sort_by');
            var no_advanced_search = jQuery(this).data('no_advanced_search');
            var prefix = '&';
            if(no_advanced_search)
            {
                prefix = '?';
            }
            if(current_sort != '')
            {
                if(link.indexOf('sort_by=') != -1)
                {
                    link = link.replace('sort_by=' + current_sort, 'sort_by=' + jQuery(this).val());
                }
                else if(link.indexOf('?') == -1)
                {
                    link += '?sort_by=' + jQuery(this).val();
                }
                else
                {
                    link += '&sort_by=' + jQuery(this).val();
                }
                window.location = link + app_no_tab;
            }
            else
            {
                window.location = link + prefix + 'sort_by=' + jQuery('#sort_by').val() + app_no_tab;
            }
        })
        
        //show map marker
        jQuery(document).on('click', '.show_map_marker', function(){
            if(jQuery('#map_canvas').length > 0)
            {
                if(!jQuery('#map_canvas').is(':visible'))
                {
                    jQuery('#hide_search_map').trigger('click');
                }
                var index = jQuery(this).data('index');
                google.maps.event.trigger(markers[index], 'click');
                autoCenterMap(markers[index]);
                $('html,body').animate({
                    scrollTop: $("body").offset().top
                });
            }
        })
        
        //nearby city
        jQuery(document).on('click', '.nearby_city', function(){
            jQuery.post(mooConfig.url.base + '/businesses/global_search/', 'no_keyword=1&keyword=' + '&keyword_location=' + jQuery(this).data('name'), function(data){
                var json = jQuery.parseJSON(data);
                window.location = json.location;
            });
        })
    }
    
    var initSearchMap = function(locations)
    {
        locations = jQuery.parseJSON(locations.replace(/\s+/g," "));
        var myOptions = {
            zoom:16,
            //center:new google.maps.LatLng("10.7529379", "106.6698733"),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        map = new google.maps.Map(document.getElementById('map_canvas'), myOptions);

        var infowindow = new google.maps.InfoWindow();

        //add marker
        var marker, i;
        
        for (i = 0; i < locations.length; i++) {
            marker = new google.maps.Marker({
                position: new google.maps.LatLng(locations[i].lat, locations[i].lng),
                map: map,
               // icon: locations[i].marker_icon,
                //label: locations[i].marker_label,
            });

            //market info
            google.maps.event.addListener(marker, 'click', (function(marker, j) {
                return function() {
                    infowindow.setContent('<a><img src="' + locations[j].image + '" /></a><a href="' + locations[j].link + '">' + locations[j].name + '</a><br/>' + locations[j].address + '<br/><span class="review_star"><input readonly id="input-21e" value="' + locations[j].total_score + '" type="number" class="rating form-control hide" min="0" max="5" step="0.5" data-size="xs"></span>(' + locations[j].review_count + ')');
                    infowindow.open(map, marker);
                    
                }
            })(marker, i));
            markers.push(marker);
        }
        
        //init star rating
        google.maps.event.addListener(infowindow, 'domready', function() {
            initReviewStar();
        })
        
        //add cluster
        var custer_icon = mooConfig.url.base + '/business/images/cluster_icon.png';
        var mcOptions = {styles: [{
            height: 53,
            url: custer_icon,
            width: 53
        },
        {
            height: 56,
            url: custer_icon,
            width: 56
        },
        {
            height: 66,
            url: custer_icon,
            width: 66
        },
        {
            height: 78,
            url: custer_icon,
            width: 78
        },
        {
            height: 90,
            url: custer_icon,
            width: 90
        }]}
        var markerCluster = new MarkerClusterer(map, markers, mcOptions);
        autoCenterMap();
    }
    
    var autoCenterMap = function(marker)
    {
        //  Create a new viewpoint bound
        var bounds = new google.maps.LatLngBounds();
        
        if(typeof marker != 'undefined')
        {
            bounds.extend(marker.position);
        }
        else
        {
            //  Go through each...
            for (var i = 0; i < markers.length; i++) {
                bounds.extend(markers[i].position);
            }
        }
        //  Fit these bounds to the map
        map.fitBounds(bounds);
        if(typeof marker != 'undefined')
        {
            zoomChangeBoundsListener = 
                google.maps.event.addListenerOnce(map, 'bounds_changed', function(event) {
                    if (this.getZoom()){
                        this.setZoom(16);
                    }
            });
            setTimeout(function(){google.maps.event.removeListener(zoomChangeBoundsListener)}, 2000);
        }
    }
    
    ///////////////////////////////////////////business detail page///////////////////////////////////////////
    var initBusinessItem = function()
    {
        initDeleteBusiness();
        initReviewStar();
        //initShowSearchMap();
    }
    
    function initDeleteBusiness()
    {
        //delete business
        jQuery(document).on('click', '.delete_business', function(){
            var sMessage = mooPhrase.__('business_text_confirm_remove_business');
            if(jQuery(this).data('is_claim')){
                sMessage = mooPhrase.__('business_text_claim_remove');
            }
            var app_no_tab = "";
            if(mooConfig.isApp)
            {
                app_no_tab = "?app_no_tab=1";
            }
            if(parseInt(jQuery(this).data('parent_id')) > 0)
            {
                mooAlert.confirm(sMessage, mooConfig.url.base + '/business_branch/delete_branch/' + jQuery(this).data('parent_id') + '/' + jQuery(this).data('id') + '/my' + app_no_tab);
            }
            else 
            {
                mooAlert.confirm(sMessage, mooConfig.url.base + '/businesses/delete/' + jQuery(this).data('id') + app_no_tab);
            }
        })
        
        //delete branch
        jQuery(document).on('click', '.delete_branch', function(){
            mooAlert.confirm(mooPhrase.__('business_text_confirm_remove_branch'), jQuery(this).data('url'));
        })
    }
    
    var initAddFavourite = function()
    {
        jQuery(document).on('click', '.add_favourite', function(){
            var item = jQuery(this);
            if(item.data('remove') == 1)
            {
                mooConfirmBox(mooPhrase.__('business_text_confirm_remove_favourite'), function(){
                    jQuery.post(mooConfig.url.base + '/businesses/add_favourite/', 'id=' + item.data('id'), function(data){
                        item.closest('li.full_content').remove();
                    })
                })
            }
            else 
            {
                jQuery.post(mooConfig.url.base + '/businesses/add_favourite/', 'id=' + item.data('id'), function(data){
                    var json = jQuery.parseJSON(data);
                    if(json.result == 0)
                    {
                        mooAlert.alert(json.message);
                    }
                    else
                    {
                        item.attr('title', json.text);
                        item.html(json.icon);
                    }
                })
            }
        })
    }
    
    var initBusinessDetailPage = function(business_id, tab, review_id)
    {
        jQuery(document).ajaxComplete(function(event, xhr, settings) {
            if (settings.url === mooConfig.url.base + "/reports/ajax_save" ) {
                var json = jQuery.parseJSON(xhr.responseText);
                if(json.result == 1)
                {
                    jQuery('#businessModal').modal('hide');
                }
            }
        });
        if(typeof business_id == 'undefined')
        {
            business_id = '';
        }
        if(typeof tab == 'undefined')
        {
            tab = '';
        }
        
        //delete business
        initDeleteBusiness();
        
        //view phone fax
        jQuery(document).on('click', '.business_info', function(){
            var item = jQuery(this);
            jQuery.post(mooConfig.url.base + '/businesses/load_info/', 'id=' + item.data('id') + '&task=' + item.data('task'), function(data){
                item.before(data);
                item.remove();
            });
        })
        
        //add busines favourite
        initAddFavourite();
        
        //map center
        jQuery(document).on('load', '.map_center', function(){
            iFrameFitContent(this);
        })
        
        //remove connection
        jQuery(document).on('click', '.remove_connection', function(){
            requestConnection(jQuery(this).data('wrap_id'), jQuery(this).data('user_id'), jQuery(this).data('business_id'));
        })
        
        //cancel connection request
        jQuery(document).on('click', '.cancel_connection_request', function(){
            requestConnection(jQuery(this).data('wrap_id'), jQuery(this).data('user_id'), jQuery(this).data('business_id'));
        })
        
        //add connection
        jQuery(document).on('click', '.add_connection', function(){
            requestConnection(jQuery(this).data('wrap_id'), jQuery(this).data('user_id'), jQuery(this).data('business_id'));
        })
        
        //accept connection
        jQuery(document).on('click', '.accept_connection_request', function(){
            var id = jQuery(this).data('id');
            mooConfirmBox(mooPhrase.__('text_accept_connection'), function(){
                jQuery.post(mooConfig.url.base + '/business_connection/accept_connection_request/', 'id=' + id, function(data){
                    var json = jQuery.parseJSON(data);
                    if(json.result == 0)
                    {
                        mooAlert.alert(json.message);
                    }
                    else
                    {
                        jQuery('#accept_request_' + id).remove();
                        mooAlert.alert(json.message);
                    }
                });
            })
        })
        
        //review
        jQuery(document).on('click', '#btn_read_review', function(){
            jQuery('#review-items a').trigger('click');
            $('html,body').animate({
                scrollTop: $("#tabs").offset().top - 100
            });
        })
        
        //review dialog
        jQuery(document).on('click', '.btn_write_review', function(){
            console.log("bbb");
            reviewDialog(jQuery(this).data('business_id'), '', '', jQuery(this).data('view_detail'));
        })
        
        //edit review dialog
        jQuery(document).on('click', '.edit_review', function(){
            reviewDialog(jQuery(this).data('business_id'), jQuery(this).data('id'), jQuery(this).data('parent_id'));
        })
        
        //save review
        doReview();
        
        //delete review
        jQuery(document).on('click', '.delete_review', function(){
            deleteReview(jQuery(this).data('review_id'), jQuery(this).data('parent_id'));
        })
                
        //reply dialog
        jQuery(document).on('click', '.btn_reply', function(){
            reviewDialog(jQuery(this).data('business_id'), '', jQuery(this).data('id'));
        })
        
        //send contact
        jQuery(document).on('click', '#btnContact', function(){
            mooButton.disableButton('btnContact');
            jQuery.post(mooConfig.url.base + '/businesses/send_contact/', jQuery('#contactForm').serialize(), function(data){
                var json = jQuery.parseJSON(data);
                if(json.result == 0)
                {
                    jQuery('#contactMessage').empty().append(json.message).show();
                }
                else
                {
                    jQuery('#contactForm input[type=text]').val('');
                    jQuery('#contactForm textarea').val('');
                    jQuery('#contactMessage').hide();
                    if(jQuery('.captcha_box').length > 0)
                    {
                        grecaptcha.reset();
                    }
                    mooAlert.alert(json.message);
                }
                mooButton.enableButton('btnContact');
            });
        })
        
        //status action
        jQuery(document).on('click', '#btn_approve_business', function(){
            mooAlert.confirm(mooPhrase.__('business_text_confirm_approve_business'), jQuery(this).data('url'))
        })
        
        jQuery(document).on('click', '#btn_verify_business', function(){
            mooAlert.confirm(mooPhrase.__('business_text_confirm_vefiry_business'), jQuery(this).data('url'))
        })
        
        jQuery(document).on('click', '#btn_unverify_business', function(){
            mooAlert.confirm(mooPhrase.__('business_text_confirm_unvefiry_business'), jQuery(this).data('url'))
        })
        
        //reject business
        jQuery(document).on('click', '#rejectButton', function(){
            mooButton.disableButton('rejectButton');
            mooButton.disableButton('cancelRejectButton');
            jQuery.post(mooConfig.url.base + '/businesses/reject_business/', jQuery('#rejectForm').serialize(), function(data){
                var json = jQuery.parseJSON(data);
                if(json.result == 0)
                {
                    jQuery("#rejectMessage").show();
                    jQuery("#rejectMessage").html(json.message);
                    mooButton.enableButton('rejectButton');
                    mooButton.enableButton('cancelRejectButton');
                }
                else
                {
                    window.location = json.location;
                } 
            });
        })
        
        //report review
        businessReview();
        
        //show branch map
        jQuery(document).on('click', '#view_map', function(){
            viewMap(jQuery(this).data('address'));
        })
        
        //show hide review
        jQuery(document).on('click', '.btn_show_hide', function(){
            var item = jQuery('#' + jQuery(this).data('hide_id'));
            if(!item.is(':visible'))
            {
                item.show();
                //jQuery(this).html(mooPhrase.__('business_text_hide_reviews'));
                jQuery(this).html(jQuery(this).data('hide_text'));
            }
            else
            {
                item.hide();
                //jQuery(this).html(mooPhrase.__('business_text_show_reviews'));
                jQuery(this).html(jQuery(this).data('show_text'));
            }
            jQuery('html, body').animate({
                scrollTop: jQuery('#' + jQuery(this).data('hide_id')).offset().top - 20
            }, 500);
        });
        
        //delete blog
        jQuery(document).on('click', '.delete_blog', function(){
            mooAlert.confirm(mooPhrase.__('text_delete_blog'), mooConfig.url.base + '/business_blogs/delete/' + jQuery(this).data('business') + '/'  + jQuery(this).data('id'));
        })
        
        //tab
        if(tab == mooPhrase.__('BUSINESS_DETAIL_LINK_REVIEW'))
        {
            loadBusinessReviews(business_id, review_id);
        }
        else if(tab == mooPhrase.__('BUSINESS_DETAIL_LINK_PHOTO'))
        {
            loadBusinessPhotos(business_id);
        }
        else if(tab == mooPhrase.__('BUSINESS_DETAIL_LINK_BRANCH'))
        {
            //check set default value for branch
            loadListBranches(business_id);
        }
        else if(tab == mooPhrase.__('BUSINESS_DETAIL_LINK_CHECKIN'))
        {
            loadBusinessCheckin(business_id);
        }
        else if(tab == mooPhrase.__('BUSINESS_DETAIL_LINK_FOLLOWER'))
        {
            loadBusinessFollower(business_id);
            jQuery(document).on('click', '#btn_search_follower', function(){
                loadBusinessFollower(business_id);
            })
            jQuery('#followerForm').submit(function(e){
                e.preventDefault();
                loadBusinessFollower(business_id);
            })
        }
        else if(jQuery('#activities-content').length > 0)
        {
            loadBusinessActivities('business', business_id);
        }
        
        // show overview page on mobile
        // $('#show_overview_bus_mobile').click(function(){
        //     $('body').addClass('show_overview');
        //     mooResponsive.initFeedImage();
        // });

        $('#show_feed_bus_mobile').click(function(){
            $('body').addClass('show_feed_bus');
                mooResponsive.initFeedImage();
        });
        
        //report dialog
        jQuery(document).on('click', '.business_report', function () {
            jQuery.get(mooConfig.url.base + '/reports/ajax_create/Business_Business/' + jQuery(this).data('id'), function (data) {
                jQuery('#businessModal .modal-content').empty().append(data);
                jQuery('#businessModal').modal();
            })
        })
    }
    
    function businessReview()
    {
        //report review
        jQuery(document).on('click', '#reportReviewButton', function(){
            mooButton.disableButton('reportReviewButton');
            mooButton.disableButton('cancelReportReviewButton');
            jQuery.post(mooConfig.url.base + '/business_review/do_report/', jQuery('#reportReviewForm').serialize(), function(data){
                var json = jQuery.parseJSON(data);
                mooButton.enableButton('reportReviewButton');
                mooButton.enableButton('cancelReportReviewButton');
                if(json.result == 0)
                {
                    jQuery('#reportReviewMessage').html(json.message).show();
                }
                else
                {
                    jQuery('#businessModal').modal('hide');
                    mooAlert.alert(json.message);
                }
            })
        })
        
        //review_useful
        jQuery(document).on('click', '.review_useful', function(){
            var review_id = jQuery(this).data('review_id');
            jQuery.post(mooConfig.url.base + '/business_review/useful/' + review_id, '', function(data){
                var json = jQuery.parseJSON(data);
                if(json.result == 0)
                {
                    mooAlert.alert(json.message);
                }
                else
                {
                    jQuery('#useful_count_' + review_id).empty().append(json.total);
                    /*if(json.enable == 0)
                    {
                        jQuery('#btnUseful' + review_id).addClass('btn-action');
                    }
                    else
                    {
                        jQuery('#btnUseful' + review_id).removeClass('btn-action');
                    }*/
                }
            })
        })
    }
    
    function doReview()
    {
        //remove_review_image
        jQuery(document).on('click', '.remove_review_image', function(){
            var image_id = jQuery(this).data('image_id');
            jQuery('#attach' + image_id).remove();
            var ids = jQuery('#photo_review_delete_id').val();
            if(ids != '')
            {
                ids = ids.split(',');
            }
            else
            {
                ids = [];
            }
            if(jQuery.inArray(image_id, ids) == -1)
            {
                ids.push(image_id)
            }
            jQuery('#photo_review_delete_id').val(ids.join());
        })
        
        jQuery(document).on('click', '#reviewButton', function(){
            mooButton.disableButton('reviewButton');
            mooButton.disableButton('cancelReviewButton');
            var review_id = jQuery('#reviewForm').find('#review_id').val();
            var parent_id = jQuery('#reviewForm').find('#parent_id').val();
            var review_item = jQuery('#itemreview_' + review_id);
            var reply_item = jQuery('#itemreply_' + parent_id + '_' + review_id);
            jQuery.post(mooConfig.url.base + '/business_review/do_review/', jQuery('#reviewForm').serialize(), function(data){
                if(isJson(data))
                {
                    var json = jQuery.parseJSON(data);
                    if(json.location)
                    {
                        if(mooConfig.isApp)
                        {
                            window.location = json.location + "?app_no_tab=1";
                        }
                        else
                        {
                            window.location = json.location;
                        }
                    }
                    else
                    {
                        jQuery('#reviewMessage').empty().append(json.message).show();
                        mooButton.enableButton('reviewButton');
                        mooButton.enableButton('cancelReviewButton');
                    }
                }
                else
                {
                    if(parent_id > 0)
                    {
                        if(reply_item.length > 0)
                        {
                            reply_item.after(data).remove();
                        }
                        else
                        {
                            jQuery('#reply_content' + parent_id).prepend(data);
                        }
                    }
                    else
                    {
                        if(review_item.length > 0)
                        {
                            review_item.after(data).remove();
                        }
                        else
                        {
                            jQuery('#review_content').prepend(data);
                            jQuery('#my_review_content').prepend(data);
                        }
                    }

                    jQuery('#businessModal').modal('hide');
                    if(parent_id == 0)
                    {
                        //jQuery('.btnReview').hide();
                        jQuery('#messageReviewed').show();
                        initReviewStar();
                    }
                    else
                    {
                        jQuery('#btnReply_' + parent_id).hide();
                    }

                    //update counter on tab
                    updateTabCounter('review_count', '+');
                }
            });
        })
    }
    
    var doFollow = function(item, business_id)
    {
        jQuery.post(mooConfig.url.base + '/business_follow/do_follow/' + business_id, function(data){
            var json = jQuery.parseJSON(data);
            if(json.result == 0)
            {
                if(json.require_login)
                {
                    mooAlert.alert(json.message);
                }
                else
                {
                    mooAlert.alert(json.message);
                }
            }
            else
            {
                jQuery(item).empty().append(json.text);
            }
        });
    }
    
    var updateTabCounter = function(id, char)
    {
        var count = parseInt(jQuery('#' + id).html());
        if(char == '-')
        {
            var result = count - 1;
        }
        else
        {
            var result = count + 1;
        }
        jQuery('#' + id).html(result);
    }
    
    var requestConnection = function(item, owner_id, business_id)
    {
        jQuery.post(mooConfig.url.base + '/business_connection/request_connection/', 'owner_id=' + owner_id + '&business_id=' + business_id, function(data){
            var json = jQuery.parseJSON(data);
            if(json.result == 0)
            {
                mooAlert.alert(json.message);
            }
            else
            {
                jQuery('#' + item).empty().append(json.text);
            }
        });
    }
    
    var loadBusinessReviews = function(business_id, review_id)
    {
        jQuery.post(mooConfig.url.base + '/business_review/load_business_reviews/' + business_id + '/' + review_id, '', function(data){
            jQuery('#review_content').empty().append(data);
            initBusinessReviewData();
        });
        
        //report dialog
        jQuery(document).on('click', '.business_review_report', function () {
            jQuery.get(mooConfig.url.base + '/business_review/report/' + jQuery(this).data('id'), function (data) {
                jQuery('#businessModal .modal-content').empty().append(data);
                jQuery('#businessModal').modal({
                    backdrop : 'static'
                });
            })
        })
    }
    
    var initBusinessReviewData = function(){
        initReviewStar();
        if ($('.bus_review_photo a').length){
            $('.bus_review_photo a').magnificPopup({
                type:'image',
                gallery: { enabled: false },
                zoom: {
                    enabled: true,
                    opener: function(openerElement) {
                        return openerElement;
                    }
                }
            });
        }
        mooTooltip.init();
    }
    
    var loadBusinessPhotos = function(business_id)
    {
        jQuery.post(mooConfig.url.base + '/business_photo/load_business_photos/' + business_id, '', function(data){
            jQuery('#photos-content').empty().append(data);
        });
    }
    
    var loadListBranches = function(business_id)
    {
        jQuery.post(mooConfig.url.base + '/business_branch/load_business_branches/' + business_id, '', function(data){
            jQuery('#branches-content').empty().append(data);
            initReviewStar();
        });
    }
    
    var loadBusinessCheckin = function(business_id)
    {
        jQuery.post(mooConfig.url.base + '/business_checkin/load_business_checkin/' + business_id, '', function(data){
            jQuery('#checkin-content').empty().append(data);
            mooTooltip.init();
        });
    }
    
    var loadBusinessFollower = function(business_id)
    {
        jQuery.post(mooConfig.url.base + '/business_follow/load_business_followers/' + business_id, jQuery('#followerForm').serialize(), function(data){
            jQuery('#follower-content').empty().append(data);
            initBanFollower();
            mooTooltip.init();
        });
        
        if(mooConfig.isApp)
        {
            //cancel friend request
            jQuery(document).on('click', '.cancel_friend_request', function (e) {
                jQuery.post(mooConfig.url.base + '/friends/ajax_cancel/' + jQuery(this).data('id') + '/0', function(data){
                    location.reload(); 
                });
            })
            
            //show popup
            jQuery(document).on('click', '[data-toggle="modal"]', function (e) {
                var link = jQuery(this).attr('href');
                setTimeout(function () {
                    jQuery.get(link, function(data){
                        jQuery('#businessModal .modal-content').empty().append(data);
                        jQuery('#businessModal').modal();
                    })
                }, 1);
            })
        }
    } 
    
    var loadBusinessActivities = function(task, target_id, activity_id)
    {
        if(typeof target_id == 'undefined')
        {
            target_id = '';
        }
        if(typeof activity_id != 'undefined' && activity_id != '')
        {
            activity_id = 'activity_id:' + activity_id;
        }
        else
        {
            activity_id = '';
        }
        jQuery.post(mooConfig.url.base + '/business_activities/load_activities/' + task + '/' + target_id  + '/' + activity_id, '', function(data){
            jQuery('#activities-content ul').empty().append(data);
            mooResponsive.init();
            initReviewStar();
            //initFancyBox();
            mooActivities.init();
            mooShare.init();
            mooBehavior.initMoreResults();
            mooBehavior.registerImageComment();
            mooTooltip.init();
            $(".tip").tipsy({html: true, gravity: $.fn.tipsy.autoNS,follow: 'x'});
        });
        
        if(mooConfig.isApp)
        {
            jQuery(document).ajaxComplete(function (event, xhr, settings) {
                if (settings.url.indexOf('/business_activities/load_activities/') != -1)
                {
                    jQuery('#activities-content ul').find('.dropdown-menu li a').removeAttr('data-toggle');
                }
            })
            jQuery(document).on('click', '#activities-content ul .dropdown-menu li [data-backdrop="true"]', function (e) {
                e.preventDefault();
                var link = jQuery(this).attr('href');
                setTimeout(function () {
                    jQuery.get(link, function(data){
                        jQuery('#businessModal .modal-content').empty().append(data);
                        jQuery('#businessModal').modal();
                    })
                }, 1);
            })
        }
    }
    
    var initBanFollower = function(){
        jQuery(document).on('click', '#btn_ban_follower', function(){
            var item = jQuery(this);
            jQuery.post(mooConfig.url.base + '/business_follow/ban_follower/' + item.data('business') + '/' + item.data('user'), '', function(data){
                data = jQuery.parseJSON(data);
                if(data.result == 0)
                {
                    mooAlert(data.message);
                }
                else
                {
                    item.html(mooPhrase.__('business_text_unban'));
                    item.attr('id', 'btn_unban_follower');
                }
            })
        })
        
        jQuery(document).on('click', '#btn_unban_follower', function(){
            var item = jQuery(this);
            jQuery.post(mooConfig.url.base + '/business_follow/unban_follower/' + item.data('business') + '/' + item.data('user'), '', function(data){
                data = jQuery.parseJSON(data);
                if(data.result == 0)
                {
                    mooAlert(data.message);
                }
                else
                {
                    item.html(mooPhrase.__('business_text_ban'));
                    item.attr('id', 'btn_ban_follower');
                }
            })
        })
    }
    
    var loadListConnections = function(task, business_id)
    {
        jQuery.post(mooConfig.url.base + '/business_connection/load_list_connections/' + task + '/' + business_id, '', function(data){
            jQuery('#connections-content').empty().append(data);
        });
    }
    
    var initFancyBox = function()
    {
        jQuery("a.fancybox").fancybox({
            'transitionIn'	:	'elastic',
            'transitionOut'	:	'elastic',
            'speedIn'		:	600, 
            'speedOut'		:	200, 
            'overlayShow'	:	false
        });
    }
    
    var reviewDialog = function(business_id, review_id, parent_id, view_detail, my_review)
    {
        if(typeof review_id == 'undefined' || review_id == 0)
        {
            review_id = '';
        }
        if(typeof view_detail == 'undefined')
        {
            view_detail = '';
        }
        if(typeof my_review == 'undefined')
        {
            my_review = '';
        }
        jQuery.post(mooConfig.url.base + '/business_review/review_dialog/', 'business_id=' + business_id + '&review_id=' + review_id + '&parent_id=' + parent_id + '&view_detail=' + view_detail + '&my_review=' + my_review, function(data){
            jQuery('#businessModal .modal-content').empty().append(data);
            jQuery('#businessModal').modal({
                backdrop: 'static'
            });
            initBusinessPhotoUploader();
            
            //init star
            var input = jQuery('input.rating'), count = Object.keys(input).length;
            if (count > 0) {
                input.rating('refresh', {
                    showClear: false, 
                    showCaption: false,
                    max: 5,
                    size:'md',
                    step: 1
                });
            }
            
            //review emotion
            jQuery('#business_user_review').on('rating.change', function(event, value, caption, target) {
				jQuery('#reviewForm .review_star #rating').val(value)
                jQuery('#default_emotion').val(jQuery(caption).text());
                jQuery('#emotion').val(jQuery(caption).text());
                
            });
        });
    }
    
    function deleteReview(review_id, parent_id, redirect)
    {
        mooConfirmBox(mooPhrase.__('business_text_confirm_remove_review'), function(){
            if(typeof review_id == 'undefined' || review_id == 0)
            {
                review_id = '';
            }
            jQuery.post(mooConfig.url.base + '/business_review/delete_review/' + review_id, '', function(data){
                var json = jQuery.parseJSON(data);
                if(json.result == 0)
                {
                    mooAlert.alert(json.message);
                }
                else
                {
                    if(typeof redirect != 'undefined' && redirect == 1 && json.redirect)
                    {
                        window.location = json.redirect;
                    }
                    else
                    {
                        jQuery('#itemreview_' + review_id).remove();
                        jQuery('#itemreply_' + parent_id + '_' + review_id).remove();
                        jQuery('.btnReview').show();
                        jQuery('#btnReply_' + parent_id).show();
                        jQuery('#messageReviewed').hide();
                        //update counter on tab
                        updateTabCounter('review_count', '-');
                        mooAlert.alert(json.message);
                        if(jQuery('#my_review_content').length > 0 && jQuery('#my_review_content li').length == 0 && jQuery('#my_review_content .view-more').length == 0)
                        {
                            jQuery('#my_review_content').before(mooPhrase.__('business_text_no_reviews')).remove();
                        }
                    }
                }
            });
        })
    }
    
    var getDirection = function()
    {
        jQuery(document).on('click', '.get_direction', function(){
            jQuery('#businessModal .modal-dialog').css('width', '1200px');
            jQuery('#businessModal').modal();
            jQuery.post(mooConfig.url.base + "/businesses/view_map", 'address=' + jQuery(this).data('address') + '&direction=1', function(data){
                jQuery('#businessModal .modal-content').empty().append(data);
            });
        })
    }
    
    var getParking = function()
    {
        jQuery(document).on('click', '.get_parking', function(){
            jQuery.post(mooConfig.url.base + "/businesses/view_parking", 'address=' + jQuery(this).data('address') + '&direction=1', function(data){
                jQuery('#businessModal .modal-content').empty().append(data);
                jQuery('#businessModal').modal();
            });
        })
    }
    
    ///////////////////////////////////////////create business///////////////////////////////////////////
    var initCreateBusiness = function(add_default_hour, add_default_cat)
    {
        if(typeof google.maps.places !== 'undefined')
        {
            var autocomplete = new google.maps.places.Autocomplete(document.getElementById('address'));
        }
        loadCategoryData();
        initSuggestBusinessCategory();
        initBusinessUploader();
        //rebuildDayInWeek();
        if(add_default_hour == 1)
        {
            addMultiItem('openHours', 'hours-content');
        }
        /*if(add_default_cat == 1)
        {
            addMultiItem('categoryList', 'categories-content', 1);
        }*/
        alwaysOpen();
        checkTime();
        
        //add multi item
        jQuery(document).on('click', '.btn_add_item', function(){
            if(!jQuery(this).hasClass('disabled'))
            {
                addMultiItem(jQuery(this).data('wrapper_id'), jQuery(this).data('content_id'), jQuery(this).data('suggest_cat')); 
            }
        })
        
        //remove multi item
        jQuery(document).on('click', '.btn_remove_item', function(){
            if(!jQuery(this).hasClass('disabled'))
            {
                removeMultiItem(this); 
            }
        })
        
        //add shift
        jQuery(document).on('click', '.btn_add_shift', function(){
            if(!jQuery(this).hasClass('disabled'))
            {
                addShiftItem(jQuery(this)); 
            }
        })
        
        //remove shift
        jQuery(document).on('click', '.btn_remove_shift', function(){
            if(!jQuery(this).hasClass('disabled'))
            {
                removeShiftItem(jQuery(this)); 
            }
        })
        
        //change day
        // jQuery(document).on('change', '#openHours .multi_item #day', function(){
        //     rebuildDayInWeek();
        // })
        
        //view_map
        jQuery(document).on('click', '#view_map', function(){
            viewMap();
        })
        
        //always_open
        jQuery(document).on('click', '#always_open', function(){
            alwaysOpen();
        })
        
        //save busienss
        jQuery(document).on('click', '#createBusinessButton', function(){
            $('#createBusinessButton').spin('small');
            mooButton.disableButton('createBusinessButton');
            mooButton.disableButton('cancelBusinessButton');
            jQuery("#errorMessage").hide();

            if(tinyMCE.activeEditor !== null){
                $('#editor').val(tinyMCE.activeEditor.getContent());
            }

            //save data
            jQuery.post(mooConfig.url.base + "/businesses/save", jQuery("#formBusiness").serialize(), function(data){
                var json = jQuery.parseJSON(data);
                if(json.result == 0)
                {
                    jQuery("#errorMessage").show();
                    jQuery("#errorMessage").html(json.message);
                    $('#createBusinessButton').spin(false);
                    mooButton.enableButton('createBusinessButton');
                    mooButton.enableButton('cancelBusinessButton');
                }
                else
                {
                    if(mooConfig.isApp)
                    {
                        if(json.pending || json.claim)
                        {
                            window.location = json.location + '?app_no_tab=1';
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
        })
        
        //claimNotReady
        jQuery(document).on('click', '.claimNotReady', function(){
            mooAlert.alert(mooPhrase.__('warning_accept'));
        });
        
        //claimReviewing
        jQuery(document).on('click', '#claimReviewing', function(){
            
            // hide message
            jQuery("#errorMessage").hide();
            
            // confirm
            $.fn.SimpleModal({
                btn_ok : mooPhrase.__('btn_ok'),
                btn_cancel: mooPhrase.__('cancel'),
                model: 'confirm',
                callback: function(){
                    // disbale button
                    mooButton.disableButton('claimReviewing');
                    $('#claimReviewing').spin('small');
            
                    //save data
                    jQuery.post(mooConfig.url.base + "/businesses/claims/review/", jQuery("#formBusiness").serialize(), function(data){
                        var json = jQuery.parseJSON(data);
                        if(json.result == 0)
                        {
                            jQuery("#errorMessage").html(json.message).show();
                            mooButton.disableButton('claimReviewing');
                            $('#claimReviewing').spin(false);
                        }
                        else
                        {
                            if(mooConfig.isApp)
                            {
                                window.mobileAction.backAndRefesh();
                            }
                            else
                            {
                                window.location = json.location;
                            }
                        } 
                    });
                },
                title: 'Please Confirm',
                contents: mooPhrase.__('business_text_claim_review'),
                hideFooter: false,
                closeButton: false
            }).showModal();
            
        });
        
        //claimReject
        jQuery(document).on('click', '#claimReject', function(){
            mooAlert.confirm(mooPhrase.__('business_text_claim_reject'), mooConfig.url.base + '/businesses/claims/reject/' + jQuery(this).data('business_id'));
        });
        
        //claimRemove
        jQuery(document).on('click', '#claimRemove', function(){
            var link = mooConfig.url.base + '/businesses/claims/remove/' + jQuery(this).data('business_id');
            if(mooConfig.isApp)
            {
                link += "?app_no_tab=1";
            }
            mooAlert.confirm(mooPhrase.__('business_text_claim_remove'), link);
        });
        
        //claimSubmit
        jQuery(document).on('click', '#claimSubmit', function(){
            mooButton.disableButton('claimSubmit');
            jQuery("#errorMessage").hide();
            var business_id = jQuery(this).data('business_id');
            //save data
            jQuery.post(mooConfig.url.base + "/businesses/claims/submit", 'data[id]=' + business_id, function(data){
                var json = jQuery.parseJSON(data);
                if(json.result == 0)
                {
                    jQuery("#errorMessage").html(json.message).show();
                    mooButton.enableButton('claimSubmit');
                }
                else
                {
                    if(mooConfig.isApp)
                    {
                        window.mobileAction.backAndRefesh();
                    }
                    else
                    {
                        window.location = json.location;
                    }
                } 
            });
        });
    }
    
    var viewMap = function(address)
    {
        if(typeof address == 'undefined')
        {
            address = jQuery('#address').val();
        }
        jQuery('#businessModal .modal-content').empty().append('');
        jQuery('#businessModal').modal();
        jQuery.post(mooConfig.url.base + "/businesses/view_map", 'address=' + address, function(data){
            jQuery('#businessModal .modal-content').empty().append(data);
        });
    }

    function checkTime()
    {
        $('.time_close').each(function(){
            var id = $(this).closest('.multi_item').data('id');
            var index = $('.multi_item[data-id=' + id +'] .time_open option:selected').index();
            $('.multi_item[data-id=' + id +'] .time_close').find('option:lt(' + index + ')').hide();
        });

        $(document).on('change', '.time_open', function(){
            var id = $(this).closest('.multi_item').data('id');
            var time_open = $(this).val();
            var time_close = $('.multi_item[data-id=' + id +'] .time_close').val();

            $('.multi_item[data-id=' + id +'] .time_close option').show();
            var index = $('.multi_item[data-id=' + id +'] .time_open option:selected').index();
            $('.multi_item[data-id=' + id +'] .time_close').find('option:lt(' + index + ')').hide();

            if (time_open >= time_close)
            {
                var close_new = $('.multi_item[data-id=' + id +'] .time_open option:selected').next().val();
                $('.multi_item[data-id=' + id +'] .time_close').val(close_new);
            }
        });
    }
    
    function rebuildDayInWeek()
    {
        //reset 
        jQuery('#openHours .multi_item:not(.shift) #day:visible option').show().removeClass('option_hidden');
        
        //rebuild
        var selected_day = [];
        jQuery('#openHours .multi_item:not(.shift) #day:visible').each(function(){
            if(jQuery.inArray(jQuery(this).val(), selected_day) == -1)
            {
                selected_day.push(jQuery(this).val());
            }
        });

        jQuery('#openHours .multi_item:not(.shift) #day:visible').each(function(){
            var select_value = jQuery(this).val();
            jQuery(this).find('option').each(function(){
                if(jQuery.inArray(jQuery(this).val(), selected_day) > -1 && jQuery(this).val() != select_value)
                {
                    jQuery(this).hide().addClass('option_hidden');
                }
            })
        });
    }
    
    function checkFullDay()
    {
        if(jQuery('#openHours .multi_item:not(.shift)').length == 7)
        {
            jQuery('#openHours .multi_item:first .btn_add_item').hide();
        }
    }

    var item_quantity = jQuery('#openHours .multi_item').length;
    
    function addMultiItem(wrapper_id, content_id, suggest_cat)
    {
        item_quantity++;
        var item = jQuery(jQuery('#' + content_id).html());
        jQuery('#' + wrapper_id).append(item);
        jQuery('#openHours .multi_item:last').attr('data-id', item_quantity);
        initTimer();
        
        var count = 0;
        jQuery('#' + wrapper_id).find('.multi_item:not(.shift)').each(function(){
            count++;
            if(count == 1)
            {
                jQuery(this).find('.btn_add_item').show();
                jQuery(this).find('.btn_remove_item').hide();
            }
            else
            {
                jQuery(this).find('.btn_add_item').hide();
                jQuery(this).find('.btn_remove_item').show();
            }
        });
        
        //for hours only
        if(wrapper_id == 'openHours')
        {
            var prev_item = item.prev();
            if(jQuery('#openHours .multi_item:not(.shift)').length > 1)
            {
                item.find('#time_open').val(prev_item.find('#time_open').val());
                item.find('#time_close').val(prev_item.find('#time_close').val());
                
                var index = '';
                item.find('#day option').each(function(e){
                    if(jQuery(this).attr('value') == prev_item.find('#day').val())
                    {
                        index = jQuery(this).index() + 1;
                        return false;
                    }
                })

                // var index = parseInt(prev_item.find('#day')[0].selectedIndex) + 1;
                item.find('#day option').eq(index).prop('selected', true);
            }            
            // checkFullDay();
        }
        
        if(typeof suggest_cat != 'undefined' && suggest_cat == 1)
        {
            initSuggestBusinessCategory();
        }
        
        //rebuild day
        //rebuildDayInWeek();
    }
    
    function removeMultiItem(item)
    {
        jQuery(item).closest('.multi_item').remove();

        item_quantity--;
        //for hours only
        // if(jQuery('#openHours .multi_item:not(.shift)').length < 7)
        // {
        //     jQuery('#openHours .multi_item:first').find('.btn_add_item').show();
        // }
        
        //rebuild day
        //rebuildDayInWeek();
    }
    
    function addShiftItem(item)
    {
        var old_select = item.closest('.multi_item').find('#day').val();
        var new_item = item.closest('.multi_item').clone();
        new_item.find('#day').hide();
        new_item.addClass('shift');
        new_item.find('.btn_add_shift').hide();
        new_item.find('.btn_add_item').hide();
        new_item.find('.btn_remove_item').hide();
        new_item.find('.btn_remove_shift').show();
        new_item.find('#day').val(old_select)
        item.closest('.multi_item').after(new_item);
    }
    
    function removeShiftItem(item)
    {
        jQuery(item).closest('.multi_item').remove();
    }
    
    var alwaysOpen = function()
    {
        if(jQuery('#always_open').is(':checked'))
        {
            jQuery('#openHours select').attr('disabled', 'disabled');
            jQuery('#openHours input').attr('disabled', 'disabled');
            jQuery('#openHours .add_more a').addClass('disabled');
        }
        else
        {
            jQuery('#openHours select').removeAttr('disabled');
            jQuery('#openHours input').removeAttr('disabled');
            jQuery('#openHours .add_more a').removeClass('disabled');
        }
        initTimer();
        //checkFullDay();
    }
    
    var initSuggestBusinessCategory = function()
    {
        jQuery('.cat_id').each(function(){
            var item = jQuery(this);
            jQuery(this).autocomplete({
                source: function (request, response) {
                    jQuery.post(mooConfig.url.base + '/businesses/suggest_category/', 'keyword=' + item.val(), function(data){
                        if(data != 'null')
                        {
                            response(jQuery.parseJSON(data));
                        }
                        else
                        {
                            item.parent().find('.category_id').val('');
                        }
                    });
                },
                search: function(event,ui) {  
                    //$("#customerOrganizationId").val(ui.item.value ? ui.item.value : "");
                },
                minLength: 3,
                messages: {
                    noResults: '',
                    results: function() {}
                },
                select: function (event, ui) {
                    item.val(ui.item.label);
                    item.parent().find('.category_id').val(ui.item.value);
                    return false;
                },
                focus: function( event, ui ) {
                    item.val( ui.item.label );
                    jQuery('.ui-autocomplete li').removeClass('selected');
                    jQuery('#cat_suggest_' + ui.item.value).addClass('selected');
                    return false;  
                },  
            }).data("ui-autocomplete")._renderItem = function(ul, item) {
                var term = this.term;
                //var regex = new RegExp("\\S*" + $.ui.autocomplete.escapeRegex(term) + "\\S*", "gi");
                var regex = new RegExp($.ui.autocomplete.escapeRegex(term), "gi");
                var label = item.label.replace(regex, '<b>$&</b>');
                return $('<li id="cat_suggest_' + item.value + '"></li>').append(label).appendTo(ul);
            };;
        });
    }
    
    var initBusinessUploader= function()
    {
        if($('#select-0').length > 0)
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
                        endpoint: mooConfig.url.base + "/businesses/upload_photo/"
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
                            jQuery('#logo').val(response.filename);
                            jQuery('#business_logo').attr('src', response.path).show();
                        }
                    }
                }}
            });
        }
    }
    
    var initTimer = function()
    {
        jQuery('#openHours .timer').each(function(){
            jQuery(this).timeEntry({
                show24Hours: true,
                spinnerImage: ''
            })
        })
    }
    
    //////////////////////////////////////dashboard business photo//////////////////////////////////////
    var initBusinessPhoto = function(type, target_id)
    {
        initBusinessPhotoUploader(type, target_id);
        
        //save photo
        jQuery(document).on('click', '#saveButton', function(){
            mooButton.disableButton('triggerUpload');
            mooButton.disableButton('saveButton');
            jQuery.post(mooConfig.url.base + "/business_photo/save_photos", jQuery("#formBusinessPhotos").serialize(), function(data){
                var json = jQuery.parseJSON(data);
                if(json.result == 0)
                {
                    jQuery("#errorMessage2").show();
                    jQuery("#errorMessage2").html(json.message);
                    mooButton.enableButton('triggerUpload');
                    mooButton.enableButton('saveButton');
                }
                else
                {
                    if(mooConfig.isApp)
                    {
                        window.mobileAction.backAndRefesh();
                    }
                    else
                    {
                        window.location = json.location;
                    }
                } 
            });
        })
    }
    
    var initBusinessPhotoUploader = function(type, target_id)
    {
        //init uploader
        var errorHandler = function(event, id, fileName, reason) {
            if ($('#attachments_upload .qq-upload-list .errorUploadMsg').length > 0){
                $('#attachments_upload .qq-upload-list .errorUploadMsg').html(mooPhrase.__('tmaxsize'));
            }else {
                $('#attachments_upload .qq-upload-list').prepend('<div class="errorUploadMsg">'+ mooPhrase.__('tmaxsize') +'</div>');
            }
            $('#attachments_upload .qq-upload-fail').remove();
        }; 

        if($('#business_uploader').length > 0) {
            var uploader = new mooFileUploader.fineUploader({
                element: $('#business_uploader')[0],
                autoUpload: false,
                text: {
                    uploadButton: '<div class="upload-section"><i class="material-icons">photo_camera</i>'+mooPhrase.__('tdesc') +'</div>'
                },
                validation: {
                    allowedExtensions: ['jpg', 'jpeg', 'gif', 'png'],
                },
                request: {
                    endpoint: mooConfig.url.base+ "/photo/photo_upload/album/" + type + "/" + target_id
                },
                callbacks: {
                    onError: errorHandler,
                    onComplete: function(id, fileName, response) {
                        if (response.filename)
                        {
                            if($('#attachments').length > 0)
                            {
                                var attachs = $('#attachments').val();
                                if (attachs == '')
                                {
                                    $('#attachments').val( response.photo );
                                }
                                else
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
                        }
                        else if(response.result == 0)
                        {
                            mooAlert.alert(response.message);
                        }
                    }
                }
            });
            
            $('#triggerUpload').click(function() {
                uploader.uploadStoredFiles();
            });
        }
        
        jQuery(document).on('click', '.photo_edit_checkbox', function(){
            var id = jQuery(this).data('id');
            var photos = jQuery('#photo_delete_id').val();
            if(photos != '')
            {
                photos = photos.split(',');
            }
            else
            {
                photos = [];
            }
            if(jQuery.inArray(id, photos) == -1)
            {
                photos.push(id);
            }
            jQuery('#photo_delete_id').val(photos.join());
            jQuery(this).closest('li').remove();
        })
    }
    
    //////////////////////////////////////dashboard business admin//////////////////////////////////////
    var initBusinessAdmin = function(business_id)
    {
        //suggest admin
        var item = jQuery('#suggest_admin');
        item.autocomplete({
            source: function (request, response) {
                jQuery.post(mooConfig.url.base + '/business_admin/suggest_admin/' + business_id, 'keyword=' + item.val(), function(data){
                    if(data != '[]')
                    {
                        response(jQuery.parseJSON(data));
                    }
                    else
                    {
                        jQuery('#business_admin_id').val('');
                    }
                });
            },
            minLength: 3,
            select: function (event, ui) {
                item.val(ui.item.label);
                jQuery('#business_admin_id').val(ui.item.value);
                return false;
            },
            focus: function (event, ui) {
                item.val(ui.item.label);
                jQuery('#business_admin_id').val(ui.item.value);
                jQuery('.ui-autocomplete li').removeClass('selected');
                jQuery('#admin_suggest_' + ui.item.value).addClass('selected');
                return false;
            }
        }).data("ui-autocomplete")._renderItem = function(ul, item) {
            var term = this.term;
            //var regex = new RegExp("\\S*" + $.ui.autocomplete.escapeRegex(term) + "\\S*", "gi");
            var regex = new RegExp($.ui.autocomplete.escapeRegex(term), "gi");
            var label = item.label.replace(regex, '<b>$&</b>');
            
            return $('<li id="admin_suggest_' + item.value + '" style="overflow: hidden;"></li>')
                    .append('<a class="img_wrapper2" href="javascript:void(0)"><img class="user_avatar_small" src="' + item.image + '"/></a>')
                    .append('<div class="suggest-right" style="margin-left: 40px">' + label + '</div>').appendTo(ul);
        };;
        
        //load admin list
        jQuery.post(mooConfig.url.base + '/business_admin/load_admin/' + business_id, '', function(data){
            jQuery('#admin_content').append(data);
        });
        
        //add admin
        jQuery(document).on('click', '#addAdminButton', function(){
            mooButton.disableButton('addAdminButton');
            jQuery.post(mooConfig.url.base + '/business_admin/add_admin/', jQuery('#adminForm').serialize(), function(data){
                mooButton.enableButton('addAdminButton');
                if(isJson(data))
                {
                    var json = jQuery.parseJSON(data);
                    jQuery('#adminMessage').empty().append(json.message).show();
                }
                else
                {
                    jQuery('#noResult').hide();
                    jQuery('#adminMessage').hide();
                    jQuery('#business_admin_id').val('');
                    jQuery('#suggest_admin').val('');
                    //jQuery('#admin_content').append(data);
                    //load admin list
                    jQuery.post(mooConfig.url.base + '/business_admin/load_admin/' + business_id, '', function(data){
                        jQuery('#admin_content').empty().append(data);
                    });
                }
            });
        })
        
        //remove admin
        jQuery(document).on('click', '.remove_business_admin', function(){
            var business_id = jQuery(this).data('business_id');
            var user_id = jQuery(this).data('user_id');
            jQuery.post(mooConfig.url.base + '/business_admin/remove_admin/', 'business_id=' + business_id + '&user_id=' + user_id, function(data){
                var json = jQuery.parseJSON(data);
                if(json.result == 0)
                {
                    mooAlert.alert(json.message);
                }
                else
                {
                    //jQuery('#admin_item_' + user_id).remove();
                    //load admin list
                    jQuery.post(mooConfig.url.base + '/business_admin/load_admin/' + business_id, '', function(data){
                        jQuery('#admin_content').empty().append(data);
                    });
                }
            });
        })
    }
    
    //////////////////////////////////////dashboard business admin permission//////////////////////////////////////
    var initBusinessPermission = function(){
        jQuery(document).on('click', '#saveButton', function(){
            mooButton.disableButton('saveButton');
            jQuery.post(mooConfig.url.base + '/business_admin/save_permission/', jQuery('#adminForm').serialize(), function(data){
                mooButton.enableButton('saveButton');
                var json = jQuery.parseJSON(data);
                if(json.result == 0)
                {
                    jQuery('#adminMessage').empty().append(json.message).show();
                }
                else 
                {
                    location.reload(); 
                }
            });
        })
    }
    
    //////////////////////////////////////dashboard business branch//////////////////////////////////////
    var initBusinessBranch = function(add_default_hour)
    {
        if(typeof google.maps.places !== 'undefined')
        {
            var autocomplete = new google.maps.places.Autocomplete(document.getElementById('address'));
        }
        loadCategoryData();
        initSuggestBusinessCategory();
        initBusinessUploader();
        initBusinessPhotoUploader();
        if(add_default_hour == 1)
        {
            addMultiItem('openHours', 'hours-content');
        }
        alwaysOpen();
        checkTime();
        
        //add multi item
        jQuery(document).on('click', '.btn_add_item', function(){
            if(!jQuery(this).hasClass('disabled'))
            {
                addMultiItem(jQuery(this).data('wrapper_id'), jQuery(this).data('content_id'), jQuery(this).data('suggest_cat')); 
            }
        })
        
        //remove multi item
        jQuery(document).on('click', '.btn_remove_item', function(){
            if(!jQuery(this).hasClass('disabled'))
            {
                removeMultiItem(this); 
            }
        })
        
        //add shift
        jQuery(document).on('click', '.btn_add_shift', function(){
            if(!jQuery(this).hasClass('disabled'))
            {
                addShiftItem(jQuery(this)); 
            }
        })
        
        //remove shift
        jQuery(document).on('click', '.btn_remove_shift', function(){
            if(!jQuery(this).hasClass('disabled'))
            {
                removeShiftItem(jQuery(this)); 
            }
        })
        
        //view_map
        jQuery(document).on('click', '#view_map', function(){
            viewMap();
        })
        
        //always_open
        jQuery(document).on('click', '#always_open', function(){
            alwaysOpen();
        })
        
        //save branch
        jQuery(document).on('click', '#createButton', function(){
            mooButton.disableButton('createButton');
            mooButton.disableButton('cancelButton');
            jQuery("#errorMessage").hide();

            if(tinyMCE.activeEditor !== null){
                $('#editor').val(tinyMCE.activeEditor.getContent());
            }

            //save data
            jQuery.post(mooConfig.url.base + "/business_branch/save_branch", jQuery("#formBranch").serialize(), function(data){
                var json = jQuery.parseJSON(data);
                if(json.result == 0)
                {
                    jQuery("#errorMessage").show();
                    jQuery("#errorMessage").html(json.message);
                    mooButton.enableButton('createButton');
                    mooButton.enableButton('cancelButton');
                }
                else
                {
                    if(mooConfig.isApp)
                    {
                        if(jQuery('#back_and_refresh').val() == 1)
                        {
                            window.mobileAction.backAndRefesh();
                        }
                        else
                        {
                            window.location = json.location + "?app_no_tab=1";
                        }
                    }
                    else
                    {
                        window.location = json.location;
                    }
                } 
            });
        })
        
        jQuery(document).on('click', '.delete_branch', function(){
            mooAlert.confirm(mooPhrase.__('business_text_confirm_remove_branch'), jQuery(this).data('url'));
        })
    }
    
    function loadCategoryData()
    {
        if(jQuery('#categories-data').length > 0 && jQuery('#categories-data').html().trim() != '')
        {
            var data = jQuery.parseJSON(jQuery('#categories-data').html().trim());
            for(var i in data)
            {
                var item = data[i];
                var new_item = jQuery(jQuery('#categories-content').html());
                new_item.find('.cat_id').val(item.path_name);
                new_item.find('.category_id').val(item.id);
                if(i == 0)
                {
                    new_item.find('.btn_remove_item').hide();
                }
                else
                {
                    new_item.find('.btn_add_item').hide();
                }
                jQuery('#categoryList').append(new_item);
            }
        }
        else
        {
            var new_item = jQuery(jQuery('#categories-content').html());
            new_item.find('.btn_remove_item').hide();
            jQuery('#categoryList').append(new_item);
        }
    }
    
    //////////////////////////////////////dashboard business branch//////////////////////////////////////
    var initBusinessVerify = function()
    {
        var iItemLimit = 5;
        var newPhotos = new Array();
        var uploaderVerify = new mooFileUploader.fineUploader({
            element: $('#photos_upload')[0],
            autoUpload: false,
            text: {
                uploadButton: '<div class="upload-section"><i class="material-icons">photo_camera</i>' + mooPhrase.__('business_upload_document_drag') + '</div>'
            },
            validation: {

                allowedExtensions: ['jpg', 'jpeg', 'gif', 'png']
            },
            request: {
                endpoint: mooConfig.url.base + '/businesses/verifies/ajax_upload'
            },
            callbacks: {
                onComplete: function(id, fileName, response) {
                    newPhotos.push(response.photo);
                    $('#new_photos').val(newPhotos.join(','));
                    $('#nextStep').show();
                },
                onValidate: function(data){
                    if (data.size && this._options.validation.sizeLimit && data.size > this._options.validation.sizeLimit){
                        mooGlobal.errorHandler;
                        return false;
                    }
                },
                onSubmit: function(id, fileName){
                    if (typeof this.iItemCount === 'undefined'){
                        this.iItemCount = 0;
                        this.bShowMessage = 0;
                    }
                    this.iItemCount++;
                    if (iItemLimit > 0 && iItemLimit < this.iItemCount){
                        this.iItemCount--;
                        if (this.bShowMessage == 0){
                            this.bShowMessage = 1;
                            $.fn.SimpleModal({
                                btn_ok : mooPhrase.__('confirm'), 
                                title: mooPhrase.__('message'), 
                                contents: mooPhrase.__('maximun_number_documents'), 
                                model: "modal" 
                            }).showModal();
                        }
                        return false;
                    }
                },
                onCancel: function(id, fileName){
                    if (typeof this.iItemCount !== 'undefined'){
                        this.iItemCount--;
                    }
                }
            }
        });
        uploaderVerify._options.messages['maxItemFiles'] = mooPhrase.__('text_maximum_document');

        $('#triggerUpload').click(function() {
            if(typeof uploaderVerify.iItemCount !== 'undefined' && uploaderVerify.iItemCount <= iItemLimit){
                uploaderVerify.uploadStoredFiles();
            } else {
                uploaderVerify._error('maxItemFiles', 'error');
                return false;
            }
        });

        $('#nextStep').click(function() {
            $('#loadingSpin').spin('tiny');
            $('#verifyByDocuments').submit();
            $(this).addClass('disabled');
        });
        
        jQuery('#new_photos').val(newPhotos.join(','));
    }
    
    ///////////////////////////////////////////home feed///////////////////////////////////////////
    var initHomeFeed = function(user_id)
    {
        jQuery(document).on('click', '.tab-content li a', function(){
            jQuery('.tab-content li a').removeClass('current');
            jQuery(this).addClass('current');
        });

        jQuery(document).on('click', '#all_activites', function(){
            loadBusinessActivities('all');
        })
        
        jQuery(document).on('click', '#following_activites', function(){
            loadBusinessActivities('following');
        })
        
        if(user_id > 0)
        {
            jQuery('#feed-type li a.current').trigger('click');
        }
        else
        {
            loadBusinessActivities('all');
        }
    }
    
    var initBusinessFeatured = function()
    {
        jQuery('#calFeaturedPrice').click(function(){
            if( isInt(jQuery('#feature_day').val())) {
                var price  = parseFloat(jQuery("#calFeaturedPrice").attr('ref')) * parseInt(jQuery('#feature_day').val());
                jQuery('#featured_price').html(price);
                jQuery('#price').val(price);
                jQuery("#featuredBtn").show();
            }else{
                jQuery("#errorMessage").show();
                jQuery("#errorMessage").html(mooPhrase.__('f_must_interger'));
                
            }
        });
    }
    
    var initMyReviews = function()
    {
        initReviewStar();
        
        //review dialog
        jQuery(document).on('click', '.btn_write_review', function(){
            reviewDialog(jQuery(this).data('business_id'), '', '', jQuery(this).data('view_detail'), jQuery(this).data('my_review'));
        })
        
        //edit review dialog
        jQuery(document).on('click', '.edit_review', function(){
            reviewDialog(jQuery(this).data('business_id'), jQuery(this).data('id'), jQuery(this).data('parent_id'), jQuery(this).data('view_detail'), jQuery(this).data('my_review'));
        })
        
        //delete review
        jQuery(document).on('click', '.delete_review', function(){
            deleteReview(jQuery(this).data('review_id'), jQuery(this).data('parent_id'));
        })
        
        //save review
        doReview();
        
        businessReview();
    }
    
    var initFollow = function()
    {
        //follow
        jQuery(document).off('click', '.btn_follow');
        jQuery(document).on('click', '.btn_follow', function(){
            var item = jQuery(this);
            if(item.data('remove') == 1)
            {
                mooConfirmBox(mooPhrase.__('business_text_confirm_unfollow'), function(){
                    jQuery.post(mooConfig.url.base + '/business_follow/do_follow/' + item.data('id'), function(data){
                        item.closest('li.full_content').remove();
                    })
                })
            }
            else 
            {
                doFollow(this, item.data('id'));
            }
        });
        
        //follow in my business
        jQuery(document).off('click', '.btn_follow_tab');
        jQuery(document).on('click', '.btn_follow_tab', function(){
            doFollow(this, jQuery(this).data('id'));
            $(this).parents('li.feed_bus').remove();
        })
    }
    function isInt(value) {
        return !isNaN(value) && (function(x) { return (x | 0) === x; })(parseFloat(value))
    }
    
    var initBusinessUserProfile = function(tab)
    {
        if(tab == 'change-profile-picture')
        {
            jQuery('#avatar_upload a').trigger('click');
        }
        else if(tab != '')
        {
            jQuery('#browse ul li').removeClass('current');
            var item = jQuery('#browse ul li#tab-' + tab + ' a');
            jQuery.post(item.data('url'), '', function(data){
                jQuery('#' + item.attr('rel')).append(data);
            })
        }
    }
    
    var initReviewDetail = function()
    {
        initReviewStar();
        
        //edit review dialog
        jQuery(document).on('click', '.edit_review', function(){
            reviewDialog(jQuery(this).data('business_id'), jQuery(this).data('id'), jQuery(this).data('parent_id'), jQuery(this).data('view_detail'));
        })
        
        //review dialog
        jQuery(document).on('click', '.btn_write_review', function(){
            reviewDialog(jQuery(this).data('business_id'), '', '', jQuery(this).data('view_detail'));
        })
        
        jQuery(document).on('click', '#copy_link', function(){
            copyToClipboard('review_link');
        })
        
        //save review
        doReview();
        
        //delete review
        jQuery(document).on('click', '.delete_review', function(){
            deleteReview(jQuery(this).data('review_id'), jQuery(this).data('parent_id'), jQuery(this).data('redirect'));
        })
    }
    
    function copyToClipboard(elementId) 
    {
        var aux = document.createElement("input");
        aux.setAttribute("value", document.getElementById(elementId).value);
        document.body.appendChild(aux);
        aux.select();
        document.execCommand("copy");
        document.body.removeChild(aux);
    }
    
    var initRegistration = function()
    {
        jQuery(document).on('click', '#btnFindAddress', function(){
            jQuery('#msgInvalidPostalCode').hide();
            $.post(mooConfig.url.base + "/business_locations/find_address_by_postcode", 'postal_code=' + $("#postal_code").val(), function(data){
                if(isJson(data))
                {
                    var json = jQuery.parseJSON(data);
                    if(json.result == 0)
                    {
                        mooAlert.alert(json.message);
                    }
                }
                else
                {
                    if(data != '')
                    {
                        var html = '<option>' + data + '</option>';
                        jQuery('#postal_code_address').html(html).show();
                    }
                    else
                    {
                        jQuery('#msgInvalidPostalCode').show();
                    }
                }
            });
        })
        
        jQuery(document).on('click', '#btnSignup', function(){
            if($('#tos').is(':checked')){
                mooButton.disableButton('btnSignup');
                $.post(mooConfig.url.base + "/users/do_signup", $("#regForm").serialize(), function(data){
                    $('#step2Box').spin(false);
                    var result = '';
                    if(isJson(data))
                    {
                        if(result.redirect)
                        {
                            window.location = result.redirect;
                        }
                    }
                    else
                    {
                        if (data != '') {
                            $("#regError").fadeIn();
                            $("#regError").html(data);
                            $('#step2Submit').removeAttr('disabled');
                            grecaptcha.reset(); // FIXED_JS if ($this->Moo->isRecaptchaEnabled()):
                        } else {
                            window.location = mooConfig.url.base + '/businesses/after_registration';
                        }
                    }
                    mooButton.enableButton('btnSignup');
                });
            }else{
                $("#regError").fadeIn();
                $("#regError").html(mooPhrase.__('you_have_to_agree_with_term_of_service'));
            }
        })
        
        jQuery(document).on('click', '#no_postal_code', function(){
            if(jQuery(this).is(':checked'))
            {
                jQuery('.custom_address').show();
                jQuery('.custom_address').find('input').removeAttr('disabled');
                jQuery('.custom_address').find('select').removeAttr('disabled');
                jQuery('#postal_code').attr('disabled', 'disabled');
                jQuery('#postal_code_address').attr('disabled', 'disabled');
                jQuery('#btnFindAddress').attr('disabled', 'disabled');
            }
            else
            {
                jQuery('.custom_address').hide();
                jQuery('.custom_address').find('input').attr('disabled', 'disabled');
                jQuery('.custom_address').find('select').attr('disabled', 'disabled');
                jQuery('#postal_code').removeAttr('disabled');
                jQuery('#postal_code_address').removeAttr('disabled');
                jQuery('#btnFindAddress').removeAttr('disabled');
            }
        })
        
        if(jQuery('#edit_profile').length > 0)
        {
            if(jQuery('#postal_code').val() == '')
            {
                jQuery('#no_postal_code').trigger('click');
            }
            else
            {
                jQuery('#btnFindAddress').trigger('click');
            }
        }
    }
    
    var userChart = function(){
        if($('.chart_user_statitic').size() > 0){
            $('.chart_user_statitic').each(function(){
                var chart_name = $(this);
                 
                var canvas_id = chart_name.data('id_canvas');
                
                var canvas;
                
                canvas = this;
                
               
                var center = chart_name.data('width') / 2;
                var mini_circle = center - 15;
                var ctx;
                var lastend = 0;
                var myColor = ["#7eb93d","#f8b444","#E65365"];
                var myData = [chart_name.data('userful'),chart_name.data('unrated'),chart_name.data('reported')];
                var myTotal = 0;
                for (var j = 0; j < myData.length; j++) {
                    myTotal += (typeof myData[j] == 'number') ? myData[j] : 0;
                }
                //draw canvas chart

               
                ctx = canvas.getContext("2d");
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                for (var i = 0; i < myData.length; i++) {
                    ctx.fillStyle = myColor[i];
                    ctx.beginPath();
                    ctx.moveTo(center,center);
                    ctx.arc(center,center,center,lastend,lastend+
                      (Math.PI*2*(myData[i]/myTotal)),false);
                    ctx.lineTo(center,center);
                    ctx.fill();
                    lastend += Math.PI*2*(myData[i]/myTotal);
                }

                // new circle inside
                
                ctx.beginPath();
                ctx.arc(center,center,mini_circle,0,2*Math.PI);
                ctx.fillStyle = "#fff";
                ctx.fill();



            });
       } 

    }

    var fillColorSvg = function(){
    jQuery('img.svg').each(function(){
        var $img = jQuery(this);
        var imgID = $img.attr('id');
        var imgClass = $img.attr('class');
        var imgURL = $img.attr('src');
    
        jQuery.get(imgURL, function(data) {
            // Get the SVG tag, ignore the rest
            var $svg = jQuery(data).find('svg');
    
            // Add replaced image's ID to the new SVG
            if(typeof imgID !== 'undefined') {
                $svg = $svg.attr('id', imgID);
            }
            // Add replaced image's classes to the new SVG
            if(typeof imgClass !== 'undefined') {
                $svg = $svg.attr('class', imgClass+' replaced-svg');
            }
    
            // Remove any invalid XML tags as per http://validator.w3.org
            $svg = $svg.removeAttr('xmlns:a');
            
            // Check if the viewport is set, else we gonna set it if we can.
            if(!$svg.attr('viewBox') && $svg.attr('height') && $svg.attr('width')) {
                $svg.attr('viewBox', '0 0 ' + $svg.attr('height') + ' ' + $svg.attr('width'))
            }
    
            // Replace image with new SVG
            $img.replaceWith($svg);
    
        }, 'xml');
    
        });
    }

    var showChartOnMoreMenu = function(){
        $('#menu_mobile').on('shown.bs.modal', function (e) {
            
            userChart();
     
        });
    }
    var scrollToId = function(){
        $("#bus_landing_scroll").click(function() {
            $('html, body').animate({
                scrollTop: $("#cat_interested").offset().top
            }, 2000);
        });
    };
    var initAdvancedSearchDialog = function (){
        if(typeof google.maps.places !== 'undefined')
        {
            var autocomplete = new google.maps.places.Autocomplete(document.getElementById('address'));
            if(jQuery('#address_mobile').length > 0)
            {
                new google.maps.places.Autocomplete(document.getElementById('address_mobile'));
            }
        }
        jQuery(document).on('click', '#btn_global_search', function(){
            
            jQuery.post(mooConfig.url.base + '/businesses/global_search/', jQuery(this).closest('form').serialize(), function(data){
                var json = jQuery.parseJSON(data);
                if(json.result == 0)
                {
                    mooAlert.alert(json.message);
                }
                else
                {
                    if(mooConfig.isApp)
                    {
                        window.location = json.location + "&app_no_tab=1";
                    }
                    else
                    {
                        window.location = json.location;
                    }
                }
            });
        })
        jQuery('#distance').keypress(function (e) {
            if(e.which == 13)  // the enter key code
            {
                jQuery(this).closest('form').find('#btn_global_search').trigger('click');
                return false;  
            }
        });
        //initSuggestGlobalLocation();
        initSuggestGlobalCategory();
    };
    
    var initSuggestGlobalLocation = function()
    {
        jQuery('.global_search_location').each(function(){
            var item = jQuery(this);
            item.autocomplete({
                source: function (request, response) {
                    jQuery.post(mooConfig.url.base + '/business/suggest_global_location/', 'keyword=' + item.val(), function(data){
                        if(data != 'null')
                        {
                            response(jQuery.parseJSON(data));
                        }
                        else
                        {
                            jQuery('#global_search_location_id').val('');
                        }
                    });
                },
                minLength: 3,
                select: function (event, ui) {
                    item.val(ui.item.label);
                    //jQuery('#global_search_location_id').val(ui.item.value);
                    return false;
                },
                focus: function( event, ui ) {
                    item.val( ui.item.label );
                    jQuery('.ui-autocomplete li').removeClass('selected');
                    jQuery('#location_suggest_' + ui.item.value).addClass('selected');
                    return false;  
                },  
            }).data("ui-autocomplete")._renderItem = function(ul, item) {
                var term = this.term;
                //var regex = new RegExp("\\S*" + $.ui.autocomplete.escapeRegex(term) + "\\S*", "gi");
                var regex = new RegExp($.ui.autocomplete.escapeRegex(term), "gi");
                var label = item.label.replace(regex, '<b>$&</b>');
                return $('<li id="location_suggest_' + item.value + '"></li>').append(label).appendTo(ul);
            };
        })
        jQuery('.global_search_location').keypress(function (e) {
            if(e.which == 13)  // the enter key code
            {
                jQuery(this).closest('form').find('#btn_global_search').trigger('click');
                return false;  
            }
        });
    }

    /*var initSuggestGlobalCategory = function()
    {
        jQuery('.global_search_category').each(function(){
            var item = jQuery(this);
            var item_target = jQuery(this);
            var arrow = item.parent().find('.drop-history');
            var history = [];
            if(typeof jQuery(this).data('history') != 'undefined')
            {
                history = jQuery(this).data('history');
            }
            item.mousedown(function() {
                if(this.value == '')
                {
                    item.autocomplete({
                        source: history,
                        minLength: 0,
                        focus: function( event, ui ) {
                            item.val( ui.item.label );
                            return false;  
                        }
                    }).data("ui-autocomplete")._renderItem = function(ul, item) {
                        item_target.after(jQuery('.ui-autocomplete'));
                        var html = '';
                        var item_id = '';
                        if(item.image)
                        {
                            html = 
                                '<a href="' + item.link + '">\n\
                                    <div class="user_avatar_small attached-image">' + item.image + '</div>\n\
                                    <div class="suggest_info">\n\
                                        <div class="suggest_name">' + item.label + '</div>\n\
                                        <div class="suggest_more_info">' + item.address + '</div>\n\
                                    </div>\n\
                                </a>';
                            item_id = 'business_suggest_' + item.value;
                        }
                        else
                        {
                            var term = this.term;
                            //var regex = new RegExp("\\S*" + $.ui.autocomplete.escapeRegex(term) + "\\S*", "gi");
                            var regex = new RegExp($.ui.autocomplete.escapeRegex(term), "gi");
                            html = item.label.replace(regex, '<b>$&</b>');
                            item_id = 'cat_suggest_' + item.value;
                        }
                        return $('<li id="' + item_id + '"></li>').data("item.autocomplete", item).append(html).appendTo(ul);
                    };
                    item.autocomplete("search");
                }
            })
            arrow.click(function(){
                if(!jQuery('.ui-autocomplete').is(':visible'))
                {
                    item.trigger('mousedown');
                }
                else
                {
                    jQuery('.ui-autocomplete').hide();
                }
            })
            
            item.keyup(function() {
                if(this.value != '')
                {
                    var page = 1;
                    var responseData = '';
                    var search_item = item;
                    var lock_search = 0;
                    var keyword = '';
                    if(page == 1)
                    {
                        keyword = item.val();
                    }
                    item.autocomplete({
                        source: function (request, response) {
                            jQuery.post(mooConfig.url.base + '/business/suggest_global_category/', 'keyword=' + keyword + '&page=' + page, function(data){
                                if(data != '')
                                {
                                    if(responseData == ''){
                                        responseData = jQuery.parseJSON(data);
                                    }
                                    else{
                                        responseData = responseData.concat(jQuery.parseJSON(data));
                                    }
                                    lock_search = 0;
                                    response(responseData);
                                }
                            });
                        },
                        minLength: 3,
                        select: function (event, ui) {
                            item.val(ui.item.label);
                            if(ui.item.link)
                            {
                                window.location = ui.item.link;
                            }
                            return false;
                        },
                        focus: function( event, ui ) {
                            item.val( ui.item.label );
                            jQuery('.ui-autocomplete li').removeClass('selected');
                            if(ui.item.image)
                            {
                                jQuery('#business_suggest_' + ui.item.value).addClass('selected');
                            }
                            else
                            {
                                jQuery('#cat_suggest_' + ui.item.value).addClass('selected');
                            }
                            return false;  
                        }
                    }).data("ui-autocomplete")._renderItem = (function(ul, item) {
                        item_target.after(jQuery('.ui-autocomplete'));
                        var html = '';
                        var item_id = '';
                        var self = this;
                        if(item.image)
                        {
                            html = 
                                '<a href="' + item.link + '">\n\
                                    <div class="user_avatar_small attached-image">' + item.image + '</div>\n\
                                    <div class="suggest_info">\n\
                                        <div class="suggest_name">' + item.label + '</div>\n\
                                        <div class="suggest_more_info">' + item.address + '</div>\n\
                                    </div>\n\
                                </a>';
                            item_id = 'business_suggest_' + item.value;
                        }
                        else
                        {
                            var term = this.term;
                            //var regex = new RegExp("\\S*" + $.ui.autocomplete.escapeRegex(term) + "\\S*", "gi");
                            var regex = new RegExp($.ui.autocomplete.escapeRegex(term), "gi");
                            html = item.label.replace(regex, '<b>$&</b>');
                            item_id = 'cat_suggest_' + item.value;
                        }
                        $(ul).scroll(function(){
                            if (isScrollbarBottom($(ul)) && lock_search == 0) {
                                page++;
                                lock_search = 1;
                                search_item.autocomplete("search");
                            }
                        });
                        return $('<li id="' + item_id + '"></li>').data("item.autocomplete", item).append(html).appendTo(ul);
                    });
                }
            })
        })
        
        jQuery('.global_search_category').keypress(function (e) {
            if(e.which == 13)  // the enter key code
            {
                var form = jQuery(this).closest('form');
                if(form.find('#btn_global_search').length > 0)
                {
                    form.find('#btn_global_search').trigger('click');
                }
                if(form.find('#btn_global_search_landing').length > 0)
                {
                    form.find('#btn_global_search_landing').trigger('click');
                }
                return false;  
            }
        });
    }
    */
    function isScrollbarBottom(container) {
        var height = container.outerHeight();
        var scrollHeight = container[0].scrollHeight;
        var scrollTop = container.scrollTop();
        if (scrollTop >= scrollHeight - height - (scrollHeight / 4)) {
            return true;
        }
        return false;
    };
    
    var showMoreLessContent = function(){
        $(document).ready(function() {
            // Configure/customize these variables.
            var showChar = 100;  // How many characters are shown by default
            var ellipsestext = "...";
            var moretext = "Show more >";
            var lesstext = "Show less";


            $('.more').each(function() {
                var content = $(this).html();

                if(content.length > showChar) {

                    var c = content.substr(0, showChar);
                    var h = content.substr(showChar, content.length - showChar);

                    var html = c + '<span class="moreellipses">' + ellipsestext+ '&nbsp;</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="" class="morelink">' + moretext + '</a></span>';

                    $(this).html(html);
                }

            });

            $(".morelink").click(function(){
                if($(this).hasClass("less")) {
                    $(this).removeClass("less");
                    $(this).html(moretext);
                } else {
                    $(this).addClass("less");
                    $(this).html(lesstext);
                }
                $(this).parent().prev().toggle();
                $(this).prev().toggle();
                return false;
            });
        });
    }
    
    var initForMobile = function(){
        jQuery(document).on('click', '#btn_search', function(){
            if(!jQuery('#mobile_search').is(':visible'))
            {
                jQuery('#mobile_search').show();
            }
            else
            {
                jQuery('#mobile_search').hide();
            }
        });
        
        jQuery(document).on('click', '#mobile_menu_detail_info li a', function(){
            var id = jQuery(this).data('id');
            jQuery('.menu_detail_info .detail_info_item').hide();
            if(!jQuery('#' + id).is(':visible'))
            {
                jQuery('#' + id).show();
            }
            else
            {
                jQuery('#' + id).hide();
            }
            jQuery('html, body').animate({
                scrollTop: $("#" + id).offset().top - 100
            }, 200);
        });
        
        //active mobile tab
        jQuery(document).on('click', '.profile_plg_menu ul li a', function(e){
            jQuery('.profile_plg_menu ul li a').removeClass('active');
            if(jQuery('#' + jQuery(this).data('id')).is(':visible'))
            {
                jQuery(this).addClass('active');
            }
        })
    }

    var storeCoords = function(c) {
        x = c.x;
        y = c.y;
        w = c.w;
        h = c.h;
    }
    var jcrop_api;
    var x = 0,
        y = 0,
        w = 0,
        h = 0;
    var initBusinessCoverUploader = function()
    {

        var JCropper;
        if( !mooConfig.isMobile ) {
            $('#cover-img').Jcrop({
                aspectRatio: 14 / 5,
                onSelect: storeCoords,
                minSize: [ 400, 200 ],
                boxWidth: 570,
            }, function(){
                JCropper = this;
            });
        }
        var id = $('#business_id').val();
        if($('#select-1').length > 0)
        {
            var uploader = new mooFileUploader.fineUploader({
                element: $('#select-1')[0],
                multiple: false,
                text: {
                    uploadButton: '<div class="upload-section"><i class="material-icons">photo_camera</i>' + mooPhrase.__('tdesc') + '</div>'
                },
                validation: {
                    allowedExtensions: ['jpg', 'jpeg', 'gif', 'png'],
                },
                request: {
                    endpoint: mooConfig.url.base + "/business_photo/upload_cover_photo/" + id
                },
                callbacks: {
                    onSubmit: function(id, fileName){
                        var promise = validateFileDimensions(id, [400, 200],this);
                        return promise;
                    },
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
                                $('#image_name').val(response.filename);
                                if( !mooConfig.isMobile ) {
                                    JCropper.setImage(response.thumb);
                                }else{
                                    $('#cover_wrapper img').attr('src', response.thumb);
                                }

                                $('#coverModal').on('click', '.close', function () {
                                    $.post(mooConfig.url.base + '/business_photo/remove_image_null', response, function() {});
                                })
                            }
                        }
                    }
                }
            });
        };

        function validateFileDimensions(id, dimensionsLimits,obj)
        {
            window.URL = window.URL || window.webkitURL;
            var file = obj.getFile(id);

            var image = new Image();
            var status = false;
            var sizeDetermination = {};

            image.onerror = function(e) {
                sizeDetermination['error'] = mooPhrase.__('cannot_determine_dimensions_for_image_may_be_too_large');
            };

            image.onload = function() {
                sizeDetermination = { width: this.width, height: this.height };

                var minWidth = sizeDetermination.width >= dimensionsLimits[0],
                    minHeight = sizeDetermination.height >= dimensionsLimits[1];

                // if min-width or min-height satisfied the limits, then approve the image
                if( minWidth && minHeight ){
                    uploader.uploadStoredFiles();
                }
                else{
                    uploader.clearStoredFiles();
                    mooAlert.alert(mooPhrase.__('please_choose_an_image_that_s_at_least_400_pixels_wide_and_at_least_150_pixels_tall'));
                }
            };
            image.src = window.URL.createObjectURL(file);
        }

    }

    var initSaveBusinessCover = function()
    {
        //show cover
        jQuery(document).on('click', '#btnChangeCover', function(){
            jQuery.post(mooConfig.url.base + '/businesses/change_cover/' + jQuery(this).data('id'), function(data){
                jQuery('#coverModal .modal-content').empty().append(data);
                jQuery('#coverModal').modal({
                    backdrop : 'static'
                });
            })
        })
        
        $('.save-cover').unbind('click');
        $(document).on('click','#saveChangeCoverButton',function() {
            var modal = $('#coverModal');
            $('#cover_wrapper').spin('large');

            var jcrop_width = $('#cover_wrapper .jcrop-holder').width();
            var jcrop_height = $('#cover_wrapper .jcrop-holder').height();
            var id = $(this).data('id');
            var image_name = $('#image_name').val();

            $.post(mooConfig.url.base + '/business_photo/save_cover', {id: id, x: x, y: y, w: w, h: h, jcrop_width: jcrop_width, jcrop_height: jcrop_height, image_name: image_name}, function(data) {
                if(mooConfig.isApp)
                {
                    window.mobileAction.backAndRefesh();
                }
                    
                modal.modal('hide');

                x = y = w = h = 0;
                if (data != ''){
                    var json = $.parseJSON(data);
                    $('#cover_img_display').attr("src",json.thumb);
                }
            });
        });
    }
    
    var initSetDefaultCover = function()
    {
        $('#change_default_photo').on('click', function() {
            var id = $(this).data('id');

            $.post(mooConfig.url.base + '/business_photo/change_default_photo/' + id, function(data) {
                if (data != ''){
                    var json = $.parseJSON(data);
                    $('#cover_img_display').attr("src",json.thumb);
                }
            });
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
    
    var initPaginator = function()
    {
        jQuery('.pagination li a').each(function(){
            var link = jQuery(this).attr('href');
            if(mooConfig.isApp)
            {
                var hasQueryParams = new RegExp(/(\?.*)$/).test(link);
                link = hasQueryParams ? link + "&app_no_tab=1" : link + "?app_no_tab=1";
            }
            jQuery(this).attr('href', link);
        })
    }
    
    return{
        fillColorSvg: function(){
            fillColorSvg();
        },
        userChart: function(){
            userChart();
        },
        initReviewStar: function()
        {
            initReviewStar();
        },
        initFollow: function()
        {
            initFollow();
        },
        initSearchPage: function()
        {
            initSearchPage();
        },
        initSearchMap: function(locations)
        {
            initSearchMap(locations);
        },
        initBusinessDetailPage: function(business_id, tab, sub_tab)
        {
            initBusinessDetailPage(business_id, tab, sub_tab);
        },
        initCreateBusiness: function(add_default_hour, add_default_cat)
        {
            initCreateBusiness(add_default_hour, add_default_cat);
        },
        initBusinessPhoto: function(type, target_id)
        {
            initBusinessPhoto(type, target_id);
        },
        initBusinessAdmin: function(business_id)
        {
            initBusinessAdmin(business_id);
        },
        initBusinessPermission: function()
        {
            initBusinessPermission();
        },
        initBusinessBranch: function(add_default_hour)
        {
            initBusinessBranch(add_default_hour);
        },
        initBusinessVerify: function()
        {
            initBusinessVerify();
        },
        initHomeFeed: function(user_id)
        {
            initHomeFeed(user_id);
        },
        initBusinessFeatured: function()
        {
            initBusinessFeatured();
        },
        initMyReviews: function()
        {
            initMyReviews();
        },
        initFancyBox: function()
        {
            initFancyBox();
        },
        initBusinessUserProfile: function(tab)
        {
            initBusinessUserProfile(tab);
        },
        getDirection: function()
        {
            getDirection();
        },
        getParking: function()
        {
            getParking();
        },
        initReviewDetail: function()
        {
            initReviewDetail();
        },
        initRegistration: function()
        {
            initRegistration();
        },
        initUserDetail: function(user_id, acitvity_id)
        {
            initUserDetail(user_id, acitvity_id);
        },
        initFilterConversation: function(){
            initFilterConversation();
        },
        initActivityForm: function()
        {
            initActivityForm();
        },
        showChartOnMoreMenu: function(){
            showChartOnMoreMenu();
        },
        scrollToId: function(){
            scrollToId();
        },
        initAdvancedSearchDialog: function(){
            initAdvancedSearchDialog();
        },
        initCheckIn: function(){
            initCheckIn();
        },
        initActivity: function(){
            initActivity();
        },
        initBusinessItem: function(){
            initBusinessItem();
        },
        initAddFavourite: function(){
            initAddFavourite();
        },
        showMoreLessContent: function(){
            showMoreLessContent();
        },
        initForMobile: function(){
            initForMobile();
        },
        initBusinessCoverUploader: function () {
            initBusinessCoverUploader();
        },
        initSaveBusinessCover: function () {
            initSaveBusinessCover();
        },
        initSetDefaultCover: initSetDefaultCover,
        parseAjaxLink: function(){
            parseAjaxLink();
        },
        initPaginator: initPaginator,
        initBusinessReviewData: initBusinessReviewData
    }
}));