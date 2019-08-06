<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>

<div class="item_stat extra_info review-tooltip-profile">
    <span class="review-star-rating">
        <input readonly value="<?php echo !empty($aReviewRating) ? $aReviewRating['rating_avg'] : 0.00; ?>" type="number" class="rating hide" data-stars="5" data-size="xs">
    </span>
    &nbsp;
    <a class="review-count" href="<?php echo $aUser['moo_href'] . '?tab=reviews'; ?>">
        <?php echo __dn('review', '%s review', '%s reviews', !empty($aReviewRating) ? $aReviewRating['review_count'] : 0, !empty($aReviewRating) ? $aReviewRating['review_count'] : 0); ?>
    </a>
</div>

<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["mooReview"], function(mooReview) {
        $(document).ready(function() {
            mooReview.initWidgetReview(true);
        });
    });
</script>
<?php endif; ?>
