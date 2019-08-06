jQuery(window).load(function(){
    jQuery.post(baseUrl + "/feedback/popups/load_shortcut", '', function(data){
        jQuery('body').append(data);
        jQuery.feedback.initFeedbackUploader();
        jQuery.feedback.initFeedbackImage();
    });
})