<?php if(Configure::read('Store.store_enabled')):?>
    <?php $this->Html->scriptStart(array(
        'inline' => false, 
        'domReady' => true, 
        'requires' => array('jquery', 'store_store', 'store_slick_slider', 'store_jquery_ui'), 
        'object' => array('$', 'store_store')
    ));?>
        store_store.loadProductDescription(<?php echo $product['id'];?>, '');
    <?php $this->Html->scriptEnd(); ?> 
    <div class="box2 main-container page-shop" style="width: auto">
        <div class="content_center">
            <div class="page-content">
                <div class="container page-view-detail">
                    <div class="product-view">
                        <div class="post-103 product type-product status-publish has-post-thumbnail product_cat-accessories product_cat-bags product_cat-bands product_cat-blazers product_cat-blazers-bags product_cat-books product_cat-bootees-bags product_cat-clothing product_cat-clothing-notebooks product_cat-coats product_cat-coats-clothing product_cat-coats-clothing-notebooks product_cat-cocktail product_cat-day product_cat-dresses product_cat-evening product_cat-furniture product_cat-handbags product_cat-jackets product_cat-jeans product_cat-kids product_cat-laptop product_cat-lingerie product_cat-notebooks product_cat-run product_cat-sandals product_cat-shoes product_cat-sports product_cat-sports-shoes product_cat-t-shirts product_cat-t-shirts-clothing-notebooks product_cat-table product_cat-watches jsn-master featured shipping-taxable purchasable product-type-simple product-cat-accessories product-cat-bags product-cat-bands product-cat-blazers product-cat-blazers-bags product-cat-books product-cat-bootees-bags product-cat-clothing product-cat-clothing-notebooks product-cat-coats product-cat-coats-clothing product-cat-coats-clothing-notebooks product-cat-cocktail product-cat-day product-cat-dresses product-cat-evening product-cat-furniture product-cat-handbags product-cat-jackets product-cat-jeans product-cat-kids product-cat-laptop product-cat-lingerie product-cat-notebooks product-cat-run product-cat-sandals product-cat-shoes product-cat-sports product-cat-sports-shoes product-cat-t-shirts product-cat-t-shirts-clothing-notebooks product-cat-table product-cat-watches instock" id="product-103" itemtype="http://schema.org/Product" itemscope="">
                            <div class="store_plugin-tabs" id="tabs">
                                <?php echo $this->Form->hidden('default_tab_value', array(
                                    'value' => !empty($this->request->query['tab']) ? $this->request->query['tab'] : 0
                                ));?>
                                <ul class="tabs">
                                    <li class="description_tab active">
                                        <a href="#tab-description"><?php echo __d('store', 'Description');?></a>
                                    </li>
                                    <li class="discussion_tab">
                                        <a href="#tab-discussion"><?php echo __d('store', 'Discussions');?></a>
                                    </li>
                                    <li class="videos_tab">
                                        <a href="#tab-videos"><?php echo __d('store', 'Video');?></a>
                                    </li>
                                    <li class="videos_tab">
                                        <a href="#tab-reviews">
                                            <?php echo __d('store', 'Reviews');?>
                                            (<span id="review_count"><?php echo $product['rating_count'];?></span>)
                                        </a>
                                    </li>
                                    <li class="policy_tab">
                                        <a href="#tab-policy"><?php echo __d('store', 'Store Policies');?></a>
                                    </li>
                                </ul>
                                <div id="tab-description" <?php if(!empty($product['article'])):?>class="panel entry-content"<?php endif;?> style="display: block;">
                                    <?php echo $product['article'];?>
                                </div>
                                <div id="tab-discussion" class="panel entry-content" style="display: none;">
                                    <div id="reviews">
                                        <?php echo $this->renderComment();?>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                                <div id="tab-videos" class="panel entry-content text-center" style="display: none;">
                                    <?php echo $this->Element('Store.list/videos_list', array(
                                            'product_id' => $product['id'],
                                            'page' => 1
                                    ));?> 
                                </div>
                                <div id="tab-reviews" class="panel entry-content text-center" style="display: none;">
                                    <?php echo $this->Element('Store.review', array(
                                            'product_id' => $product['id'],
                                            //'is_reviewed' => $is_reviewed
                                    ));?>
                                </div>
                                <div id="tab-policy" class="panel" style="display: none;">
                                    <?php echo $store['policy'];?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif;?>