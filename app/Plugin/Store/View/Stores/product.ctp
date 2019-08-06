<?php
echo $this->Html->css(array(
    'Store.cloudzoom',
    'Store.store',
    'Store.star-rating',
    'Store.jquery-ui',
    'Store.flex_slider'
), array('block' => 'css', 'minify'=>false));
?>
    
<?php 
    $productImages = $product['StoreProductImage'];
    $mainProductImage = !empty($productImages[0]) ? $productImages[0] : null;
    $producer = !empty($product['StoreProducer']) ? $product['StoreProducer'] : '';
    $store = $product['Store'];
    $product = $product['StoreProduct'];
?>
<?php $this->Html->scriptStart(array(
    'inline' => false, 
    'domReady' => true, 
    'requires' => array('jquery', 'store_store', 'store_jquery_ui', 'store_cloudzoom', 'store_star_rating', 'store_fancybox', 'store_flex_slider'), 
    'object' => array('$', 'store_store')
));?>
    store_store.initGlobal();
    store_store.initProductDetail('<?php echo $product['allow_comment'];?>');
<?php $this->Html->scriptEnd(); ?> 

<script type="text/template" id="buy_feature_product_confirm">
    <?php echo sprintf(__d('store', 'This feature costs %s for %s day(s). If this product already set as featured, expiration date will be expanded. Are you sure you want to buy?'), $this->Store->formatMoney($package['StorePackage']['price']), $package['StorePackage']['period']);?>
</script>
<div class="bar-content">  
    <?php echo $this->Element('Store.mobile/mobile_menu');?>
    <div class="content_center">
        <?php echo $this->Element('Store.mobile/mobile_group');?>
        <div class="main-container page-shop">
            <div class="page-content">
                <div class="container page-view-detail">
                    <?php if(!empty($user['Role']) && $user['Role']['is_admin'] && $product['approve'] == 0):?> 
                    <div id="flashMessage" class="Metronic-alerts alert alert-danger fade in">
                        <?php echo __d('store', 'This product is currently disapproved.');?>
                    </div>
                    <?php endif;?>
                    <nav itemprop="breadcrumb" class="store_plugin-breadcrumb">
                        <a href="<?php echo STORE_URL;?>"><?php echo __d('store', 'Products');?></a>		
                        <?php if(!empty($cat_paths)):?>
                            <?php foreach($cat_paths as $cat_path):
                                $cat_path = $cat_path['StoreCategory'];
                            ?>
                                <span class="separator">/</span>				
                                <a href="<?php echo $cat_path['moo_href'];?>"><?php echo $cat_path['name'];?></a>	
                            <?php endforeach;?>
                        <?php endif;?>
                        <span class="separator">/</span>		
                        <?php echo $product['name'];?>
                    </nav>
                    <div class="product-view">
                        <?php if($noPermission):?>
                            <div id="flashMessage" class="Metronic-alerts alert alert-danger fade in">
                                <?php echo __d('store', 'You don\'t have permission to view this page');?>
                            </div>
                        <?php else:?>
                            <div class="post-103 product type-product status-publish has-post-thumbnail product_cat-accessories product_cat-bags product_cat-bands product_cat-blazers product_cat-blazers-bags product_cat-books product_cat-bootees-bags product_cat-clothing product_cat-clothing-notebooks product_cat-coats product_cat-coats-clothing product_cat-coats-clothing-notebooks product_cat-cocktail product_cat-day product_cat-dresses product_cat-evening product_cat-furniture product_cat-handbags product_cat-jackets product_cat-jeans product_cat-kids product_cat-laptop product_cat-lingerie product_cat-notebooks product_cat-run product_cat-sandals product_cat-shoes product_cat-sports product_cat-sports-shoes product_cat-t-shirts product_cat-t-shirts-clothing-notebooks product_cat-table product_cat-watches jsn-master featured shipping-taxable purchasable product-type-simple product-cat-accessories product-cat-bags product-cat-bands product-cat-blazers product-cat-blazers-bags product-cat-books product-cat-bootees-bags product-cat-clothing product-cat-clothing-notebooks product-cat-coats product-cat-coats-clothing product-cat-coats-clothing-notebooks product-cat-cocktail product-cat-day product-cat-dresses product-cat-evening product-cat-furniture product-cat-handbags product-cat-jackets product-cat-jeans product-cat-kids product-cat-laptop product-cat-lingerie product-cat-notebooks product-cat-run product-cat-sandals product-cat-shoes product-cat-sports product-cat-sports-shoes product-cat-t-shirts product-cat-t-shirts-clothing-notebooks product-cat-table product-cat-watches instock" id="product-103" itemtype="http://schema.org/Product" itemscope="">
                                <div class="row">
                                    <div class="col-xs-12 col-md-5">
                                        <div class="single-product-image">
                                            <?php if($product['featured']):?>
                                            <div class="featured_product">
                                                <span><?php echo __d('store', 'Featured');?></span>
                                            </div>
                                            <?php endif;?>
                                            <div class="images">
                                                <a class="cloud-zoom" id="cloudZoom" href="<?php echo $this->Store->getProductImage($mainProductImage, array('prefix' => PRODUCT_PHOTO_LARGE_WIDTH));?>" >
                                                    <img class="img-responsive" src="<?php echo $this->Store->getProductImage($mainProductImage, array('prefix' => PRODUCT_PHOTO_LARGE_WIDTH));?>"/>
                                                </a>
                                                <div class="zoom_in_marker" <?php if($productImages == null):?>style="bottom:0"<?php endif;?>>
                                                    <i class="material-icons">open_with</i>
                                                </div>
                                                <?php if($productImages != null):?>
                                                <div id="carousel" class="flexslider">
                                                    <ul class="recent_list slides">
                                                        <?php foreach($productImages as $k => $productImage):?>
                                                        <li class="photo_container">
                                                             <a href="<?php echo $this->Store->getProductImage($productImage, array('prefix' => PRODUCT_PHOTO_LARGE_WIDTH));?>" rel="popupWin:'<?php echo $this->Store->getProductImage($productImage, array('prefix' => PRODUCT_PHOTO_LARGE_WIDTH));?>', useZoom: 'cloudZoom', smallImage: '<?php echo $this->Store->getProductImage($productImage, array('prefix' => PRODUCT_PHOTO_LARGE_WIDTH));?>'" class="cloud-zoom-gallery">
                                                                <img itemprop="image" src="<?php echo $this->Store->getProductImage($productImage, array('prefix' => PRODUCT_PHOTO_THUMB_WIDTH));?>" class="img-responsive"/>
                                                            </a>
                                                        </li>
                                                        <?php endforeach;?>
                                                    </ul>
                                                </div>
                                                <?php endif;?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-md-7">
                                        <div class="summary entry-summary single-product-info">
                                            <h1 class="product_title entry-title" itemprop="name">
                                                <?php echo $product['name'];?>
                                            </h1>
                                            <div itemprop="description" class="short-description">
                                                <p><?php echo $product['brief'];?></p>
                                            </div>
                                            <div class="price-box">
                                                <p class="price">
                                                    <?php if($product['allow_promotion']):?>
                                                    <span class="old-price">
                                                        <span class="amount"><?php echo $this->Store->formatMoney($product['old_price']);?></span>
                                                    </span>
                                                    <?php endif;?>
                                                    <span class="special-price">
                                                        <span class="amount" id="total_price"><?php echo $this->Store->formatMoney($product['new_price']);?></span>
                                                    </span>
                                                </p>
                                            </div>
                                            <div class="clear"></div>
                                            <div class="comment-form-rating">
                                                <?php echo $this->Element('Store.total_product_review', array(
                                                    'product' => $product
                                                ));?> 
                                            </div>
                                            
                                            <?php if(!$product['out_of_stock']):?>
                                                <?php if($attributes != null):?>
                                                <form class="variations_form cart" id="pro-attr-form">
                                                    <?php echo $this->Form->hidden('product_id', array(
                                                        'value' => $product['id'],
                                                        'id' => ''
                                                    ));?>
                                                    <table class="variations" cellspacing="0">
                                                        <?php foreach($attributes as $attribute):
                                                            $attribute_children = $attribute['children'];
                                                            $child_list = $attribute['child_list'];
                                                            $attribute = $attribute['StoreAttribute'];
                                                        ?>
                                                            <?php if($child_list != null):?>
                                                            <tr>
                                                                <td class="label">
                                                                    <label><?php echo $attribute['name'];?></label>
                                                                </td>
                                                                <td class="value">
                                                                    <?php echo $this->Form->select('attribute_id.', $child_list, array(
                                                                        'empty' => __d('store',  'Choose an option'),
                                                                        'id' => '',
                                                                        'class' => 'product_attribute'
                                                                    ));?>
                                                                </td>
                                                            </tr>
                                                            <?php endif;?>
                                                        <?php endforeach;?>
                                                    </table>
                                                </form>
                                                <?php endif;?>
                                                <?php if($this->Store->allowBuyProduct()):?>
                                                    <div class="cart">
                                                        <?php if($product['product_type'] == STORE_PRODUCT_TYPE_DIGITAL && $this->Store->isBoughtDigitalProduct($product['id'])):?> 
                                                            <a class="single_add_to_cart_button button alt" href="<?php echo $this->request->base.'/stores/products/download_product/'.$product['id'];?>">
                                                                <?php echo __d('store', 'Download');?>
                                                            </a>
                                                        <?php elseif($product['product_type'] == STORE_PRODUCT_TYPE_LINK && $this->Store->isBoughtDigitalProduct($product['id'])):?>  
                                                            <a class="single_add_to_cart_button button alt" href="<?php echo $this->request->base.'/stores/products/download_product/'.$product['id'];?>" target="_blank">
                                                                <?php echo __d('store', 'View Link');?>
                                                            </a>
                                                        <?php else:?>
                                                            <?php if($product['show_quanity'] && $product['product_type'] == STORE_PRODUCT_TYPE_REGULAR):?>
                                                            <div class="quantity">
                                                                <input type="number" size="4" class="input-text qty text quantity_cart" id="quantity-cart" title="Qty" value="1" name="quantity" min="1" step="1">
                                                            </div>
                                                            <?php endif;?>
                                                            <button class="single_add_to_cart_button button alt <?php if($product['product_type'] != STORE_PRODUCT_TYPE_REGULAR &&  $this->Store->isProductInCart($product['id'])):?>active<?php else:?>add_to_cat<?php endif;?> cart_product_<?php echo $product['id'];?>" type="button" data-id="<?php echo $product['id'];?>" data-wrapper_id="quantity-cart" data-type="<?php echo $product['product_type'];?>">
                                                                <?php echo ($product['product_type'] != STORE_PRODUCT_TYPE_REGULAR &&  $this->Store->isProductInCart($product['id'])) ? __d('store', 'Added to cart') : __d('store', 'Add to cart'); ?>
                                                            </button>
                                                        <?php endif;?>
                                                    </div>
                                                <?php endif;?>
                                            <?php else:?>
                                                <div class="cart">
                                                <?php echo __d('store', 'Out of stock')?>
                                                </div>
                                            <?php endif;?>
                                            <div class="actions">
                                                <div class="action-buttons">
                                                    <div class="add-to-links">
                                                        <div class="yith-wcwl-add-to-wishlist add-to-wishlist-103">
                                                            <div style="display:block" class="yith-wcwl-add-button show">
                                                                <?php if($product['in_wishlist']):?>
                                                                    <a class="add_to_wishlist active add_to_wishlist" data-action="0" data-placement="top" data-toggle="tooltip" data-original-title="<?php echo __d('store', 'Remove from wishlist');?>" href="javascript:void(0)" data-id="<?php echo $product['id'];?>">
                                                                        <i class="material-icons">favorite</i>
                                                                    </a>
                                                                <?php else:?>
                                                                    <a class="add_to_wishlist add_to_wishlist" data-action="1" data-placement="top" data-toggle="tooltip" data-original-title="<?php echo __d('store', 'Add to wishlist');?>" href="javascript:void(0)" data-id="<?php echo $product['id'];?>">
                                                                        <i class="material-icons">favorite</i>
                                                                    </a>
                                                                <?php endif;?>
                                                            </div>
                                                        </div>
                                                        <div class="clear"></div>			
                                                    </div>
                                                    <div class="sharefriend">
                                                        <?php /*?>
                                                        <a href="mailto: yourfriend@domain.com?Subject=<?php echo __d('store', 'Checkout this product');?>: <?php echo $product['name'];?>&Body=<?php echo Router::url("", true).$product['moo_href'];?>" data-placement="top" data-toggle="tooltip" data-original-title="<?php echo __d('store', 'Email your friend');?>">
                                                            <?php echo __d('store', 'Email your friend');?>
                                                        </a>
                                                        <?php */?>
                                                        <a href="javascript:void(0)" class="email_friend" data-id="<?php echo $product['id'];?>" data-placement="top" data-toggle="tooltip" data-original-title="<?php echo __d('store', 'Email your friend');?>">
                                                            <i class="material-icons">email</i>
                                                        </a>
                                                    </div>
                                                    <div class="shareproduct">
                                                        <?php if($product['is_liked']):?>
                                                            <a href="javascript:void(0)" class="active like_product" data-id="<?php echo $product['id'];?>" data-action="0" data-text="<?php echo __d('store', 'Like This Product');?>" data-placement="top" data-toggle="tooltip" data-original-title="<?php echo __d('store', 'You Liked This Product');?>">
                                                                <i class="material-icons">thumb_up</i>
                                                                <span class="count" id="Store_Store_Product_like_<?php echo $product['id'];?>"><?php echo $product['like_count'];?></span>
                                                            </a>
                                                        <?php else:?>
                                                            <a href="javascript:void(0)" class="like_product" data-id="<?php echo $product['id'];?>" data-action="1" data-text="<?php echo __d('store', 'You Liked This Product');?>" data-placement="top" data-toggle="tooltip" data-original-title="<?php echo __d('store', 'Like This Product');?>">
                                                                <i class="material-icons">thumb_up</i>
                                                                <span class="count" id="Store_Store_Product_like_<?php echo $product['id'];?>"><?php echo $product['like_count'];?></span>
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
                                                    <div class="shareproduct">
                                                        <a href="javascript:void(0)" class="report_product" data-id="<?php echo $product['id'];?>" data-placement="top" data-toggle="tooltip" data-original-title="<?php echo __d('store', 'Report');?>">
                                                            <i class="material-icons">report</i>
                                                        </a>
                                                    </div>
                                                    <?php if($uid > 0 && $uid != $store['user_id']):?>
                                                    <div class="shareproduct">
                                                        <?php if((!Configure::read('Chat.chat_disable') && Configure::read('Chat.chat_turn_on_notification') && Configure::read('core.send_message_to_non_friend') && $store_user['User']['receive_message_from_non_friend']) ||
                                                                 (!Configure::read('Chat.chat_disable') && Configure::read('Chat.chat_turn_on_notification') && $are_friend)):
                                                        ?>
															<a href="javascript:void(0)" onclick="require(['mooChat'],function(chat){chat.openChatWithOneUser(<?php echo $store['user_id'];?>)});">
																<i class="material-icons">help_outline</i>
															</a>
														<?php else:?>
															<a href="javascript:void(0)" class="ask_seller" data-userid="<?php echo $store['user_id'];?>" data-placement="top" data-toggle="tooltip" data-original-title="<?php echo __d('store', 'Ask seller');?>">
																<i class="material-icons">help_outline</i>
															</a>
														<?php endif;?>
                                                    </div>
													<?php endif;?>
                                                </div>
                                            </div>
                                            <div class="clear"></div>
                                            <div class="product_meta">
                                                <span class="sku_wrapper">
                                                    <?php echo __d('store', 'Product Code');?>: 
                                                    <span class="sku"><?php echo $product['product_code'];?></span>
                                                </span>
                                                <?php if(!empty($producer['name'])):?>
                                                <span class="sku_wrapper">
                                                    <?php echo __d('store', 'Producer');?>: 
                                                    <span class="sku"><?php echo $producer['name'];?></span>
                                                </span>
                                                <?php endif;?>
                                                <?php if(!empty($product['warranty'])):?>
                                                <span class="sku_wrapper">
                                                    <?php echo __d('store', 'Warranty');?>: 
                                                    <span class="sku"><?php echo $product['warranty'];?></span>
                                                </span>
                                                <?php endif;?>
                                            </div>
                                            <?php if($product['allow_share'] && !$is_app):?>
                                            <div class="single-product-sharing">
                                                <div class="widget widget_socialsharing_widget">
                                                    <h3 class="widget-title"><?php echo __d('store', 'Share this product');?></h3>
                                                    <ul class="social-icons">
                                                        <li>
                                                            <a target="_blank" title="Facebook" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(Router::url('/', true).$product['moo_url']);?>" class="facebook social-icon">
                                                                <i class="social-icon-new s-facebook"></i>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a target="_blank" title="Twitter" href="https://twitter.com/home?status=<?php echo urlencode($product['name']);?>&nbsp;<?php echo urlencode(Router::url('/', true).$product['moo_url']);?>" class="twitter social-icon">
                                                                <i class="social-icon-new s-twitter"></i>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a target="_blank" title="Google +" href="https://plus.google.com/share?url=<?php echo urlencode(Router::url('/', true).$product['moo_url']);?>" class="gplus social-icon">
                                                                <i class="social-icon-new s-google-plus"></i>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a target="_blank" title="LinkedIn" href="https://www.linkedin.com/shareArticle?mini=true&amp;url=<?php echo urlencode(Router::url('/', true).$product['moo_url']);?>&amp;title=<?php echo urlencode($product['name']);?>" class="linkedin social-icon">
                                                                <i class="social-icon-new s-linkedin"></i>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <?php endif;?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif;?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if($isMobile || $this->request->is('androidApp') || $this->request->is('iosApp')):?>
	<div class="box2 search-friend">
        <h3><?php echo __d('store', 'Seller Info');?></h3>
        <?php echo $this->Element('Store.widgets/seller_info', array(
            'store' => $store,
            'store_user' => $store_user,
            'are_friend' => $are_friend
        ));?>
    </div>
<?php endif; ?>
<?php if($this->request->is('androidApp') || $this->request->is('iosApp')):?>
    <?php echo $this->Element('Store.widgets/product_detail_description', array(
        'product' => $product,
        'store' => $store
    ));?>
    <?php echo $this->Element('Store.widgets/related_product', array(
        'product_id' => $product['id'],
        'relatedProducts' => $relatedProducts
    ));?>
<?php endif; ?>
