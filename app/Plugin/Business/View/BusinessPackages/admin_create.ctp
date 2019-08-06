<?php $helper = MooCore::getInstance()->getHelper('Business_Business');  ?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <?php if (!$bIsEdit) : ?>
        <h4 class="modal-title"><?php echo __d('business','Add New Package'); ?></h4>
    <?php else: ?>
        <h4 class="modal-title"><?php echo __d('business','Edit Package'); ?></h4>
    <?php endif; ?>
</div>
<div class="modal-body">
    <form id="createForm" class="form-horizontal" role="form">
        <?php echo $this->Form->hidden('id', array('value' => $package['BusinessPackage']['id'])); ?>
        <?php echo $this->Form->hidden('trial', array('value' => $package['BusinessPackage']['trial'])); ?>
        <div class="form-body">
            <?php if (!$bIsEdit) : ?>
                <div class="form-group" style="display: none;">
                    <label class="col-md-3 control-label"><?php echo __d('business','Trial package'); ?></label>
                    <div class="col-md-9">
                        <div class="checkbox-list">
                            <?php echo $this->Form->checkbox('trial_package', array()); ?>
                        </div>
                    </div>
                </div> 
                <div id="trial_package_select" class="form-group" style="display:none;">
                    <label class="col-md-3 control-label"><?php echo __d('business','Package'); ?></label>
                    <div class="col-md-9">
                        <?php
                            echo $this->Form->select('package_select', $package_selects, array(
                                'class' => 'form-control',
                                'empty' => __d('business','Select package for trial'),
                            ));
                        ?>
                    </div>
                </div>
                <div class="form-group sl_form_group">
                    <label class="col-md-3 control-label"><?php echo __d('business','Package Type'); ?></label>
                    <div class="col-md-9">
                        <?php
                        echo $this->Form->select('type', array(
                            BUSINESS_ONE_TIME => __d('business','One Time'),
                            BUSINESS_RECURRING => __d('business','Recurring')), array(
                            'class' => 'form-control',
                            'empty' => false,
                            'value' => $package['BusinessPackage']['type'],
                        ));
                        ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="form-group sl_form_group">
                    <label class="col-md-3 control-label"><?php echo __d('business','Package Type'); ?></label>
                    <div class="col-md-9">
                        <?php
                        echo $this->Form->select('type', array(
                            BUSINESS_ONE_TIME => __d('business','One Time'),
                            BUSINESS_RECURRING => __d('business','Recurring')), array(
                            'class' => 'form-control',
                            'empty' => false,
                            'value' => $package['BusinessPackage']['type'],
                            'disabled' => 'disabled'
                        ));
                        ?>
                    </div>
                </div>
            <?php endif; ?>
            <div class="form-group sl_form_group">
                <label class="col-md-3 control-label"><?php echo __d('business','Title'); ?></label>
                <div class="col-md-9">
                    <?php echo $this->Form->text('name', array('placeholder' => 'Enter text', 'class' => 'form-control', 'value' => $package['BusinessPackage']['name'])); ?>
                </div>
            </div>
            <?php if(!$bIsEdit): ?>
                <div id="recurring_type" style="display:none" >
                    <div class="form-group sl_form_group">
                        <label class="col-md-3 control-label"><?php echo __d('business','Recurring Price'); ?> (<span><?php echo  $currency['Currency']['symbol'] ?></span>)(<a data-html="true" class="tooltips" title="<?php echo __d('business','The amount will be charged each billing cycle for recurring plans'); ?>" data-placement="top">?</a>)</label>
                        <div class="col-md-3">
                            <?php if($bIsEdit && $isUsedPackage):?>
                                <?php echo $package['BusinessPackage']['price'];?>
                            <?php else:?>
                                <?php echo $this->Form->text('price', array('placeholder' => '0.00', 'class' => 'form-control', 'value' => $package['BusinessPackage']['price'])); ?>
                            <?php endif;?>
                        </div>
                        <div class="col-md-3">
                        </div>
                    </div>
                    <div class="form-group sl_form_group">
                        <label class="col-md-3 control-label"><?php echo __d('business','Billing Cycle'); ?>(<a data-html="true" class="tooltips" title="<?php echo __d('business','Time duration of each billing cycle'); ?>" data-placement="top">?</a>)</label>
                        <div class="col-md-3">
                            <?php if($bIsEdit && $isUsedPackage):?>
                                <?php echo $package['BusinessPackage']['billing_cycle'];?>
                            <?php else:?>
                                <?php
                                echo $this->Form->text('billing_cycle', array('style' => "width:100%; padding: 6px 0px", 'value' => $package['BusinessPackage']['billing_cycle']));
                                ?>
                            <?php endif;?>
                        </div>
                        <div class="col-md-3">
                            <?php
                                echo $this->Form->select('billing_cycle_type', 
                                                        array(
                                                            1 => __d('business','Days'), 
                                                            2 => __d('business','Week'), 
                                                            3 => __d('business','Month'),
                                                            4 => __d('business','Year'),
                                                            5 => __d('business','Forever')),
                                                        array(
                                                            'class' => 'form-control',
                                                            'empty' => false,
                                                            'value' => $helper->getTextDurationId($package['BusinessPackage']['billing_cycle_type'])));
                            ?>
                        </div>
                    </div>
                </div>
                <div id="onetime_type">
                    <div class="form-group sl_form_group">
                        <label class="col-md-3 control-label"><?php echo __d('business','Price'); ?> (<span><?php echo  $currency['Currency']['symbol'] ?></span>)</label>
                        <div class="col-md-3">
                            <?php if($bIsEdit && $isUsedPackage):?>
                                <?php echo $package['BusinessPackage']['price'];?>
                            <?php else:?>
                                <?php echo $this->Form->text('price', array('placeholder' => '0.00', 'class' => 'form-control', 'value' => $package['BusinessPackage']['price'])); ?>
                            <?php endif;?>
                        </div>
                    </div>
                </div>
            <?php else : ?>
                <?php if($package['BusinessPackage']['type'] == BUSINESS_ONE_TIME) : ?>
                    <div class="form-group sl_form_group">
                        <label class="col-md-3 control-label"><?php echo __d('business','Price'); ?> (<span><?php echo  $currency['Currency']['symbol'] ?></span>)(<a data-html="true" class="tooltips" title="<?php echo __d('business','The amount will be charged each billing cycle for recurring plans'); ?>" data-placement="top">?</a>)</label>
                        <div class="col-md-3">
                            <?php if($bIsEdit && $isUsedPackage):?>
                                <?php echo $package['BusinessPackage']['price'];?>
                            <?php else:?>
                                <?php echo $this->Form->text('price', array('placeholder' => '0.00', 'class' => 'form-control', 'value' => $package['BusinessPackage']['price'])); ?>
                            <?php endif;?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="form-group sl_form_group">
                        <label class="col-md-3 control-label"><?php echo __d('business','Recurring Price'); ?> (<span><?php echo  $currency['Currency']['symbol'] ?></span>)</label>
                        <div class="col-md-3">
                            <?php if($bIsEdit && $isUsedPackage):?>
                                <?php echo $package['BusinessPackage']['price'];?>
                            <?php else:?>
                                <?php echo $this->Form->text('price', array('placeholder' => '0.00', 'class' => 'form-control', 'value' => $package['BusinessPackage']['price'])); ?>
                            <?php endif;?>
                        </div>
                    </div>
                    <div class="form-group sl_form_group">
                        <label class="col-md-3 control-label"><?php echo __d('business','Billing Cycle'); ?>(<a data-html="true" class="tooltips" title="<?php echo __d('business','Time duration of each billing cycle'); ?>" data-placement="top">?</a>)</label>
                        <div class="col-md-3">
                            <?php if($bIsEdit && $isUsedPackage):?>
                                <?php echo $package['BusinessPackage']['billing_cycle'];?>
                            <?php else:?>
                                <?php
                                echo $this->Form->text('billing_cycle', array('style' => "width:100%; padding: 6px 0px", 'value' => $package['BusinessPackage']['billing_cycle']));
                                ?>
                            <?php endif;?>
                        </div>
                        <div class="col-md-3">
                            <?php if($bIsEdit && $isUsedPackage):?>
                                <?php echo $package['BusinessPackage']['billing_cycle_type'];?>
                            <?php else:?>
                                <?php
                                    echo $this->Form->select('billing_cycle_type', 
                                                            array(
                                                                1 => __d('business','Days'), 
                                                                2 => __d('business','Week'), 
                                                                3 => __d('business','Month'),
                                                                4 => __d('business','Year'),
                                                                5 => __d('business','Forever')),
                                                            array(
                                                                'class' => 'form-control',
                                                                'empty' => false,
                                                                'value' => $helper->getTextDurationId($package['BusinessPackage']['billing_cycle_type'])));
                                ?>
                            <?php endif;?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            <div class="form-group sl_form_group">
                <label class="col-md-3 control-label"><?php echo __d('business','Duration'); ?>(<a data-html="true" class="tooltips" title="<?php echo __d('business','Maximum duration of this plan. For one-time plans, the plan will expire after the period of time set here. For recurring plans, the user will be billed at the above billing cycles for the period of time specified here'); ?>" data-placement="top">?</a>)</label>
                <div class="col-md-3">
                    <?php if($bIsEdit && $isUsedPackage):?>
                        <?php echo $package['BusinessPackage']['duration'];?>
                    <?php else:?>
                        <?php
                        echo $this->Form->text('duration', array('style' => "width:100%; padding: 6px 0px", 'value' => $package['BusinessPackage']['duration']));
                        ?>
                    <?php endif;?>
                </div>
                <div class="col-md-3">
                    <?php if($bIsEdit && $isUsedPackage):?>
                        <?php echo $package['BusinessPackage']['duration_type'];?>
                    <?php else:?>
                        <?php
                            echo $this->Form->select('duration_type', 
                                                    array(
                                                        1 => __d('business','Days'), 
                                                        2 => __d('business','Week'), 
                                                        3 => __d('business','Month'),
                                                        4 => __d('business','Year'),
                                                        5 => __d('business','Forever')),
                                                    array(
                                                        'class' => 'form-control',
                                                        'empty' => false,
                                                        'value' => $helper->getTextDurationId($package['BusinessPackage']['duration_type'])));
                        ?>
                    <?php endif;?>
                </div>
            </div>
            <div class="form-group sl_form_group">
                <label class="col-md-3 control-label"><?php echo __d('business','Expiration Reminder'); ?>(<a data-html="true" class="tooltips" title="<?php echo __d('business','Specifies time before expiration to send renewal reminder'); ?>" data-placement="top">?</a>)</label>
                <div class="col-md-3">
                    <?php if($bIsEdit && $isUsedPackage):?>
                        <?php echo $package['BusinessPackage']['expiration_reminder'];?>
                    <?php else:?>
                        <?php
                        echo $this->Form->text('expiration_reminder', array('style' => "width:100%; padding: 6px 0px", 'value' => $package['BusinessPackage']['expiration_reminder']));
                        ?>
                    <?php endif;?>
                </div>
                <div class="col-md-3">
                    <?php if($bIsEdit && $isUsedPackage):?>
                        <?php echo $package['BusinessPackage']['expiration_reminder_type'];?>
                    <?php else:?>
                        <?php
                            echo $this->Form->select('expiration_reminder_type', 
                                                    array(
                                                        1 => __d('business','Days'), 
                                                        2 => __d('business','Week'), 
                                                        3 => __d('business','Month'),
                                                        4 => __d('business','Year'),
                                                        5 => __d('business','Forever')),
                                                    array(
                                                        'class' => 'form-control',
                                                        'empty' => false,
                                                        'value' => $helper->getTextDurationId($package['BusinessPackage']['expiration_reminder_type'])));
                        ?>
                    <?php endif;?>
                </div>
            </div>
            <div class="form-group sl_form_group">
                <label class="col-md-3 control-label"><?php echo __d('business','Can manage admins'); ?></label>
                <div class="col-md-9">
                    <div class="checkbox-list">
                        <?php echo $this->Form->checkbox('manage_admin', array('checked' => $package['BusinessPackage']['manage_admin'])); ?>
                    </div>
                </div>
            </div>
            <div class="form-group sl_form_group">
                <label class="col-md-3 control-label"><?php echo __d('business','Can response a review'); ?></label>
                <div class="col-md-9">
                    <div class="checkbox-list">
                        <?php echo $this->Form->checkbox('response_review', array('checked' => $package['BusinessPackage']['response_review'])); ?>
                    </div>
                </div>
            </div>
            <div class="form-group sl_form_group">
                <label class="col-md-3 control-label"><?php echo __d('business','Can send verification request'); ?></label>
                <div class="col-md-9">
                    <div class="checkbox-list">
                        <?php echo $this->Form->checkbox('send_verification_request', array('checked' => $package['BusinessPackage']['send_verification_request'])); ?>
                    </div>
                </div>
            </div>
            <div class="form-group sl_form_group">
                <label class="col-md-3 control-label"><?php echo __d('business','Enable contact us form'); ?></label>
                <div class="col-md-9">
                    <div class="checkbox-list">
                        <?php echo $this->Form->checkbox('contact_form', array('checked' => $package['BusinessPackage']['contact_form'])); ?>
                    </div>
                </div>
            </div>
            <div class="form-group sl_form_group">
                <label class="col-md-3 control-label"><?php echo __d('business','Enable follow'); ?></label>
                <div class="col-md-9">
                    <div class="checkbox-list">
                        <?php echo $this->Form->checkbox('follow', array('checked' => $package['BusinessPackage']['follow'])); ?>
                    </div>
                </div>
            </div>
            <div class="form-group sl_form_group">
                <label class="col-md-3 control-label"><?php echo __d('business','Enable Checkin'); ?></label>
                <div class="col-md-9">
                    <div class="checkbox-list">
                        <?php echo $this->Form->checkbox('checkin', array('checked' => $package['BusinessPackage']['checkin'])); ?>
                    </div>
                </div>
            </div>
            <div class="form-group sl_form_group">
                <label class="col-md-3 control-label"><?php echo __d('business','Enable favorite'); ?></label>
                <div class="col-md-9">
                    <div class="checkbox-list">
                        <?php echo $this->Form->checkbox('favourite', array('checked' => $package['BusinessPackage']['favourite'])); ?>
                    </div>
                </div>
            </div>
            <?php if($package['BusinessPackage']['trial'] == 0 && false): ?>
    			<div class="form-group sl_form_group_hide">
                    <label class="col-md-3 control-label"><?php echo __d('business','Most Popular'); ?></label>
                    <div class="col-md-9">
                        <div class="checkbox-list">
                            <?php echo $this->Form->checkbox('most_popular', array('checked' => $package['BusinessPackage']['most_popular'])); ?>
                        </div>
                    </div>
                </div> 
            <?php endif; ?>
            <div class="form-group sl_form_group_hide">
                <label class="col-md-3 control-label"><?php echo __d('business','Enable'); ?></label>
                <div class="col-md-9">
                    <div class="checkbox-list">
                        <?php echo $this->Form->checkbox('enable', array('checked' => $package['BusinessPackage']['enable'])); ?>
                    </div>
                </div>
            </div> 
        </div>
    </form>
    <div class="alert alert-danger error-message" style="display:none;margin-top:10px;">
    </div>
</div>
<div class="modal-footer sl_form_group">
    <button type="button" class="btn default" data-dismiss="modal"><?php echo __d('business','Close') ?></button>
    <a href="#" id="createButton" class="btn btn-action"><?php echo __d('business','Save') ?></a>

</div>
<?php if($this->request->is('ajax')): ?>
<script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
<?php endif; ?>
    jQuery(document).ready(function() {
       jQuery.admin.initCreateItem("<?php echo  $this->request->base ?>/admin/business/business_packages/save"); 
       jQuery.admin.initPackageScript(); 
       <?php if(!$bIsEdit): ?>
            $("#enable").prop('checked', true);
       <?php endif; ?>
    });
<?php if($this->request->is('ajax')): ?>
</script>
<?php else: ?>
<?php $this->Html->scriptEnd(); ?>
<?php endif; 