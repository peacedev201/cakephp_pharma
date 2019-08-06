(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery', 'mooSearch', 'mooUser', 'mooPhrase'], factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS-like
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals (root is window)
        root.mooInsider = factory(root.jQuery);
    }
}(this, function ($, mooSearch) {
    var old_width = 0;
    var viewport = function () {
        var e = window, a = 'inner';
        if (!('innerWidth' in window)) {
            a = 'client';
            e = document.documentElement || document.body;
        }
        return e[ a + 'Width' ];
    }
    var viewportHeight = function () {
        var e = window, a = 'inner';
        if (!('innerHeight' in window)) {
            a = 'client';
            e = document.documentElement || document.body;
        }
        return e[ a + 'Height' ];
    }

    var notifyClick = function () {
        $('.notify_content').click(function () {
            $('.notify_content').toggleClass('open');
            $('.conversation_content.dropdown').removeClass('open');
        });
        $('body').click(function (e) {
            if (!$('.notify_content.dropdown').is(e.target)
                    && $('.notify_content.dropdown').has(e.target).length === 0
                    && $('.open').has(e.target).length === 0
                    ) {
                $('.notify_content.dropdown').removeClass('open');
            }
        });
        $('.conversation_content').click(function () {
            $('.conversation_content').toggleClass('open');
            $('.notification_show.dropdown').removeClass('open');
        });
        $('body').click(function (e) {
            if (!$('.conversation_content.dropdown').is(e.target)
                    && $('.conversation_content.dropdown').has(e.target).length === 0
                    && $('.open').has(e.target).length === 0
                    ) {
                $('.conversation_content.dropdown').removeClass('open');
            }
        });
    }

    return{
        init: function () {
            notifyClick();
            $(window).resize(function () {
                notifyClick();
            });
        }
    }
}));
