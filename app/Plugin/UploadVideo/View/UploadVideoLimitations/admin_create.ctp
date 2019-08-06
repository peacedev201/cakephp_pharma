<?php
/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>

<?php 
__d('upload_video', 'User role is required');
__d('upload_video', 'Videos can upload is required');
__d('upload_video', 'Videos can upload is numeric');
__d('upload_video', 'File size is required');
__d('upload_video', 'File size is numeric');
?>

<?php
$aPerType = array('D' => __d('upload_video', 'Day'), 'M' => __d('upload_video', 'Month'), 'Y' => __d('upload_video', 'Year'));
?>

<script type="text/javascript">
    $(document).ready(function() {
        $('#btnSave').click(function() {
            disableButton('btnSave');
            $.post("<?php echo $this->Html->url(array('plugin' => 'upload_video', 'controller' => 'upload_video_limitations', 'action' => 'admin_save_validate')); ?>", $("#createForm").serialize(), function(data) {
                enableButton('btnSave');
                var json = $.parseJSON(data);
                if (json.result === 1) {
                    $("#createForm").submit();
                } else {
                    $(".error-message").show();
                    $(".error-message").html(json.message);
                }
            });
            return false;
        });
    });
</script>

<style>
    .form-video-value, .form-video-per-type {
        display: inline-block;
        width: auto;
    }
</style>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title"><?php echo (!empty($aLimitation['UploadVideoLimitations']['id'])) ? __d('upload_video', 'Edit Limitation') : __d('upload_video', 'Add New Limitation'); ?></h4>
</div>

<div class="modal-body">
    <form id="createForm" class="form-horizontal" action="<?php echo $this->Html->url(array('plugin' => 'upload_video', 'controller' => 'upload_video_limitations', 'action' => 'admin_save')); ?>" method="post" enctype="multipart/form-data">
        <?php echo $this->Form->hidden('id', array('value' => $aLimitation['UploadVideoLimitations']['id'])); ?>
        <div class="form-body">
        <?php if(empty($aRoles) && empty($aLimitation['UploadVideoLimitations']['id'])): ?>
            <div class="form-group">
                <div class="col-md-12">
                    <div class="alert alert-danger error-message" style="margin-top: 10px;">
                        <?php echo __d('upload_video', 'No more user role found'); ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="form-group">
                <label class="col-md-5 control-label"><?php echo __d('upload_video', 'User role');?></label>
                <div class="col-md-7">
                    <?php if(!empty($aLimitation['UploadVideoLimitations']['role_id'])): ?>
                        <?php echo $this->Form->text('role_text', array('value' => $aRoles[$aLimitation['UploadVideoLimitations']['role_id']], 'class' => 'form-control', 'disabled' => true)); ?>
                        <?php echo $this->Form->hidden('role_id', array('value' => $aLimitation['UploadVideoLimitations']['role_id'])); ?>
                    <?php else: ?>
                        <?php echo $this->Form->select('role_id', $aRoles, array('value' => '', 'class' => 'form-control')); ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-5 control-label">
                    <?php echo __d('upload_video', 'How many videos can upload?'); ?>
                    <br />
                    <?php echo __d('upload_video', '(per day, per month and per year)'); ?>
                </label>
                <div class="col-md-7">
                    <?php echo $this->Form->text('value', array('value' => !empty($aLimitation['UploadVideoLimitations']['value']) ? $aLimitation['UploadVideoLimitations']['value'] : 0, 'class' => 'form-control form-video-value')); ?>
                    /
                    <?php echo $this->Form->select('per_type', $aPerType, array('empty' => false, 'value' => $aLimitation['UploadVideoLimitations']['per_type'], 'class' => 'form-control form-video-per-type')); ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-5 control-label">
                    <?php echo __d('upload_video', 'Max upload file size (MB)'); ?>
                </label>
                <div class="col-md-7">
                    <?php echo $this->Form->text('size', array('value' => !empty($aLimitation['UploadVideoLimitations']['size']) ? $aLimitation['UploadVideoLimitations']['size'] : 0, 'class' => 'form-control form-video-value')); ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-5 control-label">&nbsp;</label>
                <div class="col-md-7">
                    <?php echo __d('upload_video', "Enter '0' to unlimited"); ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-5 control-label">&nbsp;</label>
                <div class="col-md-7">
                    <div class="alert alert-danger error-message" style="display: none; margin-top: 10px;"></div>
                </div>
            </div>
        <?php endif; ?>
        </div>
    </form>
</div>

<?php if(!empty($aRoles) || !empty($aLimitation['UploadVideoLimitations']['id'])): ?>
<div class="modal-footer">
    <a href="javascript:void(0)" id="btnSave" class="btn btn-action"><?php echo __d('upload_video', 'Save'); ?></a>
    <button type="button" class="btn default" data-dismiss="modal"><?php echo __d('upload_video', 'Close'); ?></button>
</div>
<?php endif; ?>