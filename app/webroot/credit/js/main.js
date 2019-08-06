(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery', 'mooButton', 'mooPhrase', 'mooGlobal','mooFileUploader','mooValidate', 'typeahead', 'bloodhound', 'tagsinput'], factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS-like
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals (root is window)
        root.mooCredit = factory();
    }
}(this, function ($, mooButton, mooPhrase, mooGlobal,mooFileUploader,mooValidate, typeahead, bloodhound, tagsinput) {

    var initCreditSendToMember = function(){
        var friends_friendSuggestion = new Bloodhound({
            datumTokenizer:function(d){
                return Bloodhound.tokenizers.whitespace(d.name);
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            prefetch: {
                url: mooConfig.url.base +'/credits/get_member.json',
                cache: false,
                filter: function(list) {
                    return $.map(list.data, function(obj) {
                        return obj;
                    });
                }
            },
            
            identify: function(obj) { return obj.id; },
        });
            
        friends_friendSuggestion.initialize();

        $('#friendSuggestion').tagsinput({
            freeInput: false,
            itemValue: 'id',
            itemText: 'name',
            typeaheadjs: {
                name: 'friends_friendSuggestion',
                displayKey: 'name',
                highlight: true,
                limit:10,
                source: friends_friendSuggestion.ttAdapter(),
                templates:{
                    notFound:[
                        '<div class="empty-message">',
                            'unable to find any member',
                        '</div>'
                    ].join(' '),
                    suggestion: function(data){
                    if($('#userTagging').val() != '')
                    {
                        var ids = $('#friendSuggestion').val().split(',');
                        if(ids.indexOf(data.id) != -1 )
                        {
                            return '<div class="empty-message" style="display:none">unable to find any member</div>';
                        }
                    }
                        return [
                            '<div class="suggestion-item">',
                                '<img alt src="'+data.avatar+'"/>',
                                '<span class="text">'+data.name+'</span>',
                            '</div>',
                        ].join('')
                    }
                }
            }
        });

        $('#sendButton').click(function(){
            mooButton.disableButton('sendButton');
            $('#sendButton').spin('small');
            //console.log(sModal);
            $.post(mooConfig.url.base + '/credits/ajax_doSend', jQuery("#sendCredits").serialize(), function(data){
                mooButton.enableButton('sendButton');
                $('#sendButton').spin(false);
                var json = $.parseJSON(data);

                if ( json.result == 1 )
                {
                    $("#friend").val('');
                    $("#credit").val('');
                    $(".error-message").hide();
                    $(".alert-success").show();
                    $(".alert-success").html(json.message);
                }
                else
                {
                    $(".alert-success").hide();
                    $(".error-message").show();
                    $(".error-message").html(json.message);
                }
            });

            return false;
        });
    }

    var initCreditSendToFriend = function()
    {
        $("#friends").tokenInput(mooConfig.url.base + '/friends/do_get_json',
            { preventDuplicates: true,
                hintText: mooPhrase.__('Enter a friend\'s name'),
                noResultsText: mooPhrase.__('No results'),
                tokenLimit: 10,
                resultsFormatter: function(item)
                {
                    return '<li>' + item.avatar + item.name + '</li>';
                }
            }
        );


        $('#sendButton').click(function(){
            mooButton.disableButton('sendButton');
            $('#sendButton').spin('small');
            //console.log(sModal);
            $.post(mooConfig.url.base + '/credits/ajax_doSend', jQuery("#sendCredits").serialize(), function(data){
                mooButton.enableButton('sendButton');
                $('#sendButton').spin(false);
                var json = $.parseJSON(data);

                if ( json.result == 1 )
                {
                    $("#friend").val('');
                    $("#credit").val('');
                    $(".error-message").hide();
                    $(".alert-success").show();
                    $(".alert-success").html(json.message);
                }
                else
                {
                    $(".alert-success").hide();
                    $(".error-message").show();
                    $(".error-message").html(json.message);
                }
            });

            return false;
        });
    }

    var creditFaqCreate = function(){
            mooButton.disableButton('createButton');
            $.post(mooConfig.url.base + "/credits/faq_save", $("#createForm").serialize(), function (data) {
                mooButton.enableButton('createButton');
                var json = $.parseJSON(data);

                if (json.result == 1)
                    location.reload();
                else
                {
                    $(".error-message").show();
                    $(".error-message").html('<strong>Error! </strong>' + json.message);
                }
            });
            return false;
    }

    var toggleField = function()
    {
        $('.opt_field').toggle();
    }

    var initWithDrawRequest = function()
    {
        $('#createFormWithDraw').validate({
            errorClass: "error-message",
            errorElement: "div"
        });

        $('#amount').on('keyup',function(){
            var text,
                number = $(this).val();
            text = (number * formula_credit) / formula_money;
            $("#wd_money").text(text);
        });

        $.validator.addMethod('compare',function(value, element, params){
            return (parseInt(value) >= parseInt(minimum_withdrawal_amount) && parseInt(value) <= parseInt(maximum_withdrawal_amount)) ? true : false;

            return false;
        },mooPhrase.__('validate_between'));

        $("#btnWithDraw").on('click',function(){
            $("#amount").rules("add",{
                    required: true,
                    number:true,
                    compare : $("#amount").val()
                });

           $("#payment").rules("add",{
                required:true
           });
            $("#payment_info").rules("add",{
                required: function(element){
                    if($('#li_payment_info').css("display") == 'none'){
                        return false;
                    }else{
                        return true;
                    }
                }
            });
        });

        $("#payment").on('change',function(){
           var value = $("#payment option:selected").val();

            if(value != ""){
                $("#li_payment_info").show();
            }else{
                $("#li_payment_info").hide();
            }
        });

        $(".delete_withdraw_request").on('click',function(){
           var id = $(this).attr('data-id');
            $('#themeModal .modal-content').load(url + "/credits/withdraw_delete/" + id);
            $("#themeModal").modal('show');
        });

    }

    var init = function(){
        $('body').on('validateCredit', function(e, data){
            data.status = false;
            data.message = mooPhrase.__('credit_amount_not_enought');
        });
    }

    var initBuyCreditPaypal = function(urlReturn, urlReturnPaypal){
        $('input[type="submit"]').prop('disabled', true);
        $('#buyCreditForm input').on('change', function() {
            $('input[type="submit"]').prop('disabled', false);
            var valSelect = $('input[name=sell_selected]:checked', '#buyCreditForm').val(); 
            var arrVal = valSelect.split('_');
            $("#sell_id").val(arrVal[0]);
            $("#amount").val(arrVal[1]);
            $("#return").val(urlReturn + "/" + arrVal[0] + '?app_no_tab=1');
            $("#notify_url").val(urlReturnPaypal + "/" + arrVal[0]);
        });
    }

    return {
        init : function(){
            init();
        },
        initCreditSendToMember: function (){
            initCreditSendToMember();
        },
        initCreditSendToFriend: function () {
            initCreditSendToFriend();
        },
        creditFaqCreate: function () {
            creditFaqCreate();
        },
        toggleField: function () {
            toggleField();
        },
        uploader : function() {
            uploader();
        },
        initWithDrawRequest : function() {
            initWithDrawRequest();
        },
        initBuyCreditPaypal: function(urlReturn, urlReturnPaypal){
            initBuyCreditPaypal(urlReturn, urlReturnPaypal);
        }
    }

}));
