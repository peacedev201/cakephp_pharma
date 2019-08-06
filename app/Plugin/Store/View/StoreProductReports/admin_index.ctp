<?php
    echo $this->Html->css(array(
        'jquery-ui', 
        'footable.core.min'), null, array('inline' => false));
    echo $this->Html->script(array(
        'jquery-ui', 
        'footable',
        'Store.admin'), array('inline' => false));
    $this->Html->addCrumb(__d('store',  'Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('store',  'Reports Manager'), array(
        'controller' => 'store_product_reports', 
        'action' => 'admin_index'));
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Stores'));
    $this->end();
?>
<?php echo$this->Moo->renderMenu('Store', __d('store', 'Reports'));?>
<div id="page-wrapper">
	<div class="panel panel-default">
        <div class="panel-heading">
            <?php echo __d('store', "Manage Reports");?>
            <div class="pull-right">
                <div class="btn-group">
                    <button aria-expanded="false" type="button" class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown">
                        <?php echo __d('store', "Actions");?>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu pull-right" role="menu">
                        <li>
                            <a title="<?php echo __d('store', "Enable");?>" href="javascript:void(0)" onclick="jQuery.admin.activeAll('approve')">
                                <?php echo __d('store', "Approve");?>
                            </a> 
                        </li>
                        <li>
                            <a title="<?php echo __d('store', "Disable");?>" href="javascript:void(0)" onclick="jQuery.admin.activeAll('disapprove')">
                                <?php echo __d('store', "Un approve");?>
                            </a> 
                        </li>
                        <li>
                            <a title="<?php echo __d('store', "Delete");?>" href="javascript:void(0)" onclick="jQuery.admin.deleteAll('delete')">
                                <?php echo __d('store', "Delete");?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="clear"></div>
        </div>
        <div class="panel-body">
            <?php if(!empty($reports)):?>
                <form class="form-horizontal" id="adminForm" method="post" action="<?php echo $admin_url?>">
                    <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="sample_1">
                        <thead>
                            <tr>
                                <th style="width: 7px">
                                    <?php echo $this->Form->checkbox('', array(
                                        'hiddenField' => false,
                                        'div' => false,
                                        'label' => false,
                                        'onclick' => 'jQuery.admin.toggleCheckboxes(this)'
                                    ));?>
                                </th>
                                <th>
                                    <?php echo $this->Paginator->sort('name', __d('store',  'Name')); ?>
                                </th>
                                <th style="width: 25%">
                                    <?php echo __d('store',  'Reason'); ?>
                                </th>
                                <th style="width: 12%">
                                    <?php echo $this->Paginator->sort('User.name', __d('store',  'Reporter')); ?>
                                </th>
                                <th style="width: 12%">
                                    <?php echo $this->Paginator->sort('created', __d('store',  'Create Date')); ?>
                                </th>
                                <th style="text-align: center;width: 80px">
                                    <?php echo __d('store',  'Approve');?>
                                </th>
                                <th style="width: 12%"></th>
                            </tr>
                        </thead>
                        <tbody>

                        <?php 
                            $count = 0;
                            foreach ($reports as $report): 
                                $user = $report['User'];
                                $product = $report['StoreProduct'];
                                $report = $report['StoreProductReport'];
                        ?>
                            <tr class="gradeX <?php (++$count % 2 ? "odd" : "even") ?>">
                                <td style="text-align: center">
                                    <input type="checkbox" value="<?php echo $report['id']?>" class="multi_cb" id="cb<?php echo $report['id']?>" name="data[cid][]">
                                </td>
                                <td>
                                    <?php echo $this->Text->truncate(h($product['name']), 100, array('eclipse' => '...')) ?>
                                </td>
                                <td>
                                    <?php echo $report['content']?>
                                </td>
                                <td>
                                    <a href="<?php echo $this->request->base?>/admin/users/edit/<?php echo $user['id']?>">
                                        <?php echo h($user['name'])?>
                                    </a>
                                </td>
                                <td>
                                    <?php echo $this->Time->niceShort($product['created'])?>
                                </td>
                                <td style="text-align: center">
                                    <?php if($product['approve']):?>
                                        <a href="javascript:void(0)" onclick="jQuery.admin.action('<?php echo $report['id'];?>', 'disapprove')">
                                            <i class="fa fa fa-check" title="<?php echo __d('store', "Un approve");?>"></i>
                                        </a>
                                    <?php else:?> 
                                        <a href="javascript:void(0)" onclick="jQuery.admin.action('<?php echo $report['id'];?>', 'approve')">
                                            <i class="fa fa fa-close" title="<?php echo __d('store', "Approve");?>"></i>
                                        </a>
                                    <?php endif;?>
                                </td>
                                <td>
                                    <a href="<?php echo $product['moo_href'];?>" target="_blank">
                                        <?php echo __d('store', "View");?>
                                    </a>
                                    &#124;
                                    <a href="javascript:void(0)" onclick="jQuery.admin.action('<?php echo $report['id'];?>', 'delete')">
                                        <?php echo __d('store', "Delete");?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach ?>

                        </tbody>
                    </table>
                    </div>
                </form>
				<div class="row">
                    <div class="col-sm-3">
                        <?php echo $this->Paginator->counter(array(
                            'separator' => __d('store', ' of a total of ')
                        ));?>
                    </div>
                    <div class="col-sm-9">
                        <div id="dataTables-example_paginate" class="dataTables_paginate paging_simple_numbers">
                            <ul class="pagination">
                                <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->first(__d('store', 'First'), array('class' => 'paginate_button previous', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                                <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->prev(__d('store', 'Previous'), array('class' => 'paginate_button previous', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                                <?php echo $this->Paginator->numbers(array('class' => 'paginate_button', 'tag' => 'li', 'separator' => '')); ?>
                                <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->next(__d('store', 'Next'), array('class' => 'paginate_button next', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                                <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->last(__d('store', 'Last'), array('class' => 'paginate_button next', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                            </ul>
                        </div>
                    </div>
                </div>
				
            <?php else:?>
                <?php echo __d('store', "No Reports");?>
            <?php endif;?>
        </div>
    </div>
</div>
