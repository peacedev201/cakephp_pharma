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
            $.post("<?php echo $this->Html->url(array('plugin' => 'role_badge', 'controller' => 'award_badges', 'action' => 'admin_save_validate')); ?>", $("#createForm").serialize(), function(data) {
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
    #uniform-show_next_name {
        margin: 1px 3px 4px 0 !important;
    }
</style>

<?php $oRoleBadgeHelper = MooCore::getInstance()->getHelper('RoleBadge_RoleBadge'); ?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title"><?php echo (!empty($aAwardBadge['AwardBadge']['id'])) ? __d('role_badge', 'Edit Award') : __d('role_badge', 'Add New Award'); ?></h4>
</div>
<div class="modal-body">
    <form id="createForm" class="form-horizontal" action="<?php echo $this->Html->url(array('plugin' => 'role_badge', 'controller' => 'award_badges', 'action' => 'admin_save')); ?>" method="post" enctype="multipart/form-data">
        <?php echo $this->Form->hidden('id', array('value' => $aAwardBadge['AwardBadge']['id'])); ?>
        <?php echo $this->Form->hidden('thumbnail', array('value' => $aAwardBadge['AwardBadge']['thumbnail'])); ?>
        <div class="form-body">
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('role_badge', 'Name');?></label>
                <div class="col-md-9">
                    <?php echo $this->Form->text('name', array('placeholder' => __d('role_badge','Enter text'), 'class' => 'form-control', 'value' => $aAwardBadge['AwardBadge']['name'])); ?>
                    
                    <div class="tips">
                    <?php if (empty($aAwardBadge['AwardBadge']['name'])): ?>
                        *<?php echo __d('role_badge', 'You can add translation language after creating Award'); ?>
                    <?php else: ?>
                        <?php 
                        $this->MooPopup->tag(array(
                            'href'=>$this->Html->url(array('plugin' => 'role_badge', 'controller' => 'award_badges', 'action' => 'admin_ajax_translate', 'name', $aAwardBadge['AwardBadge']['id'])),
                            'innerHtml'=> __d('role_badge', 'Translation'),
                            'title' => __d('role_badge', 'Translation'),
                            'target' => 'ajax-translate'
                        ));
                        ?>
                    <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('role_badge', 'Description');?></label>
                <div class="col-md-9">
                    <?php echo $this->Form->textarea('description', array('class' => 'form-control', 'value' => $aAwardBadge['AwardBadge']['description'])); ?>
                    
                    <div class="tips">
                    <?php if (empty($aAwardBadge['AwardBadge']['description'])): ?>
                        *<?php echo __d('role_badge', 'You can add translation language after creating Award'); ?>
                    <?php else: ?>
                        <?php 
                        $this->MooPopup->tag(array(
                            'href'=>$this->Html->url(array('plugin' => 'role_badge', 'controller' => 'award_badges', 'action' => 'admin_ajax_translate', 'description', $aAwardBadge['AwardBadge']['id'])),
                            'innerHtml'=> __d('role_badge', 'Translation'),
                            'title' => __d('role_badge', 'Translation'),
                            'target' => 'ajax-translate'
                        ));
                        ?>
                    <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">&nbsp;</label>
                <div class="col-md-9">
                    <?php echo $this->Form->input('show_next_name', array('type' => 'checkbox', 'label' => __d('role_badge', 'Show next to member full name'), 'checked' => (!empty($aAwardBadge['AwardBadge']['show_next_name']) ? 1 : 0))); ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('role_badge', 'Icon');?></label>
                <div class="col-md-9">
                    <input type="file" name="thumbnail">
                    <div style="margin-top: 10px;">
                        <?php if (!empty($aAwardBadge['AwardBadge']['thumbnail'])): ?>
                        <img height="50" src="<?php echo $oRoleBadgeHelper->getImage($aAwardBadge['AwardBadge']['thumbnail']); ?>" />
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
        </div>
    </form>
</div>

<div class="modal-footer">
    <a href="javascript:void(0)" id="btnSave" class="btn btn-action"><?php echo __d('role_badge', 'Save'); ?></a>
    <button type="button" class="btn default" data-dismiss="modal"><?php echo __d('role_badge', 'Close'); ?></button>
</div>