(function(root, factory) {
    if (typeof define === 'function' && define.amd) {
// AMD
        define(['jquery', 'mooFileUploader', 'mooBehavior', 'mooAlert', 'mooButton', 'mooPhrase', 'mooAjax', 'mooUser', 'mooGlobal'], factory);
    } else if (typeof exports === 'object') {
// Node, CommonJS-like
        module.exports = factory(require('jquery'));
    } else {
// Browser globals (root is window)
        root.mooFaq = factory();
    }
}(this, function($, mooFileUploader, mooBehavior, mooAlert, mooButton, mooPhrase, mooAjax, mooUser, mooGlobal) {
    var initCreateFaq = function(settings)
    {
        var is_click = settings;
        $('.js_drop_down_helpful').unbind('click');
        $('.js_drop_down_helpful').click(function()
        {
            var dataID = $(this).data();
            $(".link_helpful_"+dataID.id).slideToggle();
        });
        $('.js_drop_down_detail_faq').unbind('click');
        $('.js_drop_down_detail_faq').click(function()
        {
            if (!is_click) {
                jQuery(this).closest(".faq_info").children(".list_body").slideToggle();
            } else {
                if (jQuery(this).hasClass("on-clicked"))
                {
                    jQuery(this).closest(".faq_info").children(".list_body").slideToggle();
                    is_click = false;
                } else {
                    $(".list_body").slideUp();
                    $(".js_drop_down_detail_faq").removeClass('on-clicked');
                    jQuery(this).closest(".faq_info").children(".list_body").slideToggle();
                    jQuery(this).toggleClass('on-clicked');
                }
            }
            if (!is_click) {
                jQuery(this).toggleClass('on-clicked');
                is_click = true;
            }
        });
        $('.answerno').unbind('click');
        $('.answerno').click(function()
        {
            mooButton.disableButton('answerno');
            mooAjax.post({
                url: mooConfig.url.base + '/faq/faq_helpfulreports/ajax_answerno',
                data: $("#answernoForm").serialize()
            }, function(data) {
                mooButton.enableButton('answerno');
                $('.answerno').spin(false);
                var json = $.parseJSON(data);
                if (json.result == 1)
                {
                    window.location.href = mooConfig.url.base+'/faqs/view/'+json.faqid+'/name/1';
                } else
                {
                    $(".alert-success").hide();
                    $(".error-message").show();
                    $(".error-message").html(json.message);
                }
            });
            return false;
        });
        $('.answerno-faq-list').unbind('click');
        $('.answerno-faq-list').click(function()
        {
            var datasubmit = $(this).data();
            $('answerno-faq-list').addClass('disabled');
            mooAjax.post({
                url: mooConfig.url.base + '/faq/faq_helpfulreports/ajax_answernobrowsepage',
                data: $("#answernoForm" + datasubmit.id).serialize()
            }, function(data) {
                $('answerno-faq-list').removeClass('disabled');
                $("#userfull-" + datasubmit.id).html(data);
                $("#link_helpful_" + datasubmit.id).show();
                
//                var json = $.parseJSON(data);
//                if (json.result == 1)
//                {
//                     $("#userfull-"+datasubmit.id).html(json.data);
//                } else
//                {
//                    $(".error-message").show();
//                    $(".error-message").html(json.message);
//                }
            });
            return false;
        });
        $('.submit_answer_yes').unbind('click');
        $('.submit_answer_yes').click(function()
        {
            var datasubmit = $(this).data();
            $('submit_answer_yes').addClass('disabled');
            mooAjax.post({
                url: mooConfig.url.base + '/faq/faq_helpfulreports/answeryes/' + datasubmit.id,
                data: null
            }, function(data) {
                $('submit_answer_yes').removeClass('disabled');
                $("#userfull-" + datasubmit.id).html(data);
            });
            return false;
        });
        $('#menu-category-faq').unbind('change');
        $('#menu-category-faq').change(function() {
            window.location.href = mooConfig.url.base+'/faq/faqs/index/category:'+$(this).val()+'?app_no_tab=1';
        });
        
    };
    var changeVisiable = function(id, e)
    {
        var value = 0;
        if ($(e).hasClass('faq_no'))
        {
            value = 1;
        }
        $(e).attr('class', '');
        if (value)
        {
            $(e).addClass('faq_yes');
            $(e).attr('title', 'yes');
        } else
        {
            $(e).addClass('faq_no');
            $(e).attr('title', 'no');
        }
        var baseurl = window.location.href;
        $.post(baseurl + "/admin/faq/faqs/visiable", {'id': id, 'value': value}, function(data) {

        });
    }

    return{
        initCreateFaq: function(settings) {
            initCreateFaq(settings);
        }
    }
}));