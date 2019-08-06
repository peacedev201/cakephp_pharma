/* Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */

(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery', 'mooPhrase','mooAjax'], factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS-like
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals (root is window)
        root.mooPendinginvite = factory();
    }
}(this, function ($,mooPhrase,mooAjax) {
    
    var initOnPending = function () {
        $('.delete_invite').click(function(e){
            var invite_id = $(this).attr('rel');
            if($.isNumeric(invite_id)){
                e.preventDefault();
                $(this).spin('small');
                var url = mooConfig.url.base + '/friend_inviters/ajax_delete';
                mooAjax.post({
                    url : url,
                    data: {id:invite_id}
                }, function(data){
                      $('#invite_'+invite_id).remove();
                });
            }
        });
        
        $('.resend_invite').click(function(e){
            var invite_id = $(this).attr('rel');
            if($.isNumeric(invite_id)){
                e.preventDefault();
                $(this).spin('small');
                var url = mooConfig.url.base + '/friend_inviters/ajax_resend';
                    mooAjax.post({
                        url : url,
                        data: {id:invite_id}
                    }, function(data){
                            $('#'+invite_id+'_link').prev().remove();
                            $('#'+invite_id+'_link').remove();                           
                    });
            }
        });
    }
                      
    return {
        initOnPending : initOnPending
    }

}));