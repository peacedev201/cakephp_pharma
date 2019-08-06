
<script>
$(document).ready(function(){    
    $('#createButton').click(function(){
        disableButton('createButton');
        $.post("<?php echo $this->request->base.$url_admin_feebback.$url_blockips.$url_ajax_save?>", $("#createForm").serialize(), function(data){
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
</script>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title"><?php echo __d('feedback', 'Add New Ip');?></h4>
</div>
<div class="modal-body">
<!-- <form id="createForm" class="form-horizontal" role="form"> -->
<?php echo  $this->Form->create('FeedbackBlockip', array('class' => 'form-horizontal', 'id' => 'createForm', 'role' => 'form')); ?>
    <?php echo  $this->Form->hidden('id'); ?>
    <div class="form-body">
        <div class="form-group">
            <label class="col-md-3 control-label"><?php echo __d('feedback', 'IP Address');?></label>
            <div class="col-md-9">
                <?php echo  $this->Form->text('blockip_address',array('placeholder'=>__d('feedback', 'IP Address'),'class'=>'form-control')); ?>
            </div>
        </div>
        <div class="form-group" style="display: none">
            <label class="col-md-3 control-label"><?php echo __d('feedback', 'Feedback Posting');?></label>
            <div class="col-md-9">
                <?php echo  $this->Form->hidden('blockip_feedback', array(
                    'value' => 1
                )); ?>
            </div>
        </div>
        <!-- <hr>
        <h4>Post Permission</h4>
        <?php echo $this->element('admin/permissions', array('permission' => $category['Category']['create_permission'])); ?> -->
    </div>
</form>
    <div class="alert alert-danger error-message" style="display:none;margin-top:10px;">

    </div>

</div>
<div class="modal-footer">

    <button type="button" class="btn default" data-dismiss="modal"><?php echo __d('feedback', 'Close');?></button>
    <a href="#" id="createButton" class="btn blue" onclick="createPlugin()"><i class="icon-save"></i> <?php echo __d('feedback', 'Save');?></a>

</div>