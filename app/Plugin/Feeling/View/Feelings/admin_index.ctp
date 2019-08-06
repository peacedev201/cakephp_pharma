<?php
echo $this->Html->css(array( 'fineuploader' ));
echo $this->Html->script(array(
    'vendor/jquery.fileuploader'
    //'footable'
), array('inline' => false));
?>

<?php
    echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
    echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));

//Trung
$this->Html->addCrumb(__d('feeling','Plugins Manager'), '/admin/plugins');
if(!empty($pCategoryId)){
    $this->Html->addCrumb(__d('feeling','Feeling Categories'), array('controller' => 'feeling_categories', 'action' => 'admin_index', $pCategoryId));
    $this->Html->addCrumb(__d('feeling','Feeling Status'), array('controller' => 'feelings', 'action' => 'admin_index', $pCategoryId));
}else{
    $this->Html->addCrumb(__d('feeling','Feeling Status'), array('controller' => 'feelings', 'action' => 'admin_index'));
}


    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Feeling'));
    $this->end();
?>
<?php //echo$this->Moo->renderMenu('Feeling', 'General');?>

<?php $this->Paginator->options(array('url' => $this->passedArgs)); ?>

    <script>
        <?php $this->Html->scriptStart(array('inline' => false)); ?>
        $(document).on('loaded.bs.modal', function (e) {
            Metronic.init();
        });
        $(document).on('hidden.bs.modal', function (e) {
            $(e.target).removeData('bs.modal');
        });

        function save_order()
        {
            var list = {};
            $('input[name="data[order]" ]').each(function(index, value) {
                list[$(value).data('id')] = $(value).val();
            });
            jQuery.post("<?php echo $this->Html->url(array('plugin' => 'feeling', 'controller' => 'feelings', 'action' => 'admin_save_order')); ?>", {cate: list}, function(data) {
                window.location = data;
            });
        }

        function changeVisiable(id, e) {
            var value = 0;
            if ($(e).hasClass('bg_feeling_no')) {
                value = 1;
            }

            $.post("<?php echo $this->Html->url(array('plugin' => 'feeling', 'controller' => 'feelings', 'action' => 'admin_visiable')); ?>", {
                'id': id,
                'value': value
            }, function (data) {
                var json = $.parseJSON(data);
                if (json.result == 1) {
                    $(e).attr('class', '');
                    if (value) {
                        $(e).addClass('bg_feeling_yes');
                        $(e).attr('title', '<?php echo __d('feeling', 'yes'); ?>');
                    }
                    else {
                        $(e).addClass('bg_feeling_no');
                        $(e).attr('title', '<?php echo __d('feeling', 'no'); ?>');
                    }
                }
            });
        }
        <?php $this->Html->scriptEnd(); ?>
    </script>

<?php $oFeelingHelper = MooCore::getInstance()->getHelper('Feeling_Feeling'); ?>

<div class="portlet-body form">
    <div class="portlet-tabs">
        <div class="tabbable tabbable-custom boxless tabbable-reversed ">
            <?php echo $this->Moo->renderMenu('Feeling', __d('feeling', 'Feeling Status'));?>
            <div class="portlet-body" style="margin-top: 10px;">
                <div class="table-toolbar">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="btn-group">
                                <button class="btn btn-gray" onclick="confirmSubmitForm('<?php echo __d('feeling', 'Are you sure you want to delete these feelings?'); ?>', 'deleteForm')">
                                    <?php echo __d('feeling', 'Delete'); ?>
                                </button>
                            </div>
                            <div class="btn-group">
                                <button class="btn btn-gray" onclick="save_order()">
                                    <?php echo __d('feeling', 'Save order'); ?>
                                </button>
                            </div>
                            <div class="btn-group">
                                <?php if(!empty($pCategoryId)): ?>
                                <button class="btn btn-gray" data-backdrop="true" data-toggle="modal" data-target="#ajax" href="<?php echo $this->Html->url(array('plugin' => 'feeling', 'controller' => 'feelings', 'action' => 'admin_create', 0, $pCategoryId)); ?>">
                                    <?php echo __d('feeling', 'Add New'); ?>
                                </button>
                                <?php else: ?>
                                <button class="btn btn-gray" data-backdrop="true" data-toggle="modal" data-target="#ajax" href="<?php echo $this->Html->url(array('plugin' => 'feeling', 'controller' => 'feelings', 'action' => 'admin_create')); ?>">
                                    <?php echo __d('feeling', 'Add New'); ?>
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6"></div>
                    </div>
                </div>
                <div class="table-toolbar" style="margin: 0">
                    <div class="row">
                        <div class="col-md-6">
                            <form method="post" action="<?php echo $this->Html->url(array('plugin' => 'feeling', 'controller' => 'feelings', 'action' => 'admin_multi_delete', $pCategoryId)); ?>" id="deleteForm">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                    <tr class="tbl_head">
                                        <?php if ($cuser['Role']['is_super']): ?>
                                            <th width="30"><input type="checkbox" onclick="toggleCheckboxes2(this)"></th>
                                        <?php endif; ?>
                                        <th width="30"><?php echo $this->Paginator->sort('id', __d('feeling', 'ID')); ?></th>
                                        <th class="text-center"><?php echo __d('feeling', 'Image'); ?></th>
                                        <th class="text-center"><?php echo __d('feeling', 'Status'); ?></th>
                                        <th class="text-center"><?php echo __d('feeling', 'Group'); ?></th>
                                        <th class="text-center"><?php echo $this->Paginator->sort('active', __d('feeling', 'Active')); ?></th>
                                        <th width="50px"><?php echo $this->Paginator->sort('order', __d('feeling', 'Order')); ?></th>
                                        <th width="50"><?php echo __d('feeling', 'Actions'); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $count = 0; ?>
                                    <?php foreach ($aFeelings as $aBSItem): ?>
                                        <tr class="gradeX <?php echo (++$count % 2 ? "odd" : "even") ?>" id="<?php echo $aBSItem['Feeling']['id']; ?>">
                                            <?php if ($cuser['Role']['is_super']): ?>
                                                <td class="text-center" style="vertical-align: middle;"><input type="checkbox" name="feelingIds[]" value="<?php echo $aBSItem['Feeling']['id']; ?>" class="check"></td>
                                            <?php endif; ?>
                                            <td class="text-center" style="vertical-align: middle;"><?php echo $aBSItem['Feeling']['id']; ?></td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                <img src="<?php echo $oFeelingHelper->getFeelingImage($aBSItem, array('prefix' => '32_square')); ?>">
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                <?php echo $aBSItem['Feeling']['label']; ?>
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                <?php echo $oFeelingHelper->getCategoryLabelTranslation($aBSItem['Feeling']['category_id']); ?>
                                            </td>
                                            <td class="text-center" style="vertical-align: middle;">
                                                <?php
                                                if ($aBSItem['Feeling']['active']) {
                                                    ?>
                                                    <span onclick="changeVisiable(<?php echo $aBSItem['Feeling']['id']; ?>, this);" class="bg_feeling_yes" title="<?php echo __d('feeling', 'yes'); ?>">&nbsp</span>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <span onclick="changeVisiable(<?php echo $aBSItem['Feeling']['id']; ?>, this);" class="bg_feeling_no" title="<?php echo __d('feeling', 'no'); ?>">&nbsp</span>
                                                    <?php
                                                }
                                                ?>
                                            </td>

                                            <td class="text-center" style="vertical-align: middle;" width="50px">
                                                <input data-id="<?php echo $aBSItem['Feeling']['id'] ?>" type="text" name="data[order]" value="<?php echo $aBSItem['Feeling']['order'] ?>" />
                                            </td>

                                            <td class="text-center" style="vertical-align: middle;">
                                                <?php if(!empty($pCategoryId)): ?>
                                                    <?php
                                                    $this->MooPopup->tag(array(
                                                        'href'=>$this->Html->url(array(
                                                            'plugin' => 'feeling',
                                                            "controller" => "feelings",
                                                            "action" => "admin_create",
                                                            $aBSItem['Feeling']['id'],
                                                            $pCategoryId
                                                        )),
                                                        'title' => '',
                                                        'innerHtml'=> '<i class="icon-edit icon-small"></i>',
                                                        'target' => 'ajax'
                                                    ));
                                                    ?>
                                                    <a href="javascript:void(0)" onclick="mooConfirm('<?php echo __d('feeling', 'Are you sure you want to delete this feeling Status?'); ?>', '<?php echo $this->Html->url(array('plugin' => 'feeling', 'controller' => 'feelings', 'action' => 'admin_delete', $aBSItem['Feeling']['id'], $pCategoryId)); ?>')"><i class="icon-trash icon-small"></i></a>
                                                <?php else: ?>
                                                    <?php
                                                    $this->MooPopup->tag(array(
                                                        'href'=>$this->Html->url(array(
                                                            'plugin' => 'feeling',
                                                            "controller" => "feelings",
                                                            "action" => "admin_create",
                                                            $aBSItem['Feeling']['id']
                                                        )),
                                                        'title' => '',
                                                        'innerHtml'=> '<i class="icon-edit icon-small"></i>',
                                                        'target' => 'ajax'
                                                    ));
                                                    ?>
                                                    <a href="javascript:void(0)" onclick="mooConfirm('<?php echo __d('feeling', 'Are you sure you want to delete this feeling Status?'); ?>', '<?php echo $this->Html->url(array('plugin' => 'feeling', 'controller' => 'feelings', 'action' => 'admin_delete', $aBSItem['Feeling']['id'])); ?>')"><i class="icon-trash icon-small"></i></a>
                                                <?php endif; ?>

                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                        <div class="col-md-6"></div>
                    </div>
                </div>
            </div>

            <div class="pagination" style="margin: 0">
                <?php echo $this->Paginator->prev('« ' . __d('feeling', 'Previous'), null, null, array('class' => 'disabled')); ?>
                <?php echo $this->Paginator->numbers(); ?>
                <?php echo $this->Paginator->next(__d('feeling', 'Next') . ' »', null, null, array('class' => 'disabled')); ?>
            </div>
        </div>
    </div>
</div>
