<?php echo $this->Html->css(array('Business.business-widget.css' )); ?>
<?php if($business_reviews != null):?>
<div class="box2 filter_block recent-biz-review">
    <h3><?php echo !empty($title) ? $title : __d('business', 'Recent Reviews');?></h3>
    <div class="box_content">
        <ul class="biz-review-mini">
            <?php foreach($business_reviews as $business_review):
                $user = $business_review['User'];
                $business = $business_review['Business'];
                $business_review = $business_review['BusinessReview'];
            ?>
                <li >
                    <?php
                        echo $this->Moo->getItemPhoto(array('User' => $user),array( 'prefix' => '100_square'), array('class' => 'img_wrapper2 user_avatar_large'));
                    ?>
                    <div class="biz-mini-info">
                    <?php echo $this->Moo->getName($user)?>
                    <?php echo __d('business', 'wrote a review for');?>
                    <a href="<?php echo $business['moo_hrefreview'].'?review='.$business_review['id'];?>">
                        <?php echo $business['name'];?>
                    </a>

                        <span class="review_star">
                            <input id="input-21e" readonly value="<?php echo $business_review['rating']; ?>" type="number" class="rating form-control hide" min="0" max="5" step="0.5" data-size="xs">
                        </span>


                        <div class="clear"></div>

                    </div>
                    <div class="biz-review-text">
                        <?php echo $this->Text->truncate(strip_tags(str_replace(array('<br>','&nbsp;'), array(' ',''), $business_review['content'])), 200, array('eclipse' => '')); ?>
                        <div class="date"><?php echo $this->Moo->getTime($business_review['created'], Configure::read('core.date_format'), $utz )?></div>
                    </div>
                </li>
            <?php endforeach;?>
        </ul>
    </div>
</div>
<?php endif;?>