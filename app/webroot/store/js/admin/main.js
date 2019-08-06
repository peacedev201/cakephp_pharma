jQuery(document).on("click", ".quickview-wrapper .closeqv",function(event){
    jQuery('.quickview-wrapper .quick-modal').css({width: ""});
    jQuery('.quickview-wrapper #quickview-content').empty();
    jQuery('.quickview-wrapper').removeClass('open');
});

jQuery(document).on("click", ".atc-notice-wrapper .close",function(){
    jQuery('.atc-notice-wrapper').fadeOut();jQuery('.atc-notice').html('');
});

//product view mode (grid - list)
jQuery('.view-mode').each(function() {
    jQuery(this).find('.grid').on("click", function(event) {
        event.preventDefault();
        jQuery('#archive-product .view-mode').find('.grid').addClass('active');
        jQuery('#archive-product .view-mode').find('.list').removeClass('active');
        jQuery('#archive-product .shop-products').removeClass('list-view');
        jQuery('#archive-product .shop-products').addClass('grid-view');
        jQuery('#archive-product .list-col4').removeClass('col-xs-12 col-sm-5');
        jQuery('#archive-product .list-col8').removeClass('col-xs-12 col-sm-7');
    });
    jQuery(this).find('.list').on("click", function(event) {
        event.preventDefault();
        jQuery('#archive-product .view-mode').find('.list').addClass('active');
        jQuery('#archive-product .view-mode').find('.grid').removeClass('active');
        jQuery('#archive-product .shop-products').addClass('list-view');
        jQuery('#archive-product .shop-products').removeClass('grid-view');
        jQuery('#archive-product .list-col4').addClass('col-xs-12 col-sm-4');
        jQuery('#archive-product .list-col8').addClass('col-xs-12 col-sm-8');
    });
});

function mooConfirmBox( msg, callback )
{
    // Set title
    $($('#portlet-config  .modal-header .modal-title')[0]).html(MooPhrase.__('please_confirm'));
    // Set content
    $($('#portlet-config  .modal-body')[0]).html(msg);
    // OK callback, remove all events bound to this element
    $('#portlet-config  .modal-footer .ok').off("click").click(function(){
        callback();
        $('#portlet-config').modal('hide');
    });
    $('#portlet-config').modal('show');

}

//tooltip
jQuery(window).ready(function(){
    /*jQuery('.yith-wcwl-add-to-wishlist a, .compare-button a, .detail-link.quickview .fa, .sharefriend a').each(function() {
        genietip(jQuery(this), 'html');
    });*/
    jQuery('.social-icons a').each(function() {
        genietip(jQuery(this), 'title');
    });
})


function genietip(element, content) {
    if (content == 'html') {
        var tipText = element.html();
    } else {
        var tipText = element.attr('title');
    }
    element.on('mouseover', function() {
        
        if (jQuery('.genietip').length == 0) {
            element.before('<span class="genietip">' + tipText + '</span>');
            var tipWidth = jQuery('.genietip').outerWidth();
            var tipPush = -(tipWidth / 2 - element.outerWidth() / 2);
            jQuery('.genietip').css('margin-left', tipPush);
        }
    });
    element.on('mouseleave', function() {
        jQuery('.genietip').remove();
    });
};


var StorePluginCustom = function() {
    var viewport = function() {
        var e = window, a = 'inner';
        if (!('innerWidth' in window)) {
            a = 'client';
            e = document.documentElement || document.body;
        }
        return e[ a + 'Width' ];
    }
    var scrollTop = function() {
        $(window).scroll(function() {
            if (viewport() > 992) {
                var _top = $(window).scrollTop();
                if (_top > 500) {
                    if (!$('.scrollTop').length) {
                        $('body').append('<div class="scrollTop"></div>');
                        $(".scrollTop").click(function() {
                            $("html, body").animate({scrollTop: 0}, "fast");
                            return false;
                        });
                    }
                } else {
                    $('.scrollTop').remove();
                }
            }
        });

    }
    var quantitychange = function() {

        // This button will increment the value
        $('.qtyplus').click(function(e) {
            // Stop acting like a button
            e.preventDefault();
            // Get the field name
            fieldName = $(this).attr('field');
            // Get its current value
            var currentVal = parseInt($('input[name=' + fieldName + ']').val());
            // If is not undefined
            if (!isNaN(currentVal)) {
                // Increment
                $('input[name=' + fieldName + ']').val(currentVal + 1);
            } else {
                // Otherwise put a 0 there
                $('input[name=' + fieldName + ']').val(0);
            }
        });
        // This button will decrement the value till 0
        $(".qtyminus").click(function(e) {
            // Stop acting like a button
            e.preventDefault();
            // Get the field name
            fieldName = $(this).attr('field');
            // Get its current value
            var currentVal = parseInt($('input[name=' + fieldName + ']').val());
            // If it isn't undefined or its greater than 0
            if (!isNaN(currentVal) && currentVal > 0) {
                // Decrement one
                $('input[name=' + fieldName + ']').val(currentVal - 1);
            } else {
                // Otherwise put a 0 there
                $('input[name=' + fieldName + ']').val(0);
            }
        });

    };
    var viewmoreListing = function() {
        $(".view-more-area .view-more-btn").click(function() {
            $(this).closest('div').prev('div').find('.check-view-more').toggleClass('store-list-view-more');
            if ($(this).hasClass('check-text-more')) {
                $(this).removeClass('check-text-more');
                $(this).html('View less');
            }
            else {
                $(this).addClass('check-text-more');
                $(this).html('View more');
            }
        });
    }
    var mCategoryClick = function() {
        $(".m-category-wrapper > li > a.no-ajax ").click(function() {
            $(this).toggleClass("collapsed_active");
        });
        $(".menustore ").click(function() {
            $('.menustore-wrapper').toggleClass("showtime");
            if ($('.menustore-wrapper').hasClass("showtime")) {
                $(".menustore > .fa ").removeClass('fa-hand-o-left');
                $(".menustore > .fa ").addClass('fa-hand-o-right');
            }
            else {
                $(".menustore > .fa ").removeClass('fa-hand-o-right');
                $(".menustore > .fa ").addClass('fa-hand-o-left');
            }
        });
    };
    var leftWidgetClick = function() {
        $("aside .item-collapse").click(function() {
            $(this).next().slideToggle("fast");
            if ($(this).find('i').hasClass('fa-minus')) {
                $(this).find('i').removeClass('fa-minus');
                $(this).find('i').addClass('fa-plus');
            }
            else {
                $(this).find('i').removeClass('fa-plus');
                $(this).find('i').addClass('fa-minus');
            }
        });
    };
    return{
        init: function() {
            scrollTop();
            quantitychange();
            viewmoreListing();
            mCategoryClick();
            leftWidgetClick();
            $(window).resize(function() {

            });
        }
    }
}();
jQuery(document).ready(function() {
    StorePluginCustom.init();
    $('[data-toggle="tooltip"]').tooltip();
});