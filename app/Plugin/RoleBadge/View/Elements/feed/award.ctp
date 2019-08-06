<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>

<?php $oRoleBadgeHelper = MooCore::getInstance()->getHelper('RoleBadge_RoleBadge'); ?>
<?php foreach($aAwards as $aAward): ?>
&nbsp;<a class="no-ajax tip" href="javascript:void(0);" original-title="<?php echo $aAward['AwardBadge']['description']; ?>"><img class="icon-role-badge-username-feed" src="<?php echo $oRoleBadgeHelper->getImage($aAward['AwardBadge']['thumbnail']); ?>"></a>
<?php endforeach; ?>