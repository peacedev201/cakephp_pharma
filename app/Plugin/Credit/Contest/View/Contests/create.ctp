<?php
$helper = MooCore::getInstance()->getHelper('Contest_Contest');
$this->addPhraseJs(array(
'drag_photo' => __d('contest', "Drag or click here to upload photo")
));
?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('jquery', 'mooContest'), 'object' => array('$', 'mooContest'))); ?>	
mooContest.initCreateContest({
'url': '<?php echo (!empty($contest['Contest']['id'])) ? $contest['Contest']['moo_href'] : '' ?>',
'is_edit': '<?php echo $is_edit; ?>'
});
<?php $this->Html->scriptEnd(); ?>
<div class="create_form">
    <div class="bar-content">
        <div class="content_center">
            <div class="box3">
                <form action="<?php echo $this->request->base; ?>/contests/save" enctype="multipart/form-data" id="createForm" method="post">
                    <div class="mo_breadcrumb">
                        <?php if (!empty($contest['Contest']['id'])) : ?>
                        <h1><?php echo __d('contest', 'Edit Contest'); ?></h1>
                        <?php else: ?>
                        <h1><?php echo __d('contest', 'Add new Contest'); ?></h1>
                        <?php endif; ?>
                    </div>
                    <div class="full_content p_m_10">
                        <?php
                        if (!empty($contest['Contest']['id']))
                        echo $this->Form->hidden('id', array('value' => $contest['Contest']['id']));
                        echo $this->Form->hidden('thumbnail', array('value' => $contest['Contest']['thumbnail']));
                        ?>
                        <div class="form_content">
                            <ul>
                                <li>
                                    <div class="col-md-2">
                                        <label><?php echo __d('contest', 'Contest Type') ?>*</label>
                                    </div>
                                    <div class="col-md-10">
                                        <?php echo $this->Form->select('type', array('photo' => __d('contest', 'Photo Contest'), 'music' => __d('contest', 'Music Contest'), 'video' => __d('contest', 'Video Contest')), array('value' => $contest['Contest']['type'], 'empty' => __d('contest', 'Select contest type'))); ?>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                                <li>
                                    <div class="col-md-2">
                                        <label><?php echo __d('contest', 'Category') ?>*</label>
                                    </div>
                                    <div class="col-md-10">
                                        <?php echo $this->Form->select('category_id', $categories, array('value' => $contest['Contest']['category_id'], 'empty' => __d('contest', 'Select category'))); ?>
                                    </div>
                                    <div class="clear"></div>
                                </li>                                        
                                <li>
                                    <div class="col-md-2">
                                        <label><?php echo __d('contest', 'Contest Name') ?>*</label>
                                    </div>
                                    <div class="col-md-10">
                                        <?php echo $this->Form->text('name', array('value' => $contest['Contest']['name'])); ?>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                                <li>
                                    <div class='col-md-2'>
                                        <label><?php echo __d('contest', 'Description') ?>*</label>
                                    </div>
                                    <div class='col-md-10'>
                                        <?php echo $this->Form->textarea('description', array('style' => 'height:100px', 'value' => $contest['Contest']['description'])); ?>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                                <li>
                                    <div class='col-md-2'>
                                        <label><?php echo __d('contest', 'Award') ?>*</label>
                                    </div>
                                    <div class='col-md-10'>
                                        <?php echo $this->Form->textarea('award', array('style' => 'height:100px', 'value' => $contest['Contest']['award'])); ?>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                                <li>
                                    <div class='col-md-2'>
                                        <label><?php echo __d('contest', 'Terms & Conditions') ?>*</label>
                                    </div>
                                    <div class='col-md-10'>
                                        <?php echo $this->Form->textarea('term_and_condition', array('style' => 'height:100px', 'value' => $contest['Contest']['term_and_condition'])); ?>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                                <li>
                                    <div class="col-md-2">
                                        <label><?php echo __d('contest', 'Thumbnail') ?></label>
                                    </div>
                                    <div class="col-md-10">
                                        <div id="contest_thumnail"></div>
                                        <div id="contest_thumnail_preview">
                                            <?php if (!empty($contest['Contest']['thumbnail'])): ?>
                                            <img width="150" src="<?php echo $helper->getImage($contest, array('prefix' => '150_square')) ?>" />
                                            <?php else: ?>
                                            <img width="150" style="display: none;" src="" />
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                                <div class="title_form">
                                    <b><?php echo __d('contest', 'Contest Duration') ?></b>
                                </div>
                                <li>
                                    <div class='col-md-2'>
                                        <label><?php echo __d('contest', 'Start') ?>*</label>
                                    </div>
                                    <div class="col-md-10">
                                        <div class='col-xs-6'>
                                            <?php echo $this->Form->text('from', array('placeholder' => __d('contest', 'Select date'), 'class' => 'datepicker', 'value' => $contest['Contest']['from'])); ?>
                                        </div>
                                        <div class='col-xs-6'>
                                            <div class="m_l_2">
                                                <?php echo $this->Form->text('from_time', array('placeholder' => __d('contest', 'Select time'), 'value' => $contest['Contest']['from_time'], 'class' => 'timepicker')); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                                <li>
                                    <div class='col-md-2'>
                                        <label><?php echo __d('contest', 'End') ?>*</label>
                                    </div>
                                    <div class="col-md-10">
                                        <div class='col-xs-6'>
                                            <?php echo $this->Form->text('to', array('placeholder' => __d('contest', 'Select date'), 'class' => 'datepicker', 'value' => $contest['Contest']['to'])); ?>
                                        </div>
                                        <div class='col-xs-6'>
                                            <div class="m_l_2">
                                                <?php echo $this->Form->text('to_time', array('placeholder' => __d('contest', 'Select time'), 'value' => $contest['Contest']['to_time'], 'class' => 'timepicker')); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                                <div class="title_form">
                                    <b><?php echo __d('contest', 'Submit Entries Duration') ?></b>
                                </div>
                                <li>
                                    <div class='col-md-2'>
                                        <label><?php echo __d('contest', 'Start') ?>*</label>
                                    </div>
                                    <div class="col-md-10">
                                        <div class='col-xs-6'>
                                            <?php echo $this->Form->text('s_from', array('placeholder' => __d('contest', 'Select date'), 'class' => 'datepicker', 'value' => $contest['Contest']['s_from'])); ?>
                                        </div>
                                        <div class='col-xs-6'>
                                            <div class="m_l_2">
                                                <?php echo $this->Form->text('s_from_time', array('placeholder' => __d('contest', 'Select time'), 'value' => $contest['Contest']['s_from_time'], 'class' => 'timepicker')); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                                <li>
                                    <div class='col-md-2'>
                                        <label><?php echo __d('contest', 'End') ?>*</label>
                                    </div>
                                    <div class="col-md-10">
                                        <div class='col-xs-6'>
                                            <?php echo $this->Form->text('s_to', array('placeholder' => __d('contest', 'Select date'), 'class' => 'datepicker', 'value' => $contest['Contest']['s_to'])); ?>
                                        </div>
                                        <div class='col-xs-6'>
                                            <div class="m_l_2">
                                                <?php echo $this->Form->text('s_to_time', array('placeholder' => __d('contest', 'Select time'), 'value' => $contest['Contest']['s_to_time'], 'class' => 'timepicker')); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                                <div class="title_form">
                                    <b><?php echo __d('contest', 'Voting Duration') ?></b>
                                </div>
                                <li>
                                    <div class='col-md-2'>
                                        <label><?php echo __d('contest', 'Start') ?>*</label>
                                    </div>
                                    <div class="col-md-10">
                                        <div class='col-xs-6'>
                                            <?php echo $this->Form->text('v_from', array('placeholder' => __d('contest', 'Select date'), 'class' => 'datepicker', 'value' => $contest['Contest']['v_from'])); ?>
                                        </div>
                                        <div class='col-xs-6'>
                                            <div class="m_l_2">
                                                <?php echo $this->Form->text('v_from_time', array('placeholder' => __d('contest', 'Select time'), 'value' => $contest['Contest']['v_from_time'], 'class' => 'timepicker')); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                                <li>
                                    <div class='col-md-2'>
                                        <label><?php echo __d('contest', 'End') ?>*</label>
                                    </div>
                                    <div class="col-md-10">
                                        <div class='col-xs-6'>
                                            <?php echo $this->Form->text('v_to', array('placeholder' => __d('contest', 'Select date'), 'class' => 'datepicker', 'value' => $contest['Contest']['v_to'])); ?>
                                        </div>
                                        <div class='col-xs-6'>
                                            <div class="m_l_2">
                                                <?php echo $this->Form->text('v_to_time', array('placeholder' => __d('contest', 'Select time'), 'value' => $contest['Contest']['v_to_time'], 'class' => 'timepicker')); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                                <li>
                                    <div class="col-md-2">
                                        <label><?php echo __d('contest', 'Timezone') ?></label>
                                    </div>
                                    <div class="col-md-10">
                                        <?php $currentTimezone = !empty($contest['Contest']['timezone']) ? $contest['Contest']['timezone'] : $cuser['timezone']; ?>
                                        <?php echo $this->Form->select('timezone', $this->Moo->getTimeZones(), array('empty' => false, 'value' => $currentTimezone)); ?>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                                <li>
                                    <div class="col-md-2">
                                    </div>
                                    <div class="col-md-10">
                                        <?php echo $this->Form->checkbox('vote_without_join', array('checked' => (!empty($contest['Contest']['id']))? $contest['Contest']['vote_without_join']: 1)); ?>
                                        <label style="width:auto;"><b><?php echo __d('contest', 'Allow other members to vote for an entry without joining the contest') ?></b></label>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                                <li>
                                    <div class="col-md-2">

                                    </div>
                                    <div class="col-md-10">
                                        <?php echo $this->Form->checkbox('auto_approve', array('checked' => (!empty($contest['Contest']['id']))? $contest['Contest']['auto_approve']: 1)); ?>
                                        <label style="width:auto;"><b><?php echo __d('contest', 'Set entries automatically approved') ?></b></label>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                                <li>
                                    <div class="col-md-2">
                                        <label><?php echo __d('contest', 'Maximum entries a participant can submit') ?>*</label>
                                    </div>
                                    <div class="col-md-10">
                                        <?php echo $this->Form->text('maximum_entry', array('value' => $contest['Contest']['maximum_entry'])); ?>
                                        <b><?php echo __d('contest', 'Set 0 for unlimited entries'); ?></b>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                                <?php if(Configure::read('Credit.credit_enabled') && $contest_integrate_credit): ?>
                                    <li>
                                        <div class="col-md-2">
                                            <label><?php echo __d('contest', 'Submit Entry Fee(Credit)') ?>*</label>
                                        </div>
                                        <div class="col-md-10">
                                            <?php echo $this->Form->text('submit_entry_fee', array('value' => (!empty($contest['Contest']['submit_entry_fee'])) ? $contest['Contest']['submit_entry_fee'] : 0 )); ?>
                                            <b><?php echo __d('contest', 'Set 0 for free'); ?></b>
                                        </div>
                                        <div class="clear"></div>
                                    </li>
                                    <li>
                                        <div class="col-md-2">
                                            <label><?php echo __d('contest', '% credit for winner') ?>*</label>
                                        </div>
                                        <div class="col-md-10">
                                            <?php echo $this->Form->text('win_percent', array('value' => (!empty($contest['Contest']['win_percent'])) ? $contest['Contest']['win_percent'] : 0)); ?>
                                            <b><?php $commission = Configure::read('Contest.contest_credit_commission'); echo __d('contest', 'Commission credit fee for admin is %s', $commission. '%'); ?></b>
                                        </div>
                                        
                                        <div class="clear"></div>
                                    </li>
                                <?php else: ?>
                                    <?php echo $this->Form->hidden('submit_entry_fee', array('value' => (!empty($contest['Contest']['submit_entry_fee'])) ? $contest['Contest']['submit_entry_fee'] : 0)); ?>
                                    <?php echo $this->Form->hidden('win_percent', array('value' => (!empty($contest['Contest']['win_percent'])) ? $contest['Contest']['win_percent'] : 0)); ?>
                                <?php endif; ?>
                                <li>
                                    <div class="col-md-2">
                                        <label><?php echo __d('contest', 'Tags') ?> <a href="javascript:void(0)" class="tip" title="<?php echo __d('contest', 'Separated by commas or space') ?>">(?)</a></label>
                                    </div>
                                    <div class="col-md-10">
                                        <?php echo $this->Form->text('tags', array('value' => $contest['Contest']['tags'])); ?>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                                <li>
                                    <div class='col-md-2'>
                                        <label><?php echo __d('contest', 'Privacy') ?></label>
                                    </div>
                                    <div class='col-md-10'>
                                        <?php
                                        echo $this->Form->select( 'privacy',
                                        array( PRIVACY_EVERYONE => __d('contest', 'Everyone'),
                                        PRIVACY_FRIENDS => __d('contest', 'Friends Only'),
                                        PRIVACY_ME => __d('contest', 'Only Me')
                                        ),
                                        array('empty' => false, 'value' => $contest['Contest']['privacy'])
                                        );
                                        ?>
                                    </div>
                                    <div class="clear"></div>
                                </li>

                                <li>
                                    <div class="col-md-2">
                                        <label>&nbsp;</label>
                                    </div>
                                    <?php
                                    
                                    echo $this->Form->hidden('publish_confirm', array('value' => 0));
                                    echo $this->Form->hidden('contest_status', array('value' => !empty($contest['Contest']['contest_status']) ? $contest['Contest']['contest_status']: 'draft'));
                                    ?>
                                    <div class="col-md-10">
                                        <button type="submit" id="draft_button" class='btn btn-action'><?php echo __d('contest', 'Save as Draft') ?></button>
                                        <button type="submit" id="publish_button" class='btn btn-action'><?php echo __d('contest', 'Publish') ?></button>
                                        <?php if (!empty($contest['Contest']['id'])) : ?>
                                        <a href="<?php echo $contest['Contest']['moo_href'] ?>" class="button"><?php echo __('Cancel'); ?></a>
                                        <a href="javascript:void(0)" class="button deleteContest" data-id="<?php echo $contest['Contest']['id'] ?>" ><?php echo __('Delete') ?></a>											
                                        <?php endif; ?>	                               
                                    </div>
                                    <div class="clear"></div>		                            
                                </li>
                                <li>
                                    <div class=""><?php echo __d('contest', '* is required fields') ?></div>
                                    <div class="error-message" id="errorMessage" style="display:none"></div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>