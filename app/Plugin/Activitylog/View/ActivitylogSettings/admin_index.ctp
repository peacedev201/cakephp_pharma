<?php
__d('activitylog', 'Activity Log Enabled');

$this->Html->addCrumb(__d('activitylog', 'Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('activitylog', 'Activity Log Settings'), array('controller' => 'activitylog_settings', 'action' => 'admin_index'));

$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array("cmenu" => "Activity log"));
$this->end();
?>

<div class="portlet-body form">
    <div class=" portlet-tabs">
        <div class="tabbable tabbable-custom boxless tabbable-reversed">
            <?php echo $this->Moo->renderMenu('Activitylog', __d('activitylog', 'Settings')); ?>
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