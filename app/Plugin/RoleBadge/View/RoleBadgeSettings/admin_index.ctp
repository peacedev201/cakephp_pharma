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
$this->Html->addCrumb(__d('role_badge', 'Settings'), array('controller' => 'role_badge_settings', 'action' => 'admin_index'));

$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array("cmenu" => "User Badges"));
$this->end();
?>

<div class="portlet-body form">
    <div class=" portlet-tabs">
        <div class="tabbable tabbable-custom boxless tabbable-reversed">
            <?php echo $this->Moo->renderMenu('RoleBadge', __d('role_badge', 'Settings')); ?>
            <div class="row" style="padding-top: 10px;">
                <div class="col-md-12">
                    <div class="tab-content">
                        <div class="tab-pane active" id="portlet_tab1">
                            <?php echo $this->element('admin/setting'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>