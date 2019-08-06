<?php echo $this->Html->css(array(
    'fineuploader',
    'Business.business'), array('block' => 'css', 'minify'=>false));
?>

<?php 
    $businessHelper = MooCore::getInstance()->getHelper('Business_Business');
    $branchTimes = !empty($branch['BusinessTime']) ? $branch['BusinessTime'] : null;
    $paymentSelect = !empty($branch['PaymentSelect']) ? $branch['PaymentSelect'] : null;
    $businessCategories = !empty($branch['BusinessCategory']) ? $branch['BusinessCategory'] : null;
    $branch = $branch['Business'];
?>

<?php $this->Html->scriptStart(array(
    'inline' => false, 
    'domReady' => true, 
    'requires' => array('jquery', 'mooBusiness', 'business_timeentry', 'business_jqueryui'), 
    'object' => array('$', 'mooBusiness')
));?>
    mooBusiness.initBusinessBranch(<?php echo $branchTimes == null ? 1 : 0?>);
<?php $this->Html->scriptEnd(); ?>
    
<div class="create_form">
    <div class="box3">
        <form id="formBranch">
            <?php
                if($is_app)
                {
                    echo $this->Form->hidden('back_and_refresh', array('value' => isset($this->request->query['back_and_refresh']) ? $this->request->query['back_and_refresh'] : 0));
                }
            ?>
            <?php
                echo $this->Form->hidden('parent_id', array('value' => $business_id));
            ?>
            <?php
                echo $this->Form->hidden('id', array('value' => $branch['id']));
            ?>
            <div class="mo_breadcrumb">
                <h1>
                    <?php
                    if (empty($branch['id'])) {
                        echo __d('business', 'Add new sub page');
                    } else {
                        echo __d('business', 'Edit sub page');
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
                                    'value' => $branch['business_type_id'],
                                    'empty' => false
                                )); ?>
                            </div>
                            <div class="clear"></div>
                        </li>
                        <li>
                            <div class="col-md-3">
                                <label>
                                    <?php echo __d('business', 'Sub Page Name') ?>
                                    <span class="required">*</span>
                                </label>
                            </div>
                            <div class="col-md-9">
                                <?php echo $this->Form->text('name', array(
                                    'value' => $branch['name']
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
                                    'value' => $branch['company_number']
                                )); ?>
                            </div>
                            <div class="clear"></div>
                        </li>
                        <li>
                            <div class="col-md-3">
                                <label><?php echo __d('business', 'Description') ?></label>
                            </div>
                            <div class="col-md-9">
                                <?php echo $this->Form->tinyMCE('description', array(
                                    'id' => 'editor',
                                    'value' => $branch['description']
                                )); ?>
                            </div>
                            <div class="clear"></div>
                        </li>
                        <li>
                            <div class="addon_avatar create_paading_right">
                                <div class="col-md-3">
                                    <label>
                                        <?php echo __d('business', 'Logo') ?>
                                        <span class="required">*</span>
                                    </label>
                                </div>
                                <div class="col-md-9">
                                    <?php echo $this->Form->hidden('logo', array(
                                        'value' => $branch['logo']
                                    )); ?>
                                    <div id="select-0" style="margin: 10px 0 0 0px;"></div>
                                    <?php if (!empty($branch['logo'])): ?>
                                        <img width="150" src="<?php echo $this->request->base.'/'.BUSINESS_FILE_URL.$branch['logo'] ?>" id="business_logo" class="img_wrapper">
                                    <?php else: ?>
                                        <img width="150" src="" id="business_logo" class="img_wrapper" style="display: none;">
                                    <?php endif; ?>

                                </div>
                            </div>
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
                                    'value' => $branch['address']
                                )); ?>
                                <div>
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
                                    'value' => $branch['email']
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
                                    'value' => $branch['phone']
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
                                    'value' => $branch['fax']
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
                                    'value' => $branch['website']
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
                                    'value' => $branch['facebook']
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
                                    'value' => $branch['twitter']
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
                                    'value' => $branch['linkedin']
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
                                    'value' => $branch['youtube']
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
                                    'value' => $branch['instagram']
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
                                    'checked' => $branch['always_open'],
                                    'value' => 1,
                                    'hiddenField' => false,
                                )); ?>
                                <label for="always_open">
                                    <?php echo __d('business', 'Check this if your company always open') ?>
                                </label>
                                <div class="wrap_multi_item" id="openHours">
                                    <?php if($branchTimes != null):?>
                                        <?php 
                                            $array = array();
                                            $date_day = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
                                            foreach ($branchTimes as $item) {
                                                $array[$item['day']][] = $item;
                                            }
                                            uksort($array, function($key1, $key2) use ($date_day) {
                                                return (array_search($key1, $date_day) > array_search($key2, $date_day));
                                            });
                                            $old_day = '';
                                            $k = 0;
                                            foreach ($array as $item): 
                                            foreach($item as $branchTime):
                                                $k++;
                                                $time_open = substr( $branchTime['time_open'],  0, 5);
                                                $time_close = substr( $branchTime['time_close'],  0, 5);
                                                $next_day = $branchTime['next_day'] ? '1' : '0';
                                                $shift = false;
                                                if($old_day != $branchTime['day'])
                                                {
                                                    $old_day = $branchTime['day'];
                                                }
                                        ?>
                                            <div class="multi_item <?php if($shift):?>shift<?php endif;?>" data-id="<?php echo $k + 1;?>">
                                                <div class="col-md-4">
                                                    <?php echo $this->Form->select('day.', $days, array(
                                                        'empty' => false,
                                                        'value' => $branchTime['day'],
                                                        'style' => $shift ? 'display:none' : ''
                                                    )); ?>
                                                </div>
                                                <div class="col-md-4">
                                                    <?php echo $this->Form->select('time_open.', $times_open, array(
                                                        'value' => '0 ' . $time_open,
                                                        'class' => 'time_open'
                                                    )); ?>
                                                </div>
                                                <div class="col-md-4">
                                                    <?php echo $this->Form->select('time_close.', $times_close, array(
                                                        'value' => $next_day . ' ' .  $time_close,
                                                        'class' => 'time_close'
                                                    )); ?>
                                                </div>
                                                <div class="add_more">
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
                                <br/>
                                <label>
                                    <?php echo __d('business', 'Timezone') ?>
                                </label>
                                <?php echo $this->Form->select('timezone', $this->Moo->getTimeZones(), array(
                                    'value' => empty($branch['timezone']) ? 'Europe/London' :$branch['timezone'],
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
                                <?php if($branchPayments != null):?>
                                    <?php foreach($branchPayments as $branchPayment):
                                        $branchPayment = $branchPayment['BusinessPayment'];
                                    ?>
                                    <div class="col-md-6">
                                        <?php echo $this->Form->checkbox('business_payment_id.', array(
                                            'checked' => !empty($paymentSelect) && in_array($branchPayment['id'], $paymentSelect) ? 1 : 0,
                                            'id' => 'business_payment'.$branchPayment['id'],
                                            'value' => $branchPayment['id'],
                                            'hiddenField' => false
                                        )); ?>
                                        <label for="business_payment<?php echo $branchPayment['id'];?>">
                                            <?php echo $branchPayment['name'];?>
                                        </label>
                                    </div>
                                    <?php endforeach;?>
                                <?php endif;?>
                            </div>
                            <div class="clear"></div>
                        </li>
                        <li>
                            <div class="col-md-3">
                                <label><?php echo __d('business', 'Photos') ?></label>
                            </div>
                            <div class="col-md-9">
                                <?php echo $this->Element('misc/upload_photo', array(
                                    'business_photos' => $branch_photos
                                ));?>
                            </div>
                            <div class="clear"></div>
                        </li>
                        <li>
                            <div class="col-md-3"></div>

                            <div class="col-md-9">           
                                <a href="javascript:void(0)" class="button" id="createButton">
                                    <?php echo __d('business', 'Save')?>
                                </a>
                                <a href="javascript:void(0)" onclick="<?php echo $is_app ? "window.mobileAction.backOnly();" : "window.location = '".$this->request->base.'/businesses'."'";?>" class="button" id="cancelButton">
                                    <?php echo __d('business', 'Cancel')?>
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </form>
    </div>
    <div class="error-message" id="errorMessage" style="display:none"></div>
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