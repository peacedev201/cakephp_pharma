<?php

echo $this->Html->css(array(
    'fineuploader',
    '/commercial/css/jquery-ui.css'), null, array('inline' => false));
echo $this->Html->css(array( 'fineuploader','/commercial/css/commercial.css' ));
	$this->addPhraseJs(array(
		'click_to_upload' => __d('ads','Click to upload'),
		'can_not_upload_file_more_than' => __d('question','Can not upload file more than').' '.$file_max_upload,
	));
?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true,
    'requires' => array('jquery', 'ads_main','ads_jquery-ui'),
    'object' => array('$', 'ads_main'))); ?>	
ads_main.initAds();
        <?php if($ad_placement_id): ?>
ads_main.initReport();
ads_main.loadPlacementDetail();
        <?php endif; ?>
<?php $this->Html->scriptEnd(); ?>
<div class="create_form">
    <div class="bar-content">
        <div class="content_center">
            <div class="box3">
                <div class="mo_breadcrumb">
                    <h1 class="store-form-name"><?php echo __d('ads', "Create Campaign");?></h1>	
                </div>
                <div class="createStore-content full_content p_m_10">
                    <div class="form_content">
                        <form class="form-horizontal" id="formAds" method="post">
                            <?php echo $this->Form->hidden('id');?>
                            <?php echo $this->Form->hidden('ads_image');?>
                            <?php echo $this->Form->hidden('ads_type');?>
                            <?php echo $this->Form->hidden('ads_path');?>
                            <?php echo $this->Form->hidden('placement_period');?>
                            <h1><?php echo __d('ads', "Client Info");?></h1>
                            <ul>
                                <li class="form-row">
                                    <div class="col-md-2">
                                        <label><?php echo __d('ads', "Client Name");?> <span class="required" style="color: red">(*)</span></label>
                                    </div>
                                    <div class="col-md-10">
                                        <?php echo $this->Form->input("client_name", array(
                                            'div' => false,
                                            'label' => false,
                                            'class' => 'form-control',
                                            'value' => !empty($user['name']) ? $user['name'] : ''
                                        ));?>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                                <li class="form-row">
                                    <div class="col-md-2">
                                        <label><?php echo __d('ads', "Email");?> <span class="required" style="color: red">(*)</span></label>
                                    </div>
                                    <div class="col-md-10">
                                        <?php echo $this->Form->input("email", array(
                                            'div' => false,
                                            'label' => false,
                                            'class' => 'form-control',
                                            'value' => !empty($user['email']) ? $user['email'] : ''
                                        ));?>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                                <li class="form-row">
                                    <div class="col-md-2">
                                        <label><?php echo __d('ads', "Note");?></label>
                                    </div>
                                    <div class="col-md-10">
                                        <?php echo $this->Form->textarea("note", array(
                                            'div' => false,
                                            'label' => false,
                                            'class' => 'form-control',
                                        ));?>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                            </ul>
                            <h1><?php echo __d('ads', "Campaign");?></h1>
                            <ul>
                                <li class="form-row">
                                    <div class="col-md-2">
                                        <label><?php echo __d('ads', "Campaign Name");?> <span class="required" style="color: red">(*)</span></label>
                                    </div>
                                    <div class="col-md-10">
                                        <?php echo $this->Form->input("name", array(
                                            'div' => false,
                                            'label' => false,
                                            'class' => 'form-control',
                                        ));?>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                                <li class="form-row">
                                    <div class="col-md-2">
                                        <label><?php echo __d('ads', "Placement");?> <span class="required" style="color: red">(*)</span></label>
                                    </div>
                                    <div class="col-md-10">
                                        <?php echo $this->Form->select("ads_placement_id", $ads_placement, array(
                                            'div' => false,
                                            'label' => false,
                                            'class' => 'form-control',
                                            'empty' => __d('ads', "Select position"),
                                            'value' => $ad_placement_id
                                            //'onchange' => 'jQuery.ads.loadPlacementDetail()'
                                        ));?>
                                        <a class="view_all" href="javascript:void(0)">
                                            <?php echo __d('ads', "View all");?>
                                        </a>
                                        <div class="clear"></div>
                                        <br/>
                                        <a href="javascript:void(0)" id="placementDetail" data-id="" style="display: none">
                                            <?php echo __d('ads', "Placement position");?>
                                        </a>
                                        <span id="placement_detail"></span>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                                <li class="form-row">
                                    <div class="col-md-2">
                                        <label><?php echo __d('ads', "Planned start date");?></label>
                                    </div>
                                    <div class="col-md-10 ads_placement_empty">
                                        <?php echo __d('ads', "Please select placement");?>
                                    </div>
                                    <div class="col-md-10 ads_placement" style="display: none">
                                        <div class="col-md-3">
                                            <?php echo $this->Form->input("start_date", array(
                                                'div' => false,
                                                'label' => false,
                                                'class' => 'form-control',
                                            ));?>
                                        </div>
                                        <div class="col-md-1 age_and">
                                            <?php echo __d('ads', "at");?>
                                        </div>
                                        <div class="col-md-2">
                                            <?php echo $this->Form->select("start_hour", $hour, array(
                                                'div' => false,
                                                'label' => false,
                                                'class' => 'form-control',
                                            ));?>
                                        </div>
                                        <div class="col-md-1 age_and"><?php echo __d('ads','Hours'); ?></div>
                                        <div class="col-md-2">
                                            <?php echo $this->Form->select("start_min", $minute, array(
                                                'div' => false,
                                                'label' => false,
                                                'class' => 'form-control',
                                            ));?>
                                        </div>
                                        <div class="col-md-1 age_and"><?php echo __d('ads','Minutes') ?></div>
                                        <div class="clear"></div>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                                <li class="form-row">
                                    <div class="col-md-2">
                                        <label><?php echo __d('ads', "Estimated end date");?></label>
                                    </div>
                                    <div class="col-md-10 ads_placement_empty">
                                        <?php echo __d('ads', "Please select placement");?>
                                    </div>
                                    <div class="col-md-10 ads_placement" style="display: none" id="end_date"></div>
                                    <div class="clear"></div>
                                </li>
                                <li class="form-row">
                                    <div class="col-md-2">
                                        <label><?php echo __d('ads', "Timezone");?></label>
                                    </div>
                                    <div class="col-md-10 ads_placement_empty">
                                        <?php echo __d('ads', "Please select placement");?>
                                    </div>
                                    <div class="col-md-10 ads_placement" style="display: none">
                                        <?php echo $this->Form->select('timezone', $this->Moo->getTimeZones(), array(
                                            'value' => !empty($cuser['timezone']) ? $cuser['timezone'] : Configure::read('core.timezone'),
                                            'empty' => false
                                        )); ?>
                                        <p>
                                            <?php echo __d('ads', 'This is just planned start date. This date will change depends on the day you made payment. If you made payment before planned start date, ad will active and visible to target users at this planned date. If you made payment after planned date, ad will active right after the payment is made.');?>
                                        </p>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                            </ul>
                            <h1><?php echo __d('ads', "Banner");?></h1>
                            <ul class="ads_placement_empty">
                                <li class="form-row">
                                    <div class="col-md-2">
                                        <label><?php echo __d('ads', "Please select placement");?></label>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                            </ul>
                            <ul style="display: none" class="ads_placement">
                                <li class="form-row for_html" style="display: none">
                                    <div class="col-md-2">
                                        <label><?php echo __d('ads', "Ads Title");?></label>
                                    </div>
                                    <div class="col-md-10">
                                        <?php echo $this->Form->input("ads_title", array(
                                            'div' => false,
                                            'label' => false,
                                            'class' => 'form-control',
                                          //  'onkeyup' => 'jQuery.ads.remainingCharacters(this, 25, "ads_title_remaining")',
                                            'maxlength' => 25,
                                            'id' =>'ads_title'
                                        ));?>
                                        <?php echo sprintf(__d('ads', 'You have %s characters remaining'), '<span id="ads_title_remaining">25</span>');?>
                                    </div>

                                    <div class="clear"></div>
                                </li>
                                <li class="form-row">
                                    <div class="col-md-2">
                                        <label><?php echo __d('ads', "Banner Image");?> <span class="required" style="color: red">(*)</span></label>
                                    </div>
                                    <div class="col-md-10">
                                        <span id="required_size"></span>
                                        <div id="product_image"></div>
                                        <div id="product_image_preview"></div>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                                <li class="form-row">
                                    <div class="col-md-2">
                                        <label><?php echo __d('ads', "Banner Link");?> <span class="required" style="color: red">(*)</span></label>
                                    </div>
                                    <div class="col-md-10">
                                        <?php echo $this->Form->input("link", array(
                                            'div' => false,
                                            'label' => false,
                                            'class' => 'form-control',
                                        ));?>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                                <li class="form-row for_html" style="display: none">
                                    <div class="col-md-2">
                                        <label><?php echo __d('ads', "Description");?></label>
                                    </div>
                                    <div class="col-md-10">
                                        <?php echo $this->Form->input("description", array(
                                            'div' => false,
                                            'label' => false,
                                            'class' => 'form-control',
                                            'id' => 'ads_description',
                                          //  'onkeyup' => 'jQuery.ads.remainingCharacters(this, 90, "ads_description_remaining")',
                                            'maxlength' => 90,
                                            'id'=>'ads_description'
                                        ));?>
                                        <?php echo sprintf(__d('ads', 'You have %s characters remaining'), '<span id="ads_description_remaining">90</span>');?>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                                <li class="form-row">
                                    <div class="col-md-2">
                                    </div>
                                    <div class="col-md-10">
                                        <a href="javascript:void(0)" class="pull-right" id="previewBanner">
                                            <?php echo __d('ads', "Preview");?>
                                        </a>
                                        <div id="previewBannerImage" style="display: none" class="col-md-12">
                                            <a href="javascript:void(0)">
                                                <img src="" />
                                            </a>
                                        </div>
                                        <div id="previewBannerHtml" style="display: none" class="col-md-12">
                                            <a href="javascript:void(0)">
                                                <img src="" />
                                            </a>
                                            <a href="javascript:void(0)" class="banner_title" target="_blank"></a>
                                            <div class="banner_description"></div>
                                        </div>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                            </ul>
                            <h1><?php echo __d('ads', "Audience");?></h1>
                            <ul>
                                <!--<li class="form-row">
                                    <div class="col-md-2">
                                        <label><?php echo __d('ads', "User Group");?></label>
                                    </div>
                                    <div class="col-md-10">
                                       <?php  //echo $this->element('Ads.ads/permissions',array('permission' => $ads_campaign['role_id'])); ?>
                                    </div>
                                    <div class="clear"></div>
                                </li>-->
                                <li class="form-row">
                                    <div class="col-md-2">
                                        <label><?php echo __d('ads', "Age Between");?></label>
                                    </div>
                                    <div class="col-md-2">
                                        <?php echo $this->Form->input("age_from", array(
                                            'div' => false,
                                            'label' => false,
                                            'class' => 'form-control',
                                        ));?>
                                    </div>
                                    <div class="col-md-1 age_and">
                                        <?php echo __d('ads', "and");?>
                                    </div>
                                    <div class="col-md-2">
                                        <?php echo $this->Form->input("age_to", array(
                                            'div' => false,
                                            'label' => false,
                                            'class' => 'form-control',
                                        ));?>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                                <li class="form-row">
                                    <div class="col-md-2">
                                        <label><?php echo __d('ads', "Gender");?></label>
                                    </div>
                                    <div class="col-md-10">
                                        <?php echo $this->Form->select("gender", $gender, array(
                                            'div' => false,
                                            'label' => false,
                                            'class' => 'form-control',
                                            'empty' => array('0' => __d('ads', "All"))
                                        ));?>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                            </ul>
                            <span style="font-style: italic"><?php echo __d('ads', "Note: Guest user will not be affected by age and gender setting");?></span>
                        </form>
                        <div class="col-md-2">&nbsp;</div>
                        <div class="col-md-10">
                            <div style="margin:20px 0">           
                                <a href="javascript:void(0)" class="button" id="createButton">
                                    <?php echo __d('ads', 'Submit')?>
                                </a>
                                <a href="<?php echo $this->request->base;?>" class="button" id="cancelButton">
                                    <?php echo __d('ads', 'Cancel')?>
                                </a>
                            </div>
                            <div class="error-message" id="errorMessage" style="display:none"></div>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>                
            </div>
        </div>
    </div>
</div>
<section class="modal fade" id="adsModal" role="basic" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content"></div>
        <?php if($this->request->is('androidApp') || $this->request->is('iosApp')): ?>
        <div class="modal-footer">
            <button class="btn btn-action mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1" data-dismiss="modal"><?php echo __('Close'); ?></button>
        </div>
        <?php endif; ?>
    </div>
</section>  
