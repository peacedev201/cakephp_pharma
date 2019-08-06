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
    $this->Html->addCrumb(__d('verify_profile', 'Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('verify_profile', 'Profile Verify Reasons'), array('controller' => 'verify_profile_reasons', 'action' => 'admin_index'));
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Profile Verify'));
    $this->end();
?>

<?php $this->Paginator->options(array('url' => $this->passedArgs)); ?>

<?php $this->Html->scriptStart(array('inline' => false)); ?>
    $(document).on('loaded.bs.modal', function (e) {
        Metronic.init();
    });
    $(document).on('hidden.bs.modal', function (e) {
        $(e.target).removeData('bs.modal');
    });
<?php $this->Html->scriptEnd(); ?>

<div class="portlet-body form">
    <div class="portlet-tabs">
        <div class="tabbable tabbable-custom boxless tabbable-reversed ">
            <?php echo$this->Moo->renderMenu('VerifyProfile', __d('verify_profile', 'Reasons')); ?>
            <div class="portlet-body" style="margin-top: 10px;">
                <div class="table-toolbar">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="btn-group">
                                <button class="btn btn-gray" onclick="confirmSubmitForm('<?php echo __d('verify_profile', 'Are you sure you want to delete these reasons? This cannot be undone!'); ?>', 'deleteForm')">
                                    <?php echo __d('verify_profile', 'Delete'); ?>
                                </button>
                            </div>
                            <div class="btn-group">
                                <button class="btn btn-gray" data-backdrop="true" data-toggle="modal" data-target="#themeModal" href="<?php echo $this->Html->url(array('plugin' => 'verify_profile', 'controller' => 'verify_profile_reasons', 'action' => 'admin_create', 0)); ?>">
                                <?php echo __d('verify_profile', 'Add New'); ?>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6"></div>
                    </div>
                </div>
                <form method="post" action="<?php echo $this->Html->url(array('plugin' => 'verify_profile', 'controller' => 'verify_profile_reasons', 'action' => 'admin_multi_delete')); ?>" id="deleteForm">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr class="tbl_head">
                                <?php if ($cuser['Role']['is_super']): ?>
                                <th width="30"><input type="checkbox" onclick="toggleCheckboxes2(this)"></th>
                                <?php endif; ?>
                                <th width="30"><?php echo $this->Paginator->sort('id', __d('verify_profile', 'ID')); ?></th>
                                <th><?php echo __d('verify_profile', 'Description'); ?></th>
                                <th width="50"><?php echo __d('verify_profile', 'Actions'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $count = 0; ?>
                            <?php foreach ($aReasons as $aReason): ?>
                                <tr class="gradeX <?php echo (++$count % 2 ? "odd" : "even") ?>" id="<?php echo $aReason['VerifyReason']['id']; ?>">
                                    <?php if ( $cuser['Role']['is_super'] ): ?>
                                    <td><input type="checkbox" name="reasons[]" value="<?php echo $aReason['VerifyReason']['id']; ?>" class="check"></td>
                                    <?php endif; ?>
                                    <td><?php echo $aReason['VerifyReason']['id']; ?></td>
                                    <td><?php echo $aReason['VerifyReason']['description']; ?></td>
                                    <td>
                                        <?php
                                            $this->MooPopup->tag(array(
                                                'href'=>$this->Html->url(array(
                                                    'plugin' => 'verify_profile', 
                                                    "controller" => "verify_profile_reasons",
                                                    "action" => "admin_create",
                                                    $aReason['VerifyReason']['id']
                                                )),
                                                'title' => '',
                                                'innerHtml'=> '<i class="icon-edit icon-small"></i>',
                                                'target' => 'themeModal'
                                            ));
                                        ?>
                                        <a href="javascript:void(0)" onclick="mooConfirm('<?php echo __d('verify_profile', 'Are you sure you want to delete this reason? This cannot be undone!'); ?>', '<?php echo $this->Html->url(array('plugin' => 'verify_profile', 'controller' => 'verify_profile_reasons', 'action' => 'admin_delete', $aReason['VerifyReason']['id'])); ?>')"><i class="icon-trash icon-small"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </form>
            </div>
                            
            <div class="pagination">
		<?php echo $this->Paginator->prev('« '.__d('verify_profile', 'Previous'), null, null, array('class' => 'disabled')); ?>
		<?php echo $this->Paginator->numbers(); ?>
		<?php echo $this->Paginator->next(__d('verify_profile', 'Next').' »', null, null, array('class' => 'disabled')); ?>
            </div>
        </div>
    </div>
</div>