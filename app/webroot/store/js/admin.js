
jQuery.admin = {
    action: function(id, task) 
    {
        $('input.multi_cb').removeAttr('checked');
        $('#cb' + id).trigger('click');
        if(task == 'delete')
        {
            if(confirm(mooPhrase.__('are_you_sure_you_want_to_delete')))
            {
                this.doSubmit(task);
            }
        }
        else if(task == 'create')
        {
            window.location = jQuery('#adminForm').attr('action') + task + '/' + id;
        }
        else
        {
            this.doSubmit(task);
        }
    },
    
    activeAll: function(task)
    {
        if(jQuery('input.multi_cb:checked').length < 1)
        {
            mooAlert(mooPhrase.__('you_must_select_at_least_an_item'));
        }
        else 
        {
            this.doSubmit(task);
        }
    },
    
    deleteAll: function(task)
    {
        if(jQuery('input.multi_cb:checked').length < 1)
        {
            mooAlert(mooPhrase.__('you_must_select_at_least_an_item'));
        }
        else if(confirm(mooPhrase.__('are_you_sure_you_want_to_delete'))) 
        {
            this.doSubmit(task);
        }
    },
    
    saveAll: function(task)
    {
        jQuery('.multi_cb').trigger('click');
        this.doSubmit(task);
    },

    doSubmit: function(task)
    {
        var action = jQuery('#adminForm').attr('action');
        jQuery('#adminForm').attr('action', action + task);
        jQuery('#adminForm').submit();
    },
    
    toggleCheckboxes: function (obj)
    {
        if (jQuery(obj).is(':checked'))
        {
            jQuery('.multi_cb').attr('checked', 'checked');
            jQuery('.multi_cb').parent('span').addClass('checked');
        }
        else
        {
            jQuery('.multi_cb').attr('checked', false);
            jQuery('.multi_cb').parent('span').removeClass('checked');
        }
    }
};