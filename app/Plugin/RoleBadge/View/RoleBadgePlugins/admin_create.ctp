<?php
/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>

<script type="text/javascript">
    $(document).ready(function() {
        $('#btnSave').click(function() {
            disableButton('btnSave');
            $.post("<?php echo $this->Html->url(array('plugin' => 'role_badge', 'controller' => 'role_badge_plugins', 'action' => 'admin_save_validate')); ?>", $("#createForm").serialize(), function(data) {
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
    #uniform-show_next_name, #uniform-thumbnail_default {
        margin: 1px 3px 4px 0 !important;
    }
</style>

<?php $oRoleBadgeHelper = MooCore::getInstance()->getHelper('RoleBadge_RoleBadge'); ?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title"><?php echo (!empty($aRoleBadge['RoleBadge']['id'])) ? __d('role_badge', 'Edit Badge') : __d('role_badge', 'Add New Badge'); ?></h4>
</div>
<div class="modal-body">
    <form id="createForm" class="form-horizontal" action="<?php echo $this->Html->url(array('plugin' => 'role_badge', 'controller' => 'role_badge_plugins', 'action' => 'admin_save')); ?>" method="post" enctype="multipart/form-data">
        <?php echo $this->Form->hidden('id', array('value' => $aRoleBadge['RoleBadge']['id'])); ?>
        <?php echo $this->Form->hidden('desktop_profile', array('value' => $aRoleBadge['RoleBadge']['desktop_profile'])); ?>
        <?php echo $this->Form->hidden('desktop_feed', array('value' => $aRoleBadge['RoleBadge']['desktop_feed'])); ?>
        <?php echo $this->Form->hidden('mobile_profile', array('value' => $aRoleBadge['RoleBadge']['mobile_profile'])); ?>
        <?php echo $this->Form->hidden('mobile_feed', array('value' => $aRoleBadge['RoleBadge']['mobile_feed'])); ?>
        <div class="form-body">
        <?php if(empty($aRoles) && empty($aRoleBadge['RoleBadge']['id'])): ?>
            <div class="form-group">
                <div class="col-md-12">
                    <div class="alert alert-danger error-message" style="margin-top: 10px;">
                        <?php echo __d('role_badge', 'No more user role found'); ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('role_badge', 'Role');?></label>
                <div class="col-md-9">
                    <?php if(!empty($aRoleBadge['RoleBadge']['role_id'])): ?>
                        <?php echo $this->Form->text('role_text', array('value' => $aRoles[$aRoleBadge['RoleBadge']['role_id']], 'class' => 'form-control', 'disabled' => true)); ?>
                        <?php echo $this->Form->hidden('role_id', array('value' => $aRoleBadge['RoleBadge']['role_id'])); ?>
                    <?php else: ?>
                        <?php echo $this->Form->select('role_id', $aRoles, array('value' => '', 'class' => 'form-control')); ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">&nbsp;</label>
                <div class="col-md-9">
                    <?php echo $this->Form->input('show_next_name', array('type' => 'checkbox', 'label' => __d('role_badge', 'Show next to member full name'), 'checked' => (!empty($aRoleBadge['RoleBadge']['show_next_name']) ? 1 : 0))); ?>
                    <?php echo $this->Form->input('thumbnail_default', array('type' => 'checkbox', 'label' => __d('role_badge', 'Icon default'))); ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">
                    <?php echo __d('role_badge', 'Desktop Profile');?>
                    <br/>
                    (<a data-html="true" href="javascript:void(0)" class="tooltips" data-original-title="<?php echo __d('role_badge', 'Recommended size height %s', '26px');?>" data-placement="top">26px</a>)
                </label>
                <div class="col-md-9">
                    <input type="file" name="desktop_profile">
                    <div style="margin-top: 10px;">
                        <?php if (!empty($aRoleBadge['RoleBadge']['desktop_profile'])): ?>
                        <img src="<?php echo $oRoleBadgeHelper->getImage($aRoleBadge['RoleBadge']['desktop_profile']); ?>" />
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">
                    <?php echo __d('role_badge', 'Desktop Feed');?>
                    <br/>
                    (<a data-html="true" href="javascript:void(0)" class="tooltips" data-original-title="<?php echo __d('role_badge', 'Recommended size height %s', '14px');?>" data-placement="top">14px</a>)
                </label>
                <div class="col-md-9">
                    <input type="file" name="desktop_feed">
                    <div style="margin-top: 10px;">
                        <?php if (!empty($aRoleBadge['RoleBadge']['desktop_feed'])): ?>
                        <img src="<?php echo $oRoleBadgeHelper->getImage($aRoleBadge['RoleBadge']['desktop_feed']); ?>" />
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">
                    <?php echo __d('role_badge', 'Mobile Profile');?>
                    <br/>
                    (<a data-html="true" href="javascript:void(0)" class="tooltips" data-original-title="<?php echo __d('role_badge', 'Recommended size height %s', '50px');?>" data-placement="top">50px</a>)
                </label>
                <div class="col-md-9">
                    <input type="file" name="mobile_profile">
                    <div style="margin-top: 10px;">
                        <?php if (!empty($aRoleBadge['RoleBadge']['mobile_profile'])): ?>
                        <img src="<?php echo $oRoleBadgeHelper->getImage($aRoleBadge['RoleBadge']['mobile_profile']); ?>" />
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">
                    <?php echo __d('role_badge', 'Mobile Feed');?>
                    <br/>
                    (<a data-html="true" href="javascript:void(0)" class="tooltips" data-original-title="<?php echo __d('role_badge', 'Recommended size height %s', '28px');?>" data-placement="top">28px</a>)
                </label>
                <div class="col-md-9">
                    <input type="file" name="mobile_feed">
                    <div style="margin-top: 10px;">
                        <?php if (!empty($aRoleBadge['RoleBadge']['mobile_feed'])): ?>
                        <img src="<?php echo $oRoleBadgeHelper->getImage($aRoleBadge['RoleBadge']['mobile_feed']); ?>" />
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">&nbsp;</label>
                <div class="col-md-9">
                    <div class="alert alert-danger error-message" style="display: none; margin-top: 10px;"></div>
                </div>
            </div>
        <?php endif; ?>
        </div>
    </form>
</div>
<?php if(!empty($aRoles) || !empty($aRoleBadge['RoleBadge']['id'])): ?>
<div class="modal-footer">
    <a href="javascript:void(0)" id="btnSave" class="btn btn-action"><?php echo __d('role_badge', 'Save'); ?></a>
    <button type="button" class="btn default" data-dismiss="modal"><?php echo __d('role_badge', 'Close'); ?></button>
</div>
<?php endif; ?>