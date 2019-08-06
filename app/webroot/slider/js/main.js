(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery', 'mooGlobal'], factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS-like
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals (root is window)
        root.mooSlider = factory();
    }
}(this, function ($, mooGlobal) {
    var initSlideshow = function ( data ) {
        $( '#front-demo-' + data.key ).DrSlider({
            height: data.height,
            width: data.width,
            navigationType: data.navigationType,
            duration: data.duration,
            transitionSpeed: data.transitionSpeed,
            showNavigation: data.showNavigation,
            classNavigation: undefined,
            navigationColor: data.navigationColor,
            navigationHoverColor: data.navigationHoverColor,
            navigationHighlightColor: data.navigationHighlightColor,
            navigationNumberColor: data.navigationNumberColor,
            positionNavigation: data.positionNavigation,
            navigationType: data.navigationType,
            showControl:  data.showControl,
            classButtonNext: undefined,
            classButtonPrevious: undefined,
            controlColor: data.controlColor,
            controlBackgroundColor: data.controlBackgroundColor,
            positionControl: data.positionControl,
            transition: data.transition,
            showProgress: data.showProgress,
            progressColor: data.progressColor,
            pauseOnHover: data.pauseOnHover
        });
    }
    return {
        initSlideshow: function ( data ) {
            initSlideshow(data);
        }
    }
}));