(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery', 'mooSpotlight', 'mooPhrase', 'mooGlobal', 'mooCarousel'], factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS-like
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals (root is window)
        root.spotlight = factory();
    }
}(this, function ($, mooSpotlight, mooPhrase, mooGlobal, mooCarousel) {

    var initCarousel = function ( params ) {
        var jcarousel = $('#jsCarousel'+params.id);
        jcarousel.on('jcarousel:reload jcarousel:create', function () {
                var carousel = $(this),
                    width = carousel.innerWidth();
                if (width >= 600) {
                    width = width / 3;
                } else if (width >= 350) {
                    width = width / 2;
                }
                carousel.jcarousel('items').css('width', Math.ceil('120') + 'px');
            })
            .jcarousel({
                wrap: 'circular'
        });

        $('#jcarousel-control-prev'+params.id).jcarouselControl({
                target: '-=1'
        });

        $('#jcarousel-control-next'+params.id).jcarouselControl({
                target: '+=1'
        });

        $('.jcarousel-pagination').on('jcarouselpagination:active', 'a', function() {
                $(this).addClass('active');
            })
            .on('jcarouselpagination:inactive', 'a', function() {
                $(this).removeClass('active');
            })
            .on('click', function(e) {
                e.preventDefault();
            })
            .jcarouselPagination({
                perPage: 1,
                item: function(page) {
                    return '<a href="#' + page + '">' + page + '</a>';
                }
         });

        jcarousel.jcarouselAutoscroll({
            target: '+=1'
        });

        /*$('#jsCarousel img').hover(function() {
            $(this).addClass('transition');
        }, function() {
            $(this).removeClass('transition');
        });*/
        $(".spotlight_multiple").hover(function(el) {
            jcarousel.jcarouselAutoscroll('stop');
            var left = 0;
            if (this == $('.slider div').last().get()[0])
            {
                left = '-35px';
            }
            if (this == $('.slider div').first().get()[0])
            {
                left = '12px';
            }
            $(this).css({'z-index' : '10', 'width': '120px'});
            $(this).find('img').addClass("hover").stop()
                .animate({

                    
                }, 200);

        } , function() {
            $(this).css({'z-index' : '0', 'width': '120px'});
            $(this).find('img').removeClass("hover").stop()
                .animate({
                    width: '120px',
                    height: '120px',
                }, 400);
            jcarousel.jcarouselAutoscroll('start');
        });
    }


    return {
        initCarousel: function ( params ) {
            initCarousel( params );
        },
    }

}));