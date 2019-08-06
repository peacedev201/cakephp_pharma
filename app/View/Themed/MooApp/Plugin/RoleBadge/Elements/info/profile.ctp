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
<div class="item_stat extra_info p-role-badge-tooltip"><img class="icon-role-badge-tooltip-mobile" src="<?php echo $oRoleBadgeHelper->getImage($aRoleBadge['RoleBadge']['mobile_feed']); ?>"></div>