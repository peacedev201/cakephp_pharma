<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["jquery","mooUser"], function($,mooUser) {
        mooUser.initOnUserProfile();
        mooUser.initOnProfileEdit();
    });
</script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery','mooUser'), 'object' => array('$', 'mooUser'))); ?>
    mooUser.initOnUserProfile();
    mooUser.initOnProfileEdit();
<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>

<?php $this->setNotEmpty('west');?>
<?php $this->start('west'); ?>
<div class="bar-content">
    <div class="profile-info-menu">
        <?php echo $this->element('profilenav', array("cmenu" => "profile"));?>
    </div>
</div>
<?php $this->end(); ?>
<?php

$bday_month = '';
$bday_day = '';
$bday_year = '';
if (!empty($cuser['birthday']))
{
    $birthday = explode('-', $cuser['birthday']);
    $bday_year = $birthday[0];
    $bday_month = $birthday[1];
    $bday_day = $birthday[2];
}
?>

<div class="bar-content ">
    <div class="content_center profile-info-edit">
        <form id="form_edit_user" action="<?php echo $this->request->base?>/users/profile" method="post">
        <div id="center" class="post_body">
            <div class="mo_breadcrumb">
                 <h1><?php echo __('Profile Information')?></h1>
                 <a href="<?php echo $this->request->base?>/users/view/<?php echo $uid?>" class="topButton button button-action button-mobi-top"><?php echo __('View Profile')?></a>
            </div>
            <div class="full_content">
                <div class="content_center">

                    <div class="edit-profile-section">
                        <h2><?php echo __('User & Specialty Information')?></h2>
                        <ul class="">
                            <li>
                                <div class="col-sm-3">
                                    <label><?php echo __('Birthday')?></label>
                                </div>
                                <div class="col-sm-9">
                                    <div class="col-xs-4">
                                        <?php echo $this->Form->month('birthday', array('value' => $bday_month))?>
                                    </div>
                                    <div class="col-xs-4">
                                        <div class='p_l_2'>
                                            <?php echo $this->Form->day('birthday', array('value' => $bday_day))?>
                                        </div>
                                    </div>
                                    <div class="col-xs-4">
                                        <?php echo $this->Form->year('birthday', 1930, date('Y'), array('value' => $bday_year))?>
                                    </div>
                                    <a href="javascript:void(0)" class="tip profile-tip" title="<?php echo __('Only month and date will be shown on your profile')?>">(?)</a>
                                </div>
                                <div class="clear"></div>
                            </li>
                            <li>
                                <div class="col-sm-3">
                                    <label><?php echo __('Gender')?></label>
                                </div>
                                <div class="col-sm-9">
                                    <?php echo $this->Form->radio('gender', $this->Moo->getGenderList(), array('label' => true, 'value' => $cuser['gender'],'hiddenField'=>true, 'legend'=> false, 'separator'=>'&nbsp;&nbsp;&nbsp;')); ?>
                                </div>
                                <div class="clear"></div>
                            </li>
                            <?php $enable_timezone_selection = Configure::read('core.enable_timezone_selection');
                            if ( !empty( $enable_timezone_selection ) ): ?>
                                <li>
                                    <div class="col-sm-3">
                                        <label><?php echo __('Timezone')?></label>
                                    </div>
                                    <div class="col-sm-9">
                                        <?php echo $this->Form->select('timezone', $this->Moo->getTimeZones(), array('value' => $cuser['timezone'])); ?>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                            <?php endif; ?>
                            <li>
                                <div class="col-sm-3">
                                    <label> <?php echo __('Specialty')?></label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="hidden" name="data[specialty]" id="_" value="">
                                    <?php echo $this->Form->radio('specialty', array(SPECIALTY_PHARMACIST => __('Pharmacist'), SPECIALTY_STUDENT => __('Student'), SPECIALTY_OTHER=>__('Others')), array('class' => 'specialty','id' => false, 'label' => true, 'value' => $cuser['specialty'],'hiddenField'=>true, 'legend'=> false, 'separator'=>'&nbsp;&nbsp;&nbsp;')); ?>
                                </div>
                                <div class="clear"></div>
                            </li>
                            <div id="university_info" style="<?php echo $univ_style ;?>">
                                <li>
                                    <div class="col-sm-3">
                                        <label><?php echo __('University')?></label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="hidden" name="data[university_id]" id="_" value="">
                                        <?php $i = 0; foreach($universities as $university):?>
                                            <div class="col-md-3">
                                                <input type="radio" name="data[university_id]" <?php echo ($cuser['university_id'] == $university['University']['code'] || (empty($cuser['university_id']) && empty($cuser['university']) && $i == 0)) ? 'checked' : '';?> id="univ_<?php echo $university['University']['id'];?>" value="<?php echo $university['University']['code'];?>" class="univer_radio">
                                                <label for="univ_<?php echo $university['University']['id'];?>"><?php echo $university['University'][$uniField];?></label>
                                            </div>
                                            <?php $i++; endforeach;?>
                                        <div class="clear"></div>
                                        <div><?php echo __('Please type in (Pharmacist who graduated other university)');?></div>
                                        <?php echo $this->Form->text('university', array('value' => $cuser['university']));?>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                                <div class="form-group required">
                                    <label class="col-md-3 control-label">
                                        <?php echo __('Admission year')?>
                                    </label>
                                    <div class="col-md-9 form-inline">
                                        <?php echo $this->Form->text('admission_year', array('value' => $cuser['admission_year'])); ?>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                            </div>
                        </ul>
                    </div>

                    <div class="edit-profile-section">
                        <h2><?php echo __('Optional Information')?></h2>
                        <ul >
                            <?php if ( in_array('user_username', $uacos) && ( Configure::read('core.username_change') || empty($cuser['username']) ) ): ?>
                                <li>
                                    <div class="col-sm-3">
                                        <label><?php echo __('Username')?></label>
                                    </div>
                                    <div class="col-sm-9">
                                        <?php echo $this->Form->text('username', array('value' => $cuser['username'])); ?>
                                        <div>
                                            <?php
                                            $ssl_mode = Configure::read('core.ssl_mode');
                                            $http = (!empty($ssl_mode)) ? 'https' :  'http';
                                            ?>
                                            <?php echo $http.'://'.$_SERVER['SERVER_NAME'].$this->base;?>/<span id="profile_user_name"><?php if ($cuser['username']) echo '-'.$cuser['username']?></span>
                                        </div>
                                        <a href="javascript:void(0)" class="button button-primary" style="margin-top: 5px;" id="checkButton"><i class="material-icons">done</i> <?php echo __('Check Availability')?></a>
                                        <div style="display:none;margin:5px 0 0" id="message"></div>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                            <?php endif; ?>
                            <li>
                                <div class="col-sm-3">
                                    <label><?php echo __('About')?></label>
                                </div>
                                <div class="col-sm-9">
                                    <?php echo $this->Form->textarea('about', array('value' => $cuser['about'])); ?>
                                </div>
                                <div class="clear"></div>
                            </li>
                        </ul>
                    </div>
                    <?php if ( !empty( $custom_fields ) || (count($profile_type) > 1 && ($is_edit || !$cuser['ProfileType']['id'])) ): ?>
                        <div class="edit-profile-section">
                            <h2><?php echo __('Additional Information')?></h2>
                            <?php
                            echo $this->element( 'custom_fields', array( 'show_require' => true, 'show_heading' => true ) );
                            ?>
                        </div>
                    <?php endif; ?>
                    <div class="edit-profile-section">
                        <h2><?php echo __('User Settings')?></h2>
                        <ul class="">
                            <li>
                                <div class="col-sm-3">
                                    <label><?php echo __('Profile Privacy')?></label>
                                </div>
                                <div class="col-sm-9">
                                    <?php echo $this->Form->select('privacy', array( PRIVACY_EVERYONE => __('Everyone'),
                                        PRIVACY_FRIENDS => __('Friends Only'),
                                        PRIVACY_ME => __('Only Me')),
                                        array('value' => $cuser['privacy'], 'empty' => false)); ?>
                                </div>
                                <div class="clear"></div>
                            </li>
                            <li>
                                <?php echo $this->Form->checkbox('hide_online', array('checked' => $cuser['hide_online'])); ?>
                                <?php echo __('Do not show my online status')?>
                            </li>
                            <?php
                            $send_message_to_non_friend = Configure::read('core.send_message_to_non_friend');
                            ?>
                            <?php if($send_message_to_non_friend): ?>
                                <li>
                                    <?php echo $this->Form->checkbox('receive_message_from_non_friend', array('checked' => $cuser['receive_message_from_non_friend'])); ?>
                                    <?php echo __('Receive message from non-friend')?>
                                </li>
                            <?php endif; ?>
                            <?php  ?>
                        </ul>

                        <div class='col-sm-3 hidden-xs hidden-sm'>&nbsp;</div>
                        <div class='col-sm-9'>
                            <div style="margin-top:10px"><input id="save_profile_2" type="submit" class="btn btn-action" value="<?php echo __('Save Changes')?>"></div>
                        </div>
                        <div class='clear'></div>
                    </div>

                <div class="error-message" id="errorMessage" style="display:none"></div>

                </div>
            </div>
        </div>
        </form>
    </div>
</div>