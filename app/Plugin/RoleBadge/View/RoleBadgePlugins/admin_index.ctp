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
$this->Html->addCrumb(__d('role_badge', 'Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('role_badge', 'Badges Manager'), array('controller' => 'role_badge_plugins', 'action' => 'admin_index'));

$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array("cmenu" => "User Badges"));
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

<?php $oRoleBadgeHelper = MooCore::getInstance()->getHelper('RoleBadge_RoleBadge'); ?>
    
<div class="portlet-body form">
    <div class="portlet-tabs">
        <div class="tabbable tabbable-custom boxless tabbable-reversed ">
            <?php echo $this->Moo->renderMenu('RoleBadge', __d('role_badge', 'Badges Manager')); ?>
            <div class="portlet-body" style="margin-top: 10px;">
                <div class="table-toolbar">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="btn-group">
                                <button class="btn btn-gray" data-backdrop="true" data-toggle="modal" data-target="#themeModal" href="<?php echo $this->Html->url(array('plugin' => 'role_badge', 'controller' => 'role_badge_plugins', 'action' => 'admin_create')); ?>">
                                    <?php echo __d('role_badge', 'Add New'); ?>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6"></div>
                    </div>
                </div>
                <div class="table-toolbar" style="margin: 0">
                    <div class="row">
                        <div class="col-md-6">
                            <form method="post" action="<?php echo $this->Html->url(array('plugin' => 'role_badge', 'controller' => 'role_badge_plugins', 'action' => 'admin_multi_delete')); ?>" id="deleteForm">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr class="tbl_head">
                                            <th class="text-center" width="50"><?php echo $this->Paginator->sort('id', __d('role_badge', 'ID')); ?></th>
                                            <th><?php echo $this->Paginator->sort('Role.name', __d('role_badge', 'Role')); ?></th>
                                            <th class="text-center"><?php echo __d('role_badge', 'Badge'); ?></th>
                                            <th width="50"><?php echo __d('role_badge', 'Actions'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $count = 0; ?>
                                        <?php foreach ($aRoleBadges as $aRoleBadge): ?>
                                            <tr class="gradeX <?php echo (++$count % 2 ? "odd" : "even") ?>" id="<?php echo $aRoleBadge['RoleBadge']['id']; ?>">
                                                <td class="text-center"><?php echo $aRoleBadge['RoleBadge']['id']; ?></td>
                                                <td>
                                                    <?php
                                                        $this->MooPopup->tag(array(
                                                            'href'=>$this->Html->url(array(
                                                                'plugin' => 'role_badge', 
                                                                "controller" => "role_badge_plugins",
                                                                "action" => "admin_create",
                                                                $aRoleBadge['RoleBadge']['id']
                                                            )),
                                                            'title' => '',
                                                            'target' => 'themeModal',
                                                            'innerHtml'=> $aRoleBadge['Role']['name'],
                                                        ));
                                                    ?>
                                                </td>
                                                <td class="text-center">
                                                    <img height="26" src="<?php echo $oRoleBadgeHelper->getImage($aRoleBadge['RoleBadge']['desktop_profile']); ?>">
                                                </td>
                                                <td>
                                                    <?php
                                                        $this->MooPopup->tag(array(
                                                            'href'=>$this->Html->url(array(
                                                                'plugin' => 'role_badge', 
                                                                "controller" => "role_badge_plugins",
                                                                "action" => "admin_create",
                                                                $aRoleBadge['RoleBadge']['id']
                                                            )),
                                                            'title' => '',
                                                            'innerHtml'=> '<i class="icon-edit icon-small"></i>',
                                                            'target' => 'themeModal'
                                                        ));
                                                    ?>
                                                    <a href="javascript:void(0)" onclick="mooConfirm('<?php echo __d('role_badge', 'Are you sure you want to delete this Award? This cannot be undone!'); ?>', '<?php echo $this->Html->url(array('plugin' => 'role_badge', 'controller' => 'role_badge_plugins', 'action' => 'admin_delete', $aRoleBadge['RoleBadge']['id'])); ?>')"><i class="icon-trash icon-small"></i></a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                        <div class="col-md-6"></div>
                    </div>
                </div>
            </div>
                            
            <div class="pagination" style="margin: 0">
		<?php echo $this->Paginator->prev('« ' . __d('role_badge', 'Previous'), null, null, array('class' => 'disabled')); ?>
		<?php echo $this->Paginator->numbers(); ?>
		<?php echo $this->Paginator->next(__d('role_badge', 'Next') . ' »', null, null, array('class' => 'disabled')); ?>
            </div>
        </div>
    </div>
</div>