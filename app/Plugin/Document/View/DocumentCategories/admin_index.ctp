<?php
$this->Html->addCrumb(__d('document','Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('document', 'Document'), '/admin/document/documents');
$this->Html->addCrumb(__d('document','Document Categories'), array('controller' => 'document_categories', 'action' => 'admin_index'));
$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array('cmenu' => 'Document'));
$this->end();
?>
<?php echo $this->Moo->renderMenu('Document', __d('document','Categories'));?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
jQuery(document).ready(function(){
    jQuery( "#category_list" ).sortable( {
        items: "tr:not(.tbl_head)",
        handle: ".reorder",
        update: function(event, ui) {
        var list = jQuery('#category_list').sortable('toArray');
        jQuery.post('<?php echo $this->request->base?>/admin/categories/ajax_reorder', { cats: list });
        }
    });
});
function save_order()
{
    var list={};
    $('input[name="data[weight]"]').each(function(index,value){
        list[$(value).data('id')] = $(value).val();
    })
    jQuery.post("<?php echo $this->request->base?>/admin/categories/save_order/",{cats:list},function(data){
        window.location = data;
    });
}
<?php $this->Html->scriptEnd(); ?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
$(document).on('loaded.bs.modal', function (e) {
    Metronic.init();
});
$(document).on('hidden.bs.modal', function (e) {
    $(e.target).removeData('bs.modal');
});

<?php $this->Html->scriptEnd(); ?>
<div class="portlet-body">
    <div class="table-toolbar">
        <div class="row">
            <div class="col-md-6">
                <div class="btn-group">
                    <button class="btn btn-gray" data-toggle="modal" data-target="#ajax" href="<?php echo $this->request->base?>/admin/document/document_categories/create/">
                        <?php echo __d('document','Add New');?>
                    </button>
                    <a style="margin-left: 10px" onclick="save_order()" class="btn btn-gray" >
                        <?php echo __d('document','Save order');?>
                    </a>
                </div>
            </div>
            <div class="col-md-6">

            </div>
        </div>
    </div>
    <table class="table table-striped table-bordered table-hover" id="category_list">
        <thead>
        <tr class="tbl_head">
            <th width="50px"><?php echo __d('document','ID');?></th>
            <th width="100px"><?php echo __d('document','Name');?></th>
            <th width="50px"><?php echo __d('document','Order');?></th>            
            <th width="50px" data-hide="phone"><?php echo __d('document','Active');?></th>
            <th width="50px" data-hide="phone"><?php echo __d('document','Count');?></th>
            <th width="50px" data-hide="phone"><?php echo __d('document','Actions');?></th>
        </tr>
        </thead>
        <tbody>

        <?php $count = 0;
        foreach ($categories as $category): ?>
            <tr class="gradeX <?php (++$count % 2 ? "odd" : "even") ?>" id="<?php echo $category['Category']['id']?>">
                <td><?php echo $category['Category']['id']?></td>
                <td class="reorder"><a href="<?php echo $this->request->base?>/admin/document/document_categories/create/<?php echo $category['Category']['id']?>" data-toggle="modal" data-target="#ajax" title="<?php echo $category['Category']['name']?>"><?php if ($category['Category']['header']):?><strong><?php echo $category['Category']['name']?></strong><?php else:?><?php echo $category['Category']['name']?><?php endif;?></a></td>
                <td class="reorder"><input data-id="<?php echo $category['Category']['id']?>" style="width:50px" type="text" name="data[weight]" value="<?php echo $category['Category']['weight']?>" /> </td>                                
                <td class="reorder"><?php echo ($category['Category']['active']) ? __d('document','Yes') : __d('document','No')?></td>
                <td class="reorder"><?php echo $category['Category']['item_count']?></td>
                <td><a href="javascript:void(0)" onclick="mooConfirm('<?php echo __d('document','Are you sure you want to delete this category? All the items within it will also be deleted. This cannot be undone!');?>', '<?php echo $this->request->base?>/admin/document/document_categories/delete/<?php echo $category['Category']['id']?>')"><i class="icon-trash icon-small"></i></a></td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
</div>
