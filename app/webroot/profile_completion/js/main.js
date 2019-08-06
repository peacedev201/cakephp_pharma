(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
    	define(['jquery', 'mooFileUploader', 'mooBehavior', 'mooAlert', 'mooPhrase', 'mooAjax', 'mooGlobal', 'tinyMCE'], factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS-like
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals (root is window)
        root.mooProfileCompletion = factory();
    }
}(this, function ($, mooFileUploader, mooBehavior, mooAlert, mooPhrase, mooAjax, mooGlobal, tinyMCE) {
    var initApp = function(current_user_id){
        $.post(mooConfig.url.base + '/profile_completions/profile_app/'+current_user_id, {}, function(data) {
            $('#center').before(data);
        });
    }
    return{
        initApp : function(current_user_id){
            initApp(current_user_id);
        }
    }
}));