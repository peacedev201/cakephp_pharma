(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery', 'mooUser'], factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS-like
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals (root is window)
        root.mooReaction = factory(root.jQuery);
    }
}(this, function ($, mooUser) {

    var padding_of_parent = 8; //6 padding of parent

    var likeActivity = function(activity_id, item_type, id, reaction){

        if( $('#reaction_'+activity_id).find('.react-btn').hasClass('react-loading') ){
            return;
        }

        var type;

        if(item_type == 'photo_comment'){
            type = 'comment';
        } else{
            type = item_type;
        }

        $('#reaction_'+activity_id).find('.react-btn').addClass('react-loading');

        $.post(mooConfig.url.base + '/reactions/ajax_add/' + type + '/' + id + '/' + reaction, { noCache: 1 }, function(data){
            try
            {
                var res = $.parseJSON(data);

                if(res.is_like){
                    $('#reaction_'+activity_id).find('.react-btn').attr({
                        'data-reaction': res.reaction,
                        'data-label': res.label
                    }).html( '<span class="react-active-'+ res.ele_class +'"><i class="material-icons">thumb_up</i>'+ res.label +'</span>');
                    $('#reaction_'+activity_id).find('.react-circle').removeClass('react-active');
                    $('#reaction_'+activity_id).find('.react-circle[data-reaction='+res.reaction+']').addClass('react-active');
                }else{
                    $('#reaction_'+activity_id).find('.react-btn').attr({
                        'data-reaction': res.reaction,
                        'data-label': res.label
                    }).html( '<span class=""><i class="material-icons">thumb_up</i>'+ res.label +'</span>');
                    $('#reaction_'+activity_id).find('.react-circle').removeClass('react-active');
                }

                if(res.like_count > 0){
                    $('#react-result-like-'+activity_id).removeClass('react-see-hide');
                }else{
                    $('#react-result-like-'+activity_id).addClass('react-see-hide');
                }
                $('#react-result-like-'+activity_id).find('.react-result-count').html(res.like_count);

                if(res.love_count > 0){
                    $('#react-result-love-'+activity_id).removeClass('react-see-hide');
                }else{
                    $('#react-result-love-'+activity_id).addClass('react-see-hide');
                }
                $('#react-result-love-'+activity_id).find('.react-result-count').html(res.love_count);

                if(res.haha_count > 0){
                    $('#react-result-haha-'+activity_id).removeClass('react-see-hide');
                }else{
                    $('#react-result-haha-'+activity_id).addClass('react-see-hide');
                }
                $('#react-result-haha-'+activity_id).find('.react-result-count').html(res.haha_count);

                if(res.wow_count > 0){
                    $('#react-result-wow-'+activity_id).removeClass('react-see-hide');
                }else{
                    $('#react-result-wow-'+activity_id).addClass('react-see-hide');
                }
                $('#react-result-wow-'+activity_id).find('.react-result-count').html(res.wow_count);

                if(res.sad_count > 0){
                    $('#react-result-sad-'+activity_id).removeClass('react-see-hide');
                }else{
                    $('#react-result-sad-'+activity_id).addClass('react-see-hide');
                }
                $('#react-result-sad-'+activity_id).find('.react-result-count').html(res.sad_count);

                if(res.angry_count > 0){
                    $('#react-result-angry-'+activity_id).removeClass('react-see-hide');
                }else{
                    $('#react-result-angry-'+activity_id).addClass('react-see-hide');
                }
                $('#react-result-angry-'+activity_id).find('.react-result-count').html(res.angry_count);

                if(res.cool_count > 0){
                    $('#react-result-cool-'+activity_id).removeClass('react-see-hide');
                }else{
                    $('#react-result-cool-'+activity_id).addClass('react-see-hide');
                }
                $('#react-result-cool-'+activity_id).find('.react-result-count').html(res.cool_count);

                if(res.confused_count > 0){
                    $('#react-result-confused-'+activity_id).removeClass('react-see-hide');
                }else{
                    $('#react-result-confused-'+activity_id).addClass('react-see-hide');
                }
                $('#react-result-confused-'+activity_id).find('.react-result-count').html(res.confused_count);

                if(res.total_count > 0){
                    $('#react-result-total-'+activity_id).removeClass('react-see-hide');
                }else{
                    $('#react-result-total-'+activity_id).addClass('react-see-hide');
                }
                $('#react-result-total-'+activity_id).html(res.total_count);

                $('#reaction_'+activity_id).find('.react-btn').removeClass('react-loading');
            }
            catch (err)
            {
                $('#reaction_'+activity_id).find('.react-btn').removeClass('react-loading');
                mooUser.validateUser();
            }
        });
    };

    var initActivityReaction = function (activity_id) {
        //alert(reactionID);
        var animateReactionsOn;
        var animateReactionsOut;

        if (mooConfig.isMobile){
            $('#reaction_'+activity_id).find('.react-btn').click(function (e) {
                e.preventDefault();
                clearTimeout(animateReactionsOut);
                //clearTimeout(animateReactionsOn);
                /* -------------- */
                var windowWidth = $(window).width();
                var eleBox = $(this).parent();
                var boxOffset = eleBox.offset();
                var boxWidth = eleBox.outerWidth();
                //var boxOffsetLeftCenter = boxOffset.left + (boxWidth / 2);
                var eleReacts = eleBox.find('.reacts');
                var eleCircle = eleReacts.find('.react-circle');
                var popupWidth = eleCircle.outerWidth() * eleCircle.length + padding_of_parent;//6 padding of parent

                if(popupWidth > windowWidth){
                    var cricleNewWidth = (windowWidth - padding_of_parent) / eleCircle.length;
                    eleCircle.css({
                        width: cricleNewWidth+'px',
                        height: cricleNewWidth+'px'
                    });
                    popupWidth = eleCircle.outerWidth() * eleCircle.length + padding_of_parent;
                }
                var popupLeft = (popupWidth / 2)-(boxWidth/2);
                var popupOffsetLeft = boxOffset.left - popupLeft;

                if(popupOffsetLeft < 0 && (popupOffsetLeft + popupWidth) <= windowWidth){
                    popupLeft = popupLeft + popupOffsetLeft - 1;
                    eleReacts.css('left', '-'+popupLeft+'px');
                }else if(popupOffsetLeft >= 0 && (popupOffsetLeft + popupWidth) > windowWidth) {
                    popupLeft = popupLeft + ((popupOffsetLeft + popupWidth) - windowWidth) + 1;
                    eleReacts.css({
                        'left': '-'+popupLeft+'px'
                    });
                }else {
                    eleReacts.css({
                        'left': '-'+popupLeft+'px'
                    });
                }
                /* -------------- */

                var parent = $(this).parent();

                parent.addClass('react-show');
                parent.addClass('reaction-mobile');
                parent.find('.react-overview').show();

                animateReactionsOut = setTimeout(function () {
                    parent.removeClass("react-show").removeClass('reaction-mobile').find('.react-overview').hide();
                    clearTimeout(animateReactionsOut);
                    /* -------------- */
                    var eleReacts = parent.find('.reacts');
                    var eleCircle = eleReacts.find('.react-circle');
                    eleReacts.css({
                        'left': ''
                    });
                    eleCircle.css({
                        width: '',
                        height: ''
                    });
                    /* -------------- */
                }, 8000);
            });

            $('#reaction_'+activity_id).find('.react-overview').click(function (e) {
                e.preventDefault();
                var parent = $(this).parent();
                $(this).hide();
                parent.removeClass('react-show').removeClass('reaction-mobile');
                /* -------------- */
                var eleReacts = parent.find('.reacts');
                var eleCircle = eleReacts.find('.react-circle');
                eleReacts.css({
                    'left': ''
                });
                eleCircle.css({
                    width: '',
                    height: ''
                });
                /* -------------- */
            });

        }else{
            $('#reaction_'+activity_id).mouseenter(function () {

                /* -------------- */
                var windowWidth = $(window).width();
                var eleBox = $(this);
                var boxOffset = eleBox.offset();
                var boxWidth = eleBox.outerWidth();
                //var boxOffsetLeftCenter = boxOffset.left + (boxWidth / 2);
                var eleReacts = $(this).find('.reacts');
                var eleCircle = eleReacts.find('.react-circle');
                var popupWidth = eleCircle.outerWidth() * eleCircle.length + padding_of_parent;//6 padding of parent

                if(popupWidth > windowWidth){
                    var cricleNewWidth = (windowWidth - padding_of_parent) / eleCircle.length;
                    eleCircle.css({
                        width: cricleNewWidth+'px',
                        height: cricleNewWidth+'px'
                    });
                    popupWidth = eleCircle.outerWidth() * eleCircle.length + padding_of_parent;
                }
                var popupLeft = (popupWidth / 2)-(boxWidth/2);
                var popupOffsetLeft = boxOffset.left - popupLeft;

                if(popupOffsetLeft < 0 && (popupOffsetLeft + popupWidth) <= windowWidth){
                    popupLeft = popupLeft + popupOffsetLeft - 1;
                    eleReacts.css('left', '-'+popupLeft+'px');
                }else if(popupOffsetLeft >= 0 && (popupOffsetLeft + popupWidth) > windowWidth) {
                    popupLeft = popupLeft + ((popupOffsetLeft + popupWidth) - windowWidth) + 1;
                    eleReacts.css({
                        'left': '-'+popupLeft+'px'
                    });
                }else {
                    eleReacts.css({
                        'left': '-'+popupLeft+'px'
                    });
                }
                /* -------------- */
                var parent = $(this);

                animateReactionsOn = setTimeout(function () {
                    parent.addClass('react-show');
                }, 300);

                clearTimeout(animateReactionsOut);
            });

            $('#reaction_'+activity_id).mouseleave(function () {
                var parent = $(this);

                animateReactionsOut = setTimeout(function () {
                    parent.removeClass("react-show");
                    /* -------------- */
                    var eleReacts = parent.find('.reacts');
                    var eleCircle = eleReacts.find('.react-circle');
                    eleReacts.css({
                        'left': ''
                    });
                    eleCircle.css({
                        width: '',
                        height: ''
                    });
                    /* -------------- */
                }, 500);

                clearTimeout(animateReactionsOn);
            });

            $('#reaction_'+activity_id).find('.react-btn').click(function (e) {
                e.preventDefault();
                //var data = $(this).data();
                //console.log('truoc khi bam: ',$(this).data());
                clearTimeout(animateReactionsOut);
                clearTimeout(animateReactionsOn);

                likeActivity(activity_id,
                    $(this).attr('data-type'),
                    $(this).attr('data-id'),
                    $(this).attr('data-reaction')
                );
            });
        }

        $('#reaction_'+activity_id).find('.react-circle').click(function (e) {
            e.preventDefault();
            //var data = $(this).data();
            $(this).parent().parent().removeClass("react-show").removeClass("reaction-mobile").find('.react-overview').hide();
            clearTimeout(animateReactionsOut);
            clearTimeout(animateReactionsOn);

            likeActivity(activity_id,
                $(this).data('type'),
                $(this).data('id'),
                $(this).data('reaction')
            );
        });
    };

    var likeActivityComment = function(comment_id, item_type, id, reaction){

        if( $('#comment_reaction_'+comment_id).find('.react-btn').hasClass('react-loading') ){
            return;
        }

        var type;

        if(item_type == 'photo_comment'){
            type = 'comment';
        } else{
            type = item_type;
        }

        $('#comment_reaction_'+comment_id).find('.react-btn').addClass('react-loading');

        $.post(mooConfig.url.base + '/reactions/ajax_add/' + type + '/' + id + '/' + reaction, { noCache: 1 }, function(data){
            try
            {
                var res = $.parseJSON(data);

                if(res.is_like){
                    $('#comment_reaction_'+comment_id).find('.react-btn').attr({
                        'data-reaction': res.reaction,
                        'data-label': res.label
                    }).html( '<span class="react-active-'+ res.ele_class +'"><i class="material-icons">thumb_up</i>'+ res.label +'</span>');
                    $('#comment_reaction_'+comment_id).find('.react-circle').removeClass('react-active');
                    $('#comment_reaction_'+comment_id).find('.react-circle[data-reaction='+res.reaction+']').addClass('react-active');
                }else{
                    $('#comment_reaction_'+comment_id).find('.react-btn').attr({
                        'data-reaction': res.reaction,
                        'data-label': res.label
                    }).html( '<span class=""><i class="material-icons">thumb_up</i>'+ res.label +'</span>');
                    $('#comment_reaction_'+comment_id).find('.react-circle').removeClass('react-active');
                }

                if(res.like_count > 0){
                    $('#comment-react-result-like-'+comment_id).removeClass('react-see-hide');
                }else{
                    $('#comment-react-result-like-'+comment_id).addClass('react-see-hide');
                }
                $('#comment-react-result-like-'+comment_id).find('.react-result-count').html(res.like_count);

                if(res.love_count > 0){
                    $('#comment-react-result-love-'+comment_id).removeClass('react-see-hide');
                }else{
                    $('#comment-react-result-love-'+comment_id).addClass('react-see-hide');
                }
                $('#comment-react-result-love-'+comment_id).find('.react-result-count').html(res.love_count);

                if(res.haha_count > 0){
                    $('#comment-react-result-haha-'+comment_id).removeClass('react-see-hide');
                }else{
                    $('#comment-react-result-haha-'+comment_id).addClass('react-see-hide');
                }
                $('#comment-react-result-haha-'+comment_id).find('.react-result-count').html(res.haha_count);

                if(res.wow_count > 0){
                    $('#comment-react-result-wow-'+comment_id).removeClass('react-see-hide');
                }else{
                    $('#comment-react-result-wow-'+comment_id).addClass('react-see-hide');
                }
                $('#comment-react-result-wow-'+comment_id).find('.react-result-count').html(res.wow_count);

                if(res.sad_count > 0){
                    $('#comment-react-result-sad-'+comment_id).removeClass('react-see-hide');
                }else{
                    $('#comment-react-result-sad-'+comment_id).addClass('react-see-hide');
                }
                $('#comment-react-result-sad-'+comment_id).find('.react-result-count').html(res.sad_count);

                if(res.angry_count > 0){
                    $('#comment-react-result-angry-'+comment_id).removeClass('react-see-hide');
                }else{
                    $('#comment-react-result-angry-'+comment_id).addClass('react-see-hide');
                }
                $('#comment-react-result-angry-'+comment_id).find('.react-result-count').html(res.angry_count);

                if(res.cool_count > 0){
                    $('#comment-react-result-cool-'+comment_id).removeClass('react-see-hide');
                }else{
                    $('#comment-react-result-cool-'+comment_id).addClass('react-see-hide');
                }
                $('#comment-react-result-cool-'+comment_id).find('.react-result-count').html(res.cool_count);

                if(res.confused_count > 0){
                    $('#comment-react-result-confused-'+comment_id).removeClass('react-see-hide');
                }else{
                    $('#comment-react-result-confused-'+comment_id).addClass('react-see-hide');
                }
                $('#comment-react-result-confused-'+comment_id).find('.react-result-count').html(res.confused_count);

                if(res.total_count > 0){
                    $('#comment-react-result-total-'+comment_id).removeClass('react-see-hide');
                }else{
                    $('#comment-react-result-total-'+comment_id).addClass('react-see-hide');
                }
                $('#comment-react-result-total-'+comment_id).html(res.total_count);

                $('#comment_reaction_'+comment_id).find('.react-btn').removeClass('react-loading');
            }
            catch (err)
            {
                $('#comment_reaction_'+comment_id).find('.react-btn').removeClass('react-loading');
                mooUser.validateUser();
            }
        });
    };

    var initCommentReaction = function (comment_id) {
        //alert(reactionID);
        var animateReactionsOn;
        var animateReactionsOut;

        if (mooConfig.isMobile){
            $('#comment_reaction_'+comment_id).find('.react-btn').click(function (e) {
                e.preventDefault();
                clearTimeout(animateReactionsOut);
                //clearTimeout(animateReactionsOn);
                /* -------------- */
                if($(this).parents('#theaterComments').length > 0){
                    var eleComment = $(this).parents('#theaterComments');
                    var commentOffset = eleComment.offset();
                    var commentWidth = eleComment.outerWidth();
                    var _eleBox = $(this).parent();
                    var _boxOffset = _eleBox.offset();
                    var _boxWidth = _eleBox.outerWidth();

                    var _eleReacts = _eleBox.find('.reacts');
                    var _eleCircle = _eleReacts.find('.react-circle');
                    var _popupWidth = _eleCircle.outerWidth() * _eleCircle.length + padding_of_parent;//6 padding of parent

                    if(_popupWidth > commentWidth){
                        var _cricleNewWidth = (commentWidth - padding_of_parent) / _eleCircle.length;
                        _eleCircle.css({
                            width: _cricleNewWidth+'px',
                            height: _cricleNewWidth+'px'
                        });
                        _popupWidth = _eleCircle.outerWidth() * _eleCircle.length + padding_of_parent;
                    }
                    var _popupLeft = (_popupWidth / 2)-(_boxWidth/2);
                    var _popupOffsetLeft = _boxOffset.left - _popupLeft;

                    if(_popupOffsetLeft < commentOffset.left && (_popupOffsetLeft + _popupWidth) <= (commentOffset.left + commentWidth)){
                        _popupLeft = _popupLeft + _popupOffsetLeft - commentOffset.left - 1;
                        _eleReacts.css('left', '-'+_popupLeft+'px');
                    }else if(_popupOffsetLeft >= commentOffset.left && (_popupOffsetLeft + _popupWidth) > (commentOffset.left + commentWidth)) {
                        _popupLeft = _popupLeft + ((_popupOffsetLeft - commentOffset.left + _popupWidth) - commentWidth) + 1;
                        _eleReacts.css({
                            'left': '-'+_popupLeft+'px'
                        });
                    }else {
                        _eleReacts.css({
                            'left': '-'+_popupLeft+'px'
                        });
                    }
                }else{
                    var windowWidth = $(window).width();
                    var eleBox = $(this).parent();
                    var boxOffset = eleBox.offset();
                    var boxWidth = eleBox.outerWidth();
                    //var boxOffsetLeftCenter = boxOffset.left + (boxWidth / 2);
                    var eleReacts = eleBox.find('.reacts');
                    var eleCircle = eleReacts.find('.react-circle');
                    var popupWidth = eleCircle.outerWidth() * eleCircle.length + padding_of_parent;//6 padding of parent

                    if(popupWidth > windowWidth){
                        var cricleNewWidth = (windowWidth - padding_of_parent) / eleCircle.length;
                        eleCircle.css({
                            width: cricleNewWidth+'px',
                            height: cricleNewWidth+'px'
                        });
                        popupWidth = eleCircle.outerWidth() * eleCircle.length + padding_of_parent;
                    }
                    var popupLeft = (popupWidth / 2)-(boxWidth/2);
                    var popupOffsetLeft = boxOffset.left - popupLeft;

                    if(popupOffsetLeft < 0 && (popupOffsetLeft + popupWidth) <= windowWidth){
                        popupLeft = popupLeft + popupOffsetLeft - 1;
                        eleReacts.css('left', '-'+popupLeft+'px');
                    }else if(popupOffsetLeft >= 0 && (popupOffsetLeft + popupWidth) > windowWidth) {
                        popupLeft = popupLeft + ((popupOffsetLeft + popupWidth) - windowWidth) + 1;
                        eleReacts.css({
                            'left': '-'+popupLeft+'px'
                        });
                    }else {
                        eleReacts.css({
                            'left': '-'+popupLeft+'px'
                        });
                    }
                }
                /* -------------- */
                var parent = $(this).parent();

                parent.addClass('react-show');
                parent.addClass('reaction-mobile');
                parent.find('.react-overview').show();

                animateReactionsOut = setTimeout(function () {
                    parent.removeClass("react-show").removeClass('reaction-mobile').find('.react-overview').hide();
                    clearTimeout(animateReactionsOut);
                    /* -------------- */
                    var eleReacts = parent.find('.reacts');
                    var eleCircle = eleReacts.find('.react-circle');
                    eleReacts.css({
                        'left': ''
                    });
                    eleCircle.css({
                        width: '',
                        height: ''
                    });
                    /* -------------- */
                }, 8000);
            });

            $('#comment_reaction_'+comment_id).find('.react-overview').click(function (e) {
                e.preventDefault();
                var parent = $(this).parent();
                $(this).hide();
                parent.removeClass('react-show').removeClass('reaction-mobile');
                /* -------------- */
                var eleReacts = parent.find('.reacts');
                var eleCircle = eleReacts.find('.react-circle');
                eleReacts.css({
                    'left': ''
                });
                eleCircle.css({
                    width: '',
                    height: ''
                });
                /* -------------- */
            });

        }else{
            $('#comment_reaction_'+comment_id).mouseenter(function () {
                /* -------------- */
                if($(this).parents('#theaterComments').length > 0){
                    var eleComment = $(this).parents('#theaterComments');
                    var commentOffset = eleComment.offset();
                    var commentWidth = eleComment.outerWidth();
                    var _eleBox = $(this);
                    var _boxOffset = _eleBox.offset();
                    var _boxWidth = _eleBox.outerWidth();

                    var _eleReacts = $(this).find('.reacts');
                    var _eleCircle = _eleReacts.find('.react-circle');
                    var _popupWidth = _eleCircle.outerWidth() * _eleCircle.length + padding_of_parent;//6 padding of parent

                    if(_popupWidth > commentWidth){
                        var _cricleNewWidth = (commentWidth - padding_of_parent) / _eleCircle.length;
                        _eleCircle.css({
                            width: _cricleNewWidth+'px',
                            height: _cricleNewWidth+'px'
                        });
                        _popupWidth = _eleCircle.outerWidth() * _eleCircle.length + padding_of_parent;
                    }
                    var _popupLeft = (_popupWidth / 2)-(_boxWidth/2);
                    var _popupOffsetLeft = _boxOffset.left - _popupLeft;

                    if(_popupOffsetLeft < commentOffset.left && (_popupOffsetLeft + _popupWidth) <= (commentOffset.left + commentWidth)){
                        _popupLeft = _popupLeft + _popupOffsetLeft - commentOffset.left - 1;
                        _eleReacts.css('left', '-'+_popupLeft+'px');
                    }else if(_popupOffsetLeft >= commentOffset.left && (_popupOffsetLeft + _popupWidth) > (commentOffset.left + commentWidth)) {
                        _popupLeft = _popupLeft + ((_popupOffsetLeft - commentOffset.left + _popupWidth) - commentWidth) + 1;
                        _eleReacts.css({
                            'left': '-'+_popupLeft+'px'
                        });
                    }else {
                        _eleReacts.css({
                            'left': '-'+_popupLeft+'px'
                        });
                    }
                }else{
                    var windowWidth = $(window).width();
                    var eleBox = $(this);
                    var boxOffset = eleBox.offset();
                    var boxWidth = eleBox.outerWidth();
                    //var boxOffsetLeftCenter = boxOffset.left + (boxWidth / 2);
                    var eleReacts = $(this).find('.reacts');
                    var eleCircle = eleReacts.find('.react-circle');
                    var popupWidth = eleCircle.outerWidth() * eleCircle.length + padding_of_parent;//6 padding of parent

                    if(popupWidth > windowWidth){
                        var cricleNewWidth = (windowWidth - padding_of_parent) / eleCircle.length;
                        eleCircle.css({
                            width: cricleNewWidth+'px',
                            height: cricleNewWidth+'px'
                        });
                        popupWidth = eleCircle.outerWidth() * eleCircle.length + padding_of_parent;
                    }
                    var popupLeft = (popupWidth / 2)-(boxWidth/2);
                    var popupOffsetLeft = boxOffset.left - popupLeft;

                    if(popupOffsetLeft < 0 && (popupOffsetLeft + popupWidth) <= windowWidth){
                        popupLeft = popupLeft + popupOffsetLeft - 1;
                        eleReacts.css('left', '-'+popupLeft+'px');
                    }else if(popupOffsetLeft >= 0 && (popupOffsetLeft + popupWidth) > windowWidth) {
                        popupLeft = popupLeft + ((popupOffsetLeft + popupWidth) - windowWidth) + 1;
                        eleReacts.css({
                            'left': '-'+popupLeft+'px'
                        });
                    }else {
                        eleReacts.css({
                            'left': '-'+popupLeft+'px'
                        });
                    }
                }
                /* -------------- */
                var parent = $(this);

                animateReactionsOn = setTimeout(function () {
                    parent.addClass('react-show');
                }, 300);

                clearTimeout(animateReactionsOut);
            });

            $('#comment_reaction_'+comment_id).mouseleave(function () {
                var parent = $(this);

                animateReactionsOut = setTimeout(function () {
                    parent.removeClass("react-show");
                    /* -------------- */
                    var eleReacts = parent.find('.reacts');
                    var eleCircle = eleReacts.find('.react-circle');
                    eleReacts.css({
                        'left': ''
                    });
                    eleCircle.css({
                        width: '',
                        height: ''
                    });
                    /* -------------- */
                }, 500);

                clearTimeout(animateReactionsOn);
            });

            $('#comment_reaction_'+comment_id).find('.react-btn').click(function (e) {
                e.preventDefault();
                //var data = $(this).data();
                //console.log('truoc khi bam: ',$(this).data());
                clearTimeout(animateReactionsOut);
                clearTimeout(animateReactionsOn);

                likeActivityComment(comment_id,
                    $(this).attr('data-type'),
                    $(this).attr('data-id'),
                    $(this).attr('data-reaction')
                );
            });
        }

        $('#comment_reaction_'+comment_id).find('.react-circle').click(function (e) {
            e.preventDefault();
            //var data = $(this).data();
            $(this).parent().parent().removeClass("react-show").removeClass("reaction-mobile").find('.react-overview').hide();
            clearTimeout(animateReactionsOut);
            clearTimeout(animateReactionsOn);

            likeActivityComment(comment_id,
                $(this).data('type'),
                $(this).data('id'),
                $(this).data('reaction')
            );
        });
    };
    /*var initCommentReactionBK = function (comment_id) {
        //alert(reactionID);
        var animateReactionsOn;
        var animateReactionsOut;

        $('#comment_reaction_'+comment_id).mouseenter(function () {

            var parent = $(this);

            animateReactionsOn = setTimeout(function () {
                parent.addClass('react-show');
            }, 1000);

            clearTimeout(animateReactionsOut);
        });

        $('#comment_reaction_'+comment_id).mouseleave(function () {
            var parent = $(this);

            animateReactionsOut = setTimeout(function () {
                parent.removeClass("react-show");
            }, 1000);

            clearTimeout(animateReactionsOn);
        });

        $('#comment_reaction_'+comment_id).find('.react-btn').click(function (e) {
            e.preventDefault();
            //var data = $(this).data();
            //console.log('truoc khi bam: ',$(this).data());
            clearTimeout(animateReactionsOut);
            clearTimeout(animateReactionsOn);

            likeActivityComment(comment_id,
                $(this).attr('data-type'),
                $(this).attr('data-id'),
                $(this).attr('data-reaction')
            );
        });

        $('#comment_reaction_'+comment_id).find('.react-circle').click(function (e) {
            e.preventDefault();
            //var data = $(this).data();
            $(this).parent().parent().removeClass("react-show");
            clearTimeout(animateReactionsOut);
            clearTimeout(animateReactionsOn);

            likeActivityComment(comment_id,
                $(this).data('type'),
                $(this).data('id'),
                $(this).data('reaction')
            );
        });
    };*/

    var likeItemComment = function(item_id, item_type, id, reaction){

        if( $('#item_reaction_'+item_id).find('.react-btn').hasClass('react-loading') ){
            return;
        }

        $('#item_reaction_'+item_id).find('.react-btn').addClass('react-loading');

        $.post(mooConfig.url.base + '/reactions/ajax_add/' + item_type + '/' + id + '/' + reaction, { noCache: 1 }, function(data){
            try
            {
                var res = $.parseJSON(data);

                if(res.is_like){
                    $('#item_reaction_'+item_id).find('.react-btn').attr({
                        'data-reaction': res.reaction,
                        'data-label': res.label
                    }).html( '<span class="react-active-'+ res.ele_class +'"><i class="material-icons">thumb_up</i>'+ res.label +'</span>');
                    $('#item_reaction_'+item_id).find('.react-circle').removeClass('react-active');
                    $('#item_reaction_'+item_id).find('.react-circle[data-reaction='+res.reaction+']').addClass('react-active');
                }else{
                    $('#item_reaction_'+item_id).find('.react-btn').attr({
                        'data-reaction': res.reaction,
                        'data-label': res.label
                    }).html( '<span class=""><i class="material-icons">thumb_up</i>'+ res.label +'</span>');
                    $('#item_reaction_'+item_id).find('.react-circle').removeClass('react-active');
                }

                if(res.like_count > 0){
                    $('#item-react-result-like-'+item_id).removeClass('react-see-hide');
                }else{
                    $('#item-react-result-like-'+item_id).addClass('react-see-hide');
                }
                $('#item-react-result-like-'+item_id).find('.react-result-count').html(res.like_count);

                if(res.love_count > 0){
                    $('#item-react-result-love-'+item_id).removeClass('react-see-hide');
                }else{
                    $('#item-react-result-love-'+item_id).addClass('react-see-hide');
                }
                $('#item-react-result-love-'+item_id).find('.react-result-count').html(res.love_count);

                if(res.haha_count > 0){
                    $('#item-react-result-haha-'+item_id).removeClass('react-see-hide');
                }else{
                    $('#item-react-result-haha-'+item_id).addClass('react-see-hide');
                }
                $('#item-react-result-haha-'+item_id).find('.react-result-count').html(res.haha_count);

                if(res.wow_count > 0){
                    $('#item-react-result-wow-'+item_id).removeClass('react-see-hide');
                }else{
                    $('#item-react-result-wow-'+item_id).addClass('react-see-hide');
                }
                $('#item-react-result-wow-'+item_id).find('.react-result-count').html(res.wow_count);

                if(res.sad_count > 0){
                    $('#item-react-result-sad-'+item_id).removeClass('react-see-hide');
                }else{
                    $('#item-react-result-sad-'+item_id).addClass('react-see-hide');
                }
                $('#item-react-result-sad-'+item_id).find('.react-result-count').html(res.sad_count);

                if(res.angry_count > 0){
                    $('#item-react-result-angry-'+item_id).removeClass('react-see-hide');
                }else{
                    $('#item-react-result-angry-'+item_id).addClass('react-see-hide');
                }
                $('#item-react-result-angry-'+item_id).find('.react-result-count').html(res.angry_count);

                if(res.cool_count > 0){
                    $('#item-react-result-cool-'+item_id).removeClass('react-see-hide');
                }else{
                    $('#item-react-result-cool-'+item_id).addClass('react-see-hide');
                }
                $('#item-react-result-cool-'+item_id).find('.react-result-count').html(res.cool_count);

                if(res.confused_count > 0){
                    $('#item-react-result-confused-'+item_id).removeClass('react-see-hide');
                }else{
                    $('#item-react-result-confused-'+item_id).addClass('react-see-hide');
                }
                $('#item-react-result-confused-'+item_id).find('.react-result-count').html(res.confused_count);

                if(res.total_count > 0){
                    $('#item-react-result-total-'+item_id).removeClass('react-see-hide');
                }else{
                    $('#item-react-result-total-'+item_id).addClass('react-see-hide');
                }
                $('#item-react-result-total-'+item_id).html(res.total_count);

                $('#item_reaction_'+item_id).find('.react-btn').removeClass('react-loading');
            }
            catch (err)
            {
                $('#item_reaction_'+item_id).find('.react-btn').removeClass('react-loading');
                mooUser.validateUser();
            }
        });
    };

    var initItemReaction = function (item_id) {
        //alert(reactionID);
        var animateReactionsOn;
        var animateReactionsOut;

        if (mooConfig.isMobile){
            $('#item_reaction_'+item_id).find('.react-btn').click(function (e) {
                e.preventDefault();
                clearTimeout(animateReactionsOut);
                //clearTimeout(animateReactionsOn);
                /* -------------- */
                var windowWidth = $(window).width();
                var eleBox = $(this).parent();
                var boxOffset = eleBox.offset();
                var boxWidth = eleBox.outerWidth();
                //var boxOffsetLeftCenter = boxOffset.left + (boxWidth / 2);
                var eleReacts = eleBox.find('.reacts');
                var eleCircle = eleReacts.find('.react-circle');
                var popupWidth = eleCircle.outerWidth() * eleCircle.length + padding_of_parent;//6 padding of parent

                if(popupWidth > windowWidth){
                    var cricleNewWidth = (windowWidth - padding_of_parent) / eleCircle.length;
                    eleCircle.css({
                        width: cricleNewWidth+'px',
                        height: cricleNewWidth+'px'
                    });
                    popupWidth = eleCircle.outerWidth() * eleCircle.length + padding_of_parent;
                }
                var popupLeft = (popupWidth / 2)-(boxWidth/2);
                var popupOffsetLeft = boxOffset.left - popupLeft;

                if(popupOffsetLeft < 0 && (popupOffsetLeft + popupWidth) <= windowWidth){
                    popupLeft = popupLeft + popupOffsetLeft - 1;
                    eleReacts.css('left', '-'+popupLeft+'px');
                }else if(popupOffsetLeft >= 0 && (popupOffsetLeft + popupWidth) > windowWidth) {
                    popupLeft = popupLeft + ((popupOffsetLeft + popupWidth) - windowWidth) + 1;
                    eleReacts.css({
                        'left': '-'+popupLeft+'px'
                    });
                }else {
                    eleReacts.css({
                        'left': '-'+popupLeft+'px'
                    });
                }
                /* -------------- */

                var parent = $(this).parent();

                parent.addClass('react-show');
                parent.addClass('reaction-mobile');
                parent.find('.react-overview').show();

                animateReactionsOut = setTimeout(function () {
                    parent.removeClass("react-show").removeClass('reaction-mobile').find('.react-overview').hide();
                    clearTimeout(animateReactionsOut);
                    /* -------------- */
                    var eleReacts = parent.find('.reacts');
                    var eleCircle = eleReacts.find('.react-circle');
                    eleReacts.css({
                        'left': ''
                    });
                    eleCircle.css({
                        width: '',
                        height: ''
                    });
                    /* -------------- */
                }, 8000);
            });

            $('#item_reaction_'+item_id).find('.react-overview').click(function (e) {
                e.preventDefault();
                var parent = $(this).parent();
                $(this).hide();
                parent.removeClass('react-show').removeClass('reaction-mobile');
                /* -------------- */
                var eleReacts = parent.find('.reacts');
                var eleCircle = eleReacts.find('.react-circle');
                eleReacts.css({
                    'left': ''
                });
                eleCircle.css({
                    width: '',
                    height: ''
                });
                /* -------------- */
            });

        }else{
            $('#item_reaction_'+item_id).mouseenter(function () {
                /* -------------- */
                var windowWidth = $(window).width();
                var eleBox = $(this);
                var boxOffset = eleBox.offset();
                var boxWidth = eleBox.outerWidth();
                //var boxOffsetLeftCenter = boxOffset.left + (boxWidth / 2);
                var eleReacts = $(this).find('.reacts');
                var eleCircle = eleReacts.find('.react-circle');
                var popupWidth = eleCircle.outerWidth() * eleCircle.length + padding_of_parent;//6 padding of parent

                if(popupWidth > windowWidth){
                    var cricleNewWidth = (windowWidth - padding_of_parent) / eleCircle.length;
                    eleCircle.css({
                        width: cricleNewWidth+'px',
                        height: cricleNewWidth+'px'
                    });
                    popupWidth = eleCircle.outerWidth() * eleCircle.length + padding_of_parent;
                }
                var popupLeft = (popupWidth / 2)-(boxWidth/2);
                var popupOffsetLeft = boxOffset.left - popupLeft;

                if(popupOffsetLeft < 0 && (popupOffsetLeft + popupWidth) <= windowWidth){
                    popupLeft = popupLeft + popupOffsetLeft - 1;
                    eleReacts.css('left', '-'+popupLeft+'px');
                }else if(popupOffsetLeft >= 0 && (popupOffsetLeft + popupWidth) > windowWidth) {
                    popupLeft = popupLeft + ((popupOffsetLeft + popupWidth) - windowWidth) + 1;
                    eleReacts.css({
                        'left': '-'+popupLeft+'px'
                    });
                }else {
                    eleReacts.css({
                        'left': '-'+popupLeft+'px'
                    });
                }
                /* -------------- */
                var parent = $(this);

                animateReactionsOn = setTimeout(function () {
                    parent.addClass('react-show');
                }, 300);

                clearTimeout(animateReactionsOut);
            });

            $('#item_reaction_'+item_id).mouseleave(function () {
                var parent = $(this);

                animateReactionsOut = setTimeout(function () {
                    parent.removeClass("react-show");
                    /* -------------- */
                    var eleReacts = parent.find('.reacts');
                    var eleCircle = eleReacts.find('.react-circle');
                    eleReacts.css({
                        'left': ''
                    });
                    eleCircle.css({
                        width: '',
                        height: ''
                    });
                    /* -------------- */
                }, 500);

                clearTimeout(animateReactionsOn);
            });

            $('#item_reaction_'+item_id).find('.react-btn').click(function (e) {
                e.preventDefault();
                //var data = $(this).data();
                //console.log('truoc khi bam: ',$(this).data());
                clearTimeout(animateReactionsOut);
                clearTimeout(animateReactionsOn);

                likeItemComment(item_id,
                    $(this).attr('data-type'),
                    $(this).attr('data-id'),
                    $(this).attr('data-reaction')
                );
            });
        }

        $('#item_reaction_'+item_id).find('.react-circle').click(function (e) {
            e.preventDefault();
            //var data = $(this).data();
            $(this).parent().parent().removeClass("react-show").removeClass("reaction-mobile").find('.react-overview').hide();
            clearTimeout(animateReactionsOut);
            clearTimeout(animateReactionsOn);

            likeItemComment(item_id,
                $(this).data('type'),
                $(this).data('id'),
                $(this).data('reaction')
            );
        });
    };
    /*var initItemReactionX = function (item_id) {
        //alert(reactionID);
        var animateReactionsOn;
        var animateReactionsOut;

        $('#item_reaction_'+item_id).mouseenter(function () {

            var parent = $(this);

            animateReactionsOn = setTimeout(function () {
                parent.addClass('react-show');
            }, 1000);

            clearTimeout(animateReactionsOut);
        });

        $('#item_reaction_'+item_id).mouseleave(function () {
            var parent = $(this);

            animateReactionsOut = setTimeout(function () {
                parent.removeClass("react-show");
            }, 1000);

            clearTimeout(animateReactionsOn);
        });

        $('#item_reaction_'+item_id).find('.react-btn').click(function (e) {
            e.preventDefault();
            //var data = $(this).data();
            //console.log('truoc khi bam: ',$(this).data());
            clearTimeout(animateReactionsOut);
            clearTimeout(animateReactionsOn);

            likeItemComment(item_id,
                $(this).attr('data-type'),
                $(this).attr('data-id'),
                $(this).attr('data-reaction')
            );
        });

        $('#item_reaction_'+item_id).find('.react-circle').click(function (e) {
            e.preventDefault();
            //var data = $(this).data();
            $(this).parent().parent().removeClass("react-show");
            clearTimeout(animateReactionsOut);
            clearTimeout(animateReactionsOn);

            likeItemComment(item_id,
                $(this).data('type'),
                $(this).data('id'),
                $(this).data('reaction')
            );
        });
    };*/

    var likePhotoComment = function(photo_id, item_type, id, reaction){
        if( $('#photo_reaction_'+photo_id).find('.react-btn').hasClass('react-loading') ){
            return;
        }

        $('#photo_reaction_'+photo_id).find('.react-btn').addClass('react-loading');

        $.post(mooConfig.url.base + '/reactions/ajax_add/' + item_type + '/' + id + '/' + reaction, { noCache: 1 }, function(data){
            try
            {
                var res = $.parseJSON(data);

                if(res.is_like){
                    $('#photo_reaction_'+photo_id).find('.react-btn').attr({
                        'data-reaction': res.reaction,
                        'data-label': res.label
                    }).html( '<span class="react-active-'+ res.ele_class +'"><i class="material-icons">thumb_up</i>'+ res.label +'</span>');
                    $('#photo_reaction_'+photo_id).find('.react-circle').removeClass('react-active');
                    $('#photo_reaction_'+photo_id).find('.react-circle[data-reaction='+res.reaction+']').addClass('react-active');
                }else{
                    $('#photo_reaction_'+photo_id).find('.react-btn').attr({
                        'data-reaction': res.reaction,
                        'data-label': res.label
                    }).html( '<span class=""><i class="material-icons">thumb_up</i>'+ res.label +'</span>');
                    $('#photo_reaction_'+photo_id).find('.react-circle').removeClass('react-active');
                }

                if(res.like_count > 0){
                    $('#photo-react-result-like-'+photo_id).removeClass('react-see-hide');
                }else{
                    $('#photo-react-result-like-'+photo_id).addClass('react-see-hide');
                }
                $('#photo-react-result-like-'+photo_id).find('.react-result-count').html(res.like_count);

                if(res.love_count > 0){
                    $('#photo-react-result-love-'+photo_id).removeClass('react-see-hide');
                }else{
                    $('#photo-react-result-love-'+photo_id).addClass('react-see-hide');
                }
                $('#photo-react-result-love-'+photo_id).find('.react-result-count').html(res.love_count);

                if(res.haha_count > 0){
                    $('#photo-react-result-haha-'+photo_id).removeClass('react-see-hide');
                }else{
                    $('#photo-react-result-haha-'+photo_id).addClass('react-see-hide');
                }
                $('#photo-react-result-haha-'+photo_id).find('.react-result-count').html(res.haha_count);

                if(res.wow_count > 0){
                    $('#photo-react-result-wow-'+photo_id).removeClass('react-see-hide');
                }else{
                    $('#photo-react-result-wow-'+photo_id).addClass('react-see-hide');
                }
                $('#photo-react-result-wow-'+photo_id).find('.react-result-count').html(res.wow_count);

                if(res.sad_count > 0){
                    $('#photo-react-result-sad-'+photo_id).removeClass('react-see-hide');
                }else{
                    $('#photo-react-result-sad-'+photo_id).addClass('react-see-hide');
                }
                $('#photo-react-result-sad-'+photo_id).find('.react-result-count').html(res.sad_count);

                if(res.angry_count > 0){
                    $('#photo-react-result-angry-'+photo_id).removeClass('react-see-hide');
                }else{
                    $('#photo-react-result-angry-'+photo_id).addClass('react-see-hide');
                }
                $('#photo-react-result-angry-'+photo_id).find('.react-result-count').html(res.angry_count);

                if(res.cool_count > 0){
                    $('#photo-react-result-cool-'+photo_id).removeClass('react-see-hide');
                }else{
                    $('#photo-react-result-cool-'+photo_id).addClass('react-see-hide');
                }
                $('#photo-react-result-cool-'+photo_id).find('.react-result-count').html(res.cool_count);

                if(res.confused_count > 0){
                    $('#photo-react-result-confused-'+photo_id).removeClass('react-see-hide');
                }else{
                    $('#photo-react-result-confused-'+photo_id).addClass('react-see-hide');
                }
                $('#photo-react-result-confused-'+photo_id).find('.react-result-count').html(res.confused_count);

                if(res.total_count > 0){
                    $('#photo-react-result-total-'+photo_id).removeClass('react-see-hide');
                }else{
                    $('#photo-react-result-total-'+photo_id).addClass('react-see-hide');
                }
                $('#photo-react-result-total-'+photo_id).html(res.total_count);

                $('#photo_reaction_'+photo_id).find('.react-btn').removeClass('react-loading');
            }
            catch (err)
            {
                $('#photo_reaction_'+photo_id).find('.react-btn').removeClass('react-loading');
                mooUser.validateUser();
            }
        });
    };

    var initPhotoReaction = function (photo_id) {
        //alert(reactionID);
        var animateReactionsOn;
        var animateReactionsOut;

        if (mooConfig.isMobile){
            $('#photo_reaction_'+photo_id).find('.react-btn').click(function (e) {
                e.preventDefault();
                clearTimeout(animateReactionsOut);
                //clearTimeout(animateReactionsOn);
                /* -------------- */
                if($(this).parents('.photo_on_theater').length > 0){
                    var eleComment = $(this).parents('.photo_left');
                    var commentOffset = eleComment.offset();
                    var commentWidth = eleComment.outerWidth();
                    var _eleBox = $(this).parent();
                    var _boxOffset = _eleBox.offset();
                    var _boxWidth = _eleBox.outerWidth();

                    var _eleReacts = _eleBox.find('.reacts');
                    var _eleCircle = _eleReacts.find('.react-circle');
                    var _popupWidth = _eleCircle.outerWidth() * _eleCircle.length + padding_of_parent;//6 padding of parent

                    if(_popupWidth > commentWidth){
                        var _cricleNewWidth = (commentWidth - padding_of_parent) / _eleCircle.length;
                        _eleCircle.css({
                            width: _cricleNewWidth+'px',
                            height: _cricleNewWidth+'px'
                        });
                        _popupWidth = _eleCircle.outerWidth() * _eleCircle.length + padding_of_parent;
                    }
                    var _popupLeft = (_popupWidth / 2)-(_boxWidth/2);
                    var _popupOffsetLeft = _boxOffset.left - _popupLeft;

                    if(_popupOffsetLeft < commentOffset.left && (_popupOffsetLeft + _popupWidth) <= (commentOffset.left + commentWidth)){
                        _popupLeft = _popupLeft + _popupOffsetLeft - commentOffset.left - 1;
                        _eleReacts.css('left', '-'+_popupLeft+'px');
                    }else if(_popupOffsetLeft >= commentOffset.left && (_popupOffsetLeft + _popupWidth) > (commentOffset.left + commentWidth)) {
                        _popupLeft = _popupLeft + ((_popupOffsetLeft - commentOffset.left + _popupWidth) - commentWidth) + 1;
                        _eleReacts.css({
                            'left': '-'+_popupLeft+'px'
                        });
                    }else {
                        _eleReacts.css({
                            'left': '-'+_popupLeft+'px'
                        });
                    }
                }else{
                    var windowWidth = $(window).width();
                    var eleBox = $(this).parent();
                    var boxOffset = eleBox.offset();
                    var boxWidth = eleBox.outerWidth();
                    //var boxOffsetLeftCenter = boxOffset.left + (boxWidth / 2);
                    var eleReacts = eleBox.find('.reacts');
                    var eleCircle = eleReacts.find('.react-circle');
                    var popupWidth = eleCircle.outerWidth() * eleCircle.length + padding_of_parent;//6 padding of parent

                    if(popupWidth > windowWidth){
                        var cricleNewWidth = (windowWidth - padding_of_parent) / eleCircle.length;
                        eleCircle.css({
                            width: cricleNewWidth+'px',
                            height: cricleNewWidth+'px'
                        });
                        popupWidth = eleCircle.outerWidth() * eleCircle.length + padding_of_parent;
                    }
                    var popupLeft = (popupWidth / 2)-(boxWidth/2);
                    var popupOffsetLeft = boxOffset.left - popupLeft;

                    if(popupOffsetLeft < 0 && (popupOffsetLeft + popupWidth) <= windowWidth){
                        popupLeft = popupLeft + popupOffsetLeft - 1;
                        eleReacts.css('left', '-'+popupLeft+'px');
                    }else if(popupOffsetLeft >= 0 && (popupOffsetLeft + popupWidth) > windowWidth) {
                        popupLeft = popupLeft + ((popupOffsetLeft + popupWidth) - windowWidth) + 1;
                        eleReacts.css({
                            'left': '-'+popupLeft+'px'
                        });
                    }else {
                        eleReacts.css({
                            'left': '-'+popupLeft+'px'
                        });
                    }
                }
                /* -------------- */

                var parent = $(this).parent();

                parent.addClass('react-show');
                parent.addClass('reaction-mobile');
                parent.find('.react-overview').show();

                animateReactionsOut = setTimeout(function () {
                    parent.removeClass("react-show").removeClass('reaction-mobile').find('.react-overview').hide();
                    clearTimeout(animateReactionsOut);
                    /* -------------- */
                    var eleReacts = parent.find('.reacts');
                    var eleCircle = eleReacts.find('.react-circle');
                    eleReacts.css({
                        'left': ''
                    });
                    eleCircle.css({
                        width: '',
                        height: ''
                    });
                    /* -------------- */
                }, 8000);
            });

            $('#photo_reaction_'+photo_id).find('.react-overview').click(function (e) {
                e.preventDefault();
                var parent = $(this).parent();
                $(this).hide();
                parent.removeClass('react-show').removeClass('reaction-mobile');
                /* -------------- */
                var eleReacts = parent.find('.reacts');
                var eleCircle = eleReacts.find('.react-circle');
                eleReacts.css({
                    'left': ''
                });
                eleCircle.css({
                    width: '',
                    height: ''
                });
                /* -------------- */
            });

        }else{
            $('#photo_reaction_'+photo_id).mouseenter(function () {
                /* -------------- */
                if($(this).parents('.photo_on_theater').length > 0){
                    var eleComment = $(this).parents('.photo_left');
                    var commentOffset = eleComment.offset();
                    var commentWidth = eleComment.outerWidth();
                    var _eleBox = $(this);
                    var _boxOffset = _eleBox.offset();
                    var _boxWidth = _eleBox.outerWidth();

                    var _eleReacts = $(this).find('.reacts');
                    var _eleCircle = _eleReacts.find('.react-circle');
                    var _popupWidth = _eleCircle.outerWidth() * _eleCircle.length + padding_of_parent;//6 padding of parent

                    if(_popupWidth > commentWidth){
                        var _cricleNewWidth = (commentWidth - padding_of_parent) / _eleCircle.length;
                        _eleCircle.css({
                            width: _cricleNewWidth+'px',
                            height: _cricleNewWidth+'px'
                        });
                        _popupWidth = _eleCircle.outerWidth() * _eleCircle.length + padding_of_parent;
                    }
                    var _popupLeft = (_popupWidth / 2)-(_boxWidth/2);
                    var _popupOffsetLeft = _boxOffset.left - _popupLeft;

                    if(_popupOffsetLeft < commentOffset.left && (_popupOffsetLeft + _popupWidth) <= (commentOffset.left + commentWidth)){
                        _popupLeft = _popupLeft + _popupOffsetLeft - commentOffset.left - 1;
                        _eleReacts.css('left', '-'+_popupLeft+'px');
                    }else if(_popupOffsetLeft >= commentOffset.left && (_popupOffsetLeft + _popupWidth) > (commentOffset.left + commentWidth)) {
                        _popupLeft = _popupLeft + ((_popupOffsetLeft - commentOffset.left + _popupWidth) - commentWidth) + 1;
                        _eleReacts.css({
                            'left': '-'+_popupLeft+'px'
                        });
                    }else {
                        _eleReacts.css({
                            'left': '-'+_popupLeft+'px'
                        });
                    }
                }else{
                    var windowWidth = $(window).width();
                    var eleBox = $(this);
                    var boxOffset = eleBox.offset();
                    var boxWidth = eleBox.outerWidth();
                    //var boxOffsetLeftCenter = boxOffset.left + (boxWidth / 2);
                    var eleReacts = $(this).find('.reacts');
                    var eleCircle = eleReacts.find('.react-circle');
                    var popupWidth = eleCircle.outerWidth() * eleCircle.length + padding_of_parent;//6 padding of parent

                    if(popupWidth > windowWidth){
                        var cricleNewWidth = (windowWidth - padding_of_parent) / eleCircle.length;
                        eleCircle.css({
                            width: cricleNewWidth+'px',
                            height: cricleNewWidth+'px'
                        });
                        popupWidth = eleCircle.outerWidth() * eleCircle.length + padding_of_parent;
                    }
                    var popupLeft = (popupWidth / 2)-(boxWidth/2);
                    var popupOffsetLeft = boxOffset.left - popupLeft;

                    if(popupOffsetLeft < 0 && (popupOffsetLeft + popupWidth) <= windowWidth){
                        popupLeft = popupLeft + popupOffsetLeft - 1;
                        eleReacts.css('left', '-'+popupLeft+'px');
                    }else if(popupOffsetLeft >= 0 && (popupOffsetLeft + popupWidth) > windowWidth) {
                        popupLeft = popupLeft + ((popupOffsetLeft + popupWidth) - windowWidth) + 1;
                        eleReacts.css({
                            'left': '-'+popupLeft+'px'
                        });
                    }else {
                        eleReacts.css({
                            'left': '-'+popupLeft+'px'
                        });
                    }
                }
                /* -------------- */
                var parent = $(this);

                animateReactionsOn = setTimeout(function () {
                    parent.addClass('react-show');
                }, 300);

                clearTimeout(animateReactionsOut);
            });

            $('#photo_reaction_'+photo_id).mouseleave(function () {
                var parent = $(this);

                animateReactionsOut = setTimeout(function () {
                    parent.removeClass("react-show");
                    /* -------------- */
                    var eleReacts = parent.find('.reacts');
                    var eleCircle = eleReacts.find('.react-circle');
                    eleReacts.css({
                        'left': ''
                    });
                    eleCircle.css({
                        width: '',
                        height: ''
                    });
                    /* -------------- */
                }, 500);

                clearTimeout(animateReactionsOn);
            });

            $('#photo_reaction_'+photo_id).find('.react-btn').click(function (e) {
                e.preventDefault();
                //var data = $(this).data();
                //console.log('truoc khi bam: ',$(this).data());
                clearTimeout(animateReactionsOut);
                clearTimeout(animateReactionsOn);

                likePhotoComment(photo_id,
                    $(this).attr('data-type'),
                    $(this).attr('data-id'),
                    $(this).attr('data-reaction')
                );
            });
        }

        $('#photo_reaction_'+photo_id).find('.react-circle').click(function (e) {
            e.preventDefault();
            //var data = $(this).data();
            $(this).parent().parent().removeClass("react-show").removeClass("reaction-mobile").find('.react-overview').hide();
            clearTimeout(animateReactionsOut);
            clearTimeout(animateReactionsOn);

            likePhotoComment(photo_id,
                $(this).data('type'),
                $(this).data('id'),
                $(this).data('reaction')
            );
        });
    };

    // console.log('mooConfig.url.webroot', mooConfig.url.webroot);
    // console.log('mooConfig.url.base', mooConfig.url.base);
    // console.log('mooConfig.url.full', mooConfig.url.full);
    return{
        initActivityReaction: function (activity_id) {
            initActivityReaction(activity_id);
        },
        initCommentReaction: function (comment_id) {
            initCommentReaction(comment_id);
        },
        initItemReaction: function (item_id) {
            initItemReaction(item_id);
        },
        initPhotoReaction: function (item_id) {
            initPhotoReaction(item_id);
        }
    }
}));