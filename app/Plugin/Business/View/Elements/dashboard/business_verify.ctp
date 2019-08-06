<?php if($business['Business']['verify'] == BUSINESS_STATUS_VERIFY): ?>
    <div id="flashMessage" class="Metronic-alerts alert alert-danger fade in"><i class="verify_bus"></i><?php echo __d('business', 'Your business has been verified.'); ?></div>
<?php else: ?>
<?php
$this->addPhraseJs(array(
    'message' => __d('business', 'Message'),
    'maximun_number_documents' => __d('business', 'Maximum number documents for verification request is %s', 5),
));
?>

<?php $this->Html->scriptStart(array(
    'inline' => false, 
    'domReady' => true, 
    'requires' => array('jquery', 'mooBusiness'), 
    'object' => array('$', 'mooBusiness')
));?>
    mooBusiness.initBusinessVerify();
<?php $this->Html->scriptEnd(); ?>
    
<div class="featured_verify">
    <h3>
        <?php echo __d('business', 'Request a Verified Badge '); ?>
    </h3>

    <ul class="nav nav-tabs chart-tabs">
        <li class="active">
            <a id="verifyByPhone" data-toggle="tab" href="#portlet_tab1"><?php echo __d('business', 'Verify by Phone'); ?></a>
        </li>
        <li>
            <a data-toggle="tab" href="#portlet_tab2"><?php echo __d('business', 'Verify by Documents'); ?></a>
        </li>
    </ul>

    <div class="row" style="padding-top: 10px">
        <div class="col-md-12">
            <div class="tab-content">
                <div id="portlet_tab1" class="tab-pane active">
                    <div class="">
                        <form id='verifyByPhone' action="<?php echo $this->request->base . '/businesses/verifies/phone'; ?><?php echo $is_app ? "?app_no_tab=1" : "";?>" method="post">
                            <?php echo $this->Form->hidden('id', array('value' => $business['Business']['id'])); ?>
                            <div class="full_content p_m_10">
                                <div class="form_content">
                                    <ul>
                                        <li>
                                            <div class="col-md-12">
                                                <label>
                                                    <?php echo __d('business', 'Please type business phone number below, one of our advisors will call to verify.') ?>
                                                </label>
                                            </div>
                                            <div class="col-md-12">
                                                <?php echo $this->Form->text('phone_number'); ?>
                                            </div>
                                            <div class="clear"></div>
                                        </li>
                                    </ul>
                                    <br/>
                                    <div class="col-md-12">
                                        <div>
                                            <button type='submit' class='btn btn-action'><?php echo __d('business', 'Send request'); ?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div id="portlet_tab2" class="tab-pane">
                    <div class="">
                        <div class="description">
                            <p><?php echo __d('business', 'Please upload the documents, one of our advisors will contact you to verify.'); ?></p>
                        </div>
                        <form id='verifyByDocuments' action="<?php echo $this->request->base . '/businesses/verifies/documents'; ?><?php echo $is_app ? "?app_no_tab=1" : "";?>" method="post">
                            <?php echo $this->Form->hidden('id', array('value' => $business['Business']['id'])); ?>
                            <div class="full_content p_m_10">
                                <div class="form_content">
                                    <div id="photos_upload"></div>
                                    <div id="photo_review"></div>
                                    <a href="javascript:void(0)" class="btn btn-action" id="triggerUpload"><?php echo __d('business', 'Upload Queued Documents')?></a>
                                    <input type="hidden" name="new_photos" id="new_photos">
                                    <input type="button" class="btn btn-action" id="nextStep" value="<?php echo __d('business', 'Submit Documents')?>" style="display:none">
                                    <div id="loadingSpin" style="display: inline-block; padding: 0 10px;"></div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
