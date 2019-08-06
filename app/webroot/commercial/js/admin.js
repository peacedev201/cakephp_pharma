
jQuery.admin = {
    changeAdsAction: function(item)
    {
        if(jQuery(item).val() != '')
        {
            window.location = mooConfig.url.base  + '/admin/ads/action/' + jQuery(item).val() + '/' + jQuery(item).data('id');
        }
    },
}