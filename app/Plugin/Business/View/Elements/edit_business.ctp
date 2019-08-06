<?php
echo $this->Html->css(array(
    'fineuploader',
    'jquery-ui',
    'Business.business'), array('block' => 'css', 'minify'=>false));
?>

<?php 
    $businessHelper = MooCore::getInstance()->getHelper('Business_Business');
    $businessTimes = !empty($business['BusinessTime']) ? $business['BusinessTime'] : null;
    $businessAddresses = !empty($business['BusinessAddress']) ? $business['BusinessAddress'] : null;
    $businessCategories = !empty($business['BusinessCategory']) ? $business['BusinessCategory'] : null;
    $paymentSelect = !empty($business['PaymentSelect']) ? $business['PaymentSelect'] : null;
    $aOwner = !empty($business['User']) ? $business['User'] : null;
    $businessPackage = !empty($business['BusinessPackage']) ? $business['BusinessPackage'] : null;
    $business = $business['Business'];
	$original_business = !empty($original_business) ? $original_business['Business'] : $business;
    echo $this->addPhraseJs(array(
        'warning_accept' => __d('business', '%s is in progress of editting the business, it\'s not ready to accept/reject now.', '<a href="' . $aOwner['moo_href'] . '">' . $aOwner['moo_title'] . '</a>')
    ));
?>

<?php $this->Html->scriptStart(array(
    'inline' => false, 
    'domReady' => true, 
    'requires' => array('jquery', 'mooBusiness', 'business_timeentry', 'business_jqueryui'), 
    'object' => array('$', 'mooBusiness')
));?>
    mooBusiness.initCreateBusiness(<?php echo $businessTimes == null ? 1 : 0?>, <?php echo $businessCategories == null ? 1 : 0?>);

<?php $this->Html->scriptEnd(); ?>
    
<div class="create_form">
    <div class="box3">
    <?php if(!empty($claim_id) || !empty($business['claim_id'])): ?>
        <?php if((isset($business['is_claim']) && $business['is_claim'] == 3 && $cuser['Role']['is_admin']) || (isset($business['is_claim']) && $business['is_claim'] == 2 && $cuser['Role']['is_admin'] && $aOwner['id'] != $cuser['id'])): ?>
            <div class="alert alert-success fade in">
                <?php echo __d('business', "Please review the changes made by %s below. If everything is okay, please hit the \"Accept this claim request\" button to publish the changes and transfer to owner of this business page to %s, all other claimed request will be auto deteted. %sClick here%s to view other claimed request for this business.", '<a href="' . $aOwner['moo_href'] . '">' . $aOwner['moo_title'] . '</a>', '<a href="' . $aOwner['moo_href'] . '">' . $aOwner['moo_title'] . '</a>', '<a href="' . $this->request->base . '/admin/business/business_claims/">', '</a>'); ?>
            </div>
            <p>
                <?php if(isset($business['is_claim']) && $business['is_claim'] == 3): ?>
                <button id="claimReviewing" class="btn btn-action topButton" data-business_id="<?php echo $business['id']; ?>">
                    <?php echo __d('business', 'Accept this claim request'); ?>
                </button>
                <button id="claimReject" class="btn btn-action topButton" data-business_id="<?php echo $business['id']; ?>">
                    <?php echo __d('business', 'Reject'); ?>
                </button>
                <?php else: ?>
                <button class="btn btn-action topButton claimNotReady">
                    <?php echo __d('business', 'Accept this claim request'); ?>
                </button>
                <button class="btn btn-action topButton claimNotReady">
                    <?php echo __d('business', 'Reject'); ?>
                </button>
                <?php endif; ?>
            </p>
        <?php else: ?>
            <div class="alert alert-success fade in">
                <p><?php echo __d('business', "Your changes need around 24hrs to be reviewed and approved. Once it's approved, the changes will be published and you will become the owner of this business page. This listing can not be claimed again."); ?></p>
                <?php if(!empty($business['claim_id'])): ?>
                <p><?php echo __d('business', "Important: If you updated the below info, you need to hit \"Submit for reviewing\" button to notify admin to let them know that new info is added and ready to review again."); ?></p>
                <?php endif; ?>
            </div>
            <p>
                <button id="claimSubmit" class="btn btn-action topButton<?php echo (isset($business['is_claim']) && $business['is_claim'] == 2) ? '' : ' disabled'; ?>" data-business_id="<?php echo (isset($business['is_claim']) && $business['is_claim'] == 2) ? $business['id'] : ''; ?>"><?php echo __d('business', 'Submit for reviewing'); ?></button>
                <?php if(!empty($business['claim_id'])): ?>
                <button id="claimRemove" class="btn btn-action topButton" data-business_id="<?php echo $business['id']; ?>"><?php echo __d('business', 'Remove claim request'); ?></button>
                <?php endif; ?>
            </p>
        <?php endif; ?>
    <?php endif; ?>
    </div>
    <div class="box3">
        <form id="formBusiness">
            <?php
                echo $this->Form->hidden('id', array('value' => $business['id']));
                if(!empty($claim_id)){
                    echo $this->Form->hidden('claim_id', array('value' => $claim_id));
                } else {
                    echo $this->Form->hidden('claim_id', array('value' => $business['claim_id']));
                }
            ?>
            <div class="mo_breadcrumb">
                <h1>
                    <?php
                    if(!empty($claim_id) || !empty($business['claim_id'])){
                        echo __d('business', 'Claim Business');
                    } else if (empty($business['id'])) {
                        echo __d('business', 'Add new business');
                    } else {
                        echo __d('business', 'Edit business');
                    }
                    ?>
                </h1>
            </div>
            <div class="full_content p_m_10">
                <div class="form_content">
                    <ul>
                        <li>
                            <div class="col-md-3">
                                <label>
                                    <?php echo __d('business', 'Category') ?>
                                    <span class="required">*</span>
                                </label>
                            </div>
                            <div class="col-md-9">
                                <div class="wrap_multi_item" id="categoryList"></div>
                            </div>
                            <div class="clear"></div>
                        </li>
                        <li>
                            <div class="col-md-3">
                                <label>
                                    <?php echo __d('business', 'Business Type') ?>
                                    <span class="required">*</span>
                                </label>
                            </div>
                            <div class="col-md-9">
                                <?php echo $this->Form->select('business_type_id', $businessTypes, array(
                                    'value' => $business['business_type_id'],
                                    'empty' => false
                                )); ?>
                            </div>
                            <div class="clear"></div>
                        </li>
                        <li>
                            <div class="col-md-3">
                                <label>
                                    <?php echo __d('business', 'Company Name') ?>
                                    <span class="required">*</span>
                                </label>
                            </div>
                            <div class="col-md-9">
                                <?php echo $this->Form->text('name', array(
                                    'value' => $business['name']
                                )); ?>
                            </div>
                            <div class="clear"></div>
                        </li>
                        <li>
                            <div class="col-md-3">
                                <label><?php echo __d('business', 'Company Number') ?></label>
                            </div>
                            <div class="col-md-9">
                                <?php echo $this->Form->text('company_number', array(
                                    'value' => $business['company_number']
                                )); ?>
                            </div>
                            <div class="clear"></div>
                        </li>
                        <li>
                            <div class="col-md-3">
                                <label>
                                    <?php echo __d('business', 'About Company') ?>
                                </label>
                            </div>
                            <div class="col-md-9">
                                <?php echo $this->Form->tinyMCE('description', array(
                                    'id' => 'editor',
                                    'value' => $business['description']
                                )); ?>
                            </div>
                            <div class="clear"></div>
                        </li>
                        <li>
                            <div class="addon_avatar create_paading_right">
                                <div class="col-md-3">
                                    <label>
                                        <?php echo __d('business', 'Company Logo') ?>
                                        <span class="required">*</span>
                                    </label>
                                </div>
                                <div class="col-md-9">
                                    <?php echo $this->Form->hidden('logo', array(
                                        'value' => $business['logo']
                                    )); ?>
                                    <div id="select-0"></div>
                                    <?php if (!empty($business['logo'])): ?>
                                        <img width="150" src="<?php echo $this->Business->getPhoto($original_business, array('tag' => false)); ?>" id="business_logo" class="img_wrapper">
                                    <?php else: ?>
                                        <img width="150" src="" id="business_logo" class="img_wrapper" style="display: none;">
                                    <?php endif; ?>

                                </div>
                            </div>
                            <div class="clear"></div>
                        </li>
                        <li>
                            <div class="sub_form">
                                <?php echo __d('business', 'Head office information') ?>
                            </div>
                            <?php if(empty($business['id'])):?>
                            <div class="form_description">
                                <?php echo __d('business', 'You can add more locations at Dashboard later after the page is created') ?>
                            </div>
                            <?php endif;?>
                            <div class="clear"></div>
                        </li>
                        <li>
                            <div class="col-md-3">
                                <label>
                                    <?php echo __d('business', 'Address') ?>
                                    <span class="required">*</span>
                                </label>
                            </div>
                            <div class="col-md-9">
                                <?php echo $this->Form->text('address', array(
                                    'class' => 'address_value',
                                    'value' => $business['address']
                                )); ?>
                                <div class="form_description">
                                <a href="javascript:void(0)" id="view_map">
                                    <?php echo __d('business', 'View map');?>
                                </a>
                            </div>
                            </div>
                            
                            <div class="clear"></div>
                        </li>
                        <li>
                            <div class="col-md-3">
                                <label>
                                    <?php echo __d('business', 'Email') ?>
                                    <span class="required">*</span>
                                </label>
                            </div>
                            <div class="col-md-9">
                                <?php echo $this->Form->text('email', array(
                                    'value' => $business['email']
                                )); ?>
                            </div>
                            <div class="clear"></div>
                        </li>
                        <li>
                            <div class="col-md-3">
                                <label><?php echo __d('business', 'Phone') ?></label>
                            </div>
                            <div class="col-md-9">
                                <?php echo $this->Form->text('phone', array(
                                    'value' => $business['phone']
                                )); ?>
                            </div>
                            <div class="clear"></div>
                        </li>
                        <li>
                            <div class="col-md-3">
                                <label><?php echo __d('business', 'Fax') ?></label>
                            </div>
                            <div class="col-md-9">
                                <?php echo $this->Form->text('fax', array(
                                    'value' => $business['fax']
                                )); ?>
                            </div>
                            <div class="clear"></div>
                        </li>
                        <li>
                            <div class="col-md-3">
                                <label><?php echo __d('business', 'Website') ?></label>
                            </div>
                            <div class="col-md-9">
                                <?php echo $this->Form->text('website', array(
                                    'value' => $business['website']
                                )); ?>
                            </div>
                            <div class="clear"></div>
                        </li>
                        <li>
                            <div class="col-md-3">
                                <label><?php echo __d('business', 'Facebook page') ?></label>
                            </div>
                            <div class="col-md-9">
                                <?php echo $this->Form->text('facebook', array(
                                    'value' => $business['facebook']
                                )); ?>
                            </div>
                            <div class="clear"></div>
                        </li>
                        <li>
                            <div class="col-md-3">
                                <label><?php echo __d('business', 'Twitter page') ?></label>
                            </div>
                            <div class="col-md-9">
                                <?php echo $this->Form->text('twitter', array(
                                    'value' => $business['twitter']
                                )); ?>
                            </div>
                            <div class="clear"></div>
                        </li>
                        <li>
                            <div class="col-md-3">
                                <label><?php echo __d('business', 'Linkedin page') ?></label>
                            </div>
                            <div class="col-md-9">
                                <?php echo $this->Form->text('linkedin', array(
                                    'value' => $business['linkedin']
                                )); ?>
                            </div>
                            <div class="clear"></div>
                        </li>
                        <li>
                            <div class="col-md-3">
                                <label><?php echo __d('business', 'Youtube page') ?></label>
                            </div>
                            <div class="col-md-9">
                                <?php echo $this->Form->text('youtube', array(
                                    'value' => $business['youtube']
                                )); ?>
                            </div>
                            <div class="clear"></div>
                        </li>
                        <li>
                            <div class="col-md-3">
                                <label><?php echo __d('business', 'Instagram page') ?></label>
                            </div>
                            <div class="col-md-9">
                                <?php echo $this->Form->text('instagram', array(
                                    'value' => $business['instagram']
                                )); ?>
                            </div>
                            <div class="clear"></div>
                        </li>
                        <li>
                            <div class="col-md-3">
                                <label><?php echo __d('business', 'Business hours') ?></label>
                            </div>
                            <div class="col-md-8">
                                <?php echo $this->Form->checkbox('always_open', array(
                                    'checked' => $business['always_open'],
                                    'value' => 1,
                                    'hiddenField' => false,
                                )); ?>
                                <label for="always_open">
                                    <?php echo __d('business', 'Check this if your company always open') ?>
                                </label>
                                <div class="wrap_multi_item" id="openHours">
                                    <?php if($businessTimes != null):?>
                                        <?php 
                                            $array = array();
                                            $date_day = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
                                            foreach ($businessTimes as $item) {
                                                $array[$item['day']][] = $item;
                                            }
                                            uksort($array, function($key1, $key2) use ($date_day) {
                                                return (array_search($key1, $date_day) > array_search($key2, $date_day));
                                            });
                                            $old_day = '';
                                            $k = 0;
                                            foreach ($array as $item): 
                                            foreach($item as $businessTime):
                                                $k++;
                                                $time_open = substr( $businessTime['time_open'],  0, 5);
                                                $time_close = substr( $businessTime['time_close'],  0, 5);
                                                $next_day = $businessTime['next_day'] ? '1' : '0';
                                                $shift = false;
                                                if($old_day != $businessTime['day'])
                                                {
                                                    $old_day = $businessTime['day'];
                                                }
                                        ?>
                                            <div class="multi_item <?php if($shift):?>shift<?php endif;?>" data-id="<?php echo $k;?>">
                                                <div class="col-md-4 col-xs-12">
                                                    <?php echo $this->Form->select('day.', $days, array(
                                                        'empty' => false,
                                                        'value' => $businessTime['day'],
                                                        'style' => $shift ? 'display:none' : ''
                                                    )); ?>
                                                </div>
                                                <div class="col-md-4 col-xs-12">
                                                    <?php echo $this->Form->select('time_open.', $times_open, array(
                                                        'value' => '0 ' . $time_open,
                                                        'class' => 'time_open'
                                                    )); ?>
                                                </div>
                                                <div class="col-md-4 col-xs-12">
                                                    <?php echo $this->Form->select('time_close.', $times_close, array(
                                                        'value' => $next_day . ' ' .  $time_close,
                                                        'class' => 'time_close'
                                                    )); ?>
                                                </div>
                                                <div class="add_more add_more_btn_app">
                                                    <a class="btn_add_shift" href="javascript:void(0)" <?php if($shift):?>style="display: none"<?php endif;?>>
                                                        
                                                    </a>
                                                    <a class="btn_remove_shift" href="javascript:void(0)" <?php if(!$shift):?>style="display: none"<?php endif;?>>
                                                        
                                                    </a>
                                                    <?php if($k == 1):?>
                                                        <a class="btn_add_item" href="javascript:void(0)" data-wrapper_id="openHours" data-content_id="hours-content" data-suggest_cat="">
                                                            <i class="material-icons">add</i>
                                                        </a>
                                                    <?php endif;?>
                                                    <a class="btn_remove_item" href="javascript:void(0)" <?php if($k == 1 || $shift):?>style="display: none"<?php endif;?>>
                                                        <i class="material-icons">clear</i>
                                                    </a>
                                                    
                                                </div>
                                                <div class="clear"></div>
                                            </div>
                                        <?php endforeach;?>
                                        <?php endforeach;?>
                                    <?php endif;?>
                                </div>
                                
                            </div>
                            <div class="clear"></div>
                        </li>
                        <li>
                            <div class="col-md-3">
                                <label>
                                    <?php echo __d('business', 'Timezone') ?>
                                </label>
                            </div>
                            <div class="col-md-9">
                                <?php echo $this->Form->select('timezone', $this->Moo->getTimeZones(), array(
                                    'value' => empty($business['timezone']) ? 'Europe/London' :$business['timezone'],
                                    'empty' => false
                                )); ?>
                            </div>
                            <div class="clear"></div>
                        </li>
                        <li>
                            <div class="col-md-3">
                                <label><?php echo __d('business', 'We accept') ?></label>
                            </div>
                            <div class="col-md-9">
                                <?php if($businessPayments != null):?>
                                    <?php foreach($businessPayments as $businessPayment):
                                        $businessPayment = $businessPayment['BusinessPayment'];
                                    ?>
                                    <div class="col-md-6">
                                        <?php echo $this->Form->checkbox('business_payment_id.', array(
                                            'checked' => !empty($paymentSelect) && in_array($businessPayment['id'], $paymentSelect) ? 1 : 0,
                                            'id' => 'business_payment'.$businessPayment['id'],
                                            'value' => $businessPayment['id'],
                                            'hiddenField' => false
                                        )); ?>
                                        <label for="business_payment<?php echo $businessPayment['id'];?>">
                                            <?php echo $businessPayment['name'];?>
                                        </label>
                                    </div>
                                    <?php endforeach;?>
                                <?php endif;?>
                            </div>
                            <div class="clear"></div>
                        </li>
                        <li>
                            <div class="col-md-3">
                                <label>&nbsp;</label>
                            </div>
                            <div class="col-md-9">     
                                <?php if(empty($business['id']) || ($business['id'] > 0 && $permission_can_edit_page)):?>
                                <button type="button" class="btn btn-action" id="createBusinessButton">
                                    <?php echo __d('business', 'Save')?>
                                </button>
                                <?php endif;?>
                                <a href="javascript:void(0)" onclick="<?php echo $is_app ? "window.mobileAction.backOnly();" : "window.location = '".$this->request->base.'/businesses'."'";?>" class="button" id="cancelBusinessButton">
                                    <?php echo __d('business', 'Cancel')?>
                                </a>
                            </div>
                            <div class="clear"></div>
                        </li>
                    </ul>
                    <div class="error-message" id="errorMessage" style="display:none"></div>
                </div>
            </div>
        </form>
    </div>
</div>

<script id="hours-content" type="text/template">
    <div class="multi_item">
        <div class="col-md-4 col-xs-12">
            <?php echo $this->Form->select('day.', $days, array(
                'empty' => false
            )); ?>
        </div>
        <div class="col-md-4 col-xs-12">
            <?php echo $this->Form->select('time_open.', $times_open, array(
                'empty' => false,
                'value' => '0 00:00',
                'class' => 'time_open'
            )); ?>
        </div>
        <div class="col-md-4 col-xs-12">
            <?php echo $this->Form->select('time_close.', $times_close, array(
                'empty' => false,
                'value' => '0 00:30',
                'class' => 'time_close'
            )); ?>
        </div>
        <div class="add_more add_more_btn_app">
            <a class="btn_add_shift" href="javascript:void(0)">
                
            </a>
            <a class="btn_remove_shift" href="javascript:void(0)" style="display: none">
                
            </a>
            <a class="btn_add_item" href="javascript:void(0)" data-wrapper_id="openHours" data-content_id="hours-content" data-suggest_cat="">
                <i class="material-icons">add</i>
            </a>
            <a class="btn_remove_item" href="javascript:void(0)" style="display: none">
                <i class="material-icons">clear</i>
            </a>
        </div>
        <div class="clear"></div>
    </div>
</script>

<script id="categories-data" type="text/template">
<?php echo !empty($businessCategories) ? json_encode($businessCategories) : '';?>
</script>
<script id="categories-content" type="text/template">
    <div class="multi_item">
        <div class="input_group">
            <?php echo $this->Form->text('business_category.', array(
                'class' => 'cat_id',
                'id' => ''
            )); ?>
            <?php echo $this->Form->hidden('category_id.', array(
                'id' => '',
                'class' => 'category_id'
            ));?>
        </div>
        <div class="add_more">
            <a class="btn_add_item"  href="javascript:void(0)" data-wrapper_id="categoryList" data-content_id="categories-content" data-suggest_cat="1">
                <i class="material-icons">add</i>
            </a>
            <a tyle="display: none"  class="btn_remove_item"  href="javascript:void(0)">
                <i class="material-icons">clear</i>
            </a>
            
        </div>
    </div>
</script>

<?php
if($is_app)
{
    $this->MooGzip->script(array('zip'=>'mobile.action.bundle.js.gz','unzip'=>'MooApp.mobile.action.bundle'));
}
?>