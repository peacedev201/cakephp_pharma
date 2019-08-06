<?php $this->Html->scriptStart(array(
    'inline' => false, 
    'domReady' => true, 
    'requires' => array('jquery', 'store_manager'), 
    'object' => array('$', 'store_manager')
));?>
    store_manager.initManage();
<?php $this->Html->scriptEnd(); ?>
    
<?php $this->setNotEmpty('west');?>
<?php $this->start('west'); ?>
    <?php echo $this->Element('manager_menu'); ?>
<?php $this->end(); ?>
    
<script type="text/template" id="buy_feature_product_confirm">
    <?php echo sprintf(__d('store', 'This feature costs %s for %s day(s). If this product already set as featured, expiration date will be expanded. Are you sure you want to buy?'), $this->Store->formatMoney($package['StorePackage']['price'], null, null, STORE_SHOW_MONEY_TYPE_NORMAL), $package['StorePackage']['period']);?>
</script>
<div class="bar-content">
    <?php echo $this->Element('Store.mobile/mobile_manager_menu'); ?>
    <div class="content_center">
        <ul class="breadcrumb">
            <li>
                <a href="<?php echo STORE_MANAGER_URL;?>">
                    <i class="material-icons">home</i>
                </a>
                <span class="divider"></span>
            </li>
            <li>
                <a href="<?php echo $url;?>">
                    <?php echo __d('store', "Manage Products");?>
                </a>
                <span class="divider-last"></span>
            </li>
        </ul>
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
                                <a title="<?php echo __d('store', "Add New");?>" href="<?php echo $url;?>create">
                                    <?php echo __d('store', "Add New");?>
                                </a>
                            </li>
                            <li>
                                <a title="<?php echo __d('store', "Delete");?>" href="javascript:void(0)" id="delete_all">
                                    <?php echo __d('store', "Delete");?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
            <div class="panel-body">
				<p>
                    <?php echo __d('store', 'You can manage all products from all of your business pages here.');?>
                </p>
                <form id="searchForm" method="get" action="<?php echo $url;?>">
                    <div class="form-group form-search-app">
                        <div class="col-md-1">

                        </div>
                        <div class="col-md-4">
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
                        <div class="col-md-3">
                            <select name="store_category_id" class="form-control">
                                <option value="0"><?php echo __d('store', "All category");?></option>
                                <?php echo $this->Category->outputOptionType($storeCats, 'StoreCategory', array('0'), !empty($search['store_category_id']) ? $search['store_category_id'] : '');?>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <button class="sl-mng-btn btn btn-primary btn-lg" type="submit"><?php echo __d('store', "Search");?></button>
                        </div>
                        <div class="clear"></div>
                    </div>
                </form>
                <?php if($products != null):?>
                <form class="form-horizontal" id="adminForm" method="post" action="<?php echo $url;?>">
                    <div class="div-detail-app manage-product">
                        <div class="div-full-breabcrum">
                            <div class="col-md-1 col-custom-1">
                                <?php echo $this->Form->checkbox('', array(
                                    'hiddenField' => false,
                                    'class' => 'group_checkbox'
                                ));?>
                            </div>
                            <div class="col-md-1 ">
                                <div class="group-group ">
                                    <i class="text-app"><?php echo $this->Paginator->sort('product_code', __d('store', 'Code')); ?></i>
                                </div>
                            </div>
                            <div class="<?php if(Configure::read('Store.store_buy_featured_product')):?>col-md-3<?php else:?>col-md-5<?php endif;?> col-custom-3">
                                <div class="group-group text-left">
                                    <i class="text-app"><?php echo $this->Paginator->sort('name', __d('store', 'Name')); ?></i>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="group-group">
                                    <i class="text-app"><?php echo $this->Paginator->sort('price', __d('store', 'Price')); ?></i>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="group-group">
                                    <i class="text-app"><?php echo __d('store', 'Enable');?></i>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="group-group">
                                    <i class="text-app"><?php echo __d('store', 'Approve');?></i>
                                </div>
                            </div>
                            <?php if(Configure::read('Store.store_buy_featured_product')):?>
                            <div class="col-md-2">
                                <div class="group-group">
                                    <i class="text-app"><?php echo __d('store', 'Featured');?></i>
                                </div>
                            </div>
                            <?php endif;?>
                            <div class="col-md-1">
                                <div class="group-group">
                                    <i class="text-app"></i>
                                </div>
                            </div>
                        </div>
                           <?php foreach($products as $product):
                                $productImage = !empty($product['StoreProductImage'][0]) ? $product['StoreProductImage'][0] : null;
                                $product = $product['StoreProduct'];
                            ?>
                        <div class="div-detail-row ">
                            <div class="top-list-brb">
                                <?php echo __d('store', "Product Listing");?>
                            </div>
                            <div class="col-xs-12 col-md-1 col-custom-1">
                                <div class="group-group">
                                    <i class="visible-sm visible-xs icon-app material-icons">check_circle</i>
                                    <i class="text-app">
                                        <?php echo $this->Form->checkbox('cid.', array(
                                            'hiddenField' => false,
                                            'id' => 'cb'.$product['id'],
                                            'class' => 'multi_cb',
                                            'value' => $product['id']
                                        ));?>
                                    </i>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-1">
                                <div class="group-group">
                                    <i class="visible-sm visible-xs icon-app material-icons">business_center</i>
                                    <i class="text-app"><?php echo $product['product_code'];?></i>
                                </div>
                            </div>
                            <div class="col-xs-12 <?php if(Configure::read('Store.store_buy_featured_product')):?>col-md-3<?php else:?>col-md-5<?php endif;?> col-custom-3">
                                <div class="group-group group-group-name text-left">
                                    <i class="visible-sm visible-xs icon-app material-icons">title</i>
                                    <i class="text-app">
                                        <div class="group-group-img">
                                            <img src="<?php echo $this->Store->getProductImage($productImage, array('prefix' => PRODUCT_PHOTO_TINY_WIDTH));?>" title="<?php echo $product['name'];?>" alt="<?php echo $product['name'];?>" />
                                        </div>
                                        <a href="<?php echo $product['moo_href']?>">
                                            <?php echo  $this->Text->truncate(strip_tags(str_replace(array('<br>','&nbsp;'), array(' ',''), $product['name'])), 80, array('eclipse' => ''));?>
                                        </a>
                                    </i>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-2">
                                <div class="group-group store-price-enhance">
                                    <i class="visible-sm visible-xs icon-app material-icons">monetization_on</i>
                                    <i class="text-app"><?php echo $this->Store->formatMoney($product['price']);?></i>
                                </div>
                            </div>
                            <div class="col-xs-6 col-md-1">
                                <div class="group-group">
                                    <i class="visible-sm visible-xs icon-app material-icons">done_all</i>
                                        <?php if($product['enable']):?>
                                    <a href="javascript:void(0)" class="action_disable" data-id="<?php echo $product['id']?>">
                                        <i class="text-app material-icons" title="<?php echo __d('store', "Disable");?>">done</i>
                                    </a>
                                        <?php else:?> 
                                    <a href="javascript:void(0)" class="action_enable" data-id="<?php echo $product['id']?>">
                                        <i class="text-app material-icons" title="<?php echo __d('store', "Enable");?>">clear</i>
                                    </a>
                                        <?php endif;?>
                                </div>
                            </div>
                            <div class="col-xs-6 col-md-1">
                                <div class="group-group">
                                    <i class="visible-sm visible-xs icon-app material-icons">av_timer</i>
                                    <a>
                                        <?php if($product['approve']):?>
                                            <i class="text-app material-icons" title="<?php echo __d('store', "Your product was approved");?>">done</i>
                                        <?php else:?> 
                                            <i class="text-app material-icons" title="<?php echo __d('store', "Your product is waiting for approval");?>">clear</i>
                                        <?php endif;?>
                                    </a>
                                </div>
                            </div>
                            <?php if(Configure::read('Store.store_buy_featured_product') && $product['approve']):?>
                            <div class="col-xs-12 col-md-2">
                                <div class="group-group">
                                    <i class="visible-sm visible-xs icon-app material-icons">av_timer</i>
                                    <i class="text-app" style="line-height: 1px;">
                                    <?php if($product['featured'] && $product['feature_expiration_date'] != null):?>
                                        <?php echo __d('store', 'Expire').": ".date('Y-m-d', strtotime($product['feature_expiration_date']));?>
                                        <a href="javascript:void(0)" data-id="<?php echo $product['id'];?>" class="buy_featured_product">
                                            <?php echo __d('store', 'Upgrade');?>
                                        </a>
                                    <?php else:?> 
                                        <a href="javascript:void(0)" data-id="<?php echo $product['id'];?>" class="buy_featured_product">
                                            <?php echo __d('store', 'Buy');?>
                                        </a>
                                    <?php endif;?>
                                    </i>
                                </div>
                            </div>
                            <?php elseif(Configure::read('Store.store_buy_featured_product') && !$product['approve']):?>
								<div class="col-xs-12 col-md-2"></div>
                            <?php endif;?>
                             <div class="hidden-xs hidden-sm col-md-1 padding-0 no-border-right">
								<div class="group-group">
									<a class="action-manage" href="<?php echo $url;?>create/<?php echo $product['id'];?>">
										<i class="text-app material-icons" title="<?php echo __d('store', 'Edit');?>">create</i>
									</a>

									<a class="action-manage action_delete" href="javascript:void(0)" data-id="<?php echo $product['id']?>">
										<i class="text-app material-icons" title="<?php echo __d('store', 'Delete');?>">delete_sweep</i>
									</a>
								</div>
                            </div>
                            <div class="visible-sm visible-xs col-xs-6 iconnottext isleft">
                                <a href="<?php echo $url;?>create/<?php echo $product['id'];?>">
                                    <i class="material-icons">create</i>
                                </a>
                            </div>
                            <div class="visible-sm visible-xs col-xs-6 iconnottext">
                                <a class="action_delete" href="javascript:void(0)" data-id="<?php echo $product['id']?>">
                                    <i class="material-icons">delete_sweep</i>                                  
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
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
</div>