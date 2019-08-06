<?php  
$this->Html->addCrumb(__d('contest','Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('contest', 'Contest Categories'), '/admin/contest/contest_categories');
echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));
$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array('cmenu' => 'Contests'));
$this->end();
?>
<?php echo  $this->Moo->renderMenu('Contest', __d('contest','Categories')); ?>

<?php
$this->Paginator->options(array('url' => $this->passedArgs));
?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
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

$(document).on('hidden.bs.modal', function (e) {
    $(e.target).removeData('bs.modal');
});
<?php $this->Html->scriptEnd(); ?>
<div class="portlet-body">
    <div class="table-toolbar">
        <div class="row">
            <div class="col-md-6">
                <div class="btn-group">
                    <button class="btn btn-gray" data-toggle="modal" data-target="#ajax" href="<?php echo  $this->request->base ?>/admin/contest/contest_categories/create">
                        <?php echo __d('contest', 'Add New');?>
                    </button>
                    <a style="margin-left: 10px" onclick="save_order()" class="btn btn-gray" >
                        <?php echo __d('contest', 'Save order');?>
                    </a>
                </div>
            </div>
            <div class="col-md-6">    
            </div>
        </div>
        <div class="row">
            <div class="col-md-12" style="padding-top: 5px;">
                <div class="note note-info hide">
                    <p>
                        <?php echo __d('contest', 'You can enable Spam Challenge to force user to answer a challenge question in order to register.')?> <br/>
                        <?php echo __d('contest', 'To enable this feature, click System Settings -> Security -> Enable Spam Challenge');?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <table class="table table-striped table-bordered table-hover" id="sample_1">
        <thead>
            <tr class="tbl_head">
                <th width="50px"><?php echo __d('contest', 'ID');?></th>
                <th><?php echo __d('contest', 'Name');?></th>
                <th width="50px"><?php echo __d('contest', 'Order');?></th>
                <th width="50px"><?php echo __d('contest', 'Type');?></th>
                <th data-hide="phone"><?php echo __d('contest', 'Parent');?></th>
                <th width="50px" data-hide="phone"><?php echo __d('contest', 'Header');?></th>
                <th width="50px" data-hide="phone"><?php echo __d('contest', 'Active');?></th>
                <th width="50px" data-hide="phone"><?php echo __d('contest', 'Count');?></th>
                <th width="50px" data-hide="phone"><?php echo __d('contest', 'Actions');?></th>
            </tr>
        </thead>
        <tbody>
            <?php $count = 0;
            foreach ($categories as $category):
                ?>
                <tr class="gradeX <?php ( ++$count % 2 ? "odd" : "even") ?>" id="<?php echo  $category['Category']['id'] ?>">
                    <td width="50px"><?php echo  $category['Category']['id'] ?></td>
                    <td class="reorder">
                        <?php
                        $this->MooPopup->tag(array(
                               'href'=>$this->Html->url(array("controller" => "categories",
                                                              "action" => "ajax_create",
                                                              "plugin" => false,
                                                              $category['Category']['id'],
                                                              'Contest',
                                                          )),
                               'title' => $category['Category']['name'],
                               'innerHtml'=> ($category['Category']['header']) ? "<strong>" . $category['Category']['name'] . "</strong>" : $category['Category']['name'],
                               'target' => 'ajax'
                        )); 
                        ?>
                       </td>
                    <td width="50px" class="reorder"><input data-id="<?php echo $category['Category']['id']?>" style="width:50px" type="text" name="data[weight]" value="<?php echo $category['Category']['weight']?>" /> </td>
                    <td width="50px" class="reorder"><?php echo  $category['Category']['type'] ?></td>
                    <td class="reorder"><?php if (!empty($category['Parent']['name'])) echo $category['Parent']['name'];
                    else echo __d('contest','ROOT'); ?></td>
                    <td width="50px" class="reorder"><?php echo  ($category['Category']['header']) ? __d('contest','Yes') : __d('contest','No') ?></td>
                    <td width="50px" class="reorder"><?php echo  ($category['Category']['active']) ? __d('contest','Yes') : __d('contest','No') ?></td>
                    <td width="50px" class="reorder"><?php echo  $category['Category']['item_count'] ?></td>
                    <?php if($category['Category']['header']): ?>
                        <td width="50px"><a href="javascript:void(0)" onclick="mooConfirm('<?php echo addslashes(__d('contest', 'Are you sure you want to delete this category? All parent of sub-category it will also be changed to ROOT. This cannot be undone!'));?>', '<?php echo $this->request->base?>/admin/categories/delete/<?php echo $category['Category']['id']?>')"><i class="icon-trash icon-small"></i></a></td>
                    <?php else: ?>
                        <td width="50px"><a href="javascript:void(0)" onclick="mooConfirm('<?php echo addslashes(__d('contest', 'Are you sure you want to delete this category? All the items within it will also be deleted. This cannot be undone!'));?>', '<?php echo $this->request->base?>/admin/categories/delete/<?php echo $category['Category']['id']?>')"><i class="icon-trash icon-small"></i></a></td>
                    <?php endif; ?>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>
