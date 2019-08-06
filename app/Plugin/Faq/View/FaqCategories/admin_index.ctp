<?php
$faqHelper = MooCore::getInstance()->getHelper('Faq_Faq');
echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));

$this->Html->addCrumb(__d('faq', 'Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('faq', 'FAQ'), '/admin/faq/faqs');
$this->Html->addCrumb(__d('faq', 'FAQ Categories Manager'), array('controller' => 'faq_categories', 'action' => 'admin_index'));

$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array('cmenu' => 'FAQ'));
$this->end();
$this->Paginator->options(array('url' => $data_search));
?>
<?php echo $this->Moo->renderMenu('Faq', __d('faq', 'FAQ Categories')); ?>
<script>
<?php $this->Html->scriptStart(array('inline' => false)); ?>

    $(document).on('hidden.bs.modal', function(e) {
        $(e.target).removeData('bs.modal');
    });
    function save_order()
    {
            var list = {};
    $('input[name="data[order]" ]').each(function(index, value) {
    list[$(value).data('id')] = $(value).val();
            })
        jQuery.post("<?php echo $this->request->base ?>/admin/faq/faq_categories/save_order/", {cate: list}, function(data) {
            window.location = data;
        });
    }
    function changeVisiable(id, e) {

        var value = 0;
        if ($(e).hasClass('faq_no'))
        {
            value = 1;
        }
        $.post("<?php echo $this->request->base ?>/admin/faq/faq_categories/visiable", {'id': id, 'value': value}, function(data) {
            var json = $.parseJSON(data);
            if (json.result == 1)
            {
                $(e).attr('class', '');
                if (value)
                {
                    $(e).addClass('faq_yes');
                    $(e).attr('title', '<?php echo __d('faq', 'yes'); ?>');
                }
                else
                {
                    $(e).addClass('faq_no');
                    $(e).attr('title', '<?php echo __d('faq', 'no'); ?>');
                }
            }
        });
    }
<?php $this->Html->scriptEnd(); ?>
</script>
<?php
echo $this->Html->script(array(
    'vendor/jquery.fileuploader',
    //'jquery-ui', 
    'footable'), array('inline' => false));
?>

<div class="portlet-body">
    <div class="table-toolbar">
        <div class="row">
            <div class="col-md-6">
                <div class="btn-group">
                    <button class="btn btn-gray" data-toggle="modal" data-target="#ajax" href="<?php echo $this->request->base ?>/admin/faq/faq_categories/create_category">
                        <?php echo __d('faq', 'Add New Category'); ?>
                    </button>
                    <a style="margin-left: 10px" onclick="save_order()" class="btn btn-gray" >
                        <?php echo __d('faq', 'Save order'); ?>
                    </a>
                </div>
                <div class="btn-group">
                    <?php if ($data_search['parent_id'] != 0): ?>
                        <a class="btn btn-gray" href="<?php echo $this->Html->url(array("controller" => "faq_categories", "action" => "admin_index", "plugin" => "faq")) ?>"><?php echo __d('faq', "Back") ?></a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-6">    
            </div>
        </div>

    </div>
    <table class="table table-striped table-bordered table-hover" id="sample_1">
        <thead>
            <tr class="tbl_head">
                <th><?php echo $this->Paginator->sort('id', __d('faq', 'ID')); ?></th>
                <th><?php echo __d('faq', 'Icon'); ?></th>
                <th><?php echo $this->Paginator->sort('name', __d('faq', 'Name')); ?></th>
                <th><?php echo $this->Paginator->sort('parent_id', __d('faq', 'Parent')); ?></th>
                <th><?php echo $this->Paginator->sort('active', __d('faq', 'Active')); ?></th>
                <th><?php echo $this->Paginator->sort('count', __d('faq', 'Count')); ?></th>
                <th width="50px"><?php echo $this->Paginator->sort('order', __d('faq', 'Order')); ?></th>
                <th width="50px"><?php echo __d('faq', 'Actions'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($categories)): ?>
                <?php
                $count = 0;
                foreach ($categories as $faqRs):
                    ?>
                    <tr class="gradeX <?php (++$count % 2 ? "odd" : "even") ?>" id="<?php echo $faqRs['FaqHelpCategorie']['id'] ?>">
                        <td width="50px"><?php echo $faqRs['FaqHelpCategorie']['id'] ?></td>
                        <td width="50px" class="">
                        	<?php $thumb = $faqHelper->getImage($faqRs);?>
                            <?php if($thumb): ?>
                            <img width="50" height="50" src="<?php echo $thumb;?>" />
                            <?php endif; ?>
                        </td>
                        <td class="reorder">
                            <?php
                            $this->MooPopup->tag(array(
                                'href' => $this->Html->url(array("controller" => "faq_categories",
                                    "action" => "create_category",
                                    "plugin" => "faq",
                                    $faqRs['FaqHelpCategorie']['id'],
                                    '',
                                )),
                                'title' => $faqRs['FaqHelpCategorie']['name'],
                                'innerHtml' => $faqRs['FaqHelpCategorie']['name'],
                                'target' => 'ajax'
                            ));
                            ?>
                        </td>
                        <td class="reorder"> 
                            <?php $category_parent = $faqHelper->getCateParent($faqRs['FaqHelpCategorie']['id']); ?>
                            <?php echo $category_parent['name']; ?>
                        </td>

                        <td>
                            <?php
                            if ($faqRs['FaqHelpCategorie']['active']) {
                                ?>
                                <span onclick="changeVisiable(<?php echo $faqRs['FaqHelpCategorie']['id']; ?>, this);" class="faq_yes" title="<?php echo __d('faq', 'yes'); ?>">&nbsp</span>
                                <?php
                            } else {
                                ?>
                                <span onclick="changeVisiable(<?php echo $faqRs['FaqHelpCategorie']['id']; ?>, this);" class="faq_no" title="<?php echo __d('faq', 'no'); ?>">&nbsp</span>
                                <?php
                            }
                            ?>						
                        </td>
                        <td class="reorder"> <?php echo $faqRs['FaqHelpCategorie']['count'] ?></td>
                        <td width="50px"><input data-id="<?php echo $faqRs['FaqHelpCategorie']['id'] ?>" type="text" name="data[order]" value="<?php echo $faqRs['FaqHelpCategorie']['order'] ?>" /> </td> 
                        <td width="50px">
                            <?php if ($faqHelper->checkCateHaveChild($faqRs['FaqHelpCategorie']['id'])): ?>
                                <a href="<?php echo $this->request->base ?>/admin/faq/faq_categories/index/parent_id:<?php echo $faqRs['FaqHelpCategorie']['id'] ?>"><?php echo __d('faq', "Manage Sub") ?></a>
                            <?php else: ?>
                                <a href="javascript:void(0)" onclick="mooConfirm('<?php echo __d('faq', 'Are you sure you want to delete this category?') ?>', '<?php echo $this->request->base ?>/admin/faq/faq_categories/delete_category/<?php echo $faqRs['FaqHelpCategorie']['id'] ?>')"><i class="icon-trash icon-small"></i></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8"> <?php echo __d('faq', 'There are no category found!') ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php echo $this->Paginator->first(__d('faq', 'First')); ?>&nbsp;
        <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->prev(__d('faq', 'Prev')) : ''; ?>&nbsp;
        <?php echo $this->Paginator->numbers(); ?>&nbsp;
        <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->next(__d('faq', 'Next')) : ''; ?>&nbsp;
        <?php echo $this->Paginator->last(__d('faq', 'Last')); ?>
    </div>
</div>

<style>
    .faq_yes
    {
        background-image: url('<?php echo $this->request->base ?>/faq/img/yes.png');
        width: 16px;
        height: 16px;
        display: block;
        cursor: pointer;
    }
    .faq_no
    {
        background-image: url('<?php echo $this->request->base ?>/faq/img/no.png');
        width: 16px;
        height: 16px;
        display: block;
        cursor: pointer;
    }
</style>