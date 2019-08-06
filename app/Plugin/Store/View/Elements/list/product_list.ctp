<div class="shop-products row <?php echo $view;?>-view">
    <?php if($products != null):?>
        <?php foreach($products as $product):
            $productImage = !empty($product['StoreProductImage'][0]) ? $product['StoreProductImage'][0] : null;
            $product = $product['StoreProduct'];
        ?> 
    <div class="first  item-col col-xs-12 col-sm-4 post-86 product type-product status-publish has-post-thumbnail product_cat-accessories product_cat-laptop product_cat-notebooks product_cat-watches jsn-master sale featured shipping-taxable purchasable product-type-simple product-cat-accessories product-cat-laptop product-cat-notebooks product-cat-watches instock jsn-bootstrap3">
        <div class="product-wrapper">
                <?php if($product['allow_promotion']):?>
            <span class="onsale">
                <span class="sale-bg"></span>
                <span class="sale-text"><?php echo __d('store', 'Sale');?></span>
            </span>	
                <?php endif;?>
            <div class="list-col4 <?php if($view == 'list'):?> col-xs-12 col-sm-4<?php endif;?>">
                <div class="product-image">
                    <?php if($product['featured']):?>
                    <a class="featured_product" href="<?php echo $product['moo_href'];?>">
                        <span><?php echo __d('store', 'Featured');?></span>
                    </a>
                    <?php endif;?>
                    <a title="<?php echo $product['name'];?>" href="<?php echo $product['moo_href'];?>">
                        <img width="300" height="300" alt="<?php echo $product['name'];?>" class="primary_image wp-post-image" src="<?php echo $this->Store->getProductImage($productImage, array('prefix' => PRODUCT_PHOTO_THUMB_WIDTH));?>">					
                        <span class="shadow"></span>
                    </a>
                    <a title="<?php echo $product['name'];?>" href="javascript:void(0)" class="quick-view quickview" data-link="<?php echo $product['moo_href'].'/1';?>">
                            <?php echo __d('store', 'Quick View');?>
                    </a>
                </div>
            </div>
            <div class="list-col8 <?php if($view == 'list'):?> col-xs-12 col-sm-8<?php endif;?>">
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
                            <?php if($this->Store->allowBuyProduct()):?>
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
                            <?php endif;?>
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
                                <a href="javascript:void(0)" class="shareFeedBtn" share-url="<?php echo $product['url_share'];?>" data-placement="top" data-toggle="tooltip" data-original-title="<?php echo __d('store', 'Share To News Feed');?>">
                                    <i class="material-icons">share</i>
                                </a>
                            </div>	
                            <?php endif;?>
                        </div>
                    </div>
                </div>
                <div class="listview">
                    <h2 class="product-name">
                        <a href="<?php echo $product['moo_href'];?>">
                            <?php echo $product['name'];?>
                        </a>
                    </h2>
                    <span class="review_star">
                        <input readonly value="<?php echo $product['rating']; ?>" type="number" class="rating form-control hide">
                    </span>
                    <?php if(!empty($product['brief'])):?>
                    <div class="product-desc">
                        <p><?php echo $product['brief'];?></p>
                    </div>
                    <?php endif;?>
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
                        <div class="action-buttons">
                            <?php if($this->Store->allowBuyProduct()):?>
                            <div class="add-to-cart">
                                <p style="border:4px solid #ccc; padding: 12px;" class="product store_plugin add_to_cart_inline ">
                                    <?php if($product['product_type'] == STORE_PRODUCT_TYPE_DIGITAL && $this->Store->isBoughtDigitalProduct($product['id'])):?> 
                                        <a class="button add_to_cart_button product_type_simple" href="<?php echo $this->request->base.'/stores/products/download_product/'.$product['id'];?>">
                                            <?php echo __d('store', 'Download');?>
                                        </a>
                                    <?php elseif($product['product_type'] == STORE_PRODUCT_TYPE_LINK && $this->Store->isBoughtDigitalProduct($product['id'])):?>  
                                        <a class="button add_to_cart_button product_type_simple" href="<?php echo $this->request->base.'/stores/products/download_product/'.$product['id'];?>" target="_blank">
                                            <?php echo __d('store', 'View Link');?>
                                        </a>
                                    <?php elseif($product['out_of_stock']):?>
                                    <a class="button add_to_cart_button product_type_simple" href="<?php echo $product['moo_href'];?>">
                                        <?php echo __d('store', 'Out of stock');?>
                                    </a>
                                    <?php elseif($product['attribute_to_buy'] == 1):?>
                                    <a class="button add_to_cart_button product_type_simple" href="<?php echo $product['moo_href'];?>">
                                        <?php echo __d('store', 'Go to buy');?>
                                    </a>
                                        <?php else:?>
                                    <a class="button add_to_cart_button product_type_simple <?php if($product['product_type'] != STORE_PRODUCT_TYPE_REGULAR &&  $this->Store->isProductInCart($product['id'])):?>active<?php else:?>add_to_cat<?php endif;?> cart_product_<?php echo $product['id'];?>" href="javascript:void(0)" data-id="<?php echo $product['id'];?>" data-type="<?php echo $product['product_type'];?>">
                                        <?php echo ($product['product_type'] != STORE_PRODUCT_TYPE_REGULAR &&  $this->Store->isProductInCart($product['id'])) ? __d('store', 'Added to cart') : __d('store', 'Add to cart'); ?>
                                    </a>
                                    <?php endif;?>
                                </p>						
                            </div>
                            <?php endif;?>
                            <div class="add-to-links">
                                <div class="yith-wcwl-add-to-wishlist add-to-wishlist-86">
                                    <div style="display:block" class="yith-wcwl-add-button show">
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
                                </div>					
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
                                    <a href="javascript:void(0)" class="shareFeedBtn" share-url="<?php echo $product['url_share'];?>">
                                        <i class="material-icons">share</i>
                                    </a>
                                <?php else:?>
                                    <div class="list_option">
                                        <div class="dropdown">
                                            <a href="javascript:void(0)" class="list_option_button"  data-toggle="dropdown" id="product_detail_<?php echo $product["id"] ?>">
                                                <i class="material-icons">share</i>
                                            </a>
                                            <ul class="dropdown-menu" for="product_detail_<?php echo $product["id"] ?>">
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
                            <div class="count-like-click">
                                <span class="count" id="Store_Store_Product_like_<?php echo $product['id']; ?>">
                                    <?php echo $product['like_count']; ?>
                                </span> <?php echo __d('store', 'people like this'); ?>
                            </div>
                        </div>
                        <div class="clear"></div>
                        
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
        <?php endforeach;?>
    <?php else:?>
        <center style="margin-top:10px"><?php echo __d('store', 'No products');?></center>
    <?php endif;?>
</div>
<?php if($products != null):?>
<div class="toolbar tb-bottom">
    <p class="store_plugin-result-count" id="bottom-paginator-counter">
        <?php echo $this->Paginator->counter(sprintf(__d('store', 'Showing %s–%s of %s results'), '{:start}', '{:end}', '{:count}'));?>
    </p>
    <nav class="store_plugin-pagination" id="bottom-paginator">
        <ul class="page-numbers">
            <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->first(__d('store', 'First'), array(
                'class' => 'page-numbers previous', 
                'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
            <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->prev(__d('store', 'Previous'), array(
                'class' => 'page-numbers previous', 
                'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
            <?php echo $this->Paginator->numbers(array(
                'class' => 'page-numbers', 
                'tag' => 'li', 
                'separator' => '')); ?>
            <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->next(__d('store', 'Next'), array(
                'class' => 'page-numbers next', 
                'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
            <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->last(__d('store', 'Last'), array(
                'class' => 'page-numbers next', 
                'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
        </ul>
    </nav>
    <div class="clearfix"></div>
</div>
<?php endif;?>