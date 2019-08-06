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
<a href="javascript:void(0)" class="p-role-badge"><img class="icon-role-badge" src="<?php echo $oRoleBadgeHelper->getImage($aRoleBadge['RoleBadge']['mobile_profile']); ?>"></a>