<?php
$this->Html->addCrumb(__d('document','Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('document', 'Document'), '/admin/document/documents');
$this->Html->addCrumb(__d('document','Document Licenses'), array('controller' => 'document_licenses', 'action' => 'admin_index'));
$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array('cmenu' => 'Document'));
$this->end();
?>
<?php echo $this->Moo->renderMenu('Document', __d('document','License Manager'));?>

<?php $this->Html->scriptStart(array('inline' => false)); ?>
$(document).on('hidden.bs.modal', function (e) {
    $(e.target).removeData('bs.modal');
});
<?php $this->Html->scriptEnd(); ?>
<div class="portlet-body">
    <div class="table-toolbar">
        <div class="row">
            <div class="col-md-6">
                <div class="btn-group">
                    <button class="btn btn-gray" data-toggle="modal" data-target="#ajax" href="<?php echo $this->request->base?>/admin/document/document_licenses/create/">
                        <?php echo __d('document','Add New');?>
                    </button>                  
                </div>
            </div>
            <div class="col-md-6">
            </div>
        </div>
    </div>
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr class="tbl_head">
            <th width="50px"><?php echo __d('document','ID');?></th>
            <th><?php echo __d('document','Title');?></th>
            <th><?php echo __d('document','Url');?></th>
            <th><?php echo __d('document','Name');?></th>
            <th width="50px"><?php echo __d('document','Number Item');?></th>
            <th width="50px"><?php echo __d('document','Actions');?></th>
        </tr>
        </thead>
        <tbody>

        <?php $count = 0;
        foreach ($licenses as $license): ?>
            <tr class="gradeX <?php (++$count % 2 ? "odd" : "even") ?>">
                <td><?php echo $license['DocumentLicense']['id']?></td>
                <td><a href="<?php echo $this->request->base?>/admin/document/document_licenses/create/<?php echo $license['DocumentLicense']['id']?>" data-toggle="modal" data-target="#ajax" title="<?php echo $license['DocumentLicense']['title']?>"><?php echo $license['DocumentLicense']['title'];?></a></td>
                <td><?php echo $license['DocumentLicense']['url']?></td>
                <td><?php echo $license['DocumentLicense']['name']?></td>                                                      
                <td class="reorder"><?php echo $license['DocumentLicense']['item_count']?></td>
                <td><a href="javascript:void(0)" onclick="mooConfirm('<?php echo __d('document','Are you sure you want to delete this license?');?>', '<?php echo $this->request->base?>/admin/document/document_licenses/delete/<?php echo $license['DocumentLicense']['id']?>')"><i class="icon-trash icon-small"></i></a></td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
</div>
