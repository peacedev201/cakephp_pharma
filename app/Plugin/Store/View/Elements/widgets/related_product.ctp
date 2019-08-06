<?php if(Configure::read('Store.store_enabled')):?>
    <?php if(!empty($relatedProducts)):?> 
        <?php $this->Html->scriptStart(array(
            'inline' => false, 
            'domReady' => true, 
            'requires' => array('jquery', 'store_store', 'store_slick_slider'), 
            'object' => array('$', 'store_store')
        ));?>
            store_store.initRelatedProducts();
        <?php $this->Html->scriptEnd(); ?> 
        <div class="box2 main-container page-shop" style="width: auto">
            <div class="product-view product-view-widget">
                <div class="widget related_products_widget">
                    <h3 class="widget-title"><span style="display:inline"><?php echo  __d('store', 'Related Products'); ?></span></h3>
                    <div class="related products">
                        <div class="shop-products row grid-view">
                            <?php foreach($relatedProducts as $product):
                                $productImage = !empty($product['StoreProductImage'][0]) ? $product['StoreProductImage'][0] : null;
                                $product = $product['StoreProduct'];
                            ?>
                            <div class="relatedPD first last  item-col col-xs-12 col-sm-4 col-md-4  product type-product status-publish has-post-thumbnail product_cat-accessories product_cat-laptop product_cat-notebooks product_cat-watches jsn-master sale featured shipping-taxable purchasable product-type-simple product-cat-accessories product-cat-laptop product-cat-notebooks product-cat-watches instock jsn-bootstrap3">
                                <div class="product-wrapper">
                                    <?php if($product['allow_promotion']):?>
                                    <span class="onsale">
                                        <span class="sale-bg"></span>
                                        <span class="sale-text"><?php echo __d('store', 'Sale');?></span>
                                    </span>				
                                    <?php endif;?>
                                    <div class="list-col4">
                                        <div class="product-image">
                                            <?php if($product['featured']):?>
                                            <div class="featured_product">
                                                <span><?php echo __d('store', 'Featured');?></span>
                                            </div>
                                            <?php endif;?>
                                            <a href="<?php echo $product['moo_href'];?>" title="<?php echo $product['name'];?>">
                                                <img width="300" height="300" src="<?php echo $this->Store->getProductImage($productImage, array('prefix' => PRODUCT_PHOTO_THUMB_WIDTH));?>" class="primary_image wp-post-image" alt="06_2" />					
                                                <span class="shadow"></span>
                                            </a>
                                            <a class="quick-view quickview" href="javascript:void(0)" title="<?php echo $product['name'];?>" data-link="<?php echo $product['moo_href'].'/1';?>">
                                                <?php echo __d('store', 'Quick View');?>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="list-col8">
                                        <div class="gridview">
                                            <h2 class="product-name">
                                                <a href="<?php echo $product['moo_href'];?>">
                                                    <?php echo $product['name'];?>
                                                </a>
                                            </h2>
                                            <span class="review_star">
                                                <input readonly value="<?php echo $product['rating']; ?>" type="number" class="rating form-control hide">
                                            </span>
                                            <div class="price-box">
                                                <?php if($product['allow_promotion']):?>
                                                <span class="old-price">
                                                    <span class="amount"><?php echo $this->Store->formatMoney($product['old_price']);?></span>
                                                </span>
                                                <?php endif;?>
                                                <span class="special-price">
                                                    <span class="amount"><?php echo $this->Store->formatMoney($product['new_price']);?></span>
                                                </span>
                                            </div>
                                            <div class="actions">
                                                <div class="action-buttons store-product-action">
                                                    <div class="add-to-cart">
                                                        <p class="product store_plugin add_to_cart_inline " style="border:4px solid #ccc; padding: 12px;">
                                                            <?php if($product['product_type'] == STORE_PRODUCT_TYPE_DIGITAL && $this->Store->isBoughtDigitalProduct($product['id'])):?> 
                                                                <a class="button add_to_cart_button product_type_simple" href="<?php echo $this->request->base.'/stores/products/download_product/'.$product['id'];?>">
                                                                    <?php echo __d('store', 'Download');?>
                                                                </a>
                                                            <?php elseif($product['product_type'] == STORE_PRODUCT_TYPE_LINK && $this->Store->isBoughtDigitalProduct($product['id'])):?>  
                                                                <a class="button add_to_cart_button product_type_simple" href="<?php echo $this->request->base.'/stores/products/download_product/'.$product['id'];?>" target="_blank">
                                                                    <?php echo __d('store', 'View Link');?>
                                                                </a>
                                                            <?php elseif($product['out_of_stock']):?>
                                                            <a class="button add_to_cart_button product_type_simple" href="<?php echo $product['moo_href'];?>" data-placement="top" data-toggle="tooltip" data-original-title="<?php echo __d('store', 'Out of stock');?>">
                                                                <?php echo __d('store', 'Out of stock');?>
                                                            </a>
                                                            <?php elseif($product['attribute_to_buy'] == 1):?>
                                                            <a class="button add_to_cart_button product_type_simple" href="<?php echo $product['moo_href'];?>" data-placement="top" data-toggle="tooltip" data-original-title="<?php echo __d('store', 'Go to buy');?>">
                                                                <?php echo __d('store', 'Go to buy');?>
                                                            </a>
                                                            <?php else:?>
                                                            <a class="button add_to_cart_button product_type_simple <?php if($product['product_type'] != STORE_PRODUCT_TYPE_REGULAR &&  $this->Store->isProductInCart($product['id'])):?>active<?php else:?>add_to_cat<?php endif;?> cart_product_<?php echo $product['id'];?>" href="javascript:void(0)" data-id="<?php echo $product['id'];?>" data-type="<?php echo $product['product_type'];?>">
                                                                <?php echo ($product['product_type'] != STORE_PRODUCT_TYPE_REGULAR &&  $this->Store->isProductInCart($product['id'])) ? __d('store', 'Added to cart') : __d('store', 'Add to cart'); ?>
                                                            </a>
                                                            <?php endif;?>
                                                        </p>						
                                                    </div>
                                                    <div class="add-to-links">
                                                        <?php if($product['in_wishlist']):?>
                                                            <a class="add_to_wishlist active product_wishlist_<?php echo $product['id'];?>" data-action="0" data-placement="top" data-toggle="tooltip" data-original-title="<?php echo __d('store', 'Remove from wishlist');?>" href="javascript:void(0)" data-id="<?php echo $product['id'];?>">
                                                                <i class="material-icons">favorite</i>
                                                            </a>
                                                        <?php else:?>
                                                            <a class="add_to_wishlist product_wishlist_<?php echo $product['id'];?>" data-action="1" data-placement="top" data-toggle="tooltip" data-original-title="<?php echo __d('store', 'Add to wishlist');?>" href="javascript:void(0)" data-id="<?php echo $product['id'];?>">
                                                                <i class="material-icons">favorite</i>
                                                            </a>
                                                        <?php endif;?>
                                                    </div>
                                                    <div class="shareproduct">
                                                        <?php if($product['is_liked']):?>
                                                            <a href="javascript:void(0)" class="active product_like_<?php echo $product['id'];?> like_product" data-id="<?php echo $product['id'];?>" data-action="0" data-text="<?php echo __d('store', 'Like This Product');?>" data-placement="top" data-toggle="tooltip" data-original-title="<?php echo __d('store', 'You Liked This Product');?>">
                                                                <i class="material-icons">thumb_up</i>
                                                            </a>
                                                        <?php else:?>
                                                            <a href="javascript:void(0)" class="product_like_<?php echo $product['id'];?> like_product" data-id="<?php echo $product['id'];?>" data-action="1" data-text="<?php echo __d('store', 'You Liked This Product');?>" data-placement="top" data-toggle="tooltip" data-original-title="<?php echo __d('store', 'Like This Product');?>">
                                                                <i class="material-icons">thumb_up</i>
                                                            </a>
                                                        <?php endif;?>
                                                    </div>	
                                                    <?php if($product['allow_share']):?>
                                                    <div class="shareproduct">
                                                        <?php if(!$is_app):?>
                                                            <a href="javascript:void(0)" class="shareFeedBtn" share-url="<?php echo $product['url_share'];?>" data-placement="top" data-toggle="tooltip" data-original-title="<?php echo __d('store', 'Share To News Feed');?>">
                                                                <i class="material-icons">share</i>
                                                            </a>
                                                        <?php else:?>
                                                            <div class="list_option">
                                                                <div class="dropdown">
                                                                    <a href="javascript:void(0)" class="list_option_button" id="product_detail_<?php echo $product["id"] ?>">
                                                                        <i class="material-icons">share</i>
                                                                    </a>
                                                                    <ul class="mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect" for="product_detail_<?php echo $product["id"] ?>">
                                                                        <?php echo $this->element('share/menu', array(
                                                                            'param' => 'Store_Store_Product',
                                                                            'action' => 'product_item_detail',
                                                                            'id' => $product['id']
                                                                        ));?>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        <?php endif;?>
                                                    </div>	
                                                    <?php endif;?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                            <?php endforeach;?> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif;?>
<?php endif;?>
