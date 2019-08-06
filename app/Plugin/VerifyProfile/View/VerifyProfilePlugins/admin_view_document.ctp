<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>

<?php $oModelPhoto = MooCore::getInstance()->getModel('Photo.Photo'); ?>
<?php $oPhotoHelper = MooCore::getInstance()->getHelper('Photo_Photo'); ?>

<script>    
    $(document).ready(function() {
        $('#verifyButton').click(function() {
            disableButton('verifyButton');
            $.post("<?php echo $this->Html->url(array('admin' => false, 'plugin' => 'verify_profile', 'controller' => 'verify_profiles', 'action' => 'ajax_verify'));?>", $("#verifyForm").serialize(), function() {
                window.location.reload();
            });
            return false;
        });
        
        $("input:checkbox").uniform();
    });
    
    function loadUnverify(){
	$('#themeModal .modal-content').html('');
	$('#themeModal .modal-content').spin('small');	
	$('#themeModal .modal-content').load('<?php echo $this->Html->url(array("plugin" => "verify_profile", "controller" => "verify_profile_plugins", "action" => "admin_unverify", $aVerifyProfile['User']['id'])); ?>', function(){
	    $('#themeModal .modal-content').spin(false);
            $('#themeModal').modal('show');
	});
    }
</script>

<style>
    .verify-document{
        text-align: center;
    }
    
    .verify-document img{
        margin-bottom: 10px;
    }
</style>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title"><?php echo __d('verify_profile', 'View Document'); ?></h4>
</div>
<div class="modal-body">
    <form action="<?php echo $this->request->base?>/admin/users/edit" method="post" class="form-horizontal">
        <div class="form-body">
            <h4><?php echo __d('verify_profile', 'Basic Information'); ?></h4>
            <div class="form-group">
                <label class="control-label col-md-3"><?php echo $this->Moo->getItemPhoto(array('User' => $aVerifyProfile['User']), array("height" => "200px" , "class" => "img_wrapper", 'prefix' => '200_square')); ?><br /></label>
                <div class="col-md-9"></div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3"><?php echo __d('verify_profile', 'Registered Date');?>:</label>
                <div class="col-md-9">
                    <p class="form-control-static">
                        <?php echo $this->Moo->getTime($aVerifyProfile['User']['created'], Configure::read('core.date_format'), $utz); ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3"><?php echo __d('verify_profile', 'Last Online');?>:</label>
                <div class="col-md-9">
                    <p class="form-control-static">
                        <?php echo $this->Moo->getTime($aVerifyProfile['User']['last_login'], Configure::read('core.date_format'), $utz); ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3"><?php echo __d('verify_profile', 'Stats');?>:</label>
                <div class="col-md-9">
                    <p class="form-control-static">
                        <?php echo $aVerifyProfile['User']['photo_count']; ?> <?php echo __d('verify_profile', 'photos'); ?>,
                        <?php echo $aVerifyProfile['User']['friend_count']; ?> <?php echo __d('verify_profile', 'friends'); ?>,
                        <?php echo $aVerifyProfile['User']['blog_count']; ?> <?php echo __d('verify_profile', 'blogs'); ?>, 
                        <?php echo $aVerifyProfile['User']['topic_count']; ?> <?php echo __d('verify_profile', 'topics'); ?>, 
                        <?php echo $aVerifyProfile['User']['group_count']; ?> <?php echo __d('verify_profile', 'groups'); ?>, 
                        <?php echo $aVerifyProfile['User']['event_count']; ?> <?php echo __d('verify_profile', 'events'); ?>, 
                        <?php echo $aVerifyProfile['User']['video_count']; ?> <?php echo __d('verify_profile', 'videos'); ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3"><?php echo __d('verify_profile', 'Role'); ?>:</label>
                <div class="col-md-9">
                    <p class="form-control-static">
                        <?php echo $sRole; ?>
                    </p>
                </div>
            </div>
            <hr>
            <h4><?php echo __d('verify_profile', 'Required Information'); ?></h4>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('verify_profile', 'Full Name'); ?></label>
                <div class="col-md-9">
                    <p class="form-control-static">
                        <?php echo $aVerifyProfile['User']['name']; ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('verify_profile', 'Email Address'); ?></label>
                <div class="col-md-9">
                    <p class="form-control-static">
                        <?php echo $aVerifyProfile['User']['email']; ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('verify_profile', 'Birthday'); ?></label>
                <div class="col-md-9">
                    <p class="form-control-static">
                        <?php echo strftime('%B %d, %Y', strtotime($aVerifyProfile['User']['birthday'])); ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('verify_profile', 'Gender'); ?></label>
                <div class="col-md-9">
                    <p class="form-control-static">
                        <?php echo __d('verify_profile', $aVerifyProfile['User']['gender']); ?>
                    </p>
                </div>
            </div>
            <?php 
                $aPhotosId = explode(',', $aVerifyProfile['VerifyProfile']['images']);
                $aPhotos = $oModelPhoto->find('all', array('conditions' => array('Photo.id' => $aPhotosId), 'fields' => array('Photo.thumbnail', 'Photo.year_folder', 'Photo.created')));
            ?>
            <?php if(!empty($aPhotos)): ?>
            <hr>
            <h4><?php echo __d('verify_profile', 'Verification Information'); ?></h4>
            <div class="form-group verify-document">
                <div class="col-md-12">
                    <?php foreach ($aPhotos as $aPhoto): ?>
                        <img src="<?php echo $oPhotoHelper->getImage($aPhoto, array('prefix' => '450')); ?>">
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </form>
    <form id="verifyForm" class="form-horizontal" role="form">
        <input type="hidden" name="id" value="<?php echo $aVerifyProfile['User']['id']; ?>">
    </form>
    <div class="alert alert-danger error-message" style="display: none; margin-top: 10px;"></div>
</div>
<div class="modal-footer">
    <?php if($aVerifyProfile['VerifyProfile']['status'] == 'pending'): ?>
        <a href="javascript:void(0)" id="verifyButton" class="btn btn-action"><?php echo __d('verify_profile', 'Verify'); ?></a>
        <a href="javascript:void(0)" onclick="loadUnverify();" class="btn btn-action"><?php echo __d('verify_profile', 'Deny'); ?></a>
    <?php elseif($aVerifyProfile['VerifyProfile']['status'] == 'verified'): ?>
        <a href="javascript:void(0)" onclick="loadUnverify();" class="btn btn-action"><?php echo __d('verify_profile', 'Unverify'); ?></a>
    <?php endif; ?>
    <button type="button" class="btn default" data-dismiss="modal"><?php echo __d('verify_profile', 'Close'); ?></button>
</div>