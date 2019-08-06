<?php
$faqHelper = MooCore::getInstance()->getHelper('Faq_Faq');
echo $this->Html->script(array('admin/layout/scripts/compare.js?' . Configure::read('core.version')), array('inline' => false));
echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));

$this->Html->addCrumb(__d('faq', 'Plugins Manager'), '/admin/plugins');
$this->Html->addCrumb(__d('faq', 'FAQ'), '/admin/faq/faqs');
$this->Html->addCrumb(__d('faq', 'FAQ Manager'), '/admin/faq/faqs');

$this->startIfEmpty('sidebar-menu');
echo $this->element('admin/adminnav', array('cmenu' => 'FAQ'));
$this->end();
$this->Paginator->options(array('url' => $data_search));
?>

<?php $this->Html->scriptStart(array('inline' => false)); ?>

    $(document).ready(function() {
        $('.footable').footable();
    });
    function changeVisiable(id, e)
    {
        var value = 0;
        if ($(e).hasClass('faq_no'))
        {
            value = 1;
        }
        $(e).attr('class', '');
        if (value){
            $(e).addClass('faq_yes');
            $(e).attr('title', '<?php echo __d('faq', 'yes'); ?>');
        }
        else
        {
            $(e).addClass('faq_no');
            $(e).attr('title', '<?php echo __d('faq', 'no'); ?>');
        }
        $.post("<?php echo $this->request->base ?>/admin/faq/faqs/visiable", {'id': id, 'value': value}, function(data) {
        });
    }
    function save_order()
    {
        var list = {};
        $('input[ name="data[order]"]').each(function(index, value) {
            list[$(value).data('id')] = $(value).val();
        })
		console.log(list);
        jQuery.post("<?php echo $this->request->base   ?>/admin/faq/faqs/save_order/", {faqs: list}, function(data) {
            window.location = data;
        });
    }

<?php $this->Html->scriptEnd(); ?>
<?php echo $this->Moo->renderMenu('Faq', __d('faq', 'FAQ Manager')); ?>
<div class="portlet-body">
    <div class="table-toolbar">
        <div class="row">
            <div class="col-md-6">
                <div class="btn-group">
                    <a class="btn btn-gray" href="<?php echo $this->base . '/admin/faq/faqs/create'; ?>"><?php echo __d('faq', 'Add FAQ'); ?></a>
                </div>
                <div class="btn-group">
                    <button class="btn btn-gray" id="sample_editable_1_new" onclick="confirmSubmitForm('<?php echo __d('faq', 'Are you sure you want to delete these faqs?') ?>', 'deleteForm')">
                        <?php echo __d('faq', 'Delete'); ?>
                    </button>
                    <a style="margin-left: 10px" onclick="save_order()" class="btn btn-gray" >
                        <?php echo __d('faq', 'Save order'); ?>
                    </a>
                </div>
            </div>
            <div class="col-md-6">
                <div id="sample_1_filter" class="dataTables_filter">
                    <label>
                        <form method="post" action="<?php echo $this->base . '/admin/faq/faqs'; ?>">
                            <?php echo $this->Form->text('title', array('class' => 'form-control input-medium input-inline', 'placeholder' => __d('faq', 'Search by title or category name'))); ?>
                            <?php echo $this->Form->submit('', array('style' => 'display:none')); ?>
                        </form>
                    </label>
                </div>
            </div>
        </div>
    </div>
    <form method="post" action="<?php echo $this->request->base ?>/admin/faq/faqs/delete" id="deleteForm">
        <?php echo $this->Form->hidden('category'); ?>
        <table class="table table-striped table-bordered table-hover" id="sample_1">
            <thead>
                <tr>
                    <?php if ($cuser['Role']['is_super']): ?>
                        <th width="30"><input type="checkbox" onclick="toggleCheckboxes2(this)"></th>
                    <?php endif; ?>
                    <th><?php echo $this->Paginator->sort('id', __d('faq', 'ID')); ?></th>
                    <th><?php echo $this->Paginator->sort('title', __d('faq', 'Title')); ?></th>
                    <th><?php echo $this->Paginator->sort('privacy', __d('faq', 'Enable')); ?></th>
                    <th><?php echo $this->Paginator->sort('permisson', __d('faq', 'Permisson')); ?></th>
                    <th><?php echo $this->Paginator->sort('per_userful', __d('faq', '% Userful')); ?></th>
                    <th><?php echo $this->Paginator->sort('category_id', __d('faq', 'Category')); ?></th>
                    <th width="50px"><?php echo $this->Paginator->sort('order', __d('faq', 'Order')); ?></th>
                    <th><?php echo $this->Paginator->sort('modified', __d('faq', 'Updated date')); ?></th>
                    <th><?php echo __d('faq', 'Action'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($faqs)): ?>
                    <?php
                    $count = 0;
                    foreach ($faqs as $faq):
                        ?>
                        <tr class="gradeX <?php (++$count % 2 ? "odd" : "even") ?>">
                            <?php if ($cuser['Role']['is_super']): ?>
                                <td><input type="checkbox" name="faq[]" value="<?php echo $faq['Faq']['id'] ?>" class="check"></td>
                            <?php endif; ?>
                            <td><?php echo $faq['Faq']['id'] ?></td>
                            <td>
                                <a href="<?php echo $this->request->base; ?>/admin/faq/faqs/create/<?php echo $faq['Faq']['id']; ?>" target="_blank"><?php echo $faq['Faq']['title']; ?></a>
                            </td>
                            <td>
                                <?php
                                if ($faq['Faq']['active']) {
                                    ?>
                                    <span onclick="changeVisiable(<?php echo $faq['Faq']['id']; ?>, this);" class="faq_yes" title="<?php echo __d('faq', 'yes'); ?>">&nbsp</span>
                                    <?php
                                } else {
                                    ?>
                                    <span onclick="changeVisiable(<?php echo $faq['Faq']['id']; ?>, this);" class="faq_no" title="<?php echo __d('faq', 'no'); ?>">&nbsp</span>
                                    <?php
                                }
                                ?>						
                            </td>
                            <td>
                                <?php
                                $role_id_arr = explode(",", $faq['Faq']['permission']);
                                $display = '';
                                foreach ($roles as $role) {
                                    if (in_array($role['Role']['id'], explode(',', $faq['Faq']['permission']))) {
                                        $display = $display . $role['Role']['name'] . " ,";
                                    }
                                }
                                $display = substr($display, 0, count($display) - 2);
                                echo $display;
                                ?>
                            </td>
                            <td><?php echo $faq['Faq']['per_usefull'] ?></td>
                            <td>
                                <?php $category = $faqHelper->getCategory($faq['Faq']['category_id'],false,$faq['Faq']['locale']); ?>
                                <?php echo $category['FaqHelpCategorie']['name'] ?>
                            </td>
                            <td width="50px"><input data-id="<?php echo $faq['Faq']['id'] ?>" type="text" name="data[order]" value="<?php echo $faq['Faq']['order'] ?>" /> </td> 
                            <td><?php echo $this->Moo->getTime($faq['Faq']['modified']); ?></td>
                            <td>	
                                <a href="<?php echo $this->request->base; ?>/admin/faq/faqs/create/<?php echo $faq["Faq"]["id"] ?>" class="tip" title="<?php echo __d('faq', 'Edit'); ?>"><i class="icon-edit icon-small"></i></a>				
                                <a href="javascript:void(0)" class="tip" title="<?php echo __d('faq', 'Delete'); ?>" onclick="mooConfirm('<?php echo __d('faq', 'Are you sure you want to delete this faq?'); ?>', '<?php echo $this->request->base; ?>/admin/faq/faqs/delete/<?php echo $faq["Faq"]["id"] ?>')"><i class="icon-trash icon-small"></i></a>				
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                        <tr>
                            <td colspan="10"><?php  echo __d('faq','There are no faq found!') ?></td>
                        </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </form>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        <select class="form-control" onchange="doModeration(this.value, 'faqs')">
                            <option value=""><?php echo __d('faq', 'With selected...') ?></option>
                            <option value="delete"><?php echo __d('faq', 'Delete') ?></option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="pagination pull-right">
                <?php echo $this->Paginator->prev('« ' . __d('faq', 'Previous'), null, null, array('class' => 'disabled')); ?>
                <?php echo $this->Paginator->numbers(); ?>
                <?php echo $this->Paginator->next(__d('faq', 'Next') . ' »', null, null, array('class' => 'disabled')); ?>
            </div>
        </div>
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