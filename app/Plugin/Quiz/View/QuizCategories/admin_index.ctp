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
$this->Html->addCrumb(__d('quiz', 'Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('quiz', 'Quiz Categories'), array('controller' => 'quiz_categories', 'action' => 'admin_index'));

$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array("cmenu" => "Quiz"));
$this->end();
?>

<?php echo $this->Moo->renderMenu('Quiz', __d('quiz', 'Categories')); ?>

<?php $this->Html->scriptStart(array('inline' => false)); ?>
jQuery(document).ready(function(){
    jQuery("#category_list").sortable({
        items: "tr:not(.tbl_head)",
        handle: ".reorder",
        update: function(event, ui) {
            var list = jQuery('#category_list').sortable('toArray');
            jQuery.post('<?php echo $this->request->base . '/admin/categories/ajax_reorder'; ?>', {cats: list});
        }
    });
});

function save_order(){
    var list={};
    $('input[name="data[weight]"]').each(function(index,value){
        list[$(value).data('id')] = $(value).val();
    })
    jQuery.post('<?php echo $this->request->base . '/admin/categories/save_order'; ?>', {cats:list}, function(data){
        window.location = data;
    });
}
<?php $this->Html->scriptEnd(); ?>

<?php $this->Html->scriptStart(array('inline' => false)); ?>
$(document).on('loaded.bs.modal', function(e) {
    Metronic.init();
});
$(document).on('hidden.bs.modal', function(e) {
    $(e.target).removeData('bs.modal');
});
<?php $this->Html->scriptEnd(); ?>

<div class="portlet-body">
    <div class="table-toolbar">
        <div class="row">
            <div class="col-md-6">
                <div class="btn-group">
                    <a class="btn btn-gray" data-toggle="modal" data-target="#ajax" href="<?php echo $this->request->base . '/admin/quiz/quiz_categories/create'; ?>">
                        <?php echo __d('quiz', 'Add New'); ?>
                    </a>
                    <a class="btn btn-gray" onclick="save_order()" style="margin-left: 10px">
                        <?php echo __d('quiz', 'Save order'); ?>
                    </a>
                </div>
            </div>
            <div class="col-md-6">
            </div>
        </div>
    </div>
    <div class="row" style="padding-top: 10px;">
        <div class="col-md-6">
            <div class="tab-content">
                <div class="tab-pane active">
                    <table class="table table-striped table-bordered table-hover" id="category_list">
                        <thead>
                        <tr class="tbl_head">
                            <th width="50px"><?php echo __d('quiz', 'ID'); ?></th>
                            <th><?php echo __d('quiz', 'Name'); ?></th>
                            <th width="50px"><?php echo __d('quiz', 'Order'); ?></th>
                            <th><?php echo __d('quiz', 'Parent');?></th>
                            <th width="50px"><?php echo __d('quiz', 'Header');?></th>
                            <th width="50px"><?php echo __d('quiz', 'Active'); ?></th>
                            <th width="50px"><?php echo __d('quiz', 'Count'); ?></th>
                            <th width="50px"><?php echo __d('quiz', 'Actions'); ?></th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php $count = 0;
                        foreach ($aCategories as $aCategory): ?>
                            <tr class="gradeX <?php echo (++$count % 2 ? "odd" : "even"); ?>" id="<?php echo $aCategory['Category']['id']; ?>">
                                <td><?php echo $aCategory['Category']['id']; ?></td>
                                <td class="reorder"><a href="<?php echo $this->request->base . '/admin/quiz/quiz_categories/create/' . $aCategory['Category']['id']; ?>" data-toggle="modal" data-target="#ajax" title="<?php echo $aCategory['Category']['name']; ?>"><?php if ($aCategory['Category']['header']): ?><strong><?php echo $aCategory['Category']['name']; ?></strong><?php else: ?><?php echo $aCategory['Category']['name']; ?><?php endif; ?></a></td>
                                <td class="reorder"><input class="text-center" data-id="<?php echo $aCategory['Category']['id']; ?>" style="width:50px" type="text" name="data[weight]" value="<?php echo $aCategory['Category']['weight']; ?>"/> </td>                                
                                <td class="reorder"><?php echo (!empty($aCategory['Parent']['name'])) ? $aCategory['Parent']['name'] : __d('quiz', 'ROOT'); ?></td>
                                <td class="reorder text-center"><?php echo ($aCategory['Category']['header']) ? __d('quiz', 'Yes') : __d('quiz', 'No'); ?></td>
                                <td class="reorder text-center"><?php echo ($aCategory['Category']['active']) ? __d('quiz', 'Yes') : __d('quiz', 'No'); ?></td>
                                <td class="reorder text-center"><?php echo $aCategory['Category']['item_count']; ?></td>
                                
                                <?php if($aCategory['Category']['header']): ?>
                                <td class="text-center"><a href="javascript:void(0)" onclick="mooConfirm('<?php echo addslashes(__d('quiz', 'Are you sure you want to delete this category? All parent of sub-category it will also be changed to ROOT. This cannot be undone!'));?>', '<?php echo $this->request->base . '/admin/categories/delete/' . $aCategory['Category']['id']; ?>')"><i class="icon-trash icon-small"></i></a></td>
                                <?php else: ?>
                                <td class="text-center"><a href="javascript:void(0)" onclick="mooConfirm('<?php echo addslashes(__d('quiz', 'Are you sure you want to delete this category? All the items within it will also be deleted. This cannot be undone!'));?>', '<?php echo $this->request->base . '/admin/categories/delete/' . $aCategory['Category']['id']; ?>')"><i class="icon-trash icon-small"></i></a></td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>