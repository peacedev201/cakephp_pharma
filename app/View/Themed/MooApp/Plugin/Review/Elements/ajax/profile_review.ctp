<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>

<div id="profile-content-wrap" class="bar-content">
    
    <?php echo $this->requestAction("reviews/reload/" . $aUser['User']['id'] . '/' . $this->theme, array('return')); ?>

    <div class="bar-content mooApp-profile-review">  
        <div class="content_center">
            <div class="p_m_10 profile-review-header">
                <h2><?php echo ($type == 'user') ? __d('review', 'Reviews') : __d('review', 'My Posted Reviews'); ?></h2>

                <?php if (!empty($uid) && $aUser['User']['id'] == $uid): ?>
                <div class="review_sort list_option">
                    <div class="dropdown">
                        <?php echo __d('review', 'View by'); ?>
                        <button id="dropdown-sort" class="dropdown-sort" data-target="#" data-toggle="dropdown">
                            <i class="material-icons">expand_more</i>
                        </button>
                        <ul role="menu" class="dropdown-menu browseReviews" aria-labelledby="dropdown-sort">
                            <li>
                                <a id="userReviewProfile" href="javascript:void(0)" data-url="<?php echo $this->request->base . '/reviews/profile/' . $aUser['User']['id'] . '/user'; ?>" rel="profile-content"><?php echo __d('review', 'Reviews'); ?></a>
                            </li>
                            <li>
                                <a id="reviewedReviewProfile" href="javascript:void(0)" data-url="<?php echo $this->request->base . '/reviews/profile/' . $aUser['User']['id'] . '/reviewed'; ?>" rel="profile-content"><?php echo __d('review', 'My Posted Reviews'); ?></a>
                            </li>
                        </ul>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($uid) && $aUser['User']['id'] == $uid && !empty($bShowProfileOption)): ?>
            <div class="review-status">
                <div class="pull-right">
                    <a href="javascript:void(0);" id="enableReview" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1" data-review="<?php echo (!empty($aReview) && empty($aReview['Review']['review_enable'])) ? 'enable' : 'disable'; ?>" data-type="<?php echo $type; ?>">
                        <?php echo (!empty($aReview) && empty($aReview['Review']['review_enable'])) ? __d('review', 'Enable Rating on My Profile') : __d('review', 'Disable Rating on My Profile'); ?>
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <div class="clear"></div>

            <div id="list-content">
                <?php echo $this->element('lists/reviews_list'); ?>
            </div>
        </div>
    </div>
    
    <script type="text/javascript">
        function doRefesh() {
            window.location.reload();
        }
    </script>
    
    <?php if($this->request->is('ajax')): ?>
    <script type="text/javascript">
        require(["mooReview"], function(mooReview) {
            mooReview.initOnViewAppProfile();
        });
    </script>
    <?php else: ?>
    <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('mooReview'), 'object' => array('mooReview'))); ?>
    mooReview.initOnViewAppProfile();<?php $this->Html->scriptEnd(); ?>
    <?php endif; ?>
</div>