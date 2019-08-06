<?php

echo $this->Html->css(array(
        'jquery-ui', 
        'footable.core.min'), null, array('inline' => false));
    echo $this->Html->script(array(
        'jquery-ui', 
        'footable',
        'Store.admin'), array('inline' => false));
    $this->Html->addCrumb(__d('store',  'Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('store',  'Products Manager'), array(
        'controller' => 'store_products', 
        'action' => 'admin_index'));
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Stores'));
    $this->end();
?>
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
<?php echo$this->Moo->renderMenu('Store', __d('store', 'Products'));?>
<div id="page-wrapper">
    <div class="panel panel-default">
        <div class="panel-heading">
            <?php echo __d('store', "Manage Products");?>
            <div class="pull-right">
                <div class="btn-group">
                    <button aria-expanded="false" type="button" class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown">
                        <?php echo __d('store', "Actions");?>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu pull-right" role="menu">
                        <li>
                            <a title="<?php echo __d('store', "Approve");?>" href="javascript:void(0)" onclick="jQuery.admin.activeAll('approve')">
                                <?php echo __d('store', "Approve");?>
                            </a> 
                        </li>
                        <li>
                            <a title="<?php echo __d('store', "Disapprove");?>" href="javascript:void(0)" onclick="jQuery.admin.activeAll('disapprove')">
                                <?php echo __d('store', "Disapprove");?>
                            </a> 
                        </li>
                        <li>
                            <a title="<?php echo __d('store', "Featured");?>" href="javascript:void(0)" onclick="jQuery.admin.activeAll('featured')">
                                <?php echo __d('store', "Featured");?>
                            </a> 
                        </li>
                        <li>
                            <a title="<?php echo __d('store', "Unfeatured");?>" href="javascript:void(0)" onclick="jQuery.admin.activeAll('unfeatured')">
                                <?php echo __d('store', "Unfeatured");?>
                            </a> 
                        </li>
                    </ul>
                </div>
            </div>
            <div class="clear"></div>
        </div>
        <div class="panel-body">
            <form id="searchForm" method="get" action="<?php echo $admin_url;?>">
                <div class="form-group">
                    <div class="col-md-3">
                        <?php echo $this->Form->input("keyword", array(
                            'div' => false,
                            'label' => false,
                            'class' => 'form-control',
                            'placeholder' => __d('store', 'Keyword'),
                            'name' => 'keyword',
                            'value' => !empty($search['keyword']) ? $search['keyword'] : ''
                        ));?>
                    </div>
                    <div class="col-md-3">
                        <?php echo $this->Form->input('search_type', array(
                            'options' => array(
                                '1' => __d('store',  "Name"),
                                '2' => __d('store',  "Product code")
                            ), 
                            'class' => 'form-control',
                            'div' => false,
                            'label' => false,
                            'selected' => !empty($search['search_type']) ? $search['search_type'] : '',
                            'name' => 'search_type',
                        ));?>
                    </div>
                    <div class="col-md-2">
                        <?php echo $this->Form->input('search_options', array(
                            'options' => array(
                                '' => __d('store',  "All"),
                                '1' => __d('store',  "Approve"),
                                '2' => __d('store',  "Disapprove"),
                                '3' => __d('store',  "Featured"),
                                '4' => __d('store',  "Unfeatured")
                            ), 
                            'class' => 'form-control',
                            'div' => false,
                            'label' => false,
                            'selected' => !empty($search['search_options']) ? $search['search_options'] : '',
                            'name' => 'search_options',
                        ));?>
                    </div>
                    <div class="col-md-3">
                        <select name="store_category_id" class="form-control">
                            <option value="0"><?php echo __d('store', "All category");?></option>
                            <?php echo $this->Category->outputOptionType($storeCats, 'StoreCategory', array('0'), !empty($search['store_category_id']) ? $search['store_category_id'] : '');?>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-primary btn-lg" type="submit"><?php echo __d('store', "Search");?></button>
                    </div>
                    <div class="clear"></div>
                </div>
            </form>
            <?php if(!empty($products)):?>
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
                                <th style="width: 6%">
                                    <?php echo $this->Paginator->sort('name', __d('store',  'Code')); ?>
                                </th>
                                <th>
                                    <?php echo $this->Paginator->sort('name', __d('store',  'Name')); ?>
                                </th>
                                <th style="width: 20%">
                                    <?php echo $this->Paginator->sort('User.name', __d('store',  'Seller')); ?>
                                </th>
                                <th style="width: 200px">
                                    <?php echo $this->Paginator->sort('created', __d('store',  'Create Date')); ?>
                                </th>
                                <th style="text-align: center;width: 15%">
                                    <?php echo __d('store',  'Featured');?>
                                </th>
                                <th style="text-align: center;width: 80px">
                                    <?php echo __d('store',  'Approve');?>
                                </th>
                                <th style="width: 11%"></th>
                            </tr>
                        </thead>
                        <tbody>

                        <?php 
                            $count = 0;
                            foreach ($products as $product): 
                                $store = $product['Store'];
                                $product = $product['StoreProduct'];
                        ?>
                            <tr class="gradeX <?php (++$count % 2 ? "odd" : "even") ?>">
                                <td style="text-align: center">
                                    <input type="checkbox" value="<?php echo $product['id']?>" class="multi_cb" id="cb<?php echo $product['id']?>" name="data[cid][]">
                                </td>
                                <td>
                                    <?php echo $product['product_code']; ?>
                                </td>
                                <td>
                                    <?php echo $product['name']; ?>
                                </td>
                                <td>
                                    <a href="<?php echo $this->request->base?>/admin/users/edit/<?php echo $store['user_id']?>">
                                        <?php echo h($store['name'])?>
                                    </a>
                                </td>
                                <td>
                                    <?php echo $this->Time->niceShort($product['created'])?>
                                </td>
                                <td style="text-align: center">
                                    <?php if($product['featured']):?>
                                        <a href="javascript:void(0)" onclick="jQuery.admin.action('<?php echo $product['id'];?>', 'unfeatured')">
                                            <i class="fa fa fa-check" title="<?php echo __d('store', "Un featured");?>"></i>
                                        </a>
                                        <?php if($product['unlimited_feature']):?>
                                            (<?php echo __d('store', 'Unlimited') ?>)
                                        <?php else:?>
                                            (<?php echo __d('store', 'Expire').": ".date('M d Y', strtotime($product['feature_expiration_date']));?>)
                                        <?php endif;?>
                                    <?php else:?>
                                        <a href="javascript:void(0)" data-id="<?php echo $product['id'];?>" class="set_feature">
                                            <i class="fa fa fa-close" title="<?php echo __d('store', "Feature");?>"></i>
                                        </a>
                                    <?php endif;?>
                                </td>
                                <td style="text-align: center">
                                    <?php if($product['approve']):?>
                                    <a href="javascript:void(0)" onclick="jQuery.admin.action('<?php echo $product['id'];?>', 'disapprove')">
                                        <i class="fa fa fa-check" title="<?php echo __d('store', "Un approve");?>"></i>
                                    </a>
                                    <?php else:?> 
                                    <a href="javascript:void(0)" onclick="jQuery.admin.action('<?php echo $product['id'];?>', 'approve')">
                                        <i class="fa fa fa-close" title="<?php echo __d('store', "Approve");?>"></i>
                                    </a>
                                    <?php endif;?>
                                </td>
                                <td class="text-center">
                                    <a href="<?php echo $product['moo_href'];?>" target="_blank">
                                        <?php echo __d('store', "View");?>
                                    </a>
                                    &#124;
                                    <a href="javascript:void(0)" onclick="jQuery.admin.action('<?php echo $product['id'];?>', 'delete')">
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
                <?php echo __d('store', "No Products");?>
            <?php endif;?>
        </div>
    </div>
</div>

<?php if($this->request->is('ajax')): ?>
<script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
<?php endif; ?>
    
    //view detail
    jQuery(document).on('click', '.set_feature', function(){
        jQuery.post("<?php echo $admin_url;?>featured_dialog/" + jQuery(this).data('id'), '', function(data){
            jQuery('#storeModal .modal-content').empty().append(data); 
            jQuery('#storeModal').modal();
        });
    })
    
    jQuery(document).on('click', '#setFeatureButton', function(){
        disableButton('setFeatureButton');
        $.post("<?php echo $admin_url;?>save_feature", $("#setFeatureForm").serialize(), function(data){
            var json = $.parseJSON(data);
            if(json.result == 0)
            {
                $("#setFeatureMessage").html(json.message).show();
                enableButton('setFeatureButton');
            }
            else
            {
                location.reload();
            } 
        });
    });
    
<?php if($this->request->is('ajax')): ?>
</script>
<?php else: ?>
<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>