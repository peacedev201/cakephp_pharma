<?php echo $this->Html->css(array(
    'fineuploader',
    'Business.business'), array('block' => 'css', 'minify'=>false));
?>

<?php $this->Html->scriptStart(array(
    'inline' => false, 
    'domReady' => true, 
    'requires' => array('jquery', 'mooBusiness'), 
    'object' => array('$', 'mooBusiness')
));?>
    mooBusiness.initBusinessPhoto('<?php echo BUSINESS_MODULE_PHOTO;?>', <?php echo $business_id;?>);
<?php $this->Html->scriptEnd(); ?>

<div class="create_form">
    <div class="box3">
        <div class="mo_breadcrumb bu-manage-photo">
            <h1>
                <?php echo __d('business', 'Manage photos');?>
            </h1>
        </div>
        <div class="full_content p_m_10">
            <form id="formBusinessPhotos">
                <?php echo $this->Form->hidden('business_id', array(
                    'value' => $business_id
                ));?>
                <?php echo $this->Element('misc/upload_photo', array(
                    'business_photos' => $photos
                ));?>
                <input type="button" class="button button-primary" id="saveButton" value="<?php echo __d('business', 'Save') ?>">
                <div class="error-message" id="errorMessage2" style="display:none"></div>
            </form>
        </div>
    </div>
</div>
    
<?php
if($is_app)
{
    $this->MooGzip->script(array('zip'=>'mobile.action.bundle.js.gz','unzip'=>'MooApp.mobile.action.bundle'));
}
?>