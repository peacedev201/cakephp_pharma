<?php $oRoleBadgeHelper = MooCore::getInstance()->getHelper('RoleBadge_RoleBadge'); ?>

<?php if(!empty($bShowAssign) || !empty($aUserBadges)): ?>
<div class="box2">
    <?php if(!empty($bShowAssign) || $title_enable): ?>
    <div class="row award-badge-header">
        <h3>
            <?php if($title_enable): echo (!empty($title)) ? $title : __d('role_badge', 'Badges'); endif; ?>
        </h3>
        
        <?php if(!empty($bShowAssign)): ?>
        <div class="dropdown list_option">
            <button type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="material-icons">more_vert</i>
            </button>

            <ul role="menu" class="dropdown-menu" aria-labelledby="dropdown-edit">
                <li>
                    <?php
                    $this->MooPopup->tag(array(
                        'href' => $this->request->base . '/awards/add_badge/' . $iUserId,
                        'innerHtml'=> __d('role_badge', 'Assign badge'),
                        'title' => __d('role_badge', 'Assign badge'),
                        'class' => 'asign-badge'
                    ));
                    ?>
                </li>
            </ul>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <div class="box_content">
        <ul class="list2 sidebar-badge">
            <?php if(!empty($aUserBadges)): ?>
                <?php foreach ($aUserBadges as $aUserBadge): ?>
                <li class="c_badge">
                    <a class="no-ajax tip" href="javascript::void(0);" original-title="<?php echo $aUserBadge['AwardBadge']['description']; ?>">
                        <img src="<?php echo $oRoleBadgeHelper->getImage($aUserBadge['AwardBadge']['thumbnail']); ?>"/>
                    </a>
                </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li class="text-center">
                    <p><?php echo __d('role_badge', 'No badge found.'); ?></p>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</div>
<?php endif; ?>


