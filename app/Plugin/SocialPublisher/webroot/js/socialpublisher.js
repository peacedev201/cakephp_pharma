/* Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */

(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery','mooPhrase'], factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS-like
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals (root is window)
        root.mooFriendinviter = factory();
    }
}(this, function ($,mooPhrase) {
     
    var initOnIndex = function () {
        $(".social_share").change(function(){   
            var is_checked = $(this).is(":checked");
            var provider = $(this).val();	
       
            var is_connect;
            if(provider == 'facebook'){
                is_connect = $('#fb_connect').val();
            }else{
                is_connect = $('#twitter_connect').val();
            }
            if(is_checked && is_connect != 1){              
               window.location.href = mooConfig.url.base + '/social_publishers/loginsocial/' + provider;
            }else{
                var url = mooConfig.url.base + '/social_publishers/loginsocial/' + provider ;
                var getData = {
                    'flag': is_checked
                };
                    $.ajax({
                            type: "GET",
                            cache: false,
                            url: url,
                            dataType: "html",
                            data: getData,
                            success: function(res)
                            {
                            }
                        });
            }
         });      
        $('.logout_social').unbind('click');
        $(".logout_social").click(function(){
            logoutSocial($(this).attr('rel'));
        });        
    }   
    
    var logoutSocial = function(provider){
        var url = mooConfig.url.base + '/social_publishers/logoutsocial/' + provider ;
         $.ajax({
                type: "GET",
                cache: false,
                url: url,
                dataType: "html",
                data: {},
                success: function(res)
                {
                    if(provider == 'facebook'){
                        $('#fb_connect').val(0);
                        $("#fbook").prop( "checked", false);
                        $("#facebook_status").html('<i class="pb-iconfb"></i> ' +  mooPhrase.__('facebook'));
                    }else{
                        $('#twitter_connect').val(0);
                        $("#twitter").prop( "checked", false);
                        $("#twitter_status").html('<i class="pb-icontwitter"></i> ' +  mooPhrase.__('twitter'));
                    }
                }
      });
    }
    
    return {
        initOnIndex : initOnIndex
    }

}));