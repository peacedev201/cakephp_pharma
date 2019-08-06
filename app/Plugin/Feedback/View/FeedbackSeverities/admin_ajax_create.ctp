<script>
$(document).ready(function(){    
    $('#createButton').click(function(){
        disableButton('createButton');
        $.post("<?php echo $this->request->base.$url_admin_feebback.$url_severities.$url_ajax_save?>", $("#createForm").serialize(), function(data){
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
    <h4 class="modal-title"><?php echo __d('feedback', 'Add New Severity');?></h4>
</div>
<div class="modal-body">
<!-- <form id="createForm" class="form-horizontal" role="form"> -->
<?php echo  $this->Form->create('FeedbackSeverity', array('class' => 'form-horizontal', 'id' => 'createForm', 'role' => 'form')); ?>
    <?php echo  $this->Form->hidden('id'); ?>
    <?php echo  $this->Form->hidden('user_id'); ?>
    <div class="form-body">
        <div class="form-group">
            <label class="col-md-3 control-label"><?php echo __d('feedback', 'Name');?></label>
            <div class="col-md-9">
                <?php echo  $this->Form->text('name',array('placeholder'=>__d('feedback', 'Name'),'class'=>'form-control')); ?>
                <?php if (!$IsEdit) : ?>
                    <div class="tips">*<?php echo __d('feedback', 'You can add translation language after creating category') ?></div>
                <?php else : ?>
                    <div class="tips">
                        <?php
                        $this->MooPopup->tag(array(
                            'href' => $this->Html->url(array("controller" => "feedback_severities",
                                "action" => "admin_ajax_translate",
                                "plugin" => "feedback",
                                $aSeverity['FeedbackSeverity']['id']
                            )),
                            'title' => __d('feedback', 'Translation'),
                            'innerHtml' => __d('feedback', 'Translation'),
                        ));
                        ?>                       
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label"><?php echo __d('feedback', 'Active');?></label>
            <div class="col-md-9">
                <?php echo  $this->Form->checkbox('is_active'); ?>
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