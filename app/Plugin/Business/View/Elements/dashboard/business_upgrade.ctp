<?php $businessHelper = MooCore::getInstance()->getHelper('Business_Business'); ?>
<div class="package_current">
<span><?php echo __d('business', "Your current package is "); ?></span>
<span class="dropdown">
    <a style="cursor: pointer;"data-toggle="dropdown" data-target="#" id="dropdown-edit"><!--dropdown-user-box-->
        <?php echo $business['BusinessPackage']['name']; ?>
    </a>

    <ul aria-labelledby="dropdown-edit" class="dropdown-menu paclage_item_options" role="menu">
            <?php if($business['BusinessPackage']['manage_admin']): ?>
                <li>
                    <?php echo __d('business', "Can manage admins") ?>
                </li>
            <?php endif; ?>
            <?php if($business['BusinessPackage']['response_review']): ?>
                <li>
                    <?php echo __d('business', "Can response a review") ?>
                </li>
            <?php endif; ?>
            <?php if($business['BusinessPackage']['send_verification_request']): ?>
                <li>
                    <?php echo __d('business', "Can send verification request") ?>
                </li>
            <?php endif; ?>
            <?php if($business['BusinessPackage']['contact_form']): ?>
                <li>
                    <?php echo __d('business', "Enable contact us form") ?>
                </li>
            <?php endif; ?>
            <?php if($business['BusinessPackage']['follow']): ?>
                <li>
                    <?php echo __d('business', "Enable follow") ?>
                </li>
            <?php endif; ?>
            <?php if($business['BusinessPackage']['checkin']): ?>
                <li>
                    <?php echo __d('business', "Enable Checkin") ?>
                </li>
            <?php endif; ?>
            <?php if($business['BusinessPackage']['favourite']): ?>
                <li>
                    <?php echo __d('business', "Enable favorite") ?>
                </li>
            <?php endif; ?>
    </ul>
</span>
</div>
<div class="upgrade_title">
<?php echo __d('business', 'Upgrade'); ?>
</div>
<div class="upgrade_package">
    
<?php if(!empty($packages)): ?>
    <?php
        $planType = array('day'=>__d('business', 'Day'),'week' => __d('business', 'Week'), 'month' => __d('business', 'Month'), 'year' => __d('business', 'Year'), 'forever' => __d('business', 'Forever') );
    ?>
    <?php foreach($packages as $key=>$package) : ?>
        <div class="package_item <?php if($package['BusinessPackage']['most_popular']): ?>most_popular<?php endif; ?>">
        <div class="border_package"></div>
        <div>
            <?php if($package['BusinessPackage']['most_popular']): ?>
                <i class="flag_popular"></i>
            <?php endif; ?>
            <div class="package_head bg_<?php if($key%3 == 0) echo 'grey'; elseif($key%3 == 1) echo 'blue'; else echo 'green';  ?>">
            <div class="package_item_title">
                <?php echo $package['BusinessPackage']['name']; ?>
            </div>
            <div class="paclage_item_price">
                <span><?php //echo __d('business', 'Price') ?></span> 
                <span><?php echo $currency['Currency']['symbol']; ?><?php echo $package['BusinessPackage']['price'] ?></span> 
            </div>
            <div class="paclage_item_stat">
                <?php if($package['BusinessPackage']['type'] == BUSINESS_ONE_TIME): ?>
                    <?php echo __d('business', 'One Time Payment') ?>
                <?php else : ?>
                    <span><?php echo __d('business', 'Billing Cycle') ?></span> 
                    <?php if($package['BusinessPackage']['duration'] > 1): ?>
                        <span><?php echo $package['BusinessPackage']['billing_cycle'] ?> <?php echo$planType[$package['BusinessPackage']['billing_cycle_type']] ?>s</span> 
                    <?php else : ?>
                       <span><?php echo $package['BusinessPackage']['billing_cycle'] ?> <?php echo$planType[$package['BusinessPackage']['billing_cycle_type']] ?></span> 
                    <?php endif; ?>
                    
                <?php endif; ?>
                
                <div class="package_item_duration">
                    <span><?php echo __d('business', 'Duration') ?></span> 
                    <span>
                        <?php if($package['BusinessPackage']['duration_type'] == 'forever'): ?>
                            <?php echo $planType[$package['BusinessPackage']['duration_type']] ?>
                        <?php else: ?>
                            <?php if($package['BusinessPackage']['duration'] > 1): ?>
                                <?php echo $package['BusinessPackage']['duration'] ?> <?php echo $planType[$package['BusinessPackage']['duration_type']] ?>s
                            <?php else : ?>
                               <?php echo $package['BusinessPackage']['duration'] ?> <?php echo $planType[$package['BusinessPackage']['duration_type']] ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </span> 
                </div>
            </div>
            </div>
            <ul class="paclage_item_options">
                <?php if($package['BusinessPackage']['manage_admin']): ?>
                    <li>
                        <?php echo __d('business', "Can manage admins") ?>
                    </li>
                <?php endif; ?>
                <?php if($package['BusinessPackage']['response_review']): ?>
                    <li>
                        <?php echo __d('business', "Can response a review") ?>
                    </li>
                <?php endif; ?>
                <?php if($package['BusinessPackage']['send_verification_request']): ?>
                    <li>
                        <?php echo __d('business', "Can send verification request") ?>
                    </li>
                <?php endif; ?>
                <?php if($package['BusinessPackage']['contact_form']): ?>
                    <li>
                        <?php echo __d('business', "Enable contact us form") ?>
                    </li>
                <?php endif; ?>
                <?php if($package['BusinessPackage']['follow']): ?>
                    <li>
                        <?php echo __d('business', "Enable follow") ?>
                    </li>
                <?php endif; ?>
                <?php if($package['BusinessPackage']['checkin']): ?>
                    <li>
                        <?php echo __d('business', "Enable Checkin") ?>
                    </li>
                <?php endif; ?>
                <?php if($package['BusinessPackage']['favourite']): ?>
                    <li>
                        <?php echo __d('business', "Enable favorite") ?>
                    </li>
                <?php endif; ?>
            </ul>

            <?php $package_trial = $businessHelper->getPackageTrial($business['Business']['id'], $package['BusinessPackage']['id']); ?>
            <div class="package_form">
                <?php if(!empty($package_trial)): ?>
                    <form id="trial_payment" method="post" action="<?php echo $this->request->base ?>/business_payment/<?php echo $is_app ? "?app_no_tab=1" : "";?>">
                        <?php echo $this->Form->hidden('business_package_id', array('value' => $package_trial['BusinessPackage']['id'])); ?>
                        <?php echo $this->Form->hidden('business_id', array('value' => $business['Business']['id'])); ?>
                        <?php echo $this->Form->hidden('pay_type', array('value' => 'business_package')); ?>
                        <button type="submit" class="btn btn-action"><?php echo __d('business', 'Free Trial') ?></button>
                    </form>
                <?php endif; ?>
                <form id="main_payment" method="post" action="<?php echo $this->request->base ?>/business_payment/<?php echo $is_app ? "?app_no_tab=1" : "";?>">
                    <?php echo $this->Form->hidden('business_package_id', array('value' => $package['BusinessPackage']['id'])); ?>
                    <?php echo $this->Form->hidden('business_id', array('value' => $business['Business']['id'])); ?>
                    <?php echo $this->Form->hidden('pay_type', array('value' => 'business_package')); ?>
                    <button type="submit" class="btn btn-action"><?php echo __d('business', 'Upgrade Now') ?></button>
                </form>
            </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <?php echo __d('business', "No Package found"); ?>
<?php endif; ?>
</div>