<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>

<div class="bar-content">  
    <div class="content_center">
        
        <div class="post_body profile-review-header">
            <div class="mo_breadcrumb">
                <h2 class="header_h2"><?php echo ($type == 'user') ? __d('review', 'Reviews') : __d('review', 'My Posted Reviews'); ?></h2>

                <?php if (!empty($uid) && $aUser['User']['id'] == $uid): ?>
                <div class="review_sort list_option">
                    <div class="dropdown">
                        <?php echo __d('review', 'View by'); ?>
                        <button id="dropdown-sort" class="dropdown-sort" data-target="#" data-toggle="dropdown">
                            <i class="material-icons">expand_more</i>
                        </button>
                        <ul role="menu" class="dropdown-menu browseReviews" aria-labelledby="dropdown-sort">
                            <li>
                                <a id="userReviewProfile" href="<?php echo $aUser['User']['moo_href']; ?>" data-url="<?php echo $this->request->base . '/reviews/profile/' . $aUser['User']['id'] . '/user'; ?>" rel="profile-content"><?php echo __d('review', 'Reviews'); ?></a>
                            </li>
                            <li>
                                <a id="reviewedReviewProfile" href="<?php echo $aUser['User']['moo_href']; ?>" data-url="<?php echo $this->request->base . '/reviews/profile/' . $aUser['User']['id'] . '/reviewed'; ?>" rel="profile-content"><?php echo __d('review', 'My Posted Reviews'); ?></a>
                            </li>
                        </ul>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>
        
        <?php if (!empty($uid) && $aUser['User']['id'] == $uid && !empty($bShowProfileOption)): ?>
        <div class="post_body">
            <div class="mo_breadcrumb">
                <div class="pull-right">
                    <a href="javascript:void(0);" id="enableReview" class="btn btn-action" data-review="<?php echo (!empty($aReview) && empty($aReview['Review']['review_enable'])) ? 'enable' : 'disable'; ?>" data-type="<?php echo $type; ?>">
                        <?php echo (!empty($aReview) && empty($aReview['Review']['review_enable'])) ? __d('review', 'Enable Rating on My Profile') : __d('review', 'Disable Rating on My Profile'); ?>
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <div id="list-content">
            <?php echo $this->element('lists/reviews_list'); ?>
        </div>
    </div>
</div>