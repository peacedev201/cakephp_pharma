<?php $oRoleBadgeHelper = MooCore::getInstance()->getHelper('RoleBadge_RoleBadge'); ?>

<?php if(!empty($bShowAssign) || !empty($aUserBadges)): ?>
<div class="bar-content moo-app-role-badge">  
    <div class="content_center content_center-header">
        <div class="p_m_10 award-badge-header">
            <h2><?php echo __d('role_badge', 'Award Badges'); ?></h2>
            
            <?php if(!empty($bShowAssign)): ?>
            <div class="dropdown list_option">
                <button type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="material-icons">more_vert</i>
                </button>

                <ul role="menu" class="dropdown-menu" aria-labelledby="dropdown-edit">
                    <li>
                        <a href="javascript:void(0);" class="badgeModalContent" data-url="<?php echo $this->request->base . '/awards/add_badge/' . $iUserId; ?>"><?php echo __d('role_badge', 'Assign badge'); ?></a>
                    </li>
                </ul>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="bar-content moo-app-role-badge">
    <div class="content_center">
        <div class="box_content box_content-list">
            <ul class="list2 sidebar-badge">
                <?php if (!empty($aUserBadges)): ?>
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
</div>

<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(['jquery'], function($) {
        $(document).ready(function() {
            $('.badgeModalContent').unbind('click');
            $('.badgeModalContent').click(function() {
                var data = $(this).data();
                $('#themeModal .modal-content').html('');
                $('#themeModal .modal-content').spin('small');
                $('#themeModal .modal-content').load(data.url, function () {
                    $('#themeModal .modal-content').spin(false);
                    $('#themeModal').modal('show');
                });
            });
        });
    });
</script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('jquery'), 'object' => array('$')));?>
$('.badgeModalContent').unbind('click');
$('.badgeModalContent').click(function() {
    var data = $(this).data();
    $('#themeModal .modal-content').html('');
    $('#themeModal .modal-content').spin('small');
    $('#themeModal .modal-content').load(data.url, function () {
        $('#themeModal .modal-content').spin(false);
        $('#themeModal').modal('show');
    });
});<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>

<?php endif; ?>


