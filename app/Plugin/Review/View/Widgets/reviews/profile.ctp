<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */

?>

<?php if(!empty($bShowWidget)): ?>

<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["mooReview"], function(mooReview) {
        $(document).ready(function() {
            mooReview.initWidgetReview(true);
        });
    });
</script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('mooReview'), 'object' => array('mooReview')));?>
mooReview.initWidgetReview(true);<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>

<?php if(!empty($bLoadHeader)): ?>
<div class="box2 review-rating-content">
    <?php if($title_enable): ?>
    <h3><?php echo (!empty($title)) ? $title : __d('review', 'Reviews & Ratings'); ?></h3>
    <?php endif; ?>

    <div class="box_content" id="reviewWidgetContent">
        <?php endif; ?>
        <div class="review-rating-box-content">
            <div class="col-md-6 text-center">
                <div class="review-rating-score-count">
                    <?php echo !empty($aReviewRating['rating_avg']) ? $aReviewRating['rating_avg'] : "0.00"; ?>
                </div>
                <div class="review-star-rating">
                    <input readonly value="<?php echo !empty($aReviewRating) ? $aReviewRating['rating_avg'] : 0.00; ?>" type="number" class="rating form-control hide" data-stars="5" data-size="xs">
                </div>
                <div class="review-star-count">
                    <a href="javascript:void(0)" id="reviewProfileWidget">
                        <?php echo __dn('review', '%s review', '%s reviews', !empty($aReviewRating) ? $aReviewRating['review_count'] : 0, !empty($aReviewRating) ? $aReviewRating['review_count'] : 0); ?>
                    </a>
                </div>
            </div>
            <div class="col-md-6 review-rating-detail">
                <?php for($i = 5; $i > 0; $i--):?>
                    <div class="review-rating-score">
                        <div class="rating-left">
                            <span class="review-star-rating">
                                <i class="material-icons">star</i>
                                <span class="start-number"><?php echo $i ?></span>
                            </span>
                        </div>
                        <div class="rating-ruler">
                            <div class="percentage" style="width: <?php echo !empty($aReviewRating['review_ruler'][$i]) ? $aReviewRating['review_ruler'][$i]['percent'] : 0;?>%"></div>
                        </div>
                        <div class="rating-right"><?php echo !empty($aReviewRating['review_ruler'][$i]) ? $aReviewRating['review_ruler'][$i]['review_count'] : 0;?></div>
                    </div>
                <?php endfor;?>
            </div>
        </div>
        <div class="clear"></div>

        <?php if ($bWriteReview): ?>
        <div class="review-rating-action">
            <?php
            $this->MooPopup->tag(array(
                'href' => $this->request->base . '/reviews/write/' . $user['User']['id'],
                'innerHtml'=> __d('review', 'Write review'),
                'title' => __d('review', 'Write review'),
                'class' => 'btn btn-action'
            ));
            ?>
        </div>
        <?php endif; ?>

        <?php if(!empty($uid)): ?>
        <div class="hidden">
            <a href="javascript:void(0)" id="reloadReviewWidget" data-url="<?php echo $this->request->base . '/reviews/reload/' . $user['User']['id'] . '/' . $uid; ?>"></a>
        </div>
        <?php endif; ?>

        <?php if(!empty($bLoadHeader)): ?>
    </div>
</div>
<?php endif; ?>
    
<?php endif; ?>