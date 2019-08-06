<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>

<li class="mdl-menu__item mdl-js-ripple-effect">
    <a href="<?php echo $this->request->here; ?>" id="reviewProfile" data-url="<?php echo $this->request->base . '/reviews/profile/' . $user['User']['id'] . '/user'; ?>" rel="profile-content">
        <?php echo __d('review', 'Reviews'); ?>
    </a>
</li>

<?php if(!empty($iReviewUserId)): ?>
<li>
    <a href="<?php echo $this->request->here; ?>" id="reviewDetail" data-url="<?php echo $this->request->base . '/reviews/profile/' . $user['User']['id'] . '/user/' . $iReviewUserId; ?>" rel="profile-content"></a>
</li>
<?php endif; ?>

<?php if($bLoadReviewScript): ?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('mooReview'), 'object' => array('mooReview'))); ?>

<?php if(!empty($iReviewUserId)): ?>
mooReview.initReviewDetail();
<?php endif; ?>

<?php if(!empty($bLoadReviewProfile)): ?>
mooReview.initReviewProfile();
<?php endif; ?>

<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>
