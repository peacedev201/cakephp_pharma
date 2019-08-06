<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>

<div class="modal-header">
    <button data-dismiss="modal" class="close" type="button"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">
        <?php if(!empty($type) && $type == 'reply'): ?>
            <?php echo (!empty($aReviewUser['ReviewUser']['id'])) ? __d('review', 'Edit Reply') : __d('review', 'Write a Reply'); ?>
        <?php else: ?>
            <?php echo (!empty($aReviewUser['ReviewUser']['id'])) ? __d('review', 'Edit Review') : __d('review', 'Write a Review'); ?>
        <?php endif; ?>
    </h4>
</div>
<div class="modal-body write-review-content">
    <div class="create_form">
        <form id="reviewForm">
            <ul class="list6 full_content">
                <?php echo $this->Form->hidden('id', array('value' => $aReviewUser['ReviewUser']['id'])); ?>
                <?php echo $this->Form->hidden('recieve_id', array('value' => $iRecieveId)); ?>
                <?php echo $this->Form->hidden('type', array('value' => $type)); ?>
                
                <?php if(empty($type) || $type != 'reply'): ?>
                <li>
                    <div class="col-sm-12 text-center">
                        <div class="review-star-rating review_star">
                            <?php echo $this->Form->hidden('rating', array('value' => $aReviewUser['ReviewUser']['rating'], 'class' => 'review_point'));?>
                            <input value="<?php echo $aReviewUser['ReviewUser']['rating']; ?>" type="number" class="rating form-control hide" min="0" max="5" data-size="lg">
                        </div>
                    </div>
                    <div class="clear"></div>
                </li>
                <?php endif; ?>
                
                <li>
                    <div class="col-sm-12">
                        <label><?php echo (!empty($type) && $type == 'reply') ? __d('review', 'Your reply') : __d('review', 'Your review'); ?></label>
                    </div>
                    <div class="col-sm-12">
                        <?php echo $this->Form->textarea('content', array('value' => $aReviewUser['ReviewUser']['content'])); ?>
                    </div>
                    <div class="clear"></div>
                </li>
                <li>
                    <div class="col-sm-12">
                        <div id="images-uploader">
                            <div id="attachments_upload"></div>
                            <?php echo $this->Form->hidden('attachments');?>
                            <?php echo $this->Form->hidden('attachments_remain', array('value' => $aReviewUser['ReviewUser']['photos']));?>
                            <input type="button" class="button button-primary" id="triggerUpload" value="<?php echo __d('review', 'Upload Queued Files'); ?>">
                        </div>
                        
                        <?php $oPhotoHelper = MooCore::getInstance()->getHelper('Photo_Photo'); ?>
                        <?php $oReviewHelper = MooCore::getInstance()->getHelper('Review_Review'); ?>
                        <?php $aPhotos = $oReviewHelper->getPhotos($aReviewUser['ReviewUser']['photos']); ?>
                
                        <?php if(!empty($aPhotos)): ?>
                        <ul class="photo-list">
                            <?php foreach ($aPhotos as $aPhoto): ?>
                            <li class="photoItem">
                                <div class="p_2">
                                    <span class="layer_square" style="background-image:url(<?php echo $oPhotoHelper->getImage($aPhoto, array('prefix' => '150_square')); ?>)">
                                        <a href="javascript:void(0)" class="pull-right removePhoto" data-id="<?php echo $aPhoto['Photo']['id']; ?>">
                                            <i class="material-icons delete-icons">delete</i>
                                        </a>
                                    </span>
                                </div>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                    </div>
                </li>
            </ul>
        </form>
    </div>
    
    <div style="display:none;" class="error-message"></div>
</div>
<div class="modal-footer" style="text-align: left">
    <div class="col-sm-12">
        <a href="javascript:void(0);" class="btn btn-action" id="reviewBtn"><?php echo (!empty($aReviewUser['ReviewUser']['id'])) ? __d('review', 'Save') : __d('review', 'Post'); ?></a>
        <a href="javascript:void(0);" class="button button-action" data-dismiss="modal"><?php echo __d('review', 'Cancel'); ?></a>
    </div>
    <div class="clear"></div>
</div>

<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["mooReview"], function(mooReview) {
        $(document).ready(function() {
            mooReview.initWriteReview();
        });
    });
</script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('mooReview'), 'object' => array('mooReview'))); ?>
mooReview.initWriteReview();<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>
