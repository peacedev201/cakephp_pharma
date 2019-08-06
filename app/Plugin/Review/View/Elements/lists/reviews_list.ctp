<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>

<?php $oPhotoHelper = MooCore::getInstance()->getHelper('Photo_Photo'); ?>
<?php $oReviewHelper = MooCore::getInstance()->getHelper('Review_Review'); ?>

<ul class="list6 review-content-list">
<?php if (!empty($aUserReviews) && count($aUserReviews) > 0) : ?>
    <?php foreach ($aUserReviews as $aUserReview): ?>
    <li class="full_content p_m_10">
        <?php echo $this->Moo->getItemPhoto(array('User' => $aUserReview['User']), array('prefix' => '100_square'), array('class' => 'img_wrapper2 thumb_mobile', 'width' => '75')); ?>
        
        <div class="review-info">
            <?php echo $this->Moo->getName($aUserReview['User']); ?>

            <?php if(!empty($uid)): ?>
            <div class="dropdown list_option">
                <button type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="material-icons">more_vert</i>
                </button>
                <ul class="dropdown-menu" role="menu">
                    <?php if(!empty($uid) && (($aUserReview['ReviewUser']['user_id'] == $uid) || (!empty($cuser) && $cuser['Role']['is_admin']))): ?>
                    <li>
                        <?php
                        $this->MooPopup->tag(array(
                            'href' => $this->request->base . '/reviews/write/' . $aUserReview['Review']['user_id'] . '/' . $aUserReview['ReviewUser']['id'] . '/' . $type,
                            'innerHtml'=> __d('review', 'Edit Review'),
                            'title' => __d('review', 'Edit Review')
                        ));
                        ?>	
                    </li>
                    <li>
                        <a href="javascript:void(0);" class="deleteReview" data-id="<?php echo $aUserReview['ReviewUser']['id']; ?>" data-delete="review" data-type="<?php echo $type; ?>"><?php echo __d('review', 'Delete Review'); ?></a>
                    </li>
                    <li class="seperate"></li>
                    <?php endif; ?>
                    
                    <?php if(!empty($uid) && $type == 'user' && $aUserReview['Review']['user_id'] == $uid && empty($aUserReview['ReviewReply']['id'])): ?>
                    <li>
                        <?php
                        $this->MooPopup->tag(array(
                            'href' => $this->request->base . '/reviews/reply/' . $aUserReview['ReviewUser']['id'],
                            'innerHtml'=> __d('review', 'Reply Review'),
                            'title' => __d('review', 'Reply Review')
                        ));
                        ?>	
                    </li>
                    <?php endif; ?>
                    
                    <li>
                        <?php
                        $this->MooPopup->tag(array(
                            'href'=>$this->Html->url(array("controller" => "reports", "action" => "ajax_create", "plugin" => false, 'Review.ReviewUser', $aUserReview['ReviewUser']['id'])),
                            'innerHtml'=> __d('review', 'Report Review'),
                            'title' => __d('review', 'Report Review')
                        ));
                        ?>
                    </li>
                </ul>
            </div>
            <?php endif; ?>

            <div class="extra_info">
                <div class="review-time">
                    <?php echo $this->Moo->getTime($aUserReview['ReviewUser']['created'], Configure::read('core.date_format'), $utz); ?>
                </div>
                <div class="review-star-rating">
                    <input readonly value="<?php echo $aUserReview['ReviewUser']['rating']; ?>" type="number" class="rating form-control hide" data-stars="5" data-size="xs">
                </div>
            </div>

            <div class="review-detail-content">
                <div class="content">
                    <div class="review-truncate" data-more-text="<?php echo __d('review', 'Show More'); ?>" data-less-text="<?php echo __d('review', 'Show Less'); ?>">
                        <?php echo htmlspecialchars($aUserReview['ReviewUser']['content']); ?>
                    </div>
                </div>
                
                <?php $aPhotos = $oReviewHelper->getPhotos($aUserReview['ReviewUser']['photos']); ?>
                
                <?php if(!empty($aPhotos)): ?>
                <ul class="photo-list">
                    <?php foreach ($aPhotos as $aPhoto): ?>
                    <li class="photoItem">
                        <div class="p_2">
                            <?php
                            $this->MooPopup->tag(array(
                                'style' => 'background-image:url(' . $oPhotoHelper->getImage($aPhoto, array('prefix' => '150_square')) . ')',
                                'href' => $this->request->base . '/reviews/photo_view/' . $aPhoto['Photo']['id'],
                                'class' => 'layer_square',
                            ));
                            ?>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
                
                <div class="clear"></div>
                
                <?php if(!empty($aUserReview['ReviewReply']['id'])): ?>
                <div class="content content-reply">
                    <div class="review-truncate" data-more-text="<?php echo __d('review', 'Show More'); ?>" data-less-text="<?php echo __d('review', 'Show Less'); ?>">
                        <strong style="color: red;"><?php echo __d('review', 'Replied'); ?>:</strong>&nbsp;
                        <?php echo htmlspecialchars($aUserReview['ReviewReply']['content']); ?>
                    </div>
                    
                    <?php if(!empty($uid) && (($aUserReview['Review']['user_id'] == $uid) || (!empty($cuser) && $cuser['Role']['is_admin']))): ?>
                    <div class="dropdown list_option list-option-reply">
                        <button type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="material-icons">more_vert</i>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li>
                                <?php
                                $this->MooPopup->tag(array(
                                    'href' => $this->request->base . '/reviews/reply/' . $aUserReview['ReviewUser']['id'],
                                    'innerHtml'=> __d('review', 'Edit Reply'),
                                    'title' => __d('review', 'Edit Reply')
                                ));
                                ?>
                            </li>
                            <li>
                                <a href="javascript:void(0);" class="deleteReview" data-id="<?php echo $aUserReview['ReviewReply']['id']; ?>" data-delete="reply" data-type="<?php echo $type; ?>"><?php echo __d('review', 'Delete Reply'); ?></a>
                            </li>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>
                
                <?php $aPhotos = $oReviewHelper->getPhotos($aUserReview['ReviewReply']['photos']); ?>
                
                <?php if(!empty($aPhotos)): ?>
                <ul class="photo-list">
                    <?php foreach ($aPhotos as $aPhoto): ?>
                    <li class="photoItem">
                        <div class="p_2">
                            <?php
                            $this->MooPopup->tag(array(
                                'style' => 'background-image:url(' . $oPhotoHelper->getImage($aPhoto, array('prefix' => '150_square')) . ')',
                                'href' => $this->request->base . '/reviews/photo_view/' . $aPhoto['Photo']['id'],
                                'class' => 'layer_square',
                            ));
                            ?>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
                
                <div class="clear"></div>
                <?php endif; ?>
                
            </div>
        </div>
    </li>
    <?php endforeach; ?>
<?php else: ?>
    <li class="full_content p_m_10"><div class="clear text-center"><?php echo __d('review', 'No results found'); ?></div></li>
<?php endif; ?>
        
<?php if (!empty($more_result) && !empty($more_url)): ?>
    <?php $this->Html->viewMore($more_url) ?>
<?php endif; ?>
</ul>

<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["mooReview"], function(mooReview) {
        $(document).ready(function() {
            mooReview.initBrowseReview();
        });
    });
</script>
<?php endif; ?>