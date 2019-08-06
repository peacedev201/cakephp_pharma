<style type="text/css" media="screen">
@media (max-width:991px) {
    .modal-dialog {
        width: auto !important;
    }
    .create .create_form {
        float: none !important;
        width: 100% !important;
    }
}
</style>
<script>
    
    <?php if ($is234): ?>
                require(["jquery","mooButton"], function($,mooButton) {
                    $(document).ready(function(){
                         $('#createButton').click(function(){
                            mooButton.disableButton('createButton');
                            $.post("<?php echo $this->request->base.$url_feedback.$url_ajax_save_status?>", $("#createForm").serialize(), function(data){
                                mooButton.enableButton('createButton');
                                var json = $.parseJSON(data);
                                if ( json.result == 1 )
                                    location.reload();
                                else
                                {
                                    $(".error-message").show();
                                    $(".error-message").html(json.message);
                                }
                            });
                            return false;
                        });  
                        
                    });
                });      
        <?php else: ?>    
$(document).ready(function(){    
    $('#createButton').click(function(){
        disableButton('createButton');
        $.post("<?php echo $this->request->base.$url_feedback.$url_ajax_save_status?>", $("#createForm").serialize(), function(data){
            enableButton('createButton');
            var json = $.parseJSON(data);
            
            if ( json.result == 1 )
                location.reload();
            else
            {
                $(".error-message").show();
                $(".error-message").html(json.message);
            }
        });
        return false;
    });  
});
<?php endif; ?>
</script>
<div class="create">
    <div class="title-modal">
        <?php echo __d('feedback', 'Add status')?> 
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
    </div>
    <div class="modal-body">
        <?php if ($permission_set_status): ?>
            <div class="create_form create_form_feedback">
                <?php echo  $this->Form->create('AddStatus', array('class' => 'form-horizontal', 'id' => 'createForm', 'role' => 'form')); ?>
                <?php echo  $this->Form->hidden('iFeedback_id', array('value' => $iFeedback_id)); ?>
                <ul class="list6 list6sm2" style="position:relative">
                    <li>
                        <div class="col-md-2"><label><?php echo __d('feedback', 'Status')?></label></div>
                        <div class="col-md-10"><?php echo  $this->Form->select('status_id', $aStatuses, array('empty'=>false, 'default' => (isset($iDefault_id)) ? $iDefault_id : null ))?></div>
                        <div class="clear"></div>
                    </li>
                    <li>
                        <div class="col-md-2"><label><?php echo __d('feedback', 'Comment')?></label></div>
                        <div class="col-md-10"><?php echo  $this->Form->textarea('status_body', array(
                            'value' => $aFeedback['Feedback']['status_body']
                        )); ?></div>
                        <div class="clear"></div>
                    </li>

                    <li>
                        <div class="col-md-2"><label>&nbsp;</label></div>
                        <div class="col-md-10">
                            <a href="#" id="createButton" class="button button-action post-feedback btn-action"><?php echo __d('feedback', 'Save');?></a>
                            <a class="button button-action" href="javascript:void(0)" data-dismiss="modal"><?php echo __d('feedback', 'Close');?></a>
                        </div>
                        <div class="clear"></div>
                    </li>
                </ul>
                </form>
                <div class="error-message" style="display:none;"></div>
            </div>
        <?php else: ?>
            <?php echo __d('feedback', 'You dont\'t have permission.') ?> 
        <?php endif; ?>
    </div>
</div>
