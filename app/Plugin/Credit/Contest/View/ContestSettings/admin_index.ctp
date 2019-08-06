<?php
__d('contest','Enable Contest Hashtag');
__d('contest','Auto Approved Contest');
__d('contest','Contests');
__d('contest','Item per pages');
__d('contest','The number of contests to display per page');
__d('contest','Item per pages');
__d('contest','Create/Edit Contest');
__d('contest','View Contest');

echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));

$this->Html->addCrumb(__d('contest','Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('contest','Contest Settings'), array('controller' => 'contest_settings', 'action' => 'admin_index'));

$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array("cmenu" => "Contests"));
$this->end();
?>

<div class="portlet-body form">
    <div class=" portlet-tabs">
        <div class="tabbable tabbable-custom boxless tabbable-reversed">
            <?php echo $this->Moo->renderMenu('Contest', __d('contest','Settings'));?>
            <div class="row" style="padding-top: 10px;">
                <div class="col-md-12">
                    <div class="tab-content">
                        <div class="tab-pane active" id="portlet_tab1">
                            <?php echo $this->element('admin/setting');?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>