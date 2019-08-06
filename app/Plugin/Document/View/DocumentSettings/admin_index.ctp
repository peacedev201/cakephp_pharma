<?php
__d('document','Create/Edit Document');
__d('document','View Document');
__d('document','Document Enable');
__d('document','Item per pages');
__d('document','Auto Approval When A Document Added');
__d('document','Enable Document Hashtag');
__d('document','By pass force login');

echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));

$this->Html->addCrumb(__d('document','Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('document', 'Document'), '/admin/document/documents');
$this->Html->addCrumb(__d('document','Document Settings'), array('controller' => 'document_settings', 'action' => 'admin_index'));

$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array("cmenu" => "Document"));
$this->end();
?>

<div class="portlet-body form">
    <div class=" portlet-tabs">
        <div class="tabbable tabbable-custom boxless tabbable-reversed">
            <?php echo $this->Moo->renderMenu('Document', __d('document','Settings'));?>
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