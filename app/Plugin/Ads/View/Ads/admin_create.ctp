<?php
    echo $this->Html->css(array(
        'fineuploader',
        'jquery-ui', 
        'footable.core.min',
        '/commercial/css/commercial-admin.css',
        '/commercial/css/jquery-ui'), null, array('inline' => false));
    echo $this->Html->script(array(
        'vendor/jquery.fileuploader',
        'footable',
        '/commercial/js/commercial',
        '/commercial/js/jquery-ui'), array('inline' => false));
    $this->Html->addCrumb(__d('ads',  'Plugins Manager'), '/admin/plugins');
    $this->Html->addCrumb(__d('ads',  "Manage Campaigns"), '/admin/ads');
    if($ads_campaign['id'] > 0)
    {
        $this->Html->addCrumb(__d('ads',  "Edit Campaign"));
    }
    else 
    {
        $this->Html->addCrumb(__d('ads',  "Create Campaign"));
    }
    $this->startIfEmpty('sidebar-menu');
    echo $this->element('admin/adminnav', array('cmenu' => 'Ads'));
    $this->end();
?>
<?php echo $this->Moo->renderMenu('Ads', __d('ads','Manage Ad Campaigns'));?>
<div id="page-wrapper">
    <div class="portlet box">
        <div class="portlet-title">
            <?php if($ads_campaign['id'] > 0):?>
                <?php echo __d('ads', "Edit Campaign");?>
            <?php else:?>
                <?php echo __d('ads', "Create Campaign");?>
            <?php endif;?>
            <div class="clear"></div>
        </div>
        <div class="portlet-body form">
            <form class="form-horizontal" id='formAds' method="post">
                <?php echo $this->Form->hidden('id', array(
                    'value' => $ads_campaign['id']
                ));?>
                <?php echo $this->Form->hidden('ads_image', array(
                    'value' => $ads_campaign['ads_image']
                ));?>
                <?php echo $this->Form->hidden('ads_type');?>
                <?php echo $this->Form->hidden('ads_path', array(
                    'value' => !empty($ads_campaign['ads_image']) ? $this->request->base.ADS_BANNER_URL.$ads_campaign['ads_image'] : ''
                ));?>
                <?php echo $this->Form->hidden('placement_period');?>
                <h4><?php echo __d('ads', "Client Info");?></h4>
                <hr>
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('ads', "Client Name");?> <span class="required" style="color: red">(*)</span></label>
                    <div class="col-md-9">
                        <?php echo $this->Form->input("client_name", array(
                            'div' => false,
                            'label' => false,
                            'class' => 'form-control',
                            'value' => $ads_campaign['client_name']
                        ));?>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('ads', "Email");?> <span class="required" style="color: red">(*)</span></label>
                    <div class="col-md-9">
                        <?php echo $this->Form->input("email", array(
                            'div' => false,
                            'label' => false,
                            'class' => 'form-control',
                            'value' => $ads_campaign['email']
                        ));?>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('ads', "Note");?></label>
                    <div class="col-md-9">
                        <?php echo $this->Form->textarea("note", array(
                            'div' => false,
                            'label' => false,
                            'class' => 'form-control',
                            'value' => $ads_campaign['note']
                        ));?>
                    </div>
                    <div class="clear"></div>
                </div>
                <h4><?php echo __d('ads', "Campaign");?></h4>
                <hr>
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('ads', "Campaign Name");?> <span class="required" style="color: red">(*)</span></label>
                    <div class="col-md-9">
                        <?php echo $this->Form->input("name", array(
                            'div' => false,
                            'label' => false,
                            'class' => 'form-control',
                            'value' => $ads_campaign['name']
                        ));?>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('ads', "Placement");?> <span class="required" style="color: red">(*)</span></label>
                    <div class="col-md-9">
                        <?php echo $this->Form->select("ads_placement_id", $ads_placement, array(
                            'div' => false,
                            'label' => false,
                            'class' => 'form-control',
                            'value' => $ads_campaign['ads_placement_id'],
                            'empty' => __d('ads', "Select position"),
                            'onchange' => 'jQuery.ads.loadPlacementDetail()'
                        ));?>
                        <a class="view_all" href="javascript:void(0)" onclick="jQuery.ads.viewPlacements()">
                            <?php echo __d('ads', "View all");?>
                        </a>
                        <div class="clear"></div>
                                        <br/>
                        <a href="javascript:void(0)" id="placementDetail" onclick="jQuery.ads.viewPlacementDetail(this)" data-id="" style="display: none">
                            <?php echo __d('ads', "Placement position");?>
                        </a>
                        <span id="placement_detail"></span>
                    </div>
                    <div class="clear"></div>
                </div>
                <?php if($ads_campaign['id'] == null || $ads_campaign['item_status'] == ADS_STATUS_PENDING):?>
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('ads', "Planned start date");?><span class="required" style="color: red">(*)</span></label>
                    <div class="col-md-9 ads_placement_empty">
                        <?php echo __d('ads', "Please select placement");?>
                    </div>
                    <div class="col-md-9 ads_placement" style="display: none">
                        <div class="col-md-3">
                            <?php echo $this->Form->input("start_date", array(
                                'div' => false,
                                'label' => false,
                                'class' => 'form-control',
                                'value' => !empty($ads_campaign['set_date']) ? date('m/d/Y', strtotime($ads_campaign['set_date'])) : ''
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
                                'value' => !empty($ads_campaign['set_date']) ? date('H', strtotime($ads_campaign['set_date'])) : ''
                            ));?>
                        </div>
                        <div class="col-md-1 age_and"><?php echo __d('ads','Hours') ?></div>
                        <div class="col-md-2">
                            <?php 
                            echo $this->Form->select("start_min", $minute, array(
                                'div' => false,
                                'label' => false,
                                'class' => 'form-control',
                                'value' => !empty($ads_campaign['set_date']) ? date('i', strtotime($ads_campaign['set_date'])) : ''
                            ));?>
                        </div>
                        <div class="col-md-1 age_and"><?php echo __d('ads','Minutes') ?></div>
                        <div class="clear"></div>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('ads', "Estimated end date");?></label>
                    <div class="col-md-9 ads_placement_empty">
                        <?php echo __d('ads', "Please select placement");?>
                    </div>
                    <div class="col-md-9 ads_placement" style="display: none" id="end_date"></div>
                    <div class="clear"></div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('ads', "Timezone");?></label>
                    <div class="col-md-9 ads_placement_empty">
                        <?php echo __d('ads', "Please select placement");?>
                    </div>
                    <div class="col-md-9 ads_placement" style="display: none">
                        <?php echo $this->Form->select('timezone', $this->Moo->getTimeZones(), array(
                            'class' => 'form-control',
                            'empty' => false,
                            'value' => !empty($ads_campaign['timezone']) ? $ads_campaign['timezone'] : $cuser['timezone']
                        )); ?>
                        <p>
                            <?php echo __d('ads', 'This is just planned start date. This date will change depends on the day you made payment. If you made payment before planned start date, ad will active and visible to target users at this planned date. If you made payment after planned date, ad will active right after the payment is made.');?>
                        </p>
                    </div>
                    <div class="clear"></div>
                </div>
                <?php endif;?>
                <h4><?php echo __d('ads', "Banner");?></h4>
                <hr>
                <div class="ads_placement_empty">
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo __d('ads', "Please select placement");?></label>
                        <div class="clear"></div>
                    </div>
                </div>
                <div style="display: none" class="ads_placement">
                    <div class="form-group for_html" style="display: none">
                        <label class="col-md-3 control-label"><?php echo __d('ads', "Ads Title");?></label>
                        <div class="col-md-9">
                            <?php echo $this->Form->input("ads_title", array(
                                'div' => false,
                                'label' => false,
                                'class' => 'form-control',
                                'value' => h($ads_campaign['ads_title']),
                                'onkeyup' => 'jQuery.ads.remainingCharacters(this, 25, "ads_title_remaining")',
                                'maxlength' => 25
                            ));?>
                            <?php echo sprintf(__d('ads', 'You have %s characters remaining'), '<span id="ads_title_remaining">25</span>');?>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo __d('ads', "Banner Image");?> <span class="required" style="color: red">(*)</span></label>
                        <div class="col-md-9">
                            <span id="required_size"></span>
                            <div id="product_image"></div>
                            <div id="product_image_preview"></div>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo __d('ads', "Banner Link");?> <span class="required" style="color: red">(*)</span></label>
                        <div class="col-md-9">
                            <?php echo $this->Form->input("link", array(
                                'div' => false,
                                'label' => false,
                                'class' => 'form-control',
                                'value' => $ads_campaign['link']
                            ));?>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="form-group for_html" style="display: none">
                        <label class="col-md-3 control-label"><?php echo __d('ads', "Description");?></label>
                        <div class="col-md-9">
                            <?php echo $this->Form->input("description", array(
                                'div' => false,
                                'label' => false,
                                'class' => 'form-control',
                                'id' => 'ads_description',
                                'value' => $ads_campaign['description'],
                                'onkeyup' => 'jQuery.ads.remainingCharacters(this, 90, "ads_description_remaining")',
                                'maxlength' => 90
                            ));?>
                            <?php echo sprintf(__d('ads', 'You have %s characters remaining'), '<span id="ads_description_remaining">90</span>');?>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-2">
                        </div>
                        <div class="col-md-9">
                            <a href="javascript:void(0)" class="pull-right" onclick="jQuery.ads.previewBanner()">
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
                    </div>
                </div>
                <h4><?php echo __d('ads', "Audience");?></h4>
                <!--<div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('ads', "User Group");?></label>
                    <div class="col-md-9">
                        <?php
                            //echo $this->element('Ads.ads/permissions',array('permission' => $ads_campaign['role_id']));
//                        echo $this->Form->select("role_id", $roles, array(
//                            'div' => false,
//                            'label' => false,
//                            'class' => 'form-control',
//                            'value' => $ads_campaign['role_id'],
//                            'empty' => array('0' => __d('ads', "All"))
//                        ));?>
                    </div>
                    <div class="clear"></div>
                </div>-->
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('ads', "Age Between");?></label>
                    <div class="col-md-2">
                        <?php echo $this->Form->input("age_from", array(
                            'div' => false,
                            'label' => false,
                            'class' => 'form-control',
                            'value' => $ads_campaign['age_from'],
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
                            'value' => $ads_campaign['age_to'],
                        ));?>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('ads', "Gender");?></label>
                    <div class="col-md-9">
                        <?php echo $this->Form->select("gender", $gender, array(
                            'div' => false,
                            'label' => false,
                            'class' => 'form-control',
                            'value' => $ads_campaign['gender'],
                            'empty' => array('0' => __d('ads', "All"))
                        ));?>
                        <span style="font-style: italic"><?php echo __d('ads', "Note: Guest user will not be affected by age and gender setting");?></span>
                    </div>
                    <div class="clear"></div>
                </div>
            </form>
            <div class="form-actions">
                <div class="row">
                    <div class="col-md-offset-3 col-md-9">
                        <button class="btn btn-circle btn-action" id="createButton" onclick="saveCampaign()">
                            <?php echo __d('ads', "Save");?>
                        </button>
                        <a class="btn btn-circle btn-gray" id="cancelButton" href="<?php echo $admin_url;?>">
                            <?php echo __d('ads', "Cancel");?>
                        </a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-offset-3 col-md-6">
                        <div style="display:none;margin-top:10px" class="alert alert-danger error-message"></div>
                    </div>
                </div>
            </div>
        </div>	
    </div>
</div>

<?php if($this->request->is('ajax')): ?>
<script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
<?php endif; ?>
    
mooPhrase.add("click_to_upload", '<?php echo __d('ads', 'Click to upload')?>');
mooPhrase.add("can_not_upload_file_more_than", '<?php echo __d('ads', 'Can not upload file more than ').' '.$file_max_upload?>');
jQuery(window).load(function(){
    jQuery('#formAds')[0].reset();
    jQuery.ads.initAdsUploader();
    <?php if($ads_campaign['id'] > 0):?> 
        jQuery.ads.loadPlacementDetail();
    <?php endif;?>
});
    
//datetime picker
jQuery(function() {
    jQuery("#start_date").datepicker({
        changeMonth: true,
        onSelect: function() {
            jQuery.ads.calcualteEndDate();
        }
    });
});

function saveCampaign()
{
    disableButton('createButton');
    disableButton('cancelButton');
    jQuery(".error-message").hide();
    jQuery(".alert-success").hide();

    //save data
    jQuery.post("<?php echo $this->request->base.'/admin/commercial/';?>save", jQuery("#formAds").serialize(), function(data){
        var json = $.parseJSON(data);
        if(json.result == 0)
        {
            jQuery(".error-message").show();
            jQuery(".error-message").html(json.message);
            enableButton('createButton');
            enableButton('cancelButton');
        }
        else
        {
            window.location = json.location;
        } 
    });
}

<?php if($this->request->is('ajax')): ?>
</script>
<?php else: ?>
<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>