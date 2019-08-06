<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
?>

<?php __d('verify_profile', 'Profile Verify'); ?>

<?php
    $this->Html->addCrumb(__d('verify_profile', 'Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('verify_profile', 'Profile Verify Settings'), array('controller' => 'verify_profile_settings', 'action' => 'admin_index'));
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Profile Verify'));
    $this->end();
?>

<?php $oVerifyProfileHelper = MooCore::getInstance()->getHelper('VerifyProfile_VerifyProfile'); ?>

<?php $this->Html->scriptStart(array('inline' => false)); ?>
    function addGroup() {
        if($('#verified_group :selected').val() && $('#unverified_group :selected').val()){
            var td1 = "<td>" + $('#verified_group :selected').text() + "<input type='hidden' value='" + $('#verified_group :selected').val() + "' name='verified_group_mapping[]' /></td>";
            var td2 = "<td>" + $('#unverified_group :selected').text() + "<input type='hidden' value='" + $('#unverified_group :selected').val() + "' name='unverified_group_mapping[]' /></td>";
            var aRow = "<tr>" + td2 + td1 + "<td><a href='javascript:void(0);' onclick='removeGroup(this)' class='btn btn-gray'><?php echo __d('verify_profile', 'Remove'); ?></a></td></tr>";
            $('#group_mapping').find('table').first().find('tfoot').append(aRow);
            $('#verified_group :selected').remove();
            $('#unverified_group :selected').remove();
        }
    }
    
    function removeGroup(obj) {
        var oParent = $(obj).parents('tr');
        
        var o1_text = $(oParent.find('td')[0]).text();
        var o1_val = $(oParent.find('td')[0]).find('input:first').val();

        var o2_text = $(oParent.find('td')[1]).text();
        var o2_val = $(oParent.find('td')[1]).find('input:first').val();

        $('#unverified_group').append('<option value="' + o1_val + '">' + o1_text + '</option>');
        $('#verified_group').append('<option value="' + o2_val + '" >' + o2_text + '</option>');

        oParent.remove();
    }
    
    $(document).ready(function(){
        $('#document_request_verification').click(function() {
            $("#document_request_verification_action").toggle(this.checked);
        });
        
        $('#auto_verify_after_review').click(function() {
            $("#auto_verify_after_review_action").toggle(this.checked);
        });
    });
<?php $this->Html->scriptEnd(); ?>

<style>
    #profile-verify .intergration-setting .input.checkbox .checker{
        padding-top: 0px;
        margin-right: 5px !important;
    }
    
    #profile-verify .intergration-setting .radio-setting{
        padding-top: 0px;
        margin-left: 5px;
    }
    
    .input-file{
        padding: 4px 0px;
    }
    
    #group_mapping .table-bordered > tfoot > tr > td{
        padding-left: 25px;
    }
    
    #group_mapping .table-bordered > tfoot > tr > td:last-child{
        padding-left: 5px;
    }
</style>

<div class="portlet-body form">
    <div class="portlet-tabs">
        <div class="tabbable tabbable-custom boxless tabbable-reversed">
            <?php echo$this->Moo->renderMenu('VerifyProfile', __d('verify_profile', 'Settings')); ?>
            <div class="row" style="padding-top: 10px;">
                <div class="col-md-12">
                    <div class="tab-content">
                        <div class="tab-pane active" id="profile-verify">
                            <form id="settingForm" class="form-horizontal intergration-setting" action="<?php echo $this->Html->url(array('plugin' => 'verify_profile', 'controller' => 'verify_profile_settings', 'action' => 'admin_save')); ?>" method="post" enctype="multipart/form-data">                           	   
                                <div class="form-body">
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">
                                            <?php echo __d('verify_profile', 'Enable Profile Verify'); ?>                          
                                        </label>
                                        <div class="col-md-7">
                                            <?php
                                            	echo $this->Form->input('enable', array(
                                                    'type' => 'checkbox', 
                                                    'checked' => Configure::read('VerifyProfile.verify_profile_enable'),
                                                    'label' => __d('verify_profile', 'Enable?'),                                    
                                                )); 
                                            ?>                                                                                    
                                        </div>								            
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">
                                            <?php echo __d('verify_profile', 'Enable auto verify based on user reviews');?>                          
                                        </label>
                                        <div class="col-md-7">
                                            <?php
                                            	echo $this->Form->input('auto_verify_after_review', array(
                                                    'type' => 'checkbox', 
                                                    'checked' => Configure::read('VerifyProfile.verify_profile_auto_verify_after_review'),
                                                    'label' => __d('verify_profile', 'Enable?'),                                    
                                                )); 
                                            ?>                                                                                    
                                        </div>								            
                                    </div>
                                    <div id="auto_verify_after_review_action"<?php echo Configure::read('VerifyProfile.verify_profile_auto_verify_after_review') ? '' : ' style="display: none"'; ?>>
                                        <?php $oModelPlugin = MooCore::getInstance()->getModel('Plugin'); ?>
                                        <?php $aPlugin = $oModelPlugin->findByKey('Review', array('enabled')); ?>
                                        <?php if(empty($aPlugin)): ?>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">&nbsp;</label>
                                            <div class="col-md-7">
                                                <div class="alert alert-danger error-message">
                                                    <?php echo __d('verify_profile', 'You need to install user review plug-in to enabled this option.'); ?>
                                                </div>
                                            </div>								            
                                        </div>
                                        <?php elseif($aPlugin['Plugin']['enabled'] && Configure::read('Review.review_enabled')): ?>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">
                                                <?php echo __d('verify_profile', 'Min review'); ?>                          
                                            </label>
                                            <div class="col-md-7">
                                                <?php
                                                    echo $this->Form->text('number_review', array(
                                                        'label' => '',
                                                        'class' => 'form-control',
                                                        'value' => Configure::read('VerifyProfile.verify_profile_number_review')                                   
                                                    )); 
                                                ?>                                                                                    
                                            </div>								            
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">
                                                <?php echo __d('verify_profile', 'Min average score (max is 5)'); ?>                          
                                            </label>
                                            <div class="col-md-7">
                                                <?php
                                                    echo $this->Form->text('average_review', array(
                                                        'label' => '',
                                                        'class' => 'form-control',
                                                        'value' => Configure::read('VerifyProfile.verify_profile_average_review')                                   
                                                    )); 
                                                ?>                                                                                    
                                            </div>								            
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">
                                                <?php echo __d('verify_profile', 'Enable auto unverified if verified member does not match above settings');?>                          
                                            </label>
                                            <div class="col-md-7">
                                                <?php
                                                    echo $this->Form->input('auto_unverify_after_review', array(
                                                        'type' => 'checkbox', 
                                                        'checked' => Configure::read('VerifyProfile.verify_profile_auto_unverify_after_review'),
                                                        'label' => __d('verify_profile', 'Enable?'),                                    
                                                    )); 
                                                ?>                                                                                    
                                            </div>								            
                                        </div>
                                        <?php else: ?>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">&nbsp;</label>
                                            <div class="col-md-7">
                                                <div class="alert alert-danger error-message">
                                                    <?php echo __d('verify_profile', 'You need to enabled user review plug-in to enabled this option.'); ?>
                                                </div>
                                            </div>								            
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">
                                            <?php echo __d('verify_profile', 'Enable Documents Requirement for Verification Request');?>                          
                                        </label>
                                        <div class="col-md-7">
                                            <?php
                                            	echo $this->Form->input('document_request_verification', array(
                                                    'type' => 'checkbox', 
                                                    'checked' => Configure::read('VerifyProfile.verify_profile_document_request_verification'),
                                                    'label' => __d('verify_profile', 'Enable?'),                                    
                                                )); 
                                            ?>                                                                                    
                                        </div>								            
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">
                                            <?php echo __d('verify_profile', 'Information Needed Verification');?>                          
                                        </label>
                                        <div class="col-md-7">
                                            <?php
                                                echo $this->Form->input('full_name', array(
                                                    'type' => 'checkbox', 
                                                    'checked' => Configure::read('VerifyProfile.verify_profile_full_name'),
                                                    'label' => __d('verify_profile', 'Full Name'),                                    
                                                )); 
                                            ?>
                                            <?php
                                                echo $this->Form->input('avatar', array(
                                                    'type' => 'checkbox', 
                                                    'checked' => Configure::read('VerifyProfile.verify_profile_avatar'),
                                                    'label' => __d('verify_profile', 'Avatar'),                                    
                                                )); 
                                            ?>
                                            <?php
                                                echo $this->Form->input('birthday', array(
                                                    'type' => 'checkbox', 
                                                    'checked' => Configure::read('VerifyProfile.verify_profile_birthday'),
                                                    'label' => __d('verify_profile', 'Birthday'),                                    
                                                )); 
                                            ?>
                                            <?php
                                                echo $this->Form->input('gender', array(
                                                    'type' => 'checkbox', 
                                                    'checked' => Configure::read('VerifyProfile.verify_profile_gender'),
                                                    'label' => __d('verify_profile', 'Gender'),                                    
                                                )); 
                                            ?>
                                        </div>								            
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">
                                            <?php echo __d('verify_profile', 'The Badge Shows at');?>                          
                                        </label>
                                        <div class="col-md-7">
                                            <?php
                                                echo $this->Form->input('show_activity_feed', array(
                                                    'type' => 'checkbox', 
                                                    'checked' => Configure::read('VerifyProfile.verify_profile_show_activity_feed'),
                                                    'label' => __d('verify_profile', 'Activity Feed'),                                    
                                                )); 
                                            ?>
                                            <?php
                                                echo $this->Form->input('show_profile_page', array(
                                                    'type' => 'checkbox', 
                                                    'checked' => Configure::read('VerifyProfile.verify_profile_show_profile_page'),
                                                    'label' => __d('verify_profile', 'Profile Page'),                                    
                                                )); 
                                            ?>
                                            <?php
                                                echo $this->Form->input('show_profile_popup', array(
                                                    'type' => 'checkbox', 
                                                    'checked' => Configure::read('VerifyProfile.verify_profile_show_profile_popup'),
                                                    'label' => __d('verify_profile', 'Profile Popup'),                                    
                                                )); 
                                            ?>
                                        </div>								            
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">
                                            <?php echo __d('verify_profile', 'Verify Badge'); ?>                          
                                        </label>
                                        <div class="col-md-7">
                                            <div class="input-file">
                                                <input type="file" name="Filedata[badge]">
                                            </div>
                                            <img src="<?php echo $oVerifyProfileHelper->getImageSetting(Configure::read('VerifyProfile.verify_profile_badge_image'))?>">
                                        </div>								            
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">
                                            <?php echo __d('verify_profile', 'Show Unverify Badge'); ?>                          
                                        </label>
                                        <div class="col-md-7">
                                            <?php
                                                echo $this->Form->radio('unverify', array(1 => __d('verify_profile', 'Yes'), 0 => __d('verify_profile', 'No')), array('separator' => '<br/>', 'value' => Configure::read('VerifyProfile.verify_profile_unverify'), 'legend' => false, 'label' => array('class' => 'radio-setting'))); 
                                            ?>
                                        </div>							            
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">
                                            <?php echo __d('verify_profile', "Unverify Badge Image"); ?>                          
                                        </label>
                                        <div class="col-md-7">
                                            <div class="input-file">
                                                <input type="file" name="Filedata[unverify]">
                                            </div>
                                            <img src="<?php echo $oVerifyProfileHelper->getImageSetting(Configure::read('VerifyProfile.verify_profile_unverify_image'))?>">
                                        </div>								            
                                    </div>
                                    <div id="document_request_verification_action"<?php echo Configure::read('VerifyProfile.verify_profile_document_request_verification') ? '' : ' style="display: none"'; ?>>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">
                                                <?php echo __d('verify_profile', 'Enable Passport Option'); ?>                          
                                            </label>
                                            <div class="col-md-7">
                                                <?php
                                                    echo $this->Form->radio('passport', array(1 => __d('verify_profile', 'Yes'), 0 => __d('verify_profile', 'No')), array('separator' => '<br/>', 'value' => Configure::read('VerifyProfile.verify_profile_passport'), 'legend' => false, 'label' => array('class' => 'radio-setting'))); 
                                                ?>
                                            </div>							            
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">
                                                <?php echo __d('verify_profile', "Passport's Sample Image"); ?>                          
                                            </label>
                                            <div class="col-md-7">
                                                <div class="input-file">
                                                    <input type="file" name="Filedata[passport]">
                                                </div>
                                                <img width="140" src="<?php echo $oVerifyProfileHelper->getImageSetting(Configure::read('VerifyProfile.verify_profile_passport_image'))?>">
                                            </div>								            
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">
                                                <?php echo __d('verify_profile', 'Enable Driver License Option'); ?>                          
                                            </label>
                                            <div class="col-md-7">
                                                <?php
                                                    echo $this->Form->radio('driver', array(1 => __d('verify_profile', 'Yes'), 0 => __d('verify_profile', 'No')), array('separator' => '<br/>', 'value' => Configure::read('VerifyProfile.verify_profile_driver'), 'legend' => false, 'label' => array('class' => 'radio-setting'))); 
                                                ?>
                                            </div>								            
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">
                                                <?php echo __d('verify_profile', "Driver License's Sample Image"); ?>                          
                                            </label>
                                            <div class="col-md-7">
                                                <div class="input-file">
                                                    <input type="file" name="Filedata[driver]">
                                                </div>
                                                <img width="140" src="<?php echo $oVerifyProfileHelper->getImageSetting(Configure::read('VerifyProfile.verify_profile_driver_image'))?>">
                                            </div>								            
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">
                                                <?php echo __d('verify_profile', 'Enable ID Card Option'); ?>                          
                                            </label>
                                            <div class="col-md-7">
                                                <?php
                                                    echo $this->Form->radio('card', array(1 => __d('verify_profile', 'Yes'), 0 => __d('verify_profile', 'No')), array('separator' => '<br/>', 'value' => Configure::read('VerifyProfile.verify_profile_card'), 'legend' => false, 'label' => array('class' => 'radio-setting'))); 
                                                ?>
                                            </div>								            
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">
                                                <?php echo __d('verify_profile', "ID Card's Sample Image"); ?>                          
                                            </label>
                                            <div class="col-md-7">
                                                <div class="input-file">
                                                    <input type="file" name="Filedata[card]">
                                                </div>
                                                <img width="140" src="<?php echo $oVerifyProfileHelper->getImageSetting(Configure::read('VerifyProfile.verify_profile_card_image'))?>">
                                            </div>								            
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">
                                                <?php echo __d('verify_profile', 'Enable Deny Photo Option'); ?>                          
                                            </label>
                                            <div class="col-md-7">
                                                <?php
                                                    echo $this->Form->radio('deny', array(1 => __d('verify_profile', 'Yes'), 0 => __d('verify_profile', 'No')), array('separator' => '<br/>', 'value' => Configure::read('VerifyProfile.verify_profile_deny'), 'legend' => false, 'label' => array('class' => 'radio-setting'))); 
                                                ?>
                                            </div>								            
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">
                                                <?php echo __d('verify_profile', "Deny Photo's Sample Image"); ?>                          
                                            </label>
                                            <div class="col-md-7">
                                                <div class="input-file">
                                                    <input type="file" name="Filedata[deny]">
                                                </div>
                                                <img width="140" src="<?php echo $oVerifyProfileHelper->getImageSetting(Configure::read('VerifyProfile.verify_profile_deny_image'))?>">
                                            </div>								            
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">
                                                <?php echo __d('verify_profile', 'Number of Document'); ?>                          
                                            </label>
                                            <div class="col-md-7">
                                                <?php
                                                    echo $this->Form->text('document', array(
                                                        'label' => '',
                                                        'class' => 'form-control',
                                                        'value' => Configure::read('VerifyProfile.verify_profile_document')                                   
                                                    )); 
                                                ?>                                                                                    
                                            </div>								            
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">
                                            <?php echo __d('verify_profile', 'User Group Mapping'); ?>                          
                                        </label>
                                        <div id="group_mapping" class="col-md-7">
                                            <table class="table table-striped table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th data-hide="phone"><?php echo __d('verify_profile', 'Unverified User Group');?></th>
                                                        <th data-hide="phone"><?php echo __d('verify_profile', 'Verified User Group');?></th>
                                                        <th data-hide="phone"><?php echo __d('verify_profile', 'Action');?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="gradeX even">
                                                        <td>
                                                            <?php echo $this->Form->select('unverified_group', $aURoles, array('class' => 'form-control','empty' => false)); ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $this->Form->select('verified_group', $aVRoles, array('class' => 'form-control','empty' => false)); ?>
                                                        </td>
                                                        <td>
                                                            <input type="button" value="<?php echo __d('verify_profile', 'Add');?>" class="btn btn-circle btn-action" onclick="addGroup();">
                                                        </td>
                                                    </tr>
                                                </tbody>
                                                <tfoot><?php echo $sRowGroup; ?></tfoot>
                                            </table>                                                                                
                                        </div>								            
                                    </div>
                                </div>
                                <div class="form-actions">
                                    <div class="row">
                                        <div class="col-md-offset-3 col-md-9">
                                            <input type="button" id="saveButton" class="btn btn-circle btn-action" value="<?php echo __d('verify_profile', 'Save Settings');?>">
                                            <a href="javascript:void(0)" onclick="mooConfirm('<?php echo __d('verify_profile', 'Are you sure you want to reset all setting?') ?>', '<?php echo $this->Html->url(array('plugin' => 'verify_profile', 'controller' => 'verify_profile_settings', 'action' => 'admin_reset'));?>')" class="btn btn-circle btn-action" ><?php echo __d('verify_profile', 'Reset All');?></a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="alert alert-danger error-message" style="display: none; margin-top: 10px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
<?php $this->Html->scriptStart(array('inline' => false)); ?>
$(document).ready(function() {
    $('#saveButton').click(function() {
        disableButton('saveButton');
        $.post("<?php echo $this->Html->url(array('plugin' => 'verify_profile', 'controller' => 'verify_profile_settings', 'action' => 'admin_save_validate')); ?>", $("#settingForm").serialize(), function(data) {
            enableButton('saveButton');
            var json = $.parseJSON(data);

            if (json.result === 1) {
                $("#settingForm").submit();
            } else {
                $(".error-message").show();
                $(".error-message").html('<strong>Error!</strong> ' + json.message);
            }
        });

        return false;
    });
});
<?php $this->Html->scriptEnd(); ?>
</script>