<?php $oRoleBadgeHelper = MooCore::getInstance()->getHelper('RoleBadge_RoleBadge'); ?>

<div class="modal-header">
    <h4 class="modal-title"><?php echo __d('role_badge', 'Which badges do you want to assign to this member?'); ?></h4>
</div>

<div class="modal-body">
    <?php if(!empty($aAwardBadges)): ?>
        <form id="addBadge" class="form-horizontal" method="post" enctype="multipart/form-data">
            <?php echo $this->Form->hidden('user_id', array('value' => $uid)); ?>
            <table class="table table-bordered award-badge">
                <tbody>
                    <?php foreach($aAwardBadges as $aAwardBadge): ?>
                    <tr>
                        <td><?php echo $aAwardBadge['AwardBadge']['name']; ?></td>
                        <td class="text-center">
                            <img src="<?php echo $oRoleBadgeHelper->getImage($aAwardBadge['AwardBadge']['thumbnail']); ?>"/>
                        </td>
                        <td class="text-center">
                            <input type="checkbox" name="award_badge_id[]" value="<?php echo $aAwardBadge['AwardBadge']['id']; ?>" <?php echo in_array($aAwardBadge['AwardBadge']['id'] , $aAwardUsers) ? 'checked' : ''; ?>>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </form>
    <?php else: ?>
        <p><?php echo __d('role_badge', 'Please contact admin add award badges.'); ?></p>
    <?php endif; ?>
</div>

<div class="moo-app-role-badge-modal-footer modal-footer">
    <div class="col-md-12">
        <?php if(!empty($aAwardBadges)): ?>
        <a href="javascript:void(0);" id="btnSave" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored"><?php echo __d('role_badge', 'Done'); ?></a>
        <?php endif; ?>
        
        <a href="javascript:void(0);" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1" data-dismiss="modal"><?php echo __d('role_badge', 'Close'); ?></a>
    </div>
    <div class="clear"></div>
</div>

<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(['jquery', 'mooButton'], function($, mooButton) {
        $(document).ready(function() {
            $('#btnSave').unbind('click');
            $('#btnSave').click(function() {
                $('#btnSave').spin('tiny');
                mooButton.disableButton('btnSave');
                $.post('<?php echo $this->request->base . '/awards/add_badge_save'; ?>', $("#addBadge").serialize(), function() {
                    window.location.reload();
                });
            });
        });
    });
</script>
<?php endif; ?>

