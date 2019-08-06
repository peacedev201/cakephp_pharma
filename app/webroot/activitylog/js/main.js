(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery', 'mooBehavior', 'mooPhrase', 'mooAjax', 'mooGlobal', 'mooTooltip'], factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS-like
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals (root is window)
        root.mooActivitylog = factory();
    }
}(this, function ($, mooBehavior, mooPhrase, mooAjax, mooGlobal, mooTooltip) {

    // app/Plugin/Activitylog/View/Elements/lists/activities_list.ctp
    var initOnListing = function(){
        mooBehavior.initMoreResults();
        //initDeleteItem(); //remove this feature
    }

    // var initDeleteItem = function()
    // {
    //     $('.deleteItem').unbind('click');
    //     $('.deleteItem').click(function()
    //     {
    //         var data = $(this).data();
    //         var action = data.action;
    //         if(action.substring(0,4) == 'like'){
    //             $.get(mooConfig.url.base + '/likes/ajax_add/'+ data.type + '/' + data.target + '/1', function () {
    //                 $('#log_item_' + data.id).fadeOut();
    //             });
    //         }else if(action.substring(0,7) == 'dislike'){
    //             $.get(mooConfig.url.base + '/likes/ajax_add/'+ data.type + '/' + data.target + '/0', function () {
    //                 $('#log_item_' + data.id).fadeOut();
    //             });
    //         }else if(action == 'tagged'){
    //             $.post(mooConfig.url.base + '/activities/ajax_remove_tags', {item_id: data.target, item_type: 'activity'}, function () {
    //                 $('#log_item_' + data.id).fadeOut();
    //                 //delete this activyty log
    //                 $.get(mooConfig.url.base + '/activity_log/delete/'+ data.id, function () {
    //                 });
    //             });
    //         }else{
    //             $.fn.SimpleModal({
    //                 btn_ok : mooPhrase.__('btn_ok'),
    //                 btn_cancel: mooPhrase.__('btn_cancel'),
    //                 callback: function () {
    //                     removeItem(data.id, data.target, data.type);
    //                 },
    //                 title: mooPhrase.__('please_confirm'),
    //                 contents: mooPhrase.__('are_you_sure_you_want_to_delete_this'),
    //                 model: 'confirm',
    //                 hideFooter: false,
    //                 closeButton: false
    //             }).showModal();
    //         }
    //     });
    // }

    // var removeItem = function (id, target, type) {
    //     var url = '';
    //     switch (type){
    //         case 'activity':
    //             url = '/activities/ajax_remove';
    //             break;
    //         case 'core_activity_comment':
    //             url = '/activities/ajax_removeComment';
    //             break;
    //         case 'comment':
    //             url = '/comments/ajax_remove';
    //             break;
    //         case 'Blog_Blog':
    //             url = '/blogs/delete/'+target;
    //             break;
    //         case 'Group_Group':
    //             url = '/groups/do_delete/'+target;
    //             break;
    //         case 'Photo_Album':
    //             url = '/albums/do_delete/'+target;
    //             break;
    //         case 'Event_Event':
    //             url = '/events/do_delete/'+target;
    //             break;
    //         case 'Video_Video':
    //             url = '/videos/delete/'+target;
    //             break;
    //         case 'Topic_Topic':
    //             url = '/topics/do_delete/'+target;
    //             break;
    //     }
    //     if(url){
    //         $.post(mooConfig.url.base + url, {id: target}, function () {
    //             $('#log_item_' + id).fadeOut();
    //             //delete this activyty log
    //             $.get(mooConfig.url.base + '/activity_log/delete/'+ id, function () {
    //             });
    //         });
    //     }else{
    //         $.fn.SimpleModal({
    //             btn_ok : mooPhrase.__('btn_ok'),
    //             btn_cancel: mooPhrase.__('btn_cancel'),
    //             model: 'modal',
    //             title: mooPhrase.__('warning'),
    //             contents: mooPhrase.__('can_not_delete_this_item')
    //         }).showModal();
    //     }
    // }

    var initOnView = function(){
       $('#filter_log').on('change', function () {
           $('#list-content').html(mooPhrase.__('loading'));
           var value = $(this).val();

           mooAjax.get({
               url : mooConfig.url.base + '/activity_log/index/'+value,
           }, function(data){
               $('#list-content').html(data);
               mooTooltip.init();
               $(".tip").tipsy({ html: true, gravity: 's' });
           });
       })
    }

    return{
        initOnListing : initOnListing,
        initOnView : initOnView,
    }
}));
