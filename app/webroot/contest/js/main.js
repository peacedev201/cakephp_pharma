(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery', 'mooContestCountdown', 'mooMasonry', 'mooBridget', 'mooFileUploader', 'mooBehavior', 'mooAlert', 'mooPhrase', 'mooAjax', 'mooUser', 'mooButton', 'mooOverlay', 'mooGlobal', 'picker', 'picker_date', 'picker_time', 'tinyMCE', 'mooImagesloaded', 'mooContestMusicPlayer'], factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS-like
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals (root is window)
        root.mooContest = factory();
    }
}(this, function ($, mooContestCountdown, mooMasonry, mooBridget, mooFileUploader, mooBehavior, mooAlert, mooPhrase, mooAjax, mooUser, mooButton, mooOverlay, mooGlobal, picker, picker_date, picker_time, tinyMCE, mooImagesloaded, mooContestMusicPlayer) {
    var grid;
    var grid_entry;
    var initCreateContest = function (settings) {
        initButton('#draft_button', settings, false);
        initButton('#publish_button', settings, true);
        if (mooPhrase.__('drag_photo') != '')
            text_upload_button = '<div class="upload-section"><i class="material-icons">photo_camera</i>' + mooPhrase.__('drag_photo') + '</div>';
        else
            text_upload_button = '<div class="upload-section"></div>';

        var uploader = new mooFileUploader.fineUploader({
            element: $('#contest_thumnail')[0],
            multiple: false,
            text: {
                uploadButton: '<div class="upload-section"><i class="material-icons">photo_camera</i>' + mooPhrase.__('drag_or_click_here_to_upload_photo') + '</div>'
            },
            validation: {
                allowedExtensions: ['jpg', 'jpeg', 'gif', 'png'],
            },
            request: {
                endpoint: mooConfig.url.base + "/contests/upload_avatar"
            },
            callbacks: {
                onError: mooGlobal.errorHandler,
                onComplete: function (id, fileName, response) {
                    $('#contest_thumnail_preview > img').attr('src', response.thumb);
                    $('#contest_thumnail_preview > img').show();
                    $('#thumbnail').val(response.file);
                }
            }
        });

        initTinyMCE('description');
        initTinyMCE('award');
        initTinyMCE('term_and_condition');
        initDateTime(settings.is_edit);
        initDeleteContest();
    };
    var initButton = function (btn_id, settings, is_note) {
        $(btn_id).unbind('click');
        $('body').on('click', btn_id, function (e) {
            if (is_note == true) {
                $('#contest_status').val('published');
                button = $(btn_id);
                if (tinyMCE.activeEditor !== null) {
                    for (i = 0; i < tinyMCE.editors.length; i++) {
                        $('#' + tinyMCE.editors[i].id).val(tinyMCE.editors[i].getContent());
                    }
                }
                mooAjax.post({
                    url: mooConfig.url.base + '/contests/save',
                    data: jQuery("#createForm").serialize()
                }, function (data) {
                    var json = $.parseJSON(data);
                    if (json.result == 1) {
                        if (!mooConfig.isApp)
                        {
                            if (settings.url != '') {
                                window.location = settings.url;
                            } else {
                                window.location = json.href ;
                            }
                        }
                        else
                        {
                            window.location = json.href + '?app_no_tab=1';
                        }
                        
                    } else if (json.result == 'publish_confirm') {
                        if (mooConfig.isApp){
                            $('#publish_confirm').val(1);
                            initSubmitForm(btn_id, settings); 
                        }else{
                            // Set title
                            $($('#portlet-config  .modal-header .modal-title')[0]).html(mooPhrase.__('please_confirm'));
                            // Set content
                            $($('#portlet-config  .modal-body')[0]).html(mooPhrase.__('note_published'));
                            // OK callback
                            $('#portlet-config  .modal-footer .ok').click(function () {
                                $('#publish_confirm').val(1);
                                initSubmitForm(btn_id, settings);
                            });
                            $('#portlet-config').modal('show');
                        }

                    } else {
                        $(".error-message").show();
                        $(".error-message").html(json.message);
                    }
                });
            } else {
                $('#contest_status').val('draft');
                initSubmitForm(btn_id, settings);
            }
            return false;
        });
    };
    var initSubmitForm = function (btn_id, settings) {
        button = $(btn_id);
        button.addClass('disabled');
        if (tinyMCE.activeEditor !== null) {
            for (i = 0; i < tinyMCE.editors.length; i++) {
                $('#' + tinyMCE.editors[i].id).val(tinyMCE.editors[i].getContent());
            }
        }
        mooAjax.post({
            url: mooConfig.url.base + '/contests/save',
            data: jQuery("#createForm").serialize()
        }, function (data) {
            var json = $.parseJSON(data);
            if (json.result == 1) {
                if (!mooConfig.isApp)
                {
                    if (settings.url != '') {
                        window.location = settings.url;
                    } else {
                        window.location = mooConfig.url.base + '/contests/view/' + json.id;
                    }
                }
                else
                {
                    if (settings.url != '') {
                        window.location = settings.url + '?app_no_tab=1';
                    } else {
                        window.location = mooConfig.url.base + '/contests/view/' + json.id +'?app_no_tab=1';
                    }
                }
                
            } else {
                button.removeClass('disabled');
                $(".error-message").show();
                $(".error-message").html(json.message);
            }
        });
    };
    var initTinyMCE = function (id) {
        var tiny_plugins = "advlist autolink lists link image charmap print preview hr anchor pagebreak searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking save table  directionality emoticons template paste textcolor";
        if (mooConfig.isMobile) {
            tiny_plugins = "textcolor emoticons fullscreen";
        }
        tinyMCE.remove("textarea#" + id);
        tinyMCE.init({
            selector: "textarea#" + id,
            language: mooConfig.tinyMCE_language,
            theme: "modern",
            skin: 'light',
            plugins: [tiny_plugins],
            toolbar1: "styleselect | bold italic | bullist numlist outdent indent | forecolor backcolor emoticons | link unlink anchor image media | preview fullscreen code",
            image_advtab: true,
            directionality: "ltr",
            width: 580,
            height: 150,
            menubar: false,
            forced_root_block: 'div',
            relative_urls: false,
            remove_script_host: true,
            document_base_url: mooConfig.url.base,
            browser_spellcheck: true,
            contextmenu: false
        });
    };
    var initDateTime = function (is_edit) {
        $(".datepicker").pickadate({
            monthsFull: [mooPhrase.__('january'), mooPhrase.__('february'), mooPhrase.__('march'), mooPhrase.__('april'), mooPhrase.__('may'), mooPhrase.__('june'), mooPhrase.__('july'), mooPhrase.__('august'), mooPhrase.__('september'), mooPhrase.__('october'), mooPhrase.__('november'), mooPhrase.__('december')],
            monthsShort: [mooPhrase.__('jan'), mooPhrase.__('feb'), mooPhrase.__('mar'), mooPhrase.__('apr'), mooPhrase.__('may'), mooPhrase.__('jun'), mooPhrase.__('jul'), mooPhrase.__('aug'), mooPhrase.__('sep'), mooPhrase.__('oct'), mooPhrase.__('nov'), mooPhrase.__('dec')],
            weekdaysFull: [mooPhrase.__('sunday'), mooPhrase.__('monday'), mooPhrase.__('tuesday'), mooPhrase.__('wednesday'), mooPhrase.__('thursday'), mooPhrase.__('friday'), mooPhrase.__('saturday')],
            weekdaysShort: [mooPhrase.__('sun'), mooPhrase.__('mon'), mooPhrase.__('tue'), mooPhrase.__('wed'), mooPhrase.__('thu'), mooPhrase.__('fri'), mooPhrase.__('sat')],
            today: mooPhrase.__('today'),
            clear: mooPhrase.__('clear'),
            close: mooPhrase.__('close'),
            format: 'yyyy-mm-dd',
            onClose: function () {
                var error_message = checkTimeCondition();
                if (error_message != '') {
                    console.log(error_message);
                }
            }
        });

        $(".timepicker").pickatime({
            clear: mooPhrase.__('clear'),
            format: 'h:i A',
            onClose: function (time) {
                var error_message = checkTimeCondition();
                if (error_message != '') {
                    console.log(error_message);
                }
            }
        });
    };
    var checkTimeCondition = function () {
        var f_time = $('#from').val() + ' ' + $('#from_time').val();
        f_time = f_time.replace("-", "/").replace("-", "/");
        var cf_time = new Date(f_time).getTime();
        var t_time = $('#to').val() + ' ' + $('#to_time').val();
        t_time = t_time.replace("-", "/").replace("-", "/");
        var ct_time = new Date(t_time).getTime();
        var s_f_time = $('#s_from').val() + ' ' + $('#s_from_time').val();
        s_f_time = s_f_time.replace("-", "/").replace("-", "/");
        var cs_f_time = new Date(s_f_time).getTime();
        var s_t_time = $('#s_to').val() + ' ' + $('#s_to_time').val();
        s_t_time = s_t_time.replace("-", "/").replace("-", "/");
        var cs_t_time = new Date(s_t_time).getTime();
        var v_f_time = $('#v_from').val() + ' ' + $('#v_from_time').val();
        v_f_time = v_f_time.replace("-", "/").replace("-", "/");
        var cv_f_time = new Date(v_f_time).getTime();
        var v_t_time = $('#v_to').val() + ' ' + $('#v_to_time').val();
        v_t_time = v_t_time.replace("-", "/").replace("-", "/");
        var cv_t_time = new Date(v_t_time).getTime();

        var current_time = new Date().getTime();
        var error_message = '';
        //check  from to
        if (current_time > cf_time) {
            return mooPhrase.__('contest_duration_start');
        }
        if (current_time > ct_time) {
            return mooPhrase.__('contest_duration_end');
        }
        if (cf_time > ct_time) {
            return mooPhrase.__('contest_duration_start_end');
        }
        // check submit from to 
        if (current_time > cs_f_time) {
            return mooPhrase.__('submit_duration_start');
        }
        if (current_time > cs_t_time) {
            return mooPhrase.__('submit_duration_end');
        }
        if (cs_f_time > cs_t_time) {
            return mooPhrase.__('submit_duration_start_end');
        }
        if (cf_time > cs_f_time) {
            return mooPhrase.__('start_submit_greater_duration');
        }
        if (cs_t_time > ct_time) {
            return mooPhrase.__('end_submit_greater_duration');
        }
        // check vote from to
        if (current_time > cv_f_time) {
            return mooPhrase.__('vote_duration_start');
        }
        if (current_time > cv_t_time) {
            return mooPhrase.__('vote_duration_end');
        }
        if (cv_f_time > cv_t_time) {
            return mooPhrase.__('vote_duration_start_end');
        }
        if (cs_f_time > cv_f_time) {
            return mooPhrase.__('start_vote_greater_submit');
        }
        if (cs_t_time > cv_t_time) {
            return mooPhrase.__('end_vote_greater_submit');
        }
        return error_message;
    };
    var initDeleteContest = function ()
    {
        $('.deleteContest').unbind('click');
        $('body').on('click', '.deleteContest', function (e) {
            var data = $(this).data();
            if (!mooConfig.isApp)
            {
                var deleteUrl = mooConfig.url.base + '/contests/delete/' + data.id;
            }
            else
            {
                var deleteUrl = mooConfig.url.base + '/contests/delete/' + data.id + '?app_no_tab=1';
            }
            mooAlert.confirm(mooPhrase.__('delete_contest_confirm'), deleteUrl);
        });
        $('.rdeleteContest').unbind('click');
        $('body').on('click', '.rdeleteContest', function (e) {
            var data = $(this).data();
            if (!mooConfig.isApp)
            {
                var deleteUrl = mooConfig.url.base + '/contests/request_delete/' + data.id;
            }
            else
            {
                var deleteUrl = mooConfig.url.base + '/contests/request_delete/' + data.id + '?app_no_tab=1';
            }
            mooAlert.confirm(mooPhrase.__('request_delete_contest_confirm'), deleteUrl);
        });
    };
    var initViewContest = function ()
    {
        initDeleteContest();
        initJoinLeaveContest();
        // init tab load ajax
        var tab = $('.tab').attr('data-id');
        if ($("#" + tab).length > 0)
        {
            $('#' + tab).spin('tiny');
            $('.contest_list_action .current').removeClass('current');
            $('#' + tab).parent().addClass('current');
            $('#contest_content').load($('#' + tab).attr('data-url'), {noCache: 1}, function (response) {
                $('#' + tab).spin(false);
                // reattach events
                $('textarea').autogrow();
                $(".tip").tipsy({
                    html: true,
                    gravity: 's'
                });
                mooOverlay.registerOverlay();
            });
        }
        initWarningSubmitEntry();
        if (mooConfig.isApp){
            initPublishContent();
        }
    };
    var initPublishContent = function ()
    {
        $('#publish-content-onapp').unbind('click');
        $('body').on('click', '#publish-content-onapp', function (e) {
            e.preventDefault();
            button = $(this);
            button.addClass('disabled');
            mooAjax.post({
                url: button.data("href"),
                data: {}
            }, function (data) {
                var json = $.parseJSON(data);
                if (json.result == 1) {
                    window.location.reload();
                } else {
                    button.removeClass('disabled');
                    $(".error-message").show();
                    $(".error-message").html(json.message);
                }
            });
            return false;
        });
    };
    var initDeleteEntry = function ()
    {
        $('.deleteEntry').unbind('click');
        $('body').on('click', '.deleteEntry', function (e) {
            var data = $(this).data();
            if (!mooConfig.isApp)
            {
                var deleteUrl = mooConfig.url.base + '/contests/delete_entry/' + data.id;
            }
            else
            {
                var deleteUrl = mooConfig.url.base + '/contests/delete_entry/' + data.id + '?app_no_tab=1';
            }
            mooAlert.confirm(mooPhrase.__('delete_entry_confirm'), deleteUrl);
        });
    };
    var initDenyEntry = function ()
    {
        $('.denyEntry').unbind('click');
        $('body').on('click', '.denyEntry', function (e) {
            var data = $(this).data();
            if (!mooConfig.isApp)
            {
                var denyUrl = mooConfig.url.base + '/contests/deny_entry/' + data.id;
            }
            else
            {
                var denyUrl = mooConfig.url.base + '/contests/deny_entry/' + data.id + '?app_no_tab=1';
            }
            mooAlert.confirm(mooPhrase.__('deny_entry_confirm'), denyUrl);
        });
    };
    var initApproveEntry = function ()
    {
        $('.approveEntry').unbind('click');
        $('body').on('click', '.approveEntry', function (e) {
            var data = $(this).data();
            if (!mooConfig.isApp)
            {
                var approveUrl = mooConfig.url.base + '/contests/approve_entry/' + data.id;
            }
            else
            {
                var approveUrl = mooConfig.url.base + '/contests/approve_entry/' + data.id + '?app_no_tab=1';
            }
            mooAlert.confirm(mooPhrase.__('approve_entry_confirm'), approveUrl);
        });
    };
    var initWinEntry = function ()
    {
        $('.winEntry').unbind('click');
        $('body').on('click', '.winEntry', function (e) {
            var data = $(this).data();
            if (!mooConfig.isApp)
            {
                var winUrl = mooConfig.url.base + '/contests/win_entry/' + data.id;
            }
            else
            {
                var winUrl = mooConfig.url.base + '/contests/win_entry/' + data.id + '?app_no_tab=1';
            }
            mooAlert.confirm(mooPhrase.__('win_entry_confirm'), winUrl);
        });
    };

    var initViewEntry = function ()
    {
        initApproveEntry();
        initDenyEntry();
        initWinEntry();
        initDeleteEntry();
        initJoinLeaveContest();
        initWarningSubmitEntry();
        initVoteEntryDetail();
        initVote();
    };

    var initWarningSubmitEntry = function () {

    };
    var initJoinLeaveContest = function () {
        $('.jl_contest').unbind("click");
        $('.jl_contest').click(function () {
            button = $(this);
            button.addClass('disabled');
            mooAjax.post({
                url: button.data("href"),
                data: {}
            }, function (data) {
                var json = $.parseJSON(data);
                if (json.result == 1) {
                    window.location.reload();
                } else {
                    button.removeClass('disabled');
                    $(".error-message").show();
                    $(".error-message").html(json.message);
                }
            });
            return false;
        });
    };
    var initOnAward = function () {
        initTinyMCE('c_award');
        $('.btn-cancel').click(function () {
            $('#createForm').hide();
            $('#contest_award').show();
        });
        $('#edit_award').click(function () {
            $('#createForm').show();
            $('#contest_award').hide();
            tinyMCE.activeEditor.focus();
            $('textarea').autogrow();
        });
        $('#updateBtn').click(function () {
            button = $(this);
            button.addClass('disabled');
            if (tinyMCE.activeEditor !== null) {
                $('#c_award').val(tinyMCE.activeEditor.getContent());
            }
            mooAjax.post({
                url: mooConfig.url.base + '/contests/save_info',
                data: jQuery("#createForm").serialize()
            }, function (data) {
                var json = $.parseJSON(data);
                if (json.result == 1) {
                    window.location.reload();
                } else {
                    button.removeClass('disabled');
                    $(".error-message").show();
                    $(".error-message").html(json.message);
                }
            });
            return false;
        });
    };
    var initOnPolicy = function () {
        initTinyMCE('term_and_condition');
        $('.btn-cancel').click(function () {
            $('#createForm').hide();
            $('#contest_policy').show();
        });
        $('#edit_policy').click(function () {
            $('#createForm').show();
            $('#contest_policy').hide();
            tinyMCE.activeEditor.focus();
            $('textarea').autogrow();
        });
        $('#updateBtn').click(function () {
            button = $(this);
            button.addClass('disabled');
            if (tinyMCE.activeEditor !== null) {
                $('#term_and_condition').val(tinyMCE.activeEditor.getContent());
            }
            mooAjax.post({
                url: mooConfig.url.base + '/contests/save_info',
                data: jQuery("#createForm").serialize()
            }, function (data) {
                var json = $.parseJSON(data);
                if (json.result == 1) {
                    window.location.reload();
                } else {
                    button.removeClass('disabled');
                    $(".error-message").show();
                    $(".error-message").html(json.message);
                }
            });
            return false;
        });
    };
    var initListEntries = function () {
        
        if ($('#list-content li').length > 0) {
            $('#manage_entries').show();
        } else {
            $('#manage_entries').hide();
        }
        $("#entry_action option[value='approve']").wrap('<span>');
        $('.contest_nav a').click(function () {
            // console.log('xxxx');
           // return;

            // $(this).spin('tiny');
            $('.contest_nav .active').removeClass('active');
            $(this).addClass('active');
            var c_type = $(this).attr('data-ctype');
            $('#list-content').load($(this).attr('data-url'), {noCache: 1}, function (response) {
                if (c_type == 'approved') {
                    $("#entry_action option[value='approve']").wrap('<span>');
                }
                if (c_type == 'pending') {
                    if ($('#list-content li').length > 0) {
                        if ($("#entry_action option[value='approve']").parent().is("span")) {
                            $("#entry_action option[value='approve']").unwrap();
                        }
                        $("#entry_action option[value='win']").wrap('<span>');
                    }

                }
                if ($('#list-content li').length > 0) {
                    $('#manage_entries').show();
                } else {
                    $('#manage_entries').hide();
                }
                //    $('.contest_nav a').spin(false);
                mooOverlay.registerOverlay();
            });
            $('#entry_id_list').val('');
            $('#acion').val('');
          //  alert('xx111x');
        });
        // select checkbox
        $('.entry_edit_checkbox').unbind('click');
        $('body').on('click', '.entry_edit_checkbox', function (e) {
            if ($(this).is(':checked')) {
                var entry_id_list = $('#entry_id_list').val();
                if (entry_id_list != '') {
                    $('#entry_id_list').val(entry_id_list + ',' + $(this).data('id'));
                } else {
                    $('#entry_id_list').val($(this).data('id'));
                }
            } else {
                var entry_id_list = $('#entry_id_list').val();
                var a_ids = entry_id_list.split(',');
                for (x in a_ids)
                {
                    if (a_ids[x] == $(this).data('id')) {
                        a_ids.splice(x, 1);
                    }
                }
                var new_ids = a_ids.join(',');
                $('#entry_id_list').val(new_ids);
            }
        });
        // form submit
        $('#entry_action').unbind('click');
        $('body').on('change', '#entry_action', function (e) {
            if ($('#entry_action').val() != '') {
                var ids = $('#entry_id_list').val();
                if ($('#entry_id_list').val() != '') {
                    /*
                    if (!mooConfig.isApp){
                        if ($('#entry_action').val() == 'delete') {
                            var r = confirm(mooPhrase.__('delete_entries_confirm'));
                        }
                        if ($('#entry_action').val() == 'approve') {
                            var r = confirm(mooPhrase.__('approve_entries_confirm'));
                        }
                        if ($('#entry_action').val() == 'win') {
                            var r = confirm(mooPhrase.__('win_entries_confirm'));
                        }
                        if (r == true) {
                            $("#manage_entries").submit()
                        } else {
                            $('#entry_action').prop('selectedIndex', 0);
                        }
                    }else{ 
                        alert("xxx");
                    */
                        mooAjax.post({
                            url: mooConfig.url.base + '/contests/manage_entries_app',
                            data: jQuery("#manage_entries").serialize()
                        }, function (data) {
                            var json = $.parseJSON(data);
                            if (json.result == 1) {
                                window.location = json.url + '?app_no_tab=1';
                            } else {
                                $(".error-message").show();
                                $(".error-message").html(json.message);
                            }
                        });
                    /*
                    }
                    */
                } else {
                    $('#entry_action').prop('selectedIndex', 0);
                    mooAlert.alert(mooPhrase.__('select_entry'));
                }
            }
        });
    };
    var initOnEntries = function () {
        mooBehavior.initMoreResults();
        if (!mooConfig.isApp) {
            initEntryMasonry();
        }
        initVoteEntry();
    };
    var initVote = function () {
        $('.voteBtn').unbind("click");
        $('.voteBtn').click(function () {
            button = $(this);
            button.addClass('disabled');
            mooAjax.post({
                url: button.data("href"),
                data: {}
            }, function (data) {
                var json = $.parseJSON(data);
                if (json.result == 1) {
                    window.location.reload();
                } else {
                    button.removeClass('disabled');
                    $(".error-message").show();
                    $(".error-message").html(json.message);
                }
            });
            return false;
        });
    };
    var initVoteEntryDetail = function() {
        $('.contest_vote').unbind('click');
        $('.contest_vote').click(function () {
            var button = $(this).addClass('disabled');
            button.spin('tiny');
            mooAjax.post({
                url: button.attr('data-url'),
                data: ''
            }, function (data) {
                var json = $.parseJSON(data);
                button.spin(false);
                button.removeClass('disabled');
                $('#contest_vote_' + button.attr('data-id')).hide();
                if (json.result == 1) {
                    $('#contest_unvote_' + button.attr('data-id')).show();
                    var vote_count = parseInt($('#vote_entry_count_' + button.attr('data-id')).html());
                    if (vote_count >= 0) {
                        $('#vote_entry_count_' + button.attr('data-id')).html(vote_count + 1);
                    }
                } else {
                    $('#contest_vote_' + button.attr('data-id')).show();
                    mooAlert.alert(mooPhrase.__('can_not_vote'));
                }
            });
            return false;
        });
        $('.contest_unvote').unbind('click');
        $('.contest_unvote').click(function () {
            var button = $(this).addClass('disabled');
            button.spin('tiny');
            mooAjax.post({
                url: button.attr('data-url'),
                data: ''
            }, function (data) {
                var json = $.parseJSON(data);
                button.spin(false);
                button.removeClass('disabled');
                $('#contest_unvote_' + button.attr('data-id')).hide();
                if (json.result == 1) {
                    $('#contest_vote_' + button.attr('data-id')).show();
                    var vote_count = parseInt($('#vote_entry_count_' + button.attr('data-id')).html());
                    if (vote_count > 0) {
                        $('#vote_entry_count_' + button.attr('data-id')).html(vote_count - 1);
                    }
                } else {
                    $('#contest_unvote_' + button.attr('data-id')).show();
                    mooAlert.alert(mooPhrase.__('can_not_vote'));
                }
            });
            return false;
        });
    };
    var initVoteEntry = function() {
        $('.contest_vote').unbind('click');
        $('.contest_vote').click(function () {
            var button = $(this).addClass('disabled');
            button.spin('tiny');
            mooAjax.post({
                url: button.attr('data-url'),
                data: ''
            }, function (data) {
                var json = $.parseJSON(data);
                button.spin(false);
                button.removeClass('disabled');
                $('#contest_vote_' + button.attr('data-id')).hide();
                if (json.result == 1) {
                    $('#contest_unvote_' + button.attr('data-id')).show();
                    var vote_count = parseInt($('#vote_count_' + button.attr('data-id')).html());
                    if (vote_count >= 0) {
                        $('#vote_count_' + button.attr('data-id')).html(vote_count + 1);
                    }
                } else {
                    $('#contest_vote_' + button.attr('data-id')).show();
                    mooAlert.alert(mooPhrase.__('can_not_vote'));
                }
            });
            return false;
        });
        $('.contest_unvote').unbind('click');
        $('.contest_unvote').click(function () {
            var button = $(this).addClass('disabled');
            button.spin('tiny');
            mooAjax.post({
                url: button.attr('data-url'),
                data: ''
            }, function (data) {
                var json = $.parseJSON(data);
                button.spin(false);
                button.removeClass('disabled');
                $('#contest_unvote_' + button.attr('data-id')).hide();
                if (json.result == 1) {
                    $('#contest_vote_' + button.attr('data-id')).show();
                    var vote_count = parseInt($('#vote_count_' + button.attr('data-id')).html());
                    if (vote_count > 0) {
                        $('#vote_count_' + button.attr('data-id')).html(vote_count - 1);
                    }
                } else {
                    $('#contest_unvote_' + button.attr('data-id')).show();
                    mooAlert.alert(mooPhrase.__('can_not_vote'));
                }
            });
            return false;
        });
    };
    var initOnListing = function () {
        mooBehavior.initMoreResults();
        initContestMasonry();
    };
    var initOnViewUserVote = function () {
        mooBehavior.initMoreResults();
    };
    var initContestMasonry = function () {
        mooBridget('masonry', mooMasonry);
        if ($('.contest-item').closest("ul").length > 0) {
            // contest list
            grid = $('.contest-item').closest("ul").imagesLoaded(function () {
                grid.masonry({
                    // options
                    itemSelector: '.contest-item',
                    columnWidth: '.grid-sizer',
                    isAnimated: !Modernizr.csstransitions
                });
            });
            $('.contest-item').closest("ul").masonry({
                // options
                itemSelector: '.contest-item',
                columnWidth: '.grid-sizer',
                isAnimated: !Modernizr.csstransitions
            }).masonry('reloadItems');
        }
        if ($('.contest-content-list li').length > 0) {
            // contest list
            grid = $('.contest-content-list').imagesLoaded(function () {
                grid.masonry({
                    // options
                    itemSelector: '.contest-item',
                    columnWidth: '.grid-sizer',
                    isAnimated: !Modernizr.csstransitions
                });
            });
        }
        $('.contest-content-list').masonry({
            // options
            itemSelector: '.contest-item',
            columnWidth: '.grid-sizer',
            isAnimated: !Modernizr.csstransitions
        }).masonry('reloadItems');


    };
    var initEntryMasonry = function () {
        mooBridget('masonry', mooMasonry);
        //entries list
        if ($('.entry-content-list li').length > 0) {
            grid_entry = $('.entry-content-list').imagesLoaded(function () {
                grid_entry.masonry({
                    // options
                    itemSelector: '.entry-item',
                    columnWidth: '.grid-sizer-entry',
                    isAnimated: !Modernizr.csstransitions
                });
            });
        }
        $('.entry-content-list').masonry({
            // options
            itemSelector: '.entry-item',
            columnWidth: '.grid-sizer-entry',
            isAnimated: !Modernizr.csstransitions
        }).masonry('reloadItems');
        // }
    };
    var initCandidateList = function () {
        mooBehavior.initMoreResults();
    };
    var initSubmitEntry = function () {
        var uploader = new mooFileUploader.fineUploader({
            element: $('#entry_upload')[0],
            multiple: false,
            text: {
                uploadButton: '<div class="upload-section"><i class="material-icons">photo_camera</i>' + mooPhrase.__('drag_or_click_here_to_upload_photo') + '</div>'
            },
            validation: {
                allowedExtensions: ['jpg', 'jpeg', 'gif', 'png'],
            },
            request: {
                endpoint: mooConfig.url.base + "/contests/upload_avatar"
            },
            callbacks: {
                onError: mooGlobal.errorHandler,
                onComplete: function (id, fileName, response) {
                    $('#entry_preview > img').attr('src', response.thumb);
                    $('#entry_preview > img').show();
                    $('#thumbnail').val(response.file);
                    $('#caption').show();
                    $('#nextStep').show();
                }
            }
        });
        $('#nextStep').unbind('click');
        $('#nextStep').click(function () {
            $('#loadingSpin').spin('tiny');
            if (!mooConfig.isApp)
            {
                $('#uploadEntryForm').submit();
            }else{
                initSubmitOnApp('#uploadEntryForm');
            }
            $(this).addClass('disabled');
        });
    };
    var initSubmitOnApp = function(id) {
        var form_url = $(id).data("action");
        mooAjax.post({
            url: form_url,
            data: $(id).serialize()
        }, function (data) {
            var json = $.parseJSON(data);
            if (json.result == 1) {
                if (json.url != '') {
                    window.location = mooConfig.url.base + json.url + '?app_no_tab=1';
                } else {
                    window.location = mooConfig.url.base + '/contests/entry/' + json.id +'?app_no_tab=1';
                }
            } else {
                button.removeClass('disabled');
                $(".error-message").show();
                $(".error-message").html(json.message);
            }
        });
        return false;
    };
    var initSubmitMusic = function () {
        var uploader = new mooFileUploader.fineUploader({
            element: $('#entry_upload')[0],
            multiple: false,
            text: {
                uploadButton: '<div class="upload-section"><i class="material-icons">photo_camera</i>' + mooPhrase.__('drag_or_click_here_to_upload_photo') + '</div>'
            },
            validation: {
                allowedExtensions: ['jpg', 'jpeg', 'gif', 'png'],
            },
            request: {
                endpoint: mooConfig.url.base + "/contests/upload_avatar"
            },
            callbacks: {
                onError: mooGlobal.errorHandler,
                onComplete: function (id, fileName, response) {
                    $('#entry_preview > img').attr('src', response.thumb);
                    $('#entry_preview > img').show();
                    $('#thumbnail').val(response.file);
                }
            }
        });
        var uploader1 = new mooFileUploader.fineUploader({
            element: $('#entry_source_upload')[0],
            multiple: false,
            text: {
                uploadButton: '<div class="upload-section"><i class="material-icons">audiotrack</i>' + mooPhrase.__('drag_or_click_here_to_upload_music') + '</div>'
            },
            validation: {
                allowedExtensions: ['mp3', 'ogg', 'webm', 'wav'],
            },
            request: {
                endpoint: mooConfig.url.base + "/contests/upload_file"
            },
            callbacks: {
                onError: mooGlobal.errorHandler,
                onComplete: function (id, fileName, response) {
                    $('#source_id').val(response.file);
                    $('#caption').show();
                    $('#nextStep').show();
                }
            }
        });
        $('#nextStep').unbind('click');
        $('#nextStep').click(function () {
            $('#loadingSpin').spin('tiny');
            if (!mooConfig.isApp)
            {
                $('#uploadEntryForm').submit();
            }else{
                initSubmitOnApp('#uploadEntryForm');
            }
            $(this).addClass('disabled');
        });
    };
    initOnEditEntry = function () {
        $('.entry_edit_checkbox').unbind('click');
        $('body').on('click', '.entry_edit_checkbox', function (e) {
            if ($(this).is(':checked')) {
                var select_count = parseInt($('#select_count').val()) + 1;
                $('#select_count').val(select_count);
            } else {
                var select_count = parseInt($('#select_count').val()) - 1;
                $('#select_count').val(select_count);
            }
        });
    };
    var initEditEntry = function () {
        $("#edit-entry-button").unbind("click");
        $("#edit-entry-button").click(function(e){
            e.preventDefault();
            var button = $(this);
            button.addClass('disabled');
            mooAjax.post({
                url: mooConfig.url.base + '/contests/edit_entry_save/',
                data: jQuery("#edit_entries").serialize()
            }, function (data) {
                var json = $.parseJSON(data);
                if (json.result == 1) {
                    if (json.url != '') {
                        window.location = json.url + '?app_no_tab=1';
                    } else {
                        window.location = mooConfig.url.base + '/contests/entry/' + json.id +'?app_no_tab=1';
                    }
                } else {
                    button.removeClass('disabled');
                    $(".error-message").show();
                    $(".error-message").html(json.message);
                }
            });
            return false;
        });
    };
    var initSelectItem = function () {
        $('.entry_select').click(function () {
            $('#thumbnail').val($(this).data('file'));
            $('#thumbnail_name').val($(this).data('fname'));
            $('#item_id').val($(this).data('id'));
            $('.select_entry_li').removeClass('active');
            $(this).parents('.select_entry_li').addClass('active');
            $('#caption').val($(this).find('.photo_caption').val());
        });
        $('#select_entry').submit(function (event) {
            if ($('#thumbnail').val() == '' || $('#item_id').val() == 0) {
                mooAlert.alert(mooPhrase.__('select_entry_to_submit'));
                event.preventDefault();
            }
        });
        $('.photo_submit_btn').unbind("click");
        $('.photo_submit_btn').click(function (event) {
            $(".photo_submit_btn").addClass('disabled');
            event.preventDefault();
            var entry_select = $(this).closest("a.entry_select");
            $('#thumbnail').val(entry_select.data('file'));
            $('#thumbnail_name').val(entry_select.data('fname'));
            $('#item_id').val(entry_select.data('id'));
            $('.select_entry_li').removeClass('active');
            entry_select.parents('.select_entry_li').addClass('active');
            $('#caption').val(entry_select.find('.photo_caption').val());
            if ($('#thumbnail').val() == '' || $('#item_id').val() == 0) {
                mooAlert.alert(mooPhrase.__('select_entry_to_submit'));
            }else{
                // alert('xxx');
                var button = $(this);
                button.addClass('disabled');
                mooAjax.post({
                    url: mooConfig.url.base + '/contests/entry_upload_app',
                    data: jQuery("#select_entry").serialize()
                }, function (data) {
                    var json = $.parseJSON(data);
                    if (json.result == 1) {
                        if (!mooConfig.isApp) {
                            if (json.url != '') {
                                window.location = json.url + '?app_no_tab=1';
                            } else {
                                window.location = mooConfig.url.base + '/contests/view/' + json.id +'?app_no_tab=1';
                            }
                        }else{
                            window.location = mooConfig.url.base + '/contests/entry_upload_onapp_finish?app_no_tab=1';
                        }
                        
                       //  $(".photo_submit_btn").removeClass('disabled');
                    } else {
                        button.removeClass('disabled');
                        $(".photo_submit_btn").removeClass('disabled');
                        $(".error-message").show();
                        $(".error-message").html(json.message);
                    }
                });
            }
            return false;
        });
        

    };
    var initCountdown = function (id, date_time) {
        $('#' + id).countdown(date_time + ' UTC').on('update.countdown', function (event) {
            $(this).html(event.strftime(''
                    + '<div class="contest_day"><div><span class="c_countdown">%-D</span> <span>' + mooPhrase.__('days') + '</span></div></div>'
                    + '<div class="contest_hrs"><div><span class="c_countdown">%H</span> <span>' + mooPhrase.__('hrs') + '</span></div><span>:</span>'
                    + '<div><span class="c_countdown">%M</span> <span>' + mooPhrase.__('min') + '</span></div><span>:</span>'
                    + '<div><span class="c_countdown">%S</span> <span>' + mooPhrase.__('sec') + '</span></div></div>'));
        });
    };
    var initCountdownMobile = function (id, date_time) {
        $('#' + id).countdown(date_time + ' UTC').on('update.countdown', function (event) {
            $(this).html(event.strftime('<div class="contest_hrs"><div><span class="c_countdown">%-D</span> <span>' + mooPhrase.__('days') + '</span></div><div><span class="c_countdown">%H</span> <span>' + mooPhrase.__('hrs') + '</span></div><span>:</span>'
                    + '<div><span class="c_countdown">%M</span> <span>' + mooPhrase.__('min') + '</span></div><span>:</span>'
                    + '<div><span class="c_countdown">%S</span> <span>' + mooPhrase.__('sec') + '</span></div></div>'));
        });
    };
    var initContestInvite = function () {
        $('#sendButton').unbind('click');
        $('#sendButton').click(function () {
            mooButton.disableButton('sendButton');
            $('#sendButton').spin('small');
            $.post(mooConfig.url.base + "/contests/ajax_doSend", $("#sendMessage").serialize(), function (data) {
                mooButton.enableButton('sendButton');
                $('#sendButton').spin(false);
                var json = $.parseJSON(data);
                if (json.result == 1)
                {
                    $("#subject").val('');
                    $("#message").val('');
                    $(".error-message").hide();
                    $('#themeModal').modal('hide');
                    mooAlert.alert(mooPhrase.__('your_message_has_been_sent'));
                } else
                {
                    $(".error-message").show();
                    $(".error-message").html(json.message);
                }
            });
            return false;
        });
    };
    var initSubmitBtnVideo = function () {
        if (mooConfig.isApp) {
            $('#saveVideoEntryBtn').unbind('click');
            $('body').on('click', '#saveVideoEntryBtn', function (e) {
                initSubmitOnApp('#createForm');
                $(this).addClass('disabled');
            });
        }
    }
    var initSubmitVideo = function () {
        $('#fetchButton').unbind('click');
        $('#fetchButton').click(function () {
            $('#fetchButton').spin('small');
            $("#videoForm .error-message").hide();

            mooButton.disableButton('fetchButton');

            mooAjax.post({
                url: mooConfig.url.base + "/contests/aj_validate",
                data: $("#createForm").serialize()
            }, function (data) {
                mooButton.enableButton('fetchButton');

                if (data) {
                    $("#fetchForm .error-message").html(JSON.parse(data).error);
                    $("#fetchForm .error-message").show();
                    $('#fetchButton').spin(false);
                } else {
                    mooAjax.post({
                        url: mooConfig.url.base + "/contests/fetch",
                        data: $("#createForm").serialize()
                    }, function (data) {
                        mooButton.enableButton('fetchButton');

                        $("#fetchForm").slideUp();
                        $("#videoForm").html(data);
                    });
                }
            });
            return false;
        });

    };
    var initPlayer = function (file_url) {
        $("#jquery_jplayer_1").jPlayer({
            ready: function (event) {
                $(this).jPlayer("setMedia", {
                    mp3: file_url
                }).jPlayer("play");;
            },
            supplied: "mp3,mp4,ogg,webm,wav,m4a",
            wmode: "window",
            useStateClassSkin: true,
            autoBlur: false,
            smoothPlayBar: true,
            keyEnabled: true,
            remainingDuration: true,
            toggleDuration: true
        });
    };
    return{
        initCreateContest: function (settings) {
            initCreateContest(settings);
        },
        initViewContest: function ()
        {
            initViewContest();
        },
        initOnAward: function () {
            initOnAward();
        },
        initOnPolicy: function () {
            initOnPolicy();
        },
        initListEntries: function () {
            initListEntries();
        },
        initOnEntries: function () {
            initOnEntries();
        },
        initSubmitEntry: function () {
            initSubmitEntry();
        },
        initSelectPhoto: function () {
            initSelectItem();
        },
        initSelectVideo: function () {
            initSelectItem();
        },
        initOnEditEntry: function () {
            initOnEditEntry();
        },
        initCandidateList: function () {
            initCandidateList();
        },
        initViewEntry: function () {
            initViewEntry();
        },
        initCountdown: function (id, date_time) {
            initCountdown(id, date_time);
        },
        initCountdownMobile: function (id, date_time) {
            initCountdownMobile(id, date_time);
        },
        initContestInvite: function () {
            initContestInvite();
        },
        initOnListing: function () {
            initOnListing();
        },
        initOnViewUserVote: function () {
            initOnViewUserVote();
        },
        initOnEditEntryByAdmin: function () {
            initOnEditEntryByAdmin();
        },
        initSubmitVideo: function () {
            initSubmitVideo();
        },
        initAfterFetch: function () {
            initAfterFetch();
        },
        initSubmitMusic: function () {
            initSubmitMusic();
        },
        initPlayer: function (file) {
            initPlayer(file);
        },
        initEditEntry:  function() {
            initEditEntry();
        },
        initSubmitBtnVideo: function(){
            initSubmitBtnVideo();
        }
    };
}));