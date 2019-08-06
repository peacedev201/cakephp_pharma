<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["jquery","mooUser"], function($,mooUser) {
        mooUser.initOnUserProfile();
        mooUser.initOnProfileEdit();
        mooUser.resendValidationLink();
    });
</script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery','mooUser'), 'object' => array('$', 'mooUser'))); ?>
    mooUser.initOnUserProfile();
    mooUser.initOnProfileEdit();
    mooUser.resendValidationLink();
<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>

<?php
$this->addPhraseJs(array(
    'resend_validation'=>__('Resend validation email'),
    'resend'=>__('Resend'),
    'your_email_has_been_updated'=>__('Your email has been updated'),
    'something_went_wrong'=>__('Something went wrong, please try again'),
));
?>

<?php $this->setNotEmpty('west');?>
<?php $this->start('west'); ?>
<div class="bar-content">
    <div class="profile-info-menu">
        <?php echo $this->element('profilenav', array("cmenu" => "profile"));?>
    </div>
</div>
<?php $this->end(); ?>
<?php $this->MooPopup->register('themeModal');?>
<div class="bar-content ">
    <div class="content_center profile-info-edit profile-edit-step-1">
        <form id="form_edit_user" action="<?php echo $this->request->base?>/users/profile" method="post">
        <div id="center" class="post_body">
            <div class="mo_breadcrumb">
                 <h1><?php echo __('Profile Information')?></h1>
                 <a href="<?php echo $this->request->base?>/users/view/<?php echo $uid?>" class="topButton button button-action button-mobi-top"><?php echo __('View Profile')?></a>
            </div>
            <div class="full_content">
                <div class="content_center">

                    <div class="edit-profile-section">
                        <h2><?php echo __('Contact Information (Name, Email, Mobile)')?></h2>
                        <ul class="">
                            <li>
                                <div class="col-sm-3">
                                    <label><?php echo __('Full Name')?></label>
                                </div>
                                <div class="col-sm-9">
                                    <?php
                                    if ( Configure::read('core.name_change') )
                                        echo $this->Form->text('name', array('value' => $cuser['name']));
                                    else
                                    {
                                        echo $this->Form->hidden('name', array('value' => $cuser['name']));
                                        echo $cuser['name'];
                                    }
                                    ?>
                                </div>
                                <div class="clear"></div>
                            </li>
                            <li>
                                <div class="col-sm-3">
                                    <label><?php echo __('Login Email')?></label>
                                </div>
                                <div class="col-sm-5">
                                    <?php echo $this->Form->text('v_email', array('value' => $cuser['email'], 'readonly'=> 'readonly')); ?>
<!--                                    --><?php //echo $cuser['email']; ?>
                                </div>
                                <div class="col-sm-4 pull-right">
                                    <?php
                                    $this->MooPopup->tag(array(
                                        'href'=>$this->Html->url(array("controller" => "users",
                                            "action" => "ajax_confirm_mail",
                                            "plugin" => false,
                                            'mail'
                                        )),
                                        'title' => __('Change'),
                                        'innerHtml'=> __('Change'),
                                        'id' => 'btn_change_mail',
                                        'class' => 'btn btn-action btn-validate',
                                        'style' => ($revert && !$cuser['confirmed']) ? 'display:none' : ''
                                    ));
                                    ?>
                                    <div id="wrap_resend" class="pull-right" style="<?php echo ($revert && !$cuser['confirmed']) ? '' : 'display:none'?>">
                                        <a class="button button-action" id="cancel_validation"><?php echo __('Cancel') ?></a>
                                        <a class="btn btn-action" id="resend_validation_link"><?php echo __('Resend validation email') ?></a>
                                    </div>
                                </div>
                                <div class="clear"></div>
                            </li>
                            <li>
                                <div class="col-sm-3">
                                    <label><?php echo __('Sub Email')?></label>
                                </div>
                                <div class="col-sm-5">
                                    <?php echo $this->Form->text('v_sub_mail', array('value' => $cuser['sub_mail'], 'readonly'=> 'readonly')); ?>
<!--                                    --><?php //echo $cuser['sub_mail']; ?>
                                </div>
                                <div class="col-sm-4 pull-right">
                                    <?php
                                    $this->MooPopup->tag(array(
                                        'href'=>$this->Html->url(array("controller" => "users",
                                            "action" => "ajax_confirm_mail",
                                            "plugin" => false,
                                            'sub_mail'
                                        )),
                                        'title' => __('Change'),
                                        'innerHtml'=> __('Change'),
                                        'id' => 'btn_change_submail',
                                        'class' => 'btn btn-action btn-validate',
                                        'style' => ($sub_revert && !$cuser['submail_confirmed']) ? 'display:none' : ''
                                    ));
                                    ?>
                                    <div id="wrap_resend_sub" class="pull-right" style="<?php echo ($sub_revert && !$cuser['submail_confirmed']) ? '' : 'display:none'?>">
                                        <a class="button button-action" id="cancel_sub_validation"><?php echo __('Cancel') ?></a>
                                        <a class="btn btn-action" id="resend_sub_validation_link_0"><?php echo __('Resend validation email') ?></a>
                                    </div>
                                </div>
                                <div class="clear"></div>
                            </li>
                            <li>
                                <div class="col-sm-3">
                                    <label><?php echo __('Mobile')?></label>
                                </div>
                                <div class="col-sm-5">
                                    <?php echo $this->Form->text('v_sms_verify_phone', array('value' => $cuser['mobile'], 'readonly'=> 'readonly')); ?>
<!--                                    --><?php //echo $cuser['sms_verify_phone']; ?>
                                </div>
                                <div class="col-sm-4">
                                    <?php
                                    $this->MooPopup->tag(array(
                                        'href'=>$this->Html->url(array("controller" => "users",
                                            "action" => "ajax_sms_verify",
                                            "plugin" => false,
                                            'sub_mail'
                                        )),
                                        'title' => __('Change'),
                                        'innerHtml'=> __('Change'),
                                        'id' => '',
                                        'class' => 'btn btn-action btn-validate'
                                    ));
                                    ?>

                                </div>
                                <div class="clear"></div>
                            </li>
                        </ul>

                        <div class='col-sm-3 hidden-xs hidden-sm'>&nbsp;</div>
                        <div class='col-sm-9'>
                            <div style="margin-top:10px"><input id="save_profile_1" type="submit" class="btn btn-action" value="<?php echo __('Save Changes')?>"></div>
                        </div>
                        <div class='clear'></div>
                    </div>

                <div class="edit-profile-section" style="border:none">
                    <?php if ( !$cuser['Role']['is_super'] ): ?>
                        <ul class="list6 list6sm" style="margin:10px 0">
                            <li><a href="javascript:void(0)" class="deactiveMyAccount"><?php echo __('Deactivate my account')?></a></li>
                            <li><a href="javascript:void(0)" class="deleteMyAccount"><?php echo __('Delete my account')?></a></li>
                        </ul>
                    <?php endif; ?>
                </div>

                <div class="error-message" id="errorMessage" style="display:none"></div>

                </div>
            </div>
        </div>
        </form>
    </div>
</div>