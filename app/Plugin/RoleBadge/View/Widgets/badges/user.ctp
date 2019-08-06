<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */

?>

<?php if(!empty($aRoleUsers)): ?>
<div class="box2">
    <?php if($title_enable): ?>
    <h3><?php echo (!empty($title)) ? $title : 'User Roles'; ?></h3>
    <?php endif; ?>
    
    <div class="box_content box_online_user">
        <ul class="list_block">
            <?php foreach ($aRoleUsers as $aUser): ?>
            <li>
                <?php echo $this->Moo->getItemPhoto(array('User' => $aUser['User']), array('prefix' => '50_square'), array('class' => Configure::read('core.profile_popup') ? '' : 'img_wrapper2 user_avatar_large tip', 'title' => $aUser['User']['name'])); ?>
            </li>
            <?php endforeach; ?>
        </ul>
        <div class='clear'></div>
    </div>
</div>
<?php endif; ?>