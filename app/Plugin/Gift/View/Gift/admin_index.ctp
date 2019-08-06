<?php
    $giftHelper = MooCore::getInstance()->getHelper('Gift_Gift');
    $this->Html->addCrumb(__d('gift', 'Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('gift', 'Manage Gifts'), array('plugin' => 'gift', 'controller' => 'gifts'));
    echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
    echo $this->Html->script(array('jquery-ui', 'footable', 'Gift.admin'), array('inline' => false));
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Gift'));
    $this->end();
?>
<?php echo $this->Moo->renderMenu('Gift', __d('gift','Manage Gifts'));?>
<?php echo $this->Html->css(array('Gift.gift-admin.css' )); ?>
<style type="text/css">
    .pagination > li.current.paginate_button ,
    .pagination > li.disabled {
        position: relative;
        float: left;
        padding: 6px 12px;
        margin-left: -1px;
        line-height: 1.42857143;
        color: #428bca;
        text-decoration: none;
        background-color: #eee;
        border: 1px solid #ddd;
    }
</style>
<div class="portlet-body">
    <div class="table-toolbar">
        <form id="searchForm" method="get" action="<?php echo $admin_url;?>">
            <div class="row">
                <div class="col-md-6"></div>
                <div class="col-md-3">
                    <?php echo $this->Form->input("keyword", array(
                        'div' => false,
                        'label' => false,
                        'class' => 'form-control',
                        'placeholder' => __d('gift', 'Keyword'),
                        'name' => 'keyword',
                        'value' => $keyword
                    ));?>
                </div>
                <div class="col-md-1">
                    <button class="btn btn-gray" type="submit"><?php echo __d('gift', "Search");?></button>
                </div>
                <div class="col-md-2">
                    <a class="btn btn-gray pull-right" href="<?php echo $admin_url;?>create"><?php echo __d('gift', "Create New Gift");?></a>
                </div>
                <div class="clear"></div>
            </div>
        </form>
    </div>
    <?php if(!empty($gifts)):?>
        <form class="form-horizontal" id="adminForm" method="post" action="<?php echo $admin_url?>delete">
            <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover" id="sample_1">
                <thead>
                    <tr>
                        <th style="width: 7px">
                            <?php echo $this->Form->checkbox('', array(
                                'hiddenField' => false,
                                'div' => false,
                                'label' => false,
                                'onclick' => 'toggleCheckboxes2(this)'
                            ));?>
                        </th>
                        <th style="width: 4%">
                            <?php echo $this->Paginator->sort('id', __d('gift',  'ID')); ?>
                        </th>
                        <th style="width: 40px">
                            <?php echo __d('gift',  'Photo'); ?>
                        </th>
                        <th>
                            <?php echo $this->Paginator->sort('title', __d('gift',  'Title')); ?>
                        </th>
                        <th style="width: 10%">
                            <?php echo $this->Paginator->sort('type', __d('gift',  'Type')); ?>
                        </th>
                        <th style="width: 10%">
                            <?php echo __d('gift',  'Category'); ?>
                        </th>
                        <th style="width: 8%">
                            <?php echo $this->Paginator->sort('price', __d('gift',  'Cost')); ?>
                        </th>
                        <th style="width: 12%">
                            <?php echo __d('gift',  'Creation Date') ?>
                        </th>
                        <th style="width: 10%" class="text-center">
                            <?php echo __d('gift',  'Options') ?>
                        </th>
                    </tr>
                </thead>
                <tbody>

                <?php 
                    $count = 0;
                     $giftHelper = MooCore::getInstance()->getHelper('Gift_Gift');
                    foreach ($gifts as $gift): 
                        $gift_category = $gift['GiftCategory'];
                        $gift = $gift['Gift'];
                ?>
                    <tr class="gradeX <?php (++$count % 2 ? "odd" : "even") ?>">
                        <td style="text-align: center">
                            <input type="checkbox" value="<?php echo $gift['id']?>" class="check" id="cb<?php echo $gift['id']?>" name="data[cid][]">
                        </td>
                        <td>
                            <?php echo $gift['id']?>
                        </td>
                        <td>
                            <div style="width: 40px;height: 40px;overflow: hidden">
                                <img style="width: 40px;" src="<?php echo $giftHelper->getImage($gift, array('prefix' => 'thumb'))?>" />
                            </div>
                        </td>
                        <td>
                            <a href="<?php echo $this->request->base."/admin/gift/gift/create/".$gift['id'];?>">
                                <?php echo $this->Text->truncate(h($gift['title']), 100, array('eclipse' => '...')) ?>
                            </a>
                        </td>
                        <td>
                            <?php echo $gift['type']?>
                        </td>
                        <td>
                            <?php echo h($gift_category['name']);?>
                        </td>
                        <td>
                            <?php echo h($gift['price']);?>
                        </td>
                        <td>
                            <?php echo date('M d Y', strtotime($gift['created']));?>
                        </td>
                        <td style="text-align: center">
                            <?php if ( $gift['enable'] ): ?>
                                <a href="<?php echo $admin_url.'do_active/'.$gift['id']?>"><i class="fa fa-check-square-o " title="<?php echo __d('gift',  'Disable') ?>"></i></a>&nbsp;
                            <?php else: ?>
                                <a href="<?php echo $admin_url.'do_active/'.$gift['id']?>/1"><i class="fa fa-times-circle" title="<?php echo __d('gift',  'Enable') ?>"></i></a>&nbsp;
                            <?php endif; ?>
                            &nbsp;|
                            <a href="javascript:void(0)" class="tip" title="<?php echo __d('gift',  'Delete') ?>" onclick="mooConfirm('<?php echo __d('gift',  'Are you sure you want to delete this gift?') ?>', '<?php echo $admin_url.'delete/'.$gift['id']?>')">
                                <i class="icon-trash icon-small"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach ?>

                </tbody>
            </table>
            </div>
        </form>
        <div class="table-toolbar">
            <div class="row">
                <div class="col-md-6">
                    <div class="btn-group">
                        <button onclick="confirmSubmitForm('<?php echo __d('gift', 'Are you sure you want to delete?');?>', 'adminForm')" id="sample_editable_1_new" class="btn btn-gray">
                            <?php echo __d('gift',  'Delete'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3">
            </div>
            <div class="col-sm-9">
                <div id="dataTables-example_paginate" class="dataTables_paginate paging_simple_numbers">
                    <ul class="pagination">
                        <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->first(__d('gift','First'), array('class' => 'paginate_button previous', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                        <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->prev(__d('gift','Previous'), array('class' => 'paginate_button previous', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                        <?php echo $this->Paginator->numbers(array('class' => 'paginate_button', 'tag' => 'li', 'separator' => '')); ?>
                        <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->next(__d('gift','Next'), array('class' => 'paginate_button next', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                        <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->last(__d('gift','Last'), array('class' => 'paginate_button next', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                    </ul>
                </div>
            </div>
        </div>

    <?php else:?>
        <?php echo __d('gift', "No Gifts");?>
    <?php endif;?>
</div>
