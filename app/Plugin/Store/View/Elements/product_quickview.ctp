<?php if ($this->request->is('ajax')): ?>
    <script type="text/javascript">
        require(["jquery", "store_store", "store_jcarousel"], function($, store_store, store_jcarousel) {
            store_store.initQuickview();
        });
    </script>
<?php else: ?>
    <?php $this->Html->scriptStart(array(
        'inline' => false, 
        'domReady' => true, 
        'requires' => array('jquery', 'store_store', 'store_jcarousel'), 
        'object' => array('$', 'store_store')
    ));?>
        store_store.initQuickview();
    <?php $this->Html->scriptEnd(); ?> 
<?php endif; ?>
    
<?php
$productImages = $product['StoreProductImage'];
$mainProductImage = !empty($productImages[0]) ? $productImages[0] : null;
$product = $product['StoreProduct'];
?>

<div class="title-modal">
    <?php echo __d('store', 'Product');?>    
    <button data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
</div>
<div class="modal-body">
    <div id="quickview-content">	
        <div class="store_plugin product">
            <div class="product-images single-product-image">
                <?php if ($productImages != null):?>
                    <div class="connected-carousels">
                        <div class="stage">
                            <?php if($product['featured']):?>
                            <div class="featured_product">
                                <span><?php echo __d('store', 'Featured');?></span>
                            </div>
                            <?php endif;?>
                            <div class="carousel carousel-stage">
                                <ul>
                                    <?php foreach ($productImages as $k => $productImage): ?>
                                        <li>
                                            <img src="<?php echo $this->Store->getProductImage($productImage, array('prefix' => PRODUCT_PHOTO_LARGE_WIDTH)); ?>" width="600" height="600" alt="">
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                        <div class="navigation">
                            <?php if(count($productImages) > 4):?>
                            <a href="#" class="prev prev-navigation">&lsaquo;</a>
                            <a href="#" class="next next-navigation">&rsaquo;</a>
                            <?php endif;?>
                            <div class="carousel carousel-navigation">
                                <ul>
                                    <?php foreach ($productImages as $k => $productImage): ?>
                                        <li><img src="<?php echo $this->Store->getProductImage($productImage, array('prefix' => PRODUCT_PHOTO_TINY_WIDTH)); ?>" width="180" height="180" alt=""></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="connected-carousels">
                        <div class="stage">
                            <div class="carousel carousel-stage">
                                <ul>
                                    <li><img src="<?php echo $this->Store->getProductImage($mainProductImage, array('prefix' => PRODUCT_PHOTO_THUMB_WIDTH)); ?>" width="600" height="600" alt=""></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="product-info single-product-info">
                <h1><?php echo $product['name']; ?></h1>
                <span class="review_star">
                    <input readonly value="<?php echo $product['rating']; ?>" type="number" class="rating form-control hide">
                </span>
                <div class="price-box">
                    <p class="price">
                        <?php if ($product['allow_promotion']): ?>
                            <span class="old-price">
                                <span class="amount"><?php echo $this->Store->formatMoney($product['old_price']); ?></span>
                            </span>
                        <?php endif; ?>
                        <span class="special-price">
                            <span class="amount"><?php echo $this->Store->formatMoney($product['new_price']); ?></span>
                        </span>
                    </p>
                </div>
                <div class="quick-add-to-cart">

                    <div class="cart">
                        <?php if ($product['out_of_stock']): ?>
                            <a class="button add_to_cart_button product_type_simple" href="<?php echo $product['moo_href']; ?>">
                                <?php echo __d('store', 'Out of stock'); ?>
                            </a>
                        <?php elseif ($product['attribute_to_buy'] == 1): ?>
                            <a class="button add_to_cart_button product_type_simple" href="<?php echo $product['moo_href']; ?>">
                                <?php echo __d('store', 'Go to buy'); ?>
                            </a>
                        <?php else: ?>
                            <?php if($product['product_type'] == STORE_PRODUCT_TYPE_DIGITAL && $this->Store->isBoughtDigitalProduct($product['id'])):?> 
                                <a class="button add_to_cart_button product_type_simple" href="<?php echo $this->request->base.'/stores/products/download_product/'.$product['id'];?>">
                                    <?php echo __d('store', 'Download');?>
                                </a>
                            <?php elseif($product['product_type'] == STORE_PRODUCT_TYPE_LINK && $this->Store->isBoughtDigitalProduct($product['id'])):?>  
                                <a class="button add_to_cart_button product_type_simple" href="<?php echo $this->request->base.'/stores/products/download_product/'.$product['id'];?>" target="_blank">
                                    <?php echo __d('store', 'View Link');?>
                                </a>
                            <?php elseif($this->Store->allowBuyProduct()):?>
                                <?php if($product['show_quanity'] && $product['product_type'] == STORE_PRODUCT_TYPE_REGULAR):?>
                                <div class="quantity buttons_added">
                                    <input type="number" size="4" class="input-text qty text quantity_cart" id="quantity-cart-quickview" title="Qty" value="1" name="quantity" min="1" step="1">
                                </div>
                                <?php endif;?>
                                <a class="single_add_to_cart_button button alt <?php if($product['product_type'] != STORE_PRODUCT_TYPE_REGULAR &&  $this->Store->isProductInCart($product['id'])):?>active<?php else:?>add_to_cat<?php endif;?> cart_product_<?php echo $product['id'];?>" href="javascript:void(0)" data-id="<?php echo $product['id'];?>" data-wrapper_id="quantity-cart-quickview" data-quickview="1" data-type="<?php echo $product['product_type'];?>">
                                    <?php echo ($product['product_type'] != STORE_PRODUCT_TYPE_REGULAR &&  $this->Store->isProductInCart($product['id'])) ? __d('store', 'Added to cart') : __d('store', 'Add to cart'); ?>
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                        <div style="margin-top: 7px;display: none" id="quickview-message"></div>
                    </div>
                    <div class="actions">
                        <div class="action-buttons">
                            <div class="add-to-links">
                                <div class="yith-wcwl-add-to-wishlist add-to-wishlist-103">
                                    <div style="display:block" class="yith-wcwl-add-button show">
                                        <?php if ($product['in_wishlist']): ?>
                                            <a class="add_to_wishlist active product_wishlist_<?php echo $product['id']; ?>" data-action="0" data-placement="top" data-toggle="tooltip" data-original-title="<?php echo __d('store', 'Remove from wishlist'); ?>" href="javascript:void(0)" data-id="<?php echo $product['id'];?>" data-quickview="1">
                                                <i class="material-icons">favorite</i>
                                            </a>
                                        <?php else: ?>
                                            <a class="add_to_wishlist product_wishlist_<?php echo $product['id']; ?>" data-action="1" data-placement="top" data-toggle="tooltip" data-original-title="<?php echo __d('store', 'Add to wishlist'); ?>" href="javascript:void(0)" data-id="<?php echo $product['id'];?>" data-quickview="1">
                                                <i class="material-icons">favorite</i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="clear"></div>			
                            </div>		
                            <div class="shareproduct">
                                <?php if ($product['is_liked']): ?>
                                    <a href="javascript:void(0)" class="active product_like_<?php echo $product['id']; ?> like_product" data-id="<?php echo $product['id'];?>" data-action="0" data-text="<?php echo __d('store', 'Like This Product'); ?>" data-placement="top" data-toggle="tooltip" data-original-title="<?php echo __d('store', 'You Liked This Product'); ?>">
                                        <i class="material-icons">thumb_up</i>
                                    </a>
                                <?php else: ?>
                                    <a class="product_like_<?php echo $product['id']; ?> like_product" href="javascript:void(0)" data-id="<?php echo $product['id'];?>" data-action="1" data-text="<?php echo __d('store', 'You Liked This Product'); ?>" data-placement="top" data-toggle="tooltip" data-original-title="<?php echo __d('store', 'Like This Product'); ?>">
                                        <i class="material-icons">thumb_up</i>
                                    </a>
                                <?php endif; ?>
                            </div>
                            <?php if ($product['allow_share']): ?>
                                <div class="shareproduct">
                                    <a href="javascript:void(0)" class="shareFeedBtn" share-url="<?php echo $product['url_share'];?>" data-placement="top" data-toggle="tooltip" data-original-title="<?php echo __d('store', 'Share To News Feed');?>">
                                        <i class="material-icons">share</i>
                                    </a>
                                </div>		
                            <?php endif; ?>
                            <div class="shareproduct">
                                <a href="javascript:void(0)" class="report_product" data-id="<?php echo $product['id'];?>" data-placement="top" data-toggle="tooltip" data-original-title="<?php echo __d('store', 'Report');?>">
                                    <i class="material-icons">report</i>
                                </a>
                            </div>
                            <div class="count-like-click">
                                <span class="count" id="Store_Store_Product_like_<?php echo $product['id']; ?>">
                                    <?php echo $product['like_count']; ?>
                                </span> <?php echo __d('store', 'people like this'); ?>
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
                <?php if ($product['brief']) : ?>
                    <div class="quick-desc"><?php echo $product['brief']; ?></div>
                <?php endif; ?>
                <?php if ($product['allow_share']): ?>
                    <div class="social-sharing">	
                        <div class="widget widget_socialsharing_widget">
                            <h3 class="widget-title"><?php echo __d('store', 'Share this product'); ?></h3>
                            <ul class="social-icons">
								<li>
									<a target="_blank" title="Facebook" onclick="javascript: window.open('https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(Router::url('/', true).$product['moo_url']);?>');
										return false;" href="#" class="facebook social-icon">
										<i class="social-icon-new s-facebook"></i>
									</a>
								</li>
								<li>
									<a target="_blank" onclick="javascript: window.open('https://twitter.com/home?status=<?php echo urlencode($product['name']);?>&nbsp;<?php echo urlencode(Router::url('/', true).$product['moo_url']);?>');
										return false;" title="Twitter" href="#" class="twitter social-icon">
										<i class="social-icon-new s-twitter"></i>
									</a>
								</li>
								<li>
									<a target="_blank" title="Google +" onclick="javascript: window.open('https://plus.google.com/share?url=<?php echo urlencode(Router::url('/', true).$product['moo_url']);?>');
										return false;" href="#" class="gplus social-icon">
										<i class="social-icon-new s-google-plus"></i>
									</a>
								</li>
								<li>
									<a target="_blank" title="LinkedIn" onclick="javascript: window.open('https://www.linkedin.com/shareArticle?mini=true&amp;url=<?php echo urlencode(Router::url('/', true).$product['moo_url']);?>&amp;title=<?php echo urlencode($product['name']);?>');
										return false;" href="#" class="linkedin social-icon">
										<i class="social-icon-new s-linkedin"></i>
									</a>
								</li>
							</ul>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div>