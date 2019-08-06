<?php 
    $product_review_photos = !empty($product_review['Photo']) ? $product_review['Photo'] : null;
    $product_review = $product_review['StoreReview'];
    $parent_review = !empty($parent_review) ? $parent_review['StoreReview'] : '';
?>
<div class="title-modal">
    <?php if($parent_review != null):?>
        <?php if($product_review['id'] > 0):?>
            <?php echo __d('store', 'Edit reply');?>  
        <?php else:?>
            <?php echo __d('store', 'Write a reply');?>   
        <?php endif;?>
    <?php else:?>
        <?php if($product_review['id'] > 0):?>
            <?php echo __d('store', 'Edit review');?>  
        <?php else:?>
            <?php echo __d('store', 'Write a review');?>    
        <?php endif;?>
    <?php endif;?>
    <button data-dismiss="modal" class="close" type="button">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body">
    <div class="create_form">
        <form id="reviewForm">
            <?php echo $this->Form->hidden('store_product_id', array(
                'value' => $product_id
            ));?>
            <?php echo $this->Form->hidden('id', array(
                'value' => $product_review['id'],
                'id' => 'review_id'
            ));?>
            <?php echo $this->Form->hidden('parent_id', array(
                'value' => !empty($parent_review['id']) ? $parent_review['id'] : ''
            ));?>
            <?php echo $this->Form->hidden('view_detail', array(
                'value' => $view_detail
            ));?>
            <?php echo $this->Form->hidden('my_review', array(
                'value' => $my_review
            ));?>
            <ul style="position:relative" class="list_style">
                <?php if($parent_review == null):?>
                <li class="review_star_form 3423">                 
                    <div >
                        <span class="review_star">
                            <?php echo $this->Form->hidden('rating', array(
                                'class' => 'review_point',
                                'value' => $product_review['rating']
                            ));?>
                            <input id="product_user_review" value="<?php echo $product_review['rating']; ?>" type="number" class="rating form-control hide" min="0" max="5" step="0.5" data-size="lg">
                        </span>
                    </div>
                </li>
                <?php endif;?>
                <li>
                    <div>
                        <label>
                            <?php if($parent_review != null):?>
                                <?php echo __d('store', 'Your reply');?>
                            <?php else:?>
                                <?php echo __d('store', 'Your review');?>
                            <?php endif;?>
                        </label>
                    </div>
                    <div >
                        <?php echo $this->Form->textarea('content', array(
                            'value' => $product_review['content']
                        ));?>
                    </div>
                </li>
                <li>
                    
                    <div >
                        <div id="images-uploader" style="margin:10px 0px 10px;">
                            <div id="product_uploader"></div>
                            <input type="button" class="button button-primary" id="triggerUpload" value="<?php echo __d('store', 'Upload Queued Files') ?>">
                        </div>
                        <?php 
                            $attachments = null;
                            if (!empty($product_review_photos)): 
                        ?>
                        <ul class="list6 list6sm" id="attachments_list" style="overflow: hidden;">
                            <?php foreach ($product_review_photos as $image): 
                                $attachments[] = $image['thumbnail'];
                            ?>
                                <li id="attach<?php echo $image['id'] ?>">
                                    <i class="icon-attach"></i>
                                    <?php echo $image['thumbnail'] ?>
                                    &nbsp;
                                    <a href="javascript:void(0)" data-image_id="<?php echo $image['id'] ?>" class="attach_remove remove_review_image" title="<?php echo __d('store', 'Delete') ?>">
                                        <i class="material-icons">delete</i>
                                    </a>	            
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif;?>
                        <?php echo $this->Form->hidden('photo_review_delete_id');?>
                        <?php echo $this->Form->hidden('attachments');?>
                    </div>
                </li>
                <li>
                    <a id="reviewButton" class="button" href="javascript:void(0)">
                        <?php if($product_review['id'] > 0):?>
                            <?php echo __d('store', 'Save');?>
                        <?php else:?>
                            <?php echo __d('store', 'Post');?>
                        <?php endif;?>
                    </a>
                    <a id="cancelReviewButton" class="button" href="javascript:void(0)" data-dismiss="modal">
                        <?php echo __d('store', 'Cancel');?>
                    </a>
                    <div class="clear"></div>
                    <div style="display:none;" class="error-message" id="reviewMessage"></div>
                </li>
            </ul>
        </form>
    </div>
</div>