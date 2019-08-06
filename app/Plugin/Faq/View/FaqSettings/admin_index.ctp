<?php
__d('faq','Create/Edit Faq');
__d('faq','View Faq');
__d('faq','Faq Enabled');
__d('faq','Item per pages');
__d('faq','Allow users to create poll with option "Allow voters to add new answer"');
__d('faq','Item per pages');

echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));

$this->Html->addCrumb(__d('faq','Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('faq', 'FAQ'), '/admin/faq/faqs');
$this->Html->addCrumb(__d('faq','FAQ Settings'), array('controller' => 'faq_settings', 'action' => 'admin_index'));

$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array("cmenu" => "FAQ"));
$this->end();
?>

<div class="portlet-body form">
    <div class=" portlet-tabs">
        <div class="tabbable tabbable-custom boxless tabbable-reversed">
            <?php echo $this->Moo->renderMenu('Faq', __d('faq','FAQ Settings'));?>
            <div class="row" style="padding-top: 10px;">
                <div class="col-md-12">
                    <div class="tab-content">
                        <div class="tab-pane active" id="portlet_tab1">
                            <?php echo $this->element('Faq./adminsetting/setting');?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>