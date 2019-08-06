<?php if ($this->request->is('ajax')): ?>
    <script type="text/javascript">
        require(["jquery", "store_star_rating", "store_store"], function($, store_star_rating, store_store) {
            store_store.initReviewStar();
            store_store.initReviewPhotoPopup();
        });
    </script>
<?php else: ?>
    <?php $this->Html->scriptStart(array(
        'inline' => false, 
        'domReady' => true, 
        'requires'=>array("jquery", "store_star_rating", "store_store"), 
        'object' => array("$", "store_star_rating", "store_store"))); ?>
            store_store.initReviewStar();
            store_store.initReviewPhotoPopup();
    <?php $this->Html->scriptEnd(); ?>
<?php endif; ?>
            
<?php 
    echo $this->Html->css(array(
        'Store.star-rating',
    ));
?>
<?php
    $user = $review['User'];
    $review_photos = $review['Photo'];
    $replies = !empty($review['children']) ? $review['children'] : null;
    $review = $review['StoreReview'];
    $photoHelper = MooCore::getInstance()->getHelper('Photo_Photo');
?>
<div class="activity_item bus_review_item">
    <div class="bus_review_info bus_review_reply_info">
        <?php if($review['parent_id'] == 0):?>
        <span class="review_star">
            <input id="input-21e" readonly value="<?php echo $review['rating']; ?>" type="number" class="rating form-control hide" min="0" max="5" step="0.5" data-size="md">
        </span>
        <?php endif;?>
        <div class="bus_review_text main_review" id="item_feed_comment_text_<?php echo $review['id']?>">
            <?php if(!empty($is_reply) && $is_reply):?>
            <span class="reply_owner"><?php echo __d('store', 'Response:');?> </span>
            <?php endif;?>
            <?php echo $this->Moo->formatText($review['content'], false, true, array('no_replace_ssl' => 1)); ?>
                <?php if ($review_photos && empty($is_share_popup)):?>
                <div class="bus_review_photo">
                    <?php foreach($review_photos as $photo):?>
                    <div class="bus_review_photo_item">
                        <a class="layer_square" href="<?php echo $photoHelper->getImage(array('Photo' => $photo), array());?>">
                            <img src="<?php echo $photoHelper->getImage(array('Photo' => $photo), array('prefix' => '150_square'));?>" />
                        </a>
                    </div>
                    <?php endforeach;?>
                </div>
                <?php endif;?>
            <div class="clear"></div>
        </div>
    </div>
</div>