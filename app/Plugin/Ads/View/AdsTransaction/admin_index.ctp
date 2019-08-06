<?php
    $this->Html->addCrumb(__d('ads','Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('ads','Transactions'), array('controller' => 'ads_transaction'));
    echo $this->Html->css(array('jquery-ui', 'footable.core.min'), null, array('inline' => false));
    echo $this->Html->script(array('jquery-ui', 'footable'), array('inline' => false));
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Ads'));
    $this->end();
    __d('ads','pending');
    __d('ads','failed');
    __d('ads','canceled');
    __d('ads','completed');
?>
<?php echo$this->Moo->renderMenu('Ads', __d('ads','Transactions'));?>

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
            <div class="form-group">
                <div class="col-md-8"></div>
                <div class="col-md-3">
                    <?php echo $this->Form->input("keyword", array(
                        'div' => false,
                        'label' => false,
                        'class' => 'form-control',
                        'placeholder' => __d('ads', 'Keyword'),
                        'name' => 'keyword',
                        'value' => $keyword
                    ));?>
                </div>
                <div class="col-md-1">
                    <button class="btn btn-gray" type="submit"><?php echo __d('ads', "Search");?></button>
                </div>
                <div class="clear"></div>
            </div>
        </form>
    </div>
    <?php if(!empty($ads_transactions)):?>
        <form class="form-horizontal" id="adminForm" method="post" action="<?php echo $admin_url?>delete">
            <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover" id="sample_1">
                <thead>
                    <tr>
                        <th style="width: 4%">
                            <?php echo $this->Paginator->sort('id', __d('ads',  'ID')); ?>
                        </th>
                        <th>
                            <?php echo $this->Paginator->sort('AdsCampaign.email', __d('ads',  'Email')); ?>
                        </th>
                        <th style="width: 12%">
                            <?php echo $this->Paginator->sort('AdsCampaign.name', __d('ads',  'Campaign')); ?>
                        </th>
                        <th style="width: 12%">
                            <?php echo $this->Paginator->sort('AdsPlacement.name', __d('ads',  'Placement')); ?>
                        </th>
                        <th style="width: 12%">
                            <?php echo $this->Paginator->sort('status', __d('ads',  'Status')); ?>
                        </th>
                        <th style="width: 12%">
                            <?php echo $this->Paginator->sort('transaction_id', __d('ads',  'Transaction Id')); ?>
                        </th>
						<th style="width: 12%">
                            <?php echo $this->Paginator->sort('type', __d('ads',  'Type')); ?>
                        </th>
                        <th style="width: 8%">
                            <?php echo __d('ads',  'Date'); ?>
                        </th>
                    </tr>
                </thead>
                <tbody>

                <?php 
                    $count = 0;
                    foreach ($ads_transactions as $ads_transaction): 
                        $ads_placement = $ads_transaction['AdsPlacement'];
                        $ads_campaign = $ads_transaction['AdsCampaign'];
                        $ads_transaction = $ads_transaction['AdsTransaction'];
                ?>
                    <tr class="gradeX <?php (++$count % 2 ? "odd" : "even") ?>">
                        <td>
                            <?php echo $ads_transaction['id']?>
                        </td>
                        <td>
                            <?php echo $ads_campaign['email']?>
                        </td>
                        <td>
                            <a href="<?php echo $this->request->base;?>/admin/ads/create/<?php echo $ads_campaign['id'];?>">
                                <?php echo $this->Text->truncate(h($ads_campaign['name']), 100, array('eclipse' => '...')) ?>
                            </a>
                        </td>
                        <td>
                            <a href="<?php echo $this->request->base;?>/admin/ads/ads_placement/create/<?php echo $ads_placement['id'];?>">
                                <?php echo $this->Text->truncate(h($ads_placement['name']), 100, array('eclipse' => '...')) ?>
                            </a>
                        </td>
                        <td>
                            <?php echo __d('ads',$ads_transaction['status']);?>
                        </td>
                        <td>
                            <?php echo $ads_transaction['transaction_id']?>
                        </td>
						 <td>
                            <?php echo $ads_transaction['type']?>
                        </td>
                        <td>
                            <?php echo date('m-d-Y', strtotime($ads_transaction['created']))?>
                        </td>
                    </tr>
                <?php endforeach ?>

                </tbody>
            </table>
            </div>
        </form>
        <div class="row">
            <div class="col-sm-3">
            </div>
            <div class="col-sm-9">
                <div id="dataTables-example_paginate" class="dataTables_paginate paging_simple_numbers">
                    <ul class="pagination">
                        <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->first('First', array('class' => 'paginate_button previous', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                        <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->prev('Previous', array('class' => 'paginate_button previous', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                        <?php echo $this->Paginator->numbers(array('class' => 'paginate_button', 'tag' => 'li', 'separator' => '')); ?>
                        <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->next('Next', array('class' => 'paginate_button next', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                        <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->last('Last', array('class' => 'paginate_button next', 'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                    </ul>
                </div>
            </div>
        </div>

    <?php else:?>
        <?php echo __d('ads', "No Transactions");?>
    <?php endif;?>
</div>
