
<script>
    $(document).ready(function () {
        $('#createButton').click(function () {
            disableButton('createButton');
            $.post("<?php echo $this->request->base .'/admin/feedback/feedbacks'. $url_ajax_save_status ?>", $("#createForm").serialize(), function (data) {
                enableButton('createButton');
                var json = $.parseJSON(data);

                if (json.result == 1)
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
    <h4 class="modal-title"><?php echo __d('feedback', 'Add Status'); ?></h4>
</div>
<div class="modal-body">
    <?php if ($permission_set_status): ?>
        <!-- <form id="createForm" class="form-horizontal" role="form"> -->
        <?php echo $this->Form->create('AddStatus', array('class' => 'form-horizontal', 'id' => 'createForm', 'role' => 'form')); ?>
        <?php echo $this->Form->hidden('iFeedback_id', array('value' => $iFeedback_id)); ?>
        <div class="form-body">
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('feedback', 'Status'); ?></label>
                <div class="col-md-9">                
                    <?php echo $this->Form->select('status_id', $aStatuses, array('empty' => false, 'default' => (isset($iDefault_id)) ? $iDefault_id : null)) ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('feedback', 'Comment'); ?></label>
                <div class="col-md-9">
                    <?php echo $this->Form->textarea('status_body'); ?>
                </div>
            </div>
            <!-- <hr>
            <h4>Post Permission</h4>
            <?php echo $this->element('admin/permissions', array('permission' => $category['Category']['create_permission'])); ?> -->
        </div>
    </form>
    <div class="alert alert-danger error-message" style="display:none;margin-top:10px;"></div>
    <?php else: ?>
        <?php echo __d('feedback', 'You dont\'t have permission.') ?> 
    <?php endif; ?>
</div>
<?php if ($permission_set_status): ?>
<div class="modal-footer">

    <button type="button" class="btn default" data-dismiss="modal"><?php echo __d('feedback', 'Close'); ?></button>
    <a href="#" id="createButton" class="btn blue" onclick="createPlugin()"><i class="icon-save"></i> <?php echo __d('feedback', 'Save'); ?></a>

</div>
<?php endif; ?>