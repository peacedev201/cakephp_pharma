<div class="box_content">
    <?php if($business != null):
        if(isset($business['Business'])){
            $business = $business['Business'];
        }
    ?>
        <div class="col-md-6 col-xs-5 col-sm-6 text-center">
            <div class="wd_total_score">
            <?php echo $business['total_score'];?>
            </div>
            <span class="review_star">
                <input readonly value="<?php echo $business['total_score']; ?>" type="number" class="rating form-control hide" data-stars="5" data-size="xs">
            </span>
            <a class="star-review-cnt" href="<?php echo $business['moo_hrefreview'];?>">
                <?php echo $business['review_count'];?> <?php echo $business['review_count'] == 1 ? __d('business', 'review') : __d('business', 'reviews');?>
            </a>

        </div>
        <div class="col-md-6 col-xs-7 col-sm-6 review-rating-detail">
            <?php for($i = 5; $i > 0; $i--):?>
                <div class="item_score">
                    <div class="left">
                    <span class="review_star">
                        <!-- <input type="hidden" id="input-star<?php echo $i;?>" readonly type="number" class="rating form-control hide" data-stars="1" data-size="xs"> -->
<!--                        <i class="star-rating-<?php echo $i ?>"></i>-->
                        <i class="material-icons">star</i>
                        <span class="start-number"><?php echo $i ?></span>
                    </span>
                    </div>
                    <div class="review_ruler">
                        <div class="percentage" style="width: <?php echo !empty($rulers[$i]) ? $rulers[$i]['percent'] : 0;?>%"></div>
                    </div>
                    <div class="right">
                    <?php echo !empty($rulers[$i]) ? $rulers[$i]['total_vote'] : 0;?>
                    </div>
                </div>
            <?php endfor;?>

        </div>
        <div class="review_wd_action">
            <?php if($business['user_id'] != $uid && $is_loggedin && !$is_reviewed/* && $can_create_review*/):?>
                <a class="btn btn-action btnReview btn_write_review" href="javascript:void(0)" data-business_id="<?php echo $business['id'];?>" data-view_detail="1">
                    <?php echo __d('business', 'Write review');?>
                </a>
            <?php endif;?>
        </div>
    <?php endif;?>
</div>