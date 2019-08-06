<?php
__d('poll','Create/Edit Poll');
__d('poll','View Poll');
__d('poll','Poll Enabled');
__d('poll','Item per pages');
__d('poll','Allow users to create poll with option "Allow voters to add new answer"');
__d('poll','Min answer when create poll');
__d('poll','Allow users to create poll with option "Show on feed"');
__d('poll','By pass force login');

echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));

$this->Html->addCrumb(__d('poll','Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('poll', 'Poll'), '/admin/poll/polls');
$this->Html->addCrumb(__d('poll','Poll Settings'), array('controller' => 'poll_settings', 'action' => 'admin_index'));

$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array("cmenu" => "Poll"));
$this->end();
?>

<div class="portlet-body form">
    <div class=" portlet-tabs">
        <div class="tabbable tabbable-custom boxless tabbable-reversed">
            <?php echo $this->Moo->renderMenu('Poll', __d('poll','Settings'));?>
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