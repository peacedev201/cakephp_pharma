<?php
$videoHelper = MooCore::getInstance()->getHelper('Video_Video');
?>
<?php if (!empty($product_videos)):?>
    <?php foreach ($product_videos as $product_video):
        $video = $product_video['ProductVideo'];
		$video = $video['Video'];
		$product_video = $product_video['StoreProductVideo'];
    ?>
        <div class="video-list-index full_content ">
            <div class="item-content">
                <a href="<?php echo $this->request->base?>/stores/product_video_detail/<?php echo $product_video['id']?>/<?php echo seoUrl($video['title'])?>" <?php if(!$is_app):?>data-target="#storeModal" data-toggle="modal" data-dismiss="" data-backdrop="static"<?php endif;?>>
                   <img src='<?php echo $videoHelper->getImage(array('Video' => $video), array('prefix' => '450'))?>' />
                </a>
                <div class="video_info">
                    <a href="<?php echo $this->request->base?>/stores/product_video_detail/<?php echo $product_video['id']?>/<?php echo seoUrl($video['title'])?>" <?php if(!$is_app):?>data-target="#storeModal" data-toggle="modal" data-dismiss="" data-backdrop="static"<?php endif;?>>
                        <?php echo h($this->Text->truncate( $video['title'], 60 ))?>
                    </a>
                </div>
            </div>
        </div>
    <?php endforeach;?>
    <div class="clear"></div>
<?php else:?>
    <div class="clear text-center" style="width:100%;overflow:hidden">
        <?php echo __d('store', 'No more videos found');?>
    </div>
<?php endif; ?>
<?php if(count($product_videos) >= Configure::read('Store.video_item_per_page')): ?>
    <?php $this->Html->viewMore('/stores/load_product_video/'.$product_id.'/page:'.($page + 1), 'tab-videos') ?>
<?php endif; ?>
